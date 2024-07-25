<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\CostType;
use App\Models\TourSystem;
use App\Models\PartnerCity;

use Helper, File, Session, Auth;

class PartnerController extends Controller
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
        $model = Partner::find($id);   
        

        $model->update([$column => $value]);
    }
    public function index(Request $request)
    {     

        $name = $request->name ?? null;
        $cost_type_id = $request->cost_type_id ?? 1;
        $city_id = $request->city_id ?? Auth::user()->city_id;
        $status = $request->status ?? 1;
        $costTypeList = CostType::where('status', 1)->orderBy('display_order')->get();

        $query = Partner::where('cost_type_id', $cost_type_id);
        if($city_id){
           $query->join('partner_city', function($join ) use ($city_id) {   
              $join->on('partners.id', 'partner_city.partner_id')
              ->where('partner_city.city_id', $city_id);
            });
        }
        if($name){
            $query->where('name', 'LIKE', '%'.$name.'%');
        }
        if($status){
            $query->where('status', $status);
        }
        $items = $query->orderBy('display_order')->paginate(1000);
        
        return view('partner.index', compact( 'items', 'name', 'cost_type_id', 'costTypeList', 'city_id', 'status'));
    }
    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $cost_type_id = $request->cost_type_id ?? 1;
        $costTypeList = CostType::where('status', 1)->orderBy('display_order')->get();
        $listTour = TourSystem::where('status',1)->where('city_id',2)->get();
        return view('partner.create', compact('costTypeList', 'cost_type_id','listTour'));
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
            'cost_type_id' => 'required',
            'name' => 'required'            
        ],
        [
            'cost_type_id' => 'Bạn chưa chọn phân loại',
            'name.required' => 'Bạn chưa nhập tên'
        ]);      
        $cityArr = $dataArr['city_id'];
        unset($dataArr['city_id']);
        
        $rs = Partner::create($dataArr);
        $partner_id = $rs->id;
        if(!empty($cityArr)){
            foreach($cityArr as $city_id){
                PartnerCity::create([
                    'city_id' => $city_id,
                    'partner_id' => $partner_id
                ]);
            }
        }
      //  dd($cityArr);
        Session::flash('message', 'Tạo mới thành công');
        $city_id = isset($cityArr[0]) ? $cityArr[0] : 1; 
        return redirect()->route('partner.index', [ 'cost_type_id' => $dataArr['cost_type_id'], 'city_id' => $city_id ]);
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
        $detail = Partner::find($id);             
        $costTypeList = CostType::where('status', 1)->orderBy('display_order')->get();
        $listTour = TourSystem::where('status',1)->where('city_id', $detail->city_id)->get();

        $arrSelectedArr = [];
        if($detail->citys){
            foreach($detail->citys as $c){
                $arrSelectedArr[] = $c->city_id;
            }
        }
        return view('partner.edit', compact( 'detail', 'costTypeList','listTour', 'arrSelectedArr'));
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
            'cost_type_id' => 'required',
            'name' => 'required'            
        ],
        [
            'cost_type_id' => 'Bạn chưa chọn phân loại',
            'name.required' => 'Bạn chưa nhập tên'
        ]);    
        
        $model = Partner::find($dataArr['id']);  
        $arrCity = $dataArr['city_id'];
        unset($dataArr['city_id']);
        $model->update($dataArr);

        $partner_id = $model->id;
        PartnerCity::where('partner_id', $partner_id)->delete();
        if(!empty($arrCity)){
            foreach($arrCity as $city_id){
                PartnerCity::create([
                    'city_id' => $city_id,
                    'partner_id' => $partner_id
                ]);
            }
        }
        $city_id = isset($cityArr[0]) ? $cityArr[0] :  1; 
        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('partner.index', [ 'cost_type_id' => $dataArr['cost_type_id'], 'city_id' => $city_id]);
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
        $model = Partner::find($id);
        $model->update(['status' => 0]);

        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('partner.index', ['cost_type_id' => $model->cost_type_id, 'city_id' => $model->city_id]);
    }
}