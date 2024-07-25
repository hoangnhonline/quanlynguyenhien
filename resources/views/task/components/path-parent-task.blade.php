@if (!empty($parents))
    <div class="mb-3 font-14">
        Công việc chính:
        @foreach ($parents as $parent)
            <a class="text-underline parent-task-item" data-task-id="{{ $parent->id }}">{{ $parent->name }}</a>
            <i class="icon-arrow-right font-13"></i>
        @endforeach
    </div>
@endif
