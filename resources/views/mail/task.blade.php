<p>Hi <strong>{{ $name }}</strong>!</p>

<p>Bạn vừa nhận được 1 công việc mới từ <span style="font-weight: bold; color: blue">{{ $created_user }}</span>
<br>
Click vào <a href="{{ route("task.index", ['task_id' => $task_id]) }}" target="_blank">đây</a> để xem chi tiết</p>
