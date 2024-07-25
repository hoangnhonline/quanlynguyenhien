<?php

namespace App\Observers;

use App\Helpers\Helper;
use App\Models\Booking;
use App\Models\BookingCommission;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     *
     * @param  Booking $booking
     * @return void
     */
    public function created(Booking $booking)
    {
//        $commissions = \App\Helpers\Helper::calculateAffiliateCommission($booking->customer_id);
//        if(!empty($commissions)){
//            $index = 0;
//            foreach ($commissions as $userId => $commission){
//                $bookingCommission = new BookingCommission([
//                    'booking_id' => $booking->id,
//                    'user_id' => intval($userId),
//                    'level' => $index + 1,
//                    'amount' => floatval($commission),
//                    'status' => BookingCommission::STATUS_PENDING
//                ]);
//                $bookingCommission->save();
//                $index++;
//            }
//        }
    }

    /**
     * Handle the Booking "updated" event.
     *
     * @param  Booking  $booking
     * @return void
     */
    public function updated(Booking $booking)
    {
        if($booking->type == 1 && $booking->status == 2 && $booking->getOriginal('status') != $booking->status){
            //Check if booking have affiliate user
            if(!empty($booking->commissions)){
                foreach ($booking->commissions as $commission){
                    $commission->status = BookingCommission::STATUS_CONFIRMED;
                    $commission->save();
                }
            }
        }
    }

    /**
     * Handle the Booking "deleted" event.
     *
     * @param  Booking  $booking
     * @return void
     */
    public function deleted(Booking $booking)
    {
    }

    /**
     * Handle the Booking "restored" event.
     *
     * @param  Booking  $booking
     * @return void
     */
    public function restored(Booking $booking)
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     *
     * @param  Booking  $booking
     * @return void
     */
    public function forceDeleted(Booking $booking)
    {
        //
    }
}
