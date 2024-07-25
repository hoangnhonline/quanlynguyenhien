<form role="form" method="POST" id="todoForm">
    <input type="hidden" name="task_id" value="{{ $taskId }}">
    <div class="">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th scope="col" style="width: 20px">#</th>
                        <th scope="col">Tên việc cần làm<span class="red-star">*</span></th>
                        <th scope="col">Mô tả</th>
                        <th scope="col" style="width: 150px">Thời hạn</th>
                        <th scope="col"><i class="icon-options-vertical"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($taskDetails->isEmpty())
                        <tr>
                            <th scope="row" class="order">1</th>
                            <td>
                                <div class="form-group"><input type="text" class="form-control"
                                        name="data[0][content]" placeholder="tên việc cần làm"></div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="data[0][notes]" class="form-control mb-3" rows="3" placeholder="Mô tả"></textarea>
                                </div>
                            </td>
                            <td>
                                <div class="form-group"><input type="text" class="form-control datetimepicker"
                                        name="data[0][task_deadline]" autocomplete="off"
                                        placeholder="thời hạn"></div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm"><i
                                        class="icon-trash"></i></button>
                            </td>
                        </tr>
                    @else
                        @foreach ($taskDetails as $key => $taskDetail)
                            <tr>
                                <th scope="row">
                                    <span class="order">{{ $key + 1 }}</span>
                                    <input type="hidden" name="data[{{ $key }}][id]"
                                        value="{{ $taskDetail->id }}">
                                </th>
                                <td>
                                    <div class="form-group"><input type="text" class="form-control"
                                            name="data[{{ $key }}][content]"
                                            value="{{ $taskDetail->content }}" placeholder="tên việc cần làm"></div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <textarea name="data[{{ $key }}][notes]" class="form-control mb-3" rows="3" placeholder="Mô tả">{{ $taskDetail->notes }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group"><input type="text" class="form-control datetimepicker"
                                            name="data[{{ $key }}][task_deadline]"
                                            value="{{ !empty($taskDetail->task_deadline) ? date('d/m/Y H:i', strtotime($taskDetail->task_deadline)) : '' }}"
                                            autocomplete="off" placeholder="thời hạn"></div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm"><i
                                            class="icon-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-info" id="btn-add-row">Thêm</button>
        </div>
    </div>
</form>
