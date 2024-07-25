<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Rooms;


class BookingTicket extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'booking_tickets';

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
        'booking_id',
        'ticket_type_id',
        'use_date',
        'adults',
        'childs',
        'infants',
        'price_adult',
        'price_child',
        'price_infant',
        'total_price_adult',
        'total_price_child',
        'total_price_infant',
        'total_amount',
        'status',
        'created_at',
        'updated_at'
    ];

    public function ticketType(){
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }
}
