<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingLogs;
use App\Models\Location;
use App\Models\Tour;
use App\Models\TourSystem;
use App\Models\CarCate;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Ctv;
use App\Models\TourSystemPrice;
use App\Models\GrandworldSchedule;

use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingDnController extends Controller
{
    public function maps(Request $request){
        return view('booking-dn.maps');
    }
    public function fastSearch(Request $request){
        $arrSearch['keyword'] = $keyword = $request->keyword ? $request->keyword : null;        
        if($keyword){
            if(strlen($keyword) <= 9){
                $id_search = $keyword;
                $id_search = strtolower($id_search);
                $id_search = str_replace("ptt", "", $id_search);
                $id_search = str_replace("pth", "", $id_search);
                $id_search = str_replace("ptv", "", $id_search);
                $id_search = str_replace("ptx", "", $id_search);
                $id_search = str_replace("ptc", "", $id_search);
                $arrSearch['id_search'] = $id_search;

                $detail = Booking::findOrFail($id_search);
                if($detail->type == 1){
                    return redirect()->route('booking-dn.edit', ['id' => $id_search, 'keyword' => $keyword]);
                }elseif($detail->type == 2){
                    return redirect()->route('booking-hotel.edit', ['id' => $id_search, 'keyword' => $keyword]);
                }elseif($detail->type == 3){
                    return redirect()->route('booking-ticket.edit', ['id' => $id_search, 'keyword' => $keyword]);
                }elseif($detail->type == 4){
                    return redirect()->route('booking-car.edit', ['id' => $id_search, 'keyword' => $keyword]);
                }elseif($detail->type == 5){
                    return redirect()->route('booking-camera.edit', ['id' => $id_search, 'keyword' => $keyword]);
                }
            }else{
                $phone = $keyword;

                $detail = Booking::where('phone', $phone)->first();
                if($detail->type == 1){
                    return redirect()->route('booking-dn.edit', ['id' => $detail->id, 'keyword' => $keyword]);
                }elseif($detail->type == 2){
                    return redirect()->route('booking-hotel.edit', ['id' => $detail->id, 'keyword' => $keyword]);
                }elseif($detail->type == 3){
                    return redirect()->route('booking-ticket.edit', ['id' =>$detail->id, 'keyword' => $keyword]);
                }elseif($detail->type == 4){
                    return redirect()->route('booking-car.edit', ['id' => $detail->id, 'keyword' => $keyword]);
                }elseif($detail->type == 5){
                    return redirect()->route('booking-camera.edit', ['id' => $detail->id, 'keyword' => $keyword]);
                }
            }
        }
        

    }
    
    public function changeStatus(Request $request){
        $id = $request->id;
        $model = Booking::find($id);

         // luu log
        $oldData = ['status' => $model->status];
        $dataArr = ['status' => 2];
        $contentDiff = array_diff_assoc($dataArr, $oldData);
        if(!empty($contentDiff)){
            $oldContent = [];

            foreach($contentDiff as $k => $v){
                $oldContent[$k] = $oldData[$k];
            }
            BookingLogs::create([
                'booking_id' =>  $id,
                'content' =>json_encode(['old' => $oldContent, 'new' => $contentDiff]),
                'action' => 3, // ajax hoa hong
                'user_id' => Auth::user()->id
            ]);
        }
        // update
        $model->update(['status' => 2]);
    }
    public function changeValueByColumn(Request $request){
        $id = $request->id;
        $column = $request->col;
        $value = $request->value;
        $model = Booking::find($id);
        
        
        if($column == "cano_id"){
            $cano_id = $value;
            $hdv_id = $model->hdv_id;
            $use_date = $model->use_date;
            $bk = Booking::find($id);
            $bk->update(['cano_id' => $cano_id]);
           
        }

        $model->update([$column => $value]);
    }
    
    
    public function info(Request $request){
        $id = $request->id;
        $detail = Booking::find($id);
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        return view('booking-dn.modal', compact( 'detail', 'listUser'));
    }

    public function saveInfo(Request $request){
        $detail = Booking::find($request->booking_id);
        $hdv_id = $request->hdv_id;
        $call_status = $request->call_status;
        $hdv_notes = $request->hdv_notes;
        $detail->update(['hdv_id' => $hdv_id, 'hdv_notes' => $hdv_notes, 'call_status' => $call_status]);
        //$this->replyMessCapNhat($detail); //chatbot
    }
    
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index(Request $request)
    {

        if(Auth::user()->id == 23){            
            return redirect()->route('booking-car.index');
        } // tuan vu
        $day = date('d');
        $month_do = date('m');
        $arrSearch['type'] = $type = $request->type ? $request->type : 1;  
           
        $arrSearch['keyword'] = $keyword = $request->keyword ? $request->keyword : null;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null; 
        $arrSearch['status'] = $status = $request->status ? $request->status : [1,2,4];
        
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : null;
        $arrSearch['tour_cate'] = $tour_cate = $request->tour_cate ? $request->tour_cate : null;
        $arrSearch['tour_type'] = $tour_type = $request->tour_type ?? [1,2,3];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $arrSearch['phone'] = $phone = $request->phone ? $request->phone : null;
        $arrSearch['name'] = $name = $request->name ? $request->name : null;
        $arrSearch['sort_by'] = $sort_by = $request->sort_by ? $request->sort_by : 'created_at';        
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 2;
        
       
        $use_df_default = Auth::user()->id == 151 ? date('d/m/Y', strtotime('yesterday')) : date('d/m/Y', time());
        $arrSearch['use_date_from'] = $use_date_from = $request->use_date_from ? $request->use_date_from : $use_df_default;
        $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;
                 
        
        $arrSearch['created_at'] = $created_at = $request->created_at ? $request->created_at :  null;

        $arrSearch['book_date'] = $book_date = $request->book_date ? $request->book_date :  null;
        $arrSearch['book_date_from'] = $book_date_from = $request->book_date_from ? $request->book_date_from :  null;
        
        $arrSearch['book_date_to'] = $book_date_to = $request->book_date_to ? $request->book_date_to : null;
        $arrSearch['no_meals'] = $no_meals = $request->no_meals ? $request->no_meals : null;

        $arrSearch['city_id'] = $city_id = 2;

        $query = Booking::where('type', $type);
        $query->where('city_id', $city_id);
       
        if($keyword){
            $type = null;
        }
        if($no_meals){
            
            $query->where('meals', 0);            
        }
        if($keyword){
            if(strlen($keyword) <= 8){
                $id_search = $keyword;
            }else{
                $phone = $keyword;
            }
        }
        $arrSearch['month'] = $month = $request->month ?? date('m');        
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $mindate = "$year-$month-01";        
        $maxdate = date("Y-m-t", strtotime($mindate));
        // if($ko_cap_treo > -1){
        //     $query->where('ko_cap_treo', $ko_cap_treo);
        // }
        if($id_search){
           //  dd($id_search);
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptt", "", $id_search);
            $id_search = str_replace("pth", "", $id_search);
            $id_search = str_replace("ptv", "", $id_search);
            $id_search = str_replace("ptx", "", $id_search);
            $id_search = str_replace("ptc", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);            
        }elseif($phone){
            $arrSearch['phone'] = $phone;
            $query->where('phone', $phone);            
        }else{
          
            if($status){

                $arrSearch['status'] = $status;
                $query->whereIn('status', $status);            
            }         
           
            if($tour_id){                
                $query->where('tour_id', $tour_id);            
            }
            if($tour_cate){                
                $query->where('tour_cate', $tour_cate);
            }
            if($tour_type && $type == 1){               
                $query->whereIn('tour_type', $tour_type);            
            }
           
            if($phone){               
                $query->where('phone', $phone);
            }            
            if($name){               
                $query->where('name', 'LIKE', '%'.$name.'%');
            }             
            if($nguoi_thu_tien){               
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }
            if($nguoi_thu_coc){               
                $query->where('nguoi_thu_coc', $nguoi_thu_coc);
            }            
           
            if(Auth::user()->role < 3){                
                if($user_id && $user_id > 0){
                    $arrSearch['user_id'] = $user_id;
                    $query->where('user_id', $user_id);
                }    
            }else{           
                $arrSearch['user_id'] = Auth::user()->id;                
                $query->where('user_id', Auth::user()->id);
            }

                          

            if($created_at){
                $tmpDate = explode('/', $created_at);
                $created_at_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
                $query->where('created_at','>=', $created_at_format." 00:00:00");
                $query->where('created_at','<=', $created_at_format." 23:59:59");
            }else{
                if($time_type == 1){ // theo thangs
                    $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
                    $arrSearch['use_date_to'] = $use_date_to = date('d/m/Y', strtotime($maxdate));
                  
                    $query->where('use_date','>=', $mindate);                   
                    $query->where('use_date', '<=', $maxdate);
                }elseif($time_type == 2){ // theo khoang ngay
                    $arrSearch['use_date_from'] = $use_date_from = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
                    $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;

                    if($use_date_from){
                        $arrSearch['use_date_from'] = $use_date_from;
                        $tmpDate = explode('/', $use_date_from);
                        $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
                        $query->where('use_date','>=', $use_date_from_format);
                    }
                    if($use_date_to){
                        $arrSearch['use_date_to'] = $use_date_to;
                        $tmpDate = explode('/', $use_date_to);
                        $use_date_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];   
                        if($use_date_to_format < $use_date_from_format){
                            $arrSearch['use_date_to'] = $use_date_from;
                            $use_date_to_format = $use_date_from_format;   
                        }        
                        $query->where('use_date', '<=', $use_date_to_format);
                    }
                }else{
                    $arrSearch['use_date_from'] = $use_date_from = $arrSearch['use_date_to'] = $use_date_to = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
                    
                    $arrSearch['use_date_from'] = $use_date_from;
                    $tmpDate = explode('/', $use_date_from);
                    $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
                    $query->where('use_date','=', $use_date_from_format);
                    $day = $tmpDate[0];
                    $month_do = $tmpDate[1];
                }
            }
            
            
        }//end else
        

        $query->orderBy($sort_by, 'desc');
        
        
        $allList = $query->get();        
        
        
       
        $items  = $query->paginate(300); 
       // dd($items);
        $tong_hoa_hong_cty = $tong_so_nguoi = $tong_phan_an = $tong_coc = $tong_phan_an_te = 0 ;
        $tong_thuc_thu = $tong_hoa_hong_chup = $tong_doanh_so =  0; 
        $cap_nl = $cap_te = $tong_te =  0;
        $arrHDV = [];
        $tong_hdv_thu = $tong_thao_thu = 0;
      

        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
       
        $agent = new Agent();
       
        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        } 
       
        $userArr = [];
        $ghep = $vip = $thue = $tong_vip= 0;
        $arrThuCoc = $arrThuTien = [];
        if($allList->count() > 0){
            
            foreach($allList as $bk){
                if($bk->tour_type == 1){
                    $ghep += $bk->adults;
                }elseif($bk->tour_type == 2){
                    $vip++;
                    $tong_vip += $bk->adults;
                }elseif($bk->tour_type == 3){
                    $thue++;
                }
                $userArr[$bk->user_id] = $bk->user_id;            
                if($bk->status < 3){                                      
                    $tong_so_nguoi += $bk->adults;
                    $tong_te += $bk->childs;                                    
                    $tong_coc += $bk->tien_coc;                
                    if($bk->nguoi_thu_coc){
                        if(!isset($arrThuCoc[$bk->nguoi_thu_coc])) $arrThuCoc[$bk->nguoi_thu_coc] = 0;
                        $arrThuCoc[$bk->nguoi_thu_coc] += $bk->tien_coc;
                    }
                    if($bk->nguoi_thu_tien){
                        if(!isset($arrThuTien[$bk->nguoi_thu_tien])) $arrThuTien[$bk->nguoi_thu_tien] = 0;
                        $arrThuTien[$bk->nguoi_thu_tien] += $bk->tien_thuc_thu;
                    }
                    $tong_phan_an += $bk->meals;
                    $tong_phan_an_te += $bk->meals_te;                                
                    $cap_nl += $bk->cap_nl;
                    $cap_te += $bk->cap_te;                  
                    $tong_hoa_hong_cty += $bk->hoa_hong_cty;                    
                         
                }                  
                
                //update level                
                if(isset($arrUser[$bk->user_id]) && $arrUser[$bk->user_id]->level != $bk->level){
                    $bk->update(['level' => $arrUser[$bk->user_id]->level]);    
                }                
            }    
        }            
             
        if($agent->isMobile()){
            $view = 'booking-dn.m-index';
        }else{                   
            $view = 'booking-dn.index';
            
        }
            
        $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->where('city_id', $city_id)->get();
        $listTag = Location::where('city_id', $city_id)->get();
        return view($view, compact( 'items', 'arrSearch', 'type', 'listUser', 'tong_so_nguoi', 'tong_hoa_hong_cty', 'tong_phan_an', 'tong_coc', 'keyword', 'tong_thuc_thu', 'tong_te', 'arrHDV', 'arrUser',  'tong_phan_an_te', 'time_type', 'month', 'year', 'day','month_do', 'tourSystem'
            ,'arrThuCoc', 'arrThuTien', 'tong_doanh_so', 'city_id', 'tour_id', 'listTag', 'ghep', 'vip', 'tong_vip'));
        
    }
    
    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {   
        $user = Auth::user();
        $type = $request->type ? $request->type : 1;
        $tourList = Tour::all();
        $tour_id = $request->tour_id ?? null;
        $city_id = 2;

        $listTag = Location::where('city_id', $city_id)->get();
        
        $partnerList = Partner::getList(['cost_type_id'=> 54, 'city_id' => $city_id]);
                       
        
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        $tourSystem = TourSystem::where(['status' => 1, 'city_id' => $city_id])->orderBy('display_order')->get();
        
        $view = "booking-dn.add-tour";  
        return view($view, compact('type', 'listUser', 'listTag', 'tour_id', 'tourSystem', 'city_id', 'partnerList'));               
    }
   
    /**
    * Store a newly created resource in storage.
    *
    * @param  Request  $request
    * @return Response
    */
    public function store(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();
        
        $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',
            'location_id' => 'required',            
        ],
        [  
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'use_date.required' => 'Bạn chưa nhập ngày đi',
            'location_id.required' => 'Bạn chưa chọn nơi đón',            
        ]); 
        
        $arrPrice = ['total_price', 'total_cost', 'hoa_hong_cty', 'adult_cost', 'child_cost', 'price_adult', 'price_child', 'total_price_adult', 'total_price_child', 'con_lai', 'tien_coc'];
        foreach($arrPrice as $key){
            $dataArr[$key] = isset($dataArr[$key]) ?  (int) str_replace(',', '', $dataArr[$key]) : 0;
        }
        
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $tmpDate = explode('/', $dataArr['use_date']);
      
        $dataArr['use_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        
        if($dataArr['book_date']){
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
        }else{
            $dataArr['book_date'] = date('Y-m-d');    
        }
        //

        if($user->role < 3){            
            $detailUserBook = Account::find($dataArr['user_id']);      
        }else{
            $dataArr['user_id'] = $user->id;            
        }  
        
       
        $dataArr['name'] = ucwords($dataArr['name']);
       
        // -----------------end add customer
        
        $dataArr['created_user'] = $dataArr['updated_user'] = Auth::user()->id;
        if(isset($dataArr['no_meals'])){
            $dataArr['meals'] = $dataArr['meals_te'] = 0;
        }else{
            $dataArr['meals'] = $dataArr['adults'];
            $dataArr['meals_te'] = $dataArr['childs'];
        }
        $dataArr['city_id'] = 2;
        $rs = Booking::create($dataArr);
        $id = $rs->id;
        
        unset($dataArr['_token']);
        //store log
        $rsLog = BookingLogs::create([
            'booking_id' => $id,
            'content' => json_encode($dataArr),
            'user_id' => $user->id,
            'action' => 1
        ]);
       

        Session::flash('message', 'Tạo mới thành công');
        $use_date = date('d/m/Y', strtotime($dataArr['use_date']));        

        return redirect()->route('booking-dn.index', ['type' => $dataArr['type'], 'use_date_from' => $use_date, 'tour_id' => $dataArr['tour_id']]);
    }
    
   
   
    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return Response
    */
    public function edit($id, Request $request)
    {
        
        $tagSelected = [];
        $keyword = $request->keyword ?? null;
        $detail = Booking::find($id);
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        if(Auth::user()->role == 1){
            $ctvList = Ctv::where('status', 1)->where('leader_id', 18)->get();
        }else{
            if(Auth::user()->id == 64){
                $leader_id = 3;
            }else{
                $leader_id = Auth::user()->id;
            }
            $ctvList = Ctv::where('status', 1)->where('leader_id', $leader_id)->get();
        }
         $listTag = Location::all();
        if($detail->user_id != Auth::user()->id && Auth::user()->role == 2){
            dd('Bạn không có quyền truy cập.');
        }
        $arrSearch = $request->all();
        $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();

       
        $carCate = CarCate::where('type', 1)->get();
        $city_id = $detail->city_id;
         $partnerList = Partner::getList(['cost_type_id'=> 54, 'city_id' => $city_id]);
        return view('booking-dn.edit-tour', compact( 'detail', 'listUser', 'arrSearch','listTag', 'ctvList', 'keyword', 'tourSystem', 'carCate', 'city_id', 'partnerList'));
                   
        
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
        $user = Auth::user();
        $dataArr = $request->all();
      
        $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',            
        ],
        [  
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'use_date.required' => 'Bạn chưa nhập ngày đi',
            'location_id.required' => 'Bạn chưa chọn nơi đón',
            
        ]); 
          
        $arrPrice = ['total_price', 'total_cost', 'hoa_hong_cty', 'adult_cost', 'child_cost', 'price_adult', 'price_child', 'total_price_adult', 'total_price_child', 'con_lai', 'tien_coc'];
        foreach($arrPrice as $key){
            $dataArr[$key] = isset($dataArr[$key]) ?  (int) str_replace(',', '', $dataArr[$key]) : 0;
        }
        
        
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $tmpDate = explode('/', $dataArr['use_date']);
      
        $dataArr['use_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        

        if($dataArr['book_date']){
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
        }else{
            $dataArr['book_date'] = date('Y-m-d');    
        }

        if(Auth::user()->role < 3){
            $dataArr['user_id'] = $dataArr['user_id'];
        }else{
            $dataArr['user_id'] = Auth::user()->id;
        }
        $use_date = date('d/m/Y', strtotime($dataArr['use_date']));
        $model = Booking::find($dataArr['id']);
        $oldData = $model->toArray();
        
        unset($dataArr['_token']);
        
        $dataArr['export'] = 2;
        //$dataArr['notes'] = 'Updated.'.$dataArr['notes'];
        $dataArr['updated_user'] = Auth::user()->id;
        if(isset($dataArr['no_meals'])){
            $dataArr['meals'] = $dataArr['meals_te'] = 0;
        }else{
            $dataArr['meals'] = $dataArr['adults'];
            $dataArr['meals_te'] = $dataArr['childs'];
        }
        $dataArr['city_id'] = 2;
        $model->update($dataArr);        

        $booking_id = $dataArr['id'];
        
        unset($dataArr['no_meals']);
        $contentDiff = array_diff_assoc($dataArr, $oldData);
        
        $booking_id = $model->id;
        if(!empty($contentDiff)){
            $oldContent = [];
           
            foreach($contentDiff as $k => $v){
                $oldContent[$k] = $oldData[$k];
            }
            $rsLog = BookingLogs::create([
                'booking_id' => $booking_id,
                'content' =>json_encode(['old' => $oldContent, 'new' => $contentDiff]),
                'action' => 2,
                'user_id' => $user->id
            ]);            
        }
        
        Session::flash('message', 'Cập nhật thành công');
        
        return redirect()->route('booking-dn.index', ['use_date_from' => $use_date, 'user_id' => $dataArr['user_id'], 'tour_type[]' => $dataArr['tour_type'], 'tour_id' => $dataArr['tour_id']]); 
        
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
        $model = Booking::find($id);        
        $use_date = date('d/m/Y', strtotime($model->use_date));
        $type = $model->type;
        $model->update(['status' => 0]);       
        // redirect
        Session::flash('message', 'Xóa thành công');        
        return redirect()->route('booking-dn.index', ['type' => $type, 'use_date_from' => $use_date, 'tour_id' => $model->tour_id]);   
    }

    public function qrCode($id)
    {
        $detail = Booking::find($id);
        $link = 'https://plantotravel.vn/booking/'.$id.'';
        $qrCode = QrCode::size(250)->generate($link);
        return view('qr-code', compact('qrCode','detail','link'));
        
    }
    public function checkUnc(Request $request){        
        $id = $request->id;
        $rs = Booking::find($id);
        //dd($rs);
        $errorStr = '';
        if($rs->nguoi_thu_tien == 2){
            $paymentList = $rs->payment;
            if($paymentList->count() == 0){
                $errorStr = 'Thiếu UNC';
            }
        }
       
        return $errorStr;
    }

    public function ajaxGetPrice(Request $request){
        $defaultArr = ['adult_cost' => '', 'child_cost' => ''];
        $tour_id = $request->tour_id;
        $partner_id = $request->partner_id;
        $use_date = $request->use_date;
        $tmp = explode('/', $use_date);
       
        $use_date_format = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];

        $dataArr = TourSystemPrice::getPriceByDate($partner_id, $tour_id, $use_date_format);
        if(!$dataArr){
            return json_encode($defaultArr);
        }
        return json_encode($dataArr);
    }

}