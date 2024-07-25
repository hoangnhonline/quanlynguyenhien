<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PartnerCity extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'partner_city';

	 /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    protected $fillable = [      
      'partner_id',
      'city_id',
    ];

    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id');
    }
}	