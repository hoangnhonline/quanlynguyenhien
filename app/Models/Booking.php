<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Booking extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'booking';

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
    protected $fillable = ['hoa_hong_cty', 'hoa_hong_sales', 'location_id', 'name', 'content', 'cap_nl', 'cap_te', 'phone', 'phone_1', 'phone_sales', 'facebook', 'email', 'book_date', 'use_date', 'address', 'infants', 'last_address', 'adults', 'childs', 'total_price', 'total_cost', 'extra_fee', 'discount', 'type', 'cty_send', 'is_send', 'price_adult', 'price_infant', 'meals', 'meals_te', 'price_child', 'tien_coc', 'ngay_coc', 'status', 'danh_sach', 'notes', 'user_id', 'level', 'tour_id', 'tour_type', 'tour_cate', 'hdv_id', 'cano_id', 'con_lai', 'nguoi_thu_coc', 'nguoi_thu_tien', 'mu_di_bo', 'created_user', 'updated_user'
                            ];
    
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }
    
    public function hdv()
    {
        return $this->belongsTo('App\User', 'hdv_id');
    }
    public function cano()
    {
        return $this->belongsTo('App\Models\Partner', 'cano_id');
    }
     public function updatedUser()
    {
        return $this->belongsTo('App\Models\WAccount', 'updated_user');
    }
   
    public function payment()
    {
        return $this->hasMany('App\Models\BookingPayment', 'booking_id');
    }
    public function bill()
    {
        return $this->hasMany('App\Models\BookingBill', 'booking_id');
    }
    
    public function tour()
    {
        return $this->belongsTo('App\Models\Tour', 'tour_id');
    }
    
   
    public static function getBookingForRelated(){
        $minRange = date("Y-m-d", strtotime(" -1 months"));
        $query = self::where('status', '>', 0)->where(function ($query) use ($minRange) {
            $query->where('use_date', '>=', $minRange)
                  ->orWhere('checkin', '>=', $minRange);
        });
        if(Auth::user()->role > 1){
            $query->where('user_id', Auth::user()->id);
        }
        $arrBooking = $query->get();
        return $arrBooking;
    }

    public function comments(){
        return $this->hasMany(BookingLogs::class, 'booking_id', 'id');
    }

    public function commissions(){
        return $this->hasMany(BookingCommission::class, 'booking_id');
    }
    public function maxis()
    {
        return $this->hasMany('App\Models\MaxiHistory', 'booking_id')->groupBy('maxi_id');
    }
}
