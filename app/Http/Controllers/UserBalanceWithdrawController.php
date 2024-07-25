<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\UserBalanceHistory;
use App\Models\UserBalanceWithdraw;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Cost;
use App\Models\CostPayment;
use App\Models\CostDetail;
use App\Models\CostType;
use Jenssegers\Agent\Agent;
use App\Models\Partner;
use App\Models\Payer;
use App\Models\AccLogs;
use App\Models\BankInfo;
use App\Models\Logs;
use App\Models\CostCate;
use App\Models\TourSystem;
use Helper, File, Session, Auth, Image, Hash, DB;

class UserBalanceWithdrawController extends Controller
{
    private $_minDateKey = 0;
    private $_maxDateKey = 1;
  public function index(Request $request)
  {

    $monthDefault = date('m');
    $month = $request->month ?? $monthDefault;
    $year = $request->year ?? date('Y');
    $mindate = "$year-$month-01";
    $maxdate = date("Y-m-t", strtotime($mindate));
    //dd($maxdate);
    //$maxdate = '2021-03-01';
    $maxDay = date('d', strtotime($maxdate));

    $arrSearch['type'] = $type = $request->type ? $request->type : null;
    $arrSearch['multi'] = $multi = $request->multi ?? 0;
    $arrSearch['status'] = $status = $request->status ?? null;

    $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
    $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
    $arrSearch['nguoi_chi'] = $nguoi_chi = $request->nguoi_chi ? $request->nguoi_chi : null;
    $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
    $arrSearch['is_fixed'] = $is_fixed = $request->is_fixed ?? null;
    $arrSearch['tour_id'] = $tour_id = $request->tour_id ?? null;
    $arrSearch['id_search'] = $id_search = $request->id_search ?? null;
    $content = !empty($request->content) ? $request->content : null;
    $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));

    $currentDate = Carbon::now();
    $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y');

    $date_use = date('d/m/Y');
    $partnerList = (object)[];

    $query = UserBalanceWithdraw::where('status', '<>', 0);
    if ($id_search) {
      //  dd($id_search);
      $id_search = strtolower($id_search);
      $id_search = str_replace("cp", "", $id_search);
      $arrSearch['id_search'] = $id_search;
      $query->where('id', $id_search);
    } else {
      if ($type) {
        $query->where('type', $type);
      }
      if ($user_id) {
        $query->where('user_id', $user_id);
      }

        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => today
            $rangeDate = Carbon::now();
            $query->where('use_date','=', $rangeDate->format('Y-m-d'));
            $time_type = 3;
            $month = $rangeDate->format('m');
            $year = $rangeDate->year;
        } elseif (count($rangeDate) === 1) {
            //case page is initialized and range_date has value,
            //when counting the number of elements in rangeDate = 1 => only select a day
            $use_date = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey]);
            $query->where('created_at','=', $use_date->format('Y-m-d'));
            $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
            $time_type = 3;
            $month = $use_date->format('m');
            $year = $use_date->year;
        } else {
            $query->where('created_at','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $query->where('created_at', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
            $time_type = 1;
            $month = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('m');
            $year = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->year;
        }
    }

    $items = $query->orderBy('id', 'desc')->paginate(10000);
    $total_actual_amount = $total_quantity = 0;
    foreach ($items as $o) {
      $total_actual_amount += $o->amount;
      $total_quantity += 1;
    }
    return view('user-balance-withdraw.index', compact('items', 'content', 'arrSearch', 'total_actual_amount', 'nguoi_chi', 'partnerList', 'total_quantity', 'month', 'city_id', 'time_type', 'year', 'is_fixed', 'type', 'multi'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create(Request $request)
  {

    $cate_id = $request->cate_id ? $request->cate_id : null;
    $date_use = $request->date_use ? $request->date_use : null;
    $cateList = CostType::where('status', 1)->orderBy('display_order')->get();
    $partnerList = null;
    if ($cate_id) {
      $partnerList = Partner::getList(['cost_type_id' => $cate_id]);
    }
    $users = Account::where('status', 1)->where('is_staff', 0)->where('is_sales', 0)->orderBy('name')->get();
    $month = $request->month ?? null;
    $bankInfoList = BankInfo::all();
    $vietNameBanks = \App\Helpers\Helper::getVietNamBanks();
    $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();
    return view('user-balance-withdraw.create', compact('cate_id', 'date_use', 'cateList', 'month', 'partnerList', 'bankInfoList', 'vietNameBanks', 'tourSystem', 'users'));
  }

  public function sms(Request $request)
  {
    return view('cost.sms');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param Request $request
   * @return Response
   */
  public function store(Request $request)
  {
    $dataArr = $request->all();

    $this->validate($request, [
        'amount' => 'required',
        'user_id' => 'required'
    ],
        [
            'date.required' => 'Bạn chưa nhập ngày',
            'amount.required' => 'Bạn chưa nhập số tiền',
            'user_id.required' => 'Bạn chưa chọn chọn đối tác',
        ]);
    $dataArr['amount'] = (int)str_replace(',', '', $dataArr['amount']);

    //Generate noi dung CK
    $noi_dung_ck = !empty($dataArr['noi_dung_ck']) ? $dataArr['noi_dung_ck'] : 'CK ' . $dataArr['user_id'];
    if ($dataArr['image_url'] && $dataArr['image_name']) {
      $tmp = explode('/', $dataArr['image_url']);
      if (!is_dir('uploads/' . date('Y/m/d'))) {
        mkdir('uploads/' . date('Y/m/d'), 0777, true);
      }
      $destionation = date('Y/m/d') . '/' . end($tmp);
      File::move(config('plantotravel.upload_path') . $dataArr['image_url'], config('plantotravel.upload_path') . $destionation);
      $dataArr['image_url'] = $destionation;
    }

    $user = User::findOrFail($dataArr['user_id']);

    $arr = [
        'amount' => $dataArr['amount'],
        'image_url' => $dataArr['image_url'],
        'nguoi_chi' => $dataArr['nguoi_chi'],
        'notes' => $dataArr['notes'],
        'user_id' => $dataArr['user_id'],
        'code' => $noi_dung_ck,
        'current_balance' => $user->balance ?? 0,
        'status' => UserBalanceWithdraw::STATUS_PENDING,
        'account_name' => $dataArr['account_name'],
        'account_number' => $dataArr['account_number'],
        'account_bank_name' => $dataArr['account_bank_name'],
        'account_bank_branch' => $dataArr['account_bank_branch'],
    ];
    $rs = UserBalanceWithdraw::create($arr);

    //write logs
    unset($dataArr['_token'], $dataArr['image_name']);
    Logs::create([
        'table_name' => 'user_balance_withdraws',
        'user_id' => Auth::user()->id,
        'action' => 1,
        'content' => json_encode($arr),
        'object_id' => $rs->id
    ]);

    Session::flash('message', 'Tạo mới thành công');
    $month = date('m', strtotime($dataArr['date']));
    return redirect()->route('user-balance-withdraw.index', ['month' => $month]);
  }

  public function update(Request $request)
  {
    $dataArr = $request->all();
    $cost_id = $dataArr['id'];
    $model = UserBalanceWithdraw::findOrFail($cost_id);
    $oldData = $model->toArray();
    $this->validate($request, [
        'amount' => 'required',
        'user_id' => 'required'
    ],
        [
            'date.required' => 'Bạn chưa nhập ngày',
            'amount.required' => 'Bạn chưa nhập số tiền',
            'user_id.required' => 'Bạn chưa chọn chọn đối tác',
        ]);
    $dataArr['amount'] = (int)str_replace(',', '', $dataArr['amount']);

    //Generate noi dung CK
    $noi_dung_ck = !empty($dataArr['noi_dung_ck']) ? $dataArr['noi_dung_ck'] : 'CK ' . $dataArr['user_id'];
    if ($dataArr['image_url'] && $dataArr['image_name']) {
      $tmp = explode('/', $dataArr['image_url']);
      if (!is_dir('uploads/' . date('Y/m/d'))) {
        mkdir('uploads/' . date('Y/m/d'), 0777, true);
      }
      $destionation = date('Y/m/d') . '/' . end($tmp);
      File::move(config('plantotravel.upload_path') . $dataArr['image_url'], config('plantotravel.upload_path') . $destionation);
      $dataArr['image_url'] = $destionation;
    }

    $arr = [
        'amount' => $dataArr['amount'],
        'image_url' => $dataArr['image_url'],
        'nguoi_chi' => $dataArr['nguoi_chi'],
        'notes' => $dataArr['notes'],
        'user_id' => $dataArr['user_id'],
        'code' => $noi_dung_ck,
        'status' => $dataArr['status'],
        'account_name' => $dataArr['account_name'],
        'account_number' => $dataArr['account_number'],
        'account_bank_name' => $dataArr['account_bank_name'],
        'account_bank_branch' => $dataArr['account_bank_branch'],
    ];
    $model->update($arr);

    //write logs
    unset($dataArr['_token'], $dataArr['image_name']);
    $contentDiff = array_diff_assoc($dataArr, $oldData);
    //dd($contentDiff);
    if (!empty($contentDiff)) {
      $oldContent = [];

      foreach ($contentDiff as $k => $v) {
        if (isset($oldData[$k])) {
          $oldContent[$k] = $oldData[$k];
        }
      }
      Logs::create([
          'table_name' => 'user_balance_withdraws',
          'user_id' => Auth::user()->id,
          'action' => 2,
          'content' => json_encode($contentDiff),
          'old_content' => json_encode($oldContent),
          'object_id' => $model->id
      ]);
    }

    Session::flash('message', 'Cập nhật thành công');
    return redirect()->route('user-balance-withdraw.index');
  }

  /**
   * Display the specified resource.
   *
   * @param int $id
   * @return Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param int $id
   * @return Response
   */
  public function edit($id)
  {

    $detail = UserBalanceWithdraw::find($id);
    if ($detail->status == UserBalanceWithdraw::STATUS_COMPLETED) {
      return view('ko-cap-nhat');
    }
    $partnerList = Partner::getList(['cost_type_id' => $detail->cate_id]);
    $cateList = CostType::where('status', 1)->orderBy('display_order')->get();
    $payerList = Payer::all();
    $payerNameArr = [];
    foreach ($payerList as $pay) {
      $payerNameArr[$pay->id] = $pay->name;
    }
    $bankInfoList = BankInfo::all();
    $vietNameBanks = \App\Helpers\Helper::getVietNamBanks();
    $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();
    $users = Account::where('status', 1)->where('is_staff', 0)->where('is_sales', 0)->orderBy('name')->get();
    return view('user-balance-withdraw.edit', compact('detail', 'cateList', 'partnerList', 'payerList', 'payerNameArr', 'bankInfoList', 'vietNameBanks', 'tourSystem', 'users'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param int $id
   * @return Response
   */
  public function destroy($id)
  {
    // delete
    $model = UserBalanceWithdraw::find($id);
    if ($model->code_ung_tien || $model->time_chi_tien) {
      return view('ko-cap-nhat');
    }
    $oldStatus = $model->status;
    $model->update(['status' => 0]);

    Logs::create([
        'table_name' => 'user_balance_withdraws',
        'user_id' => Auth::user()->id,
        'action' => 3,
        'content' => json_encode(['status' => 0]),
        'object_id' => $model->id
    ]);

    // redirect
    Session::flash('message', 'Xóa thành công');
    return redirect()->route('cost.index');
  }

  public function viewQRCode(Request $request)
  {
    $data = UserBalanceHistory::where('id', $request->id)->first();
    $data->qrcode_clicked = !empty($data->qrcode_clicked) ? $data->qrcode_clicked + 1 : 1;
    $data->save();
    return response()->json(['data' => $data->qrcode_clicked]);
  }

  public function ajaxUserBanks(Request $request){
    $userId = $request->user_id;
    $id = $request->id;
    if(!empty($id)){
      return response()->json(['data' => UserBankAccount::where('id', $id)->first()]);
    }
    if(empty($userId)){
      return response()->json(['data' => []]);
    }
    return response()->json(['data' => UserBankAccount::where('user_id', $userId)->get()]);
  }
}
