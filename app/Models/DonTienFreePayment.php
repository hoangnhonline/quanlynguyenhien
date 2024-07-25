<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DonTienFreePayment extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'don_tien_free_payment';

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
    protected $fillable = ['don_tien_id', 
                            'amount', 
                            'image_url', 
                            'type',
                            'pay_date',
                            'notes',
                            'status',
                            'flow',
                            'collecter_id'     
                            ];
    
    public function booking()
    {
        return $this->belongsTo('App\Models\DonTienFree', 'don_tien_id');
    }
}
