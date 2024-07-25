<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaxiHistory extends Model
{
    protected $table = 'maxi_history';
    protected $fillable = [
        'id',
        'booking_id',
        'date',
        'created_at',
        'updated_at',
        'maxi_id'
    ];
    public $timestamps = true;

    public function maxi()
    {
        return $this->belongsTo('App\Models\Maxi', 'maxi_id');
    }
}
