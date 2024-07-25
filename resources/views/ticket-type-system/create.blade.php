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
      <li><a href="{{ route('ticket-type-system.index') }}">Loại vé </a></li>
      <li class="active">Tạo mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('ticket-type-system.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('ticket-type-system.store') }}" id="dataForm">
    <div class="row">
      <!-- left column -->

      <div class="col-md-7">
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
                  <label>Tên loại vé <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                </div>  
                <div class="form-group">
                  <label>Giá vốn <span class="red-star">*</span></label>
                  <input type="text" class="form-control number" name="price" id="price" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                  <label>Thứ tự hiển thị <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="display_order" id="display_order" value="{{ old('display_order') }}">
                </div>
                
                  <div class="form-group">
                    <label>Thành phố <span class="red-star">*</span></label>
                    <select class="form-control select2" name="city_id" id="city_id">
                      @foreach($cityList as $city)
                      <option value="{{ $city->id }}" {{ old('city_id', $city_id) == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
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