<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Cost extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cost';

	 /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [ 'total_money', 'type', 'status', 'date_use', 'notes', 'image_url', 'content', 'cate_id', 'booking_id', 'amount', 'price', 'nguoi_chi', 'hdv_id', 'partner_id', 'city_id', 'is_fixed', 'cano_nhap', 'hoang_the', 'payment_request_id', 'code_chi_tien', 'time_code_chi_tien', 'time_chi_tien', 'nguoi_nop', 'code_ung_tien', 'time_code_ung_tien', 'time_ung_tien', 'nguoi_ung', 'code_nop_tien', 'time_code_nop_tien', 'time_nop_tien', 'sms_ung', 'sms_chi', 'bank_info_id', 'noi_dung_ck', 'qrcode_clicked', 'tour_id', 'tour_no', 'user_id'];

    public function details()
    {
        return $this->hasMany('App\Models\CostDetail', 'cost_id');
    }
    public function costType()
    {
        return $this->belongsTo('App\Models\CostType', 'cate_id');
    }
    public function partner()
    {
        return $this->belongsTo('App\Models\Partner', 'partner_id');
    }
    public function paymentRequest()
    {
        return $this->belongsTo('App\Models\PaymentRequest', 'payment_request_id');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\BankInfo', 'bank_info_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
