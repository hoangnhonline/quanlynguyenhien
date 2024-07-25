<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HotelTypes extends Model  {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hotel_types';   

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
    protected $fillable = [
                    'name',                     
                    'display_order',                                        
                    'status'
                ];
    
}