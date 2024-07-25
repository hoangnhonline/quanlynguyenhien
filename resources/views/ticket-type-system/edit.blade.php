@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Loại vé
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('ticket-type-system.index') }}">Danh mục</a></li>
      <li class="active">Cập nhật</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('ticket-type-system.index') }}" style="margin-bottom:5px">Quay lại</a>
   
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
          <form role="form" method="POST" action="{{ route('ticket-type-system.update') }}" id='dataForm'>
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
              <div class="form-group">
                <label>Tên loại vé <span class="red-star">*</span></label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $detail->name }}">
              </div>     
              <div class="form-group">
                <label>Giá vốn <span class="red-star">*</span></label>
                <input type="text" class="form-control number" name="price" id="display_order" value="{{ old('price', $detail->price) }}">
              </div>
              <div class="form-group">
                  <label>Thứ tự hiển thị <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="display_order" id="display_order" value="{{ old('display_order', $detail->display_order) }}">
                </div>
                <div class="form-group">
                  <label>Trạng thái</label>
                  <select class="form-control" name="status" id="status">
                    <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>Hiện</option>
                    <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Ẩn</option>
                  </select>
                </div>
              <div class="form-group">
                <label>Thành phố <span class="red-star">*</span></label>
                 <select class="form-control" name="city_id" id="city_id"> 
                    @foreach($cityList as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $detail->city_id) == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                    @endforeach                                         
                 </select>
              </div>           
            </div>                    
            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('ticket-type-system.index')}}">Hủy</a>
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