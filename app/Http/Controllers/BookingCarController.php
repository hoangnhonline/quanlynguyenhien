<?php

namespace App\Http\Controllers;

use App\Models\DonTienFree;
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
use App\Models\CarCate;
use App\Models\BoatPrices;
use App\Models\Drivers;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Ctv;
use App\Models\BookingLocation;
use App\Models\GrandworldSchedule;
use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use function Psy\debug;

class BookingCarController extends Controller
{

    private $_minDateKey = 0;
    private $_maxDateKey = 1;

    public function index(Request $request)
    {

        $day = date('d');
        $month_do = date('m');
        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y');;
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
        $arrSearch['driver_id'] = $driver_id = $request->driver_id ? $request->driver_id : null;
        $arrSearch['car_cate_id'] = $car_cate_id = $request->car_cate_id ? $request->car_cate_id : null;
        $arrSearch['no_driver'] = $no_driver = $request->no_driver ? $request->no_driver : null;
        //$arrSearch['tour_type'] = $tour_type = $request->tour_type ?? [1,2,3];

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y'); //this month

        $query = Booking::where(['type' => 4])->whereNotIn('tour_cate', [2, 3]);
        $arrSearch['unc0'] = $unc0 = $request->unc0 ? $request->unc0 : null;
        if ($unc0 == 1) {
            $query->where('check_unc', 0);
        }
        if ($id_search) {
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptx", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
            $arrSearch['use_date_from'] = $arrSearch['use_date_to'] = null;
        } else {
            if ($city_id) {
                $query->where('city_id', $city_id);
            }
            if ($partner_id) {
                $query->where('partner_id', $partner_id);
            }
            if ($no_driver) {
                $query->where('driver_id', 0);
            }
            if ($car_cate_id) {
                $query->where('car_cate_id', $car_cate_id);
            }
            if ($status) {
                $query->whereIn('status', $status);
            }
            if ($phone) {
                $query->where('phone', $phone);
            }
            if ($nguoi_thu_tien) {
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }
            if ($nguoi_thu_coc) {
                $query->where('nguoi_thu_coc', $nguoi_thu_coc);
            }
            if ($ctv_id) {
                $query->where('ctv_id', $ctv_id);
            }
            if ($driver_id > 0) {
                $query->where('driver_id', $driver_id);
            }
            if (Auth::user()->role < 3 || Auth::user()->id == 23) { // 23 la Tuan Vu
                if ($user_id && $user_id > 0) {
                    $arrSearch['user_id'] = $user_id;
                    $query->where('user_id', $user_id);
                }
            } else {
                $arrSearch['user_id'] = Auth::user()->id;
                $query->where('user_id', Auth::user()->id);
            }

            $rangeDate = array_unique(explode(' - ', $range_date));
            if (empty($rangeDate[$this->_minDateKey])) {
                //case page is initialized and range_date is empty => today
                $rangeDate = Carbon::now();
                $query->where('use_date', '=', $rangeDate->format('Y-m-d'));
                $time_type = 3;
                $month = $rangeDate->format('m');
                $year = $rangeDate->year;
            } elseif (count($rangeDate) === 1) {
                //case page is initialized and range_date has value,
                //when counting the number of elements in rangeDate = 1 => only select a day
                $use_date = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey]);
                $query->where('use_date', '=', $use_date->format('Y-m-d'));
                $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
                $time_type = 3;
                $month = $use_date->format('m');
                $year = $use_date->year;
            } else {
                $query->where('use_date', '>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                $query->where('use_date', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
                $time_type = 1;
                $month = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('m');
                $year = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->year;
            }

        }


        $query->orderBy('use_date_time')->orderBy('use_date');

        $allList = $query->get();

        if (Auth::user()->role == 1) {
            $ctvList = Ctv::where('status', 1)->where('leader_id', 18)->get();
        } else {
            if (Auth::user()->id == 64) {
                $leader_id = 3;
            } else {
                $leader_id = Auth::user()->id;
            }
            $ctvList = Ctv::where('status', 1)->where('leader_id', $leader_id)->get();
        }

