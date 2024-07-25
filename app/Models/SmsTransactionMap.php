<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SmsTransactionMap extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sms_transaction_map';	

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
    protected $fillable = ['sms_transaction_id', 'type', 'code', 'amount'];
    
}
