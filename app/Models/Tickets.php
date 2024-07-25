<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Tickets extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tickets';

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
                            'ticket_type_id',                           
                            'amount',
                            'price', 
                            'price_sell',
                            'commission',
                            'total',
                            'status',
                            'company_id'               
                            ];

    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'booking_id');
    } 
     public function ticketType()
    {
        return $this->belongsTo('App\Models\TicketTypeSystem', 'ticket_type_id');
    }   
}
