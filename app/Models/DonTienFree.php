<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DonTienFree extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'don_tien_free';	

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
    protected $fillable = ['booking_id', 'location_id', 'type', 'use_date', 'car_cate_id', 'use_time', 'driver_id', 'related_id', 'notes',
        'phone', 'name', 'status', 'user_id', 'location_id_2', 'use_date_time', 'cost'
    ];
    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'booking_id');
    }
    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }
    public function location2()
    {
        return $this->belongsTo('App\Models\Location', 'location_id_2');
    }
    public function carCate()
    {
        return $this->belongsTo('App\Models\CarCate', 'car_cate_id');
    }
    public function driver()
    {
        return $this->belongsTo('App\Models\Drivers', 'driver_id');
    }
    public function payment()
    {
        return $this->hasMany('App\Models\DonTienFreePayment', 'don_tien_id');
    }
    
}
