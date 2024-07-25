<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Hotels extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'hotels';	

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
        'name',
        'slug',
        'description',
        'stars',
        'hotel_type',
        'city_id',
        'latitude',
        'longitude',
        'amenities',
        'banner_url',
        'payment_opt',
        'is_hot',
        'check_in',
        'check_out',
        'policy',
        'surcharge',
        'status',
        'display_order',
        'related',
        'comm_fixed',
        'comm_percentage',
        'tax_fixed',
        'tax_percentage',
        'email',
        'phone',
        'website',
        'refundable',
        'arrivalpay',
        'tripadvisor_id',
        'thumbnail_image',
        'thumbnail_id',
        'near',
        'diem_noi_bat',
        'meta_title',
        'meta_keywords',
        'meta_desc',
        'created_user',
        'updated_user',
        'created_at',
        'updated_at',
        'com_type',
        'com_value',
        'lowest_price',
        'partner',
        'related_id',
        'title_mail'
    ];

    public function images()
    {
        return $this->hasMany('App\Models\HotelImg', 'hotel_id');
    }
    public function thumbnail()
    {
        return $this->belongsTo('App\Models\HotelImg', 'thumbnail_id');
    }
    public function rooms()
    {
        return $this->hasMany('App\Models\Rooms', 'hotel_id')->orderBy('display_order');
    }
     #chinh sach
    public function policies()
    {
        return $this->hasMany('App\Models\HotelPolicy', 'hotel_id');
    }

    #chi tiet chich sach
    #Tien ich
    public function amenities()
    {
        return $this->hasManyThrough(HotelsTypesSettings::class, HotelAmenity::class, 'hotel_id', 'id',
            'id', 'amenity_id');
    }

    #City
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public static function getHotelMinPrice($hotel_id)
    {
        $rs = RoomsPrice::where('hotel_id', $hotel_id)->where('to_date', '>=', date('Y-m-d'))->orderBy('price', 'asc')->first();
        return !$rs ? 0 : $rs->price;
    }

    public static function getHotelMinPriceGoc($hotel_id)
    {
        $rs = RoomsPrice::where('hotel_id', $hotel_id)->where('to_date', '>=', date('Y-m-d'))->orderBy('price', 'asc')->first();
        return !$rs ? 0 : $rs->price;
    }

    #filter theo ten
    public function filterName($query, $name)
    {
        $query->where('hotels.name', 'like', "%$name%");
        return $query;
    }

    #filter theo khoan gia: ["1000000-2000000", '2000000-3000000].v.v.
    public function filterPrice($query, $price)
    {
        foreach ($price as $index => $value) {
            $itemPrice = explode('-', $value);
            if ($index == 0) {
                $query->whereBetween($this->table . '.lowest_price', $itemPrice);
            } else {
                $query->orWhereBetween($this->table . '.lowest_price', $itemPrice);
            }
        }

        return $query;
    }

    #filter theo loai hinh khach san: hotel, bungalow.v.v.
    public function filterType($query, $type)
    {
        $query->whereIn('hotel_type', $type);
        return $query;
    }

    #filter theo dac diem khach san: trung tam, ho boi.v.v.
    public function filterFeatured($query, $featured)
    {
        $query->join('hotel_featured', 'hotel_featured.hotel_id', '=', 'hotels.id');
        $query->whereIn('hotel_featured.featured_id', $featured);
        return $query;
    }

    #filter theo thanh pho: phu quoc, da nang.v.v.
    public function filterCity($query, $city)
    {
        $query->join('city', $this->table . ".city_id", '=', 'city.id');
        $query->where('city.name', 'like', "%$city%");
        return $query;
    }
    public function type()
    {
        return $this->belongsTo('App\Models\HotelsTypesSettings', 'hotel_type');
    }
}
