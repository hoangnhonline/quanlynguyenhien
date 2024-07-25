<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Drivers;
use App\Models\DriverImg;
use App\Models\City;
use App\Models\CarCate;
use App\Models\Area;
use App\Models\DriverArea;

use Helper, File, Session, Auth;

class DriversController extends Controller
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
        $model = Drivers::find($id);   
        

        $model->update([$column => $value]);
    }
    public function index(Request $request)
    {     
        $arrSearch = $request->all();      
        $arrSearch['name'] = $name = $request->name ?? null;
        $arrSearch['car_cate_id'] = $car_cate_id = $request->car_cate_id ?? null;
        $arrSearch['area_id'] = $area_id = $request->area_id ?? null;     
        $arrSearch['is_verify'] = $is_verify = $request->is_verify ?? -1;
        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $carCateList = CarCate::all();

        $query = Drivers::where('status', 1);
        if($area_id){
           $query->join('driver_area', function($join ) use ($area_id) {   
              $join->on('drivers.id', 'driver_area.driver_id')
              ->where('driver_area.area_id', $area_id);
            });
        }
        if($car_cate_id){
            $query->where('car_cate_id', $car_cate_id);
        }
        if($name){
            $query->where('name', 'LIKE', '%'.$name.'%');
        }
        if ($city_id) {
            $query->where('city_id', $city_id);
        }       
        if ($is_verify > -1) {
            $query->where('is_verify', $is_verify);
        }      
        $cityList = City::all();
        $items = $query->orderBy('id', 'desc')->paginate(20);
        $areaList = Area::where('status', 1)->orderBy('display_order')->get();
        return view('drivers.index', compact( 'items', 'carCateList', 'cityList', 'areaList', 'arrSearch', 'name', 'car_cate_id', 'city_id', 'is_verify', 'area_id'));
    }
    public function ajaxList(Request $request){

        $DriversSelected = (array) $request->DriversSelected;
        
        $str_id = $request->str_id;
        $tmpArr = explode(",", $str_id);
        $DriversSelected = array_merge($DriversSelected, $tmpArr);

        $type = isset($request->type) ? $request->type : 1;

        $query = Drivers::where('type', $type);
        
        $DriversArr = $query->orderBy('id', 'desc')->get();
       
        return view('drivers.ajax-list', compact( 'DriversArr', 'type', 'DriversSelected'));
    }
    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $car_cate_id = $request->car_cate_id ?? null;
        $carCateList = CarCate::all();
        $back_url = $request->back_url ?? null;
        $cityList = City::all();
        $areaList = Area::where('status', 1)->orderBy('display_order')->get();
        return view('drivers.create', compact('carCateList', 'car_cate_id', 'back_url', 'cityList', 'areaList'));
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
            'car_cate_id' => 'required',
            'name' => 'required',
            'phone' => 'required',           
        ],
        [
            'car_cate_id' => 'Bạn chưa chọn loại xe',
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập số điện thoại'
        ]);      
        $dataArr['is_verify'] = isset($dataArr['is_verify']) ? 1 : 0;
        //dd($dataArr);
        $rsDriver = Drivers::create($dataArr);
        $driver_id = $rsDriver->id;
        if(!empty($dataArr['area_id'])){
            foreach($dataArr['area_id'] as $area_id){
                DriverArea::create([
                    'area_id' => $area_id,
                    'driver_id' => $driver_id
                ]);
            }
        }
        
        $dataImg = [];    
        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $file) {                              
                $dataImg[] = Helper::uploadPhoto($file, 'cars');
            }
        }
        if(!empty($dataImg)){
            $i=0;
            foreach ($dataImg as $key => $img) {
                $i++;
                $rs = DriverImg::create(['driver_id' => $driver_id, 'image_url' => $img['image_path'], 'display_order' => $i, 'cate' => 1]);     // cate = 1 => hinh anh xe           
                $image_id = $rs->id;
                
                if($i == 1){               
                    $car_thumbnail_id = $image_id;                    
                }
                    
            }
            $rsDriver->update(['car_thumbnail_id' => $car_thumbnail_id]);
        } 
        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('drivers.index', [ 'car_cate_id' => $dataArr['car_cate_id'] ]);
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
        $detail = Drivers::find($id);             
        $carCateList = CarCate::all();
        $cityList = City::all();
        $areaList = Area::where('status', 1)->orderBy('display_order')->get();
        $arrSelectedArr = [];
        if($detail->area){
            foreach($detail->area as $area){
                $arrSelectedArr[] = $area->area_id;
            }
        }
        $imageList = $detail->images;
        $imageArr = [];

        if($imageList){
            foreach($imageList as $img){                
                $imageArr[] = $img;                
            }
        }
        return view('drivers.edit', compact( 'detail', 'carCateList', 'cityList', 'areaList', 'arrSelectedArr', 'imageArr'));
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
            'car_cate_id' => 'required',
            'name' => 'required',
            'phone' => 'required',           
        ],
        [
            'car_cate_id' => 'Bạn chưa chọn loại xe',
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập số điện thoại'
        ]);         
        
        $model = Drivers::find($dataArr['id']);  
        $dataArr['is_verify'] = isset($dataArr['is_verify']) ? 1 : 0;
        $model->update($dataArr);
        $driver_id = $model->id;
        DriverArea::where('driver_id', $driver_id)->delete();
        if(!empty($dataArr['area_id'])){
            foreach($dataArr['area_id'] as $area_id){
                DriverArea::create([
                    'area_id' => $area_id,
                    'driver_id' => $driver_id
                ]);
            }
        }

        $dataImg = [];    
        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $file) {                
                $dataImg[] = Helper::uploadPhoto($file, 'cars');
            }
        }
        if(!empty($dataImg)){
            $i=0;
            foreach ($dataImg as $key => $img) {
                $i++;
                DriverImg::create(['driver_id' => $driver_id, 'image_url' => $img['image_path'], 'display_order' => $i, 'cate' => 1]);            
                    
            }
        } 

        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('drivers.index', [ 'car_cate_id' => $dataArr['car_cate_id'] ]);
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
        $model = Drivers::find($id);
        $model->update(['status' => 0]);

        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('drivers.index', ['car_cate_id' => $model->car_cate_id]);
    }
}