<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class BookingNotes extends Model  {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'booking_notes';

     /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $appends = ['creator'];

    protected $hidden = ['user'];

    protected $casts = ['created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'booking_id', 'status', 'user_id' ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCreatorAttribute(){
        return $this->user->name;
    }
}
