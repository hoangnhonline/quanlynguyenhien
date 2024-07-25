<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Plan extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'plans';

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
    protected $guarded = [];

    public function tasks()
    {
        return $this->hasMany('App\Models\Task', 'plan_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }

    public function updatedUser()
    {
        return $this->belongsTo('App\Models\Account', 'updated_user');
    }


}
