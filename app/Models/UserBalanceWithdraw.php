<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserBalanceWithdraw extends Model
{
  const STATUS_PENDING = 'pending';
  const STATUS_COMPLETED = 'completed';

  const TRANSACTION_TYPE_WITHDRAW = 'withdraw';

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'user_balance_withdraws';

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = true;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  public function user()
  {
    return $this->belongsTo('App\User', 'user_id');
  }
}
