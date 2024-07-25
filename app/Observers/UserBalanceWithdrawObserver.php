<?php

namespace App\Observers;

use App\Models\UserBalanceHistory;
use App\Models\UserBalanceWithdraw;
use App\User;

class UserBalanceWithdrawObserver
{
    /**
     * Handle the UserBalanceWithdraw "created" event.
     *
     * @param \App\Models\UserBalanceWithdraw $balanceWithdraw
     * @return void
     */
    public function created(UserBalanceWithdraw $balanceWithdraw)
    {
        if ($balanceWithdraw->status == UserBalanceWithdraw::STATUS_COMPLETED) {
            $this->createUserBalanceHistory($balanceWithdraw);
        }
    }

    /**
     * Handle the UserBalanceWithdraw "updated" event.
     *
     * @param \App\Models\UserBalanceWithdraw $balanceWithdraw
     * @return void
     */
    public function updated(UserBalanceWithdraw $balanceWithdraw)
    {
        if ($balanceWithdraw->status == UserBalanceWithdraw::STATUS_COMPLETED && $balanceWithdraw->getOriginal('status') != UserBalanceWithdraw::STATUS_COMPLETED) {
            $this->createUserBalanceHistory($balanceWithdraw);
        }
    }

    private function createUserBalanceHistory(UserBalanceWithdraw $balanceWithdraw)
    {
        $currentBalance = User::findOrFail($balanceWithdraw->user_id)->balance;
        $userBalance = new UserBalanceHistory([
            'user_id' => $balanceWithdraw->user_id,
            'amount' => -$balanceWithdraw->amount,
            'current_balance' => $currentBalance,
            'status' => UserBalanceHistory::STATUS_CONFIRMED,
            'type' => UserBalanceHistory::TRANSACTION_TYPE_WITHDRAW,
            'withdraw_id' => $balanceWithdraw->id
        ]);
        $userBalance->save();
    }
}
