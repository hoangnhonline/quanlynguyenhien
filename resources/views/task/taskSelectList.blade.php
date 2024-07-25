@foreach($tasks as $task)
    <option value="{{ $task->id }}"
        {{ $selectedValue == $task->id  ? "selected" : "" }}>
        {{ !empty($prefix) ? $prefix . ' > ' : '' }}{{ $task->name }}</option>
    @if($task->subtask_count)
        @include('task.taskSelectList', ['tasks' =>  \App\Models\Task::where('parent_task_id', $task->id)->where('id', '!=', $exclude_id)->where('status', '>', 0)->get(), 'selectedValue' =>  $selectedValue,'prefix' => (!empty($prefix) ? $prefix . ' > ' : '') . $task->name, 'exclude_id' => $exclude_id])
    @endif
@endforeach
