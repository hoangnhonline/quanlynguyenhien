<?php

namespace App\Http\Controllers;

use App\Models\RestaurantSet;
use App\Models\Rooms;
use App\Models\TourPrice;
use App\Models\UserCombo;
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
use App\Models\GrandworldSchedule;
use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingComboController extends Controller
{

    public function index(Request $request)
    {
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1, 2];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;

        $query = UserCombo::whereRaw('1=1');
        if ($id_search) {
            $id_search = strtolower($id_search);
            $id_search = str_replace("ptv", "", $id_search);
            $arrSearch['id_search'] = $id_search;
            $query->where('id', $id_search);
        } else if ($status) {
            $arrSearch['status'] = $status;
            $query->whereIn('status', $status);
        }
        $query->orderBy('id', 'desc');
        $allList = $query->get();
        $items = $query->paginate(300);
        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6, 7])->where('status', 1)->get();
        $agent = new Agent();
        if ($agent->isMobile()) {
            $view = 'booking-combo.m-index';
        } else {
            $view = 'booking-combo.index';
        }
        return view($view, compact('items', 'arrSearch', 'listUser'));

    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $listTag = Location::where('city_id', $user->city_id)->where('status', 1)->get();
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
        $cateList = TicketTypeSystem::where('city_id', $city_id)->where('status', 1)->get();

        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6])->where('status', 1)->get();
        $hotelList = Hotels::where('status', 1)->select(['id', 'name'])->orderBy('name')->get();
        $tourList = Tour::where('status', 1)->select(['id', 'name'])->orderBy('name')->get();
        $restaurantSetList = RestaurantSet::where('status', 1)->select(['id', 'name', 'price'])->orderBy('name')->get();
        return view("booking-combo.add", compact('listUser', 'listTag', 'cateList', 'ctvList', 'city_id', 'hotelList', 'tourList', 'restaurantSetList'));
    }

    public function edit($id, Request $request)
    {

        $tagSelected = [];
        $keyword = $request->keyword ?? null;
        $detail = UserCombo::find($id);
        $listUser = User::whereIn('level', [1, 2, 3, 4, 5, 6])->where('status', 1)->get();
        $hotelList = Hotels::where('status', 1)->select(['id', 'name'])->orderBy('name')->get();
        $tourList = Tour::where('status', 1)->select(['id', 'name'])->orderBy('name')->get();
        $restaurantSetList = RestaurantSet::where('status', 1)->select(['id', 'name', 'price'])->orderBy('name')->get();
        return view('booking-combo.edit', compact('detail', 'listUser', 'keyword', 'hotelList', 'tourList', 'restaurantSetList'));
    }

    public function store(Request $request)
    {
        $dataArr = $request->all();

        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
            'hotel_id' => 'required',
            'room_id' => 'required',
            'room_price' => 'required',
            'tour_id' => 'required',
            'tour_price' => 'required',
            'set_id' => 'required',
            'set_price' => 'required',
        ],
            [
                'from_date.required' => 'Bạn chưa nhập ngày bắt đầu',
                'to_date.required' => 'Bạn chưa nhập ngày kết thúc',
                'room_id.required' => 'Bạn chưa chọn loại phòng.',
                'hotel_id.required' => 'Bạn chưa chọn khách sạn',
                'room_price.required' => 'Bạn chưa nhập giá phòng',
                'tour_id.required' => 'Bạn chưa chọn tour.',
                'tour_price.required' => 'Bạn chưa nhập giá tour.',
                'set_id.required' => 'Bạn chưa chọn Set ăn',
                'set_price.required' => 'Bạn chưa nhập giá set ăn'
            ]);

        $dataArr['total_price'] = (int)str_replace(',', '', $dataArr['total_price']);
        $dataArr['room_price'] = (int)str_replace(',', '', $dataArr['room_price']);
        $dataArr['tour_price'] = (int)str_replace(',', '', $dataArr['tour_price']);
        $dataArr['set_price'] = (int)str_replace(',', '', $dataArr['set_price']);
        $dataArr['from_date'] = Carbon::createFromFormat('d/m/Y', $dataArr['from_date'])->format('Y-m-d');
        $dataArr['to_date'] = Carbon::createFromFormat('d/m/Y', $dataArr['to_date'])->format('Y-m-d');
        $dataArr['user_id'] = Auth::user()->id;
        $detail = UserCombo::create($dataArr);
        return redirect()->route('booking-combo.edit', ['id' => $detail->id]);
    }

    public function update(Request $request)
    {
        $dataArr = $request->all();
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
            'hotel_id' => 'required',
            'room_id' => 'required',
            'room_price' => 'required',
            'tour_id' => 'required',
            'tour_price' => 'required',
            'set_id' => 'required',
            'set_price' => 'required',
        ],
            [
                'from_date.required' => 'Bạn chưa nhập ngày bắt đầu',
                'to_date.required' => 'Bạn chưa nhập ngày kết thúc',
                'room_id.required' => 'Bạn chưa chọn loại phòng.',
                'hotel_id.required' => 'Bạn chưa chọn khách sạn',
                'room_price.required' => 'Bạn chưa nhập giá phòng',
                'tour_id.required' => 'Bạn chưa chọn tour.',
                'tour_price.required' => 'Bạn chưa nhập giá tour.',
                'set_id.required' => 'Bạn chưa chọn Set ăn',
                'set_price.required' => 'Bạn chưa nhập giá set ăn'
            ]);

        $dataArr['total_price'] = (int)str_replace(',', '', $dataArr['total_price']);
        $dataArr['room_price'] = (int)str_replace(',', '', $dataArr['room_price']);
        $dataArr['tour_price'] = (int)str_replace(',', '', $dataArr['tour_price']);
        $dataArr['set_price'] = (int)str_replace(',', '', $dataArr['set_price']);
        $dataArr['from_date'] = Carbon::createFromFormat('d/m/Y', $dataArr['from_date'])->format('Y-m-d');
        $dataArr['to_date'] = Carbon::createFromFormat('d/m/Y', $dataArr['to_date'])->format('Y-m-d');
        $detail = UserCombo::find($dataArr['id']);
        unset($dataArr['_token']);
        $detail->update($dataArr);
        Session::flash('message', 'Cập nhật thành công');
        return redirect()->route('booking-combo.edit', ['id' => $detail->id]);
    }

    public function destroy($id)
    {
        // delete
        $model = UserCombo::find($id);
        $model->update(['status' => 0]);
        // redirect
        Session::flash('message', 'Xóa booking thành công');
        return redirect()->route('booking-combo.index');
    }

    public function getHotelRooms(Request $request)
    {
        $rooms = Rooms::where('hotel_id', $request->id)->get();
        foreach ($rooms as &$room) {
            $price = Rooms::getRoomMinPrice($room->id);

            //Get price per adult
            if (!empty($price)) {
                $price = $price / $room->adults;
            }
            $room->price = $price;
        }
        return response()->json($rooms);
    }

    public function calculatePrice(Request $request)
    {
        $fromDate = $request->from_date;
        $fromDate = Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
        $tourPrice = TourPrice::getPriceByDate($fromDate, 1, $request->tour_id, 1, 2);
        return response()->json([
            'tour_price' => !empty($tourPrice) ? $tourPrice->price : 0,
        ]);
    }
}
