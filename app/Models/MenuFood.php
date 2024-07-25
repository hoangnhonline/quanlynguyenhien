<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MenuFood extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'menu_food';

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
                            'menu_cate_id',
                            'restaurant_id',
                            'name',                             
                            'status',
                            'display_order',
                            'price',
                            'created_user',
                            'updated_user',
                            'unit_id'                         
                        ];
    
    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurants', 'restaurant_id');
    }
    public function menuCate()
    {
        return $this->belongsTo('App\Models\MenuCate', 'menu_cate_id');
    }
    public function unit()
    {
        return $this->belongsTo('App\Models\MenuUnit', 'unit_id');
    } 
}