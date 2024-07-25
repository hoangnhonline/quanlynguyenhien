<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Helper;

class RoomsPrice extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'rooms_price';	

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
        'hotel_id',
        'room_id',        
        'from_date',
        'to_date',
        'price',
        'price_goc',
        'status',        
        'created_user',
        'updated_user',
        'partner_id'
    ];
    
    public function hotel()
    {
        return $this->belongsTo('App\Models\Hotels', 'hotel_id');
    }
    public function room()
    {
        return $this->belongsTo('App\Models\Rooms', 'room_id');
    }
    public static function getPriceByDate($room_id, $date, $type, $is_min){
        $arr = [];

        $rs = self::where('room_id', $room_id)->where('from_date', '<=', $date)->where('to_date', '>=', $date)->get();   

        if(!$rs) return $arr;        
        foreach($rs as $rowPrice){            
            $price = $type == 2 ? $rowPrice->price_goc : $rowPrice->price;
            $arr[$rowPrice->partner_id] = $price;
        }
        return $arr;
    }
    public static function getPriceFromTo($room_id, $from_date, $to_date, $type = 1, $is_min = 0){ // 1 gia ban, 2 gia goc
        $dateArr = Helper::getDateFromRange($from_date, $to_date);
        $priceArr = [];
        foreach($dateArr as $date){
            $priceArr[date('d/m', strtotime($date))] =  self::getPriceByDate($room_id, $date, $type, $is_min);
        }        
        return $priceArr;
    }
    
    
}
