<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use Hash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\UserMod;
use App\Models\Account;
use App\Models\Booking;
use App\Models\TourSystem;
use App\Models\MocKpi;
use App\Models\Hotels;
use Helper, File, Session, Auth;

class AccountController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function resetPass(Request $request){
        $id = $request->id;
        $detail = Account::findOrFail($id);
        $pass = $request->pass;
            $detail->update(['password' => Hash::make($pass)]);
            // echo "Đã đổi mật khẩu của tài khoản <b>".$detail->name."</b> thành: <b>". $pass."</b>";
            $mess = "Đã đổi mật khẩu của tài khoản ".$detail->name." thành: ". $pass."";
            Session::flash('message', $mess);

        return redirect()->route('staff.editPass', $id);
    }

    public function kpi(Request $request){
        $id = $request->id;
        $detail = Account::findOrFail($id);
        $month = $request->month ?? null;
        $year = $request->year ?? null;
        $tourSystem =  TourSystem::where('status', 1)->get();
        $kpiArr = [];
        if($month > 0 && $year > 0){
            $kpiList = MocKpi::where(['year_apply' => $year, 'month_apply' => $month, 'user_id' => $id])->get();
            if($kpiList->count() > 0){
                foreach($kpiList as $kpi){
                    $kpiArr[$kpi->tour_id] = $kpi->amount;
                }
            }
        }
        //dd($kpiArr);
        return view('account.kpi', compact('detail', 'id', 'tourSystem', 'month', 'year', 'kpiArr'));
    }
    public function storeKpi(Request $request){
        $dataArr = $request->all();
        $model = Account::find($dataArr['id']);
        $this->validate($request,[
            'month_apply' => 'required',
            'year_apply' => 'required',
        ],
        [
            'month_apply.required' => 'Bạn chưa chọn tháng',
            'year_apply.required' => 'Bạn chưa chọn năm'
        ]);
        $user_id = $dataArr['id'];
        $model = Account::find($dataArr['id']);

        foreach($dataArr['tour_id'] as $k => $tour_id){
            if($dataArr['amount'][$k] > 0){
                $amount = str_replace(",", "", $dataArr['amount'][$k]);
                $arr = [
                    'month_apply' => $dataArr['month_apply'],
                    'year_apply' => $dataArr['year_apply'],
                    'tour_id' => $tour_id,
                    'user_id' => $user_id
                ];

                $check = MocKpi::where($arr)->first();
                if($check){
                    $check->update(['amount' => $amount]);
                }else{
                    $arr['amount'] = $amount;
                    MocKpi::create($arr);
                }
            }
        }

        Session::flash('message', 'Lưu mốc KPI thành công');

        return redirect()->route('account.kpi', ['id' => $dataArr['id'], 'month' => $dataArr['month_apply'], 'year' => $dataArr['year_apply']]);
    }
    public function index(Request $request)
    {
        // $allUser = Account::whereIn('level', [1, 2, 7])->where('role', '>', 3)->where('status', 1)->get();
        // foreach($allUser as $user){
        //     $user_id = $user->id;
        //     $countBooking = Booking::where('use_date', '>=', '2022-11-01')->where('user_id', $user_id)->count();
        //     if($countBooking == 0){
        //         $user->update(['status' => 2]);
        //     }
        // }
        //dd('1111');

        if( Auth::user()->role > 2 ){
            return redirect()->route('home');
        }
        $status = $request->status ?? 1;
        $query = Account::where('status', $status)->where('role', '>', 3);
        $user_type = $request->user_type ?? null;
        $city_id = $request->city_id ?? Auth::user()->city_id;
        $role = $request->role ?? null;

        $level = $request->level ?? null;
        $email = $request->email ?? null;
        $user_id_manage = $request->user_id_manage ?? null;
        $debt_type = $request->debt_type ?? null;
        if(Auth::user()->id == 333){
            $user_id_manage = 333;
        }
        $hdv = $request->hdv ?? null;
        $phone = $request->phone ?? null;
        if($user_type){
            $query->where('user_type', $user_type);
        }
        if($city_id){
            $query->where('city_id', $city_id);
        }
        if($role){
            $query->where('role', $role);
        }
        if($user_id_manage){
            $query->where('user_id_manage', $user_id_manage);
        }
        if($debt_type){
            $query->where('debt_type', $debt_type);
        }

        if($level){
            $query->where('level', $level);
        }
        if($phone){
            $query->where('phone', $phone);
        }
        if($hdv){
            $query->where('hdv', 1);
        }
        if($email){
            $query->where('email', $email);
        }
        //dd($role);
        $hotelList = Hotels::where('status', 1)->get();
        $items = $query->orderBy('id', 'desc')->paginate(1000);
        return view('account.index', compact('items', 'email', 'phone', 'user_type', 'role', 'level', 'hdv', 'user_id_manage', 'debt_type', 'city_id', 'status', 'hotelList'));
    }
    public function create()
    {
        if(Auth::user()->role > 2){
            return redirect()->route('home');
        }
        $modList = Account::where(['role' => 2, 'status' => 1])->get();

        return view('account.create', compact('modList'));
    }
    public function ajaxSave(Request $request)
    {
        $dataArr = $request->all();

        $add_name = trim(ucwords($request->add_name));
        $add_phone = $request->add_name;

        if( $add_name != "" && $add_phone != ''){
            // check xem co chua
            $arr = Account::where('phone', '=', $add_phone)->first();
            if( !empty( (array) $arr)) {
                $return_id = $arr->id;
            }else{
                $rs = Account::create([
                    'name'=> $add_name,
                    'phone' => $add_phone,
                    'level' => 1,
                    'role' => 5,
                    'status' => 1,
                    'email' => $add_phone.'@gmail.com',
                   ]);
                $return_id = $rs->id;
            }

        }
        return $return_id;

    }
    public function ajaxList(Request $request){

        $id_selected = $request->sales_id ?? null;
        $tagArr = Account::all();

        //$tagArr = $query->orderBy('id', 'desc')->get();

        return view('location.ajax-list', compact( 'tagArr', 'id_selected'));
    }
    public function createTx()
    {
        if(Auth::user()->role > 2){
            return redirect()->route('home');
        }
        $modList = Account::where(['role' => 2, 'status' => 1])->get();

        return view('account.create-tx', compact('modList'));
    }
    public function createDt()
    {
        if(Auth::user()->role > 2){
            return redirect()->route('home');
        }


        return view('account.create-dt');
    }
    public function changePass(){
        return view('account.change-pass');
    }

    public function storeNewPass(Request $request){
        $user_id = Auth::user()->id;
        $detail = Account::find($user_id);
        $old_pass = $request->old_pass;
        $new_pass = $request->new_pass;
        $new_pass_re = $request->new_pass_re;

         $this->validate($request,[
            'old_pass' => 'required',
            'new_pass' => 'required|between:6,30',
            'new_pass_re' => 'required|same:new_pass|between:6,30'
        ],
        [
            'old_pass.required' => 'Bạn chưa nhập mật khẩu hiện tại',
            'new_pass.required' => 'Bạn chưa nhập mật khẩu',
            'new_pass.between' => 'Nhập lại mật khẩu phải từ 6 đến 30 ký tự',
            'new_pass_re.required' => 'Bạn chưa nhập lại mật khẩu',
            'new_pass_re.between' => 'Mật khẩu phải từ 6 đến 30 ký tự',
            'new_pass_re.same' => 'Mật khẩu nhập lại không giống'
        ]);
        if( $old_pass == '' || $new_pass == "" || $new_pass_re == ""){
            return redirect()->back()->withErrors(["Chưa nhập đủ thông tin bắt buộc!"])->withInput();
        }

        if(!password_verify($old_pass, $detail->password)){
            return redirect()->back()->withErrors(["Nhập mật khẩu hiện tại không đúng!"])->withInput();
        }

        if($new_pass != $new_pass_re ){
            return redirect()->back()->withErrors("Xác nhận mật khẩu mới không đúng!")->withInput();
        }


        $detail->password = Hash::make($new_pass);
        $detail->save();
        Session::flash('message', 'Đổi mật khẩu thành công');

        return redirect()->route('account.change-pass');

    }
    public function storeTx(Request $request)
    {

        if(Auth::user()->role > 2){
            return redirect()->route('home');
        }
        $dataArr = $request->all();
        $dataArr['email'] = $dataArr['phone'].'@gmail.com';
        $this->validate($request,[
            'phone' => 'required|unique:users,phone',
            'name' => 'required',
            'email' => 'unique:users,email',
        ],
        [
            'phone.required' => 'Bạn chưa nhập số điện thoại',
            'name.required' => 'Bạn chưa nhập tên',
            'email.required' => 'Bạn chưa nhập email',
            'email.unique' => 'Email đã được sử dụng.',
            'email.email' => 'Bạn nhập email không hợp lệ',
        ]);

        $dataArr['password'] = Hash::make($dataArr['password']);


        $code = substr(str_shuffle(str_repeat("SQERTYUIOPADFGHJKLZXCVBNM", 5)), 0, 5);
        if(Auth::user()->id ==333){
            $dataArr['user_id_manage'] = 333;
            $dataArr['level'] = 7;
        }
        $dataArr['code'] = $code;
        $rs = Account::create($dataArr);
        /*
        if ( $rs->id > 0 ){
            Mail::send('account.mail', ['fullname' => $request->fullname, 'password' => $tmpPassword, 'email' => $request->email], function ($message) use ($request) {
                $message->from( config('mail.username'), config('mail.name'));

                $message->to( $request->email, $request->fullname )->subject('Mật khẩu đăng nhập hệ thống');
            });
        }*/

        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('account.index');
    }
    public function store(Request $request)
    {

        if(Auth::user()->role > 2){
            return redirect()->route('home');
        }
        $dataArr = $request->all();

        $this->validate($request,[
            'city_id' => 'required',
            'phone' => 'required|unique:users,phone',
            'name' => 'required',
            'email' => 'email|required|unique:users,email',
            'password' => 'required|between:6,30',
            're_password' => 'required|same:password|between:6,30',
            'level' => 'required'
        ],
        [
            'city_id.required' => 'Bạn chưa chọn tỉnh/thành',
            'phone.required' => 'Bạn chưa nhập số điện thoại',
            'name.required' => 'Bạn chưa nhập tên',
            'email.required' => 'Bạn chưa nhập email',
            'email.unique' => 'Email đã được sử dụng.',
            'email.email' => 'Bạn nhập email không hợp lệ',
            'password.required' => 'Bạn chưa nhập mật khẩu',
            'password.between' => 'Nhập lại mật khẩu phải từ 6 đến 30 ký tự',
            're_password.required' => 'Bạn chưa nhập lại mật khẩu',
            're_password.between' => 'Mật khẩu phải từ 6 đến 30 ký tự',
            're_password.same' => 'Mật khẩu nhập lại không giống',
            'level.required' => 'Bạn chưa chọn phân loại'
        ]);

        $dataArr['password'] = Hash::make($dataArr['password']);


        $code = substr(str_shuffle(str_repeat("QWERTYUIOPASDFGHJKLZXCVBNM", 5)), 0, 5);
        $dataArr['code'] = $code;
        $rs = Account::create($dataArr);
        /*
        if ( $rs->id > 0 ){
            Mail::send('account.mail', ['fullname' => $request->fullname, 'password' => $tmpPassword, 'email' => $request->email], function ($message) use ($request) {
                $message->from( config('mail.username'), config('mail.name'));

                $message->to( $request->email, $request->fullname )->subject('Mật khẩu đăng nhập hệ thống');
            });
        }*/

        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('account.index', ['city_id' => $dataArr['city_id']]);
    }
    public function destroy($id)
    {
        if(Auth::user()->role > 2){
            return redirect()->route('home');
        }
        // delete
        $model = Account::find($id);
        $model->update(['status' => 1]);

        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('account.index');
    }
    public function edit($id)
    {
        if(Auth::user()->role > 2){
            return redirect()->route('home');
        }
        $detail = Account::find($id);
        $tourSystem =  TourSystem::where('status', 1)->get();
        $kpiList = MocKpi::where('user_id', $id)->get();
        $arrKpi = [];
        foreach($kpiList as $kpi){
            $arrKpi[$kpi->tour_id] = $kpi->amount;
        }

        return view('account.edit', compact( 'detail', 'tourSystem', 'arrKpi'));
    }
    public function update(Request $request)
    {
        if(Auth::user()->role > 2){
            return redirect()->route('home');
        }
        $dataArr = $request->all();
        $model = Account::find($dataArr['id']);
       $this->validate($request,[
            'city_id' => 'required',
            'code' => 'required|unique:users,code,'.$model->id.',id',
            'phone' => 'required|unique:users,phone,'.$model->id.',id',
            'name' => 'required',
            'email' => 'email|required|unique:users,email,'.$model->id.',id',
            'level' => 'required'
        ],
        [
            'city_id.required' => 'Bạn chưa chọn tỉnh/thành',
            'code.required' => 'Bạn chưa nhập CODE',
            'code.unique' => 'CODE đã tồn taị',
            'phone.required' => 'Bạn chưa nhập số điện thoại',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'name.required' => 'Bạn chưa nhập tên',
            'email.required' => 'Bạn chưa nhập email',
            'email.unique' => 'Email đã được sử dụng.',
            'email.email' => 'Bạn nhập email không hợp lệ',
            'level.required' => 'Bạn chưa chọn phân loại'
        ]);
        $user_id = $dataArr['id'];
        $model = Account::find($dataArr['id']);

      //  $dataArr['updated_user'] = Auth::user()->id;

        $model->update($dataArr);
        MocKpi::where('user_id', $user_id)->delete();
        foreach($dataArr['tour_id'] as $k => $tour_id){
            if($dataArr['amount'][$k] > 0){
                MocKpi::create([
                    'user_id' => $user_id,
                    'tour_id' => $tour_id,
                    'amount' => str_replace(",", "", $dataArr['amount'][$k])
                ]);
            }
        }

        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('account.index');
    }
    public function updateStatus(Request $request)
    {
        if(Auth::user()->role > 2){
            return redirect()->route('home');
        }
        $model = Account::find( $request->id );


        $model->updated_user = Auth::user()->id;
        $model->status = $request->status;

        $model->save();
        $mess = $request->status == 1 ? "Mở khóa thành công" : "Khóa thành công";
        Session::flash('message', $mess);

        return redirect()->route('account.index');
    }
    public function doitac(Request $request)
    {   

        if( Auth::user()->role > 2 ){
            return redirect()->route('home');
        }
        $status = $request->status ?? 1;
        $query = Account::where('status', 1)->where('role', '>', 3)->where('hotel_id', '>', 0);
        $user_type = $request->user_type ?? null;
        $city_id = $request->city_id ?? Auth::user()->city_id;


        $level = 2;
        $email = $request->email ?? null;
        
        if($city_id){
            $query->where('city_id', $city_id);
        }       

        if($level){
            $query->where('level', $level);
        }
        if($email){
            $query->where('email', $email);
        }
        //dd($role);
        $hotelList = Hotels::where('status', 1)->get();
        $items = $query->orderBy('id', 'desc')->paginate(1000);
        return view('account.doi-tac', compact('items', 'email', 'user_type', 'level', 'city_id', 'status', 'hotelList'));
    }
}
