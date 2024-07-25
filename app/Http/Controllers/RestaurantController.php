<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Restaurants;
use App\Models\RestaurantImg;
use App\Models\MetaData;
use App\Models\Amenity;
use App\Models\Area;

use Helper, File, Session, Auth, Image;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.is_home
     *
     * @return Response
     */
    public function price(Request $request)
    {
        $all = Restaurants::all();
        foreach ($all as $h) {
            $lowest_price = $h->getRestaurantMinPrice($h->id);
            $h->update(['lowest_price' => $lowest_price]);
        }
    }

    public function index(Request $request)
    {

        if (Auth::user()->role > 1) {
            return redirect()->route('home');
        }

        $name = isset($request->name) && $request->name != '' ? $request->name : '';
       
       
        $city_id = $request->city_id ?? session('city_id_default', Auth::user()->city_id);
        $status = $request->status ? $request->status : 1;
        $co_chi = $request->co_chi ?? null;
        $query = Restaurants::where('status', $status);
        if ($city_id) {
            $query->where('city_id', $city_id);
        }
        if ($co_chi) {
            $query->where('co_chi', $co_chi);
        }

        // check editor
        if (Auth::user()->role > 1) {
            $query->where('created_user', Auth::user()->id);
        }
        if ($name != '') {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }
        $items = $query->orderBy('is_hot', 'desc')->orderBy('id', 'desc')->paginate(100);
        $areaList = Area::where('status', 1)->get();
        return view('restaurants.index', compact('items', 'name', 'status', 'city_id', 'areaList', 'co_chi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        if (Auth::user()->role > 1) {
            return redirect()->route('home');
        }        
        $areaList = Area::where('status', 1)->get();
        return view('restaurants.create', compact('areaList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $dataArr = $request->all();
        $this->validate($request, [
            'city_id' => 'required',
            'name' => 'required',
            'area_id' => 'required'      

        ],
        [
            'city_id.required' => 'Bạn chưa chọn tỉnh thành',
            'name.required' => 'Bạn chưa nhập tên khách sạn',
            'area_id.required' => 'Bạn chưa chọn khu vực',
        ]);

        $dataArr['is_hot'] = isset($dataArr['is_hot']) ? 1 : 0;
        $dataArr['co_chi'] = isset($dataArr['co_chi']) ? 1 : 0;
        $dataArr['is_show'] = isset($dataArr['is_show']) ? 1 : 0;
        $dataArr['is_home'] = isset($dataArr['is_home']) ? 1 : 0;
        $dataArr['alias'] = str_slug($dataArr['name'], " ");
        $dataArr['slug'] = str_slug($dataArr['name'], "-");
       
        $dataArr['status'] = 1;
       
        $dataArr['created_user'] = $dataArr['updated_user'] = Auth::user()->id;

        $rs = Restaurants::create($dataArr);

        $restaurant_id = $rs->id;
       
        $this->storeImage($restaurant_id, $dataArr);      

        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('restaurants.index', ['city_id' => $dataArr['city_id']]);
    }

    public function storeMeta($id, $meta_id, $dataArr)
    {

        $arrData = ['title' => $dataArr['meta_title'], 'description' => $dataArr['meta_description'], 'keywords' => $dataArr['meta_keywords'], 'custom_text' => $dataArr['custom_text'], 'updated_user' => Auth::user()->id];
        if ($meta_id == 0) {
            $arrData['created_user'] = Auth::user()->id;
            $rs = MetaData::create($arrData);
            $meta_id = $rs->id;

            $modelSp = Restaurants::find($id);
            $modelSp->meta_id = $meta_id;
            $modelSp->save();
        } else {
            $model = MetaData::find($meta_id);
            $model->update($arrData);
        }
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
        if (Auth::user()->role > 1) {
            return redirect()->route('home');
        }
        $objectSelected = [];
        $detail = Restaurants::find($id);
        if (Auth::user()->role > 1) {
            return redirect()->route('home');
        }        
        $meta = (object)[];
        if ($detail->meta_id > 0) {
            $meta = MetaData::find($detail->meta_id);
        }        
        $areaList = Area::where('status', 1)->get();    
        return view('restaurants.edit', compact('detail', 'meta', 'areaList'));
    }

    /**
     * Update the specified resource in storage.
     *policies
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
            'area_id' => 'required'      

        ],
        [
            'city_id.required' => 'Bạn chưa chọn tỉnh thành',
            'name.required' => 'Bạn chưa nhập tên khách sạn',
            'area_id.required' => 'Bạn chưa chọn khu vực',
        ]);

        $dataArr['is_hot'] = isset($dataArr['is_hot']) ? 1 : 0;
        $dataArr['is_show'] = isset($dataArr['is_show']) ? 1 : 0;
        $dataArr['is_home'] = isset($dataArr['is_home']) ? 1 : 0;
        $dataArr['co_chi'] = isset($dataArr['co_chi']) ? 1 : 0;
        $dataArr['alias'] = str_slug($dataArr['name'], " ");
        $dataArr['slug'] = str_slug($dataArr['name'], "-");

        $dataArr['updated_user'] = Auth::user()->id;

        $model = Restaurants::find($dataArr['id']);
        
        $model->update($dataArr);
     
        $restaurant_id = $dataArr['id'];

        $this->storeImage($restaurant_id, $dataArr);

        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('restaurants.index', ['city_id' => $dataArr['city_id']]);
    }

    public function storeImage($id, $dataArr)
    {
        //process old image
        $imageIdArr = isset($dataArr['image_id']) ? $dataArr['image_id'] : [];
        $hinhXoaArr = RestaurantImg::where('restaurant_id', $id)->whereNotIn('id', $imageIdArr)->pluck('id');
        if ($hinhXoaArr) {
            foreach ($hinhXoaArr as $image_id_xoa) {
                $model = RestaurantImg::find($image_id_xoa);
                $urlXoa = config('plantotravel.upload_path') . "/" . $model->image_url;
                if (is_file($urlXoa)) {
                    unlink($urlXoa);
                }
                $model->delete();
            }
        }

        //process new image
        if (isset($dataArr['thumbnail_img'])) {
            $thumbnail_img = $dataArr['thumbnail_img'];
        }

        $imageArr = [];

        if (!empty($dataArr['image_tmp_url'])) {

            foreach ($dataArr['image_tmp_url'] as $k => $image_url) {

                $origin_img = public_path() . $image_url;

                if ($image_url) {
                    if (isset($dataArr['thumbnail_img'])) {
                        $imageArr['is_thumbnail'][] = $is_thumbnail = $dataArr['thumbnail_img'] == $image_url ? 1 : 0;
                    }
                    $img = Image::make($origin_img);
                    $w_img = $img->width();
                    $h_img = $img->height();

                    $tmpArrImg = explode('/', $origin_img);

                    $new_img = config('plantotravel.upload_thumbs_path') . end($tmpArrImg);

                    if ($w_img / $h_img > 500 / 333) {

                        Image::make($origin_img)->resize(null, 333, function ($constraint) {
                            $constraint->aspectRatio();
                        })->crop(500, 333)->save($new_img);
                    } else {
                        Image::make($origin_img)->resize(500, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->crop(500, 333)->save($new_img);
                    }
                    $new_img = config('plantotravel.upload_thumbs_path_2') . end($tmpArrImg);
                    if ($w_img / $h_img > 333 / 300) {

                        Image::make($origin_img)->resize(null, 300, function ($constraint) {
                            $constraint->aspectRatio();
                        })->crop(333, 300)->save($new_img);
                    } else {
                        Image::make($origin_img)->resize(500, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->crop(333, 300)->save($new_img);
                    }
                    $imageArr['name'][] = $image_url;

                }
            }
        }
        if (!empty($imageArr['name'])) {
            foreach ($imageArr['name'] as $key => $name) {
                $rs = RestaurantImg::create(['restaurant_id' => $id, 'image_url' => $name, 'display_order' => 1]);
                $image_id = $rs->id;
                if (isset($dataArr['thumbnail_img'])) {
                    if ($imageArr['is_thumbnail'][$key] == 1) {
                        $thumbnail_id = $image_id;
                    }
                }
            }
        }
        $model = Restaurants::find($id);
        if (isset($dataArr['thumbnail_img'])) {
            $model->thumbnail_id = $thumbnail_id;
        }
        $model->save();

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
        $model = Restaurants::find($id);
        $model->update(['status' => 0]);
        RestaurantImg::where('restaurant_id', $id)->update(['status' => 0]);       
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('restaurants.index');
    }
}
