<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SmsTransaction extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sms_transaction_2';

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
        'transaction_no',
        'type',
        'so_tien',
        'tai_khoan_doi_tac',
        'ngan_hang_doi_tac',
        'ten_doi_tac',
        'ngay_giao_dich',
        'noi_dung',
        'status',
        'is_valid',
        'is_process',
        'city_id',
        'cate_id',
        'code_nop_tien',
        'code_ung_pay',
        'code_ung_cost',
        'code_nop_khac'
    ];
}
