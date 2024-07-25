<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Model;


class Task extends Model
{
    use RecordSignature;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'task';

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
    protected $guarded = [
        // 'name',
        // 'from_date',
        // 'to_date',
        // 'completed_date',
        // 'staff_id',
        // 'percent',
        // 'type',
        // 'description',
        // 'department_id',
        // 'plan_id',
        // 'parent_task_id',
        // 'status',
    ];

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'completed_date' => 'datetime'
    ];

    public function taskDetail()
    {
        return $this->hasMany('App\Models\TaskDetail', 'task_id');
    }
    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }

    public function plan()
    {
        return $this->belongsTo('App\Models\Plan', 'plan_id');
    }

    public function updatedUser()
    {
        return $this->belongsTo('App\Models\Account', 'updated_user');
    }
    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id')->orderByDesc('created_at');
    }
    public function logs()
    {
        return $this->hasMany(TaskLog::class, 'task_id')->orderByDesc('created_at');
    }
    public function subTasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }
    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id')->with('parentTask');
    }
    public function staff()
    {
        return $this->belongsTo(Account::class, 'staff_id');
    }
    public function createdUser()
    {
        return $this->belongsTo(Account::class, 'created_user');
    }

    public function todos()
    {
        return $this->hasMany(TaskDetail::class, 'task_id')->where('status', '>', 0);
    }

    public function getTaskStatusAttribute() {
        return Helper::getConstant('task_status')[$this->status] ?? "";
    }

    public function getParentsAttribute() {
        if (!$this->parentTask) {
            return [];
        }
        return $this->loopParentTasks([], $this->parentTask);
    }

    private function loopParentTasks(array $parents, $parentTask)
    {
        $parent = $parentTask;
        array_unshift($parents, $parent);

        if ($parent->parentTask) {
            return $this->loopParentTasks($parents, $parent->parentTask);
        }
        return $parents;
    }
}
