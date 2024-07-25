<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerSource;
use App\Models\Department;
use App\Models\Task;
use App\Models\TaskDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rating;
use App\Models\Hotels;
use App\Models\BookingRooms;
use App\Models\Partner;
use App\Models\CostType;
use App\Models\Tickets;
use App\Models\Location;
use App\Models\Tour;
use App\Models\Cost;
use App\Models\CarCate;
use App\Models\Drivers;
use App\Models\Revenue;
use App\Models\Debt;
use App\Models\Ctv;
use App\Models\TourSystem;
use App\Models\TicketTypeSystem;
use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash, DB;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserNotification;
use App\Models\BookingLogs;

class ReportController extends Controller
{
    private $_minDateKey = 0;
    private $_maxDateKey = 1;

    public function thuTien(Request $request){
        $id = $request->id;
        $detail = Booking::find($id);
        $newData = [
        'nguoi_thu_tien' => 2,
            'status' => 2
        ];
        $oldData = [
            'nguoi_thu_tien' => $detail->nguoi_thu_tien,
            'status' =>  $detail->status
        ];
        $detail->update($newData);
        $rsLog = BookingLogs::create([
                'booking_id' => $id,
                'content' =>json_encode(['old' => $oldData, 'new' => $newData]),
                'action' => 2,
                'user_id' => Auth::user()->id
            ]);
    }
    public function detailCostByPartner(Request $request){
        $id = $request->id;
        $date_use = $request->date_use;
        $costAll = Cost::where('date_use', $date_use)
                        ->where('status', '>', 0)
                        ->where('partner_id', $id)
                        ->get();
                        //->sum('amount');
        dd($costAll);
    }

    public function dsDoitac(Request $request){
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";

        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        $arrSearch['ctv_id'] = $ctv_id = $request->ctv_id ?? null;
        $arrSearch['level'] = $level = $request->level ? $request->level : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $query = Booking::where('type', 1)->where('status', '<', 3)->where('tour_id', 1)->whereIn('tour_type', [1, 2]);
        if($user_id){
            $query->where('user_id', $user_id);
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
                $query->where('use_date', '<=', $use_date_to_format);
            }
        }else{
            $arrSearch['use_date_from'] = $use_date_from = $arrSearch['use_date_to'] = $use_date_to = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());

