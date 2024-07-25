<div class="task-list-row">
    <div class="row row-eq-height">
        <div class="col-12">
            <table class="table table-bordered" id="table-list-data">
                <tr>
                    <th>Cần làm</th>
                    <th>Đang làm</th>
                    <th>Quá hạn</th>
                    <th>Đã xong</th>
                </tr>
                <tbody>
                    <tr>
                        <td>
                            {{ $statistics['todo'] }}
                        </td>
                        <td>
                            {{ $statistics['doing'] }}
                        </td>
                        <td>
                            {{ $statistics['over_deadline'] }}
                        </td>
                        <td>
                            {{ $statistics['done'] }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row row-eq-height">
        @foreach (Helper::getConstant('task_status') as $key => $name)
            <div class="col-12 col-md-6 col-lg mt-3 task-list-item">
                <div
                    class="card bg-primary-light {{ $key === 2 ? 'bg-task-inprogress' : '' }} {{ $key === 3 ? 'bg-task-done' : '' }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title font-15">{{ $name }}</div>
                        {{-- <div class="dropdown">
                        <a href="#" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="ml-2 icon-arrow-down"></i>
                        </a>
                    </div> --}}
                    </div>
                    @php
                        $tasks = [];
                        if (isset($items[$key])) {
                            $tasks = $items[$key];
                        }
                    @endphp
                    <div class="card-body overflow-auto" style="max-height: 100vh">
                        <div class="task-list" data-status="{{ $key }}" style="min-height: 25px">
                            @foreach ($tasks as $task)
                                <div class="card my-2 task-card" data-task-id="{{ $task->id }}">
                                    <div class="card-content">
                                        <div class="card-body p-4 body-color">
                                            <h4
                                                class="mb-3 font-w-600
                                            {{ !empty($task->to_date) && Helper::checkOverDeadline($task->status, $task->to_date) ? 'text-danger' : '' }}
                                            ">
                                                @if (!empty($task->to_date) && Helper::checkOverDeadline($task->status, $task->to_date))
                                                    <label class="label label-danger" style="padding: 5px;">Quá hạn</label>
                                                @endif
                                                {{ $task->name }}

                                            </h4>
                                            {{-- <div class="task-content mb-3">Description</div> --}}
                                            @if (!empty($task->staff_id))
                                                <p class="font-w-400 font-13"><i class="icon-user"></i>
                                                    <span class="task-date">
                                                        NV: <span class="text-blue">{{ $task->staff->name }}</span>
                                                    </span> tạo bởi: <span
                                                        class="text-blue">{{ $task->createdUser->name }}</span>
                                                </p>
                                            @endif
                                            @if (!empty($task->department))
                                                <p class="font-w-400 font-13"><i class="icon-organization"></i>
                                                    <span class="task-date">
                                                        {{ $task->department->name }}
                                                    </span>
                                                </p>
                                            @endif
                                            @if (!empty($task->from_date) || !empty($task->to_date))
                                                <p class="font-w-400 font-13">
                                                    @if (!empty($task->from_date))
                                                        <i class="icon-calendar"></i> <span
                                                            class="task-date">{{ \Carbon\Carbon::parse($task->from_date)->format('d/m/Y H:i') }}</span>
                                                    @endif
                                                    @if (!empty($task->to_date) && !empty($task->from_date))
                                                        -
                                                    @endif
                                                    @if (!empty($task->to_date))
                                                        <i class="icon-calendar"></i> <span
                                                            class="task-date {{ Helper::checkOverDeadline($task->status, $task->to_date) ? 'text-danger' : '' }}">{{ \Carbon\Carbon::parse($task->to_date)->format('d/m/Y H:i') }}
                                                        </span>
                                                    @endif
                                                </p>
                                            @endif
                                            @if (!empty($task->plan))
                                                <p class="font-w-400 font-13"><i class="icon-tag"></i>
                                                    <span class="task-date">
                                                        Kế hoạch: {{ $task->plan->name }}
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- <a href="#"
                        class="bg-primary w-100 d-block text-center py-2 px-2 mt-3 rounded text-white add-task"
                        data-toggle="modal" data-target="#addtask">
                        <i class="icon-plus align-middle text-white"></i> <span>Tạo công việc</span>
                    </a> --}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>


</div>
