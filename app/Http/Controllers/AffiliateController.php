<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\UserZalo;
use App\Models\CouponCode;
use App\Models\Media;
use App\Models\Booking;
use App\Models\BookingBk;
use App\Models\Partner;
use App\Models\Hotels;
use App\Models\Drivers;
use App\Models\DriverNew;
use App\Models\DriverImg;
use App\Models\DriverImgNew;
use App\Models\DriverArea;
use App\Models\DriverAreaNew;
use App\Models\BookingLocation;
use App\User;

class AffiliateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function index(){
        return view('affiliate.index');
    }
}
