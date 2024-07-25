<?php

namespace App\Http\Controllers;

use App\Models\AdsCampaign;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Cano;
use App\Models\CanoImg;
use App\Models\Steerman;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Session, Auth;

class AdsCampagnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $items = AdsCampaign::when($filters['s'] ?? false, function ($query, $s) {
                return  $query->where('name', 'like', "%$s%");
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                return  $query->where('status', $filters['status']);
            })
            ->orderBy('id', 'asc')
            ->paginate(20)
            ->appends($filters);
        return view('ads-campaign.index', compact('items', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        return view('ads-campaign.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->checkValidate($request);

        $data = $request->all();
        $data['from_date'] = Carbon::createFromFormat('d/m/Y', $data['from_date'])->format('Y-m-d');
        $data['to_date'] = Carbon::createFromFormat('d/m/Y', $data['to_date'])->format('Y-m-d');
        $data['budget'] = str_replace(',', '', $data['budget']);
        $cano = AdsCampaign::create($data);
        Session::flash('message', 'Tạo mới thành công');
        return redirect()->route('ads-campaign.index');
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
        $detail = AdsCampaign::findOrFail($id);
        return view('ads-campaign.edit', compact('detail'));
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
        $this->checkValidate($request);

        $data = $request->all();

        $detail = AdsCampaign::find($data['id']);
        $data['from_date'] = Carbon::createFromFormat('d/m/Y', $data['from_date'])->format('Y-m-d');
        $data['to_date'] = Carbon::createFromFormat('d/m/Y', $data['to_date'])->format('Y-m-d');
        $data['budget'] = str_replace(',', '', $data['budget']);
        $detail->update($data);

        Session::flash('message', 'Chỉnh sửa thành công');
        return redirect()->route('ads-campaign.index');
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
        $model = AdsCampaign::find($id);
        $model->delete();

        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('ads-campaign.index');
    }

    public function checkValidate(Request $request)
    {
        return $this->validate(
            $request,
            [
                'name' => 'required|unique:canoes,name,' . $request->input('id'),
                'from_date' => 'required',
                'to_date' => 'required',
                'budget' => 'required',
            ],
            [
                'name.required' => 'Bạn chưa nhập tên chiến dịch',
                'name.unique' => 'Tên chiến dịch bị trùng lặp',
                'from_date.required' => 'Bạn chưa chọn ngày bắt đầu',
                'to_date.required' => 'Bạn chưa chọn ngày kết thúc',
                'budget.required' => 'Bạn chưa nhập ngân sách',
            ]
        );
    }
}
