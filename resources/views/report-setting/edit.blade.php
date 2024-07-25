@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
        Cập nhật mục tiêu
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('report-setting.index') }}">Mục tiêu lợi nhuận</a></li>
      <li class="active">Cập nhật</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('report-setting.index') }}" style="margin-bottom:5px">Quay lại</a>

    <div class="row">
      <!-- left column -->

      <div class="col-md-7">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            Chỉnh sửa
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <form role="form" method="POST" action="{{ route('report-setting.update') }}" id='dataForm'>
            {!! csrf_field() !!}
            <input type="hidden" name="id" value="{{ $detail->id }}">
            <div class="box-body">
              @if(Session::has('message'))
              <p class="alert alert-info" >{{ Session::get('message') }}</p>
              @endif
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

                  <!-- text input -->
                  <div class="form-group">
                      <label>Module <span class="red-star">*</span></label>
                      @php
                          $moduleList = ['tour', 'hotel', 'ticket','car']
                      @endphp
                      <select class="form-control select2" name="module" id="module">
                          @foreach($moduleList as $module)
                              <option
                                  value="{{ $module }}" {{ old('module', $detail->module) == $module ? "selected" : "" }}>{{ strtoupper($module) }}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="form-group">
                      <label>Tháng <span class="red-star">*</span></label>
                      <input type="number" class="form-control" name="month" id="month"
                             value="{{ old('month', $detail->month) }}">
                  </div>
                  <div class="form-group">
                      <label>Năm <span class="red-star">*</span></label>
                      <input type="number" class="form-control" name="year" id="year"
                             value="{{ old('year', $detail->year) }}">
                  </div>
                  <div class="form-group">
                      <label>Mục tiêu lợi nhuận <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="target" id="target"
                             value="{{ old('target', $detail->target) }}">
                  </div>
            </div>
            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('report-setting.index')}}">Hủy</a>
            </div>

        </div>
        <!-- /.box -->

      </div>
      <div class="col-md-5">

    </div>
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@stop
