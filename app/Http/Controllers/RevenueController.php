<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Revenue;
use App\Models\Logs;

use Helper, File, Session, Auth;


class RevenueController extends Controller
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
        $model = Revenue::find($id);   
        $oldValue = $model->$column;

        $model->update([$column => $value]);

        //write logs
        Logs::create([
            'table_name' => 'revenue',
            'user_id' => Auth::user()->id,
            'action' => 2,
            'content' => json_encode([$column => $value]),
            'old_content' => json_encode([$column => $oldValue]),
            'object_id' => $model->id
        ]);
    }
    public function index(Request $request)
    {     
        
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $content = $request->content ?? null;
        $nguoi_thu_tien = $request->nguoi_thu_tien ?? null;
        $arrSearch['id'] = $id = $request->id ?? null;
        $city_id = $request->city_id ?? null;
        $not_kpi = $request->not_kpi ?? null;
        $status = $request->status ?? null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 1;
        $query = Revenue::where('status', '>', 0);
        if($id){
            $query->where('id', $id);
        }else{
            if($nguoi_thu_tien){
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }
             if($status){
                $query->where('status', $status);
            }
            if($content){
                $query->where('content', 'LIKE', '%'.$content.'%');
            }
            if($city_id){
                $query->where('city_id', $city_id);
            }
            if($not_kpi){
                $query->where('not_kpi', $not_kpi);
            }
            if($time_type == 1){
                $arrSearch['pay_date_from'] = $pay_date_from = $date_use = date('d/m/Y', strtotime($mindate));
                $arrSearch['pay_date_to'] = $pay_date_to = date('d/m/Y', strtotime($maxdate));
                          
                $query->where('pay_date','>=', $mindate);                   
                $query->where('pay_date', '<=', $maxdate);
            }elseif($time_type == 2){
                $arrSearch['pay_date_from'] = $pay_date_from = $date_use = $request->pay_date_from ? $request->pay_date_from : date('d/m/Y', time());
                $arrSearch['pay_date_to'] = $pay_date_to = $request->pay_date_to ? $request->pay_date_to : $pay_date_from;

                if($pay_date_from){
                    $arrSearch['pay_date_from'] = $pay_date_from;
                    $tmpDate = explode('/', $pay_date_from);
                    $pay_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
                    $query->where('pay_date','>=', $pay_date_from_format);
                }
                if($pay_date_to){
                    $arrSearch['pay_date_to'] = $pay_date_to;
                    $tmpDate = explode('/', $pay_date_to);
                    $pay_date_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];   
                    if($pay_date_to_format < $pay_date_from_format){
                        $arrSearch['pay_date_to'] = $pay_date_from;
                        $pay_date_to_format = $pay_date_from_format;   
                    }        
                    $query->where('pay_date', '<=', $pay_date_to_format);
                }
            }else{
                $arrSearch['pay_date_from'] = $pay_date_from = $arrSearch['pay_date_to'] = $pay_date_to = $date_use = $request->pay_date_from ? $request->pay_date_from : date('d/m/Y', time());
                
                $arrSearch['pay_date_from'] = $pay_date_from;
                $tmpDate = explode('/', $pay_date_from);
                $pay_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
                $query->where('pay_date','=', $pay_date_from_format);

            }
        }
        
       
        $items = $query->orderBy('id', 'desc')->paginate(50);
        $totalMoney = 0;
        foreach($items as $item){
            $totalMoney+= $item->amount;
        }
        return view('revenue.index', compact( 'items', 'content', 'nguoi_thu_tien', 'arrSearch', 'month', 'city_id', 'time_type', 'totalMoney', 'year', 'not_kpi'));
    }
    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $nguoi_thu_tien = $request->nguoi_thu_tien ?? null;     
        $back_url = $request->back_url ?? null;
        return view('revenue.create', compact('nguoi_thu_tien', 'back_url'));
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
            'nguoi_thu_tien' => 'required',
            'pay_date' => 'required',

        ],
        [  
            'amount.required' => 'Bạn chưa nhập số tiền',
            'nguoi_thu_tien.required' => 'Bạn chưa chọn người thu tiền',
            'pay_date.required' => 'Bạn chưa nhập ngày chuyển tiền',
        ]);       
        $pay_date = explode('/', $dataArr['pay_date']);
        $dataArr['pay_date'] = $pay_date[2]."-".$pay_date[1]."-".$pay_date[0];
        $dataArr['amount'] = str_replace(",", "", $dataArr['amount']);
        $not_kpi = isset($dataArr['not_kpi']) ? 1 : 0;
        if($dataArr['image_url'] && $dataArr['image_name']){
            
            $tmp = explode('/', $dataArr['image_url']);

            if(!is_dir('uploads/'.date('Y/m/d'))){
                mkdir('uploads/'.date('Y/m/d'), 0777, true);
            }

            $destionation = date('Y/m/d'). '/'. end($tmp);
            
            File::move(config('plantotravel.upload_path').$dataArr['image_url'], config('plantotravel.upload_path').$destionation);
            
            $dataArr['image_url'] = $destionation;
        }        

        $rs = Revenue::create($dataArr);

        //write logs
        unset($dataArr['_token'], $dataArr['image_name']);
        Logs::create([
            'table_name' => 'revenue',
            'user_id' => Auth::user()->id,
            'action' => 1,
            'content' => json_encode($dataArr),
            'object_id' => $rs->id
        ]);
        Session::flash('message', 'Tạo mới thành công');
        $month = date('m', strtotime($dataArr['pay_date']));
        return redirect()->route('revenue.index', [ 'month' => $month]);
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
        $detail = Revenue::find($id);
        return view('revenue.edit', compact( 'detail'));
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
            'nguoi_thu_tien' => 'required',
            'pay_date' => 'required',

        ],
        [  
            'amount.required' => 'Bạn chưa nhập số tiền',
            'nguoi_thu_tien.required' => 'Bạn chưa chọn người thu tiền',
            'pay_date.required' => 'Bạn chưa nhập ngày chuyển tiền',
        ]);       
        $pay_date = explode('/', $dataArr['pay_date']);
        $dataArr['pay_date'] = $pay_date[2]."-".$pay_date[1]."-".$pay_date[0];
        $dataArr['amount'] = str_replace(",", "", $dataArr['amount']);
        $not_kpi = isset($dataArr['not_kpi']) ? 1 : 0;
        if($dataArr['image_url'] && $dataArr['image_name']){
            
            $tmp = explode('/', $dataArr['image_url']);

            if(!is_dir('uploads/'.date('Y/m/d'))){
                mkdir('uploads/'.date('Y/m/d'), 0777, true);
            }

            $destionation = date('Y/m/d'). '/'. end($tmp);
            
            File::move(config('plantotravel.upload_path').$dataArr['image_url'], config('plantotravel.upload_path').$destionation);
            
            $dataArr['image_url'] = $destionation;
        }
        
        $model = Revenue::find($dataArr['id']);  
        
        $oldData = $model->toArray();

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
                'table_name' => 'revenue',
                'user_id' => Auth::user()->id,
                'action' => 2,
                'content' => json_encode($contentDiff),
                'old_content' => json_encode($oldContent),
                'object_id' => $model->id
            ]);
        }


        Session::flash('message', 'Cập nhật thành công');

        $month = date('m', strtotime($dataArr['pay_date']));
        return redirect()->route('revenue.index', [ 'month' => $month]);
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
        $model = Revenue::find($id);
        $model->update(['status' => 0]);

        Logs::create([
            'table_name' => 'revenue',
            'user_id' => Auth::user()->id,
            'action' => 3,
            'content' => json_encode(['status' => 0]),            
            'object_id' => $id
        ]);
        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('revenue.index', ['nguoi_thu_tien' => $model->nguoi_thu_tien]);
    }
}