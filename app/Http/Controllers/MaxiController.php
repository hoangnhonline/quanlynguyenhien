<?php

namespace App\Http\Controllers;

use App\Models\MaxiImg;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Maxi;
use App\Models\MaxiHistory;


use Helper, File, Session, Auth, Image;

class MaxiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = Maxi::query();
        $items = $query->where('status' , '>', 0)->orderBy('id')->paginate(100);
        return view('maxi.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {

        return view('maxi.create');
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
        $this->validate($request, [
            'name' => 'required',
            'display_order' => 'required'
        ],
            [
                'name.required' => 'Bạn chưa nhập tên mẫu',
                'display_order.required' => 'Bạn chưa nhập thứ tự hiển thị',
            ]);

        $rs = Maxi::create($dataArr);
        $maxi_id = $rs->id;


        $this->storeImage($maxi_id, $dataArr);
        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('maxi.index');
    }
    public function storeImage($id, $dataArr)
    {
        #Xoa het hinh cu
        MaxiImg::where('maxi_id', $id)->delete();
        //process new image
        foreach ($dataArr['image_tmp_url'] ?? [] as $k => $image_url) {
            $origin_img = public_path() . $image_url;
            if ($image_url) {
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
                $rs = MaxiImg::create(['maxi_id' => $id, 'image_url' => $image_url, 'display_order' => 1]);
                if (isset($dataArr['thumbnail_img']) && ($dataArr['thumbnail_img'] == $image_url)) {
                    Maxi::where('id', $id)->update(['thumbnail_id' => $rs->id]);
                };
            }
        }
    }

    public function storeMeta( $id, $meta_id, $dataArr ){
        //dd($meta_id);
        $arrData = [ 'title' => $dataArr['meta_title'], 'description' => $dataArr['meta_description'], 'keywords'=> $dataArr['meta_keywords'], 'custom_text' => $dataArr['custom_text'], 'updated_user' => Auth::user()->id ];
        if( $meta_id == 0){
            $arrData['created_user'] = Auth::user()->id;
            $rs = MetaData::create($arrData);
            $meta_id = $rs->id;

            $model = TicketCate::find($id);
            $model->meta_id = $meta_id;
            $model->save();
        }else {
            $model = MetaData::find($meta_id);
            $model->update( $arrData );
        }
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

        $detail = Maxi::find($id);

        return view('maxi.edit', compact('detail'));
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
        $this->validate($request, [
            'name' => 'required',
            'display_order' => 'required'
        ],
            [
                'name.required' => 'Bạn chưa nhập tên mẫu',
                'display_order.required' => 'Bạn chưa nhập thứ tự hiển thị'
            ]);

        $model = Maxi::find($dataArr['id']);
        $model->update($dataArr);
        $maxi_id = $dataArr['id'];

        $this->storeImage($maxi_id, $dataArr);
        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('maxi.index');
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
        $model = Maxi::find($id);
        $model->update(['status' => 0]);

        MaxiImg::where('maxi_id', $id)->delete();
        MaxiHistory::where('maxi_id', $id)->delete();
        // redirect
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('maxi.index');
    }
}
