<?php

namespace App\Models;

use App\Helpers\FineDiff;
use App\Helpers\Helper;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Renderer\RendererConstant;
use function Psy\debug;


class TaskLog extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'task_logs';

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

    protected $casts = [
        'old' => 'array',
        'new' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDescriptionAttribute()
    {
        $old = $this->old;
        $new = $this->new;
        $result = [];
        foreach ($new as $key => $newValue) {
            switch ($key) {
                case 'name':
                    $result[] = 'đổi tên từ "' . @$old[$key] . '" thành "' . $newValue . '"';
                    break;
                case 'from_date':
                    if (empty($newValue)) {
                        $result[] = "ngày bắt đầu đã xoá";
                        break;
                    }
                    if (!isset($old[$key])) {
                        $result[] = "thêm ngày bắt đầu \"" . Carbon::parse($newValue)->format('d/m/Y H:i') . "\"";
                        break;
                    }
                    $result[] = 'đổi ngày bắt đầu từ "' . Carbon::parse($old[$key])->format('d/m/Y H:i') . '" thành "' . Carbon::parse($newValue)->format('d/m/Y H:i') . '"';
                    break;
                case 'to_date':
                    if (empty($newValue)) {
                        $result[] = "ngày kết thúc đã xoá";
                        break;
                    }
                    if (!isset($old[$key])) {
                        $result[] = "thêm ngày kết thúc \"" . Carbon::parse($newValue)->format('d/m/Y H:i') . "\"";
                        break;
                    }
                    $result[] = 'đổi ngày kết thúc từ "' . Carbon::parse($old[$key])->format('d/m/Y H:i') . '" thành "' . Carbon::parse($newValue)->format('d/m/Y H:i') . '"';
                    break;
                case 'completed_date':
                    if (empty($newValue)) {
                        $result[] = "ngày hoàn thành đã xoá";
                        break;
                    }
                    if (!isset($old[$key])) {
                        $result[] = "thêm ngày hoàn thành \"" . Carbon::parse($newValue)->format('d/m/Y H:i') . "\"";
                        break;
                    }
                    $result[] = 'đổi ngày hoàn thành từ "' . Carbon::parse($old[$key])->format('d/m/Y H:i') . '" thành "' . Carbon::parse($newValue)->format('d/m/Y H:i') . '"';
                    break;
                case 'parent_task_id':
                    if (empty($old[$key])) {
                        $parentTask = Task::find($newValue);
                        if (!empty($parentTask)) {
                            $result[] = 'gán công việc cha là "' . $parentTask->name . '"';
                        }
                    }
                    if (!empty($old[$key])) {
                        if (empty($newValue)) {
                            $parentTask = Task::find($old[$key]);
                            if (!empty($parentTask)) {
                                $result[] = 'gỡ công việc cha từ "' . $parentTask->name . '" thành "' . 'không có' . '"';
                            }
                        } else {
                            $oldParentTask = Task::find($old[$key]);
                            $parentTask = Task::find($newValue);
                            if (!empty($parentTask)) {
                                $result[] = 'gỡ công việc cha từ "' . $oldParentTask->name . '" thành "' . $parentTask->name . '"';
                            }
                        }
                    }
                    break;
                case 'department_id':
                    if (empty($old[$key])) {
                        $department = Department::find($newValue);
                        if (!empty($department)) {
                            $result[] = 'đổi bộ phận thành "' . $department->name . '"';
                        }
                    }
                    if (!empty($old[$key])) {
                        if (empty($newValue)) {
                            $department = Department::find($old[$key]);
                            if (!empty($department)) {
                                $result[] = 'xóa bộ phận "' . $department->name . '"';
                            }
                        } else {
                            $oldDepartment = Department::find($old[$key]);
                            $department = Department::find($newValue);
                            if (!empty($department)) {
                                $result[] = 'đổi bộ phận từ "' . $oldDepartment->name . '" thành "' . $department->name . '"';
                            }
                        }
                    }
                    break;
                case 'staff_id':
                    if (empty($old[$key])) {
                        $user = User::find($newValue);
                        if (!empty($user)) {
                            $result[] = 'đổi nhân viên thành "' . $user->name . '"';
                        }
                    }
                    if (!empty($old[$key])) {
                        if (empty($newValue)) {
                            $user = User::find($old[$key]);
                            if (!empty($user)) {
                                $result[] = 'xóa nhân viên "' . $user->name . '"';
                            }
                        } else {
                            $oldUser = User::find($old[$key]);
                            $user = User::find($newValue);
                            if (!empty($user)) {
                                $result[] = 'đổi nhân viên từ "' . $oldUser->name . '" thành "' . $user->name . '"';
                            }
                        }
                    }
                    break;
                case 'type':
                    $oldLabel = $old[$key] == 1 ? 'Việc cố định' : 'Việc phát sinh';
                    $newLabel = $newValue == 1 ? 'Việc cố định' : 'Việc phát sinh';
                    $result[] = 'đổi loại công việc từ "' . $oldLabel . '" thành "' . $newLabel . '"';
                    break;
                case 'status':
                    $oldLabel = $old[$key] == 1 ? Helper::getConstant('task_status')[1] : ($old[$key] == 2 ? Helper::getConstant('task_status')[2] : Helper::getConstant('task_status')[3]);
                    $newLabel = $newValue == 1 ? Helper::getConstant('task_status')[1]  : ($newValue == 2 ? Helper::getConstant('task_status')[2] : Helper::getConstant('task_status')[3]);
                    $result[] = 'đổi trạng thái từ "' . $oldLabel . '" thành "' . $newLabel . '"';
                    break;
                case 'percent':
                    $result[] = 'đổi tiến độ từ "' . @$old[$key] . '%" thành "' . $newValue . '%"';
                    break;
                case 'todo':
                    $result[] = "Việc cần làm: " . $this->todoLogs($newValue, $old[$key] ?? []);
                    break;
                case 'description':
                    if (empty($newValue)) {
                        $result[] = 'đã xoá mô tả';
                        break;
                    }

                    $fineDiff = new FineDiff($old[$key] ?? "", $newValue, FineDiff::$characterGranularity);
                    $content = $fineDiff->renderDiffToHTML();
                    $result[] = 'cập nhật mô tả từ <a href="#" class="text-info" data-toggle="popover" data-trigger="focus" title="Cập nhật mô tả" data-content="' . $content . '">xem thay đổi</a>';
                    break;
            }
        }

        return join(',<br />', $result);
    }

    public function todoLogs($newTodoLogs, $oldTodoLogs)
    {
        $result = [];
        foreach ($newTodoLogs as $id => $newLogs) {
            $taskDetail = TaskDetail::find($id);
            $todoResult = [];
            if (empty($taskDetail)) { continue; }

            if (is_string($newLogs)) {
                $result[] = "việc \"<span class='font-w-500'>" . $taskDetail->content . "</span>\" " . $newLogs;
                continue;
            }

            foreach ($newLogs as $key => $newLog) {
                switch ($key) {
                    case "status":
                        $result[] = "đã đánh dấu việc \"<span class='font-w-500'>" . $taskDetail->content . "</span>\" " . $newLog;
                        break;
                    case "content":
                        $todoResult[] = "đổi tên từ \"" . $oldTodoLogs[$id][$key] .  "\" thành \"" . $newLog . "\"";
                        break;
                    case "notes":
                        if (empty($newLog)) {
                            $todoResult[] = "đã xoá mô tả";
                            break;
                        }
                        if (!isset($oldTodoLogs[$id][$key])) {
                            $todoResult[] = "thêm mô tả \"" . $newLog . "\"";
                            break;
                        }
                        $todoResult[] = "cập nhật mô tả từ \"" . $oldTodoLogs[$id][$key] .  "\" thành \"" . $newLog . "\"";
                        break;
                    case "task_deadline":
                        if (empty($newLog)) {
                            $todoResult[] = "đã xoá thời hạn";
                            break;
                        }
                        if (!isset($oldTodoLogs[$id][$key])) {
                            $todoResult[] = "thêm thời hạn \"" . Carbon::parse($newLog)->format('d/m/Y H:i') . "\"";
                            break;
                        }
                        $todoResult[] = "cập nhật thời hạn từ \"" . Carbon::parse($oldTodoLogs[$id][$key])->format('d/m/Y H:i') .  "\" thành \"" . Carbon::parse($newLog)->format('d/m/Y H:i') . "\"";
                        break;
                    default:
                        break;
                }
            }

            if (!empty($todoResult)) {
                $result[] = "việc \"<span class='font-w-500'>" . $taskDetail->content . "</span>\" " . join(', ', $todoResult);
            }
        }

        return join(',<br />', $result);
    }
}
