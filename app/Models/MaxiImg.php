<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaxiImg extends Model
{
    protected $table = 'maxi_img';
    protected $fillable = [
        'id',
        'maxi_id',
        'image_url',
        'display_order',
        'is_thumbnail'
    ];
    public $timestamps = false;

}
