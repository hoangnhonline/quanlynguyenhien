<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\CouponCode;
use App\Models\Restaurants;
use App\Models\Partner;

use Jenssegers\Agent\Agent;
use App\User;

use Helper, File, Session, Auth, Image, Hash;

class CouponCodeController extends Controller
{

    private $_minDateKey = 0;
    private $_maxDateKey = 1;
    public function cal(){
       $all = CouponCode::all();
       foreach($all as $a){
            $created_at = $a->created_at;
            foreach($a->details as $b){
                $b->update(['created_at' => $created_at]);
            }
       }
    }
    public function index(Request $request)
    {

        $restaurantList = Restaurants::where(
            [
                'status' => 1,
                'co_chi' => 1
            ]
        )->get();
        $arrSearch['restaurant_id'] = $restaurant_id = $request->restaurant_id ? $request->restaurant_id : null;

        $arrSearch['status'] = $status = $request->status ?? null;
        $arrSearch['user_id'] = $user_id = $request->user_id ?? null;

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y');

        $query = CouponCode::whereRaw(1);

        if($status){
            $query->where('status', $status);
        }

        if($restaurant_id){
            $query->where('restaurant_id', $restaurant_id);
        }
        if(Auth::user()->role > 1){
            $query->where('user_id', Auth::user()->id);
        }else{
            if($user_id)  $query->where('user_id', $user_id);
        }

        $arrSearch['created_at_from'] = $created_at_from = $request->created_at_from ? $request->created_at_from : date('d/m/Y', time());
        $arrSearch['created_at_to'] = $created_at_to = $request->created_at_to ? $request->created_at_to : $created_at_from;
        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y');
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 1;
        $mindate = "$year-$month-01 00:00:00";
        $maxdate = date("Y-m-t 23:59:59", strtotime($mindate));

        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => today
            $rangeDate = Carbon::now();
            $query->where('created_at','=', $rangeDate->format('Y-m-d'));
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

        $items = $query->orderBy('id', 'desc')->paginate(2000);
        $arr['total'] = $arr['chua_sd'] = $arr['da_sd'] = $arr['total_money'] = $arr['tu_lai'] = 0;
        $arr['hh_khach'] = $arr['hh_tx'] = $arr['hh_cty'] = $arr['hh_sales'] = 0;
        $arr['cong_no_cty'] = $arr['cong_no_sales'] = $arr['da_tt_cty'] = $arr['da_tt_sales'] = 0;
        if($items->count() > 0){
            foreach($items as $item){
                $arr['total']++;
                if($item->status == 1){
                    $arr['chua_sd']++;
                }else{
                     $arr['da_sd']++;
                }
                // tu lai
                if($item->tu_lai == 1) $arr['tu_lai']++;
                $arr['total_money'] += $item->total_money;
                $arr['hh_khach'] += $item->hh_khach;
                $arr['hh_tx'] += $item->hh_tx;
                $arr['hh_cty'] += $item->hh_cty;
                $arr['hh_sales'] += $item->hh_sales;
                if($item->is_pay_cty == 0){
                    $arr['cong_no_cty'] += ($item->hh_cty + $item->hh_sales);
                }else{
                    $arr['da_tt_cty'] += ($item->hh_cty + $item->hh_sales);
                }
                if($item->is_pay_sales == 0){
                    $arr['cong_no_sales'] += $item->hh_sales;
                }else{
                    $arr['da_tt_sales'] += $item->hh_sales;
                }
            }
        }
        $salesList = User::where('is_sales', 1)->where('status', 1)->get();
        return view('coupon-code.index', compact( 'items', 'arrSearch', 'restaurantList', 'restaurant_id', 'salesList', 'arr'));
    }

