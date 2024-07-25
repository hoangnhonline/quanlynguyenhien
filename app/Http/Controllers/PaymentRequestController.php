<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Faker\Provider\Payment;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use Jenssegers\Agent\Agent;
use App\Models\BankInfo;
use App\Models\Cost;
use App\Models\Logs;
use App\Models\Task;

use Maatwebsite\Excel\Facades\Excel;
use App\User;
use App\Models\BookingPayment;
use App\Models\AccLogs;
use Illuminate\Support\Str;
use Helper, File, Session, Auth, Image, Hash;

class PaymentRequestController extends Controller
{
    private $_minDateKey = 0;
    private $_maxDateKey = 1;
    public function getConfirmUng(Request $request){
        $str_id = $request->str_id;
        $tmp = explode(',', $str_id);
        $total_money = 0;
        $arrCost = [];
        if(!empty($tmp)){

            foreach($tmp as $payment_request_id){
                if($payment_request_id > 0){
                    $arrCost[$payment_request_id] = PaymentRequest::find($payment_request_id)->total_money;
                }
            }
        }
        return view('cost.ajax-confirm', compact('arrCost'));

    }
    public function getConfirmChi(Request $request){
        $str_id = $request->str_id;
        $tmp = explode(',', $str_id);
        $total_money = 0;
        $arrCost = [];
        if(!empty($tmp)){
            foreach($tmp as $payment_request_id){
                if($payment_request_id > 0){
                    $arrCost[$payment_request_id] = PaymentRequest::find($payment_request_id)->total_money;
                }
            }
        }
        return view('cost.ajax-confirm-chi', compact('arrCost'));

    }
    public function getContentUng(Request $request){
        $str_id = $request->str_id;
        $tmp = explode(',', $str_id);
        $total_money = 0;
        $arrCost = [];
        if(!empty($tmp)){
            $code_ung_tien = Str::upper(Str::random(7));

            foreach($tmp as $payment_request_id){
                if($payment_request_id > 0){
                    $detail = PaymentRequest::find($payment_request_id);
                    if(!$detail->code_ung_tien){
                        $total_money += $detail->total_money;
                        $detail->update([
                            'code_ung_tien' => $code_ung_tien,
                            'time_code_ung_tien' => date('Y-m-d H:i:s')
                        ]);

                        //write logs
                        Logs::create([
                            'table_name' => 'payment_request',
                            'user_id' => Auth::user()->id,
                            'action' => 2,
                            'content' => json_encode([
                                'code_ung_tien' => $code_ung_tien,
                                'time_code_ung_tien' => date('Y-m-d H:i:s')
                            ]),
                            'object_id' => $detail->id
                        ]);
                    }
                }
            }
            if($total_money > 0){
                // luu vao acc_logs
                AccLogs::create([
                    'code' => $code_ung_tien,
                    'so_tien' => $total_money,
                    'nguoi_yeu_cau' => Auth::user()->collecter_id,
                    'tbl' => 2, // 2 = payment_request
                    'type' => 2,
                    'time_yeu_cau' => date('Y-m-d H:i:s'),
                    'status' => 1

                ]);
                return "ND: ADVPAY ".$code_ung_tien. " ". $total_money;

            }else{
                return "Đã có lỗi xảy ra!!!";
            }

        }else{
            return "CHƯA CHỌN MỤC NÀO!!!";
        }

    }
    public function getContentChi(Request $request){
        $str_id = $request->str_id;
        $tmp = explode(',', $str_id);
        $total_money = 0;
        $arrCost = [];
        if(!empty($tmp)){
            $code_chi_tien = Str::upper(Str::random(7));

            foreach($tmp as $payment_request_id){
                if($payment_request_id > 0){
                    $detail = PaymentRequest::find($payment_request_id);
                    if(!$detail->code_chi_tien){
                        $total_money += $detail->total_money;
                        $detail->update([
                            'code_chi_tien' => $code_chi_tien,
                            'time_code_chi_tien' => date('Y-m-d H:i:s')
                        ]);
                        //write logs
                        Logs::create([
                            'table_name' => 'payment_request',
                            'user_id' => Auth::user()->id,
                            'action' => 2,
                            'content' => json_encode([
                                'code_chi_tien' => $code_chi_tien,
                                'time_code_chi_tien' => date('Y-m-d H:i:s')
                            ]),
                            'object_id' => $detail->id
                        ]);
                    }
                }
            }
            if($total_money > 0){
                return "ND: EXPPAY ".$code_chi_tien. " ". $total_money;

            }else{
                return "Đã có lỗi xảy ra!!!";
            }

        }else{
            return "CHƯA CHỌN MỤC NÀO!!!";
        }

    }
    public function changeValueByColumn(Request $request){
        $id = $request->id;
        $column = $request->col;
        $value = $request->value;
        $model = PaymentRequest::find($id);
        $oldValue = $model->$column;

        $model->update([$column => $value]);
        //write logs
        Logs::create([
            'table_name' => 'payment_request',
            'user_id' => Auth::user()->id,
            'action' => 2,
            'content' => json_encode([$column => $value]),
            'old_content' => json_encode([$column => $oldValue]),
            'object_id' => $id
        ]);
    }
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

