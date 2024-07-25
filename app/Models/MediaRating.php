<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MediaRating extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'media_rating';	

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
    protected $fillable = ['ip', 'user_id', 'use_date', 'visit', 'stars', 'content', 'ip'];    
    
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}