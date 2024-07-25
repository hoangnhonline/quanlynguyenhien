    @if ($todos->isNotEmpty())
        <div class="todo-lists">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="font-15"><i class="icon-check"></i> <span class="font-w-600">Việc cần làm</span></div>
                {{-- <button type="button" class="btn btn-sm btn-outline-dark">Xoá</button> --}}
            </div>
            <div class="px-4">
                <div class="d-flex align-items-center">
                    @php
                        $qtyTodo = $todos->count();
                        $qtyTodoCompleted = $todos
                            ->filter(function ($todo) {
                                return $todo->status === 2;
                            })
                            ->count();
                        $percent = round(($qtyTodoCompleted / $qtyTodo) * 100) . '%';
                    @endphp
                    <div class="mr-4">{{ $percent }}</div>
                    <div class="progress my-4 w-100">
                        <div class="progress-bar" style="width: {{ $percent }}" role="progressbar" aria-valuenow="0"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div>
                    <ul class="todo-list">
                        @foreach ($todos as $todo)
                            <li class="todo-item {{ $todo->status === 2 ? 'trashed' : '' }}">
                                <label class="chkbox">
                                    <input type="checkbox" class="todo-check" data-task-id="{{ $todo->task_id }}"
                                        data-todo-id="{{ $todo->id }}"
                                        {{ $todo->status === 2 ? 'checked="checked"' : '' }}>
                                    <span class="checkmark mt-2"></span>
                                </label>
                                <div class="todo-content w-100 {{ $todo->status === 2 ? 'line-through' : '' }}">
                                    <h3 class="font-15 font-w-600">{{ $todo->content }}</h3>
                                    @if (!empty($todo->task_deadline))
                                        @php
                                            $isOverDeadline = $todo->status == 1 && \Carbon\Carbon::parse($todo->task_deadline)->isPast();
                                        @endphp
                                        <p
                                            class="font-14 text-muted mb-0 font-w-500 todo-date {{ $isOverDeadline ? 'text-danger' : '' }}">
                                            {{ \Carbon\Carbon::parse($todo->task_deadline)->format('d/m/Y H:i') }}
                                            @if ($isOverDeadline)
                                                (quá hạn)
                                            @endif
                                        </p>
                                    @endif
                                    <span class="font-14 small-content text-muted mb-0">
                                        {{ $todo->notes }}
                                    </span>
                                </div>
                                {{-- <div>
                            <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"><i class="icon-options-vertical font-16"></i></a>
                            <div class="dropdown-menu p-0 m-0 dropdown-menu-right">
                                <a class="dropdown-item edit-todo" href="#">Edit</a>
                                <a class="dropdown-item delete" href="#">Delete</a>
                            </div>
                        </div> --}}
                            </li>
                        @endforeach
                    </ul>
                    {{-- <a href="#" class="bg-primary py-2 px-2 rounded ml-auto text-white"
                    data-toggle="modal" data-target="#newtodo">
                    <i class="icon-plus align-middle text-white"></i> <span>Thêm thư mục</span>
                </a> --}}
                </div>
            </div>
        </div>
    @endif
