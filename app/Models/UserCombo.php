<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class UserCombo extends Model
{


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_combo';

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

    public function hotel(){
        return $this->belongsTo(Hotels::class, 'hotel_id');
    }

    public function room(){
        return $this->belongsTo(Rooms::class, 'room_id');
    }

    public function tour(){
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function set(){
        return $this->belongsTo(RestaurantSet::class, 'set_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
