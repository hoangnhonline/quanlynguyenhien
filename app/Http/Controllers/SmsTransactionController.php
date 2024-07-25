<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SmsTransaction;
use Jenssegers\Agent\Agent;
use App\Models\AccLogs;
use App\Models\Booking;
use App\Models\PaymentRequest;
use App\Models\SmsTransactionMap;
use App\Models\Cost;
use Maatwebsite\Excel\Facades\Excel;
use App\User;
use Illuminate\Support\Str;

use Helper, File, Session, Auth, Image, Hash;

class SmsTransactionController extends Controller
{

    
    public function changeValueByColumn(Request $request){
        $id = $request->id;
        $column = $request->col;
        $value = $request->value;
        $model = SmsTransaction::find($id);
        $model->update([$column => $value]);
    }
    
    public function ajaxGetCostType(Request $request){
        $type = $request->type;
        $list = CostType::where('status', 1)->get();
        return view('sms-transaction.ajax-cost-type', compact('list'));
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

        $arrSearch['type'] = $type = $request->type ? $request->type : null;
        $arrSearch['cate_id'] = $cate_id = $request->cate_id ?? -1;        
        $arrSearch['tai_khoan_doi_tac'] = $tai_khoan_doi_tac = $request->tai_khoan_doi_tac ? $request->tai_khoan_doi_tac : null;
        $arrSearch['ten_doi_tac'] = $ten_doi_tac = $request->ten_doi_tac ? $request->ten_doi_tac : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 2;
        $arrSearch['is_valid'] = $is_valid = $request->is_valid ?? 0;
        $arrSearch['is_process'] = $is_process = $request->is_process ?? -1;
        $arrSearch['ke_toan'] = $ke_toan = $request->ke_toan ?? null;
        $arrSearch['city_id'] = $city_id = $request->city_id ?? null;
        $arrSearch['tai_khoan_doi_tac'] = $tai_khoan_doi_tac = $request->tai_khoan_doi_tac ?? null;

        $ngay_giao_dich = date('d/m/Y');
        $partnerList = (object) [];
        
        $query = SmsTransaction::where('status', 1);
        if($ten_doi_tac){
            $query->where('ten_doi_tac', 'LIKE', '%'.$ten_doi_tac.'%');
        }

        if($city_id){
            $query->where('city_id', $city_id);
        }           
        if($cate_id > -1){
            $query->where('cate_id', $cate_id);
        }
        if($ke_toan){
            $query->where('ke_toan', $ke_toan);
        }
        if($type){
            $query->where('type', $type);
        }
        if($tai_khoan_doi_tac){
            $query->where('tai_khoan_doi_tac', $tai_khoan_doi_tac);
        }
             
        
        if($time_type == 1){
            $arrSearch['ngay_giao_dich_from'] = $ngay_giao_dich_from = $ngay_giao_dich = date('d/m/Y', strtotime($mindate));
            $arrSearch['ngay_giao_dich_to'] = $ngay_giao_dich_to = date('d/m/Y', strtotime($maxdate));
                      
            $query->where('ngay_giao_dich','>=', $mindate);                   
            $query->where('ngay_giao_dich', '<=', $maxdate);
        }elseif($time_type == 2){
            $arrSearch['ngay_giao_dich_from'] = $ngay_giao_dich_from = $ngay_giao_dich = $request->ngay_giao_dich_from ? $request->ngay_giao_dich_from : '01/01/2023';
            $arrSearch['ngay_giao_dich_to'] = $ngay_giao_dich_to = $request->ngay_giao_dich_to ? $request->ngay_giao_dich_to : date('d/m/Y', time());

            if($ngay_giao_dich_from){
                $arrSearch['ngay_giao_dich_from'] = $ngay_giao_dich_from;
                $tmpDate = explode('/', $ngay_giao_dich_from);
                $ngay_giao_dich_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
                $query->where('ngay_giao_dich','>=', $ngay_giao_dich_from_format);
            }
            if($ngay_giao_dich_to){
                $arrSearch['ngay_giao_dich_to'] = $ngay_giao_dich_to;
                $tmpDate = explode('/', $ngay_giao_dich_to);
                $ngay_giao_dich_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];   
                if($ngay_giao_dich_to_format < $ngay_giao_dich_from_format){
                    $arrSearch['ngay_giao_dich_to'] = $ngay_giao_dich_from;
                    $ngay_giao_dich_to_format = $ngay_giao_dich_from_format;   
                }        
                $query->where('ngay_giao_dich', '<=', $ngay_giao_dich_to_format);
            }
        }else{
            $arrSearch['ngay_giao_dich_from'] = $ngay_giao_dich_from = $arrSearch['ngay_giao_dich_to'] = $ngay_giao_dich_to = $ngay_giao_dich = $request->ngay_giao_dich_from ? $request->ngay_giao_dich_from : date('d/m/Y', time());
            
            $arrSearch['ngay_giao_dich_from'] = $ngay_giao_dich_from;
            $tmpDate = explode('/', $ngay_giao_dich_from);
            $ngay_giao_dich_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
            $query->where('ngay_giao_dich','=', $ngay_giao_dich_from_format);
        
        }
        if($is_valid > -1){                
                $query->where('is_valid', $is_valid);            
            }
        if($is_process > -1){                
            $query->where('is_process', $is_process);            
        }
        
      
        $items = $query->orderBy('ngay_giao_dich', 'asc')->get();    
        

