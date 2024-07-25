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
use App\Models\Partner;
use App\Models\BookingCustomer;
use Carbon\Carbon;
use App\Models\Location;
use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{

    private $_minDateKey = 0;
    private $_maxDateKey = 1;

    public function congNoTour(Request $request)
    {
        $day = date('d');
        $month_do = date('m');
        $arrSearch['type'] = $type = $request->type ?? 1;
        $arrSearch['code_nop_tien'] = $code_nop_tien = $request->code_nop_tien ?? null;

        $arrSearch['user_id_manage'] = $arrSearch['user_id_manage'] = $user_id_manage = $request->user_id_manage ? $request->user_id_manage : null;

        $arrSearch['coc'] = $coc = $request->coc ? $request->coc : null;
        $arrSearch['level'] = $level = $request->level ? $request->level : null;
        $arrSearch['hh0'] = $hh0 = $request->hh0 ? $request->hh0 : null;
        $arrSearch['error'] = $error = $request->error ? $request->error : null;
        $arrSearch['hdv0'] = $hdv0 = $request->hdv0 ? $request->hdv0 : null;
        $arrSearch['cano0'] = $cano0 = $request->cano0 ? $request->cano0 : null;
        $arrSearch['chua_thuc_thu'] = $chua_thuc_thu = $request->chua_thuc_thu ?? null;
        $arrSearch['tt0'] = $tt0 = $request->tt0 ?? null;
        $arrSearch['thuc_thu'] = $thuc_thu = $request->thuc_thu ?? null;
        $arrSearch['co_coc'] = $co_coc = $request->co_coc ?? null;
        $arrSearch['is_edit'] = $is_edit = $request->is_edit ?? null;
        $arrSearch['short'] = $short = $request->short ?? null;
        $arrSearch['is_grandworld'] = $is_grandworld = $request->is_grandworld ?? null;
        $arrSearch['no_cab'] = $no_cab = $request->no_cab ? $request->no_cab : null;
        $arrSearch['no_meals'] = $no_meals = $request->no_meals ? $request->no_meals : null;
        $arrSearch['price_net'] = $price_net = $request->price_net ? $request->price_net : null;
        $arrSearch['no_driver'] = $no_driver = $request->no_driver ? $request->no_driver : null;
        $arrSearch['ok'] = $ok = $request->ok ? $request->ok : null;
        $arrSearch['hh1t'] = $hh1t = $request->hh1t ? $request->hh1t : null;
        $arrSearch['sales'] = $sales = $request->sales ? $request->sales : null;
        $arrSearch['keyword'] = $keyword = $request->keyword ? $request->keyword : null;
        $arrSearch['temp'] = $temp = $request->temp ? $request->temp : null;
        $arrSearch['ko_cap_treo'] = $ko_cap_treo = $request->ko_cap_treo > -1 ? $request->ko_cap_treo : null;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1,2,4];
        $arrSearch['export'] = $export = $request->export ? $request->export : null;
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : null;
        $arrSearch['tour_cate'] = $tour_cate = $request->tour_cate ? $request->tour_cate : null;
        $arrSearch['tour_type'] = $tour_type = $request->tour_type ?? [1,2,3];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $arrSearch['driver_id'] = $driver_id = $request->driver_id ? $request->driver_id : null;
        $arrSearch['ctv_id'] = $ctv_id = $request->ctv_id ?? null;
        $arrSearch['call_status'] = $call_status = $request->call_status ? $request->call_status : null;
        $arrSearch['hdv_id'] = $hdv_id = $request->hdv_id ? $request->hdv_id : null;
        $arrSearch['alone'] = $alone = $request->alone ? $request->alone : null;
        $arrSearch['cano_id'] = $cano_id = $request->cano_id ? $request->cano_id : null;
        $arrSearch['email'] = $email = $request->email ? $request->email : null;
        $arrSearch['phone'] = $phone = $request->phone ? $request->phone : null;
        $arrSearch['name'] = $name = $request->name ? $request->name : null;
        $arrSearch['sort_by'] = $sort_by = $request->sort_by ? $request->sort_by : 'created_at';
        $arrSearch['hotel_id'] = $hotel_id = $request->hotel_id ? $request->hotel_id : null;
        $arrSearch['hotel_book'] = $hotel_book = $request->hotel_book ? $request->hotel_book : null;
        $arrSearch['camera_id'] = $camera_id = $request->camera_id ? $request->camera_id : null;
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 2;
        $arrSearch['is_send'] = $is_send = $request->is_send ?? null;
        $arrSearch['cty_send'] = $cty_send = $request->cty_send ?? null;
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : "";
        if($type == 1){
            $use_df_default = Auth::user()->id == 151 ? date('d/m/Y', strtotime('yesterday')) : date('d/m/Y', time());
            $arrSearch['use_date_from'] = $use_date_from = $request->use_date_from ? $request->use_date_from : $use_df_default;
            $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $use_date_from;

        }
        $arrSearch['created_at'] = $created_at = $request->created_at ? $request->created_at :  null;

        $arrSearch['book_date'] = $book_date = $request->book_date ? $request->book_date :  null;
        $arrSearch['book_date_from'] = $book_date_from = $request->book_date_from ? $request->book_date_from :  null;

        $arrSearch['book_date_to'] = $book_date_to = $request->book_date_to ? $request->book_date_to : null;

        $city_id = 1;
        $query = Booking::where('type', $type);
        $query->where('city_id', 1);

        $arrSearch['unc0'] = $unc0 = $request->unc0 ? $request->unc0 : null;
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
        // if($ko_cap_treo > -1){
        //     $query->where('ko_cap_treo', $ko_cap_treo);
        // }
        if($id_search){
           //  dd($id_search);
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
        }elseif($temp == 1){
            $query->where('name', 'TEMP');
        }else{
            if($coc){
                $query->where('tien_coc', '>', 0);
            }
            if($alone){
                $query->where('adults', '=', 1)
                    ->where('type', 1)
                    ->where('tour_type', 1);
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

            if($hh1t){
                $query->where('hoa_hong_sales', '>=', 1000000);
            }
            if($thuc_thu){
                $query->where('tien_thuc_thu', '<=', 0);
            }
            if($status){
                $query->whereIn('status', $status);
            }
            if($code_nop_tien){
                $query->where('code_nop_tien', $code_nop_tien);
            }
             if($export){
                $arrSearch['export'] = $export;
                $query->where('export', $export);
            }
            if($chua_thuc_thu == 1){
                $query->where('tien_thuc_thu', 0);
            }
            if($co_coc == 1){
                $query->where('tien_coc', '>', 0);
            }
            if($no_cab){
                $query->where('cap_nl', 0);
            }
            if($no_meals){
                $arrSearch['no_meals'] = $no_meals;
                $query->where('meals', 0);
            }
            if($is_grandworld){
                $arrSearch['is_grandworld'] = $is_grandworld;
                $query->where('is_grandworld', 1);
            }
            if($is_send){
                $query->where('is_send', 1);
            }
            if($price_net){
                $arrSearch['price_net'] = $price_net;
                $query->where('price_net', 1);
            }
            if($no_driver){
                $arrSearch['no_driver'] = $no_driver;
                $query->where('driver_id', 0);
            }
            if($tour_id){
                $arrSearch['tour_id'] = $tour_id;
                $query->where('tour_id', $tour_id);
            }
            if($tour_cate){
                $arrSearch['tour_cate'] = $tour_cate;
                $query->where('tour_cate', $tour_cate);
            }
            if($tour_type && $type == 1){
                $arrSearch['tour_type'] = $tour_type;
                $query->whereIn('tour_type', $tour_type);
            }
            if($cano_id){
                $arrSearch['cano_id'] = $cano_id;
                $query->where('cano_id', $cano_id);
            }
            if($user_id_manage){
                $query->where('user_id_manage', $user_id_manage);
            }
            if($phone){
                $arrSearch['phone'] = $phone;
                $query->where('phone', $phone);
            }
            if($name){
                $arrSearch['name'] = $name;
                $query->where('name', 'LIKE', '%'.$name.'%');
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
                $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien;
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }
            if($nguoi_thu_coc){
                $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc;
                $query->where('nguoi_thu_coc', $nguoi_thu_coc);
            }
            if($level && $type == 1){
                $arrSearch['level'] = $level;
                $query->where('level', $level);
            }
            if(Auth::user()->id == 333){
                $level = 7;
                $arrSearch['level'] = $level;
                $query->where('level', $level);
            }
            if($call_status){
                $arrSearch['call_status'] = $call_status;
                $query->where('call_status', $call_status);
            }
            if($email){
                $arrSearch['email'] = $email;
                $query->where('email', $email);
            }
            if($cty_send){
                $query->where('cty_send', $cty_send);
            }

            if(Auth::user()->role < 3){
                //dd($user_id);
                if(Auth::user()->role == 1 && $user_id == null && $type == 2){
                    $user_id = 18; // admin vao ks chi thay Hotline cho nhẹ
                }

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



                if($created_at){
                    $tmpDate = explode('/', $created_at);
                    $created_at_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                    $query->where('created_at','>=', $created_at_format." 00:00:00");
                    $query->where('created_at','<=', $created_at_format." 23:59:59");
                }else{
                    $rangeDate = array_unique(explode(' - ', $range_date));
                    if (empty($rangeDate[$this->_minDateKey])) {
                        //case page is initialized and range_date is empty => today
                        $rangeDate = Carbon::now();
                        $query->where('use_date','=', $rangeDate->format('Y-m-d'));
                        $arrSearch['range_date'] = $rangeDate->format('d/m/Y') . " - " . $rangeDate->format('d/m/Y');
                        $arrSearch['use_date_from'] = $rangeDate->format('d/m/Y');
                    } elseif (count($rangeDate) === 1) {
                        //case page is initialized and range_date has value,
                        //when counting the number of elements in rangeDate = 1 => only select a day
                        $query->where('use_date','=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                        $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
                        $arrSearch['use_date_from'] = $rangeDate[$this->_minDateKey];
                    } else {
                        $query->where('use_date','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                        $query->where('use_date', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
                        $arrSearch['use_date_from'] = $arrSearch['range_date'];
                    }
                }


                if($error == 1){
                    $query->whereIn('status', [1, 2])
                        ->where('hoa_hong_sales', 0)
                        ->where('total_price', 0)
                        ->where('tien_thuc_thu', 0);
                }




        }//end else
        // lay danh sach doi tac book phong


        if($driver_id){
            $query->where('driver_id', $driver_id);
        }
        if($ctv_id){
            $query->where('ctv_id', $ctv_id);
        }

        if($sales == 1){
            $query->whereNotIn('user_id', [18,33]);
        }

       // if(Auth::user()->id == 21){
       //     $query->orderBy('address', 'desc');
        //}else{
            if($type == 1 && $short == 1){

                $query->orderBy('tour_type', 'asc')->orderBy('ko_cap_treo', 'asc')->orderBy('location_id');
                //$query->orderBy('use_date', 'asc');

            }else{

                $query->orderBy($sort_by, 'desc');
            }
       // }
        if($hdv_id || $hdv0){
            if($hdv_id){
                $arrSearch['hdv_id'] = $hdv_id;
                $allItemHDV = $query->get();
                $query->where('hdv_id', $hdv_id);
            }

            if($hdv0 == 1){
                $allItemHDV = $query->get();
                //dd($allItemHDV);
                $query->where(function ($query) {
                    $query->whereNull('hdv_id')
                        ->orWhere('hdv_id', '=', 0);
                });
            }
            if($hdv0 == 2){
                $allItemHDV = $query->get();
                $query->where('hdv_id', '>', 0);
            }
        }
        if($cano_id || $cano0){
            if($cano_id){
                $arrSearch['cano_id'] = $cano_id;
                $query->where('cano_id', $cano_id);
            }

            if($cano0 == 1){
                $query->where(function ($query) {
                    $query->whereNull('cano_id')
                        ->orWhere('cano_id', '=', 0);
                });
            }
            if($cano0 == 2){
                $query->where('cano_id', '>', 0);
            }
        }
        if($error == 1){
            $query->where('status', 1)
                    ->where('hoa_hong_cty', '<=', 0)
                    ->where('checkin', '<', date('Y-m-d'));
        }


        $query->orderBy('use_date', 'asc');


       // dd($arrSearch)

        $items = $query->get();
       // dd($items);
         $i = 0;
         if(Auth::user()->role == 1){
            if($tour_id == 3){

            $contents[] = [
                'STT' => 'STT',
                'CODE' => 'CODE',
                'Tên KH' => 'Tên KH',
                'Ngày đi' => 'Ngày đi',
                //'Trạng thái' => 'Trạng thái',
                'Điện thoại' => 'Điện thoại',
                'Nơi đón' => 'Nơi đón',
                'NL' => 'NL',
                'TE' => 'TE',
                'EB' => 'EB',
                'AN' => 'AN',
                'Cáp NL' => 'Cáp NL',
                'Cáp TE' => 'Cáp TE',
                'Tổng tiền' => 'Tổng tiền',
                'PT đón' => 'PT đón',
                'Tiền TE' =>  'Tiền TE',
                'Cọc' => 'Cọc' ,
                //'CK cho ai' => 'CK cho ai',
                'Giảm' => 'Giảm',
                'Còn lại' => 'Còn lại',
                'Thực thu' => 'Thực thu',
                'Hoa hồng' => 'Hoa hồng',
                'Sales' => 'Sales',
                'HDV' => 'HDV',
                'Cano' => 'Cano',
            ];


            foreach ($items as $item) {
                $i++;
                //dd($data->item);
                if($item->tour_id == 1){
                    $loai_tour = "";
                    if($item->tour_cate == 2){
                        $loai_tour .= "\n"."2 ĐẢO";
                    }
                }elseif($item->tour_id == 3){
                    $loai_tour = "\n"."RẠCH VẸM";
                  //  dd($loai_tour);
                }elseif($item->tour_id == 4){
                    $loai_tour = "\n"."CÂU MỰC";
                }
                $address = $item->location ? $item->location->name : $item->address;
                //dd($loai_tour);
                $notes = "";
                $notes .= $item->notes;
                $cap_nl = $item->cap_nl;
                $cap_te = $item->cap_te;

                $contents[] = [
                    'STT' => $i,
                    'CODE' => 'PTT'.$item->id,
                    'Tên KH' => $item->name,
                    'Ngày đi' => date('d/m', strtotime($item->use_date)),
                   // 'Trạng thái' => $item->status == 3 ? "HỦY" : "Hoàn tất",
                    'Điện thoại' => $item->phone,
                    'Nơi đón' => $address,
                    'NL' => $item->adults,
                    'TE' => $item->childs,
                    'EB' => $item->infants,
                    'AN' => $item->meals,
                    'Cáp NL' => $cap_nl,
                    'Cáp TE' => $cap_te,
                    'Tổng tiền' => $item->total_price,
                    'PT đón' => $item->extra_fee,
                    'Tiền TE' =>  $item->total_price_child,
                    'Cọc' => $item->tien_coc,
                    //'CK cho ai' => '',
                    'Giảm' => $item->discount,
                    'Còn lại' => $item->con_lai,
                    'Thực thu' => $item->tien_thuc_thu,
                    'Hoa hồng' => $item->hoa_hong_sales,
                    'Sales' => $item->user ? $item->user->name: "",
                    'HDV' => $item->hdv ? $item->hdv->name : "",
                    'Cano' => $item->cano_id > 0 ? $item->cano->name : "",
                ];



            }
        }else{
            $contents[] = [
                'STT' => 'STT',
                'CODE' => 'CODE',
                'Tên KH' => 'Tên KH',
                'Ngày đi' => 'Ngày đi',
                //'Trạng thái' => 'Trạng thái',
                'Điện thoại' => 'Điện thoại',
                'Nơi đón' => 'Nơi đón',
                'NL' => 'NL',
                'TE' => 'TE',
                'EB' => 'EB',
                'AN' => 'AN',
                'Cáp NL' => 'Cáp NL',
                'Cáp TE' => 'Cáp TE',
                'Tổng tiền' => 'Tổng tiền',
                'Tiền TE' =>  'Tiền TE',
                'Cọc' => 'Cọc' ,
                //'CK cho ai' => 'CK cho ai',
                'Giảm' => 'Giảm',
                'Còn lại' => 'Còn lại',
                'Thực thu' => 'Thực thu',
                'Hoa hồng' => 'Hoa hồng',

            ];
            foreach ($items as $item) {
                $i++;
                //dd($data->item);
                if($item->tour_id == 1){
                    $loai_tour = "";
                    if($item->tour_cate == 2){
                        $loai_tour .= "\n"."2 ĐẢO";
                    }
                }elseif($item->tour_id == 3){
                    $loai_tour = "\n"."RẠCH VẸM";
                  //  dd($loai_tour);
                }elseif($item->tour_id == 4){
                    $loai_tour = "\n"."CÂU MỰC";
                }
                $address = $item->location ? $item->location->name : $item->address;
                //dd($loai_tour);
                $notes = "";
                $notes .= $item->notes;
                $cap_nl = $item->cap_nl;
                $cap_te = $item->cap_te;

                $contents[] = [
                    'STT' => $i,
                    'CODE' => 'PTT'.$item->id,
                    'Tên KH' => $item->name,
                    'Ngày đi' => date('d/m', strtotime($item->use_date)),
                   // 'Trạng thái' => $item->status == 3 ? "HỦY" : "Hoàn tất",
                    'Điện thoại' => $item->phone,
                    'Nơi đón' => $address,
                    'NL' => $item->adults,
                    'TE' => $item->childs,
                    'EB' => $item->infants,
                    'AN' => $item->meals,
                    'Cáp NL' => $cap_nl,
                    'Cáp TE' => $cap_te,
                    'Tổng tiền' => $item->total_price,

                    'Tiền TE' =>  $item->total_price_child,
                    'Cọc' => $item->tien_coc,
                    //'CK cho ai' => '',
                    'Giảm' => $item->discount,
                    'Còn lại' => $item->con_lai,
                    'Thực thu' => $item->tien_thuc_thu,
                    'Hoa hồng' => $item->hoa_hong_sales,

                ];

            }


        }//else
        }


       // dd($contents);
        if(count($contents) > 0){
            try{

                $str_file_name = 'PTT_booking';

                Excel::create($str_file_name, function ($excel) use ($contents) {
                  //  dd('111');
                // Set sheets
                $excel->sheet('KH', function ($sheet) use ($contents) {
                    $sheet->fromArray($contents, null, 'A1', false, false);
                });
            })->download('xlsx');
            }catch(\Exception $ex){
                dd($ex->getMessage());
            }

        }

    }
    public function customerTour(Request $request)
    {
        $arrSearch['type'] = $type = $request->type ? $request->type : 1;
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : 1;

        $arrSearch['time_type'] = $time_type = 1;
        if($type == 1){
            $arrSearch['use_date_from'] = $use_date_from = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
            $arrSearch['use_date_to'] = $use_date_to = $request->use_date_to ? $request->use_date_to : $use_date_from;

        }
        $arrSearch['created_at'] = $created_at = $request->created_at ? $request->created_at :  null;


        $arrSearch['month'] = $month = $request->month ?? date('m') - 1;
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));

        $query = Booking::where(['type' => 1, 'tour_id' => 1]);


        $query->whereIn('status', [1,2]);

        $arrSearch['use_date_from'] = $use_date_from = $arrSearch['use_date_to'] = $use_date_to = $date_use = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());

        $arrSearch['use_date_from'] = $use_date_from;
        $tmpDate = explode('/', $use_date_from);
        $use_date_from_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
        $query->where('use_date','=', $use_date_from_format);

        $items = $query->get();

         $i = 0;
         $contentsOld[] = [
                'CODE' => 'CODE',
                'Tên KH' => 'Tên KH',
                'Năm sinh' => 'Năm sinh'
            ];
        foreach ($items as $item) {
            $cusList = BookingCustomer::where('booking_id', $item->id)->where('export', 2)->get();
            foreach($cusList as $cus){

                $contentsOld[] = [
                    'CODE' => 'PTT'.$item->id,
                    'Tên KH' => ucwords($cus->name),
                    'Năm sinh' => $cus->yob
                ];
                $cus->update(['export' => 1]);
            }

        }
        $i = 0;
        foreach($contentsOld as $arr){
            $i++;
            $arr1['STT'] = $i;
            $arr2 = array_merge($arr1, $arr);
            $contents[] = $arr2;
        }
        if(count($contents) > 0){
            try{

                $str_file_name = 'PTT_Customer';

                Excel::create($str_file_name, function ($excel) use ($contents) {
                  //  dd('111');
                // Set sheets
                $excel->sheet('KH', function ($sheet) use ($contents) {
                    $sheet->fromArray($contents, null, 'A1', false, false);
                });
            })->download('xlsx');
            }catch(\Exception $ex){
                dd($ex->getMessage());
            }

        }

    }
    public function exportGui(Request $request)
    {
        $arrSearch['type'] = $type = $request->type ? $request->type : 1;
        $arrSearch['coc'] = $coc = $request->coc ? $request->coc : null;
        $arrSearch['level'] = $level = $request->level ? $request->level : null;
        $arrSearch['hh0'] = $hh0 = $request->hh0 ? $request->hh0 : null;
        $arrSearch['hdv0'] = $hdv0 = $request->hdv0 ? $request->hdv0 : null;
        $arrSearch['cano0'] = $cano0 = $request->cano0 ? $request->cano0 : null;
        $arrSearch['chua_thuc_thu'] = $chua_thuc_thu = $request->chua_thuc_thu ?? null;
        $arrSearch['tt0'] = $tt0 = $request->tt0 ?? null;
        $arrSearch['co_coc'] = $co_coc = $request->co_coc ?? null;
        $arrSearch['is_edit'] = $is_edit = $request->is_edit ?? null;
        $arrSearch['no_cab'] = $no_cab = $request->no_cab ? $request->no_cab : null;
        $arrSearch['no_meals'] = $no_meals = $request->no_meals ? $request->no_meals : null;
        $arrSearch['no_driver'] = $no_driver = $request->no_driver ? $request->no_driver : null;
        $arrSearch['ok'] = $ok = $request->ok ? $request->ok : null;
        $arrSearch['hh1t'] = $hh1t = $request->hh1t ? $request->hh1t : null;
        $arrSearch['sales'] = $sales = $request->sales ? $request->sales : null;
        $arrSearch['keyword'] = $keyword = $request->keyword ? $request->keyword : null;
        $arrSearch['temp'] = $temp = $request->temp ? $request->temp : null;
        $arrSearch['ko_cap_treo'] = $ko_cap_treo = $request->ko_cap_treo > -1 ? $request->ko_cap_treo : null;
        $arrSearch['id_search'] = $id_search = $request->id_search ? $request->id_search : null;
        $arrSearch['status'] = $status = $request->status ? $request->status : [1,2,4];
        $arrSearch['export'] = $export = $request->export ? $request->export : null;
        $arrSearch['tour_id'] = $tour_id = $request->tour_id ? $request->tour_id : null;
        $arrSearch['tour_type'] = $tour_type = $request->tour_type ?? [1,2,3];
        $arrSearch['user_id'] = $user_id = $request->user_id ? $request->user_id : null;
        $arrSearch['driver_id'] = $driver_id = $request->driver_id ? $request->driver_id : null;
        $arrSearch['call_status'] = $call_status = $request->call_status ? $request->call_status : null;
        $arrSearch['hdv_id'] = $hdv_id = $request->hdv_id ? $request->hdv_id : null;
        $arrSearch['cano_id'] = $cano_id = $request->cano_id ? $request->cano_id : null;
        $arrSearch['email'] = $email = $request->email ? $request->email : null;
        $arrSearch['phone'] = $phone = $request->phone ? $request->phone : null;
        $arrSearch['name'] = $name = $request->name ? $request->name : null;
        $arrSearch['sort_by'] = $sort_by = $request->sort_by ? $request->sort_by : 'created_at';
        $arrSearch['hotel_id'] = $hotel_id = $request->hotel_id ? $request->hotel_id : null;
        $arrSearch['camera_id'] = $camera_id = $request->camera_id ? $request->camera_id : null;
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : "";
        if($type == 1){
            $arrSearch['use_date_from'] = $use_date_from = $request->use_date_from ? $request->use_date_from : date('d/m/Y', time());
            $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $use_date_from;

        }
        $arrSearch['created_at'] = $created_at = $request->created_at ? $request->created_at :  null;

        $arrSearch['book_date'] = $book_date = $request->book_date ? $request->book_date :  null;

        $arrSearch['book_date_to'] = $book_date_to = $request->book_date_to ? $request->book_date_to : null;

        $arrSearch['month'] = $month = $request->month ?? date('m') - 1;
        $arrSearch['year'] = $year = $request->year ?? date('Y'); ;
        $mindate = "$year-$month-01";
        $maxdate = date("Y-m-t", strtotime($mindate));

        $query = Booking::where('type', $type);

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

            if($coc){
                $query->where('tien_coc', '>', 0);
            }
            if($hh0){
                $query->where(function ($query) {
                    $query->whereNull('hoa_hong_sales')
                        ->orWhere('hoa_hong_sales', '=', 0);
                });
                $query->where('status', '<>',  3);
                $query->whereNotIn('user_id', [18,33]);
            }

            if($hh1t){
                $query->where('hoa_hong_sales', '>=', 1000000);
            }
            if($status){

                $arrSearch['status'] = $status;
                $query->whereIn('status', $status);
            }
             if($export){
                $arrSearch['export'] = $export;
                $query->where('export', $export);
            }
            if($chua_thuc_thu == 1){
                $query->where('tien_thuc_thu', 0);
            }
            if($co_coc == 1){
                $query->where('tien_coc', '>', 0);
            }
            if($no_cab){
                $arrSearch['no_cab'] = $no_cab;
                $query->where('cap_nl', 0);
            }
            if($no_meals){
                $arrSearch['no_meals'] = $no_meals;
                $query->where('meals', 0);
            }
            if($no_driver){
                $arrSearch['no_driver'] = $no_driver;
                $query->where('driver_id', 0);
            }
            if($tour_id){
                $arrSearch['tour_id'] = $tour_id;
                $query->where('tour_id', $tour_id);
            }
            if($tour_type && $type == 1){
                $arrSearch['tour_type'] = $tour_type;
                $query->whereIn('tour_type', $tour_type);
            }
            if($cano_id){
                $arrSearch['cano_id'] = $cano_id;
                $query->where('cano_id', $cano_id);
            }

            if($phone){
                $arrSearch['phone'] = $phone;
                $query->where('phone', $phone);
            }
            if($name){
                $arrSearch['name'] = $name;
                $query->where('name', 'LIKE', '%'.$name.'%');
            }
            if($hotel_id){
                $arrSearch['hotel_id'] = $hotel_id;
                $query->where('hotel_id', $hotel_id);
            }
            if($nguoi_thu_tien){
                $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien;
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }
            if($nguoi_thu_coc){
                $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc;
                $query->where('nguoi_thu_coc', $nguoi_thu_coc);
            }
            if($level && $type == 1){
                $arrSearch['level'] = $level;
                $query->where('level', $level);
            }
            if($call_status){
                $arrSearch['call_status'] = $call_status;
                $query->where('call_status', $call_status);
            }
            if($email){
                $arrSearch['email'] = $email;
                $query->where('email', $email);
            }
            if(Auth::user()->role < 3){
                if($user_id){
                    $arrSearch['user_id'] = $user_id;
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
                $arrSearch['range_date'] = $rangeDate->format('d/m/Y') . " - " . $rangeDate->format('d/m/Y');
                $arrSearch['use_date_from'] = $rangeDate->format('d/m/Y');
            } elseif (count($rangeDate) === 1) {
                //case page is initialized and range_date has value,
                //when counting the number of elements in rangeDate = 1 => only select a day
                $query->where('use_date','=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
                $arrSearch['use_date_from'] = $rangeDate[$this->_minDateKey];
            } else {
                $query->where('use_date','>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                $query->where('use_date', '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
                $arrSearch['use_date_from'] = $arrSearch['range_date'];
            }




            $query->where('export', 2);
            $query->orderBy('use_date', 'asc');


           // dd($arrSearch)

            $items = $query->get();
           // dd($items);
             $i = 0;
            if($tour_id == 3){
                $contents[] = [
                    'CODE' => 'CODE',
                    'Tên KH' => 'Tên KH',
                    'Điện thoại' => 'Điện thoại',
                    'Ghi chú' => 'Ghi chú',
                    'Trung' => 'Trung',
                    'SĐT Sales' => 'SĐT Sales',
                    'Nơi đón' => 'Nơi đón',
                    'NL' => 'NL',
                    'TE' => 'TE',
                    'EB' => 'EB',
                    'AN' => 'AN',
                    'HDV Thu' => 'HDV Thu',
                    'Sales' => 'Sales',
                    'SĐT Sales' => 'SĐT Sales'
                ];
            }else{
                $contents[] = [
                    'CODE' => 'CODE',
                    'Tên KH' => 'Tên KH',
                    'Điện thoại' => 'Điện thoại',
                    'Ghi chú' => 'Ghi chú',
                    'Trung' => 'Trung',
                    'SĐT Sales' => 'SĐT Sales',
                    'Nơi đón' => 'Nơi đón',
                    'NL' => 'NL',
                    'TE' => 'TE',
                    'EB' => 'EB',
                    'AN' => 'AN',
                    'Cáp NL' => 'Cáp NL',
                    'Cáp TE' => 'Cáp TE',
                    'HDV Thu' => 'HDV Thu',
                    'Sales' => 'Sales',
                    'SĐT Sales' => 'SĐT Sales'
                ];
            }

        foreach ($items as $item) {
            $i++;

            if($item->tour_id == 1){
                $loai_tour = "";
                if($item->tour_cate == 2){
                    $loai_tour .= "\n"."2 ĐẢO";
                }
            }elseif($item->tour_id == 3){
                $loai_tour = "\n"."RẠCH VẸM";
              //  dd($loai_tour);
            }elseif($item->tour_id == 4){
                $loai_tour = "\n"."CÂU MỰC";
            }

            $address = $item->location ? $item->location->name : $item->address;
            //dd($loai_tour);
            $notes = "";
            $notes .= $item->notes;
            if($item->tour_type == 1){
                $tour_type = "";
            }else if($item->tour_type == 2){
                $tour_type = "-VIP";
            }else{
                $tour_type = "-THUÊ CANO";
            }
            $cap_nl = $item->cap_nl;
            $cap_te = $item->cap_te;
            if($tour_id == 3){
                $contents[] = [
                    'CODE' => 'PTT'.$item->id,
                    'Tên KH' => $item->name.$tour_type,
                    'Điện thoại' => $item->phone.' - '.$item->phone_1,
                    'Ghi chú' => $item->notes,
                    'Trung' => '',
                    'SĐT Sales' => $item->phone_sales,
                    'Nơi đón' => $address,
                    'NL' => $item->adults,
                    'TE' => $item->childs,
                    'EB' => $item->infants,
                    'AN' => $item->meals,
                    'HDV Thu' => $item->hdv_thu,
                    'Sales' => $item->user ? $item->user->name: "",
                    'SĐT Sales' => $item->phone_sales,
                ];
            }else{
                $contents[] = [
                    'CODE' => 'PTT'.$item->id,
                    'Tên KH' => $item->name.$tour_type,
                    'Điện thoại' => $item->phone.' - '.$item->phone_1,
                    'Ghi chú' => $item->notes,
                    'Trung' => '',
                    'SĐT Sales' => $item->phone_sales,
                    'Nơi đón' => $address,
                    'NL' => $item->adults,
                    'TE' => $item->childs,
                    'EB' => $item->infants,
                    'AN' => $item->meals,
                    'Cáp NL' => $cap_nl,
                    'Cáp TE' => $cap_te,
                    'HDV Thu' => $item->hdv_thu,
                    'Sales' => $item->user ? $item->user->name: "",
                    'SĐT Sales' => $item->phone_sales,
                ];
            }

            $item->update(['export' => 1]);
        }

       // dd($contents);
        if(count($contents) > 0){
            try{

                $str_file_name = 'PTT_booking';
                Excel::create($str_file_name, function ($excel) use ($contents) {
                  //  dd('111');
                // Set sheets
                $excel->sheet('KH', function ($sheet) use ($contents) {
                    $sheet->fromArray($contents, null, 'A1', false, false);
                });
            })->download('xlsx');
            }catch(\Exception $ex){
                dd($ex->getMessage());
            }

        }

    }

    public function congNoHotel(Request $request){
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
        $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien = $request->nguoi_thu_tien ? $request->nguoi_thu_tien : null;
        $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc = $request->nguoi_thu_coc ? $request->nguoi_thu_coc : null;
        $arrSearch['time_type'] = $time_type = $request->time_type ? $request->time_type : 3;
        $arrSearch['search_by'] = $search_by = $request->search_by ? $request->search_by : 'checkin';
        $arrSearch['created_at'] = $created_at = $request->created_at ? $request->created_at :  null;
        $arrSearch['book_date'] = $book_date = $request->book_date ? $request->book_date :  null;
        $arrSearch['book_date_from'] = $book_date_from = $request->book_date_from ? $request->book_date_from :  null;
        $arrSearch['book_date_to'] = $book_date_to = $request->book_date_to ? $request->book_date_to : null;
        $arrSearch['checkin_from'] = $checkin_from = $request->checkin_from ? $request->checkin_from : null;
        $arrSearch['checkin_to'] = $checkin_to = $request->checkin_to ? $request->checkin_to : $checkin_from;
        $arrSearch['checkout_from'] = $checkout_from = $request->checkout_from ? $request->checkout_from : null;
        $arrSearch['checkout_to'] = $checkout_to = $request->checkout_to ? $request->checkout_to : null;

        $arrSearch['range_date'] = $range_date = $request->range_date ? $request->range_date : $currentDate->format('d/m/Y') . " - " . $currentDate->format('d/m/Y');
         $arrSearch['hh0'] = $hh0 = $request->hh0 ? $request->hh0 : null;
        $query = Booking::where('type', 2);
        $query->where('city_id', 1);
        $arrSearch['unc0'] = $unc0 = $request->unc0 ? $request->unc0 : null;
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
                $arrSearch['nguoi_thu_tien'] = $nguoi_thu_tien;
                $query->where('nguoi_thu_tien', $nguoi_thu_tien);
            }
            if($nguoi_thu_coc){
                $arrSearch['nguoi_thu_coc'] = $nguoi_thu_coc;
                $query->where('nguoi_thu_coc', $nguoi_thu_coc);
            }
            if($level && $type == 1){
                $arrSearch['level'] = $level;
                $query->where('level', $level);
            }

            if(Auth::user()->role < 3){
                //dd($user_id);
                if(Auth::user()->role == 1 && $user_id == null && $type == 2){
                    $user_id = 18; // admin vao ks chi thay Hotline cho nhẹ
                }

                if($user_id && $user_id > 0){
                    $arrSearch['user_id'] = $user_id;
                    $query->where('user_id', $user_id);
                }
            }else{
                $arrSearch['user_id'] = Auth::user()->id;
                $query->where('user_id', Auth::user()->id);
            }

            if($book_date){
                $arrSearch['book_date'] = $book_date;
                $tmpDate = explode('/', $book_date);
                $book_date_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                $query->where('book_date', '>=', $book_date_format);
            }
            if($book_date_to){
                $arrSearch['book_date_to'] = $book_date_to;
                $tmpDate = explode('/', $book_date_to);
                $book_date_to_format = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                $query->where('book_date', '<=', $book_date_to_format);
            }

            $col = $search_by;

            $rangeDate = array_unique(explode(' - ', $range_date));
            if (empty($rangeDate[$this->_minDateKey])) {
                //case page is initialized and range_date is empty => this month
                $rangeDate = Carbon::now();
                $query->where($col, '=', $rangeDate->format('Y-m-d'));
            } elseif (count($rangeDate) === 1) {
                //case page is initialized and range_date has value,
                //when counting the number of elements in rangeDate = 1 => only select a day
                $query->where($col, '=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                $arrSearch['range_date'] = $rangeDate[$this->_minDateKey] . " - " . $rangeDate[$this->_minDateKey];
            } else {
                $query->where($col, '>=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_minDateKey])->format('Y-m-d'));
                $query->where($col,  '<=', Carbon::createFromFormat('d/m/Y', $rangeDate[$this->_maxDateKey])->format('Y-m-d'));
            }

        }//end else


        if($ctv_id){
            $query->where('ctv_id', $ctv_id);
        }

        if($sales == 1){
            $query->whereNotIn('user_id', [18,33]);
        }

        $query->orderBy($sort_by, 'desc');
         $items  = $query->get();
        //dd($items);
        $listUser = User::whereIn('level', [1,2,3,4,5,6,7])->where('status', 1)->get();
        $hotelList = Hotels::all();

        $i = 0;
         $contents[] = [
                'STT' => 'STT',
                'PTH' => 'PTH',
                'Tên KH' => 'Tên KH',
                'Điện thoại' => 'Điện thoại',
                'Khách sạn' => 'Khách sạn',
                'Checkin' => 'Checkin',
                'Checkout' => 'Checkout',
                'NL/TE' => 'NL/TE',
                'Tổng tiền' => 'Tổng tiền',
                'Tổng gốc' => 'Tổng gốc',
                'Cọc' => 'Cọc',
                'Còn lại' => 'Còn lại',
                'Trạng thái' => 'Trạng thái',
                'HH sales' => 'HH sales',
                'HH CTY' => 'HH CTY',
                'CODE' => 'CODE',
                'Facebook' => 'facebook'
            ];

        foreach ($items as $item) {
            $i++;
            $trang_thai = "";
            if($item->status == 1){
                $trang_thai = "Mới";
            }elseif($item->status == 1){
                $trang_thai = "Hoàn tất";
            }elseif($item->status == 3){
                $trang_thai = "Hủy";
            }elseif($item->status == 4){
                $trang_thai = "Dời ngày";
            }
            $total_original_price = 0;
            foreach($item->rooms as $r){
                $total_original_price += $r->original_price*$r->room_amount*$r->nights;
            }
            $contents[] = [
                'STT' => $i,
                'CODE' => 'PTH'.$item->id,
                'Tên KH' => $item->name,
                'Điện thoại' => $item->phone,
                'Khách sạn' => $item->hotel ? $item->hotel->name : "",
                'Checkin' => date('d/m/Y', strtotime($item->checkin)),
                'Checkout' => date('d/m/Y', strtotime($item->checkout)),
                'NL/TE' => $item->adults.'/'.$item->childs,
                'Tổng tiền' => ($item->total_price),
                'Tổng gốc' => ($total_original_price),
                'Tiền cọc' => ($item->tien_coc),
                'Còn lại' => ($item->tien_lai),
                'Trạng thái' => $trang_thai,
                'HH sales' => $item->hoa_hong_sales,
                'HH CTY' => $item->hoa_hong_cty,
                'HOTEL CODE' => $item->booking_code,
                'Facebook' => $item->facebook
            ];

        }
        //dd($contents);
        if(count($contents) > 0){
            try{

                $str_file_name = 'PTT-HOTEL-BOOKING';

                  Excel::create($str_file_name, function ($excel) use ($contents) {
                  //  dd('111');
                // Set sheets
                $excel->sheet('KH', function ($sheet) use ($contents) {
                    $sheet->fromArray($contents, null, 'A1', false, false);
                });
            })->download('xlsx');
            }catch(\Exception $ex){
                dd($ex->getMessage());
            }

        }

    }
}
