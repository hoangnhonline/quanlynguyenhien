<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CustomerAppointment extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customer_appointments';

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
        'datetime',
        'notes',
        'status',
        'customer_id',
    ];
}
