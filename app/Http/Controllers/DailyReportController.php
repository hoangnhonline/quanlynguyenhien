<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use App\Models\ReportSetting;
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

class DailyReportController extends Controller
{
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
    public function index(Request $request)
    {
        //report
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $maxDay = date('d', strtotime($maxdate));
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['city_id'] = $city_id = $request->city_id ? $request->city_id : 1;
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : null;
        $arrSearch['tour_type'] = $tour_type = $request->tour_type ? $request->tour_type : null;
        $arrSearch['tour_cate'] = $tour_cate = $request->tour_cate ? $request->tour_cate : null;
        $query = Booking::where('type', 1)->where('status', '<', 3)->where('city_id',$city_id);
        $costQuery = Cost::where('tour_id', '>', 0)->where('status', '!=', 0)->where('city_id',$city_id);
        if(!empty($tour_id)){
            $query->where('tour_id', $tour_id);
            $costQuery->where('tour_id', $tour_id);
        }
        if(!empty($tour_type)){
            $query->where('tour_type', $tour_type);
        }
        if(!empty($tour_cate)){
            $query->where('tour_cate', $tour_cate);
        }
        $tours = TourSystem::where('status', 1)->where('city_id',$city_id)->get();

        if($time_type == 1){ // theo thangs
            $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
            $arrSearch['use_date_to'] = $use_date_to = date('d/m/Y', strtotime($maxdate));
            $query->where('use_date','>=', $mindate);
            $query->where('use_date', '<=', $maxdate);

            $costQuery->where('date_use','>=', $mindate);
            $costQuery->where('date_use','<=', $maxdate);
            $fromDate = $mindate;
            $toDate = $maxdate;
        }elseif($time_type == 2){ // theo khoang ngay
            $arrSearch['use_date_from'] = $use_date_from = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
            $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;

            if($use_date_from){
                $arrSearch['use_date_from'] = $use_date_from;
                $tmpDate = explode('/', $use_date_from);
                $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                $query->where('use_date','>=', $use_date_from_format);

                $costQuery->where('date_use','>=', $use_date_from_format);
                $fromDate = $use_date_from_format;
                $toDate = $use_date_from_format;
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
                $costQuery->where('date_use','<=', $use_date_to_format);
                $toDate = $use_date_to_format;
            }
        }else{
            $arrSearch['use_date_from'] = $use_date_from = $arrSearch['use_date_to'] = $use_date_to = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());

            $arrSearch['use_date_from'] = $use_date_from;
            $tmpDate = explode('/', $use_date_from);
            $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
            $query->where('use_date','=', $use_date_from_format);
            $costQuery->where('date_use','=', $use_date_from_format);
            $fromDate = $use_date_from_format;
            $toDate = $use_date_from_format;
        }

        $totalIncoming = $query->sum('total_price');
        $totalCost  = $costQuery->sum('total_money');
        $totalRevenue = $query->sum(\DB::raw('case when tour_type = 3 then adults * 200000 + childs * 100000 else adults * 100000 + childs * 50000 end'));

        //Get list of users
        $levels = with(clone $query)->select('level')->distinct()->get()->pluck('level');
        $byLevelChartData = [];
        foreach ($levels as $level){
            $levelLabel = '';
            switch ($level){
                case 1: $levelLabel = 'CTV'; break;
                case 2: $levelLabel = 'DT'; break;
                case 6: $levelLabel = 'NV'; break;
                case 7: $levelLabel = 'GB'; break;
            }
            $value = with(clone $query)->where('level', $level)->sum('total_price');
            if(!empty($value)){
                $byLevelChartData[$levelLabel] = $value;
            }
        }

        //Get list of tour IDS
        $tourIds = with(clone $query)->select('tour_id')->distinct()->get()->pluck('tour_id');
        $byTourTypeChartData = [];
        foreach ($tourIds as $tourId){
            $tour = TourSystem::find($tourId);
            if(!empty($tour)){
                $byTourTypeChartData[$tour->name] = with(clone $query)->where('tour_id', $tourId)->sum('total_price');
            }
        }

        //Get list of tour IDS
        $tourIds = with(clone $query)->select('tour_id')->distinct()->get()->pluck('tour_id');
        $byGuestsChartData = [];
        foreach ($tourIds as $tourId){
            $tour = TourSystem::find($tourId);
            if(!empty($tour)){
                $byGuestsChartData[$tour->name] = with(clone $query)->where('tour_id', $tourId)->sum(\DB::raw('adults + childs/2'));
            }
        }

        //Detail chart
        $detailCostChart = [];
        $detailIncommingChart = [];
        $detailChartLabels = [];
        $runningDate = new Carbon($fromDate);
        while($runningDate->isBefore($toDate)){
            $detailChartLabels[] = $runningDate->format('d/m');
            $detailIncommingChart[] = Booking::where('type', 1)->where('status', '<', 3)->where('use_date', $runningDate->format('Y-m-d'))->sum('total_price');
            $detailCostChart[] = Cost::where('tour_id', '>', 0)->where('status', '!=', 0)->where('date_use', $runningDate->format('Y-m-d'))->sum('total_money');
            $runningDate = $runningDate->addDay(1);
        }
        $totalBooking = with(clone $query)->count();
        $totalAdults = with(clone $query)->sum('adults');
        $totalChilds =with(clone $query)->sum('childs');

