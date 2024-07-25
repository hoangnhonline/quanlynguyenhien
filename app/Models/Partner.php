<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Partner extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'partners';

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
    protected $fillable = [ 'name',
                            'tour_id', 
                            'display_order',                           
                            'phone',  
                            'status',
                            'cost_type_id', 
                            'description',
                            'have_partner',
                            'hoang_the',
                            'email',
                            'contact_name',
                            'contact_phone',
                            'city_id',
                            'cano'
                            ];
    public static function getList($params = []){
        $query = self::where('status', 1);
        if(isset($params['city_id']) && $params['city_id']){
            $city_id = $params['city_id'];
            $query->join('partner_city', function($join ) use ($city_id) {
              $join->on('partners.id', 'partner_city.partner_id')
              ->where('partner_city.city_id', $city_id);
            });  
        } 
        if(isset($params['cost_type_id']) && $params['cost_type_id']){
            $query->where('cost_type_id', $params['cost_type_id']);
        } 
        if(isset($params['cano']) && $params['cano']){
            $query->where('cano', $params['cano']);
        }       
        $query->orderBy('display_order', 'desc');
        return $query->get();
    }
    public static function getListTag($id){
        $query = TagObjects::where(['object_id' => $id, 'tag_objects.type' => 2])
            ->join('tag', 'w-tag.id', '=', 'tag_objects.tag_id')            
            ->get();
        return $query;
    }
    public function createdUser()
    {
        return $this->belongsTo('App\Models\WAccount', 'created_user');
    }
     public function updatedUser()
    {
        return $this->belongsTo('App\Models\WAccount', 'updated_user');
    }
    public function cate()
    {
        return $this->belongsTo('App\Models\WArticlesCate', 'cate_id');
    }
    public function parentCate()
    {
        return $this->belongsTo('App\Models\WCateParent', 'parent_id');
    }
    public function tourDnPrice()
    {
        return $this->hasOne('App\Models\TourDnPrice', 'partner_id');
    }
    public function citys()
    {
        return $this->hasMany('App\Models\PartnerCity', 'partner_id');
    }
}
