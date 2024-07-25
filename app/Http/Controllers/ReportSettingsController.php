<?php

namespace App\Http\Controllers;

use App\Models\ReportSetting;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\Settings;
use Helper, File, Session, Auth, Image, Hash;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;

class ReportSettingsController extends Controller
{

    public function edit($id, Request $request)
    {

        $detail = ReportSetting::find($id);

        return view('report-setting.edit', compact( 'detail'));


    }
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */

    public function index(Request $request)
    {
        $status = $request->status ?? null;
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $items  = ReportSetting::orderBy('id', 'desc')->get();
        $view = 'report-setting.index';

        return view($view, compact( 'items', 'status', 'city_id'));

    }


    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request) {
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $view = 'report-setting.create';
        return view($view, compact('city_id'));


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
            'module' => 'required',
            'month' => 'required',
            'year' => 'required',
            'target' => 'required',
        ],
        [
            'module.required' => 'Bạn chưa chọn module',
            'month.required' => 'Bạn chưa chọn tháng',
            'year.required' => 'Bạn chưa chọn năm',
            'target.required' => 'Bạn chưa nhập mục tiêu',

        ]);

        $dataArr['target'] = str_replace(',', '', $dataArr['target']);


        $rs = ReportSetting::create($dataArr);

        Session::flash('message', 'Tạo mới thành công');
        return redirect()->route('report-setting.index');
    }


     public function update(Request $request)
    {
        $dataArr = $request->all();

        $this->validate($request,[
            'module' => 'required',
            'month' => 'required',
            'year' => 'required',
            'target' => 'required',
        ],
            [
                'module.required' => 'Bạn chưa chọn module',
                'month.required' => 'Bạn chưa chọn tháng',
                'year.required' => 'Bạn chưa chọn năm',
                'target.required' => 'Bạn chưa nhập mục tiêu',

            ]);


        $dataArr['target'] = str_replace(',', '', $dataArr['target']);
        $detail = ReportSetting::find($dataArr['id']);
        $detail->update($dataArr);
        Session::flash('message', 'Cập nhật thành công');
        return redirect()->route('report-setting.index');
    }
    /**
    * Update the specified resource in storage.
    *
    * @param  Request  $request
    * @param  int  $id
    * @return Response
    */


    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function destroy($id)
    {
        // delete
        $model = ReportSetting::find($id);
        $use_date = date('d/m/Y', strtotime($model->use_date));
        $type = $model->type;
		$model->update(['status' => 0]);
        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('report-setting.index');
    }


}
