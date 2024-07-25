<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rating;
use App\Models\Hotels;
use App\Models\BookingRooms;
use App\Models\BookingLogs;
use App\Models\TicketTypeSystem;
use App\Models\Tickets;
use App\Models\Location;
use App\Models\Tour;
use App\Models\CarCate;
use App\Models\BoatPrices;
use App\Models\Drivers;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Ctv;
use App\Models\GrandworldSchedule;
use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class BookingCameraController extends Controller
{
   
    public function index(Request $request)
    {
        $day = date('d');
        $month_do = date('m');        
        $arrSearch['month'] = $month = $request->month ?? date('m');        
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $mindate = "$year-$month-01";        
        $maxdate = date("Y-m-t", strtotime($mindate));              
        
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null; 
        $arrSearch['status'] = $status = $request->status ? $request->status : [1, 2];       
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;       
        $arrSearch['ctv_id'] = $ctv_id = $request->ctv_id ?? null;          
        $arrSearch['phone'] = $phone = $request->phone ? $request->phone : null;      
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 2;       
        $arrSearch['camera_id'] = $camera_id = $request->camera_id ? $request->camera_id : null;

        $query = Booking::where(['type' => 5, 'city_id' => 1]);

        if($id_search){
            $id_search = strtolower($id_search);           
            $id_search = str_replace("ptv", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
        }
        if($status){
            $arrSearch['status'] = $status;
            $query->whereIn('status', $status);            
        }        
        if($phone){
            $arrSearch['phone'] = $phone;
            $query->where('phone', $phone);
        }
        if($nguoi_thu_tien){
            $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien;
            $query->where('nguoi_thu_tien', $nguoi_thu_tien);
        }
        if($nguoi_thu_coc){
            $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc;
            $query->where('nguoi_thu_coc', $nguoi_thu_coc);
        }               
        if($ctv_id){
            $query->where('ctv_id', $ctv_id);
        }  
        if($camera_id > 0){
            $query->where('camera_id', $camera_id);
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
                
                    $query->where('use_date','<=', $use_date_to_format);
                
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

        $query->orderBy('id', 'desc');
      
        $allList = $query->get();        

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
       
        $items  = $query->paginate(300);      

        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        
        $agent = new Agent();
        if($agent->isMobile()){          
            $view = 'booking-camera.index';
        }else{
            $view = 'booking-camera.index';
        }
        $cameraList = User::where('camera', 1)->where('status', 1)->get();
        
        $tong_hoa_hong_sales = $tong_hoa_hong_chup = 0;
        if($allList->count() > 0){
            
            foreach($allList as $bk){              
                if($bk->status < 3){
                    
                    $tong_hoa_hong_sales += $bk->hoa_hong_sales;                    
                    $tong_hoa_hong_chup += $bk->hoa_hong_chup;               
                }     
            }    
        }

        return view($view, compact( 'items', 'arrSearch', 'listUser', 'cameraList', 'ctvList', 'time_type', 'nguoi_thu_tien', 'time_type', 'month', 'year', 'ctvList', 'search_by', 'tong_hoa_hong_sales', 'tong_hoa_hong_chup'));
         
    }

    public function create(Request $request)
    {   
        $user = Auth::user();        
        $listTag = Location::where('city_id', $user->city_id)->get();
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
        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6])->where('status', 1)->get();
        $cameraList = User::where('camera', 1)->where('status', 1)->get();
        $user_id_default = $user->role == 1 && $user->level == 6 ? $user->id : null;

        return view("booking-camera.add", compact('listUser', 'listTag', 'ctvList', 'cameraList', 'user_id_default'));               
    }

    public function edit($id, Request $request)
    {
        
        $tagSelected = [];
        $keyword = $request->keyword ?? null;
        $detail = Booking::find($id);
        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6])->where('status', 1)->get();
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
       
        $cameraList = User::where('camera', 1)->where('status', 1)->get();                           
        return view('booking-camera.edit', compact( 'detail', 'listUser', 'arrSearch', 'listTag', 'ctvList', 'cameraList', 'keyword'));        
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();
        
        $this->validate($request,[
            'camera_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',
            'location_id' => 'required',
          //  'user_id' => 'required',
        ],
        [  
            'camera_id.required' => 'Bạn chưa chọn thợ chụp',
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'use_date.required' => 'Bạn chưa nhập ngày chụp',
            'location_id.required' => 'Bạn chưa chọn nơi chụp',
           // 'user_id.required' => 'Bạn chưa chọn sales',
        ]);       
        $dataArr['total_price'] =(int) str_replace(',', '', $dataArr['total_price']);
        $dataArr['tien_coc'] = (int) str_replace(',', '', $dataArr['tien_coc']);
        $dataArr['con_lai'] = (int) str_replace(',', '', $dataArr['con_lai']);
        $dataArr['meals'] = 0;
        $dataArr['discount'] = 0;
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $tmpDate = explode('/', $dataArr['use_date']);
        $dataArr['use_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        if($dataArr['ngay_coc']){
            $tmpDate = explode('/', $dataArr['ngay_coc']);
            $dataArr['ngay_coc'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];  
        }
        if($dataArr['book_date']){
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
        }else{
            $dataArr['book_date'] = date('Y-m-d');    
        }

        if($user->role < 3){
            $dataArr['user_id'] = $dataArr['user_id'];
        }else{
            $dataArr['user_id'] = $user->id;
        }        
        $dataArr['name'] = ucwords($dataArr['name']);
        $dataArr['type'] = 5;
        $dataArr['hoa_hong_chup'] = 30*$dataArr['total_price']/100;  
        $dataArr['created_user'] = $dataArr['updated_user'] = Auth::user()->id;      
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
        // push notification
       // dd($rs);
        $userIdPush = Helper::getUserIdPushNoti($id, 1);
      // dd($userIdPush);
        // foreach($userIdPush as $idPush){
        //     if($idPush > 0){
        //         UserNotification::create([
        //             'title' => $user->name." vừa tạo PTC".$id,
        //             'content' => '',
        //             'user_id' => $idPush,
        //             'booking_id' => $id,
        //             'date_use' => $rs->use_date,
        //             'data' => json_encode($dataArr),
        //             'type' => 1,
        //             'is_read' => 0
        //         ]);
        //     }            
        // }
        
        // store customer
        if(!isset($dataArr['customer_id']) || $dataArr['customer_id'] == ""){
            
            $customer_id = Helper::storeCustomer($dataArr);
           
            $rs->update(['customer_id' => $customer_id]);
        }
        Session::flash('message', 'Tạo mới thành công');
        $use_date = date('d/m/Y', strtotime($dataArr['use_date']));
        // if($use_date  == date('d/m/Y', strtotime('tomorrow')) 
        //     || $use_date  == date('d/m/Y', time())
        // ){
        //    $this->curlExport();
        // }
        return redirect()->route('booking-camera.index', ['use_date_from' => $use_date, 'camera_id' => $dataArr['camera_id']]);  
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();
        
        $this->validate($request,[
            'camera_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',
            'location_id' => 'required',
            'user_id' => 'required',
        ],
        [  
            'camera_id.required' => 'Bạn chưa chọn thợ chụp',
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'use_date.required' => 'Bạn chưa nhập ngày chụp',
            'location_id.required' => 'Bạn chưa chọn nơi chụp',
            'user_id.required' => 'Bạn chưa chọn sales',
        ]);       
        $dataArr['total_price'] =(int) str_replace(',', '', $dataArr['total_price']);
        $dataArr['tien_coc'] = (int) str_replace(',', '', $dataArr['tien_coc']);
        $dataArr['con_lai'] = (int) str_replace(',', '', $dataArr['con_lai']);
        $dataArr['meals'] = 0;
        $dataArr['discount'] = 0;
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $tmpDate = explode('/', $dataArr['use_date']);
        $dataArr['use_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        if($dataArr['ngay_coc']){
            $tmpDate = explode('/', $dataArr['ngay_coc']);
            $dataArr['ngay_coc'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];  
        }
        if($dataArr['book_date']){
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];            
        }else{
            $dataArr['book_date'] = date('Y-m-d');    
        }

        if($user->role < 3){
            $dataArr['user_id'] = $dataArr['user_id'];
        }else{
            $dataArr['user_id'] = $user->id;
        }        
        $dataArr['name'] = ucwords($dataArr['name']);
        $dataArr['type'] = 5;

        $model = Booking::find($dataArr['id']);
        $oldData = $model->toArray();
        
        unset($dataArr['_token']);
        //
        //unset($oldData['updated_at']);
        
        $use_date_old = $model->use_date;
        
        $dataArr['export'] = 2;
        //$dataArr['notes'] = 'Updated.'.$dataArr['notes'];
        $dataArr['hoa_hong_chup'] = 30*$dataArr['total_price']/100;

        $dataArr['updated_user'] = Auth::user()->id;
        $model->update($dataArr); 
       
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
            // push notification
            $userIdPush = Helper::getUserIdPushNoti($booking_id);
            // dd($userIdPush);
            // foreach($userIdPush as $idPush){
            //     if($idPush > 0){
            //         UserNotification::create([
            //             'title' => 'PTC'.$booking_id.' vừa được '. $user->name." cập nhật",
            //             'content' => Helper::parseLog($rsLog),
            //             'user_id' => $idPush,
            //             'booking_id' => $booking_id,
            //             'date_use' => $model->use_date,
            //             //'data' => json_encode($dataArr),
            //             'type' => 1,
            //             'is_read' => 0
            //         ]);
            //     }                
            // }
        }        
        $use_date = date('d/m/Y', strtotime($dataArr['use_date']));
        Session::flash('message', 'Cập nhật thành công');
        return redirect()->route('booking-camera.index', ['use_date_from' => $use_date, 'camera_id' => $dataArr['camera_id']]);  
    }
    public function destroy($id)
    {
        // delete
        $model = Booking::find($id);        
        $use_date = date('d/m/Y', strtotime($model->use_date));
        $type = $model->type;
        $model->update(['status' => 0]);       
        // redirect
        Session::flash('message', 'Xóa booking thành công');        
        return redirect()->route('booking-camera.index', ['use_date_from' => $use_date]);   
    }
}