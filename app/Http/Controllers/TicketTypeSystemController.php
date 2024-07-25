<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TicketTypeSystem;

use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;

class TicketTypeSystemController extends Controller
{
    
    public function edit($id, Request $request)
    {

        $detail = TicketTypeSystem::find($id);

        return view('ticket-type-system.edit', compact( 'detail'));
    

    }
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
  
    public function index(Request $request)
    {
        $status = $request->status ?? null;
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $query = TicketTypeSystem::where('city_id', $city_id);
        if($status){
            $query->where('status', $status);
        }
        
        $items  = $query->orderBy('display_order')->get();
        $view = 'ticket-type-system.index';
           
        return view($view, compact( 'items', 'status', 'city_id'));
       
    }
    

    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request) { 
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);  
        $view = 'ticket-type-system.create';
        return view($view, compact('city_id'));
        
        
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
            'name' => 'required',
            'price' => 'required', 
        ],
        [  
            'name.required' => 'Bạn chưa nhập tên',
            'price.required' => 'Bạn chưa nhập giá',
           
        ]);       
       
        $dataArr['price'] = str_replace(',', '', $dataArr['price']);        


        $rs = TicketTypeSystem::create($dataArr);
        
        Session::flash('message', 'Tạo mới thành công');
        return redirect()->route('ticket-type-system.index');  
    }
  
    
     public function update(Request $request)
    {
        $dataArr = $request->all();
        
        $this->validate($request,[
            'name' => 'required',
            'price' => 'required', 
        ],
        [  
            'name.required' => 'Bạn chưa nhập tên',
            'price.required' => 'Bạn chưa nhập giá',
           
        ]);       
       
        $dataArr['price'] = str_replace(',', '', $dataArr['price']);        

        $detail = TicketTypeSystem::find($dataArr['id']);
      
        $detail->update($dataArr);       
        Session::flash('message', 'Cập nhật thành công');
        return redirect()->route('ticket-type-system.index'); 
    }
    /**
    * Update the specified resource in storage.
    *
    * @param  Request  $request
    * @param  int  $id
    * @return Response
    */
    

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function destroy($id)
    {
        // delete
        $model = TicketTypeSystem::find($id);        
        $use_date = date('d/m/Y', strtotime($model->use_date));
        $type = $model->type;
		$model->update(['status' => 0]);		
        // redirect
        Session::flash('message', 'Xóa thành công');        
        return redirect()->route('ticket-type-system.index');   
    }

  
}