            $arrSearch['use_date_from'] = $use_date_from;
            $tmpDate = explode('/', $use_date_from);
            $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
            $query->where('use_date','=', $use_date_from_format);

        }

        if($level && $type == 1){
            $arrSearch['level'] = $level;
            $query->where('level', $level);
        }else{
            $query->whereIn('level', [2, 7]);
        }
        $items = $query->get();
        //dd($items);
        $arrResult = [];
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        $arrUser = [];
        if($level){
            $listUser = User::where('level', $level)->where('status', 1)->get();
        }
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }
        $total_adults = $total_money = 0;
        $arrByDay = [];
        if($items->count() > 0){
            $locationList = [];
            foreach($items as $bk){
                $userArr[$bk->user_id] = $bk->user_id;
                $total_adults += $bk->adults;
                $money = 0;
                if(!$bk->location){
                     $money = $bk->adults*350000;
                    $total_money += $money;
                }else{
                    if($bk->location->is_ben == 1){
                        $money = $bk->adults*250000;
                        $total_money += $money;
                    }else{
                        $money = $bk->adults*350000;
                        $total_money += $money;
                    }
                }
                if(!isset($arrByDay[$bk->use_date])){
                    $arrByDay[$bk->use_date]['total_adults'] = 0;
                    $arrByDay[$bk->use_date]['total_money'] = 0;
                }
                $arrByDay[$bk->use_date]['total_adults'] += $bk->adults;
                $arrByDay[$bk->use_date]['total_money'] += $money;
            }
        }
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
        return view('report.doanh-so-doi-tac', compact('month', 'year', 'listUser', 'arrUser', 'time_type', 'arrSearch', 'arrResult', 'items', 'level', 'ctvList', 'ctv_id', 'arrByDay', 'total_adults', 'total_money'));
    }

    public function hotelRecent(Request $request){
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y');

        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $query = Booking::where('booking.type', 2)->where('booking.status', '<', 3);

        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => this month
            $rangeDate = Carbon::now();
            $query->where('checkin','=', $rangeDate->format('Y-m-d'));
        } elseif (count($rangeDate) === 1) {
            //case page is initialized and range_date has value,
            //when counting the number of elements in rangeDate = 1 => only select a day
            $query->where('checkin','=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
        } else {
            $query->where('checkin','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $query->where('checkin', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
        }

        $query->leftJoin('hotels', 'hotels.id', '=', 'booking.hotel_id');
        $items = $query->select('hotel_id', 'hotels.name', 'checkin',DB::raw('count(hotel_id) as amount_booking'))->groupBy('hotel_id')->orderBy('amount_booking', 'desc')->orderBy('checkin', 'desc')->get();

        //dd($items);

        return view('report.hotel-recent', compact('month', 'year', 'time_type', 'arrSearch', 'items'));
    }

    public function hotelByUser(Request $request){
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $maxDay = date('d', strtotime($maxdate));

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y');

        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 'checkin';
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : -1;
        $arrSearch['level'] = $level = $request->level ? $request->level : null;

        $queryUser = User::where('status', 1);
        if($level){
            $queryUser->where('level', $level);
        }else{
            $queryUser->whereIn('level', [1,2,3,4,5,6,7]);
        }

        $listUser =  $queryUser->get();
        $listUserIds = $listUser->pluck('id');

        $query = Booking::where('booking.type', 2)->where('booking.status', '<', 3)->whereIn('user_id', $listUserIds);

        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => this month
            $rangeDate = Carbon::now();
            $query->where('booking.'.$search_by,'=', $rangeDate->format('Y-m-d'));
        } elseif (count($rangeDate) === 1) {
            //case page is initialized and range_date has value,
            //when counting the number of elements in rangeDate = 1 => only select a day
            $query->where('booking.'.$search_by,'=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
        } else {
            $query->where('booking.'.$search_by,'>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $query->where('booking.'.$search_by, '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
        }

        $query->leftJoin('users', 'users.id', '=', 'booking.user_id');
        if($user_id > 0){
            $query->where('user_id', $user_id);
        }
        if($level){
            $query->where('users.level', $level);
        }
        $items = (clone $query)->select('user_id', 'users.name', 'checkin',DB::raw('sum(hoa_hong_cty) as total'))->groupBy(['user_id', 'users.name', 'checkin'])->orderBy('checkin', 'asc')->get();

        $summary= [];
        $dates = [];
        foreach($items as $item){
            $summary[$item->user_id]['name'] = $item->name;
            $summary[$item->user_id]['total'] = isset($summary[$item->user_id]['total']) ? $summary[$item->user_id]['total'] + $item->total : $item->total;
            $summary[$item->user_id]['detail'][$item->checkin] = $item->total;
            $dates[$item->checkin] = !empty($dates[$item->checkin]) ? $dates[$item->checkin] + $item->total : $item->total;
        }

        $details = [];
        $detailDates = [];
        if($user_id > 0){
            $query->leftJoin('hotels', 'hotels.id', '=', 'booking.hotel_id');
            $query->leftJoin('booking_rooms', 'booking_rooms.booking_id', '=', 'booking.id');
            $detailQuery = $query->select( DB::raw("IFNULL(hotels.stars, 'N/A') as stars"), 'booking.checkin',DB::raw('sum(booking.total_price) as total'),DB::raw('sum(booking_rooms.nights) as nights'),DB::raw('sum(booking.hoa_hong_cty) as hoa_hong'),DB::raw('sum(booking_rooms.room_amount) as room_count'))
                ->groupBy(['stars', 'booking.checkin'])
                ->orderBy('booking.checkin');

            foreach ($detailQuery->get() as $item){
                $details[$item->stars]['total'] = isset($details[$item->stars]['total']) ? $details[$item->stars]['total'] + $item->total : $item->total;
                $details[$item->stars]['hoa_hong'] = isset($details[$item->stars]['hoa_hong']) ? $details[$item->stars]['hoa_hong'] + $item->hoa_hong : $item->hoa_hong;
                $details[$item->stars]['nights'] = isset($details[$item->stars]['nights']) ? $details[$item->stars]['nights'] + $item->nights : $item->nights;
                $details[$item->stars]['room_count'] = isset($details[$item->stars]['room_count']) ? $details[$item->stars]['room_count'] + $item->room_count : $item->room_count;
                $details[$item->stars]['detail'][$item->checkin] = $item;
                if(!in_array($item->checkin, $detailDates)){
                    $detailDates[] = $item->checkin;
                }
            }
            //Sort $details by key stars
            ksort($details);
        }
        return view('report.hotel-by-user', compact('month', 'year', 'time_type', 'arrSearch', 'items', 'summary', 'dates', 'details', 'detailDates', 'listUser', 'user_id', 'level'));
    }
    public function customerByLevel(Request $request){
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : "";
        $time_type = 3;
        $type = $request->type ?? 1;
        $level = $request->level ?? null;
        $id_loaitru = $request->id_loaitru ?? null;

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
        $arrResult = [];
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }
        $arrLevel = [];
        $arrByDay = [];
        foreach($items as $item){
            $level_key = isset($arrUser[$item->user_id]) ? $arrUser[$item->user_id]->level : 0;
            if(!isset($arrResult[$item->user_id])){
                $arrResult[$item->user_id][$item->tour_type] = 0;
            }else{
                if(!isset($arrResult[$item->user_id][$item->tour_type])){
                    $arrResult[$item->user_id][$item->tour_type] = 0;
                }
            }

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
            if($item->tour_type == 3){
                $arrResult[$item->user_id][$item->tour_type] += 1;
            }else{
                $arrResult[$item->user_id][$item->tour_type] += $item->adults;
            }

        }

        $agent = new Agent();
        if($agent->isMobile()){
            $view = 'report.m-customer-by-level';
        }else{
            $view = 'report.customer-by-level';
        }
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $maxDay = date('d', strtotime($maxdate));
        return view($view, compact('month', 'year', 'listUser', 'arrUser', 'time_type', 'arrSearch', 'arrResult', 'arrLevel', 'level', 'id_loaitru','arrByDay', 'maxDay'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function customer(Request $request)
    {

        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $arrSearch['code'] = $code = $request->code ?? null;
        $arrSearch['phone'] = $phone = $request->phone ?? null;
        $arrSearch['email'] = $email = $request->email ?? null;
        $arrSearch['status'] = $status = $request->status ?? null;
        $arrSearch['is_send'] = $is_send = $request->is_send ?? null;
        $arrSearch['is_accept'] = $is_accept = $request->is_accept ?? null;
        $arrSearch['user_id'] = $user_id = $request->user_id ?? null;
        $arrSearch['user_id_refer'] = $user_id_refer = $request->user_id_refer ?? null;
        $arrSearch['ads'] = $ads = $request->ads ?? null;

        $query = Customer::whereRaw('1');
        if ($city_id) {
            $query->where('city_id', $city_id);
        }
        if ($code) {
            $query->where('code', $code);
        }
        if ($phone) {
            $query->where('phone', $phone);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($email) {
            $query->where('email', $email);
        }
        if ($ads) {
            $query->where('ads', true);
        }
        if ($is_send) {
            $query->where('is_send', $is_send);
            if (Auth::user()->role > 2) { // nếu ko phải là admin
                $query->where('user_id_refer', Auth::user()->id);
            }
        }
        if ($is_accept) {
            $query->where('is_accept', $is_accept);
        }
        if ($user_id_refer) {
            $query->where('user_id_refer', $user_id_refer);
        }
        if (Auth::user()->role < 3) {
            if ($user_id) {
                $arrSearch['user_id'] = $user_id;
                $query->where('created_user', $user_id);
            }
        } else {
            $arrSearch['user_id'] = $user_id = Auth::user()->id;;
            $query->where(function ($query) use ($user_id) {
                $query->where('created_user', '=', $user_id)
                    ->orWhere('user_id_refer', '=', $user_id);
            });
        }

        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['checkin_from'] = $checkin_from = $request->checkin_from ? $request->checkin_from : null;
        $arrSearch['checkin_to'] = $checkin_to = $request->checkin_to ? $request->checkin_to : $checkin_from;

        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 'contact_date';
        $arrSearch['source'] = $source = $request->source ? $request->source : null;
        $arrSearch['source2'] = $source2 = $request->source2 ? $request->source2 : null;
        $arrSearch['product_type'] = $product_type = $request->product_type ? $request->product_type : null;
        $sources  = CustomerSource::whereNull('parent_id')->get();
        $sources2  = CustomerSource::whereHas('childs')->get();

        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));

        if($time_type == 1){ // theo thangs
            $arrSearch[$search_by.'_from'] = $checkin_from = $date_use = date('d/m/Y', strtotime($mindate));
            $arrSearch[$search_by.'_to'] = $checkin_to = date('d/m/Y', strtotime($maxdate));

            $query->whereDate($search_by,'>=', $mindate);
            $query->whereDate($search_by, '<=', $maxdate);
        }elseif($time_type == 2){ // theo khoang ngay
            $arrSearch[$search_by.'_from'] = $checkin_from = $date_use = $request->contact_date_from ? $request->contact_date_from : date('d/m/Y', time());
            $arrSearch[$search_by.'_to'] = $checkin_to = $request->contact_date_to ? $request->contact_date_to : $checkin_from;

            if($checkin_from){
                $arrSearch[$search_by.'_from'] = $checkin_from;
                $tmpDate = explode('/', $checkin_from);
                $checkin_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                $query->whereDate($search_by,'>=', $checkin_from_format);
            }
            if($checkin_to){
                $arrSearch[$search_by.'_to'] = $checkin_to;
                $tmpDate = explode('/', $checkin_to);
                $checkin_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                if($checkin_to_format < $checkin_from_format){
                    $arrSearch[$search_by.'_to'] = $checkin_from;
                    $checkin_to_format = $checkin_from_format;
                }
                $query->whereDate($search_by, '<=', $checkin_to_format);
            }
        } else if ($time_type == 4) {
            $date_use = Carbon::now()->startOfWeek()->format('Y-m-d');
            $query->whereDate($search_by, '>=', $date_use);
            $query->whereDate($search_by, '<=', Carbon::now()->endOfWeek()->format('Y-m-d'));
        } else if ($time_type == 5) {
            $date_use = Carbon::now()->startOfMonth()->format('Y-m-d');
            $query->whereDate($search_by, '>=', $date_use);
            $query->whereDate($search_by, '<=', Carbon::now()->endOfMonth()->format('Y-m-d'));
        } else if ($time_type == 6) {
            $date_use = Carbon::now()->subWeek(1)->startOfWeek()->format('Y-m-d');
            $query->whereDate($search_by, '>=', $date_use);
            $query->whereDate($search_by, '<=', Carbon::now()->subWeek(1)->endOfWeek()->format('Y-m-d'));
        }  else {
            $arrSearch[$search_by . '_from'] = $checkin_from = $arrSearch[$search_by . '_to'] = $checkin_to = $date_use = $request->contact_date_from ? $request->contact_date_from : date('d/m/Y', time());
            $arrSearch[$search_by.'_from'] = $checkin_from;
            $tmpDate = explode('/', $checkin_from);
            $checkin_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
            $query->whereDate($search_by,'=', $checkin_from_format);
        }
        if($source){
            $query->where('source', $source);
        }

        if($source2){
            $query->where('source2', $source);
        }

        if($product_type){
            $query->where('product_type', $product_type);
        }

        $items = $query->orderBy('id', 'desc')->get();
        $data = [];
        $total_booking = 0;
        $total_revenue = 0;
        $sourceSummary = [];
        $source2Summary = [];
        $statusSummary = [];
        $sourceCount = [];
        $source2Count = [];
        $productTypeCount = [];
        $adsCount = [];
        foreach ($items as $item){
            $data[$item->user->name]['name'] = $item->user->name;
            $data[$item->user->name]['items'][] = $item;
            $data[$item->user->name]['total_bookings'] = isset($data[$item->user->name]['total_bookings']) ? $data[$item->user->name]['total_bookings'] + $item->bookings->count() : $item->bookings->count();
            $data[$item->user->name]['total_price'] = isset($data[$item->user->name]['total_price']) ? $data[$item->user->name]['total_price'] + $item->bookings->sum('total_price') : $item->bookings->sum('total_price');
            $data[$item->user->name][$item->source] = isset($data[$item->user->name][$item->source]) ? $data[$item->user->name][$item->source] + $item->bookings->sum('total_price') : $item->bookings->sum('total_price');
            $total_booking += $item->bookings->count();
            $total_revenue += $item->bookings->sum('total_price');
            $statusSummary[$item->status] = isset($statusSummary[$item->status]) ? $statusSummary[$item->status] + 1 : 1;
            $sourceSummary[$item->source] = isset($sourceSummary[$item->source]) ? $sourceSummary[$item->source] + $item->bookings->sum('total_price') : $item->bookings->sum('total_price');
            $sourceCount[$item->source] = isset($sourceCount[$item->source]) ? $sourceCount[$item->source] + 1 : 1;
            $source2Summary[$item->source2] = isset($source2Summary[$item->source2]) ? $source2Summary[$item->source2] + $item->bookings->sum('total_price') : $item->bookings->sum('total_price');
            $source2Count[$item->source2] = isset($source2Count[$item->source2]) ? $source2Count[$item->source2] + 1 : 1;
            $productTypeCount[$item->product_type] = isset($productTypeCount[$item->product_type]) ? $productTypeCount[$item->product_type] + 1 : 1;

            $adsName = $item->adsCampaign ? $item->adsCampaign->name : '-';
            $adsCount[$adsName] = isset($adsCount[$adsName]) ? $adsCount[$adsName] + 1 : 1;
        }
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        return view('report.customer', compact('items', 'data', 'arrSearch', 'time_type' , 'date_use', 'type', 'month', 'year', 'total_booking', 'total_revenue', 'sourceSummary', 'sourceCount', 'source2Summary', 'source2Count', 'statusSummary', 'listUser', 'sources', 'sources2', 'productTypeCount', 'adsCount'));
    }


    public function ajaxSearchBen(Request $request){
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
         $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
         $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : 1;
        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $query = Booking::where('type', 1)->where('status', '<', 3);
        //dd($id_search);
        if($id_search){
           //  dd($id_search);
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptt", "", $id_search);
            $id_search = str_replace("pth", "", $id_search);
            $id_search = str_replace("ptv", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $items = $query->where('id', $id_search)->get();
        }

        $query->where('level', 7);
        $query->whereIn('tour_type', [1,2,3]);

        $items = $query->paginate(1000);
        $arrResult = [];
        $listUser = User::where('level', 7)->where('status', 1)->get();
        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }
        $arrLevel = $arrTour = [];
        $tong_so_nguoi = $tong_phan_an = $tong_coc = 0;
        foreach($items as $item){
            $arrTour[$item->user_id][] = $item;
            if($item->status != 3){
                $tong_so_nguoi += $item->adults;
                if($item->nguoi_thu_coc == 1){
                    $tong_coc += $item->tien_coc;
                }
                $tong_phan_an += $item->meals;
            }

        }

        return view('report.ajax-ben', compact('month', 'year', 'listUser', 'arrUser', 'time_type', 'arrSearch', 'arrResult', 'arrLevel', 'items', 'tong_so_nguoi', 'tong_phan_an', 'tong_coc', 'arrSearch'));

    }
    public function ben(Request $request){

        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
         $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
         $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : 1;
        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $query = Booking::where('type', 1)->where('status', '<', 3);
        if($id_search){
           //  dd($id_search);
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptt", "", $id_search);
            $id_search = str_replace("pth", "", $id_search);
            $id_search = str_replace("ptv", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $items = $query->where('id', $id_search)->get();
        }


        if($user_id){
            $arrSearch['user_id'] = $user_id;
            $query->where('user_id', $user_id);
        }

        if($tour_id){
            $arrSearch['tour_id'] = $tour_id;
            $query->where('tour_id', $tour_id);
        }
        if($nguoi_thu_tien){
                $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien;
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }
        $query->where('level', 7);
        $query->whereIn('tour_type', [1,2,3]);

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

        }

        $items = $query->paginate(1000);
        $arrResult = [];
        $listUser = User::where('level', 7)->where('status', 1)->get();
        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }
        $arrLevel = $arrTour = [];
        $tong_so_nguoi = $tong_phan_an = $tong_coc = 0;
        foreach($items as $item){
            $arrTour[$item->user_id][] = $item;
            if($item->status != 3){
                $tong_so_nguoi += $item->adults;
                if($item->nguoi_thu_coc == 1){
                    $tong_coc += $item->tien_coc;
                }
                $tong_phan_an += $item->meals;
            }
        }
        $agent = new Agent();
        if($agent->isMobile()){
           $view = 'report.m-khach-ben';
        }else{
           $view = 'report.khach-ben';
        }

        $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();
        return view($view, compact('month', 'year', 'listUser', 'arrUser', 'time_type', 'arrSearch', 'arrResult', 'arrLevel', 'items', 'tong_so_nguoi', 'tong_phan_an', 'tong_coc', 'arrSearch', 'tourSystem'));

    }
    public function car(Request $request)
    {

        $arrSearch['chua_thuc_thu'] = $chua_thuc_thu = $request->chua_thuc_thu ?? null;
        $arrSearch['no_driver'] = $no_driver = $request->no_driver ? $request->no_driver : null;
        $arrSearch['sales'] = $sales = $request->sales ? $request->sales : null;
        $arrSearch['keyword'] = $keyword = $request->keyword ? $request->keyword : null;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1,2];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $arrSearch['driver_id'] = $driver_id = $request->driver_id ? $request->driver_id : null;
        $arrSearch['email'] = $email = $request->email ? $request->email : null;
        $arrSearch['phone'] = $phone = $request->phone ? $request->phone : null;

        $arrSearch['sort_by'] = $sort_by = $request->sort_by ? $request->sort_by : 'created_at';
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 1;

        $use_df_default = Auth::user()->id == 151 ? date('d/m/Y', strtotime('yesterday')) : date('d/m/Y', time());
        $arrSearch['use_date_from'] = $use_date_from = $request->use_date_from ? $request->use_date_from : $use_df_default;
        $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : null;
        $query = Booking::where('type', 4);
        if(Auth::user()->id == 21){
            $status = 1;
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

        // if($ko_cap_treo > -1){
        //     $query->where('ko_cap_treo', $ko_cap_treo);
        // }
        if($id_search){
           //  dd($id_search);
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptt", "", $id_search);
            $id_search = str_replace("pth", "", $id_search);
            $id_search = str_replace("ptv", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
        }elseif($phone){
            $arrSearch['phone'] = $phone;
            $query->where('phone', $phone);
        }else{




            $query->whereIn('status', [1,2]);


            if($chua_thuc_thu == 1){
                $query->where('tien_thuc_thu', 0);
            }

            if($no_driver){
                $arrSearch['no_driver'] = $no_driver;
                $query->where('driver_id', 0);
            }
            if($tour_id){
                $arrSearch['tour_id'] = $tour_id;
                $query->where('tour_id', $tour_id);
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


            if(Auth::user()->role < 3){
                if($user_id && $user_id > 0){
                    $arrSearch['user_id'] = $user_id;
                    $query->where('user_id', $user_id);
                }
            }else{
                $arrSearch['user_id'] = Auth::user()->id;
                $query->where('user_id', Auth::user()->id);
            }


            $month = $request->month ?? date('m');
            $year = $request->year ?? date('Y');
            $mindate = "$year-$month-01";
            $maxdate = date("Y-m-t", strtotime($mindate));

            if($time_type == 1){
                $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
                $arrSearch['use_date_to'] = $use_date_to = date('d/m/Y', strtotime($maxdate));

                $query->where('use_date','>=', $mindate);
                $query->where('use_date', '<=', $maxdate);
            }elseif($time_type == 2){
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

            }

        }//end else

        if($driver_id){
            $query->where('driver_id', $driver_id);
        }

        if($sales == 1){
            $query->whereNotIn('user_id', [18,33]);
        }


        $allList = $query->get();

        $items  = $query->paginate(400);
       // dd($items);
        $tong_hoa_hong_cty = $tong_hoa_hong_sales = $tong_so_nguoi = $tong_phan_an = $tong_coc = $tong_phan_an_te = 0 ;
        $tong_thuc_thu = $tong_hoa_hong_chup = 0;
        $cap_nl = $cap_te = $tong_te =  0;

        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        $hotelList = Hotels::all();

        $agent = new Agent();



        $carCate = CarCate::all();

        //if(Auth::user()->id == 21){
        if($agent->isMobile()){
            $view = 'booking.m-index-car';
        }else{
            $view = 'booking.index-car';
            $view = 'report.car';
        }

        $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        $driverArrName = [];
        foreach($driverList as $dr){
            $driverArrName[$dr->id] = $dr->name;
        }
        $arrDriver = [];
        $t_chuyen = $t_tong = $t_cty = $t_sales = $t_tx = $t_dieuhanh = 0;
        foreach($items as $bk){
            if($bk->status != 3){
                $t_chuyen++;
                if(!isset($arrDriver[$bk->driver_id])){
                    $arrDriver[$bk->driver_id]['so_lan_chay'] = 0;
                    $arrDriver[$bk->driver_id]['tong_tien'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_tx_thu'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_sales_thu'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_cty_thu'] = 0;
                }
                $arrDriver[$bk->driver_id]['so_lan_chay']++;
                $arrDriver[$bk->driver_id]['tong_tien'] += $bk->total_price;
                $t_tong += $bk->total_price;
                if($bk->nguoi_thu_tien == 1){
                    $arrDriver[$bk->driver_id]['so_tien_sales_thu'] += $bk->total_price;
                    $t_sales += $bk->total_price;
                }elseif($bk->nguoi_thu_tien == 2){
                    $arrDriver[$bk->driver_id]['so_tien_cty_thu'] += $bk->total_price;
                    $t_cty += $bk->total_price;
                }elseif($bk->nguoi_thu_tien == 3){
                    $arrDriver[$bk->driver_id]['so_tien_tx_thu'] += $bk->total_price;
                    $t_tx += $bk->total_price;
                }elseif($bk->nguoi_thu_tien == 4){{
                    $arrDriver[$bk->driver_id]['so_tien_dieuhanh_thu'] += $bk->total_price;
                    $t_dieuhanh += $bk->total_price;
                }
            }

        }
        $type = 4;
        if($agent->isMobile()){
            $view = 'report.m-car';
        }else{
            $view = 'report.car';
        }
        return view($view, compact( 'items', 'arrSearch', 'type', 'listUser', 'carCate', 'keyword', 'tong_hoa_hong_sales', 'driverList', 'time_type', 'month', 'driverArrName', 'arrDriver', 't_chuyen', 't_tong', 't_sales', 't_tx', 't_cty', 't_dieuhanh'));
        }

    }
    public function cano(Request $request){
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";

        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        //$mindate = $maxdate = '2021-04-13';
        $all = Booking::where('use_date', '>=', $mindate)->where('use_date', '<=', $maxdate)
        ->where('type', 1)->where('tour_id', 1)->whereIn('status', [1, 2])->get();
        $arrCanoUsed = [];
        $arrCanoCty = [9, 10, 11];
        $arrCanoCount = [];
        $countCano = [];
        foreach($all as $bk){
            $day = date('d', strtotime($bk->use_date));
            $day = str_pad($day, 2, "0", STR_PAD_LEFT);
            $key = $day.'-'.$bk->tour_type."-".$bk->cano_id."-".$bk->hdv_id;

            if(!isset($arrCanoUsed[$day])){
                $arrCanoUsed[$day] = [];
            }
            if(!isset($arrCanoUsed[$key])){
               // dd($bk->id, $bk->cano_id);
                $arrCanoUsed[$key] = $bk;
                if(!isset($countCano[$bk->cano_id])){
                    $countCano[$bk->cano_id] = 1;

                }else{
                    $countCano[$bk->cano_id]++;

                }
                $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['hdv_id'] = $bk->hdv_id;
                    $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['adults'] = $bk->adults;
                    $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['childs'] = $bk->childs;
                    $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['cap_nl'] = $bk->cap_nl;
                    $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['cap_te'] = $bk->cap_te;
                    $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['meals'] = $bk->meals;
                    $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['meals_te'] = $bk->meals_te;
                // $arrCanoUsed[$key] = $bk;


            }else{
                $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['adults'] += $bk->adults;
                $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['childs'] += $bk->childs;
                $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['cap_nl'] += $bk->cap_nl;
                $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['cap_te'] += $bk->cap_te;
                $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['meals'] += $bk->meals;
                $arrCanoCount[$day][$bk->tour_type][$bk->cano_id]['meals_te'] += $bk->meals_te;
            }
        }
        //dd($countCano);
        ksort($arrCanoCount);

        // chi phi
        $costAll = Cost::where('date_use', '>=', $mindate)->where('date_use', '<=', $maxdate)->where('status', '>', 0)->whereIn('cate_id', [1, 2])->get();

        $arrCost = [];
        $tong_chi = 0;
        foreach($costAll as $costDay){
            if($costDay->cate_id == 1 || $costDay->cate_id == 2 ){
                if(!isset($arrCost[$costDay->partner_id])){
                    $arrCost[$costDay->partner_id]['amount'] = $costDay->amount;
                    $arrCost[$costDay->partner_id]['total_money'] = $costDay->total_money;
                }else{
                    $arrCost[$costDay->partner_id]['amount'] += $costDay->amount;
                    $arrCost[$costDay->partner_id]['total_money'] += $costDay->total_money;
                }
            }
        }

       // dd($arrCost);
        $partnerArr = Partner::pluck('name', 'id');
        $userArr = User::pluck('name', 'id');
        return view('report.cano', compact('maxDay', 'arrCanoUsed', 'partnerArr', 'countCano', 'month', 'arrCost', 'year', 'arrCanoCty', 'arrCanoCount', 'userArr'));
    }
    public function canoDetail(Request $request){
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $cano_id = $request->cano_id ?? null;
        $mindate = "$year-$month-01";

        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        // chi phi
        $costAll = Cost::where('date_use', '>=', $mindate)
                        ->where('date_use', '<=', $maxdate)
                        ->where('status', '>', 0)
                        ->where('cate_id', 2)
                        ->where('partner_id', $cano_id)
                        ->orderBy('date_use', 'asc')
                        ->get();

        $allBooking = Booking::where('use_date', '>=', $mindate)->where('use_date', '<=', $maxdate)
        ->where('type', 1)->where('tour_id', 1)->where('status','<>',3)->where('cano_id', $cano_id)->get();

        foreach($allBooking as $bk){
            $day = date('d', strtotime($bk->use_date));
            $day = str_pad($day, 2, "0", STR_PAD_LEFT);
            $key = $day.'-'.$bk->tour_type."-".$bk->hdv_id;

            if(!isset($arrCanoUsed[$day])){
                $arrCanoUsed[$day] = [];
            }
            if(!isset($arrCanoUsed[$key])){
               // dd($bk->id, $bk->cano_id);
                $arrCanoUsed[$key] = $bk;
                if(!isset($countCano[$bk->cano_id])){
                    $countCano[$bk->cano_id] = 1;

                }else{
                    $countCano[$bk->cano_id]++;

                }
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['hdv_id'] = $bk->hdv_id;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['adults'] = $bk->adults;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['childs'] = $bk->childs;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['cap_nl'] = $bk->cap_nl;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['cap_te'] = $bk->cap_te;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['meals'] = $bk->meals;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['meals_te'] = $bk->meals_te;
                // $arrCanoUsed[$key] = $bk;


            }else{
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['adults'] += $bk->adults;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['childs'] += $bk->childs;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['cap_nl'] += $bk->cap_nl;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['cap_te'] += $bk->cap_te;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['meals'] += $bk->meals;
                $arrCanoCount[$day][$bk->hdv_id][$bk->tour_type]['meals_te'] += $bk->meals_te;
            }
        }
        //dd($arrCanoCount);
        ksort($arrCanoCount);
        //dd($arrCanoCount);
        $arrCostByDay = [];
        $totalAmount = 0;
        foreach($costAll as $cost){
            //dd($cost);
            if(!isset($arrCostByDay[date('d', strtotime($cost->date_use))])){
                $arrCostByDay[date('d', strtotime($cost->date_use))] = $cost->amount;
            }else{
                $arrCostByDay[date('d', strtotime($cost->date_use))] += $cost->amount;
            }
            $totalAmount += $cost->amount;
        }

       // dd($arrCostByDay);
        $arrCost = [];
        $tong_chi = 0;
        foreach($costAll as $costDay){
            if($costDay->cate_id == 1 || $costDay->cate_id == 2 ){
                if(!isset($arrCost[$costDay->partner_id])){
                    $arrCost[$costDay->partner_id]['amount'] = $costDay->amount;
                    $arrCost[$costDay->partner_id]['total_money'] = $costDay->total_money;
                }else{
                    $arrCost[$costDay->partner_id]['amount'] += $costDay->amount;
                    $arrCost[$costDay->partner_id]['total_money'] += $costDay->total_money;
                }
            }
        }

       // dd($arrCost);
        $partnerArr = Partner::pluck('name', 'id');
        $canoDetail = Partner::find($cano_id);
        return view('report.cano-detail', compact('maxDay', 'partnerArr', 'month', 'arrCost', 'year', 'arrCostByDay', 'totalAmount', 'canoDetail', 'arrCanoCount', 'cano_id'));
    }
    public function dateDiff($date1, $date2)
    {
        $date1_ts = strtotime($date1);
        $date2_ts = strtotime($date2);
        $diff = $date2_ts - $date1_ts;
        return round($diff / 86400);
    }

    public function doanhthuthang(Request $request){

        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $dao = $request->dao ?? 0;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $maxDay = date('d', strtotime($maxdate));

        $tong_so_ngay = $this->dateDiff($mindate, $maxdate) + 1;

        //dd($mindate, $maxdate);
        if($dao){
            $arrTourId = [1];
            $arrCostType = [1];
        }else{
            $arrTourId = [1, 6, 10, 7, 8, 3, 5];
            $arrCostType = [1, 2, 3, 5];
        }

        $all = Booking::where('use_date', '>=', $mindate)->where('use_date', '<=', $maxdate)->where('type', 1)->whereIn('status',[1, 2])->whereIn('tour_id', $arrTourId)->get();
        // tour dao
        $costAll = Cost::where('date_use', '>=', $mindate)->where('date_use', '<=', $maxdate)->where('status', '>', 0)->where('city_id', 1)->get();
        $costTour = Cost::where('date_use', '>=', $mindate)->where('date_use', '<=', $maxdate)->where('status', '>', 0)->where('city_id', 1)->whereIn('type', $arrCostType)->get();
        $costCoDinh = Cost::where('date_use', '>=', $mindate)->where('date_use', '<=', $maxdate)->where('status', '>', 0)->where('city_id', 1)->where('is_fixed', 1)->sum('total_money');
        $costPerDay = round($costCoDinh/$tong_so_ngay);
        $revenueAll = Revenue::where('pay_date', '>=', $mindate)->where('pay_date', '<=', $maxdate)->where('status', '>', 0)->get();
        $debtAll = Debt::where('pay_date', '>=', $mindate)->where('pay_date', '<=', $maxdate)->where('status', 1)->get();

        $arrCost = [];
        $tong_chi = 0;
        foreach($costAll as $costDay){
            $key = (int) date('d', strtotime($costDay->date_use));
            if(!isset($arrCost[$key])){
                $arrCost[$key]['total'] = 0;
            }
            if(!isset($arrCost[$key][$costDay->cate_id])){
                $arrCost[$key][$costDay->cate_id]['total'] = 0;
            }
           // var_dump($costDay->partner_id);
            if($costDay->partner_id > 0 && !isset($arrCost[$key][$costDay->cate_id][$costDay->partner_id])){
                $arrCost[$key][$costDay->cate_id][$costDay->partner_id] = 0;
            }

            $arrCost[$key]['total'] += $costDay->total_money;
            $arrCost[$key][$costDay->cate_id]['total'] += $costDay->total_money;
            if($costDay->partner_id > 0){
               $arrCost[$key][$costDay->cate_id][$costDay->partner_id] += $costDay->total_money;
            }

            $tong_chi += $costDay->total_money;
        }

        $arrCostTour = [];
        $tong_chi_tour = 0;
        foreach($costTour as $costDay){
            $key = (int) date('d', strtotime($costDay->date_use));
            if(!isset($arrCostTour[$key])){
                $arrCostTour[$key]['total'] = 0;
            }
            if(!isset($arrCostTour[$key][$costDay->cate_id])){
                $arrCostTour[$key][$costDay->cate_id]['total'] = 0;
            }
           // var_dump($costDay->partner_id);
            if($costDay->partner_id > 0 && !isset($arrCostTour[$key][$costDay->cate_id][$costDay->partner_id])){
                $arrCostTour[$key][$costDay->cate_id][$costDay->partner_id] = 0;
            }

            $arrCostTour[$key]['total'] += $costDay->total_money;
            $arrCostTour[$key][$costDay->cate_id]['total'] += $costDay->total_money;
            if($costDay->partner_id > 0){
               $arrCostTour[$key][$costDay->cate_id][$costDay->partner_id] += $costDay->total_money;
            }

            $tong_chi_tour += $costDay->total_money;
        }


        //dd($arrCost);
        $tong_thuc_thu = $tong_coc = $tong_cong_no = 0;
        $tong_hoa_hong_sales = 0;
        $arrDay = [];

        foreach($all as $bk){
            $key = (int) date('d', strtotime($bk->use_date));
            if(!isset($arrDay[$key])){
                $arrDay[$key] = [
                                    'tien_thuc_thu' => 0,
                                    'tien_coc' => 0,
                                    'hoa_hong_sales' => 0,
                                    'adults' => 0,
                                    'childs' => 0,
                                    'cap_nl' => 0,
                                    'cap_te' => 0,
                                    'meals' => 0
                               ];
            }
            $arrDay[$key]['tien_thuc_thu'] += $bk->tien_thuc_thu;
            $arrDay[$key]['tien_coc'] += $bk->tien_coc;
            $arrDay[$key]['hoa_hong_sales'] += $bk->hoa_hong_sales;
            $arrDay[$key]['adults'] += $bk->adults;
            $arrDay[$key]['childs'] += $bk->childs;
            $arrDay[$key]['cap_nl'] += $bk->cap_nl;
            $arrDay[$key]['cap_te'] += $bk->cap_te;
            $arrDay[$key]['meals'] += $bk->meals;
            if($bk->meals > 0 && $bk->childs > 0){
                $arrDay[$key]['meals'] += $bk->childs*0.5;
            }
            if($bk->nguoi_thu_tien != 4){
            //if($bk->type == 1){
                $tong_thuc_thu += ($bk->tien_thuc_thu + $bk->tien_coc);
            }else{
                $tong_cong_no += $bk->tien_thuc_thu + $bk->tien_coc;
            }
          //  $tong_thuc_thu += $bk->tien_thuc_thu;
            $tong_coc += $bk->tien_coc;
            $tong_hoa_hong_sales += $bk->hoa_hong_sales;
        }

        $con_lai = $tong_thuc_thu-$tong_hoa_hong_sales-$tong_chi;
        //dd($arrDay);
        $minDateFormat = date('d/m/Y', strtotime($mindate));
        $maxDateFormat = date('d/m/Y', strtotime($maxdate));
        $cateList = CostType::where('status', 1)->where('have_partner', 0)->orderBy('display_order')->get();
        //dd($cateList);
        //dd($tong_cong_no);
        return view('report.doanh-thu-thang', compact('arrDay', 'tong_thuc_thu', 'tong_hoa_hong_sales', 'arrCost', 'tong_chi', 'con_lai', 'maxDay', 'minDateFormat', 'maxDateFormat', 'month', 'year', 'cateList', 'type', 'revenueAll', 'debtAll', 'costPerDay', 'arrCostTour', 'tong_chi_tour', 'tong_cong_no'));

    }


    public function yearDoiTac(Request $request){

        $year = $request->year ?? date('Y');
        $arrSearch['month'] = $month = $request->month ?? date('m');
        $mindate = "$year-$month-01";

        $maxdate = date("Y-m-t", strtotime($mindate));

        $arrSearch['level'] = $level = $request->level ?? 2;
        $arrSearch['time_type'] = $time_type = $request->time_type ?? 4;

        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : 1;
        $query = Booking::where('status', '<', 3)
                ->where('tour_id', $tour_id)
                //->whereIn('tour_type', [1, 2]);
                ->where('level', $level);
        if($user_id){
            $query->where('user_id', $user_id);
        }
        if($time_type == 1){
            $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
            $arrSearch['use_date_to'] = $use_date_to = date('d/m/Y', strtotime($maxdate));

            $query->where('book_date','>=', $mindate);
            $query->where('book_date', '<=', $maxdate);
        }elseif($time_type == 2){
            $arrSearch['use_date_from'] = $use_date_from = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
            $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;

            if($use_date_from){
                $arrSearch['use_date_from'] = $use_date_from;
                $tmpDate = explode('/', $use_date_from);
                $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                $query->where('book_date','>=', $use_date_from_format);
            }
            if($use_date_to){
                $arrSearch['use_date_to'] = $use_date_to;
                $tmpDate = explode('/', $use_date_to);
                $use_date_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                if($use_date_to_format < $use_date_from_format){
                    $arrSearch['use_date_to'] = $use_date_from;
                    $use_date_to_format = $use_date_from_format;
                }
                $query->where('book_date', '<=', $use_date_to_format);
            }
        }elseif($time_type == 3){
            $arrSearch['use_date_from'] = $use_date_from = $arrSearch['use_date_to'] = $use_date_to = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());

            $arrSearch['use_date_from'] = $use_date_from;
            $tmpDate = explode('/', $use_date_from);
            $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
            $query->where('book_date','=', $use_date_from_format);

        }else{
            $use_date_from_format = $year.'-01-01';
            $use_date_to_format = $year.'-12-31';
            $arrSearch['use_date_from'] = date('d-m-Y', strtotime($use_date_from_format));
            $arrSearch['use_date_to'] = date('d-m-Y', strtotime($use_date_to_format));
            $query->where('book_date','>=', $use_date_from_format);
            $query->where('book_date','<=', $use_date_to_format);

        }
        // thong ke doi tac tạo trong khoảng thời gian này
        $queryUser = User::where(['level' => 2, 'status' => 1])->where('created_at','>=', $use_date_from_format." 00:00:00")->where('created_at','<=', $use_date_to_format." 23:59:59")->get();
        $arrUserByMonth = [];
        foreach($queryUser as $u){
            $monthCreate = date('m', strtotime($u->created_at));
            $arrUserByMonth[$monthCreate][] = $u;
        }
        //dd($arrUserByMonth);
        //$query->where('user_id', 272);
        $items = $query->get();
        //dd($items);
        // xem số lượng đối tác từng tháng gửi tour
        $arrDoiTac = [];
        foreach($items as $item){
            $month = date('m', strtotime($item->use_date));

            if(!isset($arrDoiTac[$month][$item->user_id])){
                $arrDoiTac[$month][$item->user_id] = 0;
            }
            $arrDoiTac[$month][$item->user_id]++;
        }
       // dd($arrDoiTac);
        //lay danh sach doi tac
        $listUser = User::where('level', $level)->where('status', 1)
        //->where('id', 526)
        ->get();
        $arrId =[];
        $arrName = [];
        $arrLastDate = $arrMinMonth = [];
        $arrCountNew = $arrIdNew = [];
        foreach($listUser as $dt){
            $arrId[] = $dt->id;
            $arrName[$dt->id] = $dt->name;
            $arrLastDate[$dt->id] = Booking::where(
                [
                    'type' => 1,
                    'user_id' => $dt->id
                ]
            )->where('status', '<', 3)->max('book_date');

            $rsMin = Booking::where(
                [
                    'type' => 1,
                    'user_id' => $dt->id,
                    'tour_id' => $tour_id
                ]
            )->where('status', '<', 3)
            ->where('book_date', '>=', $use_date_from_format)
            ->min('book_date');
          //  dd($rsMin);
            $minMonth = date('m', strtotime($rsMin));
            if($minMonth > 3){
                $arrMinMonth[$dt->id] = $minMonth;
                if(!isset($arrCountNew[$minMonth])){
                    $arrCountNew[$minMonth] = 0;
                }
                $arrCountNew[$minMonth]++;
                $arrIdNew[$minMonth][] = $dt->id;
            }
        }
      //  dd($arrIdNew);
        // lay so luong booking theo từng đối tác theo tháng
       // dd($arrId);
       // dd($arrName);
        //dd($arrDoiTac[10][490]);
        $arrByDoiTac = [];
        $arrMonth = [];
        $arrAllUserByMonth = [];

        for($i = 1; $i < 13; $i++){
            $tmpMonth = str_pad($i, 2, "0", STR_PAD_LEFT);
            $tmpYear = $year;
            $arrMonth[] = $tmpMonth;
            foreach($arrId as $user_id){
                    $arrByDoiTac[$user_id][$tmpMonth] = isset($arrDoiTac[$tmpMonth][$user_id]) ? $arrDoiTac[$tmpMonth][$user_id] : '';
                    $mindate = "$year-$tmpMonth-01";
                    $maxdate = date("Y-m-t", strtotime($mindate));
                    $arrAllUserByMonth[$tmpMonth] =  User::where(['level' => 2, 'status' => 1])->where('created_at', '<=', $maxdate." 23:59:59")->count();
            }
        }
        //dd($arrAllUserByMonth);
        $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();
        return view('report.year-doi-tac', compact('year', 'arrName', 'arrId', 'arrByDoiTac', 'items', 'level', 'arrSearch', 'listUser', 'arrMonth', 'arrLastDate', 'arrMinMonth', 'arrDoiTac', 'arrIdNew', 'arrUserByMonth', 'arrAllUserByMonth', 'tourSystem'));
    }

    public function customerByLevelAndMonth(Request $request){
        $monthDefault = date('m');
        $arrSearch['month'] = $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $level = $request->level ?? null;
        $id_loaitru = $request->id_loaitru ?? null;
        $arrSearch['year'] = $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";

        $maxdate = date("Y-m-t", strtotime($mindate));
        //dd($maxdate);
        //$maxdate = '2021-03-01';
        $maxDay = date('d', strtotime($maxdate));
        $arrLevel = [1,2,6];
        $arrResult = $arrMonth = [];
        foreach($arrLevel as $level){
            for($i = 1; $i < 13; $i++){
                $tmpMonth = str_pad($i, 2, "0", STR_PAD_LEFT);
                $tmpYear = $year;
                $arrMonth[$tmpMonth] = $tmpMonth;
                $mindate = "$year-$tmpMonth-01";
                $maxdate = date("Y-m-t", strtotime($mindate));

                $query = Booking::where(['level' => $level, 'type' => 1])->where('status', '<', 3);

                $query->where('book_date','>=', $mindate);
                $query->where('book_date','<=', $maxdate);
                $items = $query->get();
                foreach($items as $item){

                    if(!isset($arrResult[$level][$tmpMonth])){
                        $arrResult[$level][$tmpMonth] = 0;
                    }
                    $arrResult[$level][$tmpMonth]+= $item->adults;
                }

            }
        }


        $view = 'report.customer-by-level-month';

        return view($view, compact('month', 'year', 'arrResult', 'arrLevel', 'arrMonth'));

    }

    public function veCapTreoTheoThang(Request $request){

        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $maxDay = date('d', strtotime($maxdate));

        $tong_so_ngay = $this->dateDiff($mindate, $maxdate) + 1;

        //dd($mindate, $maxdate);

        $all = Booking::where('use_date', '>=', $mindate)->where('use_date', '<=', $maxdate)->where('type', 1)->whereIn('status',[1, 2])->whereIn('tour_id', [1, 6, 8])
            ->where(function ($query) {
                $query->whereNull('is_send')
                      ->orWhere('is_send', 0);
            })->get();

        $costVeCap = Cost::where('date_use', '>=', $mindate)->where('date_use', '<=', $maxdate)->where('status', '>', 0)->where('city_id', 1)->whereIn('cate_id', [4, 11])->get(); // 4 NL, 11 cap TE



        $arrCost = [];
        $tong_chi = 0;
        foreach($costVeCap as $costDay){
            $key = (int) date('d', strtotime($costDay->date_use));
            if(!isset($arrCost[$key])){
                $arrCost[$key]['total'] = 0;
            }
            if(!isset($arrCost[$key][$costDay->cate_id])){
                $arrCost[$key][$costDay->cate_id]['total'] = 0;
                $arrCost[$key][$costDay->cate_id]['amount'] = 0;
            }


            $arrCost[$key]['total'] += $costDay->total_money;
            $arrCost[$key][$costDay->cate_id]['total'] += $costDay->total_money;
            $arrCost[$key][$costDay->cate_id]['amount'] += $costDay->amount;

        }

        $arrDay = [];

        foreach($all as $bk){
            $key = (int) date('d', strtotime($bk->use_date));
            if(!isset($arrDay[$key])){
                $arrDay[$key] = ['cap_nl' => 0, 'cap_te' => 0 ];
            }
            $arrDay[$key]['cap_nl'] += $bk->cap_nl;
            $arrDay[$key]['cap_te'] += $bk->cap_te;
        }
        //dd($arrDay);
        $minDateFormat = date('d/m/Y', strtotime($mindate));
        $maxDateFormat = date('d/m/Y', strtotime($maxdate));

        return view('report.ve-cap-treo', compact('arrDay', 'arrCost', 'maxDay', 'minDateFormat', 'maxDateFormat', 'month', 'year', 'type',));

    }
    public function phanAnThang(Request $request){

        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $maxDay = date('d', strtotime($maxdate));

        $tong_so_ngay = $this->dateDiff($mindate, $maxdate) + 1;

        //dd($mindate, $maxdate);

        $all = Booking::where('use_date', '>=', $mindate)->where('use_date', '<=', $maxdate)->where('type', 1)->whereIn('status',[1, 2])->whereIn('tour_id', [1, 3])->where(function ($query) {
                $query->whereNull('is_send')
                      ->orWhere('is_send', 0);
            })->get();

        $costVeCap = Cost::where('date_use', '>=', $mindate)->where('date_use', '<=', $maxdate)->where('status', '>', 0)->where('city_id', 1)->where('cate_id', 5)->get(); // 4 NL, 11 cap TE



        $arrCost = [];
        $tong_chi = 0;
        foreach($costVeCap as $costDay){
            $key = (int) date('d', strtotime($costDay->date_use));

            if(!isset($arrCost[$key][$costDay->type])){
                $arrCost[$key][$costDay->type]['total'] = 0;
                $arrCost[$key][$costDay->type]['amount'] = 0;
            }


            //$arrCost[$key]['total'] += $costDay->total_money;
            $arrCost[$key][$costDay->type]['total'] += $costDay->total_money;
            $arrCost[$key][$costDay->type]['amount'] += $costDay->amount;

        }
        //dd($arrCost);
        $arrDay = [];
        foreach($all as $bk){
            $key = (int) date('d', strtotime($bk->use_date));
            if($bk->tour_id == 1){
                $type = 1; // dao
            }elseif($bk->tour_id == 3){
                $type = 2; // rach vem
            }
            if(!isset($arrDay[$key][$type])){
                $arrDay[$key][$type] = 0;
            }
            $arrDay[$key][$type] += ($bk->meals + $bk->meals_te/2);
        }
        $minDateFormat = date('d/m/Y', strtotime($mindate));
        $maxDateFormat = date('d/m/Y', strtotime($maxdate));

        return view('report.phan-an', compact('arrDay', 'arrCost', 'maxDay', 'minDateFormat', 'maxDateFormat', 'month', 'year', 'type',));

    }
    public function general(Request $request){
        //default is this month
        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y');
        $type = $request->type ?? 1;
        $level = $request->level ?? null;

        $query = Booking::where('status', '<', 3);

        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => this month
            $rangeDate = Carbon::now();
            $query->where('use_date','=', $rangeDate->format('Y-m-d'));
        } elseif (count($rangeDate) === 1) {
            //case page is initialized and range_date has value,
            //when counting the number of elements in rangeDate = 1 => only select a day
            $query->where('use_date','=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
        } else {
            $query->where('use_date','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $query->where('use_date', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
        }

        if($level){
            $query->where('level', $level);
            $query->whereIn('tour_type', [1,2]);
        }

        $items = $query->get();
        $arrResult = [];
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }
        $arrLevel = [];
        $arrByDay = [];
        $arrResult = [];
        $arrDoanhThu = $arrTotalByType =  $arrCountByTour = [];
        $arrDoanhThuLevel = [];
        foreach($items as $item){
            if($item->type > 1){
                if($item->user->level){
                    $item->update(['level' => $item->user->level]);
                }
              //  dd('1111');
            }
            $arrLevel[$item->level] = $item->level;
            // doanh thu tổng theo từng loại
            if(!isset($arrTotalByType['doanh_thu'][$item->type])){
                $arrTotalByType['doanh_thu'][$item->type] = 0;
            }
            $arrTotalByType['doanh_thu'][$item->type] += $item->total_price;

            // doanh thu tổng theo từng loại
            if(!isset($arrTotalByType['total_bk'][$item->type])){
                $arrTotalByType['total_bk'][$item->type] = 0;
            }
            $arrTotalByType['total_bk'][$item->type] ++;

            if(!isset($arrDoanhThu[$item->type][$item->tour_id][$item->tour_type])){
                $arrDoanhThu[$item->type][$item->tour_id][$item->tour_type] = 0;
                $arrCountByTour[$item->type][$item->tour_id][$item->tour_type] = 0;
            }

            $arrDoanhThu[$item->type][$item->tour_id][$item->tour_type] += $item->total_price;

            if(!isset($arrDoanhThuLevel[$item->type][$item->level])){
                $arrDoanhThuLevel[$item->type][$item->level] = 0;
            }
            $arrDoanhThuLevel[$item->type][$item->level] += $item->total_price;


            $arrCountByTour[$item->type][$item->tour_id][$item->tour_type] ++;

            $arrResult[$item->user_id][$item->type][$item->tour_id][$item->tour_type][] = $item;

            $arrByDay[$item->use_date][$item->type][$item->tour_id][$item->tour_type][] = $item;

            // if($item->type == 2){
            //     echo $item->level."-".$item->user_id;
            //     echo "<hr>";
            // }
        }
        $agent = new Agent();
        if($agent->isMobile()){
            $view = 'report.m-customer-by-user';
        }else{
            $view = 'report.general';
        }

        return view($view, compact('listUser', 'arrUser', 'arrSearch', 'arrResult', 'arrLevel', 'level','arrByDay', 'arrTotalByType', 'arrDoanhThu', 'arrCountByTour', 'arrDoanhThuLevel'));

    }
    public function detailByType(Request $request){
        $ticketTypeArr = TicketTypeSystem::pluck('name', 'id');

        $type = $request->type ?? 1;
        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y');

        $query = Booking::where('status', '<', 3)->where('type', $type);

        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => this month
            $rangeDate = Carbon::now();
            $query->where('use_date','=', $rangeDate->format('Y-m-d'));
            $use_date_from_format = $use_date_to_format = $rangeDate->format('Y-m-d');
        } elseif (count($rangeDate) === 1) {
            //case page is initialized and range_date has value,
            //when counting the number of elements in rangeDate = 1 => only select a day
            $use_date_from_format = $use_date_to_format = $use_date = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d');

            $query->where('use_date','=', $use_date);
            $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
        } else {

            $use_date_from_format = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d');
            $use_date_to_format = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d');
            $query->where('use_date','>=', $use_date_from_format);
            $query->where('use_date', '<=', $use_date_to_format);

        }

        $items = $query->get();
        $arrResult = [];
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }
        $arrLevel = [];

        $arrByLevel = $arrBk = [];
        $tong_bk = $tong_doanh_thu = $tong_nl = 0;

        //hotel
        $tong_so_phong = $tong_so_dem = 0;
        $arrTicketTypeId = [];
        foreach($items as $item){
            $arrLevel[$item->level] = $item->level;
            //hotel

            $so_phong = $so_dem = $so_ve = 0;
            if($item->rooms && $type == 2){

                foreach($item->rooms as $room){
                    $so_phong += $room->room_amount;
                    $so_dem += $room->nights;
                }
            }

            if($item->tickets && $type == 3){
                foreach($item->tickets as $ticket){
                    $so_ve += $ticket->amount;
                }
            }

            if(!isset($arrByLevel[$item->level])){
                $arrByLevel[$item->level]['so_booking'] = 0;
                $arrByLevel[$item->level]['so_phong'] = 0;
                $arrByLevel[$item->level]['so_dem'] = 0;
                $arrByLevel[$item->level]['so_ve'] = 0;
            }
            $arrByLevel[$item->level]['so_booking']++;
            $arrByLevel[$item->level]['so_phong'] += $so_phong;
            $arrByLevel[$item->level]['so_dem'] += $so_dem;
            $arrByLevel[$item->level]['so_ve'] += $so_ve;



            if(!isset($arrByLevel[$item->level]['doanh_thu'])){
                $arrByLevel[$item->level]['doanh_thu'] = 0;
            }
            $arrByLevel[$item->level]['doanh_thu'] += $item->total_price;

            if(!isset($arrByLevel[$item->level]['hoa_hong_sales'])){
                $arrByLevel[$item->level]['hoa_hong_sales'] = 0;
            }
            $arrByLevel[$item->level]['hoa_hong_sales'] += $item->hoa_hong_sales;

            if(!isset($arrByLevel[$item->level]['hoa_hong_cty'])){
                $arrByLevel[$item->level]['hoa_hong_cty'] = 0;
            }
            $arrByLevel[$item->level]['hoa_hong_cty'] += $item->hoa_hong_cty;

            $arrBKId = [];
            //tour-dao
            if($type == 1){
               if(!isset($arrByLevel[$item->level]['nl'])){
                    $arrByLevel[$item->level]['nl'] = 0;
                }
                $arrByLevel[$item->level]['nl'] += $item->adults;


                if(!isset($arrByLevel[$item->level]['te'])){
                    $arrByLevel[$item->level]['te'] = 0;
                }
                $arrByLevel[$item->level]['te'] += $item->childs;


                if(!isset($arrByLevel[$item->level]['eb'])){
                    $arrByLevel[$item->level]['eb'] = 0;
                }
            }elseif($type == 2){
                //hotel
                if(!isset($arrByLevel[$item->level][$item->hotel->stars])){
                    $arrByLevel[$item->level][$item->hotel->stars]['so_booking'] = 0;
                    $arrByLevel[$item->level][$item->hotel->stars]['so_phong'] = 0;
                    $arrByLevel[$item->level][$item->hotel->stars]['so_dem'] = 0;
                }
                $arrByLevel[$item->level][$item->hotel->stars]['so_booking'] ++;
                $arrByLevel[$item->level][$item->hotel->stars]['so_phong'] += $so_phong;
                $arrByLevel[$item->level][$item->hotel->stars]['so_dem'] += $so_dem;
            }elseif($type == 3){
                //hotel

                if($item->tickets){
                    foreach($item->tickets as $ticket){
                        if(!isset($arrByLevel[$item->level][$ticket->ticket_type_id])){
                            $arrByLevel[$item->level][$ticket->ticket_type_id]['so_booking'] = 0;
                            $arrByLevel[$item->level][$ticket->ticket_type_id]['so_ve'] = 0;
                            $arrBKId[] = $item->id;
                        }
                        if(!isset($arrBkId[$item->id])){
                            $arrByLevel[$item->level][$ticket->ticket_type_id]['so_booking'] ++;
                        }

                        $arrByLevel[$item->level][$ticket->ticket_type_id]['so_ve'] += $so_ve;
                    }
                }

            } // type = 3
            //end Tổng


            //tour
            $tong_bk++;
            $tong_doanh_thu += $item->total_price;
            $tong_nl += $item->adults;


            if(!isset($arrBk[$item->use_date])){
                $arrBk[$item->use_date]['so_booking'] = 0;
                $arrBk[$item->use_date]['tong_doanh_thu'] = 0;
                $arrBk[$item->use_date]['tong_hh_cty'] = 0;
                $arrBk[$item->use_date]['tong_hh_sales'] = 0;
                $arrBk[$item->use_date]['tong_nl'] = 0;
                //hotel
                $arrBk[$item->use_date]['tong_so_dem'] = 0;
                $arrBk[$item->use_date]['tong_so_phong'] = 0;
            }

            $arrBk[$item->use_date]['so_booking']++;
            $arrBk[$item->use_date]['tong_doanh_thu'] += $item->total_price;
            $arrBk[$item->use_date]['tong_hh_cty'] += $item->hoa_hong_cty;
            $arrBk[$item->use_date]['tong_hh_sales'] += $item->hoa_hong_sales;
            $arrBk[$item->use_date]['tong_nl'] += $item->adults;


            $arrBk[$item->use_date]['tong_so_phong'] += $so_phong;
            $arrBk[$item->use_date]['tong_so_dem'] += $so_dem;

            if(!isset($arrByLevel[$item->level][$item->use_date])){
                $arrByLevel[$item->level][$item->use_date]['so_booking'] = 0;
            }
            $arrByLevel[$item->level][$item->use_date]['so_booking']++;
            if(!isset($arrByLevel[$item->level][$item->use_date]['doanh_thu'])){
                $arrByLevel[$item->level][$item->use_date]['doanh_thu'] = 0;
            }
            $arrByLevel[$item->level][$item->use_date]['doanh_thu'] += $item->total_price;
            if(!isset($arrByLevel[$item->level][$item->use_date]['hoa_hong_cty'])){
                $arrByLevel[$item->level][$item->use_date]['hoa_hong_cty'] = 0;
            }
            $arrByLevel[$item->level][$item->use_date]['hoa_hong_cty'] += $item->hoa_hong_cty;


            if($type == 1){

                if(!isset($arrByLevel[$item->level][$item->use_date][$item->tour_type])){
                    $arrByLevel[$item->level][$item->use_date][$item->tour_type]['so_booking'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date][$item->tour_type]['so_booking']++;

                if(!isset($arrByLevel[$item->level][$item->use_date][$item->tour_type]['doanh_thu'])){
                    $arrByLevel[$item->level][$item->use_date][$item->tour_type]['doanh_thu'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date][$item->tour_type]['doanh_thu'] += $item->total_price;


                if(!isset($arrByLevel[$item->level][$item->use_date]['nl'])){
                    $arrByLevel[$item->level][$item->use_date]['nl'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date]['nl'] += $item->adults;


                if(!isset($arrByLevel[$item->level][$item->use_date]['te'])){
                    $arrByLevel[$item->level][$item->use_date]['te'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date]['te'] += $item->childs;


                if(!isset($arrByLevel[$item->level][$item->use_date]['eb'])){
                    $arrByLevel[$item->level][$item->use_date]['eb'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date]['eb'] += $item->infants;
            }elseif($type == 2){

                if(!isset($arrByLevel[$item->level][$item->use_date]['so_phong'])){
                    $arrByLevel[$item->level][$item->use_date]['so_phong'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date]['so_phong'] += $so_phong;

                if(!isset($arrByLevel[$item->level][$item->use_date]['so_dem'])){
                    $arrByLevel[$item->level][$item->use_date]['so_dem'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date]['so_dem'] += $so_dem;


                if(!isset($arrByLevel[$item->level][$item->use_date][$item->hotel->stars])){
                    $arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['so_booking'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['so_booking']++;

                if(!isset($arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['doanh_thu'])){
                    $arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['doanh_thu'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['doanh_thu'] += $item->total_price;

                if(!isset($arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['so_phong'])){
                    $arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['so_phong'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['so_phong'] += $so_phong;

                if(!isset($arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['so_dem'])){
                    $arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['so_dem'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date][$item->hotel->stars]['so_dem'] += $so_dem;



        } elseif($type == 3){

                if(!isset($arrByLevel[$item->level][$item->use_date]['so_ve'])){
                    $arrByLevel[$item->level][$item->use_date]['so_ve'] = 0;
                }
                $arrByLevel[$item->level][$item->use_date]['so_ve'] += $so_ve;

                if($item->tickets){
                    foreach($item->tickets as $ticket){
                        if(!isset($arrTicketTypeId[$ticket->ticket_type_id])){
                            $arrTicketTypeId[] = $ticket->ticket_type_id;
                        }
                        if(!isset($arrByLevel[$item->level][$item->use_date][$ticket->ticket_type_id])){
                            $arrByLevel[$item->level][$item->use_date][$ticket->ticket_type_id]['so_booking'] = 0;
                            $arrByLevel[$item->level][$item->use_date][$ticket->ticket_type_id]['so_ve'] = 0;
                        }


                        $arrByLevel[$item->level][$item->use_date][$ticket->ticket_type_id]['so_ve'] += $ticket->amout;


                    }


            }

            } // type = 3
        }

        $arrDate = Helper::getDateFromRange($use_date_from_format, $use_date_to_format);

        $agent = new Agent();
        if($type == 1){
            $view = 'report.tour-dao';
        }elseif($type == 2){
            $view = 'report.hotel';
        }elseif($type == 3){
            $view = 'report.ve';
        }

      //  dd($arrDoanhThuLevel);
        return view($view, compact('listUser', 'arrUser', 'arrSearch', 'arrByLevel', 'arrLevel', 'arrDate', 'tong_bk', 'arrBk', 'tong_doanh_thu', 'tong_nl', 'tong_so_phong', 'tong_so_dem', 'type', 'arrTicketTypeId', 'ticketTypeArr'));

    }

    public function weeklyReport(Request  $request){
        $from = Carbon::now()->subWeek(20)->startOfWeek()->format('Y-m-d');
        $departments = Department::all();
        $arraySearch = $request->all();
        $department_id = $request->get('department_id', $departments->first()->id);

        $tasks = TaskDetail::whereHas('task', function ($taskQuery) use ($department_id){
            return $taskQuery->whereIn('status', [1, 3])->where('department_id', $department_id);
        })->get();

        $createdTasks = TaskDetail::whereHas('task', function ($taskQuery) use ($department_id){
            return $taskQuery->whereIn('status', [1])->whereHas('createdUser', function ($userQuery) use ($department_id){
                return $userQuery->where('department_id', $department_id);
            });
        })->where('status', 1)->where('task_deadline', '>=', date('Y-m-d'))->get();

        $overdueTasks = TaskDetail::whereHas('task', function ($taskQuery) use ($department_id){
            return $taskQuery->whereIn('status', [1, 3])->where('department_id', $department_id);
        })->where('status', 1)->where('task_deadline', '<=', date('Y-m-d'))->get();

        //Get tour summary
        $tourSummary = Booking::where('type', 1)
            ->where('booking.status', '!=', 3)
            ->where('use_date', '>=', $from)
            ->join('tour_system', 'tour_id', '=', 'tour_system.id')
            ->select([DB::raw('tour_system.name as tour_name'), DB::raw('sum(total_price) as total_price')])
            ->groupBy(['tour_system.name'])->get();

        $tourBySource = Booking::where('type', 1)
            ->where('booking.status', '!=', 3)
            ->where('booking.use_date', '>=', $from)
            ->join('tour_system', 'tour_id', '=', 'tour_system.id')
            ->join('customers', 'customer_id', '=', 'customers.id')
            ->select([DB::raw('customers.source as source_name'), DB::raw('tour_system.name as tour_name') , DB::raw('sum(total_price) as total_price')])
            ->groupBy(['customers.source', 'tour_name'])->get();

        //array group by tour_name and source_name
        $tourBySource = $tourBySource->groupBy('tour_name')->map(function ($item) {
            return $item->groupBy('source_name')->map(function ($item) {
                return $item->sum('total_price');
            });
        });

        $hotelSummary = Booking::where('type', 2)->where('booking.status', '!=', 3)
            ->where('use_date', '>=', $from)
            ->sum('total_price');

        $hotelBySource = Booking::where('type', 2)
            ->where('booking.status', '!=', 3)
            ->where('booking.use_date', '>=', $from)
            ->join('customers', 'customer_id', '=', 'customers.id')
            ->select([DB::raw('customers.source as source_name'), DB::raw('sum(total_price) as total_price')])
            ->groupBy(['customers.source'])->get();
        //array group by key value
        $hotelBySource = $hotelBySource->mapWithKeys(function ($item) {
            return [$item['source_name'] => $item['total_price']];
        })->toArray();

        $ticketSummary = Booking::where('booking.type', 3)
            ->where('booking.status', '!=', 3)
            ->where('use_date', '>=', $from)
            ->join('tickets', 'booking.id', '=', 'tickets.booking_id')
            ->join('ticket_type_system', 'ticket_type_system.id', '=', 'tickets.ticket_type_id')
            ->select([DB::raw('ticket_type_system.name as ticket_name'), DB::raw('sum(total_price) as total_price')])
            ->groupBy(['ticket_type_system.name'])->get();

        $ticketBySource = Booking::where('booking.type', 3)
            ->where('booking.status', '!=', 3)
            ->where('booking.use_date', '>=', $from)
            ->join('customers', 'customer_id', '=', 'customers.id')
            ->join('tickets', 'booking.id', '=', 'tickets.booking_id')
            ->join('ticket_type_system', 'ticket_type_system.id', '=', 'tickets.ticket_type_id')
            ->select([DB::raw('customers.source as source_name'), DB::raw('ticket_type_system.name as ticket_name'), DB::raw('sum(total_price) as total_price')])
            ->groupBy(['customers.source', 'ticket_name'])->get();

        //array group by key value
        $ticketBySource = $ticketBySource->groupBy('ticket_name')->map(function ($item) {
            return $item->groupBy('source_name')->map(function ($item) {
                return $item->sum('total_price');
            });
        });

        $carSummary = Booking::where('type', 4)->where('booking.status', '!=', 3)
            ->where('use_date', '>=', $from)
            ->sum('total_price');
        $carBySource = Booking::where('type', 4)
            ->where('booking.status', '!=', 3)
            ->where('booking.use_date', '>=', $from)
            ->join('customers', 'customer_id', '=', 'customers.id')
            ->select([DB::raw('customers.source as source_name'), DB::raw('sum(total_price) as total_price')])
            ->groupBy(['customers.source'])->get();

        //array group by key value
        $carBySource = $carBySource->mapWithKeys(function ($item) {
            return [$item['source_name'] => $item['total_price']];
        })->toArray();

        return view('report.weekly', compact('departments', 'arraySearch', 'tourSummary', 'hotelSummary', 'ticketSummary', 'carSummary', 'tourBySource', 'hotelBySource', 'ticketBySource', 'carBySource', 'tasks', 'createdTasks', 'overdueTasks'));
    }

    public function loinhuanthang(Request $request){
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $dao = $request->dao ?? 0;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));

        //Check if is future -> today
        if((new Carbon($maxdate))->isFuture()){
            $maxdate = date('Y-m-d');
        }

        $maxDay = date('d', strtotime($maxdate));

        $tong_so_ngay = $this->dateDiff($mindate, $maxdate) + 1;

        //dd($mindate, $maxdate);
        if($dao){
            $arrTourId = [1];
            $arrCostType = [1];
        }else{
            $arrTourId = [1, 6, 10, 7, 8, 3, 5];
            $arrCostType = [1, 2, 3, 5];
        }

        $all = Booking::where('use_date', '>=', $mindate)
            ->where('use_date', '<=', $maxdate)
            ->where('type', 1)
            ->whereIn('status',[1, 2]);

        $all = $all->select([
                'use_date',
                'tour_no',
                DB::raw('count(*) as count_booking'),
                DB::raw('sum(adults) as sum_adults'),
                DB::raw('sum(childs) as sum_childs' ),
                DB::raw('sum(total_price) as sum_total_price'),
                DB::raw('sum(hoa_hong_sales) as sum_hoa_hong_sales'),
                DB::raw('sum(tien_thuc_thu) as sum_tien_thuc_thu'),                
                DB::raw('sum(tien_coc) as sum_tien_coc')
            ]
        )->groupBy(['use_date', 'tour_no'])->get();

        $costs  = Cost::where('date_use', '>=', $mindate)
            ->where('date_use', '<=', $maxdate)
            ->where('status', '>', 0)
            ->where('city_id', 1)
            ->select([
                'date_use',
                'tour_no',
                DB::raw('sum(total_money) as sum_total_money')
            ])->groupBy(['date_use', 'tour_no'])->get();

        $data = [];
        foreach ($all as $item){
            $tour_no = $item->tour_no ?? '-';
            $date = $item->use_date;
            $count_booking = $item->count_booking;
            $sum_adults = $item->sum_adults;
            $sum_childs = $item->sum_childs;
            $sum_total_price = $item->sum_total_price;
            $sum_hoa_hong_sales = $item->sum_hoa_hong_sales;
            $sum_tien_thuc_thu = $item->sum_tien_thuc_thu;
            $sum_tien_coc = $item->sum_tien_coc;
            $sum_total_money = $costs[$tour_no] ?? 0;
            $sum_cost = (clone $costs)->where('date_use', $date)->where('tour_no', $tour_no)->first()->sum_total_money ?? 0;

            $data[$date][$tour_no] = [
                'count_booking' => $count_booking,
                'sum_adults' => $sum_adults,
                'sum_childs' => $sum_childs,
                'sum_total_price' => $sum_total_price,
                'sum_hoa_hong_sales' => $sum_hoa_hong_sales,
                'sum_tien_thuc_thu' => $sum_tien_thuc_thu,
                'sum_total_money' => $sum_total_money,
                'sum_cost' => $sum_cost,
                'sum_revenue' => $sum_tien_thuc_thu - $sum_cost + $sum_tien_coc - $sum_hoa_hong_sales,
                'sum_tien_coc' => $sum_tien_coc
            ];
        }
        $minDateFormat = date('d/m/Y', strtotime($mindate));
        $maxDateFormat = date('d/m/Y', strtotime($maxdate));
        $cateList = CostType::where('status', 1)->where('have_partner', 0)->orderBy('display_order')->get();
        return view('report.loi-nhuan-thang', compact('maxDay', 'minDateFormat', 'maxDateFormat', 'month', 'year', 'cateList', 'type', 'data'));

    }
    public function averageGuestByLevel(Request $request){
        $day = date('d');
        $month_do = date('m');
        $arrSearch['type'] = $type = $request->type ? $request->type : 1;
        $arrSearch['user_id_manage'] = $arrSearch['user_id_manage'] = $user_id_manage = $request->user_id_manage ? $request->user_id_manage : null;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['level'] = $level = $request->level ? $request->level : 2;
        $arrSearch['sales'] = $sales = $request->sales ? $request->sales : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1,2];
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : null;
        $arrSearch['tour_cate'] = $tour_cate = $request->tour_cate ?? null;
        $arrSearch['tour_type'] = $tour_type = $request->tour_type ?? [1,2,3];
        $arrSearch['user_id'] = $user_id = $request->user_id ?? null;
        $arrSearch['ctv_id'] = $ctv_id = $request->ctv_id ?? null;
        $arrSearch['sort_by'] = $sort_by = $request->sort_by ? $request->sort_by : 'booking.created_at';

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y');

        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ?? 4;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 1;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 2;
        $arrSearch['debt_type'] = $debt_type = $request->debt_type ?? null;
        if($type == 1){
            $use_df_default = Auth::user()->id == 151 ? date('d/m/Y', strtotime('yesterday')) : date('d/m/Y', time());
            $arrSearch['use_date_from'] = $use_date_from = $request->use_date_from ? $request->use_date_from : $use_df_default;
            $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;
        }
        $arrSearch['created_at'] = $created_at = $request->created_at ? $request->created_at :  null;

        $arrSearch['book_date'] = $book_date = $request->book_date ? $request->book_date :  null;
        $arrSearch['book_date_from'] = $book_date_from = $request->book_date_from ? $request->book_date_from :  null;

        $arrSearch['book_date_to'] = $book_date_to = $request->book_date_to ? $request->book_date_to : null;
        if($type == 2){
            $arrSearch['checkin_from'] = $checkin_from = $request->checkin_from ? $request->checkin_from : null;
            $arrSearch['checkin_to'] = $checkin_to = $request->checkin_to ? $request->checkin_to : $checkin_from;

            $arrSearch['checkout_from'] = $checkout_from = $request->checkout_from ? $request->checkout_from : null;
            $arrSearch['checkout_to'] = $checkout_to = $request->checkout_to ? $request->checkout_to : null;
        }

        $query = Booking::where('booking.type', $type);
        $query->where('booking.city_id', 1);

        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));

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
        }else{
            if($debt_type){
                $arrSearch['debt_type'] = $debt_type;
                $query->join('users', 'users.id', '=', 'booking.user_id')
                    ->where('users.debt_type', $debt_type);
            }
            if($status){

                $arrSearch['status'] = $status;
                $query->whereIn('booking.status', $status);
            }

            if($tour_id){
                $arrSearch['tour_id'] = $tour_id;
                $query->where('tour_id', $tour_id);
            }
            if($tour_cate){
                $arrSearch['tour_cate'] = $tour_cate;
                $query->where('tour_cate', $tour_cate);
            }
            if($tour_type && $type == 1){
                $arrSearch['tour_type'] = $tour_type;
                $query->whereIn('tour_type', $tour_type);
            }

            if($user_id_manage){
                $query->where('booking.user_id_manage', $user_id_manage);
            }

//            if($nguoi_thu_tien){
//                $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien;
//                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
//            }
            if($nguoi_thu_coc){
                $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc;
                $query->where('nguoi_thu_coc', $nguoi_thu_coc);
            }
            if($level && $type == 1){
                $arrSearch['level'] = $level;
                if(!$debt_type){
                    $query->join('users', 'users.id', '=', 'booking.user_id')
                        ->where('users.level', $level);
                }else{
                    $query->where('users.level', $level);
                }
            }
            if($user_id && $user_id > 0){
                $arrSearch['user_id'] = $user_id;
                $query->where('user_id', $user_id);
            }

            $rangeDate = array_unique(explode(' - ', $range_date));
            if (empty($rangeDate[$this->_minDateKey])) {
                //case page is initialized and range_date is empty => this month
                $rangeDate = Carbon::now();
                $query->where('use_date','=', $rangeDate->format('Y-m-d'));
            } elseif (count($rangeDate) === 1) {
                //case page is initialized and range_date has value,
                //when counting the number of elements in rangeDate = 1 => only select a day
                $query->where('use_date','=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
            } else {
                $query->where('use_date','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                $query->where('use_date', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
            }

            if($ctv_id){
                $query->where('ctv_id', $ctv_id);
            }
        }

        $query->orderBy($sort_by, 'desc');
        $allList = $query->select('booking.*', 'booking.id as booking_id')->get();
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

        $items  = $query->select([
            DB::raw('users.name as user_name'),
            DB::raw('sum(adults) as sum_adults'),
            DB::raw('sum(childs) as sum_childs'),
            DB::raw('sum(meals) as sum_meals'),
            DB::raw('sum(meals_te) as sum_meals_te'),
            DB::raw('sum(cap_nl) as sum_cap_nl'),
            DB::raw('sum(cap_te) as sum_cap_te')
        ])->groupBy('user_name')->get();

        $tong_hoa_hong_cty = $tong_hoa_hong_sales = $tong_so_nguoi = $tong_phan_an = $tong_coc = $tong_phan_an_te = 0 ;
        $tong_thuc_thu = $tong_hoa_hong_chup =  0;
        $cap_nl = $cap_te = $tong_te =  0;

        $tong_hdv_thu = $tong_thao_thu = 0;

        $listUser = User::whereIn('level', [2,7])->where('status', 1)->get();

        if($level){
            $listUser = User::where('level', $level)->where('status', 1)->get();
        }
        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }

        $userArr = [];
        $ghep = $vip = $thue = $tong_vip= 0;
        $listHDV = User::where('hdv', 1)->where('status', 1)->get();
        $canoList = Partner::getList(['cano'=> 1]);
        // cal doanh so doi tac
        $arrDs = [];
        if($time_type == 1){
            foreach($items as $item){
                if(in_array($item->tour_type, [1, 2]) && !in_array($item->level, [1, 5]) ){
                    if(!isset($arrDs[$item->user_id])){
                        $arrDs[$item->user_id] = $item->adults;
                    }else{
                        $arrDs[$item->user_id] += $item->adults;
                    }
                }
            }
        }
        $tourSystem = TourSystem::where('status', 1)->orderBy('display_order')->get();
        return view('report.average-customer-by-level', compact( 'items', 'arrSearch', 'type', 'listUser', 'level', 'cap_nl', 'cap_te', 'tong_te', 'arrUser', 'listHDV', 'tong_phan_an_te', 'tong_hdv_thu', 'canoList', 'time_type', 'month', 'year', 'arrDs', 'day', 'tong_thao_thu','month_do', 'ctvList', 'ghep', 'vip', 'thue', 'tong_vip', 'debt_type', 'tourSystem'));
    }
}
