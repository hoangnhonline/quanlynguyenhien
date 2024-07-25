<?php

namespace App\Http\Controllers;

use App\Models\TicketType;
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
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class BookingTicketController extends Controller
{

    public function index(Request $request)
    {
        $day = date('d');
        $month_do = date('m');
        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));
        $arrSearch['code_nop_tien'] = $code_nop_tien = $request->code_nop_tien ?? null;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1, 2];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $arrSearch['ctv_id'] = $ctv_id = $request->ctv_id ?? null;
        $arrSearch['phone'] = $phone = $request->phone ? $request->phone : null;
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 2;
        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y');

        $query = Booking::where(['type' => 3, 'city_id' => $city_id]);
        $arrSearch['unc0'] = $unc0 = $request->unc0 ? $request->unc0 : null;
        if($unc0 == 1){
            $query->where('check_unc', 0);
        }
        if($id_search){
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptv", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
        }else{
            if($status){
                $arrSearch['status'] = $status;
                $query->whereIn('status', $status);
            }
            if($code_nop_tien){
                $query->whereIn('code_nop_tien', $code_nop_tien);
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

            $minDateKey = 0;
            $maxDateKey = 1;
            $col = $search_by == 2 ? 'book_date' : 'use_date';

            $rangeDate = array_unique(explode(' - ', $range_date));
            if (empty($rangeDate[$minDateKey])) {
                //case page is initialized and range_date is empty => this month
                $rangeDate = Carbon::now();
                $query->where($col,'=', $rangeDate->format('Y-m-d'));
            } elseif (count($rangeDate) === 1) {
                //case page is initialized and range_date has value,
                //when counting the number of elements in rangeDate = 1 => only select a day
                $query->where($col,'=', Carbon::createFromFormat('d/m/Y', $rangeDate[$minDateKey])->format('Y-m-d'));
                $arrSearch['range_date'] = $rangeDate[$minDateKey] . " - " . $rangeDate[$minDateKey];
            } else {
                $query->where($col,'>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$minDateKey])->format('Y-m-d'));
                $query->where($col, '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$maxDateKey])->format('Y-m-d'));
            }
        }


        $query->orderBy('id', 'desc');

        $allList = $query->get();
        //update level
        foreach($allList as $bk){
            if($bk->user && $bk->level != $bk->user->level){
                $bk->update(['level' => $bk->user->level]);
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

        $items  = $query->paginate(300);

        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();

        $agent = new Agent();
        if($agent->isMobile()){
            $view = 'booking-ticket.m-index';
        }else{
            $view = 'booking-ticket.index';
        }
        $ticketTypeArr = TicketTypeSystem::whereIn('status', [1, 2])->where('city_id', $city_id)->pluck('name', 'id')->toArray();

        return view($view, compact( 'items', 'arrSearch', 'listUser', 'ticketTypeArr', 'ctvList', 'time_type', 'nguoi_thu_tien', 'time_type', 'month', 'year', 'ctvList', 'search_by', 'city_id'));

    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
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
        $cateList = TicketTypeSystem::where('city_id', $city_id)->where('status', 1)->get();

        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6])->where('status', 1)->get();
        $customerId = $request->customer_id;
        $customer = null;
        if(!empty($customerId)){
            $customer = Customer::find($customerId);
        }
        $user_id_default = $user->role == 1 && $user->level == 6 ? $user->id : null;
        return view("booking-ticket.add", compact('listUser', 'listTag', 'cateList', 'ctvList', 'city_id', 'customerId', 'customer', 'user_id_default'));
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

        $ticketType = TicketTypeSystem::where('city_id', $detail->city_id)->get();
        $ticketList = $detail->tickets;
        $ticketArr = $ticketIdArr = [];
        foreach($ticketList as $t){
            $ticketArr[] = $t;
            $ticketIdArr[] = $t->ticket_type_id;
        }
        $city_id = $detail->city_id;
        return view('booking-ticket.edit', compact( 'detail', 'listUser', 'arrSearch', 'ticketType', 'ticketArr', 'listTag', 'keyword', 'ctvList', 'ticketIdArr', 'city_id'));
    }
     public function store(Request $request)
    {
        $dataArr = $request->all();

        $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ],
        [
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'address.required' => 'Bạn chưa chọn nơi giao.',

        ]);

        $dataArr['total_price'] =(int) str_replace(',', '', $dataArr['total_price']);
        $dataArr['tien_coc'] = (int) str_replace(',', '', $dataArr['tien_coc']);
        $dataArr['extra_fee'] = 0;
        $dataArr['type'] = 3;
        $dataArr['con_lai'] = (int) str_replace(',', '', $dataArr['con_lai']);
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $tmpDate = explode('/', $dataArr['use_date']);
        $dataArr['use_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
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
        if(Auth::user()->role < 3){
            $dataArr['user_id'] = $dataArr['user_id'];
        }else{
            $dataArr['user_id'] = Auth::user()->id;
        }
        $dataArr['name'] = ucwords($dataArr['name']);

        $dataArr['created_user'] = $dataArr['updated_user'] = Auth::user()->id;
        $rs = Booking::create($dataArr);
        $booking_id = $rs->id;



        foreach($dataArr['ticket_type_id'] as $key => $ticket_type_id){
            $detailTicketType = TicketTypeSystem::find($ticket_type_id);
            $dataArr['price_sell'][$key] = (int) str_replace(',', '', $dataArr['price_sell'][$key]);

            if($dataArr['price_sell'][$key] > 0){
                $dataArr['price'][$key] = (int) str_replace(',', '', $dataArr['price'][$key]);
                $dataArr['total'][$key] = (int) str_replace(',', '', $dataArr['total'][$key]);
                $dataArr['commission'][$key] = (int) str_replace(',', '', $dataArr['commission'][$key]);
                $dataRoom = [
                    'booking_id' => $booking_id,
                    'ticket_type_id' => $ticket_type_id,
                    'price_sell' => $dataArr['price_sell'][$key],
                    'price' => $dataArr['price'][$key],
                    'amount' => $dataArr['amount'][$key],
                    'total' => $dataArr['total'][$key],
                    'commission' => $dataArr['commission'][$key],
                    'company_id' => $detailTicketType->company_id
                ];
                Tickets::create($dataRoom);
            }
        }

        unset($dataArr['_token']);
        BookingLogs::create([
            'booking_id' => $booking_id,
            'content' => json_encode($dataArr),
            'user_id' => Auth::user()->id,
            'action' => 1
        ]);

        // store customer
        if(!isset($dataArr['customer_id']) || $dataArr['customer_id'] == ""){

            $customer_id = Helper::storeCustomer($dataArr);

            $rs->update(['customer_id' => $customer_id]);
        }


        $detailUser = User::find($dataArr['user_id']);
        //$this->replyMessTicket($dataArr, $rs);//chatbot
        Session::flash('message', 'Tạo mới thành công');
        $book_date = date('d/m/Y', strtotime($dataArr['book_date']));
        return redirect()->route('booking-ticket.index', ['book_date' => $book_date]);
    }

     public function update(Request $request)
    {
        $dataArr = $request->all();

        $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ],
        [
            'name.required' => 'Bạn chưa nhập tên',
            'phone.required' => 'Bạn chưa nhập điện thoại',
            'address.required' => 'Bạn chưa chọn nơi giao.',

        ]);

        $dataArr['total_price'] =isset($dataArr['total_price']) ? (int) str_replace(',', '', $dataArr['total_price']) : 0;
        $dataArr['tien_coc'] = isset($dataArr['tien_coc']) ? (int) str_replace(',', '', $dataArr['tien_coc']) : 0;
        $dataArr['extra_fee'] = 0;
        $dataArr['con_lai'] = isset($dataArr['con_lai']) ? (int) str_replace(',', '', $dataArr['con_lai']) : 0;
        $dataArr['phone'] = str_replace('.', '', $dataArr['phone']);
        $dataArr['phone'] = str_replace(' ', '', $dataArr['phone']);
        $dataArr['type'] = 3;
        $tmpDate = explode('/', $dataArr['use_date']);
        $dataArr['use_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        if(isset($dataArr['book_date'])){
            $tmpDate = explode('/', $dataArr['book_date']);
            $dataArr['book_date'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }else{
            $dataArr['book_date'] = date('Y-m-d');
        }


        if(isset($dataArr['ngay_coc'])){
            $tmpDate = explode('/', $dataArr['ngay_coc']);
            $dataArr['ngay_coc'] = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        }
        if(Auth::user()->role < 3){
            $dataArr['user_id'] = $dataArr['user_id'];
        }else{
            $dataArr['user_id'] = Auth::user()->id;
        }
        $dataArr['name'] = ucwords($dataArr['name']);
        $detail = Booking::find($dataArr['id']);
        $oldData = $detail->toArray();

        unset($dataArr['_token']);
        $dataArr['updated_user'] = Auth::user()->id;
        $detail->update($dataArr);
        $booking_id = $detail->id;
        Tickets::where('booking_id', $booking_id)->delete();
        if ($detail->source != 'website') {
            foreach ($dataArr['ticket_type_id'] as $key => $ticket_type_id) {
                $detailTicketType = TicketTypeSystem::find($ticket_type_id);
                $dataArr['price_sell'][$key] = (int)str_replace(',', '', $dataArr['price_sell'][$key]);

                if ($dataArr['price_sell'][$key] > 0) {
                    $dataArr['price'][$key] = (int)str_replace(',', '', $dataArr['price'][$key]);
                    $dataArr['total'][$key] = (int)str_replace(',', '', $dataArr['total'][$key]);
                    $dataArr['commission'][$key] = (int)str_replace(',', '', $dataArr['commission'][$key]);
                    $dataRoom = [
                        'booking_id' => $booking_id,
                        'ticket_type_id' => $ticket_type_id,
                        'price_sell' => $dataArr['price_sell'][$key],
                        'price' => $dataArr['price'][$key],
                        'amount' => $dataArr['amount'][$key],
                        'total' => $dataArr['total'][$key],
                        'commission' => $dataArr['commission'][$key],
                        'company_id' => $detailTicketType->company_id
                    ];
                    Tickets::create($dataRoom);
                }
            }
        }

        unset($dataArr['price_sell']);
        unset($dataArr['ticket_type_id']);
        unset($dataArr['price']);
        unset($dataArr['amount']);
        unset($dataArr['total']);
        unset($dataArr['commission']);

        $contentDiff = array_diff_assoc($dataArr, $oldData);


        if(!empty($contentDiff)){
            $oldContent = [];

            foreach($contentDiff as $k => $v){
                $oldContent[$k] = $oldData[$k];
            }
            BookingLogs::create([
                'booking_id' => $booking_id,
                'content' =>json_encode(['old' => $oldContent, 'new' => $contentDiff]),
                'action' => 2,
                'user_id' => Auth::user()->id
            ]);
        }
       // $this->replyMessTicketUpdate($dataArr, $detail);
        Session::flash('message', 'Cập nhật thành công');
        $book_date = date('d/m/Y', strtotime($dataArr['book_date']));
        return redirect()->route('booking-ticket.index', ['book_date' => $book_date]);
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
        return redirect()->route('booking-ticket.index', ['use_date_from' => $use_date]);
    }
}
