<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Airport;
use App\Models\BookingLogs;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Ctv;
use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserNotification;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class BookingVmbController extends Controller
{


    public function index(Request $request)
    {

        $day = date('d');
        $month_do = date('m');
        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));

        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1, 2];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $arrSearch['ctv_id'] = $ctv_id = $request->ctv_id ?? null;
        $arrSearch['partner_id'] = $partner_id = $request->partner_id ?? null;
        $arrSearch['phone'] = $phone = $request->phone ? $request->phone : null;
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 2;

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y');

        $arrSearch['location_id'] = $location_id = $request->location_id ?? null;
        $arrSearch['location_id_2'] = $location_id_2 = $request->location_id_2 ?? null;
        //$arrSearch['tour_type'] = $tour_type = $request->tour_type ?? [1,2,3];
        $query = Booking::where(['type' => 6]);
        $arrSearch['unc0'] = $unc0 = $request->unc0 ? $request->unc0 : null;
        if($unc0 == 1){
            $query->where('check_unc', 0);
        }
        if($id_search){
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptx", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
            $arrSearch['use_date_from'] = $arrSearch['use_date_to'] = null;
        }else{
            if($city_id){
                $query->where('city_id', $city_id);
            }
            if($partner_id){
                $query->where('partner_id', $partner_id);
            }
            if($location_id){
                $query->where('location_id', $location_id);
            }
            if($location_id_2){
                $query->where('location_id_2', $location_id_2);
            }
            if($status){
                $query->whereIn('status', $status);
            }
            if($phone){
                $query->where('phone', $phone);
            }
            if($nguoi_thu_tien){
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }

            if(Auth::user()->role < 3 || Auth::user()->id == 23 ){ // 23 la Tuan Vu
                if($user_id && $user_id > 0){
                    $arrSearch['user_id'] = $user_id;
                    $query->where('user_id', $user_id);
                }
            }else{
                $arrSearch['user_id'] = Auth::user()->id;
                $query->where('user_id', Auth::user()->id);
            }

            $minDateKey = 0;
            $maxDateKey = 1;

            $rangeDate = array_unique(explode(' - ', $range_date));
            if (empty($rangeDate[$minDateKey])) {
                //case page is initialized and range_date is empty => today
                $rangeDate = Carbon::now();
                $query->where('checkin','=', $rangeDate->format('Y-m-d'));
                $arrSearch['range_date'] = $rangeDate->format('d/m/Y') . " - " . $rangeDate->format('d/m/Y');
            } elseif (count($rangeDate) === 1) {
                //case page is initialized and range_date has value,
                //when counting the number of elements in rangeDate = 1 => only select a day
                $query->where('checkin','=', Carbon::createFromFormat('d/m/Y', $rangeDate[$minDateKey])->format('Y-m-d'));
                $arrSearch['range_date'] = $rangeDate[$minDateKey] . " - " . $rangeDate[$minDateKey];
            } else {
                $query->where('checkin','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$minDateKey])->format('Y-m-d'));
                $query->where('checkin', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$maxDateKey])->format('Y-m-d'));
            }
        }


        $query->orderBy('checkin');

        $allList = $query->get();

        //update level
        foreach($allList as $bk){
            if($bk->user && $bk->level != $bk->user->level){
                $bk->update(['level' => $bk->user->level]);
            }
        }
        $items  = $query->paginate(300);

        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();

        $agent = new Agent();
        $airportList = Airport::where('status', 1)->get();

        if($agent->isMobile()){

            $view = $city_id == 1 ? "booking-vmb.m-index" : "booking-vmb.m-index-other";
        }else{

            $view = $city_id == 1 ? "booking-vmb.index" : "booking-vmb.index-other";
        }
        $partnerList = Partner::getList(['cost_type_id'=> 57, 'city_id' => $city_id]);
        $partnerName = [];
        foreach($partnerList as $dr){
            $partnerName[$dr->id] = $dr->name;
        }

        return view($view, compact( 'items', 'arrSearch', 'month', 'year', 'time_type', 'listUser', 'airportList'));

    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);

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
        $airportList = Airport::where('status', 1)->get();

        $partnerList = Partner::getList(['cost_type_id'=> 52, 'city_id' => $city_id]);
        $view = $city_id == 1 ? "booking-vmb.add" : "booking-vmb.add-other";
        $user_id_default = $user->role == 1 && $user->level == 6 ? $user->id : null;
        return view($view, compact('listUser', 'ctvList', 'partnerList', 'city_id', 'airportList', 'user_id_default'));
    }

    public function edit($id, Request $request)
    {

        $city_id = 1;
        $detail = Booking::find($id);
        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6])->where('status', 1)->get();


        if($detail->user_id != Auth::user()->id && Auth::user()->role == 2){
            dd('Bạn không có quyền truy cập.');
        }
        $arrSearch = $request->all();

        $airportList = Airport::where('status', 1)->get();

        $partnerList = Partner::getList(['cost_type_id'=> 52, 'city_id' => $detail->city_id]);

        $view = $city_id == 1 ? "booking-vmb.edit" : "booking-vmb.edit-other";
        return view($view, compact( 'detail', 'listUser', 'arrSearch', 'airportList', 'city_id'));
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();
        if(Auth::user()->role == 1){
            $this->validate($request,[
                'location_id' => 'required', // loai xe
                'location_id_2' => 'required',
                'name' => 'required',
                'phone' => 'required',
                'user_id' => 'required',
            ],
            [
                'location_id.required' => 'Bạn chưa chọn sân bay xuất phát',
                'location_id_2.required' => 'Bạn chưa sân bay đến',
                'name.required' => 'Bạn chưa nhập tên khách hàng',
                'phone.required' => 'Bạn chưa nhập số điện thoại',
                'user_id.required' => 'Bạn chưa chọn Sales',
            ]);
        }else{
            $this->validate($request,[
                'location_id' => 'required', // loai xe
                'location_id_2' => 'required',
                'name' => 'required',
                'phone' => 'required',
            ],
            [
                'location_id.required' => 'Bạn chưa chọn sân bay xuất phát',
                'location_id_2.required' => 'Bạn chưa sân bay đến',
                'name.required' => 'Bạn chưa nhập tên khách hàng',
                'phone.required' => 'Bạn chưa nhập số điện thoại',
            ]);
        }

        $dataArr['adult_cost'] = isset($dataArr['adult_cost']) ? (int) str_replace(',', '', $dataArr['adult_cost']) : null;
        $dataArr['child_cost'] = isset($dataArr['child_cost']) ? (int) str_replace(',', '', $dataArr['child_cost']) : null;
        $dataArr['infant_cost'] = isset($dataArr['infant_cost']) ? (int) str_replace(',', '', $dataArr['infant_cost']) : null;

        $dataArr['price_adult'] = isset($dataArr['price_adult']) ? (int) str_replace(',', '', $dataArr['price_adult']) : null;
        $dataArr['price_child'] = isset($dataArr['price_child']) ? (int) str_replace(',', '', $dataArr['price_child']) : null;
        $dataArr['price_infant'] = isset($dataArr['price_infant']) ? (int) str_replace(',', '', $dataArr['price_infant']) : null;

        $dataArr['total_price_adult'] = isset($dataArr['total_price_adult']) ? (int) str_replace(',', '', $dataArr['total_price_adult']) : null;
        $dataArr['total_price_child'] = isset($dataArr['total_price_child']) ? (int) str_replace(',', '', $dataArr['total_price_child']) : null;
        $dataArr['total_price_infant'] = isset($dataArr['total_price_infant']) ? (int) str_replace(',', '', $dataArr['total_price_infant']) : null;

        $dataArr['total_price'] =(int) str_replace(',', '', $dataArr['total_price']);

        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);

        $tmpDate = explode('/', $dataArr['checkin']);

        $dataArr['checkin'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        if(isset($dataArr['checkout'])){
            $tmpDate = explode('/', $dataArr['checkout']);
            $dataArr['checkout'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }

        $dataArr['name'] = ucwords($dataArr['name']);
        $dataArr['type'] = 6;

        $dataArr['tour_cate'] = isset($dataArr['tour_cate']) ? 2 : 1;
        $dataArr['meals'] = $dataArr['discount'] = 0;

        $rs = Booking::create($dataArr);

        $booking_id = $rs->id;

        //$this->replyMessCar($dataArr, $rs); //chatbot
        unset($dataArr['_token']);
        //store log
        $rsLog = BookingLogs::create([
            'booking_id' => $booking_id,
            'content' => json_encode($dataArr),
            'user_id' => $user->id,
            'action' => 1
        ]);
        Session::flash('message', 'Tạo mới thành công');
        $checkin = date('d/m/Y', strtotime($dataArr['checkin']));

        return redirect()->route('booking-vmb.index', ['checkin_from' => $checkin]);
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();
        if(Auth::user()->role == 1){
            $this->validate($request,[
                'location_id' => 'required', // loai xe
                'location_id_2' => 'required',
                'name' => 'required',
                'phone' => 'required',
                'user_id' => 'required',
            ],
            [
                'location_id.required' => 'Bạn chưa chọn sân bay xuất phát',
                'location_id_2.required' => 'Bạn chưa sân bay đến',
                'name.required' => 'Bạn chưa nhập tên khách hàng',
                'phone.required' => 'Bạn chưa nhập số điện thoại',
                'user_id.required' => 'Bạn chưa chọn Sales',
            ]);
        }else{
            $this->validate($request,[
                'location_id' => 'required', // loai xe
                'location_id_2' => 'required',
                'name' => 'required',
                'phone' => 'required',
            ],
            [
                'location_id.required' => 'Bạn chưa chọn sân bay xuất phát',
                'location_id_2.required' => 'Bạn chưa sân bay đến',
                'name.required' => 'Bạn chưa nhập tên khách hàng',
                'phone.required' => 'Bạn chưa nhập số điện thoại',
            ]);
        }

        $dataArr['adult_cost'] = isset($dataArr['adult_cost']) ? (int) str_replace(',', '', $dataArr['adult_cost']) : null;
        $dataArr['child_cost'] = isset($dataArr['child_cost']) ? (int) str_replace(',', '', $dataArr['child_cost']) : null;
        $dataArr['infant_cost'] = isset($dataArr['infant_cost']) ? (int) str_replace(',', '', $dataArr['infant_cost']) : null;

        $dataArr['price_adult'] = isset($dataArr['price_adult']) ? (int) str_replace(',', '', $dataArr['price_adult']) : null;
        $dataArr['price_child'] = isset($dataArr['price_child']) ? (int) str_replace(',', '', $dataArr['price_child']) : null;
        $dataArr['price_infant'] = isset($dataArr['price_infant']) ? (int) str_replace(',', '', $dataArr['price_infant']) : null;

        $dataArr['total_price_adult'] = isset($dataArr['total_price_adult']) ? (int) str_replace(',', '', $dataArr['total_price_adult']) : null;
        $dataArr['total_price_child'] = isset($dataArr['total_price_child']) ? (int) str_replace(',', '', $dataArr['total_price_child']) : null;
        $dataArr['total_price_infant'] = isset($dataArr['total_price_infant']) ? (int) str_replace(',', '', $dataArr['total_price_infant']) : null;

        $dataArr['total_price'] =(int) str_replace(',', '', $dataArr['total_price']);

        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);

        $tmpDate = explode('/', $dataArr['checkin']);

        $dataArr['checkin'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        if(isset($dataArr['checkout'])){
            $tmpDate = explode('/', $dataArr['checkout']);
            $dataArr['checkout'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }

        $dataArr['name'] = ucwords($dataArr['name']);
        $dataArr['type'] = 6;

        $dataArr['meals'] = $dataArr['discount'] = 0;

        $dataArr['tour_cate'] = isset($dataArr['tour_cate']) ? 2 : 1;

        if($user->role < 3 || Auth::user()->id == 23){
            $dataArr['user_id'] = $dataArr['user_id'];
        }else{
            $dataArr['user_id'] = $user->id;
        }
        $dataArr['name'] = ucwords($dataArr['name']);

        $model = Booking::find($dataArr['id']);
        $oldData = $model->toArray();

        unset($dataArr['_token']);

        $model->update($dataArr);

        $booking_id = $model->id;

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

        $checkin = date('d/m/Y', strtotime($dataArr['checkin']));
        Session::flash('message', 'Cập nhật thành công');
        return redirect()->route('booking-vmb.index', ['checkin_from' => $checkin]);
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
        return redirect()->route('booking-vmb.index', ['use_date_from' => $use_date]);
    }
}
