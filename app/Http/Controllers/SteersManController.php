<?php

namespace App\Http\Controllers;

use App\Models\Steerman;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Pages;
use App\Models\Account;
use Intervention\Image\Facades\Image;

use Helper, File, Session, Auth;
use Illuminate\Support\Facades\Log;

class SteersManController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $filters = $request->all();

        $items = Steerman::when($filters['s'] ?? false, function ($query, $s) {
            return  $query->where('name', 'like', "%$s%");
        })->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($filters);

        return view('steersman.index', compact('items', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {

        return view('steersman.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $this->checkValidate($request);

        $data = $request->all();
        Steerman::create($data);

        Session::flash('message', 'Tạo mới thành công');
        return redirect()->route('steersman.index');
    }

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $detail = Steerman::find($id);
        return view('steersman.edit', compact('detail'));
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
        $this->checkValidate($request);

        $data = $request->all();

        $model = Steerman::findOrFail($data['id']);
        $model->degree_img = $data['degree_img'] ?? [];
        $model->update($data);

        Session::flash('message', 'Cập nhật thành công');
        return redirect()->route('steersman.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        // delete
        $model = Steerman::where('id', $id)->first();
        $model->delete();

        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('steersman.index');
    }


    public function checkValidate(Request $request)
    {
        return $this->validate(
            $request,
            [
                'name' => 'required|string|max:255',
                'experiences' => 'required|integer|min:1',
            ],
            [
                'name.required' => 'Bạn chưa nhập tên tài công',
                'experiences.required' => 'Bạn chưa nhập số năm kinh nghiệm',
            ]
        );
    }
}
