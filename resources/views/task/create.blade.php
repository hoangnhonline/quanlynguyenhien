<form role="form" method="POST" action="{{ route('task.store') }}" id="dataForm">
    <div class="row py-15">
        <div class="col-md-6">
            <!-- text input -->
            <div class="form-group">
                <label class="font-14 font-w-500">Tên công việc <span class="red-star">*</span></label>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}"
                    placeholder="Tên công việc" autocomplete="off">
            </div>
            <div class="form-group">
                <label class="font-14 font-w-500">Công việc cha
                    <select class="form-control select2" name="parent_task_id" id="parent_task_id">
                        <option value="">--Chọn--</option>
                        @include('task.taskSelectList', [
                            'tasks' => $taskList,
                            'selectedValue' => old('parent_task_id', $parent_task_id),
                            'prefix' => '',
                            'exclude_id' => null,
                        ])
                    </select>
            </div>
            <div class="form-group">
                <label class="font-14 font-w-500">Nhân viên <span class="red-star">*</span></label>
                <select class="form-control select2" name="staff_id" id="staff_id">
                    <option value="">--Chọn--</option>
                    @foreach ($userList as $item)
                        <option data-department-id={{ $item->department_id }} value="{{ $item->id }}"
                            {{ old('staff_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="font-14 font-w-500">Bộ phận <span class="red-star">*</span></label>
                <select class="form-control select2" name="department_id" id="department_id">
                    <option value="">--Chọn--</option>
                    @foreach ($departmentList as $department)
                        <option value="{{ $department->id }}"
                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <label class="font-14 font-w-500">Kế hoạch <span class="red-star"></span></label>
                <select class="form-control select2" name="plan_id" id="plan_id">
                    <option value="">--Chọn--</option>
                    @foreach ($planList as $plan)
                        <option value="{{ $plan->id }}"
                            {{ old('plan_id', $plan_id) == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="font-14 font-w-500">Loại công việc <span class="red-star">*</span></label>
                <select class="form-control select2" name="type" id="type">
                    <option value="1" {{ old('type') == 1 ? 'selected' : '' }}>Việc cố định</option>
                    <option value="2" {{ old('type') == 2 || old('type') == null ? 'selected' : '' }}>
                        Việc phát sinh
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="font-14 font-w-500" for="status">Trạng thái</label>
                <select class="form-control select2" name="status" id="status">
                    @foreach (Helper::getConstant('task_status') as $value => $label)
                        <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="font-14 font-w-500" for="percent">Tiến độ</label>
                <select class="form-control select2" name="percent" id="percent">
                    <option value="0">0%</option>
                    @for ($i = 5; $i <= 100; $i = $i + 5)
                        <option value="{{ $i }}" {{ old('percent') == $i ?? 'selected' }}>
                            {{ $i }}%</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label class="font-14 font-w-500">Ngày bắt đầu <span class="red-star">*</span></label>
                <input type="text" class="form-control datetimepicker" name="from_date" id="from_date"
                    value="{{ old('from_date') }}" autocomplete="off">
            </div>
            <div class="form-group">
                <label class="font-14 font-w-500">Ngày kết thúc </label>
                    <input type="text" class="form-control datetimepicker" name="to_date" id="to_date"
                        value="{{ old('to_date') }}" autocomplete="off">
            </div>
            <div class="form-group">
                <label class="font-14 font-w-500">Ngày hoàn thành</label>
                <input type="text" class="form-control datetimepicker" name="completed_date" id="completed_date"
                    value="{{ old('completed_date') }}" autocomplete="off">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="font-14 font-w-500">Mô tả </label>
                <textarea name="description" class="form-control mb-3" id="description" rows="3" placeholder="Mô tả"></textarea>
            </div>
        </div>
    </div>
</form>
