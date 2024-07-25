<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class BookingLogs extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'booking_logs';

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
    protected $fillable = ['booking_id',
                            'user_id',
                            'content',
                            'notes',
                            'action'
                            ];

    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'booking_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUserNameAttribute(){
        return !empty($this->user) ? $this->user->name : 'N/A';
    }
}
