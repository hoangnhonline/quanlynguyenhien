<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Restaurants;
use App\Models\RestaurantImg;
use App\Models\MenuFood;
use App\Models\MenuCate;
use App\Models\MenuUnit;

use Helper, File, Session, Auth, Image;

class MenuFoodController extends Controller
{
    public function index(Request $request)
    {

        if (Auth::user()->role > 1) {
            return redirect()->route('home');
        }     
        $menu_cate_id = $request->menu_cate_id ?? null;    
        
        $query = MenuFood::where('status', 1);
        
        $query->where('menu_cate_id', $request->menu_cate_id);
        
        $menuCateDetail = MenuCate::find($menu_cate_id);
        $restaurant_id = $menuCateDetail->restaurant_id;
        $restaurantDetail = Restaurants::find($restaurant_id);
        $items = $query->orderBy('display_order')->paginate(100);        
        return view('backend.menu-food.index', compact('items', 'restaurant_id', 'restaurantDetail', 'menuCateDetail', 'menu_cate_id'));
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
        $menu_cate_id = $request->menu_cate_id ?? null;            
        $menuCateDetail = MenuCate::find($menu_cate_id);
        $restaurant_id = $menuCateDetail->restaurant_id;
        $restaurantDetail = Restaurants::find($restaurant_id);      
        $menuUnitList = MenuUnit::where('status', 1)->orderBy('display_order')->get();
        return view('backend.menu-food.create', compact('restaurantDetail', 'restaurant_id', 'menuUnitList', 'menuCateDetail', 'menu_cate_id'));
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
        $menu_cate_id = $dataArr['menu_cate_id'];        
       // dd($dataArr['display_order']);
        foreach($dataArr['name'] as $k => $name){
            $display_order = $dataArr['display_order'][$k];
            $price = isset($dataArr['price'][$k]) ?  str_replace(',', '', $dataArr['price'][$k]) : 0;
            $unit_id = $dataArr['unit_id'][$k];
            if($name){               
                $display_order = $display_order > 0 ? $display_order : $k + 1;
                MenuFood::create([
                    'menu_cate_id' => $menu_cate_id,
                    'restaurant_id' => $restaurant_id,
                    'name' => $name,
                    'display_order' => $display_order,
                    'price' => $price,
                    'created_user' => Auth::user()->id,
                    'updated_user' => Auth::user()->id,
                    'unit_id' => $unit_id           
                ]);
            }
        }       
     
       
        Session::flash('message', 'Tạo mới thành công');

        return redirect()->route('menu-food.index', ['menu_cate_id' => $dataArr['menu_cate_id']]);
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
        $menu_cate_id = $id;     
        $menuCateDetail = MenuCate::find($menu_cate_id);
        $restaurant_id = $menuCateDetail->restaurant_id;
        $restaurantDetail = Restaurants::find($restaurant_id);               
        $query = MenuFood::where('status', 1);
        
        $query->where('menu_cate_id', $menu_cate_id);        
        
        $items = $query->orderBy('display_order')->get();
        $menuUnitList = MenuUnit::where('status', 1)->orderBy('display_order')->get();
        return view('backend.menu-food.edit', compact('items', 'restaurantDetail', 'restaurant_id', 'menuUnitList', 'menuCateDetail', 'menu_cate_id'));
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
        $menu_cate_id = $dataArr['menu_cate_id'];        
       // dd($dataArr['display_order']);
        
        foreach($dataArr['name'] as $k => $name){
            $display_order = $dataArr['display_order'][$k];
            $unit_id = $dataArr['unit_id'][$k];
            $price = isset($dataArr['price'][$k]) ?  str_replace(',', '', $dataArr['price'][$k]) : 0;
            $id = $dataArr['id'][$k];
            if($name){               
                $display_order = $display_order > 0 ? $display_order : $k + 1;
                $model = MenuFood::find($id);
                $model->update([                    
                    'name' => $name,
                    'display_order' => $display_order,
                    'price' => $price,                    
                    'updated_user' => Auth::user()->id,
                    'unit_id' => $unit_id
                ]);
            }
        }      

        Session::flash('message', 'Cập nhật thành công');

        return redirect()->route('menu-food.index', ['menu_cate_id' => $dataArr['menu_cate_id']]);
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
        $model = MenuFood::find($id);
        $model->update(['status' => 0]);
        MenuFood::where('menu_cate_id', $id)->update(['status' => 0]);       
        Session::flash('message', 'Xóa thành công');
        return redirect()->route('menu-food.index', ['menu_cate_id' => $model->menu_cate_id]);
    }
}
