<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Rating;

use Helper, File, Session, Auth, Image;

class AreaController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index(Request $request)
    {        
        $items = Area::where('status', 1)->paginate(20);        
        
        return view('area.index', compact( 'items'));
    }
    

    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    { 
        return view('area.create');
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
        ],
        [  
            'name.required' => 'Bạn chưa nhập tên',
        ]);       
        $dataArr['display_order'] = (int) $dataArr['display_order'];
        
        $rs = Area::create($dataArr);
    
        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('area.index');
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
        
        $tagSelected = [];

        $detail = Area::find($id);
        if( Auth::user()->role > 2 ){
            if($detail->created_user != Auth::user()->id){
                return redirect()->route('home');
            }
        }
        return view('area.edit', compact( 'detail'));
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
            'name' => 'required',
        ],
        [   
            'name.required' => 'Bạn chưa nhập tên',                
        ]);
        $dataArr['display_order'] = (int) $dataArr['display_order'];
       
        $model = Area::find($dataArr['id']);
       
        $model->update($dataArr);        
        
        Session::flash('message', 'Cập nhật thành công');        
        
        return redirect()->route('area.index');    
        
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
        $model = Area::find($id);
        $oldStatus = $model->status;
		$model->update(['status'=>0]);		
        // redirect
        Session::flash('message', 'Xóa thành công');        
        return redirect()->route('area.index');   
    }
}
