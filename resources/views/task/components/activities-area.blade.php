<div class="activities-list-area">
    <div class="note">
        <form id="comment-form">
            <div class="mb-3 font-15"><i class="icon-note"></i> <span class="font-w-600">Hoạt động</span></div>
            <div class="mb-3 font-14 px-4 d-flex flex-column align-items-end">
                <div class="notification-comment w-100"></div>
                <textarea name="comment" class="form-control mb-3" rows="3" id="edit-description" placeholder="Viết bình luận"></textarea>
                <input type="hidden" name="task_id" value="{{ $task_id }}">
                <button id="btn-comment" type="submit" class="btn btn-outline-success w-fit" data-task-log-id="">
                    <i class="icon-paper-plane"></i>
                    Gửi
                </button>
            </div>
            <div class="activities px-4">
                @include('task.components.activity-logs', ['logs' => $logs])
            </div>
        </form>
    </div>
</div>