        $arrSearch['status'] = $status = $request->status ?? null;
        $arrSearch['bank_info_id'] = $bank_info_id = $request->bank_info_id ?? null;
        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $arrSearch['code_ung_tien'] = $code_ung_tien = $request->code_ung_tien ?? null;
        $arrSearch['code_chi_tien'] = $code_chi_tien = $request->code_chi_tien ?? null;
        $arrSearch['user_id'] = $user_id = $request->user_id ?? null;
        $arrSearch['nguoi_chi'] = $nguoi_chi = $request->nguoi_chi ?? null;
        $arrSearch['urgent'] = $urgent = $request->urgent ?? null;
        $arrSearch['time_type'] = $time_type = $request->time_type ?? 3;
        $arrSearch['id'] = $id = $request->id ?? null;
        $arrSearch['use_date_from'] = $use_date_from = $date_pay = date('d/m/Y', strtotime($mindate));
        $content = $request->content ? $request->content : null;

        $arrSearch['acc_checked'] = $acc_checked = $request->acc_checked ?? null;

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y'); //this month

        $date_pay = null;
        $query = PaymentRequest::where('status', '>', 0);
        if($id){
            $query->where('id', $id);
        }else{
            if($status){
                $query->where('status', $status);
            }
            if($urgent){
                $query->where('urgent', 1);
            }
            if($acc_checked){
                $query->where('acc_checked', 1);
            }
            if($code_ung_tien){
                $query->where('code_ung_tien', $code_ung_tien);
            }
            if($code_chi_tien){
                $query->where('code_chi_tien', $code_chi_tien);
            }
            if($nguoi_chi){
                $query->where('nguoi_chi', $nguoi_chi);
            }
            if($city_id){
                $query->where('city_id', $city_id);
            }
            if($bank_info_id){
                $query->where('bank_info_id', $bank_info_id);
            }

            $rangeDate = array_unique(explode(' - ', $range_date));
            if (empty($rangeDate[$this->_minDateKey])) {
                //case page is initialized and range_date is empty => today
                $rangeDate = Carbon::now();
                $query->where('date_pay','=', $rangeDate->format('Y-m-d'));
                $time_type = 3;
                $month = $rangeDate->format('m');
                $year = $rangeDate->year;
            } elseif (count($rangeDate) === 1) {
                //case page is initialized and range_date has value,
                //when counting the number of elements in rangeDate = 1 => only select a day
                $use_date = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey]);
                $query->where('date_pay','=', $use_date->format('Y-m-d'));
                $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
                $time_type = 3;
                $month = $use_date->format('m');
                $year = $use_date->year;
            } else {
                $query->where('date_pay','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                $query->where('date_pay', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
                $time_type = 1;
                $month = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('m');
                $year = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->year;
            }

        }
        if(Auth::user()->role < 3){
            if($user_id && $user_id > 0){
                $arrSearch['user_id'] = $user_id;
                $query->where('user_id', $user_id);
            }
        }else{
            $arrSearch['user_id'] = Auth::user()->id;
            $query->where('user_id', Auth::user()->id);
        }
        $items = $query->orderBy('id', 'desc')->paginate(2000);
        $total_actual_amount = $total_quantity = 0;
        foreach($items as $o){
            $total_actual_amount+= $o->total_money;
            $total_quantity += $o->amount;
        }

        $bankInfoList = BankInfo::all();
        $agent = new Agent();
        if($agent->isMobile()){
            $view = 'payment-request.m-index';
        }else{
            $view = 'payment-request.index';
        }
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        return view($view, compact( 'items', 'content', 'arrSearch', 'date_pay', 'total_actual_amount', 'nguoi_chi', 'total_quantity', 'month', 'city_id', 'time_type','year', 'bank_info_id', 'bankInfoList', 'status', 'user_id', 'listUser', 'urgent'));
    }
    public function export(Request $request)
    {
        $month = $request->month ?? "04";
        $year = date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        $arrSearch['type'] = $type = $request->type ? $request->type : null;
        $arrSearch['bank_info_id'] = $bank_info_id = $request->bank_info_id ? $request->bank_info_id : null;
        $arrSearch['partner_id'] = $partner_id = $request->partner_id ? $request->partner_id : null;
        $arrSearch['nguoi_chi'] = $nguoi_chi = $request->nguoi_chi ? $request->nguoi_chi : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 1;
        $content = $request->content ? $request->content : null;

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y'); //this month

        $query = PaymentRequest::where('status', 1);
        if($nguoi_chi){
            $query->where('nguoi_chi', $nguoi_chi);
        }
        if($type){
            $query->where('type', $type);
        }
        if($partner_id){
            $query->where('partner_id', $partner_id);
        }
        $partnerList = (object) [];
        if($bank_info_id){
            $query->where('bank_info_id', $bank_info_id);
            $partnerList = Partner::getList(['cost_type_id'=> $bank_info_id]);
        }

        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => today
            $rangeDate = Carbon::now();
            $query->where('date_pay','=', $rangeDate->format('Y-m-d'));
            $time_type = 3;
            $month = $rangeDate->format('m');
            $year = $rangeDate->year;
        } elseif (count($rangeDate) === 1) {
            //case page is initialized and range_date has value,
            //when counting the number of elements in rangeDate = 1 => only select a day
            $use_date = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey]);
            $query->where('date_pay','=', $use_date->format('Y-m-d'));
            $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
            $time_type = 3;
            $month = $use_date->format('m');
            $year = $use_date->year;
        } else {
            $query->where('date_pay','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $query->where('date_pay', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
            $time_type = 1;
            $month = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('m');
            $year = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->year;
        }
        $items = $query->orderBy('date_pay', 'asc')->get();
        //dd($items);

        $cateList = CostType::pluck('name', 'id');
        $partnerList = Partner::pluck('name', 'id');

        $i = 0;
        $contents[] = [
                '#' => '#',
                'Ngày' => 'Ngày',
                'Nội dung' => 'Nội dung',
                'Đối tác' => 'Đối tác',
                'Giá' => 'Giá',
                'Số lượng' => 'Số lượng',
                'Thành tiền' => 'Thành tiền',
                'Ghi chú'=> 'Ghi chú'
            ];
            $total  = $totalAmount = 0;
           // dd($items);
        foreach ($items as $item) {
            $total += $item->total_money;
            $totalAmount += $item->amount;
            $i++;
            $contents[] = [
                '#' => $i,
                'Ngày' => date('d/m', strtotime($item->date_pay)),
                'Nội dung' => $item->bank_info_id > 0 && isset( $cateList[$item->bank_info_id]) ? $cateList[$item->bank_info_id] : "",
                'Đối tác' => $item->partner_id > 0 && isset($partnerList[$item->partner_id]) ? $partnerList[$item->partner_id] : "",
                'Giá' => number_format($item->price),
                'Số lượng' => ($item->amount),
                'Thành tiền' => number_format($item->total_money),
                'Ghi chú'=> $item->notes
            ];

        }
        $contents[] = [
                '#' => '',
                'Ngày' => '',
                'Nội dung' => '',
                'Đối tác' => '',
                'Giá' => '',
                'Số lượng' => number_format($totalAmount),
                'Thành tiền' => number_format($total),
                'Ghi chú'=> ''
            ];
        if(!empty($contents)){
            try{
                $filename = 'Cost-'.date('dmhis', time());
                Excel::create($filename, function ($excel) use ($contents, $filename) {
                    // Set sheets
                    $excel->sheet($filename, function ($sheet) use ($contents) {
                        $sheet->fromArray($contents, null, 'A1', false, false);
                    });
                })->download('xls');
            }catch(\Exception $ex){
                dd($ex);
            }
        }


    }

    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $payment_request_id = $request->payment_request_id ?? null;
        $costDetail = (object) [];
        $view = 'create';
        if($payment_request_id){
            $costDetail = PaymentRequest::find($payment_request_id);
            $view = 'create-by-cost';
        }
        $bank_info_id = $request->bank_info_id ? $request->bank_info_id : null;
        $date_pay = $request->date_pay ? $request->date_pay : null;
        $bankInfoList = BankInfo::all();
        $vietNameBanks = \App\Helpers\Helper::getVietNamBanks();
        return view('payment-request.'.$view, compact('bank_info_id', 'date_pay', 'bankInfoList', 'costDetail', 'vietNameBanks'));
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  Request  $request
    * @return Response
    */
    public function store(Request $request)
    {
        $dataArr = $request->all();

        $this->validate($request,[
            'date_pay' => 'required',
            'city_id' => 'required',
            'total_money' => 'required'

        ],
        [
            'date_pay.required' => 'Bạn chưa nhập ngày',
            'city_id.required' => 'Bạn chưa chọn tỉnh/thành',
            'total_money.required' => 'Bạn chưa nhập số tiền',
        ]);


        $dataArr['total_money'] = (int) str_replace(',', '', $dataArr['total_money']);
        $dataArr['urgent'] = isset($dataArr['urgent']) ? 1 : 0;
        $dataArr['acc_checked'] = isset($dataArr['acc_checked']) ? 1 : 0;
        $date_pay = $dataArr['date_pay'];
        $tmpDate = explode('/', $dataArr['date_pay']);
        $dataArr['date_pay'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        if($dataArr['image_url'] && $dataArr['image_name']){

            $tmp = explode('/', $dataArr['image_url']);

            if(!is_dir('uploads/'.date('Y/m/d'))){
                mkdir('uploads/'.date('Y/m/d'), 0777, true);
            }

            $destionation = date('Y/m/d'). '/'. end($tmp);

            File::move(config('plantotravel.upload_path').$dataArr['image_url'], config('plantotravel.upload_path').$destionation);

            $dataArr['image_url'] = $destionation;
        }
        $arr = [
            'date_pay' => $dataArr['date_pay'],
            'notes' => $dataArr['notes'],
            'content' => str_replace('PTH', 'PLAN ', $dataArr['content']),
            'total_money' => (int) str_replace(',', '', $dataArr['total_money']),
            'image_url' => $dataArr['image_url'],
            'booking_id' => $dataArr['booking_id'],
            'bank_info_id' => $dataArr['bank_info_id'],
            'city_id' => $dataArr['city_id'],
            'user_id' => Auth::user()->id,
            'status' => 1,
            'urgent' => $dataArr['urgent'],
            'payment_request_id' => isset($dataArr['payment_request_id']) ? $dataArr['payment_request_id'] : null
        ];
        //dd($arr);
        $rs = PaymentRequest::create($arr);
         //write logs
        Logs::create([
            'table_name' => 'payment_request',
            'user_id' => Auth::user()->id,
            'action' => 1,
            'content' => json_encode($arr),
            'object_id' => $rs->id
        ]);

        Session::flash('message', 'Tạo mới thành công');

        $month = $tmpDate[1];
        $year = $tmpDate[2];

        return redirect()->route('payment-request.index', ['month' =>$month, 'year' => $year, 'time_type' => 1]);
    }
    public function update(Request $request)
    {
        $dataArr = $request->all();
        $payment_request_id = $dataArr['id'];
        $model= PaymentRequest::findOrFail($payment_request_id);

        $oldData = $model->toArray();

        $oldStatus = $model->status;
        $this->validate($request,[
            'date_pay' => 'required',
            'city_id' => 'required',
            'total_money' => 'required'

        ],
        [
            'date_pay.required' => 'Bạn chưa nhập ngày',
            'city_id.required' => 'Bạn chưa chọn tỉnh/thành',
            'total_money.required' => 'Bạn chưa nhập số tiền',
        ]);

        if($dataArr['image_url'] && $dataArr['image_name']){

            $tmp = explode('/', $dataArr['image_url']);

            if(!is_dir('uploads/'.date('Y/m/d'))){
                mkdir('uploads/'.date('Y/m/d'), 0777, true);
            }

            $destionation = date('Y/m/d'). '/'. end($tmp);

            File::move(config('plantotravel.upload_path').$dataArr['image_url'], config('plantotravel.upload_path').$destionation);

            $dataArr['image_url'] = $destionation;
        }
        if($dataArr['unc_url'] && $dataArr['unc_name']){

            $tmp = explode('/', $dataArr['unc_url']);

            if(!is_dir('uploads/'.date('Y/m/d'))){
                mkdir('uploads/'.date('Y/m/d'), 0777, true);
            }

            $destionation = date('Y/m/d'). '/'. end($tmp);

            File::move(config('plantotravel.upload_path').$dataArr['unc_url'], config('plantotravel.upload_path').$destionation);

            $dataArr['unc_url'] = $destionation;
        }
        //dd($dataArr);
        $dataArr['total_money'] = (int) str_replace(',', '', $dataArr['total_money']);
        $dataArr['urgent'] = isset($dataArr['urgent']) ? 1 : 0;
        $dataArr['acc_checked'] = isset($dataArr['acc_checked']) ? 1 : 0;

        $date_pay = $dataArr['date_pay'];
        $tmpDate = explode('/', $dataArr['date_pay']);
        $dataArr['date_pay'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        $month = $tmpDate[1];
        $year = $tmpDate[2];
        $dataArr['content'] = str_replace('PTH', 'PLAN ', $dataArr['content']);

        $model->update($dataArr);

        //write logs
        unset($dataArr['_token'], $dataArr['image_name'], $dataArr['unc_name']);
        $contentDiff = array_diff_assoc($dataArr, $oldData);
        //dd($contentDiff);
        if(!empty($contentDiff)){
            $oldContent = [];

            foreach($contentDiff as $k => $v){
                if(isset($oldData[$k])){
                    $oldContent[$k] = $oldData[$k];
                }
            }
            Logs::create([
                'table_name' => 'payment_request',
                'user_id' => Auth::user()->id,
                'action' => 2,
                'content' => json_encode($contentDiff),
                'old_content' => json_encode($oldContent),
                'object_id' => $model->id
            ]);
        }
        // if($dataArr['status'] == 2 && $oldStatus == 1){
        //     //reply zalo
        //     //$this->replyMess($payment_request_id);
        // }
        $booking_id = '';
        if($dataArr['booking_id']){
            $booking_id = trim(strtolower($dataArr['booking_id']));
            $booking_id = str_replace("pth", '', $booking_id);
            $booking_id = str_replace("ptt", '', $booking_id);
            $booking_id = str_replace("ptv", '', $booking_id);
        }
        if($dataArr['unc_url'] && $booking_id != ''){
            BookingPayment::create([
                'booking_id' => $booking_id,
                'amount' => $dataArr['total_money'],
                'pay_date' => $dataArr['date_pay'],
                'image_url' => $dataArr['unc_url'],
                'notes' => $dataArr['nguoi_chi'] == 1 ? "Admin chi tiền" : "Kế toán chi tiền",
                'flow' => 2
            ]);
        }

        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('payment-request.index', ['month' => $month, 'time_type' => 1, 'year' => $year]);
    }
    public function replyMess($id){

        $url = 'https://openapi.zalo.me/v2.0/oa/message?access_token=ZaVgNfRnPLUDG-XRalLgKuT2u5UJwn83YYxgIf302XZv9iX1ljKr5ia6ongBp3bgwYJd19F03q_vDECyjzeoDVGeuJglm6a_yY_hMwpR1IwmRiz6nTv0Bw0igLNz-c1Tv16i0fttL5FYGgC3hAOW3SPB_dA6-0rYw1py1uli77Vn4jCIfifGREGLln2Yfaf3sdEP6OsPSMVDGQCGX_DuRl95kXwEe4b5a6s6J-AhVp2zHwzrwBXP8Prjaotvt4mzkMo1SkE22G2XQze8leeALDj4tX5FQ2s7kZsGxcDL';
        $detail = PaymentRequest::find($id);

        $detailUser = User::find($detail->user_id);
        if($detail->nguoi_chi == 1){
            $payer = 'Nguyễn Hoàng';
        }elseif($detail->nguoi_chi == 2){
            $payer = 'Thương Trần';
        }elseif($detail->nguoi_chi == 3){
            $payer = 'Ngọc Nguyễn';
        }elseif($detail->nguoi_chi == 4){
            $payer = 'Mộng Tuyền';
        }
        $text = '';
        if($detail->booking_id){
            $text = ' của booking '.$detail->booking_id;
        }
       //dd($detailUser->zalo_id);
        $arrData = [
            'recipient' => [
                'user_id' => $detailUser->zalo_id,
            ],
            'message' => [
                'text' => $payer.' đã thanh toán '.number_format($detail->total_money).$text. ' cho '.$detail->bank->name,
            ]
        ];
        $ch = curl_init( $url );
        # Setup request to send json via POST.
        $payload = json_encode( $arrData );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);


        // $arrData = [
        //     'recipient' => [
        //         'user_id' => '7317386031055599346',
        //     ],
        //     'message' => [
        //         'text' => 'Đã nhận. Mã booking là '.$booking_code,
        //     ]
        // ];
        // $ch = curl_init( $url );
        // # Setup request to send json via POST.
        // $payload = json_encode( $arrData );
        // curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        // curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        // # Return response instead of printing.
        // curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        // # Send request.
        // $result = curl_exec($ch);
        // curl_close($ch);
        # Print response.
        echo "<pre>$result</pre>";

    }
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return Response
    */
    public function show($id)
    {
    //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return Response
    */
    public function edit($id)
    {

        $detail = PaymentRequest::find($id);
        if($detail->code_ung_tien && Auth::user()->id != 1){
            return view('ko-cap-nhat');
        }
        $bankInfoList = BankInfo::all();
        $vietNameBanks = \App\Helpers\Helper::getVietNamBanks();
        return view('payment-request.edit', compact( 'detail', 'bankInfoList', 'vietNameBanks'));
    }
    public function urgent()
    {

        $count = PaymentRequest::where('status', 1)->where('urgent', 1)->count();

        $countTask = Task::where('status', 1)->where('staff_id', Auth::user()->id)->count();
        $maxiToday = \App\Models\MaxiHistory::where('date', date('Y-m-d'))->pluck('maxi_id')->toArray();
        $maxiTomorrow = \App\Models\MaxiHistory::where('date', date('Y-m-d', strtotime('tomorrow')))->pluck('maxi_id')->toArray();       
        $arrTrung = array_intersect($maxiToday, $maxiTomorrow);
        $strNameMaxi = '';
        if(!empty($arrTrung)){
            foreach($arrTrung as $maxi_id){
                $strNameMaxi .= \App\Models\Maxi::find($maxi_id)->name.",";
            }
            $strNameMaxi = substr($strNameMaxi, 0, -1);
        }        
        
        if($count > 0 || $countTask > 0){
            return view('payment-request.urgent', compact( 'count', 'countTask', 'strNameMaxi'));
        }




    }
    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function destroy($id)
    {
        // delete
        $model = PaymentRequest::find($id);
        $oldStatus = $model->status;
        $month = date('m', strtotime($model->pay_date));
        $year = date('Y', strtotime($model->pay_date));
        if(!$model->code_ung_tien){
            $model->update(['status' => 0]);
        }

        Logs::create([
            'table_name' => 'payment_request',
            'user_id' => Auth::user()->id,
            'action' => 3,
            'content' => json_encode(['status' => 0]),
            'object_id' => $model->id
        ]);

        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('payment-request.index', ['time_type' => 1, 'month' => $month, 'year' => $year]);
    }

    public function viewQRCode(Request $request){
        $data = PaymentRequest::where('id', $request->id)->first();
        $data->qrcode_clicked = !empty($data->qrcode_clicked) ? $data->qrcode_clicked + 1 : 1;
        $data->save();
        return response()->json(['data' => $data->qrcode_clicked]);
    }
}
