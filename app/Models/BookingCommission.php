<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BookingCommission extends Model  {
    const STATUS_PENDING = 0;
    const STATUS_CONFIRMED = 1;
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'booking_commissions';

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

    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'booking_id');
    }
}
