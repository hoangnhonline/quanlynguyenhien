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
        $user_id = Auth::user()->id;
       
        $rsTour = Booking::whereRaw('use_date = "'
            .date('Y-m-d').'" ')
            ->where('status', 1);
        $totalGrand = 0;

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
        $taskCount = 0;
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
    
}
