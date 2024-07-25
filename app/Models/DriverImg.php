<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DriverImg extends Model  {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'driver_img';   

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
                    'driver_id', 
                    'image_url', 
                    'display_order',                    
                    'type',
                    'status',
                    'cate'
                ];
    
}