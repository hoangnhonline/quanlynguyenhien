<?php

namespace App\Http\View\Composers;

use App\Models\Department;
use App\Models\Plan;
use App\Models\Task;
use App\User;
use Illuminate\View\View;

class TaskComposer
{
    public function compose(View $view)
    {

        $parentTaskList = Task::with('subTasks')->whereHas('subTasks', function ($query) {
            return $query->whereNotNull('parent_task_id')->where('status', '>', 0);
        })->get();

        $planList = Plan::where('status', 1)->orderBy('name', 'ASC')->get();
        $departmentList = Department::where('status', 1)->orderBy('display_order', 'ASC')->get();
        $userList = User::where('status', 1)->where('is_staff', 1)->get();

        $view->with(compact('parentTaskList', 'planList', 'departmentList', 'userList'));
    }
}
