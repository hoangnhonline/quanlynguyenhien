<?php

namespace App\Http\Controllers;

use App\Models\TaskLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class TaskLogController extends Controller
{


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function index($id)
    {
        $logs = TaskLog::with(['user'])->where('task_id', $id)->orderBy('created_at', 'desc')->get();
        return view('task.components.activity-logs', compact('logs'));
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
        $this->validate(
            $request,
            [
                'comment' => 'required',
                'task_id' => 'required'
            ],
            [
                'comment.required' => 'Bạn chưa nhập nội dung.',
                'task_id.required' => 'Bạn chưa chọn loại công việc.',
            ]
        );
        $dataArr['user_id'] = Auth::id();
        TaskLog::create($dataArr);

        return response()->json(['message' => 'Gửi bình luận thành công']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Request $request)
    {
        $taskLogId = $request->logId;
        $taskLog = TaskLog::find($taskLogId);
        return response()->json(['data' => $taskLog]);
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
        $dataArr = $request->all();
        $id = $request->logId;
        $this->validate(
            $request,
            [
                'comment' => 'required',
                'task_id' => 'required'
            ],
            [
                'comment.required' => 'Bạn chưa nhập nội dung.',
                'task_id.required' => 'Bạn chưa chọn loại công việc.',
            ]
        );
        TaskLog::find($id)->update($dataArr);
        return response()->json(['message' => 'Chỉnh sửa bình luận thành công']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $id = $request->logId;
        $model = TaskLog::find($id);
        $model->delete();
        return response()->json(['message' => 'Xoá bình luận thành công']);
    }
}
