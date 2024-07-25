<p>Hi <strong>{{ $name }}</strong>!</p>

<p>Công việc <span style="font-weight: bold; color: blue">{{ $task_name }}</span> đã <strong style="color: green"> hoàn thành</strong><br> 
Click vào <a href="{{ route("task.index", ['task_id' => $task_id]) }}" target="_blank">đây</a> để xem chi tiết.</p>
