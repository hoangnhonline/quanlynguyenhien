<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Ads extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ads';

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
        'code',
        'name',
        'type',
        'status',
        'date_start',
        'date_end',
        'budget',
        'city_id'
    ];  

}
