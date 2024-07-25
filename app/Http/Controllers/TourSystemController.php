<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TourSystem;
use App\Models\TourPrice;
use App\Models\Partner;
use App\Models\TourSystemPrice;
use App\Models\PartnerSystem;

use Helper, File, Session, Auth, Image;

class TourSystemController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index(Request $request)
    {   
        
        
        $name = isset($request->name) && $request->name != '' ? $request->name : '';       
        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $query = TourSystem::where('status', 1);
        
        if($city_id > 0){
            $query->where('city_id', $city_id);
        }
         
        // check editor
        if( Auth::user()->role > 2 ){
            $query->where('created_user', Auth::user()->id);
        }
        if( $name != ''){
            $query->where('name', 'LIKE', '%'.$name.'%');
        }
        $partnerList = Partner::getList(['cost_type_id' => 54, 'city_id' => $city_id]);

        $partnerArr = [];

        foreach($partnerList as $partner){
            $partnerArr[$partner->id] = $partner->name;
        }
        $items = $query->orderBy('id', 'asc')->paginate(20);   
       
        return view('tour-system.index', compact( 'items', 'name', 'city_id', 'partnerList', 'arrSearch', 'partnerArr'));
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        // if( Auth::user()->role > 2 ){
        //     return redirect()->route('home');
        // }       
        $allTypeList = HotelsTypesSettings::where('type', 3)->get();
        $TourSystemAmen = [];
        foreach($allTypeList as $types){            
            $TourSystemAmen[] = $types;            
        }        
        $hotelList = Hotels::where('status', 1)->orderBy('id', 'desc')->get();
        $hotel_id = $request->hotel_id ?? null;
        $partnerList = Partner::getList(['cost_type_id' => 54, 'city_id' => $city_id]);
        return view('tour-system.create', compact('TourSystemAmen', 'hotelList', 'hotel_id'));
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
            'hotel_id' => 'required',
            'name' => 'required',                                               
        ],
        [
            'hotel_id.required' => 'Bạn chưa chọn khách sạn',
            'name.required' => 'Bạn chưa nhập tên loại phòng',
        ]);
       
        $dataArr['is_hot'] = isset($dataArr['is_hot']) ? 1 : 0;
        if(isset($dataArr['amenities'])){
             $dataArr['amenities'] = implode(',', $dataArr['amenities']);
         }       

        $dataArr['status'] = 1;

        $dataArr['created_user'] = Auth::user()->id;

        $dataArr['updated_user'] = Auth::user()->id;        

        $detailHotel = Hotels::find($dataArr['hotel_id']);
        $dataArr['city_id'] = $detailHotel->city_id;
        $rs = TourSystem::create($dataArr);

        $hotel_id = $rs->id;
       
        $this->storeImage($hotel_id, $dataArr);       
        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('tour-system.index', ['hotel_id' => $dataArr['hotel_id'], 'city_id' => $dataArr['city_id']]);
    }

    public function storeMeta( $id, $meta_id, $dataArr ){
       
        $arrData = [ 'title' => $dataArr['meta_title'], 'description' => $dataArr['meta_description'], 'keywords'=> $dataArr['meta_keywords'], 'custom_text' => $dataArr['custom_text'], 'updated_user' => Auth::user()->id ];
        if( $meta_id == 0){
            $arrData['created_user'] = Auth::user()->id;            
            $rs = MetaData::create( $arrData );
            $meta_id = $rs->id;
            
            $modelSp = TourSystem::find( $id );
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
    
        $detail = TourSystem::find($id);   

        $partnerList = Partner::getList(['cost_type_id' => 54, 'city_id' => $city_id]);
        return view('tour-system.edit', compact('detail', 'partnerList'));
    }
    public function editPrice(Request $request)
    {       
        $partner_id = $request->partner_id;
        $id = $request->id;
        $detail = TourSystem::find($id);   
        $detailPartner = Partner::find($partner_id);
        $city_id = $detail->city_id;
        $partnerList = Partner::getList(['cost_type_id' => 54, 'city_id' => $city_id]);
        $partnerArr = [];

        foreach($partnerList as $partner){
            $partnerArr[$partner->id] = $partner->name;
        }

        $priceList = TourSystemPrice::where(['tour_id' => $id, 'partner_id' => $partner_id, 'status' => 1])->get();
        $priceArr = [];
        $priceFromDateArr = [];
        foreach($priceList as $r){
            $priceArr[] = $r->toArray();
            $priceFromDateArr[] = $r->from_date;
        } 
        if(empty($priceArr)){
            return redirect()->route('tour-system.price', ['id' => $id]);
        }
        return view('tour-system.edit-price', compact('detail', 'partnerList', 'partnerArr', 'detailPartner', 'priceList', 'priceArr', 'priceFromDateArr'));
    }
    public function price(Request $request)
    {       
        //dd('11111');
        if( Auth::user()->role > 1 ){
            return redirect()->route('home');
        }
        $detail = (object) [];
        $id = $request->id ?? null; // is edit
        if($id){
            $detail = TourPrice::find($id);
        }
        $partner_id = $request->partner_id ?? 1;
        $tour_id = $request->tour_id ?? null;
        $tour_type = $request->tour_type ?? null;
        $cano_type = $request->cano_type ?? null;
        $level = $request->level ?? null;        

        $query = TourPrice::where(['partner_system_id' => $partner_id, 'tour_id' => $tour_id, 'status' => 1]);
        if($tour_type){
            $query->where('tour_type', $tour_type);
        }
        if($level){
            $query->where('level', $level);
        }
        if($cano_type){
            $query->where('cano_type', $cano_type);
        }
        $priceList = $query->get();        
        $detailTour = TourSystem::find($tour_id);       
       
        $city_id = $detailTour->city_id;
        $partnerList = PartnerSystem::where(['status' => 1, 'city_id' => $city_id])->get();
        $partnerArr = [];

        foreach($partnerList as $partner){
            $partnerArr[$partner->id] = $partner->name;
        }       
        //$priceArr = TourSystemPrice::getPriceFromTo($id, '2019-08-29', '2019-09-03');
        //$minPrice = TourSystemPrice::getTourSystemMinPrice($id);
        //$minPrice = TourSystemPrice::getHotelMinPrice($detail->hotel_id);       
        $view = 'price';
        if($tour_id == 1){
            $view = 'price-tour-dao';
        }
        return view('tour-system.'.$view, compact('detail',  'partnerList', 'partnerArr', 'partner_id', 'tour_id', 'tour_type', 'priceList', 'cano_type', 'tour_type', 'level', 'id', 'detail', 'detailTour'));
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
            'hotel_id' => 'required',
            'name' => 'required',                                                   
        ],
        [
            'hotel_id.required' => 'Bạn chưa chọn khách sạn',
            'name.required' => 'Bạn chưa nhập tên loại phòng',                  
        ]);
       
        $dataArr['is_hot'] = isset($dataArr['is_hot']) ? 1 : 0; 

        $dataArr['updated_user'] = Auth::user()->id;
            
        $model = TourSystem::find($dataArr['id']);
        if(isset($dataArr['amenities'])){
             $dataArr['amenities'] = implode(',', $dataArr['amenities']);
         }  

        $model->update($dataArr);
       
        $hotel_id = $dataArr['id'];

        $detailHotel = Hotels::find($hotel_id);
        
        $this->storeImage($hotel_id, $dataArr);

        Session::flash('message', 'Cập nhật thành công');       

        return redirect()->route('tour-system.index', ['hotel_id' => $dataArr['hotel_id'], 'city_id' => $detailHotel->city_id]);
    }
    public function storePrice(Request $request){

        $dataArr = $request->all();
        $this->validate($request,[
            'partner_id' => 'required',
            'level' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'price' => 'required',
            'price_child' => 'required',
            'from_adult' => 'required',
            'to_adult' => 'required',
            'cap_nl' => 'required',
            'cap_te' => 'required',
            'meals' => 'required',
            'meals_te' =>  'required'                                  
        ],
        [
            'partner_id.required' => 'Bạn chưa chọn đối tác',
            'level.required' => 'Bạn chưa chọn Phân loại sales',
            'from_date.required' => 'Bạn chưa nhập Từ ngày',
            'to_date.required' => 'Bạn chưa nhập Đến ngày',
            'price.required' => 'Bạn chưa nhập giá NL',
            'price_child.required' => 'Bạn chưa nhập giá trẻ em',
            'from_adult.required' => 'Bạn chưa nhập số lượng từ',
            'to_adult.required' => 'Bạn chưa nhập Số lượng đến',
            'cap_nl.required' => 'Bạn chưa nhập giá cáp treo NL',
            'cap_te.required' => 'Bạn chưa nhập giá cáp treo TE',
            'meals.required' => 'Bạn chưa nhập giá phần ăn NL',
            'meals_te.required' => 'Bạn chưa nhập giá phần ăn TE',
        ]);

        // TourSystemPrice::where('tour_id', $dataArr['tour_id'])->where('partner_id', $dataArr['partner_id'])->delete();

        
        $dataArr['price'] = str_replace(",", '', $dataArr['price']);
        $dataArr['price_child'] = str_replace(",", '', $dataArr['price_child']);
        $dataArr['price_child_no_cable'] = str_replace(",", '', $dataArr['price_child_no_cable']);
        $dataArr['cap_nl'] = str_replace(",", '', $dataArr['cap_nl']);
        $dataArr['cap_te'] = str_replace(",", '', $dataArr['cap_te']);
        $dataArr['meals'] = str_replace(",", '', $dataArr['meals']);
        $dataArr['meals_te'] = str_replace(",", '', $dataArr['meals_te']);
        $dataArr['extra_fee'] = isset($dataArr['extra_fee']) ? str_replace(",", '', $dataArr['extra_fee']) : 0;
        
        
        $tmpFrom = explode("/", $dataArr['from_date']);
        $tmpTo = explode("/", $dataArr['to_date']);  
        $dataArr['from_date'] = $tmpFrom[2].'-'.$tmpFrom[1].'-'.$tmpFrom[0];
        $dataArr['to_date'] = $tmpTo[2].'-'.$tmpTo[1].'-'.$tmpTo[0];
        $dataArr['created_user'] = $dataArr['updated_user'] = Auth::user()->id;
        $id = isset($dataArr['id']) ? $dataArr['id'] : null;
       
        if($id){
            $model = TourPrice::find($id);
            $model->update($dataArr);
        }else{
            TourPrice::create($dataArr);    
        }

        Session::flash('message', 'Cập nhật giá tour thành công'); 

        return redirect()->route('tour-system.price', ['tour_id' => $dataArr['tour_id'], 'id' => $dataArr['id'], 'level' => $dataArr['level'], 'tour_type' => $dataArr['tour_type']]);
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
        if( isset( $dataArr[' '])){
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
            $model = TourSystem::find( $id );
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
        $model = TourSystem::find($id);
        $model->delete();        
        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('tour-system.index');
    }
}
