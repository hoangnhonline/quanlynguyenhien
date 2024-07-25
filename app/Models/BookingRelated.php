<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BookingRelated extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'booking_related';

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
                            'related_id'       
                            ];
    
    public static function getBookingRelated($booking_id){
        $rs = self::where(function ($query) use ($booking_id) {
            $query->where('booking_id', $booking_id)
                  ->orWhere('related_id', $booking_id);
        })->get();
        $arrReturn = [];
        if($rs){
            foreach($rs as $bk){
                $arrReturn[$bk->booking_id] = $bk->booking_id;
                $arrReturn[$bk->related_id] = $bk->related_id;
            }
            unset($arrReturn[$booking_id]);
        }
        return $arrReturn;
    }
}
