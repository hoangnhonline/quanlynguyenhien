@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Danh sách kế hoạch
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route( 'dashboard' ) }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('plan.index') }}">Danh sách kế hoạch</a></li>
                <li class="active">Tạo mới</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('plan.index') }}" style="margin-bottom:5px">Quay lại</a>
            <form role="form" method="POST" action="{{ route('plan.store') }}" id="dataForm">
                <div class="row">
                    <!-- left column -->

                    <div class="col-12">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Tạo mới</h3>
                            </div>
                            <!-- /.box-header -->
                            {!! csrf_field() !!}

                            <div class="box-body">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Tên kế hoạch <span class="red-star">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name"
                                           value="{{ old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label>Từ ngày </label>
                                    <input type="text" class="form-control datepicker" name="from_date"
                                           id="from_date" value="{{ old('from_date') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Đến ngày </label>
                                    <input type="text" class="form-control datepicker" name="to_date"
                                           id="to_date" value="{{ old('to_date') }}" autocomplete="off">
                                </div>

                                @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                                    <div class="form-group">
                                        <label>Bộ phận <span class="red-star">*</span></label>
                                        <select class="form-control" name="department_id" id="department_id">
                                            <option value="">--Chọn--</option>
                                            @foreach($departmentList as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ old('department_id') == $department->id  ? "selected" : "" }}>
                                                    {{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                                <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm"
                                   href="{{ route('plan.index')}}">Hủy</a>
                            </div>

                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left) -->
                </div>
            </form>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>

@stop
