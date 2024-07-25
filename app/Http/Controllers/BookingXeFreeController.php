<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
use App\Models\DonTienFree;
use App\Models\GrandworldSchedule;
use App\Models\Logs;
use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class BookingXeFreeController extends Controller
{

    private $_minDateKey = 0;
    private $_maxDateKey = 1;

    public function create(Request $request)
    {
        $user = Auth::user();
        $id = $request->booking_id;
        $detailBooking = Booking::find($id);
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $listTag = Location::where('city_id', $city_id)->where('status', 1)->get();
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
        $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        $cateList = CarCate::where('type', 1)->get();
        // get list booking xe
        $bkList = DonTienFree::where('booking_id', $id)->orderBy('use_date')->get();
        $user_id_default = $user->role == 1 && $user->level == 6 ? $user->id : null;
        return view("booking-xe-free.add", compact('listUser', 'listTag', 'ctvList', 'driverList', 'cateList', 'detailBooking', 'bkList', 'user_id_default'));
    }
    public function edit(Request $request)
    {
        $user = Auth::user();
        $id = $request->id;
        $detail = DonTienFree::find($id);
        $city_id = $detail->city_id;

        $booking_id = $detail->booking_id;
        $detailBooking = Booking::find($booking_id);
        $listTag = Location::where('city_id', 1)->where('status', 1)->get();
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
        $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        $cateList = CarCate::where('type', 1)->get();
        // get list booking xe
        $bkList = DonTienFree::where('booking_id', $booking_id)->orderBy('use_date')->get();
        return view("booking-xe-free.edit", compact('listUser', 'listTag', 'ctvList', 'driverList', 'cateList', 'detailBooking', 'bkList', 'detail', 'city_id'));
    }
    public function store(Request $request){
        $user = Auth::user();
        $dataArr = $request->all();

        $this->validate($request,[
            'car_cate_id' => 'required',
            'use_date' => 'required',
            'location_id' => 'required',
            'don_gio' => 'required',
            'location_id_2' => 'required'
        ],
        [

            'car_cate_id.required' => 'Bạn chưa chọn loại xe',
            'use_date.required' => 'Bạn chưa nhập ngày đi',
            'location_id.required' => 'Bạn chưa chọn nơi đón',
            'don_gio.required' => 'Bạn chưa nhập giờ đi',
            'location_id_2.required' => 'Bạn chưa chọn nơi trả',
        ]);

        $tmpDate = explode('/', $dataArr['use_date']);
        $use_date = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        $detailBooking = Booking::find($dataArr['booking_id']);
        $dataArr['cost'] =isset($dataArr['cost']) ? (int) str_replace(',', '', $dataArr['cost']) : 0 ;
        $arr = [
            'booking_id' => $dataArr['booking_id'],
            'location_id' => $dataArr['location_id'],
            'location_id_2' => $dataArr['location_id_2'],
            'type' => 1, // don
            'use_date' => $use_date. " ".$dataArr['don_gio'].":".$dataArr['don_phut'].":00",
            'car_cate_id' => $dataArr['car_cate_id'],
            'use_time' => $dataArr['don_gio'].":".$dataArr['don_phut'],
            'notes' => $dataArr['notes'],
            'user_id' => $detailBooking->user_id,
            'phone' => $detailBooking->phone,
            'name' => $dataArr['name'],
            'status' => isset($dataArr['status']) ? $dataArr['status'] : 1,
            'use_date_time' => $use_date." ".$dataArr['don_gio'].":".$dataArr['don_phut'].":00",
            'cost' => $dataArr['cost']
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

        Session::flash('message', 'Tạo cuốc xe thành công');
        return redirect()->route('booking-xe-free.create', ['booking_id' => $dataArr['booking_id']]);
    }

    public function index(Request $request){

        // $query = DonTienFree::all();
        // foreach($query as $rs){
        //     $rs->update([
        //         'use_date_time' => $rs->use_date." ".$rs->use_time.":00"
        //     ]);
        // }

        $day = date('d');
        $month_do = date('m');
        $arrSearch['type'] = $type = $request->type ?? null;

        $arrSearch['no_driver'] = $no_driver = $request->no_driver ?? null;

        $arrSearch['status'] = $status = $request->status ?? [1];
        $arrSearch['user_id'] = $user_id = $request->user_id ?? null;
        $arrSearch['driver_id'] = $driver_id = $request->driver_id ?? null;
        $arrSearch['phone'] = $phone = $request->phone ?? null;
        $arrSearch['car_cate_id'] = $car_cate_id = $request->car_cate_id ?? null;
        $arrSearch['time_type'] = $time_type = $request->time_type ??  1;

        $use_df_default = Auth::user()->id == 151 ? date('d/m/Y', strtotime('yesterday')) : date('d/m/Y', time());

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y'); //this month



        $query = DonTienFree::whereRaw('1');

        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));

        if($status){
            $query->whereIn('status', $status);
        }
        if($type){
            $query->whereIn('type', $type);
        }
        if($phone){
            $arrSearch['phone'] = $phone;
            $query->where('phone', $phone);
        }
        if($driver_id){
            $query->where('driver_id', $driver_id);
        }
        if(Auth::user()->role < 3){
            if($user_id && $user_id > 0){
                $query->where('user_id', $user_id);
            }
        }else{
            $arrSearch['user_id'] = Auth::user()->id;
            $query->where('user_id', Auth::user()->id);
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


        $items  = $query->orderBy('use_date_time')->get();
        $total_cost = 0;
        foreach($items as $item){
            if($item->status != 3){
                $total_cost += $item->cost;
            }
            //update level
            if($item->user && $item->level != $item->user->level){
                $item->update(['level' => $item->user->level]);
            }

        }
        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6])->where('status', 1)->get();
        $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        $carCate = CarCate::where('type', 1)->get();

        return view("booking-xe-free.index", compact('items', 'arrSearch', 'time_type', 'driverList', 'carCate', 'listUser', 'total_cost'));
    }
    public function update(Request $request){
        $user = Auth::user();
        $dataArr = $request->all();

        $this->validate($request,[
            'car_cate_id' => 'required',
            'use_date' => 'required',
            'location_id' => 'required',
            'don_gio' => 'required',
            'location_id_2' => 'required'
        ],
        [

            'car_cate_id.required' => 'Bạn chưa chọn loại xe',
            'use_date.required' => 'Bạn chưa nhập ngày đi',
            'location_id.required' => 'Bạn chưa chọn nơi đón',
            'don_gio.required' => 'Bạn chưa nhập giờ đi',
            'location_id_2.required' => 'Bạn chưa chọn nơi trả',
        ]);

        $tmpDate = explode('/', $dataArr['use_date']);
        $use_date = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        $detailBooking = Booking::find($dataArr['booking_id']);
        $rs = DonTienFree::find($dataArr['id']);

        $oldData = $rs->toArray();

        $dataArr['cost'] =isset($dataArr['cost']) ? (int) str_replace(',', '', $dataArr['cost']) : 0 ;
        $arrUpdate = [
            'booking_id' => $dataArr['booking_id'],
            'location_id' => $dataArr['location_id'],
            'location_id_2' => $dataArr['location_id_2'],
            'type' => 1, // don
            'use_date' => $use_date,
            'car_cate_id' => $dataArr['car_cate_id'],
            'use_time' => $dataArr['don_gio'].":".$dataArr['don_phut'],
            'notes' => $dataArr['notes'],
            'use_date_time' => $use_date." ".$dataArr['don_gio'].":".$dataArr['don_phut'].":00",
            'status' => $dataArr['status'],
            'cost' => $dataArr['cost'],
            'phone' => $detailBooking->phone
        ];
        if(Auth::user()->role == 1){
            $arrUpdate['driver_id'] = $dataArr['driver_id'];
        }


        $rs->update($arrUpdate);

        //write logs
        unset($dataArr['_token']);
        $contentDiff = array_diff_assoc($arrUpdate, $oldData);
        //dd($contentDiff);
        if(!empty($contentDiff)){
            $oldContent = [];

            foreach($contentDiff as $k => $v){
                if(isset($oldData[$k])){
                    $oldContent[$k] = $oldData[$k];
                }
            }
            Logs::create([
                'table_name' => 'don_tien_free',
                'user_id' => Auth::user()->id,
                'action' => 2,
                'content' => json_encode($contentDiff),
                'old_content' => json_encode($oldContent),
                'object_id' => $rs->id
            ]);
        }

        Session::flash('message', 'Cập nhật cuốc xe thành công');
        return redirect()->route('booking-xe-free.create', ['booking_id' => $dataArr['booking_id']]);
    }
}
