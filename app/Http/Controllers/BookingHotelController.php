<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rating;
use App\Models\Hotels;
use App\Models\Rooms;
use App\Models\BookingRooms;
use App\Models\BookingLogs;
use App\Models\RoomsPrice;
use App\Models\Location;
use App\Models\Tour;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Account;
use App\Models\BookingPayment;
use App\Models\BookingRelated;
use App\Models\Ctv;
use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserNotification;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class BookingHotelController extends Controller
{
    private $_minDateKey = 0;
    private $_maxDateKey = 1;

    public function checkPayment(Request $request)
    {
        $id = $request->id;
        $detail = Booking::find($id);
        $total_price = $detail->total_price;
        $tong_tien_goc = $detail->ptt_tong_tien_goc;

        $paymentList = BookingPayment::where('booking_id', $id)->where('status', 1)->where('type', 2)->get(); // AUTO mới hợp lệ
        $thu = $chi = 0;
        foreach($paymentList as $pay){
            if($pay->flow == 1){
                $thu += $pay->amount;
            }
            if($pay->flow == 2){
                $chi += $pay->amount;
            }
        }
        $strError = "";
        $arrError = [];
        if($chi > 0){
            if($chi > $tong_tien_goc){
                $arrError[] = "CHI > VỐN";
            }elseif($chi < $tong_tien_goc){
                $arrError[] = "CHI < VỐN";
            }
        }else{
            $arrError[] = "Chưa CHI";
        }


        if($thu < $total_price){
            $arrError[] = "THU < TIỀN BÁN";
        }elseif($thu > $total_price){
            $arrError[] = "THU < TIỀN BÁN";
        }

        if(!empty($arrError)){
            $strError = implode(', ', $arrError);
        }
        return $strError;
    }
    public function saveBookingCode(Request $request){
        $id = $request->id;
        $booking_code = $request->booking_code;
        $detail = Booking::find($id);
        $oldContent = ['booking_code' => $detail->booking_code];

        $detail->update(['booking_code' => $booking_code]);
        $newContent = ['booking_code' => $booking_code];
        //ghi log
        BookingLogs::create([
            'booking_id' =>  $id,
            'content' =>json_encode(['old' => $oldContent, 'new' => $newContent]),
            'action' => 2,
            'user_id' => Auth::user()->id
        ]);

    }
    public function calCommissionHotel(Request $request){
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $all = Booking::where('type', 2)->where('checkin', '>=', $from_date)->where('checkin', '<=', $to_date)
       ->whereNotIn('status',[3,4])->get();

        foreach($all as $bk){

            $user_id = $bk->user_id;
            if(in_array($user_id, [18, 6, 3, 451, 610])){ // 6 la Yen Vi, 3 là Phụng
                $percentCty = 100;
            }else{
                $percentCty = 30;
            }
            $i = 0;
            $i++;
            $rooms = $bk->rooms;
            $tong_hoa_hong = 0;
            foreach($rooms as $r){
                if($r->total_price == 0){
                    $r->update(['total_price' => $r->price_sell*$r->nights]);
                }
                $price_sell = $r->price_sell;
                $nights = $r->nights;
                $total_price = $r->total_price;
                $original_price = $r->original_price;
                if(strlen($original_price) < 5 && $original_price > 0){
                    $original_price = $original_price*1000;
                }
                if($original_price == 0){
                    $original_price = $price_sell-50000;
                }elseif($original_price > $price_sell){
                    $original_price = $original_price/$nights/$r->room_amount;
                }
                $tong_gia_goc = $original_price*$r->room_amount*$r->nights;
                $tong_hoa_hong+= $total_price - $tong_gia_goc;

            }
            var_dump($original_price, $price_sell, '------');
            $hoa_hong_cty = $percentCty*$tong_hoa_hong/100;
            $hoa_hong_sales = $tong_hoa_hong-$hoa_hong_cty;
            var_dump($i, $bk->id, $tong_hoa_hong, $hoa_hong_cty, $hoa_hong_sales);
            echo "<hr>";
            $bk->update(['hoa_hong_sales' => $hoa_hong_sales, 'hoa_hong_cty' => $hoa_hong_cty]);

        }

    }
    public function ajaxGetRoomPrices(Request $request){
        $room_id = $request->room_id;
        $checkin = $request->checkin;
        $checkout = $request->checkout;
        $tmp1 = explode('/', $checkin);
        $tmp2 = explode('/', $checkout);
        $checkin_format = $tmp1[2].'-'.$tmp1[1].'-'.$tmp1[0];
        $checkout_format = $tmp2[2].'-'.$tmp2[1].'-'.$tmp2[0];

        $dataArr = RoomsPrice::getPriceFromTo($room_id, $checkin_format, $checkout_format, 2, 0);
        // $totalPrice = 0;
        // foreach($dataArr as $date => $price){
        //     $totalPrice+= $price;
        // }
        // $priceAver = $totalPrice/count($dataArr);
        $firstPriceDate = reset($dataArr);

        $partnerList = Partner::getList(['cost_type_id'=> 48, 'city_id' => $city_id]);
        $partnerArrName = ['1' => 'Trực tiếp KS'];

        foreach($partnerList as $partner){
            $partnerArrName[$partner->id] = $partner->name;
        }
        return view('booking-hotel.ajax-get-room-prices', compact('dataArr', 'firstPriceDate', 'partnerArrName'));
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index(Request $request)
    {
        $day = date('d');
        $month_do = date('m');
        $arrSearch['type'] = $type = 2;
        $arrSearch['coc'] = $coc = $request->coc ? $request->coc : null;
        $arrSearch['level'] = $level = $request->level ? $request->level : null;
        $arrSearch['error'] = $error = $request->error ? $request->error : null;
        $arrSearch['thuc_thu'] = $thuc_thu = $request->thuc_thu ?? null;
        $arrSearch['co_coc'] = $co_coc = $request->co_coc ?? null;
        $arrSearch['is_edit'] = $is_edit = $request->is_edit ?? null;
        $arrSearch['ok'] = $ok = $request->ok ? $request->ok : null;
        $arrSearch['sales'] = $sales = $request->sales ? $request->sales : null;
        $arrSearch['keyword'] = $keyword = $request->keyword ? $request->keyword : null;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1,2,4];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $arrSearch['ctv_id'] = $ctv_id = $request->ctv_id ?? null;
        $arrSearch['call_status'] = $call_status = $request->call_status ? $request->call_status : null;
        $arrSearch['phone'] = $phone = $request->phone ? $request->phone : null;
        $arrSearch['sort_by'] = $sort_by = $request->sort_by ? $request->sort_by : 'created_at';
        $arrSearch['hotel_id'] = $hotel_id = $request->hotel_id ? $request->hotel_id : null;
        $arrSearch['hotel_book'] = $hotel_book = $request->hotel_book ? $request->hotel_book : null;
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ?? null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ?? null;
        $arrSearch['nguoi_chi_tien'] = $nguoi_chi_tien = $request->nguoi_chi_tien ?? null;
        $arrSearch['nguoi_chi_coc'] = $nguoi_chi_coc = $request->nguoi_chi_coc ?? null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 'checkin';
        $arrSearch['checkin_from'] = $checkin_from = $request->checkin_from ? $request->checkin_from : null;
        $arrSearch['checkin_to'] = $checkin_to = $request->checkin_to ? $request->checkin_to : $checkin_from;

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y');

         $arrSearch['hh0'] = $hh0 = $request->hh0 ? $request->hh0 : null;

        // ngày cọc và ngày thanh toán - dành cho kế toán
        $arrSearch['ptt_ngay_coc'] = $ptt_ngay_coc = $request->ptt_ngay_coc ? $request->ptt_ngay_coc :  null;
        $arrSearch['ptt_pay_date'] = $ptt_pay_date = $request->ptt_pay_date ? $request->ptt_pay_date :  null;
        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);

        $query = Booking::where('type', 2);
        $query->where('city_id', $city_id);
        $arrSearch['unc0'] = $unc0 = $request->unc0 ? $request->unc0 : null;
        $arrSearch['ptt_pay_status'] = $ptt_pay_status = $request->ptt_pay_status ?? -1;
        $arrSearch['is_vat'] = $is_vat = $request->is_vat ?? null;
        $arrSearch['vat_code'] = $vat_code = $request->vat_code ?? null;

        if($ptt_pay_status > -1){
            $query->where('ptt_pay_status', $ptt_pay_status);
        }
        if($unc0 == 1){
            $query->where('check_unc', 0);
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

        if($is_vat){
            $query->where('is_vat', $is_vat);
        }

        if($id_search){
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptt", "", $id_search);
            $id_search = str_replace("pth", "", $id_search);
            $id_search = str_replace("ptv", "", $id_search);
            $id_search = str_replace("ptx", "", $id_search);
            $id_search = str_replace("ptc", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
        }elseif($phone){
            $arrSearch['phone'] = $phone;
            $query->where('phone', $phone);
        }elseif($vat_code){
            $query->where('vat_code', $vat_code);
        }else{
            if($coc){
                $query->where('tien_coc', '>', 0);
            }

            if($hh0){
                $query->where(function ($query) {
                    $query->whereNull('hoa_hong_sales')
                        ->orWhere('hoa_hong_sales', '=', 0);
                });
                $query->where('price_net', 0);
                $query->whereIn('status', [1, 2]);
                $query->whereNotIn('user_id', [18,33]);
            }

            if($thuc_thu){
                $query->where('tien_thuc_thu', '<=', 0);
            }
            if($status){

                $arrSearch['status'] = $status;
                $query->whereIn('status', $status);
            }

            if($co_coc == 1){
                $query->where('tien_coc', '>', 0);
            }

            if($phone){
                $arrSearch['phone'] = $phone;
                $query->where('phone', $phone);
            }

            if($hotel_id){
                $arrSearch['hotel_id'] = $hotel_id;
                $query->where('hotel_id', $hotel_id);
            }
            if($hotel_book){
                $arrSearch['hotel_book'] = $hotel_book;
                $query->where('hotel_book', $hotel_book);
            }
            if($nguoi_thu_tien){
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }
            if($nguoi_thu_coc){
                $query->where('nguoi_thu_coc', $nguoi_thu_coc);
            }
            if($nguoi_chi_tien){
                $query->where('nguoi_chi_tien', $nguoi_chi_tien);
            }
            if($nguoi_chi_coc){
                $query->where('nguoi_chi_coc', $nguoi_chi_coc);
            }
            if($level){
                $arrSearch['level'] = $level;
                $query->where('level', $level);
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
            // check nếu tìm theo ngày cọc hoặc ngày thanh toán thì tất cả các ngày khác ko có hiệu lực, trả về null
            if($ptt_ngay_coc || $ptt_pay_date){
                if($ptt_ngay_coc){
                    $tmpDate = explode('/', $ptt_ngay_coc);
                    $ptt_ngay_coc_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    $query->where('ptt_ngay_coc', $ptt_ngay_coc_format);
                }
                if($ptt_pay_date){
                    $tmpDate = explode('/', $ptt_pay_date);
                    $ptt_pay_date_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    $query->where('ptt_pay_date', $ptt_pay_date_format);
                }
                $arrSearch['checkin_from'] = $checkin_from = null;
                $arrSearch['checkin_to'] = $checkin_to = null;
                $arrSearch['time_type'] = null;
            }else{
                $col = $search_by;

                $rangeDate = array_unique(explode(' - ', $range_date));
                if (empty($rangeDate[$this->_minDateKey])) {
                    //case page is initialized and range_date is empty => this month
                    $rangeDate = Carbon::now();
                    $query->where($col,'=', $rangeDate->format('Y-m-d'));
                } elseif (count($rangeDate) === 1) {
                    //case page is initialized and range_date has value,
                    //when counting the number of elements in rangeDate = 1 => only select a day
                    $query->where($col,'=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                    $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
                } else {
                    $query->where($col,'>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                    $query->where($col, '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
                }
            } //end neu tim theo ptt ngay coc va ptt ngay thanh toan


        }//end else
        //dd($arrSearch);
        // lay danh sach doi tac book phong

        $partnerList = Partner::getList(['cost_type_id'=> 48, 'city_id' => $city_id]);


        if($ctv_id){
            $query->where('ctv_id', $ctv_id);
        }

        if($sales == 1){
            $query->whereNotIn('user_id', [18,33]);
        }

        $query->orderBy($sort_by, 'desc');

        if($error == 1){
            $query->where('status', 1)
                    ->where('hoa_hong_cty', '<=', 0)
                    ->where('checkin', '<', date('Y-m-d'));
        }

        $allList = $query->get();

        if(Auth::user()->role == 1){
            $ctvList = Ctv::where('status', 1)->get();
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

        $hotelList = Hotels::where(['status' => 1, 'city_id' => $city_id])->get();

        $agent = new Agent();
        if($level){
            $listUser = User::where('level', $level)->where('status', 1)->get();
        }
        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }
        $userArr = [];
        $tong_hoa_hong_cty = $tong_hoa_hong_sales = $tong_coc = $tong_thuc_thu= 0;
        $arrThuCoc = $arrThuTien = [];
        if($allList->count() > 0){

            foreach($allList as $bk){

                $userArr[$bk->user_id] = $bk->user_id;
                if($bk->status < 3){
                    $tong_hoa_hong_cty += $bk->hoa_hong_cty;
                    $tong_hoa_hong_sales += $bk->hoa_hong_sales;
                    $tong_coc += $bk->tien_coc;
                    $tong_thuc_thu += $bk->con_lai;
                    if($bk->nguoi_thu_coc){
                        if(!isset($arrThuCoc[$bk->nguoi_thu_coc])) $arrThuCoc[$bk->nguoi_thu_coc] = 0;
                        $arrThuCoc[$bk->nguoi_thu_coc] += $bk->tien_coc;
                    }
                    if($bk->nguoi_thu_tien){
                        if(!isset($arrThuTien[$bk->nguoi_thu_tien])) $arrThuTien[$bk->nguoi_thu_tien] = 0;
                        $arrThuTien[$bk->nguoi_thu_tien] += $bk->con_lai;
                    }

                }
                //update level
                if($bk->user && $bk->level != $bk->user->level){
                    $bk->update(['level' => $bk->user->level]);
                }
            }
        }
        if($agent->isMobile()){
            $view = 'booking-hotel.m-index';
        }else{
            $view = 'booking-hotel.index';
        }
        return view($view, compact( 'items', 'arrSearch', 'type', 'listUser', 'tong_hoa_hong_sales', 'tong_hoa_hong_cty', 'hotelList', 'level', 'userArr', 'arrUser', 'partnerList', 'time_type', 'month', 'year', 'ctvList', 'arrThuCoc', 'arrThuTien', 'tong_coc', 'tong_thuc_thu', 'city_id'));

    }
    public function acc(Request $request)
    {
        $day = date('d');
        $month_do = date('m');
        $arrSearch['type'] = $type = 2;
        $arrSearch['coc'] = $coc = $request->coc ? $request->coc : null;
        $arrSearch['co_coc'] = $co_coc = $request->co_coc ?? null;
        $arrSearch['keyword'] = $keyword = $request->keyword ? $request->keyword : null;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1,2,4];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;

        $arrSearch['sort_by'] = $sort_by = $request->sort_by ? $request->sort_by : 'created_at';
        $arrSearch['hotel_id'] = $hotel_id = $request->hotel_id ? $request->hotel_id : null;
        $arrSearch['hotel_book'] = $hotel_book = $request->hotel_book ? $request->hotel_book : null;
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ?? null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ?? null;
        $arrSearch['nguoi_chi_tien'] = $nguoi_chi_tien = $request->nguoi_chi_tien ?? null;
        $arrSearch['nguoi_chi_coc'] = $nguoi_chi_coc = $request->nguoi_chi_coc ?? null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 'checkin';
        $arrSearch['checkin_from'] = $checkin_from = $request->checkin_from ? $request->checkin_from : null;
        $arrSearch['checkin_to'] = $checkin_to = $request->checkin_to ? $request->checkin_to : $checkin_from;

        // ngày cọc và ngày thanh toán - dành cho kế toán
        $arrSearch['ptt_ngay_coc'] = $ptt_ngay_coc = $request->ptt_ngay_coc ? $request->ptt_ngay_coc :  null;
        $arrSearch['ptt_pay_date'] = $ptt_pay_date = $request->ptt_pay_date ? $request->ptt_pay_date :  null;
        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);

        $query = Booking::where('type', 2);
        $query->where('city_id', $city_id);
        $arrSearch['unc0'] = $unc0 = $request->unc0 ? $request->unc0 : null;
        $arrSearch['ptt_pay_status'] = $ptt_pay_status = $request->ptt_pay_status ?? -1;
        if($ptt_pay_status > -1){
            $query->where('ptt_pay_status', $ptt_pay_status);
        }
        if($unc0 == 1){
            $query->where('check_unc', 0);
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

        if($id_search){
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptt", "", $id_search);
            $id_search = str_replace("pth", "", $id_search);
            $id_search = str_replace("ptv", "", $id_search);
            $id_search = str_replace("ptx", "", $id_search);
            $id_search = str_replace("ptc", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
        }elseif($phone){
            $arrSearch['phone'] = $phone;
            $query->where('phone', $phone);
        }else{
            if($coc){
                $query->where('tien_coc', '>', 0);
            }

            if($hh0){
                $query->where(function ($query) {
                    $query->whereNull('hoa_hong_sales')
                        ->orWhere('hoa_hong_sales', '=', 0);
                });
                $query->where('price_net', 0);
                $query->whereIn('status', [1, 2]);
                $query->whereNotIn('user_id', [18,33]);
            }


            if($status){

                $arrSearch['status'] = $status;
                $query->whereIn('status', $status);
            }

            if($co_coc == 1){
                $query->where('tien_coc', '>', 0);
            }


            if($hotel_id){
                $arrSearch['hotel_id'] = $hotel_id;
                $query->where('hotel_id', $hotel_id);
            }
            if($hotel_book){
                $arrSearch['hotel_book'] = $hotel_book;
                $query->where('hotel_book', $hotel_book);
            }
            if($nguoi_thu_tien){
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }
            if($nguoi_thu_coc){
                $query->where('nguoi_thu_coc', $nguoi_thu_coc);
            }
            if($nguoi_chi_tien){
                $query->where('nguoi_chi_tien', $nguoi_chi_tien);
            }
            if($nguoi_chi_coc){
                $query->where('nguoi_chi_coc', $nguoi_chi_coc);
            }
            if($level && $type == 1){
                $arrSearch['level'] = $level;
                $query->where('level', $level);
            }

            if(Auth::user()->role < 3){
                //dd($user_id);
                // if(Auth::user()->role == 1 && $user_id == null){
                //     $user_id = 18; // admin vao ks chi thay Hotline cho nhẹ
                // }

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
            // check nếu tìm theo ngày cọc hoặc ngày thanh toán thì tất cả các ngày khác ko có hiệu lực, trả về null
            if($ptt_ngay_coc || $ptt_pay_date){
                if($ptt_ngay_coc){
                    $tmpDate = explode('/', $ptt_ngay_coc);
                    $ptt_ngay_coc_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    $query->where('ptt_ngay_coc', $ptt_ngay_coc_format);
                }
                if($ptt_pay_date){
                    $tmpDate = explode('/', $ptt_pay_date);
                    $ptt_pay_date_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    $query->where('ptt_pay_date', $ptt_pay_date_format);
                }
                $arrSearch['checkin_from'] = $checkin_from = null;
                $arrSearch['checkin_to'] = $checkin_to = null;
                $arrSearch['time_type'] = null;
            }else{
                $col = $search_by;

                if($time_type == 1){ // theo thangs
                    $arrSearch[$col.'_from'] = $checkin_from = $date_use = date('d/m/Y', strtotime($mindate));
                    $arrSearch[$col.'_to'] = $checkin_to = date('d/m/Y', strtotime($maxdate));

                    $query->where($col,'>=', $mindate);
                    $query->where($col, '<=', $maxdate);
                }elseif($time_type == 2){ // theo khoang ngay
                    $arrSearch[$col.'_from'] = $checkin_from = $date_use = $request->checkin_from ? $request->checkin_from : date('d/m/Y', time());
                    $arrSearch[$col.'_to'] = $checkin_to = $request->checkin_to ? $request->checkin_to : $checkin_from;

                    if($checkin_from){
                        $tmpDate = explode('/', $checkin_from);
                        $checkin_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                        $query->where($col,'>=', $checkin_from_format);
                    }
                    if($checkin_to){
                        $tmpDate = explode('/', $checkin_to);
                        $checkin_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                        if($checkin_to_format < $checkin_from_format){
                            $arrSearch[$col.'_to'] = $checkin_from;
                            $checkin_to_format = $checkin_from_format;
                        }
                        $query->where($col, '<=', $checkin_to_format);
                    }
                }else{
                    $arrSearch[$col.'_from'] = $checkin_from = $arrSearch[$col.'_to'] = $checkin_to = $date_use = $request->checkin_from ? $request->checkin_from : date('d/m/Y', time());
                    $tmpDate = explode('/', $checkin_from);
                    $checkin_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    $query->where($col,'=', $checkin_from_format);

                }

            } //end neu tim theo ptt ngay coc va ptt ngay thanh toan


        }//end else
        //dd($arrSearch);
        // lay danh sach doi tac book phong

        $partnerList = Partner::getList(['cost_type_id'=> 48, 'city_id' => $city_id]);


        $query->orderBy($sort_by, 'desc');


        $allList = $query->get();


        $items  = $query->paginate(300);

        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();

        $hotelList = Hotels::where(['status' => 1, 'city_id' => $city_id])->get();

        $agent = new Agent();

        $arrUser = [];
        foreach($listUser as $u){
            $arrUser[$u->id] = $u;
        }
        $userArr = [];
        $tong_hoa_hong_cty = $tong_hoa_hong_sales = $tong_coc = $tong_thuc_thu= 0;
        $arrThuCoc = $arrThuTien = [];
        if($allList->count() > 0){

            foreach($allList as $bk){

                $userArr[$bk->user_id] = $bk->user_id;
                if($bk->status < 3){

                    if($bk->nguoi_thu_coc){
                        if(!isset($arrThuCoc[$bk->nguoi_thu_coc])) $arrThuCoc[$bk->nguoi_thu_coc] = 0;
                        $arrThuCoc[$bk->nguoi_thu_coc] += $bk->tien_coc;
                    }
                    if($bk->nguoi_thu_tien){
                        if(!isset($arrThuTien[$bk->nguoi_thu_tien])) $arrThuTien[$bk->nguoi_thu_tien] = 0;
                        $arrThuTien[$bk->nguoi_thu_tien] += $bk->con_lai;
                    }

                }

            }
        }

        return view('booking-hotel.acc', compact( 'items', 'arrSearch', 'type', 'listUser', 'hotelList', 'userArr', 'arrUser', 'partnerList', 'time_type', 'month', 'year', 'ctvList', 'arrThuCoc', 'arrThuTien', 'city_id'));

    }
    public function related(Request $request){
        $hotel_id = $request->hotel_id;
        $detail = Hotels::find($hotel_id);
        $related = $detail->related_id;
        $tmp = explode(',', $related);
        $relatedArr = [];

        foreach($tmp as $id){
            if($id > 0){
                $relatedArr[] = Partner::find($id);
            }
        }
        return view('booking.related', compact('relatedArr'));

    }
    public function ajaxRoomList(Request $request){
        $hotel_id = $request->hotel_id;
        $detail = Hotels::find($hotel_id);
        $rooms = $detail->rooms;

        return view('booking-hotel.ajax-room-list', compact('rooms'));

    }


    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $user = Auth::user();
        $city_id = $request->city_id ?? 1;
        $hotel_id = $request->hotel_id ?? null;
        $relatedArr = $roomArr = [];
        if($hotel_id){
            $detail = Hotels::find($hotel_id);
            // lay doi tac
            $related = $detail->related_id;
            $tmp = explode(',', $related);


            foreach($tmp as $id){
                if($id > 0){
                    $relatedArr[] = Partner::find($id);
                }
            }
            //lay loai phong
            $roomArr = Rooms::where('status', 1)->where('hotel_id', $hotel_id)->get()->toArray();

        }

        $listTag = Location::where('city_id', $city_id)->where('status', 1)->get();
        if(Auth::user()->role == 1){
            $ctvList = Ctv::where('status', 1)->get();
        }else{
            if(Auth::user()->id == 64){
                $leader_id = 3;
            }else{
                $leader_id = Auth::user()->id;
            }
            $ctvList = Ctv::where('status', 1)->where('leader_id', $leader_id)->get();
        }

        $cateList = Hotels::where('partner', 0)->where('status', 1)->where('city_id', $city_id)->get();

        $view = "booking-hotel.add";
        $roomList = [];
        if($hotel_id){
            $roomList = Rooms::where(['status' => 1, 'hotel_id' => $hotel_id])->orderBy('display_order')->get();
        }

        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();

        $arrBooking = Booking::getBookingForRelated();
        $customerId = $request->customer_id;
        $customer = null;
        if(!empty($customerId)){
            $customer = Customer::find($customerId);
        }
        $user_id_default = $user->role == 1 && $user->level == 6 ? $user->id : null;
        return view($view, compact('listUser', 'listTag', 'cateList', 'ctvList', 'roomList', 'hotel_id', 'city_id', 'arrBooking', 'relatedArr', 'hotel_id', 'roomArr', 'customer', 'customerId', 'user_id_default'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $dataArr = $request->all();

        $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            //'email' => 'email',
            'checkin' => 'required',
            'checkout' => 'required',
            'hotel_id' => 'required'
        ],
        [
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
           // 'email.required' => 'Bạn chưa nhập email',
           // 'email.email' => 'Email không hợp lệ',
            'checkin.required' => 'Bạn chưa nhập ngày đến',
            'checkout.required' => 'Bạn chưa nhập ngày đi',
            'hotel_id.required' => 'Bạn chưa chọn khách sạn'
        ]);

        $dataArr['total_price'] =(int) str_replace(',', '', $dataArr['total_price']);
        $dataArr['tien_coc'] = (int) str_replace(',', '', $dataArr['tien_coc']);
        $dataArr['extra_fee'] = (int) str_replace(',', '', $dataArr['extra_fee']);
        $dataArr['con_lai'] = (int) str_replace(',', '', $dataArr['con_lai']);
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);

        if($dataArr['checkin']){
            $tmpDate = explode('/', $dataArr['checkin']);
            $dataArr['checkin'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }
        if($dataArr['checkout']){
            $tmpDate = explode('/', $dataArr['checkout']);
            $dataArr['checkout'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }

        if($dataArr['book_date']){
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }else{
            $dataArr['book_date'] = date('Y-m-d');
        }
        if(isset($dataArr['ngay_coc'])){
            $tmpDate = explode('/', $dataArr['ngay_coc']);
            $dataArr['ngay_coc'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }
        if(isset($dataArr['ptt_pay_date'])){
            $tmpDate = explode('/', $dataArr['ptt_pay_date']);
            $dataArr['ptt_pay_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }
        if(isset($dataArr['ptt_ngay_coc'])){
            $tmpDate = explode('/', $dataArr['ptt_ngay_coc']);
            $dataArr['ptt_ngay_coc'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }


        if($user->role < 3){
            $dataArr['user_id'] = $dataArr['user_id'];
        }else{
            $dataArr['user_id'] = $user->id;
        }
        $dataArr['name'] = ucwords($dataArr['name']);
        $dataArr['danh_sach'] = ucwords($dataArr['danh_sach']);
        $dataArr['type'] = 2;

        $dataArr['created_user'] = $dataArr['updated_user'] = Auth::user()->id;

        // check neu có điền tổng phụ thu
         $dataArr['ptt_tong_phu_thu'] = isset($dataArr['ptt_tong_phu_thu']) ? (int) str_replace(',', '', $dataArr['ptt_tong_phu_thu']) : null;
        $dataArr['ptt_tien_coc'] = isset($dataArr['ptt_tien_coc']) ? (int) str_replace(',', '', $dataArr['ptt_tien_coc']) : null;
        $dataArr['ptt_con_lai'] = isset($dataArr['ptt_con_lai']) ? (int) str_replace(',', '', $dataArr['ptt_con_lai']) : null;
        if($dataArr['tien_coc'] == 0){
            $dataArr['nguoi_thu_coc'] = null;
        }
        $dataArr['is_vat'] = isset($dataArr['is_vat']) ? 1 : 0;
        if($dataArr['is_vat'] == 1){
            $year_vat = date('Y', strtotime($dataArr['checkin']));
            $year_vat_short = date('y', strtotime($dataArr['checkin']));
            $query_vat = Booking::where('status', '>', 0);
            $query_vat->where(function ($query) use ($year_vat) {
                    $query->where('use_date', '>=', $year_vat.'-01-01')
                        ->orWhere('checkin', '>=', $year_vat.'-01-01');
                });
            $max_vat_id = $query_vat->max('vat_id');
            if($max_vat_id > 0){
                $dataArr['vat_id'] = $max_vat_id + 1;
            }else{
                $dataArr['vat_id'] = 1;
            }
            $dataArr['vat_code'] = 'PTH'.$year_vat_short.str_pad($dataArr['vat_id'], 3, "0", STR_PAD_LEFT);
        }
        $dataArr['use_date'] = $dataArr['checkin'];
        $rsHotel = Booking::create($dataArr);
        $booking_id = $rsHotel->id;

        // store customer
        if(!isset($dataArr['customer_id']) || $dataArr['customer_id'] == ""){

            $customer_id = Helper::storeCustomer($dataArr);

            $rsHotel->update(['customer_id' => $customer_id]);
        }

        $ptt_tong_tien_phong = 0;
        foreach($dataArr['room_id'] as $key => $room_id){
            $dataArr['price_sell'][$key] = (int) str_replace(',', '', $dataArr['price_sell'][$key]);

            if($dataArr['price_sell'][$key] > 0){
                $dataArr['original_price'][$key] = (int) str_replace(',', '', $dataArr['original_price'][$key]);
                $dataArr['room_total_price'][$key] = (int) str_replace(',', '', $dataArr['room_total_price'][$key]);
                $dataRoom = [
                    'booking_id' => $booking_id,
                    'room_id' => $room_id,
                    'price_sell' => $dataArr['price_sell'][$key],
                    'nights' => $dataArr['room_nights'][$key],
                    'original_price' => $dataArr['original_price'][$key],
                    'room_amount' => $dataArr['room_amount'][$key],
                    'room_notes' => $dataArr['room_notes'][$key],
                    'total_price' => $dataArr['room_total_price'][$key]
                ];
                BookingRooms::create($dataRoom);
                // cong vao tong tien phong
                $ptt_tong_tien_phong += $dataArr['original_price'][$key]*$dataArr['room_amount'][$key]*$dataArr['room_nights'][$key];
            }
        }
        // tinh tong tien goc
        $ptt_tong_tien_goc = $ptt_tong_tien_phong + $dataArr['ptt_tong_phu_thu'];
        $ptt_con_lai = $ptt_tong_tien_goc - $dataArr['ptt_tien_coc'];
        $rsHotel->update(['ptt_tong_tien_phong' => $ptt_tong_tien_phong, 'ptt_tong_tien_goc' => $ptt_tong_tien_goc, 'ptt_con_lai' => $ptt_con_lai]);
        $rsLog = BookingLogs::create([
            'booking_id' => $booking_id,
            'content' => json_encode($dataArr),
            'user_id' => $user->id,
            'action' => 1
        ]);
        // push notification
       // dd($rs);
        // if($user->role != 1 || ($user->role == 1 && $rs->user_id != $user->id)){
        //     if($rs->user_id != $user->id){
        //         $userIdPush = [$rs->user_id];
        //     }else{
        //         $userIdPush = [1];
        //     }

        //     // dd($userIdPush);
        //     foreach($userIdPush as $idPush){
        //         if($idPush > 0){
        //             UserNotification::create([
        //                 'title' => $user->name." vừa tạo PTH".$booking_id,
        //                 'content' => '',
        //                 'user_id' => $idPush,
        //                 'booking_id' => $booking_id,
        //                 'date_use' => $rs->checkin,
        //                // 'data' => json_encode($dataArr),
        //                 'type' => 1,
        //                 'is_read' => 0
        //             ]);
        //         }
        //     }
        // }

        $detailUser = User::find($dataArr['user_id']);
        //$this->replyMessHotel($dataArr, $booking_id, $detailUser);//chatbot
        Session::flash('message', 'Tạo mới thành công');
        return redirect()->route('booking-hotel.index', ['hotel_id' => $dataArr['hotel_id'], 'time_type' => 1, 'month' => date('m', strtotime($rsHotel->checkin)), 'year' => date('Y', strtotime($rsHotel->checkin))]);
    }

    public function update(Request $request)
    {
        $dataArr = $request->all();
        //dd($dataArr);
        $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            //'email' => 'email',
            'checkin' => 'required',
            'checkout' => 'required',
            'hotel_id' => 'required',
        ],
        [
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            //'email.required' => 'Bạn chưa nhập email',
            //'email.email' => 'Email không hợp lệ',
            'checkin.required' => 'Bạn chưa nhập ngày đến',
            'checkout.required' => 'Bạn chưa nhập ngày đi',
            'hotel_id.required' => 'Bạn chưa chọn khách sạn',
        ]);

        $dataArr['total_price'] =(int) str_replace(',', '', $dataArr['total_price']);
        $dataArr['tien_coc'] = (int) str_replace(',', '', $dataArr['tien_coc']);
        $dataArr['extra_fee'] = (int) str_replace(',', '', $dataArr['extra_fee']);
        $dataArr['con_lai'] = (int) str_replace(',', '', $dataArr['con_lai']);
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);

        if($dataArr['checkin']){
            $tmpDate = explode('/', $dataArr['checkin']);
            $dataArr['checkin'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }
        if($dataArr['checkout']){
            $tmpDate = explode('/', $dataArr['checkout']);
            $dataArr['checkout'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }

        if($dataArr['book_date']){
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }else{
            $dataArr['book_date'] = date('Y-m-d');
        }
        if(isset($dataArr['ngay_coc'])){
            $tmpDate = explode('/', $dataArr['ngay_coc']);
            $dataArr['ngay_coc'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }
        if(isset($dataArr['ptt_pay_date'])){
            $tmpDate = explode('/', $dataArr['ptt_pay_date']);
            $dataArr['ptt_pay_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }
        if(isset($dataArr['ptt_ngay_coc'])){
            $tmpDate = explode('/', $dataArr['ptt_ngay_coc']);
            $dataArr['ptt_ngay_coc'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }

        if($dataArr['status'] == 3){
            $dataArr['hoa_hong_cty'] = $dataArr['hoa_hong_sales'] = 0;
        }
        $dataArr['hoa_hong_cty'] = isset($dataArr['hoa_hong_cty']) ? (int) str_replace(',', '', $dataArr['hoa_hong_cty']) : 0 ;
        $dataArr['hoa_hong_sales'] = isset($dataArr['hoa_hong_sales']) ? (int) str_replace(',', '', $dataArr['hoa_hong_sales']) : 0;
        $dataArr['danh_sach'] = ucwords($dataArr['danh_sach']);
        $rsHotel = Booking::find($dataArr['id']);

        $dataArr['is_vat'] = isset($dataArr['is_vat']) ? 1 : 0;
        if($dataArr['is_vat'] == 1 && $rsHotel->is_vat == 0){
            $year_vat = date('Y', strtotime($dataArr['checkin']));
            $year_vat_short = date('y', strtotime($dataArr['checkin']));
            $query_vat = Booking::where('status', '>', 0);
            $query_vat->where(function ($query) use($year_vat) {
                    $query->where('use_date', '>=', $year_vat.'-01-01')
                        ->orWhere('checkin', '>=', $year_vat.'-01-01');
                });

            $max_vat_id = $query_vat->max('vat_id');

            if($max_vat_id > 0){
                $dataArr['vat_id'] = $max_vat_id + 1;
            }else{
                $dataArr['vat_id'] = 1;
            }
            $dataArr['vat_code'] = 'PTH'.$year_vat_short.str_pad($dataArr['vat_id'], 3, "0", STR_PAD_LEFT);
        }

        $oldData = $rsHotel->toArray();
        //dd($oldData);
        // $dataArr['name'] = strtoupper($dataArr['name']);
        // $dataArr['danh_sach'] = strtoupper($dataArr['danh_sach']);
        //dd($dataArr);
        if($dataArr['status'] > 2){
            $dataArr['hoa_hong_cty'] = $dataArr['hoa_hong_sales'] = 0;
        }
        $dataArr['updated_user'] = Auth::user()->id;

        // check neu có điền tổng phụ thu
        $dataArr['ptt_tong_phu_thu'] = isset($dataArr['ptt_tong_phu_thu']) ? (int) str_replace(',', '', $dataArr['ptt_tong_phu_thu']) : null;
        $dataArr['ptt_tien_coc'] = isset($dataArr['ptt_tien_coc']) ? (int) str_replace(',', '', $dataArr['ptt_tien_coc']) : null;
        $dataArr['ptt_con_lai'] = isset($dataArr['ptt_con_lai']) ? (int) str_replace(',', '', $dataArr['ptt_con_lai']) : null;

        $dataArr['use_date'] = $dataArr['checkin'];
        $rsHotel->update($dataArr);

        $booking_id = $rsHotel->id;
        BookingRooms::where('booking_id', $dataArr['id'])->delete();

        $ptt_tong_tien_phong = 0;
        foreach($dataArr['room_id'] as $key => $room_id){
            $dataArr['price_sell'][$key] = (int) str_replace(',', '', $dataArr['price_sell'][$key]);

            if($dataArr['price_sell'][$key] > 0){
                $dataArr['original_price'][$key] = (int) str_replace(',', '', $dataArr['original_price'][$key]);
                $dataArr['room_total_price'][$key] = (int) str_replace(',', '', $dataArr['room_total_price'][$key]);
                $dataRoom = [
                    'booking_id' => $booking_id,
                    'room_id' => $room_id,
                    'price_sell' => $dataArr['price_sell'][$key],
                    'nights' => $dataArr['room_nights'][$key],
                    'original_price' => $dataArr['original_price'][$key],
                    'room_amount' => $dataArr['room_amount'][$key],
                    'room_notes' => $dataArr['room_notes'][$key],
                    'total_price' => $dataArr['room_total_price'][$key]
                ];
                BookingRooms::create($dataRoom);

                // cong vao tong tien phong
                $ptt_tong_tien_phong += $dataArr['original_price'][$key]*$dataArr['room_amount'][$key]*$dataArr['room_nights'][$key];
            }
        }

        // tinh tong tien goc
        $ptt_tong_tien_goc = $ptt_tong_tien_phong + $dataArr['ptt_tong_phu_thu'];
        $ptt_con_lai = $ptt_tong_tien_goc - $dataArr['ptt_tien_coc'];
        $rsHotel->update(['ptt_tong_tien_phong' => $ptt_tong_tien_phong, 'ptt_tong_tien_goc' => $ptt_tong_tien_goc, 'ptt_con_lai' => $ptt_con_lai]);
        $booking_id = $rsHotel->id;
        //$this->replyMessHotel($dataArr, $booking_id);
        Session::flash('message', 'Cập nhật thành công');
        $book_date = date('d/m/Y', strtotime($dataArr['book_date']));

        //update hoa hong
        // if(time() > strtotime($rsHotel->checkin) && $rsHotel->status < 3){
        //     $this->updateHoaHong($rsHotel);
        // }

        //ghi log
        //unset key thừa
        $unsetArr = ['room_id', 'room_amount', 'room_nights', 'price_sell', 'room_total_price', 'original_price', 'room_notes', '_token', 'room_name'];

        foreach($unsetArr as $value){
            unset($dataArr[$value]);
        }
        $contentDiff = array_diff_assoc($dataArr, $oldData);


        $booking_id = $rsHotel->id;
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
        return redirect()->route('booking-hotel.edit', ['id' => $booking_id ]);
    }
    public function replyMessHotel($dataArr, $booking_id, $detailUser){

        $url = 'https://openapi.zalo.me/v2.0/oa/message?access_token=ZaVgNfRnPLUDG-XRalLgKuT2u5UJwn83YYxgIf302XZv9iX1ljKr5ia6ongBp3bgwYJd19F03q_vDECyjzeoDVGeuJglm6a_yY_hMwpR1IwmRiz6nTv0Bw0igLNz-c1Tv16i0fttL5FYGgC3hAOW3SPB_dA6-0rYw1py1uli77Vn4jCIfifGREGLln2Yfaf3sdEP6OsPSMVDGQCGX_DuRl95kXwEe4b5a6s6J-AhVp2zHwzrwBXP8Prjaotvt4mzkMo1SkE22G2XQze8leeALDj4tX5FQ2s7kZsGxcDL';
        //$strpad = str_pad($booking_id, 5, '0', STR_PAD_LEFT);
        $use_date = date('d/m', strtotime($dataArr['checkin']));
        //$booking_code = 'T'.$ctv_id.$strpad;
        $sales = "";
        $zalo_sales_id = null;
        $zalo_sales_id = $detailUser->zalo_id;
        $sales = $detailUser->name;

        $hotelDetail = Hotels::find($dataArr['hotel_id']);
        $arrData = [
            'recipient' => [
                'user_id' => '7317386031055599346',
            ],
            'message' => [
                'text' => 'Mã booking: PTH'.$booking_id."\n\r".'Hotel: '.$use_date.':: '.$dataArr['name'].' - '.$dataArr['phone'].' - '. $hotelDetail->name.' - '.$dataArr['adults'].' người lớn'.' - '.$dataArr['childs'].' trẻ em'.' - Tổng tiền: '.number_format($dataArr['total_price']).' - Tiền cọc: '.number_format($dataArr['tien_coc']).' - Còn lại: '.number_format($dataArr['con_lai']).' - Sales: '.$sales.' - '.$dataArr['notes'],
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
        $city_id = $detail->city_id;
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        if(Auth::user()->role == 1){
            $ctvList = Ctv::where('status', 1)->get();
        }else{
            if(Auth::user()->id == 64){
                $leader_id = 3;
            }else{
                $leader_id = Auth::user()->id;
            }
            $ctvList = Ctv::where('status', 1)->where('leader_id', $leader_id)->get();
        }
         $listTag = Location::where('city_id', $city_id)->where('status', 1)->get();
        if($detail->user_id != Auth::user()->id && Auth::user()->role == 2){
            dd('Bạn không có quyền truy cập.');
        }
        $arrSearch = $request->all();

        $roomArr = $roomIdArr = [];
        $roomsList = $detail->rooms;

        foreach($roomsList as $r){
            $roomArr[] = $r->toArray();
            $roomIdArr[] = $r->room_id;
        }
        $hotelList = Hotels::where('city_id', $city_id)->where('status', 1)->get();
        $type = $detail->type;
         $relatedArr = [];
        if($detail->hotel_id){
            $detailHotels = Hotels::find($detail->hotel_id);
            $related = $detailHotels->related_id;
            $tmp = explode(',', $related);

            foreach($tmp as $id){
                if($id > 0){
                    $relatedArr[] = Partner::find($id);
                }
            }
        }

        $arrBooking = Booking::getBookingForRelated();

        $relatedIdArr = BookingRelated::getBookingRelated($id);
        //lay loai phong
        if($request->hotel_id){
            $roomArrHotel = Rooms::where('status', 1)->where('hotel_id', $request->hotel_id)->get()->toArray();
        }else{
            $roomArrHotel = Rooms::where('status', 1)->where('hotel_id', $detail->hotel_id)->get()->toArray();
        }


        return view('booking-hotel.edit', compact('roomsList', 'detail', 'listUser', 'arrSearch', 'hotelList', 'type', 'roomArr', 'relatedArr', 'listTag', 'ctvList', 'keyword', 'city_id', 'roomIdArr', 'arrBooking', 'relatedIdArr', 'roomArrHotel'));
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
        $model->update(['status' => 0]);
        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('booking-hotel.index', ['use_date_from' => $use_date]);
    }

    function updateHoaHong($bk){

        $arr30 = [2,3];
        $user_id = $bk->user_id;
        if($user_id == 18 || $user_id == 6 || $user_id == 3 || $user_id == 451){ // 6 la Yen Vi
            $percentCty = 100 ;
        }else{
            $percentCty = 30;
        }
        $i = 0;
        $i++;
        $rooms = $bk->rooms;
        $tong_hoa_hong = 0;
        foreach($rooms as $r){
            $price_sell = $r->price_sell;
            $nights = $r->nights;
            $total_price = $r->total_price;
            $original_price = $r->original_price;
            if(strlen($original_price) < 5 && $original_price > 0){
                $original_price = $original_price*1000;
            }
            if($original_price == 0){
                $original_price = $price_sell-50000;
            }elseif($original_price > $price_sell){
                $original_price = $original_price/$nights/$r->room_amount;
            }
            $tong_gia_goc = $original_price*$r->room_amount*$r->nights;
            $tong_hoa_hong+= $total_price - $tong_gia_goc;

        }
        var_dump($original_price, $price_sell, '------');
        $hoa_hong_cty = $percentCty*$tong_hoa_hong/100;
        $hoa_hong_sales = $tong_hoa_hong-$hoa_hong_cty;
        var_dump($i, $bk->id, $tong_hoa_hong, $hoa_hong_cty, $hoa_hong_sales);
        echo "<hr>";
        $bk->update(['hoa_hong_sales' => $hoa_hong_sales, 'hoa_hong_cty' => $hoa_hong_cty]);
    }
}
