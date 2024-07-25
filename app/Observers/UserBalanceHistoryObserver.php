<?php

namespace App\Observers;

use App\Models\UserBalanceHistory;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UserBalanceHistoryObserver
{
    /**
     * Handle the UserBalanceHistory "created" event.
     *
     * @param \App\Models\UserBalanceHistory $balanceHistory
     * @return void
     */
    public function created(UserBalanceHistory $balanceHistory)
    {
        //Update user balance
        if ($balanceHistory->status == UserBalanceHistory::STATUS_CONFIRMED) {
            $user = User::findOrFail($balanceHistory->user_id);
            if(!$user->balance){
                $user->balance = 0;
            }
            $user->balance += $balanceHistory->amount;
            $user->save();
        }
    }

    /**
     * Handle the UserBalanceHistory "updated" event.
     *
     * @param \App\Models\UserBalanceHistory $balanceHistory
     * @return void
     */
    public function updated(UserBalanceHistory $balanceHistory)
    {
        //
        if ($balanceHistory->status == UserBalanceHistory::STATUS_CONFIRMED && $balanceHistory->getOriginal('status') != UserBalanceHistory::STATUS_CONFIRMED) {
            $user = User::findOrFail($balanceHistory->user_id);
            if(!$user->balance){
                $user->balance = 0;
            }
            $user->balance += $balanceHistory->amount;
            $user->save();
        }
    }

    /**
     * Handle the UserBalanceHistory "deleted" event.
     *
     * @param \App\Models\UserBalanceHistory $balanceHistory
     * @return void
     */
    public function deleted(UserBalanceHistory $balanceHistory)
    {
        //
        if ($balanceHistory->status == UserBalanceHistory::STATUS_CONFIRMED) {
            $user = User::findOrFail($balanceHistory->user_id);
            $user->balance -= $balanceHistory->amount;
            $user->save();
        }
    }
}
