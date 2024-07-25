<?php

namespace App\Observers;

use App\Models\UserBalanceHistory;
use App\Models\BookingCommission;
use App\User;

class BookingCommissionObserver
{
    /**
     * Handle the BookingCommission "created" event.
     *
     * @param \App\Models\BookingCommission $bookingCommission
     * @return void
     */
    public function created(BookingCommission $bookingCommission)
    {
        if ($bookingCommission->status == BookingCommission::STATUS_CONFIRMED) {
            $this->createUserBalanceHistory($bookingCommission);
        }
    }

    /**
     * Handle the BookingCommission "updated" event.
     *
     * @param \App\Models\BookingCommission $bookingCommission
     * @return void
     */
    public function updated(BookingCommission $bookingCommission)
    {
        if ($bookingCommission->status == BookingCommission::STATUS_CONFIRMED && $bookingCommission->getOriginal('status') == BookingCommission::STATUS_PENDING) {
            $this->createUserBalanceHistory($bookingCommission);
        }
    }

    private function createUserBalanceHistory(BookingCommission $bookingCommission)
    {
        $currentBalance = User::findOrFail($bookingCommission->user_id)->balance;
        $userBalance = new UserBalanceHistory([
            'user_id' => $bookingCommission->user_id,
            'amount' => $bookingCommission->total_amount,
            'current_balance' => $currentBalance,
            'status' => UserBalanceHistory::STATUS_CONFIRMED,
            'type' => UserBalanceHistory::TRANSACTION_TYPE_COMMISSION,
            'booking_id' => $bookingCommission->booking_id
        ]);
        $userBalance->save();
    }
}