        $agent = new Agent();
        if($agent->isMobile()){
            $view = 'sms-transaction.m-index';
        }else{
            $view = 'sms-transaction.index';
        }           
        $arrPhanLoai = [
            1 => 'Phí CTY',
            2 => 'Thanh toán công nợ',
            3 => 'HDV nộp tiền',
            4 => 'Kế toán nộp tiền',
            5 => 'Khách CK',
            6 => 'Sales CK',
            7 => 'Đối tác CK công nợ',
            8 => 'Doanh số Đà Nẵng',
            9 => 'A Phương rút tiền',
            10 => 'Khác'

        ];
        return view($view, compact( 'items', 'cate_id', 'arrSearch', 'ngay_giao_dich', 'month', 'time_type', 'year', 'type', 'arrPhanLoai'));
    }
    public function update(Request $request)
    {
        $is_valid = 0;
        $dataArr = $request->all();
        $sms_transaction_id = $dataArr['id'];
        $model= SmsTransaction::findOrFail($sms_transaction_id);
        SmsTransactionMap::where('sms_transaction_id', $sms_transaction_id)->delete();
        if(!empty($dataArr['booking_id'])){
            $is_valid = 1;
            foreach($dataArr['booking_id'] as $k => $booking_id){
                if(isset($dataArr['amount_booking'][$k])){
                    $amount = $dataArr['amount_booking'][$k];
                    $amount = str_replace(",", "", $amount);
                    SmsTransactionMap::create([
                        'sms_transaction_id' => $sms_transaction_id,
                        'code' => $booking_id,
                        'type' => 1,
                        'amount' => $amount
                    ]);
                }
            }
        }

        if($dataArr['code_yctt']){
            $is_valid = 1;
            $tmpArrPay = explode(',', $dataArr['code_yctt']);
            foreach($tmpArrPay as $payment_request_id){
                $payment_request_id  = str_replace(" ", "", $payment_request_id);
                if($payment_request_id > 0){
                    $detailPay = PaymentRequest::find($payment_request_id);
                    $amount = $detailPay->total_money;
                    SmsTransactionMap::create([
                        'sms_transaction_id' => $sms_transaction_id,
                        'code' => $payment_request_id,
                        'type' => 2,
                        'amount' => $amount
                    ]);
                }
            }
        }

        if($dataArr['code_cost']){
            $is_valid = 1;
            $tmpArrCost = explode(',', $dataArr['code_cost']);
            foreach($tmpArrCost as $cost_id){
                $cost_id  = str_replace(" ", "", $cost_id);
                if($cost_id > 0){
                    $detailCost = Cost::find($cost_id);
                    $amount = $detailCost->total_money;
                    SmsTransactionMap::create([
                        'sms_transaction_id' => $sms_transaction_id,
                        'code' => $cost_id,
                        'type' => 3,
                        'amount' => $amount
                    ]);
                }
            }
        }
        $model->update(['is_valid' => $is_valid]);
        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('sms-transaction.index');
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
        
        $detail = SmsTransaction::find($id);       
        $arrPhanLoai = [
            1 => 'Phí CTY',
            2 => 'Thanh toán công nợ',
            3 => 'HDV nộp tiền',
            4 => 'Kế toán nộp tiền',
            5 => 'Khách CK',
            6 => 'Sales CK',
            7 => 'Đối tác CK công nợ',
            8 => 'Doanh số Đà Nẵng',
            9 => 'A Phương rút tiền',
            10 => 'CK kế toán',
            11 => 'Khác'

        ]; 
        $maps = SmsTransactionMap::where('sms_transaction_id', $id)->get();
        $arrBk = $arrCost = $arrPay = [];
        if($maps){
            foreach($maps as $item){
                if($item->type == 1){
                    $arrBk[] = $item;
                }elseif($item->type == 2){
                    $arrPay[] = $item->code;
                }else{
                    $arrCost[] = $item->code;
                }
            }
        }
        $strCost = implode(",", $arrCost);
        $strPay = implode(",", $arrPay);
       //  $allBk = Booking::where('status', '<>', 0)->where('created_at', '>=', '2022-06-01 00:00:00')->get();
       // // dd($allBk);
       //  $allPay = PaymentRequest::where('created_at', '>=', '2022-06-01 00:00:00')->get();
        
       //  $allCost = Cost::where('created_at', '>=', '2022-06-01 00:00:00')->get();
        

        return view('sms-transaction.edit', compact( 'detail', 'arrPhanLoai', 'arrBk', 'strCost', 'strPay'));
    }
    public function copy($id)
    {
        
        $detail = SmsTransaction::find($id);       
        $cateList = CostType::where('status', 1)->orderBy('display_order')->get(); 
        
        $partnerList = Partner::getList(['cost_type_id'=> $detail->cate_id]); 
      
        return view('sms-transaction.copy', compact( 'detail', 'cateList', 'partnerList'));
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
        $model = SmsTransaction::find($id);
        if($model->code_ung_tien || $model->time_chi_tien){
            return view('ko-cap-nhat');
        }
        $oldStatus = $model->status;
        $model->update(['status'=>0]);      
        // redirect
        Session::flash('message', 'Xóa thành công');        
        return redirect()->route('sms-transaction.index');   
    }
}
