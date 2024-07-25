<div class="modal-header align-items-center">
    <h4 class="modal-title font-20">{{ $title }}</h4>
    <div class="d-flex">
        <a data-task-id="{{ $task->id }}" class="btn font-16" id="todo-form" data-toggle="tooltip" data-placement="left" title=""
            data-original-title="Việc cần làm">
            <i class="icon-grid"></i>
        </a>
        @if (\App\Helpers\Helper::isSuperAdmin() || (Auth::user()->is_staff == 1 && Auth::id() == $task->created_user))
            <a data-task-name="{{ $task->name }}" data-url="{{ route('task.delete', ['id' => $task->id]) }}"
                class="font-16 btn task-trash {{ $task->status == 2 ? 'disabled' : '' }} " data-toggle="tooltip"
                data-placement="left" title="" data-original-title="Xoá công việc">
                <i class="icon-trash"></i>
            </a>
        @endif
        <a data-task-id="{{ $task->id }}" class="btn" id="task-edit" data-toggle="tooltip" data-placement="left"
            title="" data-original-title="Chỉnh sửa công việc">
            <i class="icon-pencil"></i>
        </a>
        <button type="button" class="font-16 close float-none" data-dismiss="modal" aria-label="Close">
            <i class="icon-close"></i>
        </button>
    </div>
</div>

<div class="modal-body">
    {!! $component !!}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
