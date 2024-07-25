<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Restaurants extends Model
{


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'restaurants';

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
        'user_id',
        'name',
        'slug',
        'description',       
        'city_id',
        'latitude',
        'longitude',       
        'banner_url',      
        'is_hot',        
        'status',
        'display_order',        
        'email',
        'phone',
        'website',        
        'thumbnail_image',
        'thumbnail_id',        
        'meta_title',
        'meta_keywords',
        'meta_desc',
        'created_user',
        'updated_user',
        'created_at',
        'updated_at',       
        'is_home',
        'is_show',
        'area_id',
        'co_chi',
        'phan_tram_chi',
        'quy_dinh_chi'
    ];

    #Cac field co the filter
    protected $filterable = ['type', 'city'];

    public function images()
    {
        return $this->hasMany('App\Models\RestaurantImg', 'restaurant_id');
    }
    public function menuCate()
    {
        return $this->hasMany('App\Models\MenuCate', 'restaurant_id');
    }
    public function thumbnail()
    {
        return $this->belongsTo('App\Models\RestaurantImg', 'thumbnail_id');
    }

    #City
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    

    #filter theo ten
    public function filterName($query, $name)
    {
        $query->where('restaurant.name', 'like', "%$name%");
        return $query;
    }

    #filter theo loai hinh khach san: hotel, bungalow.v.v.
    public function filterType($query, $type)
    {
        $query->whereIn('hotel_type', $type);
        return $query;
    }
}
