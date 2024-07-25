<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\TaskDetail;
use App\Models\TaskLog;
use Carbon\Carbon;
use Helper, File, Session, Auth;

class TaskDetailController extends Controller
{
    /**
    * Show the form for creating a new resource.
    *
    * @return Response
    */
    public function create(Request $request)
    {
        $taskId = $request->taskId;
        $taskDetails = TaskDetail::where([['task_id', $taskId], ['status', '>', 0]])->orderBy('id', 'asc')->get();

        $title = 'Việc cần làm';
        $component = view('task-detail.create', compact('taskDetails', 'taskId'))->render();

        return view('task-detail.components.todo-form-modal', compact('title', 'component', 'taskId'));
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  Request  $request
    * @return Response
    */
    public function store(Request $request)
    {
        $dataArr = $request->all();
        $this->validate($request,[
            'data.*.content' => 'required',
            'data.*.task_deadline' => 'nullable|date_format:d/m/Y H:i',
        ],
        [
            'data.*.content.required' => 'Bạn chưa tên việc cần làm',
            'data.*.task_deadline.date_format' => 'Vui lòng nhập đúng định dạng dd/mm/yyyy H:i',
        ]);

        $data = $dataArr['data'] ?? [];
        $taskId = $dataArr['task_id'];
        $taskDetailsId = TaskDetail::where([['task_id', $taskId], ['status', '>', 0]])->pluck('id')->toArray();
        $dataFormsId = collect($data)->pluck('id')->filter(function($v) { return $v !== null; })->toArray();

        $old = [];
        $new = [];

        foreach ($data as $todo) {
            $todo['task_deadline'] = empty($todo['task_deadline']) ? null : Carbon::createFromFormat('d/m/Y H:i', $todo['task_deadline'])->format('Y-m-d H:i:s');
            //create
            if (!isset($todo['id'])) {
                $todo['task_id'] = $taskId;
                $taskDetail = TaskDetail::create($todo);
                $new[$taskDetail->id] = $taskDetail->name . "đã được tạo";
                continue;
            }

            //update
            if (in_array($todo['id'], $taskDetailsId)) {
                $taskDetail = TaskDetail::find($todo['id']);
                $oldTodo = $taskDetail->toArray();
                $taskDetail->update($todo);

                //fix task_deadline different between request input and record in table
                $oldTodo['task_deadline'] = empty($todo['task_deadline']) ? null : Carbon::parse($oldTodo['task_deadline'])->format('Y-m-d H:i:s');
                $contentDiff = array_diff_assoc($todo, $oldTodo);
                if (!empty($contentDiff)) {
                    $oldContent = [];

                    foreach ($contentDiff as $k => $v) {
                        $oldContent[$k] = $oldTodo[$k];
                    }
                    $old[$todo['id']] = $oldContent;
                    $new[$todo['id']] = $contentDiff;
                }
            }
        }
        //delete
        foreach ($taskDetailsId as $id) {
            if (!in_array($id, $dataFormsId)) {
                $taskDetail = TaskDetail::find($id);
                $taskDetail->update(['status' => 0]);
                $new[$taskDetail->id] = $taskDetail->name . "đã xoá";
            }
        }

        if (!empty($old) || !empty($new)) {
            TaskLog::create([
                'task_id' =>  $taskId,
                'old' => ["todo" => $old],
                'new' => ["todo" => $new],
                'user_id' => Auth::user()->id
            ]);
        }

        return response()->json(['message' => 'Đã cập nhật việc cần làm']);
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
        $data = $request->all();
        $taskDetail = TaskDetail::find($data['todo_id']);

        $status = [
            1 => "chưa hoàn thành",
            2 => "hoàn thành"
        ];

        TaskLog::create([
            'task_id' =>  $taskDetail->task_id,
            'old' => ["todo" => [
                $taskDetail->id => ['status' => $status[$taskDetail->status]]
            ]],
            'new' => ["todo" => [
                $taskDetail->id => ['status' => $status[$data['status']]]
            ]],
            'user_id' => Auth::user()->id
        ]);

        $taskDetail->update(['status' => $data['status']]);

        $todos = TaskDetail::where([['task_id', $data['task_id']], ['status', '>', 0]])->get();
        return view('task.components.todo-area', compact('todos'))->render();
    }
}
