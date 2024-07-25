<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CouponCode extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'coupon_code';

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
        'zalo_id', 
        'ctv_id', 
        'restaurant_id', 
        'customer_phone', 
        'bill_url', 
        'total_money', 
        'status', 
        'user_id', 
        'time_used', 
        'tu_lai',
        'hh_tx',
        'hh_khach',
        'hh_cty',
        'hh_sales',
        'is_pay_cty',
        'pay_cty_time',
        'is_pay_sales',
        'pay_sales_time',
        'partner_id'
    ];    
    
    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurants', 'restaurant_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}