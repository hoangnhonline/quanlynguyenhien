<?php

namespace App\Http\Controllers;

use App\Helpers\Helper as HelpersHelper;
use App\Models\Plan;
use App\Models\TaskLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Task;
use App\Models\Department;
use Carbon\Exceptions\InvalidDateException;
use Helper, File, Session, Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Swift_Mailer;
use Swift_SmtpTransport;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $filters = $request->all();

        $currentDate = Carbon::now();
        $filters['range_date'] = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y');

        $parent_task_id = $filters["parent_task_id"] ?? null;

        $query = Task::where('status', '>', 0)
            ->when($filters['s'] ?? false, function ($query, $s) {
                return $query->where('name', 'like', "%$s%");
            })
            ->when($filters['status'] ?? false, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filters['plan_id'] ?? false, function ($query, $plan_id) {
                return $query->where('plan_id', $plan_id);
            })
            ->when($filters['department_id'] ?? false, function ($query, $department_id) {
                return $query->where('department_id', $department_id);
            })
            ->when($filters['staff_id'] ?? false, function ($query, $staff_id) {
                return $query->where('staff_id', $staff_id);
            })
            ->when($filters['range_date'] ?? false, function ($query) use (&$filters) {
                return $this->_queryRangeDate($query, $filters);
            });
        $parentTask = null;
        if ($parent_task_id) {
            $parentTask = Task::findOrFail($parent_task_id);
            $query->where('parent_task_id', $parent_task_id);
        }

        if (!Helper::isSuperAdmin() && empty($filters['staff_id'] ?? false)) {
            $query->where(function ($q) {
                $q->where('created_user', Auth::user()->id)
                    ->orWhere('staff_id', Auth::user()->id);
            });
        }

        $items = $query->orderBy('id', 'desc')->get()->groupBy('status')->sortKeys();

        $statistics = $this->_taskStatistics($query->get());
        $listComponent = view('task.components.list', compact('items', 'statistics'))->render();

        if ($request->ajax()) {
            return response($listComponent);
        }
        return view('task.index', compact('listComponent', 'filters', 'parent_task_id', 'parentTask'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $plan_id = isset($request->plan_id) ? $request->plan_id : null;
        $parent_task_id = isset($request->parent_task_id) ? $request->parent_task_id : null;
        $taskList = Task::where('status', '>', 0)->whereNull('parent_task_id')->orderBy('id', 'DESC')->get();

        $component = view('task.create', compact('plan_id', 'parent_task_id', 'taskList'))->render();
        $title = "Tạo công việc mới";

        return view('task.components.work-form-modal', compact('component', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->checkValidate($request);
        $dataArr = $request->all();

        if (Auth::user()->role != 1) {
            $dataArr['department_id'] = Auth::user()->department_id;
        }

        $dataArr['from_date'] = empty($dataArr['from_date']) ? null : Carbon::createFromFormat('d/m/Y H:i', $dataArr['from_date'])->format('Y-m-d H:i:s');
        $dataArr['to_date'] = empty($dataArr['to_date']) ? null : Carbon::createFromFormat('d/m/Y H:i', $dataArr['to_date'])->format('Y-m-d H:i:s');
        $dataArr['completed_date'] = empty($dataArr['completed_date']) ? null : Carbon::createFromFormat('d/m/Y H:i', $dataArr['completed_date'])->format('Y-m-d H:i:s');
       
        $task = Task::create($dataArr);


        if ($task->id > 0) {
            $user = User::where('id', $dataArr['staff_id'])->first();

            // Backup your default mailer
            $backup = Mail::getSwiftMailer();

            // Setup your gmail mailer
            $transport = new Swift_SmtpTransport(config('mail.host'), config('mail.port'), config('mail.encryption'));
            $transport->setUsername(config('mail.task_username'));
            $transport->setPassword(config('mail.task_password'));
            // Any other mailer configuration stuff needed...
            $gmail = new Swift_Mailer($transport);

            // Set the mailer as gmail
            Mail::setSwiftMailer($gmail);

            // Send your message
            Mail::send('mail.task', [
                'name' => $user->name,
                'created_user' => Auth::user()->name,
                'task_id' => $task->id
            ], function ($message) use ($user) {
                $message->from(config('mail.task_username'), config('mail.name'));
                $message->to($user->email, $user->name)->subject('Plan To Travel - công việc mới');
            });

            // Restore your original mailer
            Mail::setSwiftMailer($backup);
        }

        return response()->json(['message' => 'Tạo mới công việc thành công']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $task = Task::with(['subTasks' => function ($q) {
            return $q->where('status', '>', 0);
        }, 'parentTask', 'staff', 'department', 'plan', 'createdUser', 'todos'])->find($id);
        $title = "Chi tiết công việc";

        if (!Helper::isSuperAdmin() && !in_array(Auth::user()->id, [$task->created_user, $task->staff_id])) {
            abort(403);
        }

        $component = view('task.show', compact('task'))->render();
        return view('task.components.work-infor-modal', compact('component', 'task', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $detail = Task::find($id);

        if (!Helper::isSuperAdmin() && !in_array(Auth::user()->id, [$detail->created_user, $detail->staff_id])) {
            abort(403);
        }

        $taskList = Task::where('status', '>', 0)->whereNull('parent_task_id')->where('id', '!=', $detail->id)->orderBy('id', 'DESC')->get();

        $component = view('task.edit', compact('detail', 'taskList'))->render();
        $title = "Cập nhật công việc";

        return view('task.components.work-form-modal', compact('component', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request)
    {

        $this->checkValidate($request);

        $id = $request->id;
        $dataArr = $request->all();

        $model = Task::find($id);
        $oldData = $model->toArray();

        $dataArr['from_date'] = empty($dataArr['from_date']) ? null : Carbon::createFromFormat('d/m/Y H:i', $dataArr['from_date'])->format('Y-m-d H:i:s');
        $dataArr['to_date'] = empty($dataArr['to_date']) ? null : Carbon::createFromFormat('d/m/Y H:i', $dataArr['to_date'])->format('Y-m-d H:i:s');
        $dataArr['completed_date'] = empty($dataArr['completed_date']) ? null : Carbon::createFromFormat('d/m/Y H:i', $dataArr['completed_date'])->format('Y-m-d H:i:s');
        $model->update($dataArr);

        unset($dataArr['_token']);
        $contentDiff = array_diff_assoc($dataArr, $oldData);
        if (!empty($contentDiff)) {
            $oldContent = [];

            foreach ($contentDiff as $k => $v) {
                $oldContent[$k] = $oldData[$k];
            }
            TaskLog::create([
                'task_id' =>  $id,
                'old' => $oldContent,
                'new' => $contentDiff,
                'user_id' => Auth::user()->id
            ]);
        }

        return response()->json(['message' => 'Cập nhật công việc thành công']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // delete
        $model = Task::find($id);

        $parentTaskId = $model->parent_task_id;
        $model->delete();

        //Update parent task count
        if (!empty($parentTaskId)) {
            $parentTask = Task::findOrFail($parentTaskId);
            $parentTask->subtask_count = Task::where('parent_task_id',  $parentTaskId)->count();
            $parentTask->save();
        }

        // redirect
        Session::flash('message', 'Hủy công việc thành công');
        return redirect()->route('task.index');
    }

    public function delete($id)
    {
        // delete
        $model = Task::find($id);
        if (Auth::user()->is_staff == 1 && Auth::id() != $model->created_user) {
            return response()->json(['message' => 'Bạn không thể xóa công việc do người khác tạo'], 403);
        }
        $model->update(['status' => 0]);

        return response()->json(['message' => 'Xoá công việc thành công']);
    }
    public function ajaxList(Request $request)
    {
        $filters = $request->all(0);
        $filters['range_date'] = Carbon::parse($request->startStr)->format('d/m/Y') . ' - ' . Carbon::parse($request->endStr)->format('d/m/Y');
        $query = Task::with('staff')->where('status', '>', 0)
            ->when($filters['range_date'] ?? false, function ($query) use (&$filters) {
                return $this->_queryRangeDate($query, $filters);
            })
            ->when($filters['status'] ?? false, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filters['plan_id'] ?? false, function ($query, $plan_id) {
                return $query->where('plan_id', $plan_id);
            })
            ->when($filters['department_id'] ?? false, function ($query, $department_id) {
                return $query->where('department_id', $department_id);
            })
            ->when($filters['staff_id'] ?? false, function ($query, $staff_id) {
                return $query->where('staff_id', $staff_id);
            });

        if (!Helper::isSuperAdmin()) {
            $query->where(function ($q) {
                $q->where('created_user', Auth::user()->id)
                    ->orWhere('staff_id', Auth::user()->id);
            });
        }

        $items = $query->orderBy('status', 'asc')->get();
        return response()->json($items);
    }

    public function ajaxSave(Request $request)
    {
        $dataArr = $request->all();
        $this->validate(
            $request,
            [
                'name' => 'required',
                'type' => 'required',
            ],

            [
                'name.required' => 'Bạn chưa nhập công việc',
                'type.required' => 'Bạn chưa chọn loại công việc',
            ]
        );
        $user = Auth::user();
        $dataArr['department_id'] = $user->department_id;
        $dataArr['status'] = 1;
        $dataArr['created_user'] = $dataArr['updated_user'] = $user->id;
        $rs = Task::create($dataArr);
        return $rs->id;
    }

    public function checkValidate(Request $request)
    {
        return $this->validate(
            $request,
            array_merge([
                'name' => 'required',
                // 'department_id' => 'required',
                'type' => 'required',
                'staff_id' => 'required',
                'from_date' => 'required|date_format:d/m/Y H:i',
                'to_date' => 'nullable|date_format:d/m/Y H:i',
                'completed_date' => 'nullable|date_format:d/m/Y H:i',
            ], Auth::user()->role == 1 ? ['department_id' => 'required'] : []),
            array_merge([
                'name.required' => 'Bạn chưa nhập tên công việc',
                'type.required' => 'Bạn chưa chọn loại công việc.',
                'staff_id.required' => 'Bạn chưa chọn nhân viên.',
                'from_date.required' => 'Bạn chưa nhập nhập ngày bắt đầu',
                'from_date.date_format' => 'Vui lòng nhập đúng định dạng dd/mm/yyyy H:i',
                'to_date.date_format' => 'Vui lòng nhập đúng định dạng dd/mm/yyyy H:i',
                'completed_date.date_format' => 'Vui lòng nhập đúng định dạng dd/mm/yyyy H:i',
            ], Auth::user()->role == 1 ? ['department_id.required' => 'Bạn chưa chọn bộ phận'] : [])
        );
    }

    public function ajaxUpdateTaskStatus(Request $request)
    {
        $this->validate(
            $request,
            [
                'status' => 'required',
            ],
            [
                'status.required' => 'Chưa có trạng thái',
            ]
        );
        $data = $request->all();
        $id = $request->id;

        $task = Task::find($id);

        $oldTaskLog = ['status' => $task->status];
        $newTaskLog = ['status' => $data['status']];

        if ($data['status'] == 3) {
            $data['completed_date'] = Carbon::now();
            $oldTaskLog['completed_date'] = !empty($task['completed_date']) ? Carbon::parse($task['completed_date'])->format('Y-m-d H:i:s') : null;
            $newTaskLog['completed_date'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        if ($data['status'] != 3 && !empty($task['completed_date'])) {
            $data['completed_date'] = null;
            $oldTaskLog['completed_date'] = Carbon::parse($task['completed_date'])->format('Y-m-d H:i:s');
            $newTaskLog['completed_date'] = null;
        }

        TaskLog::create([
            'task_id' =>  $id,
            'old' => $oldTaskLog,
            'new' => $newTaskLog,
            'user_id' => Auth::user()->id
        ]);


        $task->update($data);

        if ($data['status'] == 3) {
            $user = User::where('id', $task->created_user)->first();

            // Backup your default mailer
            $backup = Mail::getSwiftMailer();

            // Setup your gmail mailer
            $transport = new Swift_SmtpTransport(config('mail.host'), config('mail.port'), config('mail.encryption'));
            $transport->setUsername(config('mail.task_username'));
            $transport->setPassword(config('mail.task_password'));
            // Any other mailer configuration stuff needed...
            $gmail = new Swift_Mailer($transport);

            // Set the mailer as gmail
            Mail::setSwiftMailer($gmail);

            // Send your message
            Mail::send('mail.task-success', [
                'name' => $user->name,
                'task_id' => $task->id,
                'task_name' => $task->name,
                'user' => $user
            ], function ($message) use ($user) {
                $message->from(config('mail.task_username'), config('mail.name'));
                $message->to($user->email, $user->name)->subject('Plan To Travel - công việc hoàn thành');
            });

            // Restore your original mailer
            Mail::setSwiftMailer($backup);
        }
        return response()->json(['message' => 'Cập nhật trạng thái thành công']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function report(Request $request)
    {
        $filters = $request->all();
        $currentDate = Carbon::now();
        $filters['range_date'] = $request->range_date ? $request->range_date : $currentDate->startOfMonth()->format('d/m/Y') . " - " . $currentDate->endOfMonth()->format('d/m/Y');
        $users = Account::withCount([
            'tasks as task_todo_count' => function ($q) use (&$filters) {
                $this->_queryRangeDate($q, $filters)
                    ->where('status', 1);
            },
            'tasks as task_in_progress_count' => function ($q) use (&$filters) {
                $this->_queryRangeDate($q, $filters)
                    ->where('status', 2);
            },
            'tasks as task_done_count' => function ($q) use (&$filters) {
                $this->_queryRangeDate($q, $filters)
                    ->where('status', 3);
            },
            'tasks as task_over_deadline' => function ($q) use (&$filters) {
                $this->_queryRangeDate($q, $filters)
                    ->where('status', '!=', 3)
                    ->whereNotNull('to_date')
                    ->whereDate('to_date', '<=', Carbon::now());
            }
        ])
            ->whereHas('tasks', function ($query) use (&$filters) {
                return $this->_queryRangeDate($query, $filters);
            })
            ->where('is_staff', 1)
            ->when($filters['staff_id'] ?? false, function ($query, $staff_id) {
                return  $query->where('id', $staff_id);
            })
            ->when($filters['department_id'] ?? false, function ($query, $department_id) {
                return  $query->where('department_id', $department_id);
            })
            ->orderBy('id', 'desc')->paginate(20)->appends($filters);

        return view('task.report', compact('users', 'filters'));
    }


    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function calendar(Request $request)
    {
        $filters = $request->all();
        return view('task.calendar', compact('filters'));
    }

    private function _queryRangeDate($qr, &$filters)
    {
        $_minDateKey = 0;
        $_maxDateKey = 1;

        return $qr->when($filters['range_date'] ?? false, function ($query, $range_date) use ($_minDateKey, $_maxDateKey, &$filters) {
            $rangeDate = array_unique(explode(' - ', $range_date));
            if (empty($rangeDate[$_minDateKey])) {
                //case page is initialized and range_date is empty => this month
                $rangeDate = Carbon::now();
                return $query->where(function ($q) use ($rangeDate) {
                    $q->whereDate('from_date', '=', $rangeDate)
                        ->orWhereDate('to_date', '=', $rangeDate);
                });
            } elseif (count($rangeDate) === 1) {
                //case page is initialized and range_date has value,
                //when counting the number of elements in rangeDate = 1 => only select a day
                $rangeDate = Carbon::createFromFormat('d/m/Y', $rangeDate[$_minDateKey]);
                return $query->where(function ($q) use ($rangeDate) {
                    $q->whereDate('from_date', '=', $rangeDate)
                        ->orWhereDate('to_date', '=', $rangeDate);
                });
                $filters['range_date'] = $rangeDate[$_minDateKey] . " - " . $rangeDate[$_minDateKey];
            } else {
                $minDate = Carbon::createFromFormat('d/m/Y', $rangeDate[$_minDateKey])->startOfDay()->format('Y-m-d H:i:s');
                $maxDate = Carbon::createFromFormat('d/m/Y', $rangeDate[$_maxDateKey])->endOfDay()->format('Y-m-d H:i:s');
                return $query->where(function ($q) use ($minDate, $maxDate) {
                    $q->whereBetween('from_date', [$minDate, $maxDate])
                        ->orWhereBetween('to_date', [$minDate, $maxDate]);
                });
            }
        });
    }

    private function _taskStatistics($tasks)
    {
        $statistics = [
            'todo' => 0,
            'doing' => 0,
            'over_deadline' => 0,
            'done' => 0
        ];

        if ($tasks->isEmpty() || count($tasks) === 0) return $statistics;

        foreach ($tasks as $key => $task) {
            if ($task['status'] === 1) { $statistics['todo'] += 1; }
            if ($task['status'] === 2) { $statistics['doing'] += 1; }
            if ($task['status'] === 3) { $statistics['done'] += 1; }

            if ($task['status'] !== 3 && !is_null($task['to_date']) && Helper::checkOverDeadline($task['status'], $task['to_date'])) {
                $statistics['over_deadline'] += 1;
            }
        }

        return $statistics;
    }
}
