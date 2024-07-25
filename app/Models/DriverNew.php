<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DriverNew extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'drivers_new';

	 /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    protected $fillable = [      
      'name',
      'car_type_id',
      'phone',
      'join_date',
      'status',
      'cmnd',
      'image_url',
      'image_car',
      'city_id',
      'car_thumbnail_id',
      'notes'
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
}	