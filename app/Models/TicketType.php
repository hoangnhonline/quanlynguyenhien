<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TicketType;


class TicketType extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ticket_type';

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
        'ticket_cate_id',
        'name',
        'description',
        'is_hot',
        'amenities',
        'display_order',
        'status',
        'image_url',
        'created_user',
        'updated_user',
    ];

    public function ticketCate()
    {
        return $this->belongsTo(TicketCate::class, 'ticket_cate_id');
    }
}
