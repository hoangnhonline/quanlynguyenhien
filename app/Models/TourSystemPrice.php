<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Helper;

class TourSystemPrice extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tour_system_price';	

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
    protected $fillable = [
        'tour_id',       
        'from_date',
        'to_date',
        'price',
        'adult_cost',
        'child_cost',
        'status',        
        'created_user',
        'updated_user',
        'partner_id'
    ];
    
    public function tour()
    {
        return $this->belongsTo('App\Models\TourSystem', 'tour_id');
    }
    public static function getPriceByDate($partner_id, $tour_id, $date){
        return self::where('tour_id', $tour_id)
        	->where('partner_id', $partner_id)
        	->where('from_date', '<=', $date)->where('to_date', '>=', $date)->first();  
    }
    public static function getPriceFromTo($tour_system_id, $from_date, $to_date, $type = 1, $is_min = 0){ // 1 gia ban, 2 gia goc
        $dateArr = Helper::getDateFromRange($from_date, $to_date);
        $priceArr = [];
        foreach($dateArr as $date){
            $priceArr[date('d/m', strtotime($date))] =  self::getPriceByDate($tour_system_id, $date, $type, $is_min);
        }        
        return $priceArr;
    }
    
    
}
