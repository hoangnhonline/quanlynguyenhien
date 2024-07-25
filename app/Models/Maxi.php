<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maxi extends Model
{
    protected $table = 'maxi';
    protected $fillable = [
        'id',
        'name',
        'avatar',
        'status',
        'note',
        'display_order',
        'is_deleted'
    ];
    public $timestamps = true;

    public function thumbnail()
    {
        return $this->belongsTo('App\Models\MaxiImg', 'thumbnail_id');
    }

    public function images()
    {
        return $this->hasMany('App\Models\MaxiImg', 'maxi_id');
    }

}
