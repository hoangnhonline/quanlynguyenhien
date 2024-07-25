<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth, Mail;
use App\Models\Booking;
use App\Models\BookingLogs;
use App\Models\Hotels;
use App\Models\UserNotification;
use App\Models\TaskDetail;
use App\Models\Account;
use App\Models\Ctv;
use App\Models\TourSystem;
use App\Models\GrandworldSchedule;
use Jenssegers\Agent\Agent;
use App\Models\Partner;
use App\User;
use Session, Hash, Helper;

class HomeController extends Controller
{

    private $_minDateKey = 0;
    private $_maxDateKey = 1;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function dashboard(Request $request){

       if(Auth::user()->id == 510){
            return redirect()->route('task-detail.index');
        } // HR
        $user_id = Auth::user()->id;
        //dd(Auth::user()->is_tour);
        if( $user_id == 21 || $user_id == 22 || $user_id == 23 || (Auth::user()->is_tour == 1 && !in_array($user_id,[32, 33, 41, 58, 65, 76, 125])) ){

            return redirect()->route('booking.index');
        }
        //ve cap treo Thuy Le
        if(Auth::user()->role == 5){
            return redirect()->route('ticket.manage');
        }
       // dd(in_array($user_id, [32, 33, 41, 58, 65, 76, 125]));
        if(in_array($user_id, [32, 33, 41, 58, 65, 76, 125])){
          return redirect()->route('media.diem-danh');
        }
        $rsTour = Booking::whereRaw('(use_date = "'
            .date('Y-m-d').'" OR checkin = "'.date('Y-m-d').'")')
            ->where('status', 1);
        $totalGrand = GrandworldSchedule::where('date_book', date('Y-m-d'))->where('status', 1)->sum('adults');

        if(Auth::user()->role != 1){
            $rsTour->where('user_id', $user_id);
        }
        $tours = $rsTour->get();
       //dd($tours);
        $allTour = $allHotel = $allTicket = $allCar = [];
        foreach($tours as $tour){
            if($tour->type == 1){
                $allTour[] = $tour;
            }elseif($tour->type == 2){
                $allHotel[] = $tour;
            }elseif($tour->type == 4){
                $allCar[] = $tour;
            }else{
                $allTicket[] = $tour;
            }
        }
        $taskCount = TaskDetail::where('task_date', date('Y-m-d'))->get()->count();
        $nvCount = Account::where('is_staff', 1)->get()->count();


        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y');

        //report
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        $query = Booking::where('type', 1)->where('status', '<', 3);
        if(Auth::user()->role != 1){
            $query->where('user_id', $user_id);
        }

        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => today
            $rangeDate = Carbon::now();
            $query->where('use_date','=', $rangeDate->format('Y-m-d'));
            $time_type = 3;
            $month = $rangeDate->format('m');
            $year = $rangeDate->year;
        } elseif (count($rangeDate) === 1) {
            //case page is initialized and range_date has value,
            //when counting the number of elements in rangeDate = 1 => only select a day
            $use_date = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey]);
            $query->where('use_date','=', $use_date->format('Y-m-d'));
            $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
            $time_type = 3;
            $month = $use_date->format('m');
            $year = $use_date->year;
        } else {
            $query->where('use_date','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $query->where('use_date', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
            $time_type = 1;
            $month = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('m');
            $year = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->year;
        }

         $level = $request->level ?? null;
        if($level){
            $query->where('level', $level);
            $query->whereIn('tour_type', [1,2]);
        }
        $items = $query->orderBy('tour_id')->get();
        //dd($items);
        $arrResult = [];
        $arrTourByCate = [];
        foreach($items as $item){
            $day = date('d/m', strtotime($item->use_date));
            if(!isset($arrResult[$day])){
                $arrResult[$day] = [];
            }
            if(!isset($arrResult[$day][$item->tour_type])){
                $arrResult[$day][$item->tour_type] = 0;
            }
            if($item->tour_type == 1){
                $arrResult[$day][$item->tour_type] += $item->adults;
            }else{
                $arrResult[$day][$item->tour_type]++;
            }
            if(!isset($arrResult[$day]['meals'])){
                $arrResult[$day]['meals'] = 0;
            }
            $arrResult[$day]['meals'] += $item->meals + $item->meals_te;

            if(!isset($arrTourByCate[$item->tour_id][$item->tour_type])){
                $arrTourByCate[$item->tour_id][$item->tour_type]['adults'] = 0;
                $arrTourByCate[$item->tour_id][$item->tour_type]['childs'] = 0;
                $arrTourByCate[$item->tour_id][$item->tour_type]['infants'] = 0;
                $arrTourByCate[$item->tour_id][$item->tour_type]['total'] = 0;
            }
            $arrTourByCate[$item->tour_id][$item->tour_type]['adults'] += $item->adults;
            $arrTourByCate[$item->tour_id][$item->tour_type]['childs'] += $item->childs;
            $arrTourByCate[$item->tour_id][$item->tour_type]['infants'] += $item->infants;
            $arrTourByCate[$item->tour_id][$item->tour_type]['total'] += 1;
        }

        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $level = $request->level ?? null;
        $id_loaitru = $request->id_loaitru ?? null;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";

        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $query = Booking::where('type', 1)->where('status', '<', 3);
        if($level){
            $query->where('level', $level);
            $query->whereIn('tour_type', [1,2]);
        }
        if($id_loaitru){
            $arrLoaiTru = explode(',', $id_loaitru);
            $query->whereNotIn('user_id', $arrLoaiTru);
        }


        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => today
            $rangeDate = Carbon::now();
            $query->where('use_date','=', $rangeDate->format('Y-m-d'));
            $time_type = 3;
            $month = $rangeDate->format('m');
            $year = $rangeDate->year;
        } elseif (count($rangeDate) === 1) {
            //case page is initialized and range_date has value,
            //when counting the number of elements in rangeDate = 1 => only select a day
            $use_date = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey]);
            $query->where('use_date','=', $use_date->format('Y-m-d'));
            $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
            $time_type = 3;
            $month = $use_date->format('m');
            $year = $use_date->year;
        } else {
            $query->where('use_date','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $query->where('use_date', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
            $time_type = 1;
            $month = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('m');
            $year = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->year;
        }

        $items = $query->get();

        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }
        $arrLevel = [];
        $arrByDay = [];
        foreach($items as $item){
            $level_key = isset($arrUser[$item->user_id]) ? $arrUser[$item->user_id]->level : 0;

            // report tháng
            if($time_type != 3){
                if(!isset($arrByDay[$item->level][$item->use_date])){
                    $arrByDay[$item->level][$item->use_date][1] = 0;
                    $arrByDay[$item->level][$item->use_date][2] = 0;
                    $arrByDay[$item->level][$item->use_date][3] = 0;
                }else{
                    if($item->tour_type == 3){
                        $arrByDay[$item->level][$item->use_date][3] += 1;
                    }else{
                        $arrByDay[$item->level][$item->use_date][$item->tour_type] += $item->adults;
                    }
                }
            }



            // kiểm tra chưa có thì khởi tạo
            if(!isset($arrLevel[$level_key])){
                if($item->tour_type == 3){ // thuê cano thì +1
                    $arrLevel[$level_key] = 1;
                }else{ // tour ghép + vip thì +adults
                    $arrLevel[$level_key] = $item->adults;
                }
            }else{ // đã có thì cập nhật
                if($item->tour_type == 3){
                    $arrLevel[$level_key] += 1;
                }else{
                    $arrLevel[$level_key] += $item->adults;
                }

            }

        }
        $tourSystem = TourSystem::pluck('name', 'id')->toArray();

        //dd($level);
        $maxDay = date('d', strtotime($maxdate));
         $agent = new Agent();
        if($agent->isMobile()){
            $view = 'm-dashboard';
        }else{
            $view = 'dashboard';
        }
       // dd($arrTourByCate);
        return view($view, compact('allTour', 'allHotel', 'allTicket', 'allCar', 'taskCount', 'nvCount','month', 'year', 'time_type', 'arrSearch', 'arrResult', 'totalGrand', 'arrByDay', 'maxDay', 'level', 'tourSystem', 'arrTourByCate'));
    }
    public function mailPreview(Request $request){

        $id = $request->id;
        $tour_id = $request->tour_id ?? null;

        $detail = Booking::find($id);
        if($detail->mail_hotel == 1){
            die('Đã gửi mail book phòng');
        }
        $userDetail = User::find($detail->user_id);
        if($tour_id == 4){
            return view('mail-preview.tour', compact('detail', 'userDetail'));
        }else{
            $detailHotel = Hotels::find($detail->hotel_id);

            if($detail->hotel_id == 31 || $detail->hotel_id == 35){
                return view('mail-preview.hotel-vin', compact('detail', 'detailHotel', 'userDetail'));
            }else{
                return view('mail-preview.hotel', compact('detail', 'detailHotel', 'userDetail'));
            }
        }



    }
    public function mailConfirm(Request $request){

        $id = $request->id;
        $detail = Booking::find($id);
        if($detail->mail_customer == 1){
            die('Đã gửi mail cho khách');
        }
        $detailHotel = Hotels::find($detail->hotel_id);
        $userDetail = User::find($detail->user_id);

        return view('mail-confirm', compact('detail', 'detailHotel', 'userDetail'));

    }

    public function saveHoaHong(Request $request){
        $user = Auth::user();
        $id = $request->id;
        $hoa_hong_sales = $request->hoa_hong_sales;
        $hoa_hong_sales = (int) str_replace(',', '', $hoa_hong_sales);
        // if(strlen($hoa_hong_sales) <= 4){
        //     $hoa_hong_sales = $hoa_hong_sales."000";
        // }
        $detail = Booking::find($id);

        // luu log
        $oldData = ['hoa_hong_sales' => $detail->hoa_hong_sales, 'status' => $detail->status];
        $dataArr = ['hoa_hong_sales' => $hoa_hong_sales, 'status' => 2];
        $contentDiff = array_diff_assoc($dataArr, $oldData);

        if(!empty($contentDiff)){
            $oldContent = [];

            foreach($contentDiff as $k => $v){
                $oldContent[$k] = $oldData[$k];
            }
            $rsLog = BookingLogs::create([
                'booking_id' =>  $id,
                'content' =>json_encode(['old' => $oldContent, 'new' => $contentDiff]),
                'action' => 3, // ajax hoa hong
                'user_id' => Auth::user()->id
            ]);
            // push notification
           // dd($rs);
           // $userIdPush = Helper::getUserIdPushNoti($id, 1);
          // dd($userIdPush);
            // foreach($userIdPush as $idPush){
            //     if($idPush > 0){
            //         UserNotification::create([
            //             'title' => 'Hoa hồng PTT'.$id.' vừa được '. $user->name." cập nhật",
            //             'content' => Helper::parseLog($rsLog),
            //             'user_id' => $idPush,
            //             'booking_id' => $id,
            //             'date_use' => $detail->use_date,
            //             //'data' => json_encode($dataArr),
            //             'type' => 2, // tinh hoa hong
            //             'is_read' => 0
            //         ]);
            //     }
            // }
        }
        // cap nhat
        $detail->update($dataArr);
    }
    public function bookPhong(Request $request){
        $id = $request->id;
        $detail = Booking::find($id);
         if($detail->mail_hotel == 1){
            die('Đã gửi mail book phòng');
        }
        //dd($detail->rooms);

        $detailHotel = Hotels::find($detail->hotel_id);
        $userDetail = User::find($detail->user_id);
        if($detail->hotel_book > 0){
            $detailBook = Partner::find($detail->hotel_book);
            $tmpEmail = explode(';', $detailBook->email);
        }else{
            $tmpEmail = explode(';', $detailHotel->email);
        }
        $emailArr = (array) $tmpEmail[0];
        $emailCC = array_slice($tmpEmail, 1);
        $arrCtvPhung = [305, 306, 307, 308, 309, 310, 311, 312, 313];
        if($detail->ctv_id > 0){
            $detailCtv = Ctv::find($detail->ctv_id);
            // cc cho email ctv
            if(in_array($detailCtv->id, $arrCtvPhung)){
                $emailCC[] = 'phungtravel1988@gmail.com';
                $emailCC[] = $detailCtv->email;
            }else{
                $emailCC[] = $detailCtv->email;
            }
        }
       // dd($emailCC);
         // cc cho email chinh
        $emailCC[] = $userDetail->email;
        // neu email booking ko trung với email user thì cc email trong booking
        // if($detail->email != $userDetail->email){
        //     $emailCC[] = $detail->email;
        // }

        if($userDetail->email == 'phungtravel1988@gmail.com'){
            $emailCC[] = "tunganphung88@gmail.com";
        }

        $emailCC[] = 'acc@plantotravel.vn';

        if ($detail->hotel_id == 31 || $detail->hotel_id == 35) {
            Mail::send('mail.mail-hotel-vin',
            [
                'detail'             => $detail,
                'detailHotel' => $detailHotel
            ],
            function($message) use ($detail, $emailArr, $emailCC, $detailHotel) {

                 $title = $detail->name." - ";
                $title .= date('d/m/Y', strtotime($detail->checkin))." - ".date('d/m/Y', strtotime($detail->checkout))." - VIN 5 SAO PHÚ QUÔC";



                $message->subject($title);
                $message->to($emailArr);
                $message->cc($emailCC);
                //$message->replyTo('', $dataArr['full_name']);
                $message->from('booking@plantotravel.vn', 'Plan To Travel');
                $message->sender('booking@plantotravel.vn', 'Plan To Travel');
        });
        }else{
            Mail::send('mail.mail-hotel',
            [
                'detail'             => $detail,
                'detailHotel' => $detailHotel,
                'userDetail' => $userDetail
            ],
            function($message) use ($detail, $emailArr, $emailCC, $detailHotel, $userDetail) {
                //if($detailHotel->id == 23 || $detailHotel->id == 39){
                    $title = $detailHotel->name. "/".$detail->name."/".date('d.m', strtotime($detail->checkin))."-".date('d.m.y', strtotime($detail->checkout))."-".$detail->phone;
                // }else{
                //     $title = 'Plan To Travel gửi yêu cầu đặt phòng ';
                //     $title .= date('d/m/Y', strtotime($detail->checkin))." - ".$detail->name." - ".$detail->phone;
                // }

                $message->subject($title);
                $message->to($emailArr);
                $message->cc($emailCC);
                //$message->replyTo('', $dataArr['full_name']);
                $message->from('booking@plantotravel.vn', 'Plan To Travel');
                $message->sender('booking@plantotravel.vn', 'Plan To Travel');
        });
        }

        $detail->update(['mail_hotel' => 1]);
        Session::flash('message', 'Gửi mail book phòng thành công');
        return redirect()->route('booking-hotel.index', ['book_date' => date('d/m/Y', strtotime($detail->book_date)), 'hotel_id' => $detail->hotel_id]);
    }
    public function bookTourCauMuc(Request $request){
        $id = $request->id;
        $detail = Booking::find($id);
         if($detail->mail_hotel == 1){
            die('Đã gửi mail book tour');
        }
        //dd($detail->rooms);

        $userDetail = User::find($detail->user_id);

        $emailArr = ['salemanager.johnstours@phuquoctrip.com'];

        $emailCC[] = $userDetail->email;

        $emailCC[] = 'acc@plantotravel.vn';

        $emailCC[] = 'nhungoc@plantotravel.vn';

            Mail::send('mail.tour-cau-muc',
            [
                'detail'             => $detail,
                'userDetail' => $userDetail
            ],
            function($message) use ($detail, $emailArr, $emailCC, $userDetail) {

                $title = 'Plan To Travel gửi yêu cầu đặt tour câu mực ';
                $title .= date('d/m/Y', strtotime($detail->use_date))." - ".$detail->name." - ".$detail->phone;

                $message->subject($title);
                $message->to($emailArr);
                $message->cc($emailCC);
                //$message->replyTo('', $dataArr['full_name']);
                $message->from('booking@plantotravel.vn', 'Plan To Travel');
                $message->sender('booking@plantotravel.vn', 'Plan To Travel');
        });


        $detail->update(['mail_hotel' => 1]);
        Session::flash('message', 'Gửi mail book tour câu mực thành công');
        return redirect()->route('booking.index', ['type'=> 1, 'use_date_from' => date('d/m/Y', strtotime($detail->use_date)), 'tour_id' => 4]);
    }
    public function confirmPhong(Request $request){
        $id = $request->id;
        $detail = Booking::find($id);
         if($detail->mail_customer == 1){
            die('Đã gửi mail book phòng');
        }
        //dd($detail->rooms);

        $detailHotel = Hotels::find($detail->hotel_id);
        $userDetail = User::find($detail->user_id);
        //$emailArr = [$detail->email, $userDetail->email];
        $emailArr = [$detail->email, $userDetail->email];
        //dd($userDetail);
        //return view('mail', compact('detail'));
        Mail::send('confirm',
            [
                'detail'             => $detail,
                'detailHotel' => $detailHotel
            ],
            function($message) use ($detail, $detailHotel, $emailArr) {
                $title = 'Plan To Travel gửi xác nhận đặt phòng tại '.$detailHotel->name." ngày ";
                $title .= date('d/m/Y', strtotime($detail->checkin));

                $message->subject($title);
                $message->to($emailArr);
                //$message->replyTo('', $dataArr['full_name']);
                $message->from('booking@plantotravel.vn', 'Plan To Travel');
                $message->sender('booking@plantotravel.vn', 'Plan To Travel');
        });
        $detail->update(['mail_customer' => 1]);
        Session::flash('message', 'Gửi mail book phòng thành công');
        return redirect()->route('booking-hotel.index', ['book_date' => date('d/m/Y', strtotime($detail->book_date))]);
    }

    public function testMail(){
        $detail = 1;
        Mail::send('test-mail',
            [                   
                'detail'             => 1,                
            ],
            function($message) use ($detail) {   
                $title = 'Test mail Plan To Travel';                        

                $message->subject($title);
                $message->to(['ceo@plantotravel.vn']);
                //$message->replyTo('', $dataArr['full_name']);
                $message->from('booking@plantotravel.vn', 'Plan To Travel');
                $message->sender('booking@plantotravel.vn', 'Plan To Travel');
        });
    }
}
