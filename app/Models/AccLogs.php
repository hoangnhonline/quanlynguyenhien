<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AccLogs extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'acc_logs';

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
        'code',
        'so_tien',
        'sms',
        'tbl',
        'type',
        'nguoi_yeu_cau',
        'nguoi_thuc_hien',
        'type',
        'time_thuc_hien',
        'time_yeu_cau',
        'status',
        'content_ck'
    ];

    public function nguoiYeuCau()
    {
        return $this->belongsTo('App\Models\Collecter', 'nguoi_yeu_cau');
    }

    public function nguoiThucHien()
    {
        return $this->belongsTo('App\Models\Collecter', 'nguoi_thuc_hien');
    }

}
