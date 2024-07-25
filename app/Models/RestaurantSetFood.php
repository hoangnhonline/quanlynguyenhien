<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RestaurantSetFood extends Model
{


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'restaurant_set_food';

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
    protected $guarded = [
    ];

}
