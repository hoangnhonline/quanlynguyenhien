@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Đối tác/phân loại
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('partner.index') }}">Đối tác/phân loại</a></li>
      <li class="active">Tạo mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('partner.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('partner.store') }}" id="dataForm">
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
               
                <div class="form-group">
                  <label for="type">Phân loại</label>
                  <select class="form-control select2" name="cost_type_id" id="cost_type_id" @if($cost_type_id) readonly @endif>
                    <option value="">--Chọn--</option>
                    @foreach($costTypeList as $cate)
                    <option value="{{ $cate->id }}" {{ $cost_type_id == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                    @endforeach
                  </select>
                </div>
                 <!-- text input -->
                @if ($cost_type_id == 24)
                <div class="form-group">
                  <label>Tour <span class="red-star">*</span></label>
                  <select class="form-control select2" name="tour_id" id="tour_id">
                    <option value="">--Chọn--</option>
                    @foreach($listTour as $tour)
                    <option value="{{ $tour->id }}" {{ old('tour_id') == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                    @endforeach
                  </select>
                  
                </div>
                @endif
                
                <div class="form-group">
                  <label>Tên đối tác/phân loại <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                  <label>Số điện thoại<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                  <label>Email<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                  <label>Tỉnh/thành: </label>
                  <div class="row">
                    <?php $i = 0; ?>
                  @foreach($cityList as $city)
                  <?php $i++; ?>
                  <div class="col-md-3">
                    <input id="city_id{{ $city->id }}" type="checkbox" name="city_id[]" value="{{ $city->id }}"> 
                    <label style="cursor: pointer;" for="city_id{{ $city->id }}">{{ $city->name }}</label>
                  </div>
                @if($i%4 == 0) </div><div class="row">@endif
                  @endforeach
                  </div>
                </div>
                <div class="form-group">
                  <label>Người liên hệ<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="contact_name" id="contact_name" value="{{ old('contact_name') }}">
                </div>  
                <div class="form-group">
                  <label>Số ĐT người liên hệ<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}">
                </div>  
                <div class="form-group">
                  <label>Thứ tự hiển thị <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="display_order" id="display_order" value="{{ old('display_order') }}">
                </div>
                <div class="form-group">
                  <label for="status">Trạng thái</label>
                  <select class="form-control select2" name="status" id="status">
                    <option value="1" {{ old('status') == 1 ?? "selected" }}>Đang làm</option>
                    <option value="2" {{ old('status') == 2 ?? "selected" }}>Đã nghỉ</option>
                  </select>
                </div>
                <!-- textarea -->
                <div class="form-group">
                  <label>Mô tả</label>
                  <textarea class="form-control" rows="4" name="description" id="descriptions">{{ old('description') }}</textarea>
                </div> 
                                            
            </div>                        
            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('partner.index')}}">Hủy</a>
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
@section('js')
<script type="text/javascript">
  
</script>
@stop