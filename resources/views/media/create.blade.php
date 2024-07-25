@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Link
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('media.index') }}">Link</a></li>
      <li class="active">Tạo mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('media.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('media.store') }}" id="dataForm">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
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
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group" >
                  
                  <label>Ngày<span class="red-star">*</span></label>
                  <input type="text" autocomplete="off" class="form-control datepicker" name="date_photo" id="date_photo" value="{{ old('date_photo') }}">
                </div>

                </div>
                <div class="col-md-4">
                  <div class="form-group">
                  <label for="user_id">Người chụp</label>
                    <select class="form-control select2" name="user_id" id="user_id">
                      <option value="">--Chọn--</option>
                      @foreach($userList as $u)
                      <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? "selected" : "" }}>{{ $u->name }}</option>
                      @endforeach
                    </select>
                </div> 
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                  <label for="user_id">Loại</label>
                    <select class="form-control select2" name="type" id="type">
                      <option value="">--chọn--</option>
                      <option value="1" {{ old('type') == 1 ? "selected" : "" }}>Ảnh</option>
                      <option value="2"{{ old('type')  == 2 ? "selected" : ""}} >Flycam</option>
                    </select>
                </div> 
                </div>
              </div>
              <div class="row" style="margin: 20px 0px">
                <div class="form-group">
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio" class="form-check-input" id="area_id1" name="area_id" value="1" checked>
                      <label class="form-check-label" for="area_id1">Tour đảo</label>
                    </div>
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio" class="form-check-input"  id="area_id2" name="area_id" value="2">
                      <label class="form-check-label" for="area_id2">Grand World</label>
                    </div>
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio" value="3"  id="area_id3"  name="area_id" class="form-check-input">
                      <label class="form-check-label" for="area_id3">Rạch Vẹm</label>
                    </div>
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio"  id="area_id4" value="4"  name="area_id" class="form-check-input">
                      <label class="form-check-label" for="area_id4">Hòn Thơm</label>
                    </div>
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio" class="form-check-input"  id="area_id5" name="area_id" value="5">
                      <label class="form-check-label" for="area_id5">Bãi Sao - 2 Đảo</label>
                    </div>
                </div>
                </div> 
                <div class="form-group" >                  
                  <label>Link<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="link" id="link" value="{{ old('link') }}">
                </div> 
            </div>           
            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('media.index')}}">Hủy</a>
            </div>
            
        </div>
        <!-- /.box -->     

      </div>
      <div class="col-md-7">
             
    </div>
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    
  });
</script>
@stop