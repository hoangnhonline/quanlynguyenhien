<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TourPrice extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tour_price';	

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
    protected $fillable = ['tour_id', 'partner_system_id', 'tour_type', 'extra_fee', 'level', 'from_adult', 'to_adult', 'price', 'price_child', 'cap_nl', 'cap_te', 'meals', 'meals_te', 'from_date', 'to_date', 'cano_type', 'price_child_no_cable'];
    
    public static function getPriceByDate($date, $partner_id, $tour_id, $tour_type, $level, $cano_type = 1){        
        return self::where([
                    'tour_id' => $tour_id,
                    'partner_system_id' => $partner_id,
                    'tour_type' => $tour_type,
                    'level' => $level,
                    'cano_type' => $cano_type
                ])->where('from_date', '<=', $date)->where('to_date', '>=', $date)->orderBy('id','desc')->first();  
    }
}
