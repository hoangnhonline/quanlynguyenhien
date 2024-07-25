<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Cano;
use App\Models\CanoImg;
use App\Models\Steerman;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Session, Auth;

class CanoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $canoes = Cano::with(['thumbnail', 'steerman'])
            ->when($filters['s'] ?? false, function ($query, $s) {
                return  $query->where('name', 'like', "%$s%");
            })
            ->when($filters['steersman_id'] ?? false, function ($query, $steersman_id) {
                return  $query->where('steersman_id', $steersman_id);
            })
            ->when($filters['kind_of_property'] ?? false, function ($query, $kind_of_property) {
                return  $query->where('kind_of_property', $kind_of_property);
            })
            ->when($filters['type'] ?? false, function ($query, $type) {
                return  $query->where('type', $type);
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                return  $query->where('status', $filters['status']);
            })
            ->orderBy('id', 'asc')
            ->paginate(20)
            ->appends($filters);


        $data = [
            'steersman' => Steerman::get()
        ];
        return view('cano.index', compact('data', 'filters', 'canoes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $data = [
            'steersman' => Steerman::get()
        ];

        return view('cano.create', compact('data'));
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
        $cano = Cano::create($data);
        $this->storeImage($cano->id, $data);

        Session::flash('message', 'Tạo mới thành công');
        return redirect()->route('cano.index');
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

        $data = [
            'steersman' => Steerman::get()
        ];
        $cano = Cano::with(['images'])->findOrFail($id);
        return view('cano.edit', compact('cano', 'data'));
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

        $cano = Cano::find($data['id']);
        $cano['certificate_of_insurance_img'] = $data['certificate_of_insurance_img'] ?? [];
        $cano['certificate_of_registry_img'] = $data['certificate_of_registry_img'] ?? [];
        $cano->update($data);

        $this->storeImage($data['id'], $data);

        Session::flash('message', 'Chỉnh sửa thành công');
        return redirect()->route('cano.index');
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
        $model = Cano::find($id);
        $model->delete();

        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('cano.index');
    }

    public function checkValidate(Request $request)
    {
        return $this->validate(
            $request,
            [
                'name' => 'required|unique:canoes,name,' . $request->input('id'),
                'steersman_id' => 'required',
                'kind_of_property' => 'required',
                'type' => 'required',
                'seats' => 'required',
            ],
            [
                'name.required' => 'Bạn chưa nhập tên cano',
                'name.unique' => 'Tên cano bị trùng lặp',
                'steersman_id.required' => 'Bạn chưa chọn tài công',
                'kind_of_property.required' => 'Bạn chưa chọn loại tài sản',
                'type.required' => 'Bạn chưa chọn loại cano',
                'seats.required' => 'Bạn chưa nhập số chỗ ngồi',
            ]
        );
    }

    public function storeImage($id, $data)
    {
        //process old image
        $imgsId = isset($data['image_id']) ? $data['image_id'] : [];
        $imgsIdRemove = CanoImg::where('cano_id', $id)->whereNotIn('id', $imgsId)->pluck('id');
        if ($imgsIdRemove) {
            foreach ($imgsIdRemove as $_id) {
                $model = CanoImg::find($_id);
                $model->delete();
            }
        }

        //process new image
        $imageArr = [];
        $thumbnail_id = [];
        if (!empty($data['image_tmp_url'])) {

            foreach ($data['image_tmp_url'] as $image_url) {

                $origin_img = public_path() . $image_url;
                if ($image_url) {
                    $imageArr['is_thumbnail'][] = ($data['thumbnail_img'] ?? $image_url) == $image_url  ? 1 : 0;
                    $img = Image::make($origin_img);
                    $w_img = $img->width();
                    $h_img = $img->height();

                    $tmpArrImg = explode('/', $origin_img);

                    // set permission
                    $thumbsPath = config('plantotravel.upload_thumbs_path');
                    $thumbsPath2 = config('plantotravel.upload_thumbs_path_2');
                    if (!is_dir($thumbsPath)) {
                        mkdir($thumbsPath, 0755, true);
                    }
                    if (!is_dir($thumbsPath2)) {
                        mkdir($thumbsPath2, 0755, true);
                    }

                    $new_img = $thumbsPath . end($tmpArrImg);
                    if ($w_img / $h_img > 550 / 350) {

                        Image::make($origin_img)->resize(null, 350, function ($constraint) {
                            $constraint->aspectRatio();
                        })->crop(550, 350)->save($new_img);
                    } else {
                        Image::make($origin_img)->resize(550, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->crop(550, 350)->save($new_img);
                    }
                    $new_img = $thumbsPath2 . end($tmpArrImg);
                    if ($w_img / $h_img > 350 / 300) {

                        Image::make($origin_img)->resize(null, 300, function ($constraint) {
                            $constraint->aspectRatio();
                        })->crop(350, 300)->save($new_img);
                    } else {
                        Image::make($origin_img)->resize(550, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->crop(350, 300)->save($new_img);
                    }
                    $imageArr['name'][] = $image_url;
                }
            }
        }

        if (!empty($imageArr['name'])) {
            foreach ($imageArr['name'] as $key => $name) {
                $rs = CanoImg::create(['cano_id' => $id, 'image_url' => $name, 'display_order' => 1]);
                $image_id = $rs->id;
                if ($imageArr['is_thumbnail'][$key] == 1) {
                    $image_id = $rs->id;
                    $thumbnail_id = $image_id;
                }
            }
        }

        if ($thumbnail_id) {
            $model = Cano::find($id);
            $model->thumbnail_id = $thumbnail_id;
            $model->save();
        }
    }
}
