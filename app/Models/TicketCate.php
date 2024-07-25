<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class TicketCate extends Model
{
    use Filterable;
    protected $table = 'ticket_cate';
    protected $guarded = [];
    public $timestamps = true;

    protected $filterable = ['name'];

    protected $fillable = ['name', 'city_id', 'display_order', 'status', 'description', 'content', 'thumbnail_id', 'meta_id', 'is_hot', 'slug', 'alias', 'children_type', 'price_adult', 'price_child', 'price_infant', 'notes', 'video_id', 'weekend_price'];

    public function filterName($query, $name)
    {
        return $query->where('name', 'like', "%$name%");
    }
    public function thumbnail()
    {
        return $this->belongsTo('App\Models\TicketCateImg', 'thumbnail_id');
    }

    public function images()
    {
        return $this->hasMany('App\Models\TicketCateImg', 'ticket_cate_id');
    }

    #chinh sach
    public function policies()
    {
        return $this->hasMany('App\Models\TicketCatePolicy', 'ticket_cate_id');
    }

    public function ticketType()
    {
        return $this->hasMany('App\Models\TicketType', 'ticket_cate_id')->orderBy('display_order');
    }


    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public static function getTicketByCityId($cityId)
    {
        return self::where(['city_id' => $cityId, 'status' => 1])->orderBy('display_order', 'asc')->get();
    }
}
