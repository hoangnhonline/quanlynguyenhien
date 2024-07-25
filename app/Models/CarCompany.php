<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CarCompany extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'car_company';

	 /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;    
}
