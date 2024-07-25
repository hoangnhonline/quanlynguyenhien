<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TaskDetail extends Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'task_detail';

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

    public function staff()
    {
        return $this->belongsTo('App\Models\Account', 'staff_id');
    }
     public function task()
    {
        return $this->belongsTo('App\Models\Task', 'task_id');
    }
    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }
}