        $items = $query->paginate(300);

        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6, 7])->where('status', 1)->get();
        $tong_hoa_hong_cty = $tong_tien_ban = $tong_tien_goc = $tong_coc = $tong_thuc_thu = 0;

        $arrThuCoc = $arrThuTien = [];

        if ($allList->count() > 0) {

            foreach ($allList as $bk) {
                if ($bk->status < 3) {

                    $tong_hoa_hong_cty += $bk->hoa_hong_cty;
                    $tong_coc += $bk->tien_coc;
                    $tong_thuc_thu += $bk->con_lai;
                    $tong_tien_goc += $bk->total_cost;
                    $tong_tien_ban += $bk->total_price;
                    if ($bk->nguoi_thu_coc) {
                        if (!isset($arrThuCoc[$bk->nguoi_thu_coc])) $arrThuCoc[$bk->nguoi_thu_coc] = 0;
                        $arrThuCoc[$bk->nguoi_thu_coc] += $bk->tien_coc;
                    }
                    if ($bk->nguoi_thu_tien) {
                        if (!isset($arrThuTien[$bk->nguoi_thu_tien])) $arrThuTien[$bk->nguoi_thu_tien] = 0;
                        $arrThuTien[$bk->nguoi_thu_tien] += $bk->con_lai;
                    }
                }
            }
        }

        $agent = new Agent();
        $carCate = CarCate::where('type', 1)->get();


        $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        $driverArrName = [];
        foreach ($driverList as $dr) {
            $driverArrName[$dr->id] = $dr->name;
        }
        $arrDriver = [];
        foreach ($items as $bk) {
            //update level
            if ($bk->user && $bk->level != $bk->user->level) {
                $bk->update(['level' => $bk->user->level]);
            }
            if (!isset($arrDriver[$bk->driver_id])) {
                $arrDriver[$bk->driver_id]['so_lan_chay'] = 0;
                $arrDriver[$bk->driver_id]['tong_tien'] = 0;
                $arrDriver[$bk->driver_id]['so_tien_tx_thu'] = 0;
                $arrDriver[$bk->driver_id]['so_tien_sales_thu'] = 0;
                $arrDriver[$bk->driver_id]['so_tien_cty_thu'] = 0;
            }
            $arrDriver[$bk->driver_id]['so_lan_chay']++;
            $arrDriver[$bk->driver_id]['tong_tien']++;
            if ($bk->nguoi_thu_tien == 1) {
                $arrDriver[$bk->driver_id]['so_tien_sales_thu'] += $bk->total_price;
            } elseif ($bk->nguoi_thu_tien == 2) {
                $arrDriver[$bk->driver_id]['so_tien_cty_thu'] += $bk->total_price;
            } else {
                $arrDriver[$bk->driver_id]['so_tien_tx_thu'] += $bk->total_price;
            }
        }
        $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        $driverArrName = [];
        foreach ($driverList as $dr) {
            $driverArrName[$dr->id] = $dr->name;
        }
        $arrDriver = [];
        $t_chuyen = $t_tong = $t_cty = $t_sales = $t_tx = $t_dieuhanh = $t_chuathu = 0;
        foreach ($items as $bk) {
            if ($bk->status != 3) {
                $t_chuyen++;
                if (!isset($arrDriver[$bk->driver_id])) {
                    $arrDriver[$bk->driver_id]['so_lan_chay'] = 0;
                    $arrDriver[$bk->driver_id]['tong_tien'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_tx_thu'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_sales_thu'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_cty_thu'] = 0;
                    $arrDriver[$bk->driver_id]['so_tien_dieuhanh_thu'] = 0;
                }
                $arrDriver[$bk->driver_id]['so_lan_chay']++;
                $arrDriver[$bk->driver_id]['tong_tien'] += $bk->total_price;
                $t_tong += $bk->total_price;
                $t_chuathu += $bk->con_lai;
                if ($bk->nguoi_thu_tien == 1) {
                    $arrDriver[$bk->driver_id]['so_tien_sales_thu'] += $bk->total_price;
                    $t_sales += $bk->total_price;
                } elseif ($bk->nguoi_thu_tien == 2) {
                    $arrDriver[$bk->driver_id]['so_tien_cty_thu'] += $bk->total_price;
                    $t_cty += $bk->total_price;
                } elseif ($bk->nguoi_thu_tien == 3) {
                    $arrDriver[$bk->driver_id]['so_tien_tx_thu'] += $bk->total_price;
                    $t_tx += $bk->total_price;
                } elseif ($bk->nguoi_thu_tien == 4) {
                    $arrDriver[$bk->driver_id]['so_tien_dieuhanh_thu'] += $bk->total_price;
                    $t_dieuhanh += $bk->total_price;
                }
            }

        }
        // dd($arrDriver);
        //if(Auth::user()->id == 21){
        if ($agent->isMobile()) {

            $view = $city_id == 1 ? "booking-car.m-index" : "booking-car.m-index-other";
        } else {

            $view = $city_id == 1 ? "booking-car.index" : "booking-car.index-other";
        }
        $nhaxeList = Partner::getList(['cost_type_id' => 52, 'city_id' => $city_id]);
        $nhaxeName = [];
        foreach ($nhaxeList as $dr) {
            $nhaxeName[$dr->id] = $dr->name;
        }

        return view($view, compact('items', 'arrSearch', 'listUser', 'carCate', 'tong_hoa_hong_cty', 'driverList', 'time_type', 'month', 'year', 'driverArrName', 'arrDriver', 't_chuyen', 't_tong', 't_sales', 't_tx', 't_cty', 't_dieuhanh', 'ctvList', 't_chuathu', 'city_id', 'nhaxeList', 'nhaxeName', 'arrThuTien', 'arrThuCoc', 'tong_tien_goc', 'tong_tien_ban', 'tong_coc', 'tong_thuc_thu'));

    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $listTag = Location::where('city_id', $city_id)->where('status', 1)->get();
        if (Auth::user()->role == 1) {
            $ctvList = Ctv::where('status', 1)->where('leader_id', 18)->get();
        } else {
            if (Auth::user()->id == 64) {
                $leader_id = 3;
            } else {
                $leader_id = Auth::user()->id;
            }
            $ctvList = Ctv::where('status', 1)->where('leader_id', $leader_id)->get();
        }
        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6])->where('status', 1)->get();
        $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        $cateList = CarCate::where('type', 1)->get();

        $nhaxeList = Partner::getList(['cost_type_id' => 52, 'city_id' => $city_id]);
        $view = $city_id == 1 ? "booking-car.add" : "booking-car.add-other";
        // check customer_id
        $customer_id = $request->customer_id ?? null;
        $customerDetail = [];
        if ($customer_id > 0) {
            if (Auth::user()->role == 1) {
                $customerDetail = Customer::find($customer_id)->toArray();
            } else {
                $customerDetail = Customer::where(['created_user' => Auth::user()->id, 'id' => $customer_id])->first()->toArray();
            }
        }
        $user_id_default = $user->role == 1 && $user->level == 6 ? $user->id : null;
        return view($view, compact('listUser', 'listTag', 'ctvList', 'driverList', 'cateList', 'city_id', 'nhaxeList', 'customerDetail', 'user_id_default'));
    }

    public function edit($id, Request $request)
    {

        $tagSelected = [];
        $keyword = $request->keyword ?? null;
        $detail = Booking::find($id);
        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6])->where('status', 1)->get();
        if (Auth::user()->role == 1) {
            $ctvList = Ctv::where('status', 1)->where('leader_id', 18)->get();
        } else {
            if (Auth::user()->id == 64) {
                $leader_id = 3;
            } else {
                $leader_id = Auth::user()->id;
            }
            $ctvList = Ctv::where('status', 1)->where('leader_id', $leader_id)->get();
        }

        if ($detail->user_id != Auth::user()->id && Auth::user()->role == 2) {
            dd('Bạn không có quyền truy cập.');
        }
        $arrSearch = $request->all();

        $carCate = CarCate::where('type', 1)->get();
        $driverList = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        $location = $detail->locationList;
        $locationArr = [];
        foreach ($location as $lo) {
            $locationArr[] = $lo->location_id;
        }
        $city_id = $detail->city_id;
        $listTag = Location::where('city_id', $city_id)->where('status', 1)->get();
        $nhaxeList = Partner::getList(['cost_type_id' => 52, 'city_id' => $city_id]);
        // xu ly gio don
        $tmpTime = explode(':', $detail->time_pickup);

        $timeOld = true;
        $don_gio = $don_phut = null;
        if (isset($tmpTime[1])) {
            $timeOld = false;
            $don_gio = $tmpTime[0];
            $don_phut = $tmpTime[1];
        }
        $view = $city_id == 1 ? "booking-car.edit" : "booking-car.edit-other";
        return view($view, compact('detail', 'listUser', 'arrSearch', 'listTag', 'ctvList', 'carCate', 'driverList', 'locationArr', 'city_id', 'nhaxeList', 'timeOld', 'don_gio', 'don_phut'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();

        $this->validate($request, [
            'car_cate_id' => 'required', // loai xe
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',
            'user_id' => 'required',
            //'location_id' => 'required',
            //'location_id_2' => 'required',
        ],
            [
                'car_cate_id.required' => 'Bạn chưa chọn loại xe',
                'name.required' => 'Bạn chưa nhập tên',
                'phone.required' => 'Bạn chưa nhập điện thoại',
                'use_date.required' => 'Bạn chưa nhập ngày đi',
                'user_id.required' => 'Bạn chưa chọn Sales',
                //'location_id.required' => 'Bạn chưa chọn nơi đón',
                //'location_id_2.required' => 'Bạn chưa chọn nơi trả',
                //'nguoi_thu_tien.required' => 'Bạn chưa chọn người thu tiền',
            ]);

        $dataArr['total_price'] = (int)str_replace(',', '', $dataArr['total_price']);
        $dataArr['total_cost'] = isset($dataArr['total_cost']) ? (int)str_replace(',', '', $dataArr['total_cost']) : null;
        $dataArr['hoa_hong_cty'] = isset($dataArr['hoa_hong_cty']) ? (int)str_replace(',', '', $dataArr['hoa_hong_cty']) : null;
        $dataArr['tien_coc'] = isset($dataArr['tien_coc']) ? (int)str_replace(',', '', $dataArr['tien_coc']) : null;
        $dataArr['con_lai'] = isset($dataArr['con_lai']) ? (int)str_replace(',', '', $dataArr['con_lai']) : 0;
        $dataArr['meals'] = 0;
        $dataArr['discount'] = 0;
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $tmpDate = explode('/', $dataArr['use_date']);

        $dataArr['use_date'] = $tmpDate[2] . '-' . $tmpDate[1] . '-' . $tmpDate[0];
        if (isset($dataArr['ngay_coc'])) {
            $tmpDate = explode('/', $dataArr['ngay_coc']);
            $dataArr['ngay_coc'] = $tmpDate[2] . '-' . $tmpDate[1] . '-' . $tmpDate[0];
        }
        if ($dataArr['book_date']) {
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2] . '-' . $tmpDate[1] . '-' . $tmpDate[0];
        } else {
            $dataArr['book_date'] = date('Y-m-d');
        }

        if ($user->role < 3 || Auth::user()->id == 23) {   // 23 = Tuấn Vũ
            if ($dataArr['user_id'] > 0) {
                $detailUserBook = Account::find($dataArr['user_id']);
                $dataArr['level'] = $detailUserBook->level;
                $dataArr['user_id_manage'] = $detailUserBook->user_id_manage;
                $dataArr['phone_sales'] = $dataArr['phone_sales'] ?? $detailUserBook->phone;
            }
        } else {
            // $detailUserBook = Account::find($user->id);
            $dataArr['user_id'] = $user->id;
            $dataArr['level'] = $user->level;
            $dataArr['phone_sales'] = $dataArr['phone_sales'] ?? $user->phone;
            //   $dataArr['user_id_manage'] = $detailUserBook->user_id_manage;
        }
        $dataArr['name'] = ucwords($dataArr['name']);
        $dataArr['type'] = 4;
        $dataArr['tour_cate'] = 1;

        $locationArr = $dataArr['location_id'];
        unset($dataArr['location_id']);
        //luu gio don
        $dataArr['time_pickup'] = $dataArr['don_gio'] . ":" . $dataArr['don_phut'];
        $dataArr['use_date_time'] = $dataArr['use_date'] . " " . $dataArr['don_gio'] . ":" . $dataArr['don_phut'] . ":00";


        $rs = Booking::create($dataArr);

        $booking_id = $rs->id;


        if (!empty($locationArr)) {
            foreach ($locationArr as $location_id) {
                if ($location_id > 0) {
                    BookingLocation::create(['location_id' => $location_id, 'booking_id' => $booking_id]);
                }

            }
        }
        //$this->replyMessCar($dataArr, $rs); //chatbot
        unset($dataArr['_token']);
        //store log
        $rsLog = BookingLogs::create([
            'booking_id' => $booking_id,
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
        //             'booking_id' => $booking_id,
        //             'date_use' => $rs->use_date,
        //             'data' => json_encode($dataArr),
        //             'type' => 1,
        //             'is_read' => 0
        //         ]);
        //     }
        // }

        // store customer
        if (!isset($dataArr['customer_id']) || $dataArr['customer_id'] == "") {

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
        return redirect()->route('booking-car.index', ['use_date_from' => $use_date, 'city_id' => $dataArr['city_id']]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();
        $this->validate($request, [
            'car_cate_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'use_date' => 'required',
            'user_id' => 'required',
            // 'location_id' => 'required',
            // 'location_id_2' => 'required',
        ],
            [
                'car_cate_id.required' => 'Bạn chưa chọn loại xe',
                'name.required' => 'Bạn chưa nhập tên',
                'phone.required' => 'Bạn chưa nhập điện thoại',
                'use_date.required' => 'Bạn chưa nhập ngày đi',
                // 'location_id.required' => 'Bạn chưa chọn nơi đón',
                // 'location_id_2.required' => 'Bạn chưa chọn nơi trả',
                //'nguoi_thu_tien.required' => 'Bạn chưa chọn người thu tiền',
                'user_id.required' => 'Bạn chưa chọn Sales',
            ]);
        $dataArr['total_price'] = (int)str_replace(',', '', $dataArr['total_price']);
        $dataArr['total_cost'] = isset($dataArr['total_cost']) ? (int)str_replace(',', '', $dataArr['total_cost']) : null;
        $dataArr['hoa_hong_cty'] = isset($dataArr['hoa_hong_cty']) ? (int)str_replace(',', '', $dataArr['hoa_hong_cty']) : null;
        $dataArr['tien_coc'] = (int)str_replace(',', '', $dataArr['tien_coc']);
        $dataArr['con_lai'] = (int)str_replace(',', '', $dataArr['con_lai']);
        $dataArr['meals'] = 0;
        $dataArr['discount'] = 0;
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $tmpDate = explode('/', $dataArr['use_date']);
        $dataArr['use_date'] = $tmpDate[2] . '-' . $tmpDate[1] . '-' . $tmpDate[0];
        if ($dataArr['ngay_coc']) {
            $tmpDate = explode('/', $dataArr['ngay_coc']);
            $dataArr['ngay_coc'] = $tmpDate[2] . '-' . $tmpDate[1] . '-' . $tmpDate[0];
        }
        if ($dataArr['book_date']) {
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2] . '-' . $tmpDate[1] . '-' . $tmpDate[0];
        } else {
            $dataArr['book_date'] = date('Y-m-d');
        }

        if ($user->role < 3 || Auth::user()->id == 23) {
            $dataArr['user_id'] = $dataArr['user_id'];
        } else {
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
        // $model->update($dataArr);

        $locationArr = $dataArr['location_id'];
        unset($dataArr['location_id']);
        //dd($dataArr);
        $dataArr['time_pickup'] = $dataArr['don_gio'] . ":" . $dataArr['don_phut'];
        $dataArr['use_date_time'] = $dataArr['use_date'] . " " . $dataArr['don_gio'] . ":" . $dataArr['don_phut'] . ":00";

        $model->update($dataArr);

        $booking_id = $model->id;
        BookingLocation::where('booking_id', $booking_id)->delete();
        if (!empty($locationArr)) {
            foreach ($locationArr as $location_id) {
                if ($location_id > 0) {
                    BookingLocation::create(['location_id' => $location_id, 'booking_id' => $booking_id]);
                }

            }
        }
        unset($dataArr['don_gio']);
        unset($dataArr['don_phut']);
        $contentDiff = array_diff_assoc($dataArr, $oldData);
        $booking_id = $model->id;

        if (!empty($contentDiff)) {
            $oldContent = [];

            foreach ($contentDiff as $k => $v) {
                $oldContent[$k] = $oldData[$k];
            }
            $rsLog = BookingLogs::create([
                'booking_id' => $booking_id,
                'content' => json_encode(['old' => $oldContent, 'new' => $contentDiff]),
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
        return redirect()->route('booking-car.index', ['use_date_from' => $use_date, 'user_id' => $dataArr['user_id'], 'city_id' => $model->city_id]);
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

    public function calendar(Request $request)
    {
        $filters = $request->all(0);
        $filters['driver_id'] = $filters['driver_id'] ?? 171;

        if ($request->ajax()) {
            $filters['range_date'] = Carbon::parse($request->startStr)->format('d/m/Y') . ' - ' . Carbon::parse($request->endStr)->format('d/m/Y');

            $dontienfree = DonTienFree::with('booking', 'location', 'location2')
                ->where('status', '>', 0)
                ->where('driver_id', $filters['driver_id'])
                ->when($filters['range_date'] ?? false, function ($query) use ($filters) {
                    return $this->_queryRangeDate($query, $filters);
                })
                ->when($filters['status'] ?? false, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->get();
            $booking = Booking::with('location', 'location2')
                ->where('status', '>', 0)
                ->where('driver_id', $filters['driver_id'])
                ->when($filters['range_date'] ?? false, function ($query) use ($filters) {
                    return $this->_queryRangeDate($query, $filters);
                })
                ->when($filters['status'] ?? false, function ($query, $status) {
                    return $query->where('status', $status);
                })
//                ->whereNotIn('id', $dontienfree->pluck('booking_id')->toArray())
                ->get();

            return response()->json($booking->concat($dontienfree));

        }

        $drivers = Drivers::where('status', 1)->orderBy('is_verify', 'desc')->get();
        return view('booking-car.calendar', compact('filters', 'drivers'));
    }


    private function _queryRangeDate($qr, &$filters)
    {
        return $qr->when($filters['range_date'] ?? false, function ($query, $range_date) use (&$filters) {
            $rangeDate = array_unique(explode(' - ', $range_date));
            if (empty($rangeDate[$this->_minDateKey])) {
                //case page is initialized and range_date is empty => today
                $rangeDate = Carbon::now();
                $query->whereDate('use_date', '=', $rangeDate->format('Y-m-d'));
            } elseif (count($rangeDate) === 1) {
                //case page is initialized and range_date has value,
                //when counting the number of elements in rangeDate = 1 => only select a day
                $use_date = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey]);
                $query->whereDate('use_date', '=', $use_date->format('Y-m-d'));
            } else {
                $query->whereDate('use_date', '>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                $query->whereDate('use_date', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
            }
        });
    }
}
