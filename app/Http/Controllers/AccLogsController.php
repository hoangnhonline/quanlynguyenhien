<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Cost;
use App\Models\PaymentRequest;
use Jenssegers\Agent\Agent;
use App\Models\AccLogs;
use App\Models\Payer;
use Maatwebsite\Excel\Facades\Excel;
use App\User;
use Illuminate\Support\Str;

use Helper, File, Session, Auth, Image, Hash;

class AccLogsController extends Controller
{
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

        $arrSearch['type'] = $type = $request->type ?? null;
        $arrSearch['code'] = $code = $request->code ?? null;
        $arrSearch['tbl'] = $tbl = $request->tbl ?? null;
        $arrSearch['nguoi_yeu_cau'] = $nguoi_yeu_cau = $request->nguoi_yeu_cau ?? null;
        $arrSearch['nguoi_thuc_hien'] = $nguoi_thuc_hien = $request->nguoi_thuc_hien ?? null;
        $arrSearch['status'] = $status = $request->status ?? 1;
        $arrSearch['search_by'] = $search_by = $request->search_by ?? 'time_yeu_cau';     
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $date_use = date('d/m/Y');
        $partnerList = (object) [];
        
        $col = $search_by;
        $query = AccLogs::where('status', $status);

        
            if($type){
                $query->where('type', $type);
            }
            if($code){
                $query->where('code', $code);
            }
            if($tbl){
                $query->where('tbl', $tbl);
            }
            if($nguoi_yeu_cau){
                $query->where('nguoi_yeu_cau', $nguoi_yeu_cau);
            }
            if($nguoi_thuc_hien){
                $query->where('nguoi_thuc_hien', $nguoi_thuc_hien);
            }
            if($status){
                $query->where('status', $status);
            }
            if($time_type == 1){
                $arrSearch[$col.'_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
                $arrSearch[$col.'_to'] = $use_date_to = date('d/m/Y', strtotime($maxdate));
                          
                $query->where($col,'>=', $mindate);                   
                $query->where($col, '<=', $maxdate);
            }elseif($time_type == 2){
                $arrSearch[$col.'_from'] = $use_date_from = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
                $arrSearch[$col.'_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;

                if($use_date_from){
                    $arrSearch[$col.'_from'] = $use_date_from;
                    $tmpDate = explode('/', $use_date_from);
                    $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
                    $query->where($col,'>=', $use_date_from_format);
                }
                if($use_date_to){
                    $arrSearch[$col.'_to'] = $use_date_to;
                    $tmpDate = explode('/', $use_date_to);
                    $use_date_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];   
                    if($use_date_to_format < $use_date_from_format){
                        $arrSearch[$col.'_to'] = $use_date_from;
                        $use_date_to_format = $use_date_from_format;   
                    }        
                    $query->where($col, '<=', $use_date_to_format);
                }
            }else{
                $arrSearch[$col.'_from'] = $use_date_from = $arrSearch[$col.'_to'] = $use_date_to = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
                
                $arrSearch[$col.'_from'] = $use_date_from;
                $tmpDate = explode('/', $use_date_from);
                $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
                $query->where($col,'=', $use_date_from_format);
            
            }
        
        $items = $query->orderBy('id', 'desc')->paginate(10000);

        return view('acc-logs.index', compact( 'items', 'status', 'type', 'nguoi_yeu_cau', 'nguoi_thuc_hien', 'tbl', 'code', 'arrSearch', 'time_type', 'use_date_from', 'use_date_to', 'month', 'year'));
    }

}
