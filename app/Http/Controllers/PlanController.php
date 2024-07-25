<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Department;
use Helper, File, Session, Auth;

class PlanController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index(Request $request)
    {
        $status = isset($request->status) ? $request->status : null;

        $query = Plan::where('status','>',0);
        if( $status != null){
            $query->where('status', $status);
        }
        $items = $query->orderBy('id','desc')->paginate(20);
        return view('plan.index', compact( 'items','status'));
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $departmentList = Department::where('status',1)->orderBy('display_order','ASC')->get();

        return view('plan.create', compact('departmentList'));
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  Request  $request
    * @return Response
    */
    public function store(Request $request)
    {
        $dataArr = $request->all();

        $this->validate($request,[
            'name' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
        ],
        [
            'name.required' => 'Bạn chưa nhập tên kế hoạch',
            'from_date.required' => 'Bạn chưa nhập ngày bắt đầu',
            'to_date.required' => 'Bạn chưa nhập ngày kết thúc',
        ]);

        if(Auth::user()->role != 1){
            $dataArr['department_id'] = Auth::user()->department_id;
        }
        $dataArr['status'] = 1;
        $dataArr['created_user'] = Auth::id();
        $dataArr['updated_user'] = Auth::id();
        $dataArr['from_date'] = Carbon::createFromFormat('d/m/Y', $dataArr['from_date'])->format('Y-m-d');
        $dataArr['to_date'] = Carbon::createFromFormat('d/m/Y', $dataArr['to_date'])->format('Y-m-d');
        Plan::create($dataArr);
        Session::flash('message', 'Tạo mới kế hoạch thành công');

        return redirect()->route('plan.index');
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return Response
    */
    public function show($id)
    {
    //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return Response
    */
    public function edit($id)
    {
        $detail = Plan::find($id);
        if (Auth::user()->is_staff == 1) {
            if (Auth::id()  != $detail->created_user) {
                Session::flash('message', 'Bạn không thể chỉnh sửa kế hoạch do người khác tạo');
                return redirect()->route('plan.index');
            }
        }
        $departmentList = Department::where('status',1)->orderBy('display_order','ASC')->get();

        return view('plan.edit', compact( 'detail','departmentList' ));
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  Request  $request
    * @param  int  $id
    * @return Response
    */
    public function update(Request $request)
    {
        $dataArr = $request->all();

        $this->validate($request,[
            'name' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
        ],
        [
            'name.required' => 'Bạn chưa nhập tên kế hoạch',
            'from_date.required' => 'Bạn chưa nhập ngày bắt đầu',
            'to_date.required' => 'Bạn chưa nhập ngày kết thúc',
        ]);


        $model = Plan::find($dataArr['id']);
        $dataArr['updated_user'] = Auth::id();

        $model->update($dataArr);

        Session::flash('message', 'Cập nhật kế hoạch thành công');

        return redirect()->route('plan.edit', $dataArr['id']);
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
        $model = Plan::find($id);
        $model->delete();

        // redirect
        Session::flash('message', 'Hủy kế hoạch thành công');
        return redirect()->route('plan.index');
    }

    public function delete($id)
    {
        // delete
        $model = Plan::find($id);
        if (Auth::user()->is_staff == 1) {
            if (Auth::id()  != $model->created_user) {
                Session::flash('message', 'Bạn không thể xóa kế hoạch do người khác tạo');
                return redirect()->route('plan.index');
            }
        }
        $model->update(['status' => 0]);

        // redirect
        Session::flash('message', 'Xóa kế hoạch thành công');
        return redirect()->route('plan.index');
    }
    public function ajaxList(Request $request){

        $department_id = Auth::user()->department_id;
        $id_selected = $request->id ?? null;
        $planList = Plan::where('department_id', $department_id)->get();

        //$tagArr = $query->orderBy('id', 'desc')->get();

        return view('plan.ajax-list', compact( 'planList', 'id_selected'));
    }

    public function ajaxSave(Request $request)
    {
        $dataArr = $request->all();
        $this->validate($request,[
            'name' => 'required',
            'type' => 'required',
            ],

        [
            'name.required' => 'Bạn chưa nhập kế hoạch',
            'type.required' => 'Bạn chưa chọn loại kế hoạch',
        ]);
        $user = Auth::user();
        $dataArr['department_id'] = $user->department_id;
        $dataArr['status'] = 1;
        $dataArr['created_user'] = $dataArr['updated_user'] = $user->id;
        $rs = Plan::create($dataArr);
        return $rs->id;
    }

}
