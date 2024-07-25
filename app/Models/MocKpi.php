<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MocKpi extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'moc_kpi';	

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
    protected $fillable = ['user_id', 'tour_id', 'amount', 'month_apply', 'year_apply'];
    
    public function user()
    {
        return $this->belongsTo('App\Models\Account', 'user_id');
    }

    public function tour()
    {
        return $this->belongsTo('App\Models\TourSystem', 'tour_id');
    }
}
