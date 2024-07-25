<?php

namespace App\Http\Controllers;

use App\Models\AdsCampaign;
use App\Models\Booking;
use App\Models\Combo;
use App\Models\CustomerAppointment;
use App\Models\CustomerSource;
use App\Models\TourSystem;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\UserRefer;
use App\Models\UserNotification;

use Helper, File, Session, Auth;

// use Maatwebsite\Excel\Facades\Excel;

// use Excel;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\Exportable;
// use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerController extends Controller
// implements FromCollection, WithHeadings
{
    private $_minDateKey = 0;
    private $_maxDateKey = 1;
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {

        $arrSearch['city_id'] = $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $arrSearch['code'] = $code = $request->code ?? null;
        $arrSearch['phone'] = $phone = $request->phone ?? null;
        $arrSearch['email'] = $email = $request->email ?? null;
        $arrSearch['name'] = $name = $request->name ?? null;
        $arrSearch['status'] = $status = $request->status ?? null;
        $arrSearch['is_send'] = $is_send = $request->is_send ?? null;
        $arrSearch['is_accept'] = $is_accept = $request->is_accept ?? null;
        $arrSearch['user_id'] = $user_id = $request->user_id ?? null;
        $arrSearch['user_id_refer'] = $user_id_refer = $request->user_id_refer ?? null;

        $query = Customer::with(['bookings' => function($q) {
            return $q->where('status', '>', '0');
        }])->whereRaw('1');
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
        if ($name) {
            $query->where('name', 'like', '%' . $name . '%' );
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

        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 5;
        $arrSearch['checkin_from'] = $checkin_from = $request->checkin_from ? $request->checkin_from : null;
        $arrSearch['checkin_to'] = $checkin_to = $request->checkin_to ? $request->checkin_to : $checkin_from;
        $arrSearch['month'] = $month = $request->month ?? date('m');
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 'contact_date';
        $arrSearch['source'] = $source = $request->source ? $request->source : null;
        $arrSearch['source2'] = $source2 = $request->source2 ? $request->source2 : null;
        $arrSearch['ads'] = $ads = $request->ads ? $request->ads : null;
        $arrSearch['ask_more'] = $ask_more = $request->ask_more ?? null;
        $arrSearch['product_type'] = $product_type = $request->product_type ? $request->product_type : null;

        $currentDate = Carbon::now();
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y'); //this month

        $monthDefault = date('m');
        $month = $request->month ?? $monthDefault;
        $type = $request->type ?? 1;
        $year = $request->year ?? date('Y');
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));


        $rangeDate = array_unique(explode(' - ', $range_date));
        if (empty($rangeDate[$this->_minDateKey])) {
            //case page is initialized and range_date is empty => today
            $rangeDate = Carbon::now();
            $query->whereDate($search_by,'=', $rangeDate->format('Y-m-d'));
            $time_type = 3;
            $month = $rangeDate->format('m');
            $year = $rangeDate->year;
        } elseif (count($rangeDate) === 1) {
            //case page is initialized and range_date has value,
            //when counting the number of elements in rangeDate = 1 => only select a day
            $use_date = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey]);
            $query->whereDate($search_by,'=', $use_date->format('Y-m-d'));
            $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
            $time_type = 3;
            $month = $use_date->format('m');
            $year = $use_date->year;
        } else {
            $query->whereDate($search_by,'>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
            $query->whereDate($search_by, '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
            $time_type = 1;
            $month = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('m');
            $year = Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->year;
        }

        if($source){          
            $query->where('source', $source);
        }

        if($source2){          
            $query->where('source2', $source2);
        }
        if($ads){
            $query->where('ads', true);
        }
        if($ask_more){
            $query->where('ask_more', 1);
        }
        if($product_type){
            $query->where('product_type', $product_type);
        }

        $items = $query->orderBy('contact_date', 'desc')->paginate(100);
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        $sources  = CustomerSource::whereNull('parent_id')->get();
        $sources2  = CustomerSource::whereHas('childs')->get();
        return view('customer.index', compact('items', 'arrSearch', 'time_type', 'type', 'month', 'year', 'listUser', 'sources', 'sources2', 'ask_more'));
    }

    public function getListNoti(Request $request)
    {
        $user_id = Auth::user()->id;
        $today = date('Y-m-d 00:00:00');
        $tomorrow = date('Y-m-d 23:59:59', strtotime('tomorrow'));

        $querySchedule1 = Customer::where('status', 1)->where('is_noti_1', 0);
        $querySchedule1->where(function ($query) use ($user_id) {
            $query->where('created_user', '=', $user_id)
                ->orWhere('user_id_refer', '=', $user_id);
        });

        $rsSchedule1 = $querySchedule1->where('schedule_1', '>=', $today)->where('schedule_1', '<=', $tomorrow)->get();

        $arrCustomerId = [];
        if ($rsSchedule1->count() > 0) {
            foreach ($rsSchedule1 as $cus) {
                $format_schedule_1 = strtotime($cus->schedule_1);
                $schedule_1 = date('Y-m-d 00:00:00', $format_schedule_1);
                if ($schedule_1 == $today) {
                    $text = "Bạn có 1 cuộc hẹn với khách hàng " . $cus->name . " vào lúc " . date('H:i', $format_schedule_1) . " hôm nay " . date('d/m', $format_schedule_1);
                } else {
                    $text = "Bạn có 1 cuộc hẹn với khách hàng " . $cus->name . " vào lúc " . date('H:i', $format_schedule_1) . " ngày mai " . date('d/m', $format_schedule_1);
                }
                $arrCustomerId[] = $cus->id;
                UserNotification::create([
                    'title' => $text,
                    'content' => '',
                    'user_id' => $user_id,
                    //'booking_id' => $id,
                    'date_use' => $cus->schedule_1,
                    //'data' => json_encode($dataArr),
                    'type' => 99, // cuộc hẹn với khách hàng
                    'is_read' => 0
                ]);

                $cus->update(['is_noti_1' => 1]);

            }
        }
        // lịch hẹn lần 2
        $querySchedule2 = Customer::where('status', 1)->where('is_noti_2', 0);
        $querySchedule2->where(function ($query) use ($user_id) {
            $query->where('created_user', '=', $user_id)
                ->orWhere('user_id_refer', '=', $user_id);
        });

        $rsSchedule2 = $querySchedule2->where('schedule_2', '>=', $today)->where('schedule_2', '<=', $tomorrow)->get();

        if ($rsSchedule2->count() > 0) {
            foreach ($rsSchedule2 as $cus) {
                if (!in_array($cus->id, $arrCustomerId)) {
                    $format_schedule_2 = strtotime($cus->schedule_2);
                    $schedule_2 = date('Y-m-d 00:00:00', $format_schedule_2);
                    if ($schedule_2 == $today) {
                        $text = "Bạn có 1 cuộc hẹn với khách hàng " . $cus->name . " vào lúc " . date('H:i', $format_schedule_2) . " hôm nay " . date('d/m', $format_schedule_2);
                    } else {
                        $text = "Bạn có 1 cuộc hẹn với khách hàng " . $cus->name . " vào lúc " . date('H:i', $format_schedule_2) . " ngày mai " . date('d/m', $format_schedule_2);
                    }

                    UserNotification::create([
                        'title' => $text,
                        'content' => '',
                        'user_id' => $user_id,
                        //'booking_id' => $id,
                        'date_use' => $cus->schedule_2,
                        //'data' => json_encode($dataArr),
                        'type' => 99, // cuộc hẹn với khách hàng
                        'is_read' => 0
                    ]);

                    $cus->update(['is_noti_2' => 1]);
                }


            }
        }


        $rsNoti = UserNotification::where('user_id', $user_id)->where('is_read', 0)->where('type', 99)->get();


        return view('customer.list-noti', compact('rsNoti'));

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    public function create()
    {
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $arrBooking = Booking::getBookingForRelated();
        $sources  = CustomerSource::whereNull('parent_id')->get();
        $sources2  = CustomerSource::whereHas('childs')->get();
        $adsCampaigns = AdsCampaign::all();
        return view('customer.create', compact('city_id', 'arrBooking', 'sources', 'sources2', 'adsCampaigns'));
    }

    public function store(Request $request)
    {
        $dataArr = $request->all();
        $this->validate($request, [
            'city_id' => 'required',
            'name' => 'required',
           // 'phone' => 'required',
            'source' => 'required',
//            'source2' => 'required',
            'ads_campaign_id' => 'required_if:ads,1',
        ],
            [
                'city_id.required' => 'Bạn chưa nhập thị trường',
                'name.required' => 'Bạn chưa nhập tên',
              //  'phone.required' => 'Bạn chưa nhập số điện thoại',
                'source.required' => 'Bạn chưa chọn nguồn',
                'source2.required' => 'Bạn chưa chọn nguồn cấp 2',
                'ads_campaign_id.required_if' => 'Bạn chưa chọn chiến dịch',
            ]);

        $dataArr['is_send'] = isset($dataArr['is_send']) ? 1 : 0;
        $dataArr['ask_more'] = isset($dataArr['ask_more']) ? 1 : 0;
        if ($dataArr['contact_date']) {
            $tmpDate = explode('/', $dataArr['contact_date']);
            $dataArr['contact_date'] = $tmpDate[2] . '-' . $tmpDate[1] . "-" . $tmpDate[0];
        }
        $dataArr['contact_date'] .= " " . $dataArr['contact_date_hour'] . ":" . $dataArr['contact_date_minute'] . ":00";

        if ($dataArr['birthday']) {
            $tmpDate = explode('/', $dataArr['birthday']);
            $dataArr['birthday'] = $tmpDate[2] . '-' . $tmpDate[1] . '-' . $tmpDate[0];
        }
        $dataArr['created_user'] = $dataArr['updated_user'] = Auth::user()->id;

        $rsCustomer = Customer::create($dataArr);

        if ($dataArr['is_send'] == 1) {
            $rsRefer = UserRefer::where('city_id', $dataArr['city_id'])->orderBy('count_refer', 'asc')->orderBy('updated_at', 'asc')->first();
            $user_id_refer = $rsRefer->user_id;
            $flag = $rsCustomer->update(['user_id_refer' => $user_id_refer, 'is_send' => 1, 'time_send' => date('Y-m-d H:i:s', time())]);
            if ($flag) {
                $rsRefer->update(['count_refer' => $rsRefer->count_refer + 1]);
            }
        }

        //Check if customer has related bookings, update user_id in booking table
        if (!empty($dataArr['related_id'])) {
            $bookings = Booking::whereIn('id', $dataArr['related_id'])->get();
            foreach ($bookings as $booking){
                $booking->update(['customer_id' => $rsCustomer->id]);
            }
        }

        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('customer.index', ['city_id' => $dataArr['city_id']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $detail = Customer::find($id);
        $sources  = CustomerSource::whereNull('parent_id')->get();
        $sources2  = CustomerSource::whereHas('childs')->get();
        $adsCampaigns = AdsCampaign::all();
        return view('customer.edit', compact('detail', 'sources', 'sources2', 'adsCampaigns'));
    }

    public function hen(Request $request)
    {
        $id = $request->id;
        $detail = Customer::find($id);
        $appointments = CustomerAppointment::where('customer_id', $id)->where('status', 1)->orderBy('id', 'desc')->get()->toArray();
        return view('customer.hen', compact('detail', 'appointments'));
    }

    public function createBooking(Request $request)
    {
        $id = $request->id;
        return view('customer.create-booking', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request)
    {

        $dataArr = $request->all();

        $this->validate($request, [
            'city_id' => 'required',
            'name' => 'required',
            //'phone' => 'required',
            'source' => 'required',
//            'source2' => 'required',
            'ads_campaign_id' => 'required_if:ads,1',
        ],
            [
                'city_id.required' => 'Bạn chưa nhập thị trường',
                'name.required' => 'Bạn chưa nhập tên',
               // 'phone.required' => 'Bạn chưa nhập số điện thoại',
                'source.required' => 'Bạn chưa chọn nguồn',
                'source2.required' => 'Bạn chưa chọn nguồn cấp 2',
                'ads_campaign_id.required_if' => 'Bạn chưa chọn chiến dịch',
            ]);

        $dataArr['is_send'] = isset($dataArr['is_send']) ? 1 : 0;
        $dataArr['ask_more'] = isset($dataArr['ask_more']) ? 1 : 0;
        if ($dataArr['contact_date']) {
            $tmpDate = explode('/', $dataArr['contact_date']);
            $dataArr['contact_date'] = $tmpDate[2] . '-' . $tmpDate[1] . "-" . $tmpDate[0];
        }
        $dataArr['contact_date'] .= " " . $dataArr['contact_date_hour'] . ":" . $dataArr['contact_date_minute'] . ":00";

        if ($dataArr['birthday']) {
            $tmpDate = explode('/', $dataArr['birthday']);
            $dataArr['birthday'] = $tmpDate[2] . '-' . $tmpDate[1] . '-' . $tmpDate[0];
        }


        $model = Customer::find($dataArr['id']);

        $dataArr['updated_user'] = Auth::user()->id;
        $oldIsSend = $model->is_send;
        $dataArr['updated_user'] = Auth::user()->id;

        $model->update($dataArr);

        if ($dataArr['is_send'] == 1 && $oldIsSend == 0) {
            $rsRefer = UserRefer::where('city_id', $dataArr['city_id'])->orderBy('count_refer', 'asc')->orderBy('updated_at', 'asc')->first();
            $user_id_refer = $rsRefer->user_id;
            $flag = $model->update(['user_id_refer' => $user_id_refer, 'is_send' => 1, 'time_send' => date('Y-m-d H:i:s', time())]);
            if ($flag) {
                $rsRefer->update(['count_refer' => $rsRefer->count_refer + 1]);
            }
        }

        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('customer.edit', $dataArr['id']);
    }

    public function getUserRefer($city_id)
    {
        $rs = UserRefer::where('city_id', $city_id)->orderBy('count_refer', 'asc')->orderBy('updated_at', 'asc')->first();
        $rs->update(['count_refer' => $rs->count_refer + 1]);
        return $rs->user_id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
//        if (Auth::user()->role == 3) {
//            return redirect()->route('dashboard');
//        }
        // delete
        $model = Customer::find($id);
        $model->delete();

        // redirect
        Session::flash('message', 'Xóa khách hàng thành công');
        return redirect()->route('customer.index');
    }

    public function updateStatus(Request $request)
    {
        if (Auth::user()->role == 3) {
            return redirect()->route('dashboard');
        }
        $model = Customer::find($request->id);

        $model->updated_user = Auth::user()->id;
        $model->status = $request->status;

        $model->save();
        $mess = $request->status == 1 ? "Mở khóa thành công" : "Khóa thành công";
        Session::flash('message', $mess);

        return redirect()->route('customer.index');
    }

    public function saveHen(Request $request)
    {
        $dataArr = $request->all();
        $customerId = $dataArr['customer_id'];
        $idArr = $dataArr['id'];
        $dateArr = $dataArr['schedule_date'];
        $hourArr = $dataArr['schedule_hour'];
        $minuteArr = $dataArr['schedule_minute'];
        $notes = $dataArr['schedule_notes'];

        for ($i = 0; $i < 10; $i++) {
            if (!empty($dateArr[$i]) && !empty($hourArr[$i]) && !empty($minuteArr[$i])) {
                $data = [
                    'customer_id' => $customerId,
                    'status' => 1,
                    'notes' => !empty($notes[$i]) ? $notes[$i] : '',
                    'created_user' => Auth::user()->id,
                    'updated_user' => Auth::user()->id,
                ];
                $tmpDate = explode('/', $dateArr[$i]);
                $data['datetime'] = $tmpDate[2] . '-' . $tmpDate[1] . "-" . $tmpDate[0];
                $data['datetime'] .= " " . $hourArr[$i] . ":" . $minuteArr[$i] . ":00";
                if(!empty($idArr[$i])){
                    $appointment = CustomerAppointment::find($idArr[$i]);
                    $appointment->update($data);
                }else{
                    CustomerAppointment::create($data);
                }
            }
        }

        // redirect
        Session::flash('message', 'Lên lịch hẹn thành công');
        return redirect()->route('customer.index');
    }

    public function getProduct(Request $request){
        $type = $request->type;
        $data = [];
        switch ($type) {
            case 1:
                $data = \App\Models\TourSystem::all();
                break;
            case 2:
                $data = \App\Models\Combo::all();
                break;
            case 3:
                $data = \App\Models\Hotels::where('city_id', 1)->get();
                break;
            case 4:
                $data = \App\Models\TicketCate::where('city_id', 1)->get();
                break;
            case 5:
                $data = \App\Models\CarCate::where('type', 1)->get();
                break;
        }
        return response()->json($data);
    }
}
