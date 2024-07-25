<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HotelAmenity;
use App\Models\HotelPolicy;
use App\Models\Policy;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Hotels;
use App\Models\HotelImg;
use App\Models\MetaData;
use App\Models\HotelFeatured;
use App\Models\Featured;
use App\Models\HotelsTypesSettings;
use App\Models\RoomsPrice;
use App\Models\Partner;
use App\Models\Amenity;
use App\Models\HotelTypes;
use Helper, File, Session, Auth, Image;

class HotelController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function price(Request $request){
        $all = Hotels::all();
        foreach($all as $h){
            $price_lowest = $h->getHotelMinPrice($h->id);
            $h->update(['lowest_price' => $price_lowest]);
        }
    }
    public function index(Request $request)
    {   
        // if( Auth::user()->role > 2 ){
        //     return redirect()->route('home');
        // }        
 
        $name = isset($request->name) && $request->name != '' ? $request->name : '';
        $stars = $request->stars ? $request->stars : null;       
        $city_id = $request->city_id ?? 1;
        $partner = $request->partner ?? null;
        $status = $request->status ? $request->status : null;
        $query = Hotels::where('status', '>', 0);

        if ($request->city_id) {
            $query->where('city_id', $request->city_id);
        }
        if ($status > 0) {
            $query->where('status', $status);
        }
        if ($stars > 0) {
            $query->where('stars', $stars);
        }
        
        $query->where('partner', 0);
        
        // check editor
        if (Auth::user()->role > 1) {
            $query->where('created_user', Auth::user()->id);
        }
        if ($name != '') {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }
        $items = $query->orderBy('is_hot', 'desc')->orderBy('id', 'desc')->paginate(100);  
        if($partner == 1){
            $view = 'hotel.index-partner';
        }else{
            $view = 'hotel.index';
        }
        return view($view, compact( 'items', 'name', 'stars', 'city_id', 'partner', 'status'));
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $objectsList = Featured::whereRaw(1)->get();
        
        $hotelAmen = $hotelPay = $hotelType = [];
        
        $featuredList = Featured::all();
        $hotelType = HotelTypes::where('status', 1)->orderBy('display_order')->get();
        $policies = Policy::where('type', 1)->get();
        $hotelAmen = Amenity::where(['type' => 1, 'status' => 1])->orderBy('display_order')->get();
        
        $partner = $request->partner ?? null;

        if($partner == 1){
            $view = 'hotel.create-partner';
        }else{
            $view = 'hotel.create';
        }
        $partnerList = Partner::getList(['cost_type_id'=> 48]);   
        return view($view, compact('objectsList', 'objectsList', 'hotelType', 'hotelAmen', 'hotelPay', 'featuredList', 'policies', 'partnerList'));
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
     
       $this->validate($request, [
            'city_id' => 'required',
            'name' => 'required',
            'stars' => 'required',

        ],
            [
                'city_id.required' => 'Bạn chưa chọn tỉnh thành',
                'name.required' => 'Bạn chưa nhập tên khách sạn',
                'stars.required' => 'Bạn chưa chọn số sao',

            ]);

        $dataArr['is_hot'] = isset($dataArr['is_hot']) ? 1 : 0;
        $dataArr['partner'] = $dataArr['partner'] ??  0;
        $dataArr['is_show'] = isset($dataArr['is_show']) ? 1 : 0;
        $dataArr['is_home'] = isset($dataArr['is_home']) ? 1 : 0;
        $dataArr['weekend_price'] = isset($dataArr['weekend_price']) ? 1 : 0;
        
        $dataArr['alias'] = str_slug($dataArr['name'], " ");
        $dataArr['slug'] = str_slug($dataArr['name'], "-");

        $dataArr['lowest_price'] = isset( $dataArr['lowest_price']) ? (int)str_replace(",", "", $dataArr['lowest_price']) : 0;
        if (isset($dataArr['com_value'])) {
            $dataArr['com_value'] = (int)str_replace(",", "", $dataArr['com_value']);
        }
        $dataArr['status'] = 1;
        $amenities = [];# (9/11/22 Khang: tach tien ich ra bang khac
        if (isset($dataArr['amenities'])) {
            $amenities = $dataArr['amenities']; # (9/11/22 Khang: tach tien ich ra bang khac
            $dataArr['amenities'] = implode(',', $dataArr['amenities']);
        }
        if(isset($dataArr['partner_id'])){
            $dataArr['related_id'] = implode(',', $dataArr['partner_id']);    
        }
        $dataArr['created_user'] = $dataArr['updated_user'] = Auth::user()->id;

        $rs = Hotels::create($dataArr);

        $hotel_id = $rs->id;
        if (!empty($dataArr['featured_id'])) {
            foreach ($dataArr['featured_id'] as $featured_id) {
                HotelFeatured::create(['hotel_id' => $hotel_id, 'featured_id' => $featured_id]);
            }
        }
        $this->storeImage($hotel_id, $dataArr);

        # (9/11/22 Khang: tach tien ich ra bang khac, them policy
        foreach ($amenities as $amenity => $value) {
            $amenityData = [
                'hotel_id' => $hotel_id,
                'amenity_id' => $value
            ];
            HotelAmenity::create($amenityData);
        }
        //dd($request->policy_content);
        // foreach ($request->policy_id as $k => $policy_id) {
        //     if($request->policy_content[$k]){
        //         $policyData = [
        //             'hotel_id' => $hotel_id,
        //             'policy_id' => $policy_id,
        //             'type' => $value['type']??0,
        //             'content' => $request->policy_content[$k]
        //         ];
        //         HotelPolicy::create($policyData);    
        //     }
            
        // }       
        #End (9/11/22 Khang: tach tien ich ra bang khac, them policy

        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('hotel.index', ['partner' => $dataArr['partner']]);
    }

    public function storeMeta( $id, $meta_id, $dataArr ){
       
        $arrData = [ 'title' => $dataArr['meta_title'], 'description' => $dataArr['meta_description'], 'keywords'=> $dataArr['meta_keywords'], 'custom_text' => $dataArr['custom_text'], 'updated_user' => Auth::user()->id ];
        if( $meta_id == 0){
            $arrData['created_user'] = Auth::user()->id;            
            $rs = MetaData::create( $arrData );
            $meta_id = $rs->id;
            
            $modelSp = Hotels::find( $id );
            $modelSp->meta_id = $meta_id;
            $modelSp->save();
        }else {
            $model = MetaData::find($meta_id);           
            $model->update( $arrData );
        }              
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
        $objectSelected = [];
        $detail = Hotels::find($id);
        if (Auth::user()->role > 1) {
            return redirect()->route('home');
        }
        $tmpArr = HotelFeatured::where(['hotel_id' => $id])->get();
        if ($tmpArr->count() > 0) {
            foreach ($tmpArr as $value) {
                $objectSelected[] = $value->featured_id;
            }
        }
        $objectsList = Featured::whereRaw(1)->get();
        $meta = (object)[];
        if ($detail->meta_id > 0) {
            $meta = MetaData::find($detail->meta_id);
        }
        
        $hotelAmen = $hotelPay = $hotelType = [];
        
        $featuredList = Featured::all();
        $policies = Policy::where('type', 1)->get();
        $hotelAmen = Amenity::where(['type' => 1, 'status' => 1])->orderBy('display_order')->get();
        $hotelType = HotelTypes::where('status', 1)->orderBy('display_order')->get();
        //get policy
        $policiesList = $detail->policies;
        $policiesArrSelected = [];
        foreach($policiesList as $p){
            $policiesArrSelected[$p->policy_id] = $p->content;
        }        
        
        $featuredList = Featured::all();
        
        $partnerList = Partner::getList(['cost_type_id'=> 48, 'city_id' => $detail->city_id]);    
        if($detail->partner == 1){
            $view = 'hotel.edit-partner';
        }else{
            $view = 'hotel.edit';
        }
        return view($view, compact('objectsList', 'objectSelected', 'detail', 'meta', 'hotelType', 'hotelAmen', 'hotelPay', 'featuredList', 'policies', 'policiesArrSelected', 'partnerList'));

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
        
        $this->validate($request, [
            'city_id' => 'required',
            'name' => 'required',
            'stars' => 'required',

        ],
            [
                'city_id.required' => 'Bạn chưa chọn tỉnh thành',
                'name.required' => 'Bạn chưa nhập tên khách sạn',
                'stars.required' => 'Bạn chưa chọn số sao',

            ]);

        $dataArr['is_hot'] = isset($dataArr['is_hot']) ? 1 : 0;
        $dataArr['is_show'] = isset($dataArr['is_show']) ? 1 : 0;
        $dataArr['is_home'] = isset($dataArr['is_home']) ? 1 : 0;
        $dataArr['partner'] = $dataArr['partner'] ??  0;
        $dataArr['weekend_price'] = isset($dataArr['weekend_price']) ? 1 : 0;

        $dataArr['lowest_price'] = isset( $dataArr['lowest_price']) ? (int)str_replace(",", "", $dataArr['lowest_price']) : 0;
        if (isset($dataArr['com_value'])) {
            $dataArr['com_value'] = (int)str_replace(",", "", $dataArr['com_value']);
        }
        $dataArr['alias'] = str_slug($dataArr['name'], " ");
        $dataArr['slug'] = str_slug($dataArr['name'], "-");

        $dataArr['updated_user'] = Auth::user()->id;

        $model = Hotels::find($dataArr['id']);
        HotelFeatured::where('hotel_id', $dataArr['id'])->delete();
        if (!empty($dataArr['featured_id'])) {
            foreach ($dataArr['featured_id'] as $featured_id) {
                HotelFeatured::create(['hotel_id' => $dataArr['id'], 'featured_id' => $featured_id]);
            }
        }
        $amenities = [];# (9/11/22 Khang: tach tien ich ra bang khac
        if (isset($dataArr['amenities'])) {
            $amenities = $dataArr['amenities'];# (9/11/22 Khang: tach tien ich ra bang khac
            $dataArr['amenities'] = implode(',', $dataArr['amenities']);
        }
        $dataArr['related_id'] = '';
        if(isset($dataArr['partner_id'])){
            $dataArr['related_id'] = implode(',', $dataArr['partner_id']);    
        }

        $model->update($dataArr);      

        $hotel_id = $dataArr['id'];

        $this->storeImage($hotel_id, $dataArr);

        # (9/11/22 Khang: tach tien ich ra bang khac, them policy
        HotelAmenity::where('hotel_id', $hotel_id)->delete();
        foreach ($amenities as $amenity => $value) {
            $amenityData = [
                'hotel_id' => $hotel_id,
                'amenity_id' => $value
            ];
            HotelAmenity::create($amenityData);
        }
        HotelPolicy::where('hotel_id', $hotel_id)->delete();
        foreach ($request->policy_id as $k => $policy_id) {
            if($request->policy_content[$k]){
                $policyData = [
                    'hotel_id' => $hotel_id,
                    'policy_id' => $policy_id,
                    'type' => $value['type']??0,
                    'content' => $request->policy_content[$k]
                ];
                HotelPolicy::create($policyData);    
            }
        }
        #End (9/11/22 Khang: tach tien ich ra bang khac, them policy
        Session::flash('message', 'Cập nhật thành công');
        return redirect()->route('hotel.index', ['partner' => $dataArr['partner']]);
    }

    public function storeImage($id, $dataArr){     
        //process old image
        $imageIdArr = isset($dataArr['image_id']) ? $dataArr['image_id'] : [];
        $hinhXoaArr = HotelImg::where('hotel_id', $id)->whereNotIn('id', $imageIdArr)->pluck('id');
        if( $hinhXoaArr )
        {
            foreach ($hinhXoaArr as $image_id_xoa) {
                $model = HotelImg::find($image_id_xoa);
                $urlXoa = config('plantotravel.upload_path')."/".$model->image_url;
                if(is_file($urlXoa)){
                    unlink($urlXoa);
                }
                $model->delete();
            }
        }       

        //process new image
        if( isset( $dataArr['thumbnail_img'])){
            $thumbnail_img = $dataArr['thumbnail_img'];

            $imageArr = []; 

            if( !empty( $dataArr['image_tmp_url'] )){

                foreach ($dataArr['image_tmp_url'] as $k => $image_url) {
                    
                    $origin_img = public_path().$image_url;                  
                    
                    if( $image_url ){

                        $imageArr['is_thumbnail'][] = $is_thumbnail = $dataArr['thumbnail_img'] == $image_url  ? 1 : 0;

                        $img = Image::make($origin_img);
                        $w_img = $img->width();
                        $h_img = $img->height();

                        $tmpArrImg = explode('/', $origin_img);
                        
                        $new_img = config('plantotravel.upload_thumbs_path').end($tmpArrImg);

                        if($w_img/$h_img > 550/350){

                            Image::make($origin_img)->resize(null, 350, function ($constraint) {
                                    $constraint->aspectRatio();
                            })->crop(550, 350)->save($new_img);
                        }else{
                            Image::make($origin_img)->resize(550, null, function ($constraint) {
                                    $constraint->aspectRatio();
                            })->crop(550, 350)->save($new_img);
                        }
                        $new_img = config('plantotravel.upload_thumbs_path_2').end($tmpArrImg);
                        if($w_img/$h_img > 350/300){

                            Image::make($origin_img)->resize(null, 300, function ($constraint) {
                                    $constraint->aspectRatio();
                            })->crop(350, 300)->save($new_img);
                        }else{
                            Image::make($origin_img)->resize(550, null, function ($constraint) {
                                    $constraint->aspectRatio();
                            })->crop(350, 300)->save($new_img);
                        }
                        $imageArr['name'][] = $image_url;
                        
                    }
                }
            }
            if( !empty($imageArr['name']) ){
                foreach ($imageArr['name'] as $key => $name) {
                    $rs = HotelImg::create(['hotel_id' => $id, 'image_url' => $name, 'display_order' => 1]);                
                    $image_id = $rs->id;
                    if( $imageArr['is_thumbnail'][$key] == 1){
                        $thumbnail_id = $image_id;
                    }
                }
            }
            $model = Hotels::find( $id );
            $model->thumbnail_id = $thumbnail_id;
            $model->save();
        }
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
        $model = Hotels::find($id);
        $model->delete();
        HotelImg::where('hotel_id', $id)->delete();
        HotelFeatured::where('hotel_id', $id)->delete();
        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('hotel.index');
    }
}
