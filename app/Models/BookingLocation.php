<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BookingLocation extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'booking_location';

	 /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['booking_id', 
                            'location_id'         
                            ];
    
    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'booking_id');
    }   
    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }
}
