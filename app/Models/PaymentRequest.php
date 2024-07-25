<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PaymentRequest extends Model  {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_request';

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
    protected $fillable = ['user_id', 'total_money', 'thuc_chi', 'image_url', 'nguoi_chi', 'status', 'content', 'notes', 'bank_info_id', 'unc_url', 'unc_type', 'city_id', 'date_pay', 'booking_id', 'urgent', 'code_chi_tien', 'time_code_chi_tien', 'time_chi_tien', 'nguoi_nop', 'code_ung_tien', 'time_code_ung_tien', 'time_ung_tien', 'nguoi_ung', 'code_nop_tien', 'time_code_nop_tien', 'time_nop_tien', 'sms_ung', 'sms_chi', 'acc_checked', 'qrcode_clicked'];

    public function bank()
    {
        return $this->belongsTo('App\Models\BankInfo', 'bank_info_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\Account', 'user_id');
    }
}
