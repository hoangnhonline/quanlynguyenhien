<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Deposit extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'deposit';

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
                            'code_nop_tien',
                            'booking_id', 
                            'amount', 
                            'deposit_date',
                            'image_url',
                            'notes',
                            'nguoi_nhan_tien',
                            'nguoi_nop_tien',
                            'notes',
                            'content',
                            'city_id',
                            'sms',
                            'status'                            
                            ];
    
    
}
