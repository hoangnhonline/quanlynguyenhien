@if (!$tasks->isEmpty())
    <div class="sub-tasks mb-3">
        <div class="mb-3 font-15"><i class="icon-list"></i> <b>Công việc phụ</b></div>
        @foreach ($tasks as $task)
            <div class="sub-tasks-list px-4">
                <i class="icon-arrow-right"></i>
                <a class="sub-task-item text-underline mb-3 font-14" data-task-id="{{ $task->id }}">
                    {{ $task->name }}
                </a>
            </div>
        @endforeach
    </div>
@endif
