<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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

use Maatwebsite\Excel\Facades\Excel;
use App\User;
use Illuminate\Support\Str;

use Helper, File, Session, Auth, Image, Hash, DB;

class CostController extends Controller
{
    private $_minDateKey = 0;
    private $_maxDateKey = 1;

    public function groupCodeChiTien(Request $request){

        $code_chi_tien = $request->code_chi_tien ?? null;
        $query = Cost::where('status', 3)->select('status', 'cate_id', 'partner_id', 'code_chi_tien', 'bank_info_id', DB::raw('SUM(total_money) AS tong_tien'))->whereNotNull('code_chi_tien');
        if($code_chi_tien){
            $query->where('code_chi_tien', $code_chi_tien);
        }
        $items = $query->groupBy('code_chi_tien')->get();

        $bankInfoList = BankInfo::all();
        $vietNameBanks = \App\Helpers\Helper::getVietNamBanks();
        $costCate = CostCate::all();

        return view('cost.group-code', compact('items', 'bankInfoList', 'vietNameBanks', 'code_chi_tien', 'costCate'));


    }
    public function parseSms(Request $request){
        $dataArr['body'] = $request->sms;
        Helper::smsParser($dataArr);
    }
    public function cal(){
       // $all = Cost::all();
       // foreach($all as $a){
       //      $date_use = $a->date_use;
       //      foreach($a->details as $b){
       //          $b->update(['date_use' => $date_use]);
       //      }
       // }
    }
    public function changeValueByColumn(Request $request){
        $id = $request->id;
        $column = $request->col;
        $value = $request->value;
        $model = Cost::find($id);
        $oldValue = $model->$column;

        $model->update([$column => $value]);

        //write logs
        Logs::create([
            'table_name' => 'cost',
            'user_id' => Auth::user()->id,
            'action' => 2,
            'content' => json_encode([$column => $value]),
            'old_content' => json_encode([$column => $oldValue]),
            'object_id' => $model->id
        ]);
    }
    public function getConfirmUng(Request $request){
        $str_id = $request->str_id;
        $tmp = explode(',', $str_id);
        $total_money = 0;
        $arrCost = [];
        if(!empty($tmp)){
            $code_ung_tien = Str::upper(Str::random(7));

            foreach($tmp as $cost_id){
                if($cost_id > 0){
                    $arrCost[$cost_id] = Cost::find($cost_id)->total_money;
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
            foreach($tmp as $cost_id){
                if($cost_id > 0){
                    $arrCost[$cost_id] = Cost::find($cost_id)->total_money;
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

            foreach($tmp as $cost_id){
                if($cost_id > 0){
                    $detail = Cost::find($cost_id);
                    if(!$detail->code_ung_tien){
                        $total_money += $detail->total_money;
                        $detail->update([
                            'code_ung_tien' => $code_ung_tien,
                            'time_code_ung_tien' => date('Y-m-d H:i:s')
                        ]);

                        //write logs
                        Logs::create([
                            'table_name' => 'cost',
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
                    'tbl' => 1,
                    'type' => 2,
                    'time_yeu_cau' => date('Y-m-d H:i:s'),
                    'status' => 1

                ]);
                return "ND: ADV ".$code_ung_tien. " ". $total_money;

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

            foreach($tmp as $cost_id){
                if($cost_id > 0){
                    $detail = Cost::find($cost_id);
                    if(!$detail->code_chi_tien){
                        $total_money += $detail->total_money;
                        $detail->update([
                            'code_chi_tien' => $code_chi_tien,
                            'time_code_chi_tien' => date('Y-m-d H:i:s')
                        ]);
                        //write logs
                        Logs::create([
                            'table_name' => 'cost',
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
                return "ND: EXP ".$code_chi_tien. " ". $total_money;

            }else{
                return "Đã có lỗi xảy ra!!!";
            }

        }else{
            return "CHƯA CHỌN MỤC NÀO!!!";
        }

    }
    public function ajaxGetCostType(Request $request){
        $type = $request->type;
        $list = CostType::where('status', 1)->get();
        return view('cost.ajax-cost-type', compact('list'));
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
        $arrSearch['code_ung_tien'] = $code_ung_tien = $request->code_ung_tien ?? null;
        $arrSearch['code_chi_tien'] = $code_chi_tien = $request->code_chi_tien ?? null;
        $arrSearch['multi'] = $multi = $request->multi ?? 0;
        $arrSearch['old'] = $old = $request->old ?? 0;
         $arrSearch['status'] = $status = $request->status ?? null;
         $currentDate = Carbon::now();

        if(is_int($request->cate_id)){
            $request->cate_id = (array) $request->cate_id;
        }
       // dd($request->cate_id);
        if($multi == 0){
            $arrSearch['cate_id'] = $cate_id = $request->cate_id ? $request->cate_id : null;
        }else{
            $arrSearch['cate_id'] = $cate_id = null;
            $arrSearch['cate_id_multi'] = $cate_id_multi = $request->cate_id_multi ? $request->cate_id_multi : [];
        }

        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $arrSearch['partner_id'] = $partner_id = $request->partner_id ? $request->partner_id : null;
        $arrSearch['nguoi_chi'] = $nguoi_chi = $request->nguoi_chi ? $request->nguoi_chi : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['is_fixed'] = $is_fixed = $request->is_fixed ?? null;
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ?? null;
        $arrSearch['tour_no'] = $tour_no = $request->tour_no ?? null;
        $arrSearch['hoang_the'] = $hoang_the = $request->hoang_the ?? null;
        $arrSearch['id_search'] = $id_search = $request->id_search ?? null;
        $content = $request->content ? $request->content : null;
        $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
        $date_use = date('d/m/Y');
        $partnerList = (object) [];

        $query = Cost::where('status', '<>', 0);

        if($id_search){
           //  dd($id_search);
            $id_search = strtolower($id_search);
            $id_search = str_replace("cp", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
        }else{
            if($nguoi_chi){
                $query->where('nguoi_chi', $nguoi_chi);
            }
            if($tour_id){
                $query->where('tour_id', $tour_id);
            }
            if($status){
                $query->where('status', $status);
            }
            if($tour_no){
                $query->where('tour_no', $tour_no);
            }
            if($tour_no){
                $query->where('tour_no', $tour_no);
            }
            if($code_ung_tien){
                $query->where('code_ung_tien', $code_ung_tien);
            }
            if($code_chi_tien){
                $query->where('code_chi_tien', $code_chi_tien);
            }
            if($city_id){
                $query->where('city_id', $city_id);
            }
            if($type){
                $query->where('type', $type);
            }
            if($partner_id){
                $query->where('partner_id', $partner_id);
            }

            if($multi == 0){
                if($cate_id){
                    $query->where('cate_id', $cate_id);

                    $detailCate = CostType::find($cate_id);
                    if($detailCate->only_staff == 1 && $old == 0){
                        $partnerList = User::where(['partner'=> 1, 'status' => 1])->get();
                    }else{
                        $partnerList = Partner::getList(['cost_type_id'=> $cate_id]);
                    }
                }
            }else{
                if(!empty($cate_id_multi)){
                    $query->whereIn('cate_id', $cate_id_multi);
                }
            }

            if($time_type == 1){
                $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
                $arrSearch['use_date_to'] = $use_date_to = date('d/m/Y', strtotime($maxdate));

                $query->where('date_use','>=', $mindate);
                $query->where('date_use', '<=', $maxdate);
            }elseif($time_type == 2){
                $arrSearch['use_date_from'] = $use_date_from = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
                $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;

                if($use_date_from){
                    $arrSearch['use_date_from'] = $use_date_from;
                    $tmpDate = explode('/', $use_date_from);
                    $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    $query->where('date_use','>=', $use_date_from_format);
                }
                if($use_date_to){
                    $arrSearch['use_date_to'] = $use_date_to;
                    $tmpDate = explode('/', $use_date_to);
                    $use_date_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    if($use_date_to_format < $use_date_from_format){
                        $arrSearch['use_date_to'] = $use_date_from;
                        $use_date_to_format = $use_date_from_format;
                    }
                    $query->where('date_use', '<=', $use_date_to_format);
                }
            }else{
                $arrSearch['use_date_from'] = $use_date_from = $arrSearch['use_date_to'] = $use_date_to = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());

                $arrSearch['use_date_from'] = $use_date_from;
                $tmpDate = explode('/', $use_date_from);
                $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                $query->where('date_use','=', $use_date_from_format);

            }

            if($is_fixed == 1){
                    $query->where('is_fixed', 1);
                }
            if($hoang_the == 1){
                $query->where('hoang_the', 1);
            }
        }

        $items = $query->orderBy('id', 'desc')->paginate(10000);
        $total_actual_amount = $total_quantity = 0;
        foreach($items as $o){
            $total_actual_amount+= $o->total_money;
            $total_quantity += $o->amount;
        }

        $cateList = CostType::where(['status' => 1, 'have_partner' => 0])->orderBy('display_order')->get();

        $agent = new Agent();
        if($agent->isMobile()){
            $view = 'cost.m-index';
        }else{
            $view = 'cost.index';
        }
        $costCate = CostCate::all();

        $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();
        return view($view, compact( 'items', 'content', 'cate_id', 'arrSearch', 'date_use', 'total_actual_amount', 'cateList', 'nguoi_chi', 'partnerList', 'partner_id', 'total_quantity', 'month', 'city_id', 'time_type','year', 'is_fixed', 'type', 'code_ung_tien', 'code_chi_tien', 'multi', 'costCate', 'tourSystem'));
    }
    public function export(Request $request)
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
        $arrSearch['code_ung_tien'] = $code_ung_tien = $request->code_ung_tien ?? null;
        $arrSearch['code_chi_tien'] = $code_chi_tien = $request->code_chi_tien ?? null;
        $arrSearch['multi'] = $multi = $request->multi ?? 0;
        $arrSearch['old'] = $old = $request->old ?? 0;
        $arrSearch['status'] = $status = $request->status ?? null;
        $currentDate = Carbon::now();

        if(is_int($request->cate_id)){
            $request->cate_id = (array) $request->cate_id;
        }
        // dd($request->cate_id);
        if($multi == 0){
            $arrSearch['cate_id'] = $cate_id = $request->cate_id ? $request->cate_id : null;
        }else{
            $arrSearch['cate_id'] = $cate_id = null;
            $arrSearch['cate_id_multi'] = $cate_id_multi = $request->cate_id_multi ? $request->cate_id_multi : [];
        }

        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $arrSearch['partner_id'] = $partner_id = $request->partner_id ? $request->partner_id : null;
        $arrSearch['nguoi_chi'] = $nguoi_chi = $request->nguoi_chi ? $request->nguoi_chi : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['is_fixed'] = $is_fixed = $request->is_fixed ?? null;
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ?? null;
        $arrSearch['tour_no'] = $tour_no = $request->tour_no ?? null;
        $arrSearch['hoang_the'] = $hoang_the = $request->hoang_the ?? null;
        $arrSearch['id_search'] = $id_search = $request->id_search ?? null;
        $content = $request->content ? $request->content : null;
        $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
        $date_use = date('d/m/Y');
        $partnerList = (object) [];

        $query = Cost::where('status', '<>', 0);

        if($id_search){
            //  dd($id_search);
            $id_search = strtolower($id_search);
            $id_search = str_replace("cp", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
        }else{
            if($nguoi_chi){
                $query->where('nguoi_chi', $nguoi_chi);
            }
            if($tour_id){
                $query->where('tour_id', $tour_id);
            }
            if($status){
                $query->where('status', $status);
            }
            if($tour_no){
                $query->where('tour_no', $tour_no);
            }
            if($tour_no){
                $query->where('tour_no', $tour_no);
            }
            if($code_ung_tien){
                $query->where('code_ung_tien', $code_ung_tien);
            }
            if($code_chi_tien){
                $query->where('code_chi_tien', $code_chi_tien);
            }
            if($city_id){
                $query->where('city_id', $city_id);
            }
            if($type){
                $query->where('type', $type);
            }
            if($partner_id){
                $query->where('partner_id', $partner_id);
            }

            if($multi == 0){
                if($cate_id){
                    $query->where('cate_id', $cate_id);

                    $detailCate = CostType::find($cate_id);
                    if($detailCate->only_staff == 1 && $old == 0){
                        $partnerList = User::where(['partner'=> 1, 'status' => 1])->get();
                    }else{
                        $partnerList = Partner::getList(['cost_type_id'=> $cate_id]);
                    }
                }
            }else{
                if(!empty($cate_id_multi)){
                    $query->whereIn('cate_id', $cate_id_multi);
                }
            }

            if($time_type == 1){
                $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
                $arrSearch['use_date_to'] = $use_date_to = date('d/m/Y', strtotime($maxdate));

                $query->where('date_use','>=', $mindate);
                $query->where('date_use', '<=', $maxdate);
            }elseif($time_type == 2){
                $arrSearch['use_date_from'] = $use_date_from = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
                $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;

                if($use_date_from){
                    $arrSearch['use_date_from'] = $use_date_from;
                    $tmpDate = explode('/', $use_date_from);
                    $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    $query->where('date_use','>=', $use_date_from_format);
                }
                if($use_date_to){
                    $arrSearch['use_date_to'] = $use_date_to;
                    $tmpDate = explode('/', $use_date_to);
                    $use_date_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    if($use_date_to_format < $use_date_from_format){
                        $arrSearch['use_date_to'] = $use_date_from;
                        $use_date_to_format = $use_date_from_format;
                    }
                    $query->where('date_use', '<=', $use_date_to_format);
                }
            }else{
                $arrSearch['use_date_from'] = $use_date_from = $arrSearch['use_date_to'] = $use_date_to = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());

                $arrSearch['use_date_from'] = $use_date_from;
                $tmpDate = explode('/', $use_date_from);
                $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                $query->where('date_use','=', $use_date_from_format);

            }

            if($is_fixed == 1){
                $query->where('is_fixed', 1);
            }
            if($hoang_the == 1){
                $query->where('hoang_the', 1);
            }
        }

        $items = $query->orderBy('date_use', 'asc')->get();

        $cateList = CostType::where('status', 1)->pluck('name', 'id');
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
                'Ngày' => date('d/m', strtotime($item->date_use)),
                'Nội dung' => $item->cate_id > 0 && isset( $cateList[$item->cate_id]) ? $cateList[$item->cate_id] : "",
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
    public function ajaxDoiTac(Request $request){
        $cate_id = $request->cate_id;
        $city_id = $request->city_id ?? 1;
        $cate_id = $cate_id == 15 ? 14 : $cate_id;
        $detailCate = CostType::find($cate_id);
        if($detailCate->only_staff == 1){
            $partnerList = User::where(['partner'=> 1, 'status' => 1])->get();
        }else{
            $partnerList = Partner::getList(['cost_type_id'=> $cate_id, 'city_id' => $city_id]);
        }

        return view('cost.doi-tac', compact( 'partnerList'));
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
        if($cate_id){
            $detailCate = CostType::find($cate_id);
            if($detailCate->only_staff == 1){
                $partnerList = User::where(['partner'=> 1, 'status' => 1])->get();
            }else{
                $partnerList = Partner::getList(['cost_type_id'=> $cate_id]);
            }
        }


        $month = $request->month ?? null;
        $bankInfoList = BankInfo::all();
        $vietNameBanks = \App\Helpers\Helper::getVietNamBanks();
        $costCate = CostCate::all();
        $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();
        return view('cost.create', compact('cate_id', 'date_use', 'cateList', 'month', 'partnerList', 'bankInfoList', 'vietNameBanks', 'costCate', 'tourSystem'));
    }
    public function sms(Request $request)
    {


        return view('cost.sms');
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
            'date_use' => 'required',
            'nguoi_chi' => 'required',
            'city_id' => 'required',
            'cate_id' => 'required',
        ],
        [
            'date_use.required' => 'Bạn chưa nhập ngày',
            'city_id.required' => 'Bạn chưa chọn tỉnh thành',
            'nguoi_chi.required' => 'Bạn chưa chọn người chi tiền',
            'cate_id.required' => 'Bạn chưa chọn loại chi phí',
        ]);


        $dataArr['total_money'] = (int) str_replace(',', '', $dataArr['total_money']);

        $date_use = $dataArr['date_use'];
        $tmpDate = explode('/', $dataArr['date_use']);
        $dataArr['date_use'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        if($dataArr['image_url'] && $dataArr['image_name']){

            $tmp = explode('/', $dataArr['image_url']);

            if(!is_dir('uploads/'.date('Y/m/d'))){
                mkdir('uploads/'.date('Y/m/d'), 0777, true);
            }

            $destionation = date('Y/m/d'). '/'. end($tmp);

            File::move(config('plantotravel.upload_path').$dataArr['image_url'], config('plantotravel.upload_path').$destionation);

            $dataArr['image_url'] = $destionation;
        }
        $is_fixed = isset($dataArr['is_fixed']) ? 1 : 0;
        $hoang_the = 0;
        if(isset($dataArr['partner_id'])){
            $detailPartner = Partner::find($dataArr['partner_id']);
            $hoang_the = 1;
        }
        $noi_dung_ck = str_replace('PTH', 'PLAN ', $dataArr['noi_dung_ck']);
        $noi_dung_ck = str_replace('PTX', 'PLAN ', $dataArr['noi_dung_ck']);
        $noi_dung_ck = str_replace('PTT', 'PLAN ', $dataArr['noi_dung_ck']);
        $noi_dung_ck = str_replace('PTC', 'PLAN ', $dataArr['noi_dung_ck']);
        $noi_dung_ck = str_replace('PTB', 'PLAN ', $dataArr['noi_dung_ck']);
        $partner_id = isset($dataArr['partner_id']) ? $dataArr['partner_id'] : null;
        $detailCate = CostType::find($dataArr['cate_id']);
        $user_id_cost = $detailCate->only_staff == 1 ? $partner_id : null;
        $arr = [
            'date_use' => $dataArr['date_use'],
            'notes' => $dataArr['notes'],
            'total_money' => $dataArr['total_money'],
            //'content' => $dataArr['content'],
            'price' => (int) str_replace(',', '', $dataArr['price']),
            'amount' => $dataArr['amount'],
            'total_money' => (int) str_replace(',', '', $dataArr['total_money']),
            'image_url' => $dataArr['image_url'],
            'booking_id' => $dataArr['booking_id'],
            'cate_id' => $dataArr['cate_id'],
            'nguoi_chi' => $dataArr['nguoi_chi'],
            'notes' => $dataArr['notes'],
            'city_id' => $dataArr['city_id'],
            'partner_id' => $partner_id,
            'hoang_the' => $hoang_the,
            'is_fixed' => $is_fixed,
            'type' => $dataArr['type'],
            'bank_info_id' => $dataArr['bank_info_id'],
            'noi_dung_ck' => $noi_dung_ck ,
            'status' => $dataArr['status'],
            'tour_no' => $dataArr['tour_no'],
            'user_id' => $user_id_cost,
            'tour_id' => $dataArr['tour_id']
        ];
        //dd($arr);
        $rs = Cost::create($arr);

        //write logs
        unset($dataArr['_token'], $dataArr['image_name']);
        Logs::create([
            'table_name' => 'cost',
            'user_id' => Auth::user()->id,
            'action' => 1,
            'content' => json_encode($arr),
            'object_id' => $rs->id
        ]);

        Session::flash('message', 'Tạo mới thành công');
        $month = date('m', strtotime($dataArr['date_use']));
        $year = date('Y', strtotime($dataArr['date_use']));
        return redirect()->route('cost.index', ['month' => $month, 'cate_id' => $dataArr['cate_id'], 'city_id' => $dataArr['city_id'], 'time_type' => 1, 'year' => $year]);
    }
    public function update(Request $request)
    {
        $dataArr = $request->all();
        $cost_id = $dataArr['id'];
        $model = Cost::findOrFail($cost_id);
        $oldData = $model->toArray();
        $this->validate($request,[
            'date_use' => 'required',
            'city_id' => 'required',
            'nguoi_chi' => 'required',
            'cate_id' => 'required',
        ],
        [
            'date_use.required' => 'Bạn chưa nhập ngày',
            'city_id.required' => 'Bạn chưa chọn tỉnh thành',
            'nguoi_chi.required' => 'Bạn chưa chọn người chi tiền',
            'cate_id.required' => 'Bạn chưa chọn loại chi phí',
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
        $date_use = $dataArr['date_use'];
        $tmpDate = explode('/', $dataArr['date_use']);
        $dataArr['date_use'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        $dataArr['is_fixed'] = isset($dataArr['is_fixed']) ? 1 : 0;
        $dataArr['hoang_the'] = 0;
        if(isset($dataArr['partner_id'])){
            $detailPartner = Partner::find($dataArr['partner_id']);
            $dataArr['hoang_the'] = 1;
        }

        $noi_dung_ck = str_replace('PTH', 'PLAN ', $dataArr['noi_dung_ck']);
        $noi_dung_ck = str_replace('PTX', 'PLAN ', $dataArr['noi_dung_ck']);
        $noi_dung_ck = str_replace('PTT', 'PLAN ', $dataArr['noi_dung_ck']);
        $noi_dung_ck = str_replace('PTC', 'PLAN ', $dataArr['noi_dung_ck']);
        $noi_dung_ck = str_replace('PTB', 'PLAN ', $dataArr['noi_dung_ck']);

        $dataArr['noi_dung_ck'] = $noi_dung_ck;

        $partner_id = isset($dataArr['partner_id']) ? $dataArr['partner_id'] : null;
        $detailCate = CostType::find($dataArr['cate_id']);

        if($detailCate->only_staff == 1){
            $dataArr['user_id'] = $partner_id;
            $dataArr['partner_id'] = null;
        }

        $model->update($dataArr);

        //write logs
        unset($dataArr['_token'], $dataArr['image_name']);
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
                'table_name' => 'cost',
                'user_id' => Auth::user()->id,
                'action' => 2,
                'content' => json_encode($contentDiff),
                'old_content' => json_encode($oldContent),
                'object_id' => $model->id
            ]);
        }


        Session::flash('message', 'Cập nhật thành công');

        $month = date('m', strtotime($dataArr['date_use']));
        $year = date('Y', strtotime($dataArr['date_use']));
        return redirect()->route('cost.index', ['month' => $month, 'cate_id' => $dataArr['cate_id'], 'city_id' => $dataArr['city_id'], 'time_type' => 1, 'year' => $year]);
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

        $detail = Cost::find($id);
        if($detail->code_ung_tien || $detail->time_chi_tien){
           // return view('ko-cap-nhat');
        }
        $detailCate = CostType::find($detail->cate_id);
        if($detailCate->only_staff == 1){
            $partnerList = User::where(['partner'=> 1, 'status' => 1])->get();
        }else{
            $partnerList = Partner::getList(['cost_type_id'=> $detail->cate_id]);
        }

        $cateList = CostType::where('status', 1)->orderBy('display_order')->get();
        $payerList = Payer::all();
        $payerNameArr = [];
        foreach($payerList as $pay){
            $payerNameArr[$pay->id] = $pay->name;
        }
        $bankInfoList = BankInfo::all();
        $vietNameBanks = \App\Helpers\Helper::getVietNamBanks();
        $costCate = CostCate::all();
        $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();
        return view('cost.edit', compact( 'detail', 'cateList', 'partnerList', 'payerList', 'payerNameArr', 'bankInfoList', 'vietNameBanks', 'costCate', 'tourSystem'));
    }
    public function copy($id)
    {

        $detail = Cost::find($id);
        $cateList = CostType::where('status', 1)->orderBy('display_order')->get();

        $partnerList = Partner::getList(['cost_type_id'=> $detail->cate_id]);
        $costCate = CostCate::all();
        $bankInfoList = BankInfo::all();
        $vietNameBanks = \App\Helpers\Helper::getVietNamBanks();
        return view('cost.copy', compact( 'detail', 'cateList', 'partnerList', 'costCate', 'bankInfoList', 'vietNameBanks'));
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
        $model = Cost::find($id);
        if($model->code_ung_tien || $model->time_chi_tien){
            return view('ko-cap-nhat');
        }
        $oldStatus = $model->status;
        $model->update(['status' => 0]);

        Logs::create([
            'table_name' => 'cost',
            'user_id' => Auth::user()->id,
            'action' => 3,
            'content' => json_encode(['status' => 0]),
            'object_id' => $model->id
        ]);

        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('cost.index');
    }

    public function viewQRCode(Request $request){
        $data = Cost::where('id', $request->id)->first();
        $data->qrcode_clicked = !empty($data->qrcode_clicked) ? $data->qrcode_clicked + 1 : 1;
        $data->save();
        return response()->json(['data' => $data->qrcode_clicked]);
    }
}