    public function indexOto(Request $request)
    {

        $partnerList = Partner::whereIn('cost_type_id',[52, 53])->get();
        $arrSearch['partner_id'] = $partner_id = $request->partner_id ? $request->partner_id : null;

        $arrSearch['status'] = $status = $request->status ?? null;
        $arrSearch['user_id'] = $user_id = $request->user_id ?? null;

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y');

        $query = CouponCode::whereRaw(1);

        if($status){
            $query->where('status', $status);
        }

        if($partner_id){
            $query->where('partner_id', $partner_id);
        }
        if(Auth::user()->role > 1){
            $query->where('user_id', Auth::user()->id);
        }else{
            if($user_id)  $query->where('user_id', $user_id);
        }

        $arrSearch['created_at_from'] = $created_at_from = $request->created_at_from ? $request->created_at_from : date('d/m/Y', time());
        $arrSearch['created_at_to'] = $created_at_to = $request->created_at_to ? $request->created_at_to : $created_at_from;
        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y');
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 1;
        $mindate = "$year-$month-01 00:00:00";
        $maxdate = date("Y-m-t 23:59:59", strtotime($mindate));

        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => today
            $rangeDate = Carbon::now();
            $query->where('created_at','=', $rangeDate->format('Y-m-d'));
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

        $items = $query->orderBy('id', 'desc')->paginate(2000);
        $arr['total'] = $arr['chua_sd'] = $arr['da_sd'] = $arr['total_money'] = $arr['tu_lai'] = 0;
        $arr['hh_khach'] = $arr['hh_tx'] = $arr['hh_cty'] = $arr['hh_sales'] = 0;
        $arr['cong_no_cty'] = $arr['cong_no_sales'] = $arr['da_tt_cty'] = $arr['da_tt_sales'] = 0;
        if($items->count() > 0){
            foreach($items as $item){
                $arr['total']++;
                if($item->status == 1){
                    $arr['chua_sd']++;
                }else{
                     $arr['da_sd']++;
                }
                // tu lai
                if($item->tu_lai == 1) $arr['tu_lai']++;
                $arr['total_money'] += $item->total_money;
                $arr['hh_khach'] += $item->hh_khach;
                $arr['hh_tx'] += $item->hh_tx;
                $arr['hh_cty'] += $item->hh_cty;
                $arr['hh_sales'] += $item->hh_sales;
                if($item->is_pay_cty == 0){
                    $arr['cong_no_cty'] += ($item->hh_cty + $item->hh_sales);
                }else{
                    $arr['da_tt_cty'] += ($item->hh_cty + $item->hh_sales);
                }
                if($item->is_pay_sales == 0){
                    $arr['cong_no_sales'] += $item->hh_sales;
                }else{
                    $arr['da_tt_sales'] += $item->hh_sales;
                }
            }
        }
        $salesList = User::where('is_sales', 1)->where('status', 1)->get();
        return view('coupon-code.index-oto', compact( 'items', 'arrSearch', 'partnerList', 'partner_id', 'salesList', 'arr'));
    }


    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $restaurantList = Restaurants::where(
            [
                'status' => 1,
                'co_chi' => 1
            ])->get();
        $restaurant_id = $request->restaurant_id ? $request->restaurant_id : null;
        $created_at = $request->created_at ? $request->created_at : null;
        $cateList = CostType::orderBy('display_order')->get();
        return view('coupon-code.create', compact('restaurant_id', 'created_at', 'cateList', 'restaurantList'));
    }
    public function changeValue(Request $request){
        $value = $request->value;
        $column = $request->col;
        $id = $request->id;
        $rs = CouponCode::find($id);
        $time = date('Y-m-d H:i:s', time());
        $arrUpdate[$column] = $value;
        if($column == "is_pay_cty"){
            $arrUpdate['pay_cty_time'] = $time;
        }elseif($column == "is_pay_sales"){
            $arrUpdate['pay_sales_time'] = $time;
        }
        $rs->update($arrUpdate);
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
            'restaurant_id' => 'required',
        ],
        [
            'restaurant_id.required' => 'Bạn chưa chọn nhà hàng/đối tác.',
        ]);
        $restaurant_id = $dataArr['restaurant_id'];
        $user_id = Auth::user()->id;
        $detailShop = Shop::find($restaurant_id);

        $code = $detailShop->precode.$user_id.'-'.rand(1000,9999);
        $created_at = date('Y-m-d');
        CouponCode::create(['code' => $code, 'zalo_id' => Auth::user()->zalo_id, 'ctv_id' => $user_id, 'user_id' => $user_id,  'restaurant_id' => $restaurant_id, 'created_at' => $created_at]);

        Session::flash('message', 'Tạo mới thành công');
        $created_at_format = date('d/m/Y');
        return redirect()->route('coupon-code.index', ['use_date_from' => $created_at_format]);
    }
    public function update(Request $request)
    {
        $dataArr = $request->all();
        $cost_id = $dataArr['id'];
        $model= CouponCode::findOrFail($cost_id);
        $this->validate($request,[
            'created_at' => 'required',
            'nguoi_chi' => 'required'
        ],
        [
            'created_at.required' => 'Bạn chưa nhập ngày',
            'nguoi_chi.required' => 'Bạn chưa chọn người chi tiền',
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
        //dd($dataArr);
        $dataArr['total_money'] = (int) str_replace(',', '', $dataArr['total_money']);
        $dataArr['price'] = (int) str_replace(',', '', $dataArr['price']);
        $created_at = $dataArr['created_at'];
        $tmpDate = explode('/', $dataArr['created_at']);
        $dataArr['created_at'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        $model->update($dataArr);

        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('coupon-code.index', ['use_date_from' => $created_at]);
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

        $detail = CouponCode::find($id);
        $cateList = CostType::orderBy('display_order')->get();
        return view('coupon-code.edit', compact( 'detail', 'cateList'));
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
        $model = CouponCode::find($id);
        $oldStatus = $model->status;
        $model->update(['status'=>0]);
        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('coupon-code.index');
    }
}