        $target = ReportSetting::where('module', 'tour')->where('month', $month)->where('year', $year)->first();

        return view('daily-report.index', compact('month', 'year', 'time_type' , 'city_id', 'arrSearch', 'tours', 'target', 'totalCost', 'totalIncoming', 'totalRevenue', 'byLevelChartData', 'byTourTypeChartData', 'byGuestsChartData', 'detailCostChart', 'detailChartLabels', 'detailIncommingChart','totalBooking', 'totalAdults', 'totalChilds'));
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function hotel(Request $request)
    {
        //report
        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $maxDay = date('d', strtotime($maxdate));
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $query = Booking::where('type', 2)->where('status', '<', 3);
        $costQuery = Cost::where('tour_id', '>', 0)->where('status', '!=', 0);

        if($time_type == 1){ // theo thangs
            $arrSearch['use_date_from'] = $use_date_from = $date_use = date('d/m/Y', strtotime($mindate));
            $arrSearch['use_date_to'] = $use_date_to = date('d/m/Y', strtotime($maxdate));
            $query->where('use_date','>=', $mindate);
            $query->where('use_date', '<=', $maxdate);

            $costQuery->where('date_use','>=', $mindate);
            $costQuery->where('date_use','<=', $maxdate);
            $fromDate = $mindate;
            $toDate = $maxdate;
        }elseif($time_type == 2){ // theo khoang ngay
            $arrSearch['use_date_from'] = $use_date_from = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
            $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;

            if($use_date_from){
                $arrSearch['use_date_from'] = $use_date_from;
                $tmpDate = explode('/', $use_date_from);
                $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                $query->where('use_date','>=', $use_date_from_format);

                $costQuery->where('date_use','>=', $use_date_from_format);
                $fromDate = $use_date_from_format;
                $toDate = $use_date_from_format;
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
                $costQuery->where('date_use','<=', $use_date_to_format);
                $toDate = $use_date_to_format;
            }
        }else{
            $arrSearch['use_date_from'] = $use_date_from = $arrSearch['use_date_to'] = $use_date_to = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());

            $arrSearch['use_date_from'] = $use_date_from;
            $tmpDate = explode('/', $use_date_from);
            $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
            $query->where('use_date','=', $use_date_from_format);
            $costQuery->where('date_use','=', $use_date_from_format);
            $fromDate = $use_date_from_format;
            $toDate = $use_date_from_format;
        }

        $totalIncoming = $query->sum('total_price');
        $totalRevenue = $query->sum('hoa_hong_cty');

        //Get list of users
        $levels = with(clone $query)->select('level')->distinct()->get()->pluck('level');
        $byLevelChartData = [];
        foreach ($levels as $level){
            $levelLabel = '';
            switch ($level){
                case 1: $levelLabel = 'CTV'; break;
                case 2: $levelLabel = 'DT'; break;
                case 6: $levelLabel = 'NV'; break;
                case 7: $levelLabel = 'GB'; break;
            }
            $value = with(clone $query)->where('level', $level)->sum('total_price');
            if(!empty($value)){
                $byLevelChartData[$levelLabel] = $value;
            }
        }

        //Get list of tour IDS
        $tourIds = with(clone $query)->select('hotel_id')->distinct()->get()->pluck('hotel_id');
        $byTourTypeChartData = [];
//        foreach ($tourIds as $tourId){
//            $tour = Hotels::find($tourId);
//            $byTourTypeChartData[$tour->name] = with(clone $query)->where('hotel_id', $tourId)->sum('total_price');
//        }

        //Get list of tour IDS
        $stars = [1,2,3,4,5];
        $byStarChartData = [];
        foreach ($stars as $star){
            $byStarChartData[$star . '*'] = with(clone $query)->whereHas('hotel', function ($query) use ($star){
                return $query->where('stars', $star);
            })->sum('total_price');
        }

        //Detail chart
        $detailCostChart = [];
        $detailIncommingChart = [];
        $detailRevenueChart = [];
        $detailChartLabels = [];
        $runningDate = new Carbon($fromDate);
        while($runningDate->isBefore($toDate)){
            $detailChartLabels[] = $runningDate->format('d/m');
            $detailIncommingChart[] = Booking::where('type', 2)->where('status', '<', 3)->where('use_date', $runningDate->format('Y-m-d'))->sum('total_price');
            $detailRevenueChart[] = Booking::where('type', 2)->where('status', '<', 3)->where('use_date', $runningDate->format('Y-m-d'))->sum('hoa_hong_cty');
            $detailCostChart[] = Cost::where('tour_id', '>', 0)->where('status', '!=', 0)->where('date_use', $runningDate->format('Y-m-d'))->sum('total_money');
            $runningDate = $runningDate->addDay(1);
        }
        $totalBooking = with(clone $query)->count();
        $totalAdults = with(clone $query)->sum('adults');
        $totalChilds =with(clone $query)->sum('childs');
        $target = ReportSetting::where('module', 'hotel')->where('month', $month)->where('year', $year)->first();


        return view('daily-report.hotel', compact('month', 'year', 'target', 'time_type', 'arrSearch', 'totalIncoming', 'totalRevenue', 'byLevelChartData', 'byTourTypeChartData', 'byStarChartData', 'detailCostChart', 'detailChartLabels', 'detailIncommingChart', 'detailRevenueChart','totalBooking', 'totalAdults', 'totalChilds'));
    }
}
