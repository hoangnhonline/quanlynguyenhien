<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Customer extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers';

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
        'email',
        'phone',
        'phone_2',
        'address',
        'birthday',
        'use_date',
        'code',
        'status',
        'created_at',
        'updated_at',
        'zalo',
        'facebook',
        'is_send',
        'is_accept',
        'city_id',
        'user_id_refer',
        'created_user',
        'created_user',
        'schedule_1',
        'schedule_2',
        'demand',
        'time_send',
        'time_accept',
        'contact_date',
        'notes',
        'note_schedule_1',
        'note_schedule_2',
        'is_noti_1',
        'is_noti_2',
        'adults',
        'childs',
        'infants',
        'source',
        'source2',
        'ads',
        'ads_campaign_id',
        'product_type',
        'product_id',
        'ask_more'
    ];

    public function userRefer()
    {
        return $this->belongsTo('App\Models\Account', 'user_id_refer');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Account', 'created_user');
    }

    public function appointments()
    {
        return $this->hasMany(CustomerAppointment::class, 'customer_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function sourceRef()
    {
        return $this->belongsTo(CustomerSource::class, 'source');
    }

    public function source2Ref()
    {
        return $this->belongsTo(CustomerSource::class, 'source2');
    }

    public function adsCampaign()
    {
        return $this->belongsTo(AdsCampaign::class, 'ads_campaign_id');
    }

    public function product(){
        switch ($this->product_type){
            case 1:
                return $this->belongsTo(TourSystem::class, 'product_id');
            case 2:
                return $this->belongsTo(Combo::class, 'product_id');
            case 3:
                return $this->belongsTo(Hotels::class, 'product_id');
            case 4:
                return $this->belongsTo(TicketCate::class, 'product_id');
            case 5:
                return $this->belongsTo(CarCate::class, 'product_id');
            default:
                return null;
        }
    }
}
