    @if (!$logs->isEmpty())
        @foreach ($logs as $log)
            <div class="activity-item media mb-3">
                <span>
                    <img src="https://img.freepik.com/free-icon/user_318-563642.jpg" width="30" alt=""
                        class="img-fluid d-flex mr-2 mt-1" />
                </span>
                @if (!empty($log->comment))
                    <div class="media-body task-comment">
                        <div>
                            <span class="font-15 font-w-500 text-blue">{{ $log->user->name }}</span>
                            <span> {{ $log->created_at->diffForHumans() }} </span>
                            @if ($log->created_at != $log->updated_at)
                                <span>(đã chỉnh sửa)</span>
                            @endif
                        </div>
                        <div class="font-14 border p-3 mb-2">
                            {!! $log->comment !!}
                        </div>
                        @if (Auth::id() === $log->user_id)
                            <a class="text-underline cusor-pointer comment-edit" data-task-log-id="{{ $log->id }}">Chỉnh
                                sửa</a>
                            <a class="text-underline cusor-pointer ml-2 comment-delete" data-task-log-id="{{ $log->id }}">Xoá</a>
                        @endif
                    </div>
                @else
                    <div class="media-body task-update">
                        <div>
                            <span class="font-15 font-w-500 text-blue">{{ $log->user->name }}</span>
                            <span class="font-14">{!! $log->description !!}</span>
                        </div>
                        <span> {{ $log->created_at->diffForHumans() }} </span>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
