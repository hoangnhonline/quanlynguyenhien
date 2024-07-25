@extends('layout')
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/tasks/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/tasks/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/tasks/css/jquery-ui.theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/tasks/css/simple-line-icons.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css" /> --}}
    <link rel="stylesheet" href="{{ asset('admin/tasks/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/css/datetimepicker.css') }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Danh sách công việc
                @if (!empty($parentTask))
                    trong "<strong style="color:#e8a23e">{{ $parentTask->name }}</strong>"
                @endif
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('task.index') }}">Danh sách công việc</a></li>
                <li class="active">Danh sách</li>
            </ol>
        </section>
        <div class="">
            <div class="content container-fluid site-width">
                <!-- START: Breadcrumbs-->
                <div class="row ">
                    <div class="col-md-12 align-self-center">
                        <div id="notification">
                        </div>
                    </div>
                </div>
                <!-- END: Breadcrumbs-->
                <a type="button" id="create-work" data-plan-id="{{ $filters['plan_id'] ?? '' }}"
                    data-parent-task-id="{{ $parent_task_id }}" class="btn btn-primary mt-2 btn-sm">Tạo mới</a>
                <div class="panel panel-default" style="margin-top: 5px;">
                    <div class="panel-body">
                        <form class="form-inline" role="form" method="GET" action="{{ route('task.index') }}"
                            id="filterForm">

                            {{-- @include('task.components.search-date-type', ['filters' => $filters]) --}}

                            <div class="form-group">
                                <input type="text" class="form-control daterange" autocomplete="off" name="range_date"
                                    value="{{ $filters['range_date'] ?? '' }}" />
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="s" id="seach"
                                    value="{{ $filters['s'] ?? '' }}" placeholder="Tên công việc" autocomplete="off">
                            </div>
                            <div class="form-group">

                                <select class="form-control select2" name="parent_task_id" id="parent_task_id"
                                    style="width: 170px">
                                    <option value="">--Công việc cha--</option>
                                    @include('task.components.parent-task-select', [
                                        'tasks' => $parentTaskList,
                                        'selectedValue' => $filters['parent_task_id'] ?? null,
                                    ])
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control select2" name="status" id="status">
                                    <option value="">--Trạng thái--</option>
                                    @foreach (Helper::getConstant('task_status') as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ ($filters['status'] ?? null) == $value ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="width: 140px">

                                <select class="form-control select2" name="department_id" id="department_id">
                                    <option value="">--Bộ phận--</option>
                                    @foreach ($departmentList as $item)
                                        <option value="{{ $item->id }}"
                                            {{ ($filters['department_id'] ?? null) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control select2" name="staff_id" id="staff_id" style="width: 140px">
                                    <option value="">--Nhân viên--</option>
                                    @foreach ($userList as $item)
                                        <option value="{{ $item->id }}"
                                            {{ ($filters['staff_id'] ?? null) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control select2" name="plan_id" id="plan_id" style="width: 140px">
                                    <option value="">--Kế hoạch--</option>
                                    @foreach ($planList as $plan)
                                        <option value="{{ $plan->id }}"
                                            {{ ($filters['plan_id'] ?? null) == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mt-2">
                                <button type="submit" class="btn btn-info btn-sm h-100">Lọc</button>
                                <a href="{{ route('task.index') }}" class="btn btn-default btn-sm h-100">Reset</a>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="adminV2">
                    <!-- START: Card Data-->
                    {!! $listComponent !!}

                    <div id="work-form-modal" class="modal fade overflow-auto" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                            </div>
                        </div>
                    </div>
                    <div id="work-infor-modal" class="modal fade overflow-auto" data-backdrop="static"
                        data-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                            </div>
                        </div>
                    </div>
                    <div id="todo-form-modal" class="modal fade overflow-auto" data-backdrop="static"
                        data-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                            </div>
                        </div>
                    </div>
                    <!-- END: Card DATA-->
                </div>
            </div>

        </div>
    </div>
    <div id="overlay">
        <div class="loader">
            <div class="inner one"></div>
            <div class="inner two"></div>
            <div class="inner three"></div>
        </div>
    </div>
    <style type="text/css">
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 12px !important;
        }

        .select2-container--bootstrap4 .select2-selection--single {
            height: 35px !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            margin-top: 0px !important;
        }

        .icon-close {
            font-size: 25px;
        }

        .icon-pencil {
            font-size: 20px;
            margin-right: -5px;
        }

        .adminV2 .form-control,
        .adminV2 .form-control:focus,
        .adminV2 .form-control:disabled,
        .adminV2 .form-control[readonly] {
            height: 50px !important;
        }
    </style>
@stop
@section('js')
    <script type="text/javascript" src="{{ asset('admin/dist/js/datetimepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/tasks/js/main.js?v=1') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            const workFormModal = $('#work-form-modal');
            const workInforModal = $('#work-infor-modal');
            const todoFormModal = $('#todo-form-modal');
            const filterForm = $('#filterForm');
            let todoRowClone = null;

            $(document).on({
                ajaxStart: function() {
                    $('#overlay').show();
                },
                ajaxStop: function() {
                    $('#overlay').hide();
                }
            });

            $(document).on('click', '[data-dismiss="modal"]', function() {
                const modal = $(this).closest('.modal');
                const taskId = modal.data('task-id')

                if (taskId) {
                    TaskHelper.getTask(taskId, workInforModal);
                    modal.data('task-id', '')
                }

                $(modal).modal("hide").removeClass("show");


            })

            $(document).on('change', '#dataForm #staff_id', function(e) {
                const test = $('#dataForm #staff_id').find(":selected").data('department-id');
                $('#dataForm #department_id').val(test || "").trigger('change');
            })

            //task list search - start
            $('input[name="time_type"], select', filterForm).on('change', function(e) {
                $(filterForm).submit();
            })



            // $(filterForm).on('submit', function(e) {
            //     e.preventDefault();
            //     const form = filterForm.serializeArray();
            //     $.each(form, (key, field) => {
            //         TaskHelper.handleForm(field)
            //     })
            //     TaskHelper.refreshList();
            // })
            //task list search - end

            //task form - start
            $(document).on('click', '#create-work , #task-edit', function(e) {
                const taskId = $(this).data('task-id');
                let url = "task/create";

                if (taskId) {
                    url = `task/${taskId}/edit`;
                }

                $.ajax({
                    url,
                    data: {
                        plan_id: $(this).data('plan-id') || undefined,
                        parent_task_id: $(this).data('parent-task-id') || undefined
                    },
                    success: function(response) {
                        workFormModal.find('.modal-content').html(response)
                        workFormModal.modal("show").addClass("show");
                        TaskHelper.reloadScript();

                        if (taskId) {
                            workInforModal.modal("hide").removeClass("show");
                            workFormModal.data('task-id', taskId);
                        }
                    },
                    error: function (res) {
                        if (res.status === 403) {
                            workInforModal.modal("hide").removeClass("show");
                            TaskHelper.displayNotification('#notification', "Không có quyền truy cập!", 'error')
                        }
                    }
                });
            })

            $(workFormModal).on('click', '#btnSaveInfo', function(e) {
                const taskId = workFormModal.data('task-id')

                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }

                const form = workFormModal.find('form').serialize();
                const formGroup = $(workFormModal).find('.form-group');
                let url = "task/store";

                formGroup.removeClass('has-error');
                formGroup.find('.help-block').remove();

                if (taskId) {
                    url = `task/${taskId}/update`;
                }

                $.ajax({
                    url,
                    type: "POST",
                    data: form,
                    success: function(res) {
                        if (taskId) {
                            TaskHelper.getTask(taskId, workInforModal)
                            setTimeout(() => {
                                TaskHelper.displayNotification(workInforModal.find(
                                    '.notification'), res.message)
                            }, 500);
                        } else {
                            TaskHelper.displayNotification('#notification', res.message)
                        }

                        workFormModal.data('task-id', '');
                        workFormModal.modal("hide").removeClass("show");
                        TaskHelper.refreshList();
                    },
                    error: function(res) {
                        const errors = res.responseJSON.errors;

                        for (name in errors) {
                            const formGroup = $(workFormModal).find('[name="' + name + '"]')
                                .closest('.form-group');
                            formGroup.addClass('has-error');
                            formGroup.find('.help-block').remove();
                            formGroup.append(`<span class="help-block">${errors[name]}</span>`);
                        }
                    }
                });
            });

            $(document).on('click', '.task-trash', function() {
                const name = $(this).data('task-name');
                const url = $(this).data('url');

                swal({
                    title: 'Bạn muốn xóa "' + name + '"?',
                    text: "Dữ liệu sẽ không thể phục hồi.",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then(function() {
                    $.ajax({
                        url,
                        success: function(res) {
                            workInforModal.modal("hide").removeClass("show");
                            TaskHelper.displayNotification('#notification', res.message)
                            TaskHelper.refreshList();
                        },
                        error: function(res) {
                            TaskHelper.displayNotification(workInforModal.find(
                                    '.notification'),
                                res.responseJSON.message, 'error')
                        }
                    });
                })
            })
            //task form - end

            $(document).on('click', ".task-card, .sub-task-item, .parent-task-item", function(e) {
                const taskId = $(this).data("task-id");
                TaskHelper.getTask(taskId, workInforModal)
            });

            //comment task - start
            $(document).on('click', '.comment-edit', function(e) {
                e.preventDefault();
                const that = $(this);
                const form = that.closest('form');
                const taskId = form.find('[name="task_id"]').val();
                const taskLogId = that.data('task-log-id');
                $.ajax({
                    url: `task/${taskId}/logs/${taskLogId}`,
                    success: function(res) {
                        const {
                            data
                        } = res;
                        form.find('[name="comment"]').val(data.comment);
                        form.find('#btn-comment').data('task-log-id', data.id);
                    },
                    error: function(res) {
                        const error = res.responseJSON.message;
                        TaskHelper.displayNotification(form.find('.notification-comment'),
                            error, 'error')
                    }
                });
            });

            $(document).on('click', '#btn-comment', function(e) {
                e.preventDefault();
                const that = $(this);
                const form = that.closest('form');
                const taskLogId = that.data('task-log-id');
                const taskId = $(form).find('[name="task_id"]').val();

                $.ajax({
                    url: `task/${taskId}/logs${taskLogId ? `/${taskLogId}` : ''}`,
                    type: "POST",
                    data: form.serialize(),
                    success: function(res) {
                        form.find('[name="comment"]').val('')
                        TaskHelper.displayNotification(form.find('.notification-comment'), res
                            .message)
                        that.data('task-log-id', '');
                        TaskHelper.refreshLogList(taskId)
                    },
                    error: function(res) {
                        const error = res.responseJSON;
                        TaskHelper.displayNotification(form.find('.notification-comment'), error
                            ?.errors
                            ?.comment,
                            'error')
                    }
                });
            });

            $(document).on('click', '.comment-delete', function() {
                const that = $(this);
                const form = that.closest('form');
                const taskId = form.find('[name="task_id"]').val();
                const taskLogId = that.data('task-log-id');

                swal({
                    title: 'Bạn muốn xóa bình luận này?',
                    text: "Dữ liệu sẽ không thể phục hồi.",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then(function() {
                    $.ajax({
                        url: `task/${taskId}/logs/${taskLogId}/destroy`,
                        type: "POST",
                        success: function(res) {
                            TaskHelper.displayNotification(form.find(
                                    '.notification-comment'), res
                                .message)
                            that.data('task-log-id', '');
                            form.find('[name="comment"]').val('')

                            $('#btn-comment').data('task-log-id', '');
                            TaskHelper.refreshLogList(taskId)
                        },
                        error: function(res) {
                            TaskHelper.displayNotification(form.find(
                                    '.notification-comment'),
                                res.responseJSON.message, 'error')
                        }
                    });
                })
            })
            //comment task - end

            // todo form event - start
            $(document).on('click', '#todo-form', function(e) {
                e.preventDefault();
                const that = $(this);
                const taskId = $(this).data("task-id");

                $.ajax({
                    url: `task/${taskId}/todos/create`,
                    success: function(res) {
                        todoFormModal.find('.modal-content').html(res)
                        todoFormModal.modal("show").addClass("show");
                        todoRowClone = todoFormModal.find('tbody tr:first').clone();
                        TaskHelper.reloadScript();
                    },
                });
            });

            $(document).on('click', '#todoForm .icon-trash', function(e) {
                $(this).closest('tr').remove();

                const tr = $('#todoForm tbody tr:visible');
                tr.each(function(index, el) {
                    $(el).find("input, textarea").each(function() {
                        this.name = TaskHelper.changeTodoIndexInputName(this.name, index);
                    });
                    $(el).find('.order').text(index + 1)
                })
            })

            $(document).on('click', '#todoForm #btn-add-row', function(e) {
                const index = $('#todoForm tbody tr:visible').length;
                const row = $(todoRowClone).clone();

                row.find('input, textarea').val('')
                row.find("input, textarea").each(function() {
                    this.name = TaskHelper.changeTodoIndexInputName(this.name, index);
                });
                row.find('.order').text(index + 1)
                $('#todoForm tbody').append(row);
                TaskHelper.reloadScript()
            })

            $(document).on('click', '.todo-check', function(e) {
                const status = e.target.checked ? 2 : 1;
                const todo_id = $(this).data('todo-id');
                const task_id = $(this).data('task-id');

                $.ajax({
                    url: `task/${task_id}/todos/${todo_id}/update`,
                    type: "POST",
                    data: {
                        task_id,
                        todo_id,
                        status
                    },
                    success: function(res) {
                        $('.todo-area').html(res)
                        TaskHelper.refreshLogList(task_id)
                    },
                });
            })

            $(document).on('click', '#btn-todo-save', function(e) {
                e.preventDefault();
                const taskId = $(this).data('task-id');
                const form = todoFormModal.find('form').serialize();
                const formGroup = $(todoFormModal).find('.form-group');
                formGroup.removeClass('has-error');
                formGroup.find('.help-block').remove();

                $.ajax({
                    url: `task/${taskId}/todos/store`,
                    type: "POST",
                    data: form,
                    success: function(res) {
                        TaskHelper.getTask(taskId, workInforModal);
                        todoFormModal.modal('hide').removeClass('show')
                        setTimeout(() => {
                            TaskHelper.displayNotification(workInforModal.find(
                                '.notification'), res.message)
                        }, 500);
                    },
                    error: function(res) {
                        const errors = res.responseJSON.errors;

                        for (name in errors) {
                            const formGroup = $(todoFormModal).find('[name="' + TaskHelper
                                    .convertDotToSquare(name) + '"]')
                                .closest('.form-group');
                            formGroup.addClass('has-error');
                            formGroup.find('.help-block').remove();
                            formGroup.append(
                                `<span class="help-block font-14">${errors[name]}</span>`);
                        }
                    }
                });
            });
            // todo form event - end

            const urlParams = new URLSearchParams(window.location.search);
            const qrTaskId = urlParams.get('task_id');
            if (qrTaskId) {
                TaskHelper.getTask(qrTaskId, workInforModal);
            }
            TaskHelper.init();
        });
    </script>
@stop
