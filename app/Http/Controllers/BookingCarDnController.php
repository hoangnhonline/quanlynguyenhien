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
class BookingCarDnController extends Controller
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
        $arrSearch['driver_id'] = $driver_id = $request->driver_id ? $request->driver_id : null;
        $arrSearch['car_cate_id'] = $car_cate_id = $request->car_cate_id ? $request->car_cate_id : null;
        $arrSearch['tour_cate'] = $tour_cate = $request->tour_cate ? $request->tour_cate : null;
        $arrSearch['no_driver'] = $no_driver = $request->no_driver ? $request->no_driver : null;
        //$arrSearch['tour_type'] = $tour_type = $request->tour_type ?? [1,2,3];
        $query = Booking::where(['type' => 4, 'city_id' => 1]);
        $arrSearch['unc0'] = $unc0 = $request->unc0 ? $request->unc0 : null;
        if($unc0 == 1){
            $query->where('check_unc', 0);
        }
        if($id_search){
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptx", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
        }else{
            if($no_driver){
                $arrSearch['no_driver'] = $no_driver;
                $query->where('driver_id', 0);
            }
            if($car_cate_id){
                $arrSearch['car_cate_id'] = $car_cate_id;
                $query->where('car_cate_id', $car_cate_id);
            }
            if($tour_cate){
                $arrSearch['tour_cate'] = $tour_cate;
                $query->where('tour_cate', $tour_cate);
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
            if($driver_id > 0){
                $query->where('driver_id', $driver_id);
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
        $tong_hoa_hong_sales = $tong_hoa_hong_chup = 0;
        if($allList->count() > 0){

            foreach($allList as $bk){
                if($bk->status < 3){

                    $tong_hoa_hong_sales += $bk->hoa_hong_sales;
                    $tong_hoa_hong_chup += $bk->hoa_hong_chup;
                }
            }
        }
        $agent = new Agent();
        $carCate = CarCate::all();

            //if(Auth::user()->id == 21){
            if($agent->isMobile()){
                $view = 'booking-car.m-index';
            }else{
                $view = 'booking-car.index';
            }

            $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
            $driverArrName = [];
            foreach($driverList as $dr){
                $driverArrName[$dr->id] = $dr->name;
            }
            $arrDriver = [];
            foreach($items as $bk){
                if(!isset($arrDriver[$bk->driver_id])){
                    $arrDriver[$bk->driver_id]['so_lan_chay'] = 0;
                    $arrDriver[$bk->driver_id]['tong_tien'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_tx_thu'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_sales_thu'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_cty_thu'] = 0;
                }
                $arrDriver[$bk->driver_id]['so_lan_chay']++;
                $arrDriver[$bk->driver_id]['tong_tien']++;
                if($bk->nguoi_thu_tien == 1){
                    $arrDriver[$bk->driver_id]['so_tien_sales_thu'] += $bk->total_price;
                }elseif($bk->nguoi_thu_tien == 2){
                    $arrDriver[$bk->driver_id]['so_tien_cty_thu'] += $bk->total_price;
                }else{
                    $arrDriver[$bk->driver_id]['so_tien_tx_thu'] += $bk->total_price;
                }
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
                        $arrDriver[$bk->driver_id]['so_tien_dieuhanh_thu'] = 0;
                    }
                    $arrDriver[$bk->driver_id]['so_lan_chay']++;
                    $arrDriver[$bk->driver_id]['tong_tien'] += $bk->total_price;
                    $t_tong += $bk->con_lai;
                    if($bk->nguoi_thu_tien == 1){
                        $arrDriver[$bk->driver_id]['so_tien_sales_thu'] += $bk->total_price;
                        $t_sales += $bk->total_price;
                    }elseif($bk->nguoi_thu_tien == 2){
                        $arrDriver[$bk->driver_id]['so_tien_cty_thu'] += $bk->total_price;
                        $t_cty += $bk->total_price;
                    }elseif($bk->nguoi_thu_tien == 3){
                        $arrDriver[$bk->driver_id]['so_tien_tx_thu'] += $bk->total_price;
                        $t_tx += $bk->total_price;
                    }elseif($bk->nguoi_thu_tien == 4){
                        $arrDriver[$bk->driver_id]['so_tien_dieuhanh_thu'] += $bk->total_price;
                        $t_dieuhanh += $bk->total_price;
                    }
                }

            }
            return view($view, compact( 'items', 'arrSearch', 'listUser', 'carCate', 'tong_hoa_hong_sales', 'driverList', 'time_type', 'month','year', 'driverArrName', 'arrDriver', 't_chuyen', 't_tong', 't_sales', 't_tx', 't_cty', 't_dieuhanh', 'ctvList'));

    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $listTag = Location::where('city_id', $user->city_id)->where('status', 1)->get();
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
        $cateList = CarCate::all();

        return view("booking-car.add", compact('listUser', 'listTag', 'ctvList', 'driverList', 'cateList'));
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
        $listTag = Location::where('status', 1)->get();
        if($detail->user_id != Auth::user()->id && Auth::user()->role == 2){
            dd('Bạn không có quyền truy cập.');
        }
        $arrSearch = $request->all();

        $carCate = CarCate::all();
        $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        return view('booking-car.edit', compact( 'detail', 'listUser', 'arrSearch', 'listTag', 'ctvList', 'carCate', 'driverList'));
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();

        $this->validate($request,[
            'car_cate_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',
            'location_id' => 'required',
            'location_id_2' => 'required',
        ],
        [
            'car_cate_id.required' => 'Bạn chưa chọn loại xe',
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'use_date.required' => 'Bạn chưa nhập ngày đi',
            'location_id.required' => 'Bạn chưa chọn nơi đón',
            'location_id_2.required' => 'Bạn chưa chọn nơi trả',
            //'nguoi_thu_tien.required' => 'Bạn chưa chọn người thu tiền',
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
            $detailUserBook = Account::find($dataArr['user_id']);
            $dataArr['level'] = $detailUserBook->level;
            $dataArr['user_id_manage'] = $detailUserBook->user_id_manage;
            $dataArr['phone_sales'] = $dataArr['phone_sales'] ?? $detailUserBook->phone;
        }else{
           // $detailUserBook = Account::find($user->id);
            $dataArr['user_id'] = $user->id;
            $dataArr['level'] = $user->level;
            $dataArr['phone_sales'] = $dataArr['phone_sales'] ?? $user->phone;
         //   $dataArr['user_id_manage'] = $detailUserBook->user_id_manage;
        }
        $dataArr['name'] = ucwords($dataArr['name']);
        $dataArr['type'] = 4;
        $rs = Booking::create($dataArr);
        $id = $rs->id;
        //$this->replyMessCar($dataArr, $rs); //chatbot
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
        //$userIdPush = Helper::getUserIdPushNoti($id, 1);
      // dd($userIdPush);
        // foreach($userIdPush as $idPush){
        //     if($idPush > 0){
        //         UserNotification::create([
        //             'title' => $user->name." vừa tạo PTX".$id,
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


        Session::flash('message', 'Tạo mới thành công');
        $use_date = date('d/m/Y', strtotime($dataArr['use_date']));
        // if($use_date  == date('d/m/Y', strtotime('tomorrow'))
        //     || $use_date  == date('d/m/Y', time())
        // ){
        //    $this->curlExport();
        // }
        return redirect()->route('booking-car.index', ['use_date_from' => $use_date]);
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();
         $this->validate($request,[
            'car_cate_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',
            'location_id' => 'required',
            'location_id_2' => 'required',
        ],
        [
            'car_cate_id.required' => 'Bạn chưa chọn loại xe',
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'use_date.required' => 'Bạn chưa nhập ngày đi',
            'location_id.required' => 'Bạn chưa chọn nơi đón',
            'location_id_2.required' => 'Bạn chưa chọn nơi trả',
            //'nguoi_thu_tien.required' => 'Bạn chưa chọn người thu tiền',
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
        $dataArr['type'] = 4;

        $model = Booking::find($dataArr['id']);
        $oldData = $model->toArray();

        unset($dataArr['_token']);
        //
        //unset($oldData['updated_at']);

        $use_date_old = $model->use_date;

        $dataArr['export'] = 2;
        //$dataArr['notes'] = 'Updated.'.$dataArr['notes'];
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
            //$userIdPush = Helper::getUserIdPushNoti($booking_id);
            // dd($userIdPush);
            // foreach($userIdPush as $idPush){
            //     if($idPush > 0){
            //         UserNotification::create([
            //             'title' => 'PTX'.$booking_id.' vừa được '. $user->name." cập nhật",
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
        return redirect()->route('booking-car.index', ['use_date_from' => $use_date, 'user_id' => $dataArr['user_id']]);
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
        return redirect()->route('booking-car.index', ['use_date_from' => $use_date]);
    }
}
