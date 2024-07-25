<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SystemRoutes;
use App\Models\Access;
use Illuminate\Support\Facades\Route;
use Helper, File, Session, Auth;

class AccessController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index(Request $request)
    {     
        $routeCollection = Route::getRoutes();
        
        foreach ($routeCollection->getRoutes() as $route) {
            
            $arr['uri'] = $route->uri;
            $action = $route->getAction();            
            $arr['prefix'] =  $prefix = $action['prefix'];            
            $route_name = '';
            if (array_key_exists('as', $action)) {
                $route_name = $action['as'];                
            }
            $arr['name'] = $route_name;
            SystemRoutes::create($arr);
        }
        dd('1111');
        return view('access.index', compact( 'items', 'name', 'id', 'all'));
    }
    public function ajaxList(Request $request){

        $id_selected = $request->id ?? null;
        $bankInfoList = BankInfo::all();
        
        //$tagArr = $query->orderBy('id', 'desc')->get();
       
        return view('access.ajax-list', compact( 'bankInfoList', 'id_selected'));
    }
    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $type = $request->type ? $request->type : 1;
        return view('access.create', compact('type'));
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
            'name' => 'required|unique:name'
            ],
        
        [
            'name.required' => 'Bạn chưa nhập tên địa điểm'
        ]);

        $rs = BankInfo::create($dataArr);
        
        $object_id = $rs->id;

        Session::flash('message', 'Tạo mới địa điểm thành công');

        return redirect()->route('access.index');
    }
    public function saveToaDo(Request $request){
        $value = $request->value;
        $id = $request->id;
       
        $rs = BankInfo::find($id);

        $rs->update(['address' => $value]);
    }
    public function ajaxDelete(Request $request){        
        $id = $request->id;        
        $rs = Booking::where('location_id', $id)->get();
        foreach($rs as $r){
            $r->update(['location_id' => 466]);
        } 
        $model = BankInfo::find($id);        
        $model->delete();        
    }
    public function saveName(Request $request){
        $value = $request->value;
        $id = $request->id;
        $rs = BankInfo::find($id);

         $value = ucwords($value);
        $rs->update(['name' => $value]);
    }
    public function ajaxSave(Request $request)
    {
        $dataArr = $request->all();
        $this->validate($request,[
            'name' => 'required',
            'bank_name' => 'required',
            'account_name' => 'required',
            'bank_no' => 'required|unique:bank_info',            
            ],
        
        [
            'name.required' => 'Bạn chưa nhập tên tài khoản',
            'bank_name.required' => 'Bạn chưa nhập tên ngân hàng',
            'account_name.required' => 'Bạn chưa nhập tên chủ tài khoản ',
            'bank_no.required' => 'Bạn chưa nhập số tài khoản'
        ]);
        $rs = BankInfo::create($dataArr);
        return $rs->id;
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
        $detail = BankInfo::find($id);
        $meta = (object) [];
        if ( $detail->meta_id > 0){
            $meta = MetaData::find( $detail->meta_id );
        }       

        return view('access.edit', compact( 'detail', 'meta'));
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
            'slug' => 'required|unique:tag,slug,'.$dataArr['id'].',id,type,'.$dataArr['type'],
        ],
        [
            'name.required' => 'Bạn chưa nhập tag',
            'slug.required' => 'Bạn chưa nhập slug',
            'slug.unique' => 'Slug đã được sử dụng.',
        ]);
        $dataArr['alias'] = Helper::stripUnicode($dataArr['name']);
        
        $model = BankInfo::find($dataArr['id']);        

        $dataArr['updated_user'] = Auth::user()->id;

        $model->update($dataArr);

        if( $dataArr['meta_id'] != '' ){

            $this->storeMeta( $dataArr['id'], $dataArr['meta_id'], $dataArr);
        }

        Session::flash('message', 'Cập nhật tag thành công');

        return redirect()->route('access.index', [ 'type' => $dataArr['type'] ]);
    }
    public function storeMeta( $id, $meta_id, $dataArr ){
       
        $arrData = [ 'title' => $dataArr['meta_title'], 'description' => $dataArr['meta_description'], 'keywords'=> $dataArr['meta_keywords'], 'custom_text' => $dataArr['custom_text'], 'updated_user' => Auth::user()->id ];
        if( $meta_id == 0){
            $arrData['created_user'] = Auth::user()->id;            
            $rs = MetaData::create( $arrData );
            $meta_id = $rs->id;
            
            $modelSp = BankInfo::find( $id );
            $modelSp->meta_id = $meta_id;
            $modelSp->save();
        }else {
            $model = MetaData::find($meta_id);           
            $model->update( $arrData );
        }              
    }
    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function deleteMulti(Request $request){
        $idArr = $request->replace_id;
        if(!empty($idArr)){
            foreach($idArr as $id){
                $rs = Booking::where('location_id', $id)->get();
                foreach($rs as $r){
                    $r->update(['location_id' => 466]);
                } 
                $model = BankInfo::find($id);
                $model->delete();
            }
        }
        // redirect
        Session::flash('message', 'Xóa địa điểm thành công');
        return redirect()->route('access.index');
    }
    public function destroy($id)
    {
        // delete
        $rs = Booking::where('location_id', $id)->get();
        foreach($rs as $r){
            $r->update(['location_id' => 466]);
        } 
        $model = BankInfo::find($id);
        $model->delete();

        // redirect
        Session::flash('message', 'Xóa địa điểm thành công');
        return redirect()->route('access.index');
    }
}