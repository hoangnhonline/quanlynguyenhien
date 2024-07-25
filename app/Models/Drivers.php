<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Drivers extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'drivers';

	 /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true; 


    protected $fillable = [      
      'name',
      'car_cate_id',
      'phone',
      'join_date',
      'status',
      'cmnd',
      'image_url',
      'image_car',
      'city_id',
      'car_thumbnail_id',
      'notes',
      'is_tour',
      'is_verify',
      'is_support'
    ];
    public function area()
    {
        return $this->hasMany('App\Models\DriverArea', 'driver_id');
    }
    public function booking()
    {
        return $this->hasMany('App\Models\Booking', 'driver_id');
    }
    public function images()
    {
        return $this->hasMany('App\Models\DriverImg', 'driver_id');
    }
    public function car()
    {
        return $this->belongsTo('App\Models\CarCate', 'car_cate_id');
    }
}	