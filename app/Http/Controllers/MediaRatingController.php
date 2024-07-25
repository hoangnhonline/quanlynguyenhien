<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\MediaCate;
use App\Models\Media;
use App\Models\Rating;
use App\Models\MediaRating;
use App\Models\Account;
use App\User;
use App\Models\SmsPayment;
use Jenssegers\Agent\Agent;
use Helper, File, Session, Auth, Image, Hash;

class MediaRatingController extends Controller
{
    private $_minDateKey = 0;
    private $_maxDateKey = 1;
    public function index(Request $request)
    {
        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $arrSearch['use_date'] = $use_date = $request->use_date ? $request->use_date : date('d/m/Y', strtotime('-2 day'));
        $tmpDate = explode('/', $use_date);
        $use_date_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        $user_id = $request->user_id ?? null;
        $ip = $request->ip ?? null;
        $stars = $request->stars ?? null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y'); //this month
        $query = MediaRating::whereRaw('1');

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

        if($ip){
            $query->where('ip', $ip);
        }
        if($user_id){
            $query->where('user_id', $user_id);
        }
        if($stars){
            $query->where('stars', $stars);
        }
        $items = $query->orderBy('ip')->orderBy('user_id')->paginate(1000);
        $userList = User::whereIn('id', [32, 33, 41, 58, 65, 76, 258, 406, 447])->get();

        $agent = new Agent();
        if($agent->isMobile()){
            $view = 'media-rating.m-index';
        }else{
            $view = 'media-rating.index';
        }
        return view($view, compact( 'items', 'use_date', 'ip', 'user_id', 'stars', 'arrSearch', 'time_type', 'userList'));
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
        $model = MediaRating::find($id);
        $model->delete();
        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('media-rating.index');
    }
}
