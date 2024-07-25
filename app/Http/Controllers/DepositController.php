<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Helper, File, Session, Auth, Str;

class DepositController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function changeValueByColumn(Request $request){
        $id = $request->id;
        $column = $request->col;
        $value = $request->value;
        $model = Deposit::find($id);
        $model->update([$column => $value]);
    }
    public function index(Request $request)
    {     
        
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        
        $nguoi_nhan_tien = $request->nguoi_nhan_tien ?? null;
        $nguoi_nop_tien = $request->nguoi_nop_tien ?? null;
        $city_id = $request->city_id ?? null;
        $arrSearch['code_nop_tien'] = $code_nop_tien = $request->code_nop_tien ?? null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 1;
        $arrSearch['status'] = $status = $request->status ?? null;
        $query = Deposit::whereRaw('1');

        if($nguoi_nhan_tien){
            $query->where('nguoi_nhan_tien', $nguoi_nhan_tien);
        }
        if($status){
            $query->where('status', $status);
        }
        if($nguoi_nop_tien){
            $query->where('nguoi_nop_tien', $nguoi_nop_tien);
        }
        if($code_nop_tien){
            $query->where('code_nop_tien', $code_nop_tien);
        }
        if($city_id){
            $query->where('city_id', $city_id);
        }

        if($time_type == 1){
            $arrSearch['deposit_date_from'] = $deposit_date_from = $date_use = date('d/m/Y', strtotime($mindate));
            $arrSearch['deposit_date_to'] = $deposit_date_to = date('d/m/Y', strtotime($maxdate));
                      
            $query->where('deposit_date','>=', $mindate);                   
            $query->where('deposit_date', '<=', $maxdate);
        }elseif($time_type == 2){
            $arrSearch['deposit_date_from'] = $deposit_date_from = $date_use = $request->deposit_date_from ? $request->deposit_date_from : date('d/m/Y', time());
            $arrSearch['deposit_date_to'] = $deposit_date_to = $request->deposit_date_to ? $request->deposit_date_to : $deposit_date_from;

            if($deposit_date_from){
                $arrSearch['deposit_date_from'] = $deposit_date_from;
                $tmpDate = explode('/', $deposit_date_from);
                $deposit_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
                $query->where('deposit_date','>=', $deposit_date_from_format);
            }
            if($deposit_date_to){
                $arrSearch['deposit_date_to'] = $deposit_date_to;
                $tmpDate = explode('/', $deposit_date_to);
                $deposit_date_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];   
                if($deposit_date_to_format < $deposit_date_from_format){
                    $arrSearch['deposit_date_to'] = $deposit_date_from;
                    $deposit_date_to_format = $deposit_date_from_format;   
                }        
                $query->where('deposit_date', '<=', $deposit_date_to_format);
            }
        }else{
            $arrSearch['deposit_date_from'] = $deposit_date_from = $arrSearch['deposit_date_to'] = $deposit_date_to = $date_use = $request->deposit_date_from ? $request->deposit_date_from : date('d/m/Y', time());
            
            $arrSearch['deposit_date_from'] = $deposit_date_from;
            $tmpDate = explode('/', $deposit_date_from);
            $deposit_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
            $query->where('deposit_date','=', $deposit_date_from_format);

        }
       
        $items = $query->orderBy('id', 'desc')->paginate(50);
        $totalMoney = 0;
        foreach($items as $item){
            $totalMoney+= $item->amount;
        }
        return view('deposit.index', compact( 'items', 'nguoi_nhan_tien', 'nguoi_nop_tien', 'arrSearch', 'month', 'city_id', 'time_type', 'totalMoney', 'year', 'status'));
    }
    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $nguoi_nhan_tien = $request->nguoi_nhan_tien ?? null;     
        $back_url = $request->back_url ?? null;
        return view('deposit.create', compact('nguoi_nhan_tien', 'back_url'));
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
            'amount' => 'required',
            'nguoi_nhan_tien' => 'required',
            'nguoi_nop_tien' => 'required',
            'deposit_date' => 'required',

        ],
        [  
            'amount.required' => 'Bạn chưa nhập số tiền',
            'nguoi_nhan_tien.required' => 'Bạn chưa chọn người nhận tiền',
            'nguoi_nop_tien.required' => 'Bạn chưa chọn người nộp tiền',
            'deposit_date.required' => 'Bạn chưa nhập ngày chuyển tiền',
        ]);       
        $deposit_date = explode('/', $dataArr['deposit_date']);
        $dataArr['deposit_date'] = $deposit_date[2]."-".$deposit_date[1]."-".$deposit_date[0];
        $dataArr['amount'] = str_replace(",", "", $dataArr['amount']);
        $dataArr['code_nop_tien'] = Str::upper(Str::random(7));
        if($dataArr['image_url'] && $dataArr['image_name']){
            
            $tmp = explode('/', $dataArr['image_url']);

            if(!is_dir('uploads/'.date('Y/m/d'))){
                mkdir('uploads/'.date('Y/m/d'), 0777, true);
            }

            $destionation = date('Y/m/d'). '/'. end($tmp);
            
            File::move(config('plantotravel.upload_path').$dataArr['image_url'], config('plantotravel.upload_path').$destionation);
            
            $dataArr['image_url'] = $destionation;
        }        

        $rs = Deposit::create($dataArr);

        Session::flash('message', 'Tạo mới thành công');
        $month = date('m', strtotime($dataArr['deposit_date']));
        return redirect()->route('deposit.index', [ 'month' => $month]);
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
        $detail = Deposit::find($id);
        return view('deposit.edit', compact( 'detail'));
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  Request  $request
    * @param  int  $id
    * @return Response
    */
    public function update(Request $request)
    {
        $dataArr = $request->all();
        
         $this->validate($request,[   
            'amount' => 'required',
            'nguoi_nhan_tien' => 'required',
            'nguoi_nop_tien' => 'required',
            'deposit_date' => 'required',

        ],
        [  
            'amount.required' => 'Bạn chưa nhập số tiền',
            'nguoi_nhan_tien.required' => 'Bạn chưa chọn người nhận tiền',
            'nguoi_nop_tien.required' => 'Bạn chưa chọn người nộp tiền',
            'deposit_date.required' => 'Bạn chưa nhập ngày chuyển tiền',
        ]);       
        $deposit_date = explode('/', $dataArr['deposit_date']);
        $dataArr['deposit_date'] = $deposit_date[2]."-".$deposit_date[1]."-".$deposit_date[0];
        $dataArr['amount'] = str_replace(",", "", $dataArr['amount']);

        if($dataArr['image_url'] && $dataArr['image_name']){
            
            $tmp = explode('/', $dataArr['image_url']);

            if(!is_dir('uploads/'.date('Y/m/d'))){
                mkdir('uploads/'.date('Y/m/d'), 0777, true);
            }

            $destionation = date('Y/m/d'). '/'. end($tmp);
            
            File::move(config('plantotravel.upload_path').$dataArr['image_url'], config('plantotravel.upload_path').$destionation);
            
            $dataArr['image_url'] = $destionation;
        }
        
        $model = Deposit::find($dataArr['id']);  

        $model->update($dataArr);

        Session::flash('message', 'Cập nhật thành công');

        $month = date('m', strtotime($dataArr['deposit_date']));
        return redirect()->route('deposit.index', [ 'month' => $month]);
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
        $model = Deposit::find($id);
        $model->update(['status' => 0]);

        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('deposit.index', ['nguoi_nhan_tien' => $model->nguoi_nhan_tien]);
    }
}