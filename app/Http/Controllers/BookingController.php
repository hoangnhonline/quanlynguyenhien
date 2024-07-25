<?php

namespace App\Http\Controllers;

use App\Models\BookingNotes;
use App\Services\NotificationService;
use App\Services\SmsService;
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
use App\Models\TourSystem;
use App\Models\CarCate;
use App\Models\BoatPrices;
use App\Models\Drivers;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Ctv;

use App\Models\TourPrice;
use App\Models\Deposit;

use App\Models\Logs;

use App\Models\CodeNopTien;
use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash, Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserNotification;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;

class BookingController extends Controller
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }   
    public function maps(Request $request){
        return view('booking.maps');
    }    
   
    public function changeValueByColumn(Request $request){
        $booking_id = $request->id;
        $column = $request->col;
        $value = $request->value;
        $model = Booking::find($booking_id);

        $oldData = [$column => $model->$column];
        $dataArr = [$column => $value];

        $contentDiff = array_diff_assoc($dataArr, $oldData);


        if(!empty($contentDiff)){
            $oldContent = [];

            foreach($contentDiff as $k => $v){
                $oldContent[$k] = $oldData[$k];
            }
            $rsLog = BookingLogs::create([
                'booking_id' => $booking_id,
                'content' =>json_encode(['old' => $oldContent, 'new' => $contentDiff]),
                'action' => 2,
                'user_id' => Auth::user()->id
            ]);
        }

        if($column == "cano_id"){
            $cano_id = $value;
            $hdv_id = $model->hdv_id;
            $use_date = $model->use_date;
            $bk = Booking::find($booking_id);
            $bk->update(['cano_id' => $cano_id]);           
        }
        if($column == 'price_net' && $value == 1){ // nếu giá net thì hoa hồng sales = 0
            $bk = Booking::find($booking_id);
            $bk->update(['hoa_hong_sales' => 0]);
        }
        if($column == 'cty_send'){
            if($value == 0){
                $model->update(['is_send' => 0]);
            }else{
                $model->update(['is_send' => 1]);
            }
        }
        $model->update([$column => $value]);
    }

   
    public function info(Request $request){
        $id = $request->id;
        $detail = Booking::find($id);
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        return view('booking.modal', compact( 'detail', 'listUser'));
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
        $arrSearch['type'] = $type = $request->type ?? 1;     
        $arrSearch['tour_no'] = $tour_no = $request->tour_no ?? null;         
        $arrSearch['short'] = $short = $request->short ?? null;       
        $arrSearch['keyword'] = $keyword = $request->keyword ? $request->keyword : null;
        $arrSearch['cano0'] = $cano0 = $request->cano0 ? $request->cano0 : null;
        $arrSearch['ko_cap_treo'] = $ko_cap_treo = $request->ko_cap_treo > -1 ? $request->ko_cap_treo : null;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1,2,4,5];
      
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : null;
        $arrSearch['tour_cate'] = $tour_cate = $request->tour_cate ? $request->tour_cate : null;
        $arrSearch['tour_type'] = $tour_type = $request->tour_type ?? [1,2,3];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
       
        $arrSearch['cano_id'] = $cano_id = $request->cano_id ? $request->cano_id : null;
        $arrSearch['phone'] = $phone = $request->phone ?? null;
       
        $arrSearch['sort_by'] = $sort_by = $request->sort_by ? $request->sort_by : 'created_at';
       
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : "";
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 2;
        $arrSearch['is_send'] = $is_send = $request->is_send ?? null;
        $arrSearch['cty_send'] = $cty_send = $request->cty_send ?? null;
        $arrSearch['no_cab'] = $no_cab = $request->no_cab ? $request->no_cab : null;
        $arrSearch['no_meals'] = $no_meals = $request->no_meals ? $request->no_meals : null;
        if($type == 1){
            $use_df_default = Auth::user()->id == 151 ? date('d/m/Y', strtotime('yesterday')) : date('d/m/Y', time());
            $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $use_df_default;

        }
        $arrSearch['created_at'] = $created_at = $request->created_at ? $request->created_at :  null;

        $arrSearch['book_date'] = $book_date = $request->book_date ? $request->book_date :  null;
        $arrSearch['book_date_from'] = $book_date_from = $request->book_date_from ? $request->book_date_from :  null;

        $arrSearch['book_date_to'] = $book_date_to = $request->book_date_to ? $request->book_date_to : null;

        $city_id = 1;
        $query = Booking::where('type', $type); // 1 : tour
        $query->whereRaw('1');

      
        if($tour_no){
            $query->where('tour_no', $tour_no);
        }
        if($keyword){
            $type = null;
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

            $query->where('id', $id_search);
        }elseif($phone){
            $query->where('phone', $phone);
        }else{
            
            if($status){
                $query->whereIn('status', $status);
            }            
            if($no_cab){
                $query->where('cap_nl', 0);
            }
            if($no_meals){
                $arrSearch['no_meals'] = $no_meals;
                $query->where('meals', 0);
            }
            if($cano_id || $cano0){
                if($cano_id){
                    $arrSearch['cano_id'] = $cano_id;
                    $query->where('cano_id', $cano_id);
                }

                if($cano0 == 1){
                    $query->where(function ($query) {
                        $query->whereNull('cano_id')
                            ->orWhere('cano_id', '=', 0);
                    });
                }
                if($cano0 == 2){
                    $query->where('cano_id', '>', 0);
                }
            }
            
            if($is_send){
                $query->where('is_send', 1);
            }
            
                      
            if($tour_cate){
                $arrSearch['tour_cate'] = $tour_cate;
                $query->where('tour_cate', $tour_cate);
            }
            if($tour_type && $type == 1){
                $arrSearch['tour_type'] = $tour_type;
                $query->whereIn('tour_type', $tour_type);
            }
            if($cano_id){
                $arrSearch['cano_id'] = $cano_id;
                $query->where('cano_id', $cano_id);
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
            
            if($cty_send){
                $query->where('cty_send', $cty_send);
            }

            if(Auth::user()->role < 3){
                if($user_id && $user_id > 0){
                    $arrSearch['user_id'] = $user_id;
                    $query->where('user_id', $user_id);
                }
            }else{
                $arrSearch['user_id'] = Auth::user()->id;
                if(Auth::user()->id == 3){
                    $query->whereIn('user_id', [3, 64]);
                }else{
                    $query->where('user_id', Auth::user()->id);
                }

            }



                if($created_at){
                    $tmpDate = explode('/', $created_at);
                    $created_at_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    $query->where('created_at','>=', $created_at_format." 00:00:00");
                    $query->where('created_at','<=', $created_at_format." 23:59:59");
                }else{
                    $minDateKey = 0;
                    $maxDateKey = 1;

                    $rangeDate = array_unique(explode(' - ', $range_date));
                    if (empty($rangeDate[$minDateKey])) {
                        //case page is initialized and range_date is empty => today
                        $rangeDate = Carbon::now();
                        $query->where('use_date','=', $rangeDate->format('Y-m-d'));
                        $arrSearch['range_date'] = $rangeDate->format('d/m/Y') . " - " . $rangeDate->format('d/m/Y');
                        $arrSearch['use_date_from'] = $rangeDate->format('d/m/Y');
                        $time_type = 3;
                    } elseif (count($rangeDate) === 1) {
                        //case page is initialized and range_date has value,
                        //when counting the number of elements in rangeDate = 1 => only select a day
                        $query->where('use_date','=', Carbon::createFromFormat('d/m/Y', $rangeDate[$minDateKey])->format('Y-m-d'));
                        $arrSearch['range_date'] = $rangeDate[$minDateKey] . " - " . $rangeDate[$minDateKey];
                        $arrSearch['use_date_from'] = $rangeDate[$minDateKey];
                        $time_type = 3;
                    } else {
                        $query->where('use_date','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$minDateKey])->format('Y-m-d'));
                        $query->where('use_date', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$maxDateKey])->format('Y-m-d'));
                        $arrSearch['use_date_from'] = $arrSearch['range_date'];
                        $time_type = 1;
                    }
                }


        }//end else        

     
        if($cano_id){
            $arrSearch['cano_id'] = $cano_id;
            $query->where('cano_id', $cano_id);
        }
        
        

        $allList = $query->get();
       

        $items  = $query->paginate(300);
       // dd($items);
        $tong_so_nguoi = $tong_phan_an = $tong_coc = $tong_phan_an_te = 0 ;
        $tong_thuc_thu = $tong_doanh_so =  0;
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
                if($bk->status != 3){
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

                }

                //update level
                if(isset($arrUser[$bk->user_id]) && $arrUser[$bk->user_id]->level != $bk->level){
                    $bk->update(['level' => $arrUser[$bk->user_id]->level]);
                }
            }
        }


        if($type == 1){
            if(Auth::user()->id == 151){
                $view = 'booking.acc-index';
            }elseif(Auth::user()->id == 212 || Auth::user()->id == 333){
                $view = 'booking.index-dieu-hanh';
                if($agent->isMobile()){
                    $view = 'booking.m-index';
                }
            }elseif(Auth::user()->id == 213){
                $view = 'booking.index-hh';
            }else{
                if($agent->isMobile()){
                    $view = 'booking.m-index';
                }else{
                    if(Auth::user()->is_full == 1){
                        $view = 'booking.index-full';
                    }else{
                        $view = 'booking.index';
                    }

                }
                if($short == 1){
                    $view = 'booking.index-short';
                }
            }

            $listHDV = User::where('hdv', 1)->where('status', 1)->get();
            foreach($listHDV as $u){
                $arrHDVDefault[$u->id] = $u;
            }
           
            $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();

            return view($view, compact( 'items', 'arrSearch', 'type', 'listUser', 'tong_so_nguoi', 'tong_phan_an', 'tong_coc', 'keyword', 'tong_thuc_thu', 'cap_nl', 'cap_te', 'tong_te', 'arrUser', 'tong_phan_an_te', 'tong_hdv_thu', 'time_type', 'month', 'year', 'day', 'tong_thao_thu','month_do', 'ghep', 'vip', 'thue', 'tong_vip', 'tourSystem'
                ,'arrThuCoc', 'arrThuTien', 'tong_doanh_so'));
        }
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $user = Auth::user();
    
        $tourList = Tour::all();
        $tour_id = $request->tour_id ?? null;
        $listTag = Location::where('city_id', $user->city_id)->where('status', 1)->get();      

        $cateList = Tour::all();       

        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        if($user->id == 333){
            $listUser = User::whereIn('level', [7])->where('status', 1)->get();
        }
        $user_id_default = $user->role == 1 && $user->level == 6 ? $user->id : null;
        return view("booking.add-tour", compact('listUser', 'listTag', 'cateList', 'tour_id', 'user_id_default'));
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
        $this->validate($request,
            array_merge(
                [
                    'name' => 'required',
                    'phone' => 'required',
                    'use_date' => 'required',
                    'location_id' => 'required',
                  
                ], $user->role < 3 ? ['user_id' => 'required'] : []
            ),
            array_merge([
                'name.required' => 'Bạn chưa nhập tên',
                'phone.required' => 'Bạn chưa nhập điện thoại',
                'use_date.required' => 'Bạn chưa nhập ngày đi',
                'location_id.required' => 'Bạn chưa chọn nơi đón',
               
            ], $user->role < 3 ? ['user_id.required' => 'Bạn chưa chọn đối tác'] : [])
        );

        $dataArr['total_price'] = (int) str_replace(',', '', $dataArr['total_price']);
      
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $tmpDate = explode('/', $dataArr['use_date']);

        $dataArr['use_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        if(isset($dataArr['ngay_coc'])){
            $tmpDate = explode('/', $dataArr['ngay_coc']);
            $dataArr['ngay_coc'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }
        if(isset($dataArr['book_date'])){
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }else{
            $dataArr['book_date'] = date('Y-m-d');
        }        

        
        $dataArr['user_id'] = $user->id;
        $dataArr['level'] = $user->level;
          
       
        $dataArr['ko_cap_treo'] = (isset($dataArr['ko_cap_treo']) || ($dataArr['cap_nl'] == 0 && $dataArr['cap_te'] == 0)) ? 1 : 0;
        

        $dataArr['name'] = ucwords($dataArr['name']);

        //gui cty khac
        if(isset($dataArr['cty_send']) && $dataArr['cty_send'] > 0){
            $dataArr['is_send'] = 1;
        }

        
        $dataArr['created_user'] = $dataArr['updated_user'] = $user->id;        
        

        $ko_thu_tien = isset($dataArr['not_pay']) && $dataArr['not_pay'] == 1 ? true : false;
        $dataArr['nguoi_thu_tien'] = $ko_thu_tien == true ? 3 : $dataArr['nguoi_thu_tien'];



        $rs = Booking::create($dataArr);

        $booking_id = $rs->id;

        unset($dataArr['_token']);
        //store log
        $rsLog = new BookingLogs([
            'booking_id' => $booking_id,
            'content' => json_encode($dataArr),
            'user_id' => $user->id,
            'action' => 1
        ]);
        $rsLog->notes = Helper::parseLog($rsLog, false);
        $rsLog->save();

        //Send notifications
        $this->sendBookingNotificationToUser($rs, $rsLog, $request->user()->id);

       
        Session::flash('message', 'Tạo mới thành công');
        $use_date = date('d/m/Y', strtotime($dataArr['use_date']));
      
        return redirect()->route('booking.index', ['range_date' => $use_date]);
    }
    public static function storeDonTienFree($booking_id, $user_id, $dataArr, $is_edit = 0){
        //luu don tien mien phi
        if($dataArr['don_location_id'] && $dataArr['don_car_cate_id'] && $dataArr['don_ngay']){

            $tmpDate = explode('/', $dataArr['don_ngay']);
            $don_ngay = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
            $arr = [
                'booking_id' => $booking_id,
                'location_id' => $dataArr['don_location_id'],
                'location_id_2' => $dataArr['don_location_id_2'],
                'type' => 1, // don
                'use_date' => $don_ngay,
                'car_cate_id' => $dataArr['don_car_cate_id'],
                'use_time' => $dataArr['don_gio'].":".$dataArr['don_phut'],
                'notes' => $dataArr['don_ghichu'],
                'user_id' => $user_id,
                'use_date_time' => $don_ngay." ".$dataArr['don_gio'].":".$dataArr['don_phut'].":00",
                'phone' => $dataArr['phone']
            ];
            $rs = DonTienFree::create($arr);
            //write logs
            unset($dataArr['_token']);
            Logs::create([
                'table_name' => 'don_tien_free',
                'user_id' => Auth::user()->id,
                'action' => 1,
                'content' => json_encode($arr),
                'object_id' => $rs->id
            ]);

        }
        if($dataArr['tien_location_id'] && $dataArr['tien_car_cate_id'] && $dataArr['tien_ngay']){

            $tmpDate = explode('/', $dataArr['tien_ngay']);
            $tien_ngay = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
            $arr = [
                'booking_id' => $booking_id,
                'location_id' => $dataArr['tien_location_id'],
                'location_id_2' => $dataArr['tien_location_id_2'],
                'type' => 2, // don
                'use_date' => $tien_ngay,
                'car_cate_id' => $dataArr['tien_car_cate_id'],
                'use_time' => $dataArr['tien_gio'].":".$dataArr['tien_phut'],
                'notes' => $dataArr['tien_ghichu'],
                'phone' => $dataArr['phone'],
                'name' => $dataArr['name'],
                'user_id' => $user_id,
                'use_date_time' => $tien_ngay." ".$dataArr['tien_gio'].":".$dataArr['tien_phut'].":00",
                'phone' => $dataArr['phone']
            ];
            $rs = DonTienFree::create($arr);
            //write logs
            unset($dataArr['_token']);
            Logs::create([
                'table_name' => 'don_tien_free',
                'user_id' => Auth::user()->id,
                'action' => 1,
                'content' => json_encode($arr),
                'object_id' => $rs->id
            ]);

        }
    }
    public function storeShort(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();

        $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',
            'location_id' => 'required',
            'user_id' => 'required',
        ],
        [
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'use_date.required' => 'Bạn chưa nhập ngày đi',
            'location_id.required' => 'Bạn chưa chọn nơi đón',
            'user_id.required' => 'Bạn chưa chọn SALES',
        ]);


        $dataArr['total_price'] =isset($dataArr['total_price']) ? (int) str_replace(',', '', $dataArr['total_price']) : 0 ;
        $dataArr['tien_coc'] = (int) str_replace(',', '', $dataArr['tien_coc']);
        $dataArr['con_lai'] = (int) str_replace(',', '', $dataArr['total_price']);
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $dataArr['hdv_thu'] = isset($dataArr['hdv_thu']) ? (int) str_replace(',', '', $dataArr['hdv_thu']) : 0;
        $tmpDate = explode('/', $dataArr['use_date']);

        $dataArr['use_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];

        $dataArr['book_date'] = date('Y-m-d');
        $dataArr['tour_cate'] = 1;
        if($user->role < 3){
            $detailUserBook = Account::find($dataArr['user_id']);
            $dataArr['level'] = $detailUserBook->level;
        }else{
            $dataArr['user_id'] = $user->id;
            $dataArr['level'] = $user->level;
        }
        $dataArr['ko_cap_treo'] = isset($dataArr['ko_cap_treo']) ? 1 : 0;

        $dataArr['name'] = ucwords($dataArr['name']);

        $ko_thu_tien = isset($dataArr['not_pay']) && $dataArr['not_pay'] == 1 ? true : false;
        $dataArr['nguoi_thu_tien'] = $ko_thu_tien == true ? 4 : 3;
       
        $dataArr['created_user'] = $dataArr['updated_user'] = Auth::user()->id;

        $dataArr['export'] = 2;
        $rs = Booking::create($dataArr);
        $id = $rs->id;

        unset($dataArr['_token']);
        //store log
        $rsLog = new BookingLogs([
            'booking_id' => $id,
            'content' => json_encode($dataArr),
            'user_id' => $user->id,
            'action' => 1
        ]);
        $rsLog->notes = Helper::parseLog($rsLog, false);
        $rsLog->save();

        //Send notifications
        $this->sendBookingNotificationToUser($rs, $rsLog, $request->user()->id);

        Session::flash('message', 'Tạo mới thành công');
        $use_date = date('d/m/Y', strtotime($dataArr['use_date']));
        // if($use_date  == date('d/m/Y', strtotime('tomorrow'))
        //     || $use_date  == date('d/m/Y', time())
        // ){
        //    $this->curlExport();
        // }

        return redirect()->route('booking.index', ['range_date' => $use_date, 'tour_id' => $dataArr['tour_id']]);
    }


    function curlExport(){
        $output=null;
        $retval=null;
        exec('curl https://plantotravel.vn/sheet/index.php', $output, $retval);
    }


    public function trangthaigoi($call_status){
        switch ($call_status) {
            case '1':
                $text = "Chưa gọi";
                break;
            case '2':
                $text = "Gọi OK";
                break;
            case '3':
                $text = "Chưa nghe máy";
                break;
            case '4':
                $text = "Thuê bao";
                break;
            case '5':
                $text = "Sai số";
                break;
            case '6':
                $text = "Dời ngày";
                break;
            case '7':
                $text = "Khách hủy";
                break;
            default:
                # code...
                break;
        }
        return $text;
    }
    public function replyMessCapNhat($rs){
        $id = $rs->id;
        $url = 'https://openapi.zalo.me/v2.0/oa/message?access_token=ZaVgNfRnPLUDG-XRalLgKuT2u5UJwn83YYxgIf302XZv9iX1ljKr5ia6ongBp3bgwYJd19F03q_vDECyjzeoDVGeuJglm6a_yY_hMwpR1IwmRiz6nTv0Bw0igLNz-c1Tv16i0fttL5FYGgC3hAOW3SPB_dA6-0rYw1py1uli77Vn4jCIfifGREGLln2Yfaf3sdEP6OsPSMVDGQCGX_DuRl95kXwEe4b5a6s6J-AhVp2zHwzrwBXP8Prjaotvt4mzkMo1SkE22G2XQze8leeALDj4tX5FQ2s7kZsGxcDL';
        //$strpad = str_pad($booking_id, 5, '0', STR_PAD_LEFT);
       // $use_date = date('d/m', strtotime($dataArr['use_date']));
        //$booking_code = 'T'.$ctv_id.$strpad;

        // reply cho sales
        $textStr = '*** CẬP NHẬT ***'."\n\r";
        $textStr .= 'Mã booking: PTT'.$id."\n\r";
        $textStr .= 'Tên KH: '.$rs->name."\n\r";
        $textStr .= 'Nơi đón: '.$rs->address."\n\r";
        $textStr .= 'Trạng thái: '.$this->trangthaigoi($rs->call_status)."\n\r";
        if($rs->hdv){
             $textStr .= 'HDV: '.$rs->hdv->name."\n\r";
        }else{
            $textStr .= 'HDV: chưa chọn'."\n\r";
        }
        if($rs->hdv_notes){
             $textStr .= 'Ghi chú: '.$rs->hdv_notes."\n\r";
        }

        $arrData = [
            'recipient' => [
                'user_id' => '7317386031055599346',
            ],
            'message' => [
                'text' => $textStr,
            ]
        ];
        $ch = curl_init( $url );
        # Setup request to send json via POST.
        $payload = json_encode( $arrData );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        # Print response.
        echo "<pre>$result</pre>";

        $arrData = [
            'recipient' => [
                'user_id' => $rs->user->zalo_id,
            ],
            'message' => [
                'text' => $textStr,
            ]
        ];
        $ch = curl_init( $url );
        # Setup request to send json via POST.
        $payload = json_encode( $arrData );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        # Print response.
        echo "<pre>$result</pre>";

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
        
         $listTag = Location::where('status', 1)->get();
        if($detail->user_id != Auth::user()->id && Auth::user()->role == 2){
            dd('Bạn không có quyền truy cập.');
        }
        $arrSearch = $request->all();
        $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();
        $tours = Tour::where('city_id', $detail->city_id)->get();        
        return view('booking.edit-tour', compact( 'detail', 'listUser', 'arrSearch','listTag', 'keyword', 'tourSystem', 'tours'));
        

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
        if($dataArr['status']!= 3){
            $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',
            'grandworld_date' => 'required_if:is_grandworld,1',
            'user_id' => 'required',
        ],
        [
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'use_date.required' => 'Bạn chưa nhập ngày đi',
            'location_id.required' => 'Bạn chưa chọn nơi đón',
            //'nguoi_thu_tien.required' => 'Bạn chưa chọn người thu tiền',
            'grandworld_date.required_if' => 'Bạn chưa nhập ngày khách chụp ảnh ở Grand World',
            'user_id.required' => 'Bạn chưa chọn Sales',
        ]);
        }

        $dataArr['total_price_adult'] = isset($dataArr['total_price_adult']) ? (int) str_replace(',', '', $dataArr['total_price_adult']) : 0;
        $dataArr['total_price_child'] = (int) str_replace(',', '', $dataArr['total_price_child']);
        $dataArr['total_price'] =(int) str_replace(',', '', $dataArr['total_price']);
        $dataArr['tien_thuc_thu'] =isset($dataArr['tien_thuc_thu']) ? (int) str_replace(',', '', $dataArr['tien_thuc_thu']) : null;
        $dataArr['tien_coc'] = (int) str_replace(',', '', $dataArr['tien_coc']);
        $dataArr['extra_fee'] = (int) str_replace(',', '', $dataArr['extra_fee']);
        $dataArr['discount'] = (int) str_replace(',', '', $dataArr['discount']);
        $dataArr['con_lai'] = (int) str_replace(',', '', $dataArr['con_lai']);
        $dataArr['hdv_thu'] = isset($dataArr['hdv_thu']) ? (int) str_replace(',', '', $dataArr['hdv_thu']) : 0;
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $tmpDate = explode('/', $dataArr['use_date']);
        $dataArr['use_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        if($dataArr['ngay_coc']){
            $tmpDate = explode('/', $dataArr['ngay_coc']);
            $dataArr['ngay_coc'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }

        if($dataArr['grandworld_date']){
            $tmpDate = explode('/', $dataArr['grandworld_date']);
            $dataArr['grandworld_date'] = $grandworld_date = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }
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

       // $dataArr['price_adult'] = $dataArr['total_price_adult']/$dataArr['adults'];

        if($dataArr['status'] > 2){
            $dataArr['hoa_hong_cty'] = $dataArr['hoa_hong_sales'] = 0;
        }
        if(isset($dataArr['hoa_hong_cty'])){
            $dataArr['hoa_hong_cty'] = (int) str_replace(',', '', $dataArr['hoa_hong_cty']);
        }
        if(isset($dataArr['hoa_hong_sales'])){
            $dataArr['hoa_hong_sales'] = (int) str_replace(',', '', $dataArr['hoa_hong_sales']);
        }
        $use_date = date('d/m/Y', strtotime($dataArr['use_date']));
        $model = Booking::find($dataArr['id']);
       

        $oldDataBooking = $model->toArray();

        unset($dataArr['_token']);
        //
        //unset($oldData['updated_at']);

        $use_date_old = $model->use_date;
        if($use_date_old != $use_date){
           // $dataArr['status'] = 1;
           // $dataArr['export'] = 2;
        }
        // if($dataArr['hoa_hong_sales'] > 0){
        //    // $dataArr['status']= 2;
        // }

        $dataArr['export'] = 2;
        //$dataArr['notes'] = 'Updated.'.$dataArr['notes'];
        $dataArr['price_old'] = isset($dataArr['price_old']) ? 1 : 0;
        if($dataArr['price_old'] == 1){
            $dataArr['price_adult'] = 500000;
            $dataArr['price_child'] = 185000;
        }else{
            $dataArr['price_adult'] = 550000;
            $dataArr['price_child'] = 275000;
        }
        $dataArr['price_cable_adult'] = 390000;
        $dataArr['price_cable_child'] = 255000;

        $dataArr['is_grandworld'] = isset($dataArr['is_grandworld']) ? 1 : 0;
        $dataArr['updated_user'] = Auth::user()->id;

        if(isset($dataArr['cap_nl'])){
            $dataArr['ko_cap_treo'] = (isset($dataArr['ko_cap_treo']) || ($dataArr['cap_nl'] == 0 && $dataArr['cap_te'] == 0)) ? 1 : 0;
        }

        //gui cty khac
        if(isset($dataArr['cty_send']) && $dataArr['cty_send'] > 0){
            $dataArr['is_send'] = 1;
        }
        if(!in_array($dataArr['tour_id'], [1, 8, 23, 24])){ // check ko phải tour đảo
            $dataArr['cap_nl'] = $dataArr['cap_te'] = 0;
            $dataArr['ko_cap_treo'] = 1;
        }
        $model->update($dataArr);


        $booking_id = $dataArr['id'];

        if(!empty($dataArr['related_id'])){
            $this->storeRelated($booking_id, $dataArr['related_id']);
        }
        //luu don tien mien phi
        if($dataArr['don_location_id'] && $dataArr['don_car_cate_id'] && $dataArr['don_ngay']){

            $tmpDate = explode('/', $dataArr['don_ngay']);
            $don_ngay = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
            $detailBooking = Booking::find($booking_id);
            $tmpArr = [
                'booking_id' => $booking_id,
                'location_id' => $dataArr['tien_location_id'],
                'location_id_2' => $dataArr['tien_location_id_2'],
                'type' => 1, // don
                'use_date' => $don_ngay,
                'car_cate_id' => $dataArr['don_car_cate_id'],
                'use_time' => $dataArr['don_gio'].":".$dataArr['don_phut'],
                'notes' => $dataArr['don_ghichu'],
                'use_date_time' => $don_ngay." ".$dataArr['don_gio'].":".$dataArr['don_phut'].":00",
                'user_id' => $detailBooking->user_id,
                'phone' => $dataArr['phone']
            ];
            if($dataArr['don_id'] > 0){
                $rsDon = DonTienFree::find($dataArr['don_id']);

                $oldDataDon = $rsDon->toArray();
                $rsDon->update($tmpArr);

                //write logs
                unset($dataArr['_token']);
                $contentDiff = array_diff_assoc($tmpArr, $oldDataDon);
                //dd($contentDiff);
                if(!empty($contentDiff)){
                    $oldContent = [];

                    foreach($contentDiff as $k => $v){
                        if(isset($oldDataDon[$k])){
                            $oldContent[$k] = $oldDataDon[$k];
                        }
                    }
                    Logs::create([
                        'table_name' => 'don_tien_free',
                        'user_id' => Auth::user()->id,
                        'action' => 2,
                        'content' => json_encode($contentDiff),
                        'old_content' => json_encode($oldContent),
                        'object_id' => $rsDon->id
                    ]);
                }

            }else{
                $rs = DonTienFree::create($tmpArr);
                unset($dataArr['_token']);
                Logs::create([
                    'table_name' => 'don_tien_free',
                    'user_id' => Auth::user()->id,
                    'action' => 1,
                    'content' => json_encode($tmpArr),
                    'object_id' => $rs->id
                ]);
            }

        }
        if($dataArr['tien_location_id'] && $dataArr['tien_car_cate_id'] && $dataArr['tien_ngay']){
            $tmpDate = explode('/', $dataArr['tien_ngay']);
            $tien_ngay = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
            $detailBooking = Booking::find($booking_id);
            $tmpArr = [
                'booking_id' => $booking_id,
                'location_id' => $dataArr['tien_location_id'],
                'location_id_2' => $dataArr['tien_location_id_2'],
                'type' => 2, // don
                'use_date' => $tien_ngay,
                'car_cate_id' => $dataArr['tien_car_cate_id'],
                'use_time' => $dataArr['tien_gio'].":".$dataArr['tien_phut'],
                'notes' => $dataArr['tien_ghichu'],
                'use_date_time' => $tien_ngay." ".$dataArr['tien_gio'].":".$dataArr['tien_phut'].":00",
                'user_id' => $detailBooking->user_id,
                'phone' => $dataArr['phone']
            ];
            if($dataArr['tien_id'] > 0){
                $rsTien = DonTienFree::find($dataArr['tien_id']);

                $oldDataTien = $rsTien->toArray();
                $rsTien->update($tmpArr);

                //write logs
                unset($dataArr['_token']);
                $contentDiff = array_diff_assoc($tmpArr, $oldDataTien);
                //dd($contentDiff);
                if(!empty($contentDiff)){
                    $oldContent = [];

                    foreach($contentDiff as $k => $v){
                        if(isset($oldDataTien[$k])){
                            $oldContent[$k] = $oldDataTien[$k];
                        }
                    }
                    Logs::create([
                        'table_name' => 'don_tien_free',
                        'user_id' => Auth::user()->id,
                        'action' => 2,
                        'content' => json_encode($contentDiff),
                        'old_content' => json_encode($oldContent),
                        'object_id' => $rsTien->id
                    ]);
                }
            }else{
                $rs = DonTienFree::create($tmpArr);
                unset($dataArr['_token']);
                Logs::create([
                    'table_name' => 'don_tien_free',
                    'user_id' => Auth::user()->id,
                    'action' => 1,
                    'content' => json_encode($tmpArr),
                    'object_id' => $rs->id
                ]);
            }
        }

        // GrandworldSchedule::where('booking_id', $dataArr['id'])->delete();
        // if($dataArr['is_grandworld'] == 1 && $grandworld_date){
        //     GrandworldSchedule::create([
        //         'date_book' => $grandworld_date,
        //         'status' => 1,
        //         'booking_id' => $dataArr['id'],
        //         'adults' => $dataArr['adults'],
        //         'childs' => $dataArr['childs']
        //     ]);
        // }
        //ghi log
        // $unsetArr = ['don_id', 'don_location_id', 'don_car_cate_id', 'don_ngay', 'don_gio', 'don_phut',
        // 'don_ghichu', 'tien_id', 'tien_location_id', 'tien_car_cate_id', 'tien_ngay', 'tien_gio', 'tien_phut', 'tien_ghichu', 'don_location_id_2', 'tien_location_id_2', 'related_id', 'ngay_coc', 'hdv_id'];

        // foreach($unsetArr as $value){
        //     unset($dataArr[$value]);
        // }
        $contentDiff = array_diff_assoc($dataArr, $oldDataBooking);

        $booking_id = $model->id;
        if(!empty($contentDiff)){
            $oldContent = [];

            foreach($contentDiff as $k => $v){
                $oldContent[$k] = isset($oldDataBooking[$k]) ? $oldDataBooking[$k] : null;
            }

            //store log
            $rsLog = new BookingLogs([
                'booking_id' => $booking_id,
                'content' =>json_encode(['old' => $oldContent, 'new' => $contentDiff]),
                'action' => 2,
                'user_id' => $user->id
            ]);
            $rsLog->notes = Helper::parseLog($rsLog, false);
            $rsLog->save();

            //Send notifications
            $this->sendBookingNotificationToUser($model, $rsLog, $request->user()->id);
        }

        Session::flash('message', 'Cập nhật thành công');
        //var_dump($use_date, date('d/m/Y', strtotime('tomorrow')))  ;die;
        // if($use_date  == date('d/m/Y', strtotime('tomorrow'))
        //     || $use_date  == date('d/m/Y', time())
        // ){
        //     $this->curlExport();
        // }
        if($model->tour_id == 4){
            return redirect()->route('booking.index', ['type' => $dataArr['type'], 'range_date' => $use_date, 'user_id' => $dataArr['user_id'], 'tour_id' => 4]);
        }else{
            return redirect()->route('booking.index', ['type' => $dataArr['type'], 'range_date' => $use_date, 'user_id' => $dataArr['user_id'], 'tour_type[]' => $dataArr['tour_type'], 'tour_id' => $dataArr['tour_id']]);
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
        $model = Booking::find($id);
        $use_date = date('d/m/Y', strtotime($model->use_date));
        $type = $model->type;

        // redirect
        // luu log
        $oldData = ['status' => $model->status];
        $dataArr = ['status' => 0];

        $model->update(['status' => 0]);

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
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('booking.index', ['type' => $type, 'range_date' => $use_date, 'tour_id' => $model->tour_id]);
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


    public function getConfirmNop(Request $request){
        $str_id = $request->str_id;
        $dt = $request->dt;
        $tmp = explode(',', $str_id);
        $total_money = 0;
        $arrCost = [];
        if(!empty($tmp)){
            $code_ung_tien = Str::upper(Str::random(7));

            foreach($tmp as $booking_id){
                if($booking_id > 0){
                    $tong_tien = 0;
                    $detail = Booking::find($booking_id);
                    if($detail->level == 2){
                        if($detail->nguoi_thu_coc == 4){
                            $tong_tien += $detail->tien_coc;
                        }
                        if($detail->nguoi_thu_tien == 4){
                            $tong_tien += $detail->tien_thuc_thu;
                        }
                        $arrCost[$booking_id]['tien_thuc_thu'] = $tong_tien;
                    }else{
                        $arrCost[$booking_id]['tien_thuc_thu'] = $detail->tien_thuc_thu;
                    }

                    $arrCost[$booking_id]['code'] = Helper::showCode($detail);
                }
            }
        }
        return view('booking.ajax-confirm', compact('arrCost', 'dt'));

    }
    public function getContentNop(Request $request){
        $str_id = $request->str_id;
        $tmp = explode(',', $str_id);
        $total_money = 0;

        if(!empty($tmp)){
            $code_nop_tien = Str::upper(Str::random(7));
            foreach($tmp as $booking_id){
                if($booking_id > 0){
                    $tong_tien = 0;
                    $checkCode = CodeNopTien::where('booking_id', $booking_id)->where('status', 1)->first();

                    if(!$checkCode){
                        $detail = Booking::find($booking_id);
                        if($detail->level == 2){
                            if($detail->nguoi_thu_coc == 4){
                                $tong_tien += $detail->tien_coc;
                            }
                            if($detail->nguoi_thu_tien == 4){
                                $tong_tien += $detail->tien_thuc_thu;
                            }

                        }else{
                            $tong_tien += $detail->tien_thuc_thu;
                        }
                        if($tong_tien > 0){
                            CodeNopTien::create(['code' => $code_nop_tien, 'amount' => $tong_tien, 'booking_id' => $booking_id]);
                        }
                    }

                    $total_money += $tong_tien;
                }
            }
            if($total_money > 0){
                return "ND: DPS ".$code_nop_tien. " ". $total_money;

            }else{
                return "Đã có lỗi xảy ra!!!";
            }

        }else{
            return "CHƯA CHỌN MỤC NÀO!!!";
        }

    }

    public function getContentNopDoiTac(Request $request){
        $str_id = $request->str_id;
        $tmp = explode(',', $str_id);
        $total_money = 0;

        if(!empty($tmp)){
            $code_nop_tien_dt = Str::upper(Str::random(7));
            foreach($tmp as $booking_id){
                if($booking_id > 0){
                    $tong_tien = 0;
                    $detail = Booking::find($booking_id);

                    if(!$detail->code_nop_tien_dt){

                        if($detail->nguoi_thu_coc == 4){
                            $tong_tien += $detail->tien_coc;
                        }
                        if($detail->nguoi_thu_tien == 4){
                            $tong_tien += $detail->tien_thuc_thu;
                        }

                        if($tong_tien > 0){
                            $detail->update([
                                'code_nop_tien_dt' => $code_nop_tien_dt,
                                'time_code_nop_tien_dt' => date('Y-m-d H:i:s')
                            ]);
                        }

                    }

                    $total_money += $tong_tien;
                }
            }
            if($total_money > 0){
                return "ND: DPDT ".$code_nop_tien_dt. " ". $total_money;

            }else{
                return "Đã có lỗi xảy ra!!!";
            }

        }else{
            return "CHƯA CHỌN MỤC NÀO!!!";
        }

    }
    public function getConfirmChi(Request $request){
        $str_id = $request->str_id;
        $tmp = explode(',', $str_id);
        $total_money = 0;
        $arrBooking = [];
        if(!empty($tmp)){
            foreach($tmp as $booking_id){
                if($booking_id > 0){
                    $arrBooking[$booking_id] = Booking::find($booking_id)->hoa_hong_sales;
                }
            }
        }
        return view('booking.ajax-confirm-chi', compact('arrBooking'));

    }
    public function getContentChi(Request $request){
        $str_id = $request->str_id;
        $tmp = explode(',', $str_id);
        $total_money = 0;
        $arrCost = [];
        if(!empty($tmp)){
            $code_chi_tien = Str::upper(Str::random(7));

            foreach($tmp as $booking_id){
                if($booking_id > 0){
                    $detail = Booking::find($booking_id);
                    if(!$detail->code_chi_tien){
                        $total_money += $detail->total_money;
                        $detail->update([
                            'code_chi_tien' => $code_chi_tien,
                            'time_code_chi_tien' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
            if($total_money > 0){
                return "ND: EXP ".$code_chi_tien. " ". $total_money;

            }else{
                return "Đã có lỗi xảy ra!!!";
            }

        }else{
            return "CHƯA CHỌN MỤC NÀO!!!";
        }

    }

    public static function storeRelated($booking_id, $relatedIdArr){
        //delete
        BookingRelated::where(function ($query) use ($booking_id) {
                $query->where(['booking_id' => $booking_id])
                      ->orWhere(['related_id' => $booking_id]);
            })->delete();
        foreach($relatedIdArr as $related_id){
            $check = BookingRelated::where(function ($query) use ($booking_id, $related_id) {
                $query->where(['booking_id' => $booking_id, 'related_id' => $related_id])
                      ->orWhere(['booking_id' => $related_id, 'related_id' => $booking_id]);
            })->first();
            if(!$check){
                BookingRelated::create(['booking_id'=> $booking_id, 'related_id' => $related_id]);
            }
        }
    }

    public function createNotes(Request $request){
        if($request->booking_id && $request->comments){
            $bookingNote = new BookingNotes([
                'booking_id' => $request->booking_id,
                'content' => $request->comments,
                'user_id' => Auth::user()->id,
                'status' => 1,
                'action' => 4
            ]);
            $bookingNote->save();
            return redirect()->back()->with('comment_success', 'Thêm ghi chú thành công!')->with('hash', 'comment');
        }
        return redirect()->back()->with('comment_error', 'Thêm ghi chú không thành công!');
    }

    private function sendBookingNotificationToUser($booking, $bookingLog, $updateUserId, $customTitle = null, $customContent = null, $extraUserIds = null)
    {
        $notificationUsers = $this->getNotificationUsers($booking, $updateUserId, $extraUserIds);
        $title = $bookingLog->action == 1 ? sprintf('PTT%s đã được tạo!', $booking->id) : sprintf('PTT%s đã được cập nhật!', $booking->id);
        if (!empty($customTitle)) {
            $title = $customTitle;
        }
        if (!empty($customContent)) {
            $content = $customContent;
        } else {
            $content = Helper::parseLog($bookingLog);
        }
        foreach ($notificationUsers as $user) {
            $this->notificationService->pushNotification($user, $title, $content, ['booking_id' => $booking->id]);
        }
    }

    private function getNotificationUsers($booking, $updateUserId, $extraUserIds)
    {
        $userIds = $this->getUserIdPushNoti($booking, 1);
        if(!empty($extraUserIds)){
            $userIds = array_merge($userIds, $extraUserIds);
        }
        return User::whereIn('id', $userIds)->get();
    }

    /**
     * @param $booking_id
     * @param int $type (1 = normal,  2 = only for sales)
     * @return array
     */
    public function getUserIdPushNoti($detail, $type = 1): array
    {
        //$type = 1 normal, type = 2 private for sales
        $role = Auth::user()->role;
        if ($type == 1) {
            if (Auth::user()->is_cskh){ //CSKH thi push cho dieu hanh (60), sale, hdv
                $userIdPush = [60, 84, $detail->user_id, $detail->hdv_id];
            } elseif ($role == 1 || $role == 2) {
                //admin thi push cho dieu hanh, sale, hdv
                $userIdPush = [60, 84, $detail->user_id, $detail->hdv_id];
            } elseif ($role == 3) {
                //dieu hanh thi push cho admin, sale, hdv
                $userIdPush = [1, 60, 84, $detail->user_id, $detail->hdv_id];
            } elseif ($role == 4) {
                //sales thi push cho admin, dieu hanh, hdv
                $userIdPush = [1, 60, 84, $detail->hdv_id];
            } elseif ($role == 5) {
                //sales thi push cho admin, dieu hanh, hdv
                $userIdPush = [1, 60, 84, $detail->user_id];
            }
        } else {
            if ($role == 1 || $role == 2) {
                //admin thi push cho dieu hanh, sale, hdv
                $userIdPush = [$detail->user_id];
            } elseif ($role == 3) {
                //dieu hanh thi push cho admin, sale, hdv
                $userIdPush = [1, $detail->user_id];
            }
        }

        //Gửi thông báo cho CSKH nếu ngày đi là hôm nay hoặc ngày mai
        if(!Auth::user()->is_cskh){
            $cskh = User::where('is_cskh', 1)->get();
            $useDate = new Carbon($detail->use_date);
            if($useDate->isToday() || $useDate->isTomorrow()){
                foreach ($cskh as $user){
                    $userIdPush[] = $user->id;
                }
            }
        }

        return $userIdPush;
    }

    public function insurance(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            abort(404);
        }

        $bookings = Booking::whereIn('id', $ids)->get();
        if ($bookings->isEmpty()) {
            abort(404);
        }
        return view('booking.insurance-preview', compact('bookings'));
    }

    public function insurancePdf(Request $request)
    {
        $ids = $request->ids;
        $bookings = Booking::whereIn('id', $ids)->get();
        if ($bookings->isEmpty()) {
            abort(404);
        }
        if ($bookings->pluck('customers')->flatten()->pluck('id')->isEmpty()) {
            Session::flash('message', 'Không có khách hàng để xuất pdf!');
            return back();
        }
        $pdf = PDF::loadView('booking.insurance', compact('bookings'));
        return $pdf->download(date('Y-m-d').'-Bảng danh sách bảo hểm.pdf');
    }

    public function insuranceMail(Request $request)
    {
        ini_set('max_execution_time', 300);
        $ids = $request->ids;

        $bookings = Booking::whereIn('id', $ids)->get();
        if ($bookings->isEmpty()) {
            abort(404);
        }
        if ($bookings->pluck('customers')->flatten()->pluck('id')->isEmpty()) {
            Session::flash('message', 'Không có khách hàng để gửi mail!');
            return back();
        }

        $directory = 'email_pdf';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        try {
            $fileName = '/insurance_' . time() . '.pdf';
            $path = $directory . $fileName;
            $fullPath = Storage::path($directory) . $fileName;

            $pdf = PDF::loadView('booking.insurance', compact('bookings'))->setPaper('a3', 'portrait');
            $pdf->save($fullPath);

            //Send email with attachment
            $email = 'baovietphuquoc.bhso2@gmail.com';
            Mail::send('mail.insurance', compact('bookings'), function ($message) use ($email, $fullPath) {
                $message->to($email, 'Bảo Việt Phú Quốc')->subject('Plan To Travel gửi danh sách bảo hiểm ngày ' . date('d/m/Y'));
                $message->attach($fullPath);
                $message->from(env('MAIL_FROM_ADDRESS'));
            });

            if (Storage::exists($path)) {
                Storage::delete($path);
            }
            Session::flash('message', 'Gửi mail thành công');
        } catch (\Exception $e) {
            Session::flash('message', $e->getMessage());
        }
        return back();
    }
}
