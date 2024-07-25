<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Restaurants;
use App\Models\RestaurantImg;
use App\Models\MenuCate;
use App\Models\MenuFood;

use Helper, File, Session, Auth, Image;

class MenuCateController extends Controller
{
    public function index(Request $request)
    {

        if (Auth::user()->role > 1) {
            return redirect()->route('home');
        }     
       
        $restaurant_id = $request->restaurant_id ?? null;
        $status = $request->status ? $request->status : 1;
        $query = MenuCate::where('status', $status);
        if ($request->restaurant_id) {
            $query->where('restaurant_id', $request->restaurant_id);
        }
        $restaurantDetail = Restaurants::find($restaurant_id);
        $items = $query->orderBy('display_order')->paginate(100);        
        return view('backend.menu-cate.index', compact('items', 'restaurant_id', 'restaurantDetail'));
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
        $restaurant_id = $request->restaurant_id;
        $restaurantDetail = Restaurants::find($restaurant_id);      
        
        return view('backend.menu-cate.create', compact('restaurantDetail', 'restaurant_id'));
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
        $restaurant_id = $dataArr['restaurant_id'];        
       // dd($dataArr['display_order']);
        foreach($dataArr['name'] as $k => $name){
            $display_order = $dataArr['display_order'][$k];
            if($name){               
                $display_order = $display_order > 0 ? $display_order : $k + 1;
                MenuCate::create([
                    'restaurant_id' => $restaurant_id,
                    'name' => $name,
                    'display_order' => $display_order,
                    'created_user' => Auth::user()->id,
                    'updated_user' => Auth::user()->id
                ]);
            }
        }       
     
       
        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('menu-cate.index', ['restaurant_id' => $dataArr['restaurant_id']]);
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
        $restaurant_id = $id;     
        $restaurantDetail = Restaurants::find($restaurant_id);               
        $query = MenuCate::where('status', 1);
        
        $query->where('restaurant_id', $restaurant_id);
        
        $restaurantDetail = Restaurants::find($restaurant_id);
        $items = $query->orderBy('display_order')->get();
        return view('backend.menu-cate.edit', compact('items', 'restaurantDetail', 'restaurant_id'));
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
        $restaurant_id = $dataArr['restaurant_id'];        
       // dd($dataArr['display_order']);
        foreach($dataArr['name'] as $k => $name){
            $display_order = $dataArr['display_order'][$k];
            $id = $dataArr['id'][$k];
            if($name){               
                $display_order = $display_order > 0 ? $display_order : $k + 1;
                $model = MenuCate::find($id);
                $model->update([                    
                    'name' => $name,
                    'display_order' => $display_order,                    
                    'updated_user' => Auth::user()->id
                ]);
            }
        }      

        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('menu-cate.index', ['restaurant_id' => $dataArr['restaurant_id']]);
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
        $model = MenuCate::find($id);
        $model->update(['status' => 0]);
        MenuFood::where('menu_cate_id', $id)->update(['status' => 0]);       
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('menu-cate.index', ['restaurant_id' => $model->restaurant_id]);
    }
}
