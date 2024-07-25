<div class="notification"></div>
@include('task.components.path-parent-task', ['parents' => $task->parents ?? []])
<div class="card">
    <div class="card-content">
        <div class="card-body p-4 body-color">
            <h2 class="mb-3 font-w-600">
                @if (Helper::checkOverDeadline($task->status, $task->to_date))
                    <label class="label label-danger" style="padding: 5px;">Quá hạn</label>
                @endif
                {{ $task->name }}
                </h2>
            <div class="font-14 mb-3 font-w-400">
                được tạo bởi: <span class="text-blue">{{ $task->createdUser->name }}</span> cách đây
                <span class="text-blue">{{ $task->created_at->diffForHumans() }}</span>, cập nhật <span
                    class="text-blue">{{ $task->updated_at->diffForHumans() }}</span>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="font-w-400 font-15">
                        <i class="icon-user"></i>
                        <span>
                            <span class="font-w-500">Nhân viên:</span> <span
                                class="text-blue">{{ $task->staff->name ?? '' }}</span>
                        </span>
                    </p>
                    <p class="font-w-400 font-15">
                        <i class="icon-organization"></i>
                        <span>
                            <span class="font-w-500">Bộ phận:</span> {{ $task->department->name ?? '' }}
                        </span>
                    </p>
                    <p class="font-w-400 font-15"><i class="icon-tag"></i>
                        <span>
                            <span class="font-w-500">Kế hoạch:</span> {{ $task->plan->name ?? '' }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="font-w-400 font-15">
                        <i class="icon-hourglass"></i>
                        <span>
                            <span class="font-w-500">Trạng thái:</span> {{ $task->task_status }}
                        </span>
                    </p>
                    <p class="font-w-400 font-15">
                        <i class="icon-speedometer"></i>
                        <span>
                            <span class="font-w-500">Tiến độ:</span> {{ $task->percent }}%
                        </span>
                    </p>
                    <p class="font-w-400 font-15">
                        <i class="icon-calendar"></i> <span class="font-w-500">Ngày bắt đầu:</span>
                        @if (!empty($task->from_date))
                            <span>{{ \Carbon\Carbon::parse($task->from_date)->format('d/m/Y H:i') }}</span>
                        @endif
                    </p>
                    <p class="font-w-400 font-15">
                        <i class="icon-calendar"></i> <span class="font-w-500">Ngày kết thúc:</span>
                        @if (!empty($task->to_date))
                            <span
                                class="{{ Helper::checkOverDeadline($task->status, $task->to_date) ? 'text-danger' : '' }}">
                                {{ \Carbon\Carbon::parse($task->to_date)->format('d/m/Y H:i') }}
                            </span>
                        @endif
                    </p>
                    <p class="font-w-400 font-15">
                        <i class="icon-calendar"></i> <span class="font-w-500">Ngày hoàn thành:</span>
                        @if (!empty($task->completed_date))
                            <span>{{ \Carbon\Carbon::parse($task->completed_date)->format('d/m/Y H:i') }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="description">
                <div class="mb-3 font-15"><i class="icon-doc"></i> <span class="font-w-500">Mô tả</span></div>
                <div class="mb-3 font-14 p-4 content">{!! $task->description ?? '' !!}</div>
            </div>
            <div class="todo-area mb-4">
                @include('task.components.todo-area', ['todos' => $task->todos ?? []])
            </div>
            @include('task.components.sub-tasks-area', ['tasks' => $task->subTasks ?? []])
            @include('task.components.activities-area', [
                'logs' => $task->logs ?? [],
                'task_id' => $task->id,
            ])
        </div>
    </div>
</div>
