<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MenuCate extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'menu_cate';

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
                            'restaurant_id',
                            'name',                             
                            'status',
                            'display_order'                           
                        ];
    
    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurants', 'restaurant_id');
    } 
    public function foods()
    {
        return $this->hasMany('App\Models\MenuFood', 'menu_cate_id');
    }    
}