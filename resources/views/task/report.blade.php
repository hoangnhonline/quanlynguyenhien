@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Thống kê nhân viên
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('task.index') }}">Quản lý công việc</a></li>
                <li class="active">Thống kê</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Bộ lọc</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-inline" role="form" method="GET" action="{{ route('task.reports') }}"
                                id="searchForm">
                                <div class="form-group">
                                    <input type="text" class="form-control daterange" autocomplete="off"
                                        name="range_date" value="{{ $filters['range_date'] ?? '' }}" />
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="staff_id" id="staff_id">
                                        <option value="">--Nhân viên--</option>
                                        @foreach ($userList as $item)
                                            <option value="{{ $item->id }}"
                                                {{ ($filters['staff_id'] ?? null) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
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
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px;">Lọc</button>
                            </form>
                        </div>
                    </div>
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered" id="table-list-data">
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th width="15%">Nhân viên</th>
                                    <th>Cần làm</th>
                                    <th>Đang làm</th>
                                    <th>Quá hạn</th>
                                    <th>Đã xong</th>
                                </tr>
                                <tbody>
                                    @if ($users->count() > 0)
                                        <?php $i = 0; ?>
                                        @foreach ($users as $user)
                                            <?php $i++; ?>
                                            <tr id="row-{{ $user->id }}">
                                                <td><span class="order">{{ $i }}</span></td>
                                                <td>
                                                    <a
                                                        href="{{ route('task.index', ['staff_id' => $user->id]) }}">{{ $user->name }}</a>
                                                </td>
                                                <td>
                                                    {{ $user->task_todo_count }}
                                                </td>
                                                <td>
                                                    {{ $user->task_in_progress_count }}
                                                </td>
                                                <td>
                                                    {{ $user->task_over_deadline }}
                                                </td>
                                                <td>
                                                    {{ $user->task_done_count }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9">Không có dữ liệu.</td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                            <div style="text-align:center">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
        </section>
        <!-- /.content -->
    </div>
    <input type="hidden" id="table_name" value="task-detail">
@stop

@section('js')

    <script type="text/javascript"></script>
@stop
