<?php

namespace App\Http\Controllers;
use App\Models\Maxi;
use App\Models\Booking;
use App\Models\MaxiHistory;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Helper, File, Session, Auth, Image;

class MaxiHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $maxi_id = $request->id;
        $detail = Maxi::find($maxi_id);
      
        $query = MaxiHistory::where('maxi_id', $maxi_id);
        $items = $query->orderBy('id')->paginate(1000);
        $arrSelected = [];
        foreach($items as $item){
            $arrSelected[] = $item->date;
        }        
        $arrBooking = Booking::getBookingForRelated();
        return view('maxi.history', compact('items', 'arrBooking', 'maxi_id', 'detail', 'arrSelected'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {


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
       
        $validatedData = $request->validate([
            'related_id' => 'required|array',
            'date' => 'required',
        ], [
            'related_id.required' => 'Vui lòng chọn ít nhất một Booking ID.',
            'date.required' => 'Vui lòng nhập Ngày mượn.',
        ]);
     
        $tmpArr = explode('/', $validatedData['date']);
        //dd($tmpArr);
        $date_format = $tmpArr[2].'/'.$tmpArr[1].'/'.$tmpArr[0];
          
        // Lưu dữ liệu vào cơ sở dữ liệu
        foreach ($validatedData['related_id'] as $bookingId) {
            MaxiHistory::create([
                'booking_id' => $bookingId,
                'date' => $date_format,
                'maxi_id' => $dataArr['maxi_id']
            ]);                   
        }

        Session::flash('message', 'Đã tạo lịch mượn thành công.');
        return back();
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        MaxiHistory::where('id', $id)->delete();
        // redirect
        Session::flash('message', 'Xóa thành công');
        return back();
    }
}
