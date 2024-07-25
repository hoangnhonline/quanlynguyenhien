<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CodeNopTien extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'code_nop_tien';	

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
    protected $fillable = ['code', 'booking_id', 'status', 'amount'];
    
}
