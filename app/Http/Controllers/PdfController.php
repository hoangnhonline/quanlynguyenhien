<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use Hash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\User;
use App\Models\TourSystem;
use Helper, File, Session, Auth;
use PDF;

class PdfController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function tour(Request $request)
    {   
        $id = $request->id;
        $detail = Booking::find($id);
        //dd($detail);
        $detailUser = User::find($detail->user_id);
        $sales = "";        
        $sales = $detailUser->name;
        $sales_phone = $detailUser->phone;
        //return view('pdf-tour', compact('detail', 'sales', 'sales_phone'));
        $tourSystem =  TourSystem::where('status', 1)->pluck('name', 'id');
        $pdf = PDF::loadView('pdf-tour', compact('detail', 'sales', 'sales_phone', 'tourSystem'));
        
        return $pdf->download('PTT'.$id.'.pdf');
    } 
    public function viewPdf(Request $request)
    {   
        $id = $request->id;
        $detail = Booking::find($id);
        $detailUser = User::find($detail->user_id);
        $tourSystem =  TourSystem::where('status', 1)->pluck('name', 'id');
        $sales = $sales_phone = "";        
        if($detailUser){
            $sales = $detailUser->name;
            $sales_phone = $detailUser->phone;    
        }        
        return view('view-pdf', compact('detail', 'sales', 'sales_phone','tourSystem'));
          
        //return $pdf->download('PTT'.$id.'.pdf');
    }    
}
