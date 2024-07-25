<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BookingRooms extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'booking_rooms';

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
    protected $fillable = ['booking_id', 
                            'room_name',
                            'room_id',                           
                            'room_amount', 
                            'nights', 
                            'original_price', 
                            'price_sell',
                            'notes',
                            'hoa_hong_cty',
                            'hoa_hong_sales',
                            'total_price',
                            'checkin',
                            'checkout',
                            'adults',
                            'childs',
                            'infants',
                            'extra_fees',
                            'extra_fees_notes'                     
                            ];

    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'booking_id');
    }  

    public function room()
    {
        return $this->belongsTo('App\Models\Rooms', 'room_id');
    }
}
