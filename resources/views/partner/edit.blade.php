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
      <li class="active">Cập nhật</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('partner.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('partner.update') }}" id="dataForm">
      <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="row">
      <!-- left column -->

      <div class="col-md-7">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Cập nhật</h3>
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
                  <select class="form-control select2" name="cost_type_id" id="cost_type_id">
                    <option value="">--Chọn--</option>
                    @foreach($costTypeList as $cate)
                    <option value="{{ $cate->id }}" {{ old('cost_type_id', $detail->cost_type_id) == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                    @endforeach
                  </select>
                </div>
                 <!-- text input -->
                 @if ($detail->cost_type_id == 24)
                 <div class="form-group">
                   <label>Tour <span class="red-star">*</span></label>
                   <select class="form-control select2" name="tour_id" id="tour_id">
                     <option value="">--Chọn--</option>
                     @foreach($listTour as $tour)
                     <option value="{{ $tour->id }}" {{ old('tour_id',$detail->tour_id) == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                     @endforeach
                   </select>
                   
                 </div>
                 @endif
                <div class="form-group">
                  <label>Tên đối tác/phân loại <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $detail->name) }}">
                </div>
                <div class="form-group">
                  <label>Số điện thoại<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                </div>  
                <div class="form-group">
                  <label>Email<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="email" id="email" value="{{ old('email', $detail->email) }}">
                </div>  
                <div class="form-group">
                  <label>Khu vực: </label>
                  <div class="row">
                    <?php $i = 0; ?>
                  @foreach($cityList as $city)
                  <?php $i++; ?>
                  <div class="col-md-3">
                    <input {{ in_array($city->id, $arrSelectedArr) ? "checked" : "" }} id="city_id{{ $city->id }}" type="checkbox" name="city_id[]" value="{{ $city->id }}"> 
                    <label style="cursor: pointer;" for="city_id{{ $city->id }}">{{ $city->name }}</label>
                  </div>
                @if($i%4 == 0) </div><div class="row">@endif
                  @endforeach
                  </div>
                </div>
                <div class="form-group">
                  <label>Người liên hệ<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="contact_name" id="contact_name" value="{{ old('contact_name', $detail->contact_name) }}">
                </div>  
                <div class="form-group">
                  <label>Số ĐT người liên hệ<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $detail->contact_phone) }}">
                </div>  
                <div class="form-group">
                  <label>Thứ tự hiển thị <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="display_order" id="display_order" value="{{ old('display_order', $detail->display_order) }}">
                </div>
                <div class="form-group">
                  <label for="status">Trạng thái</label>
                  <select class="form-control select2" name="status" id="status">
                    <option value="1" {{ old('status', $detail->status) == 1 ?? "selected" }}>Đang làm</option>
                    <option value="2" {{ old('status', $detail->status) == 2 ?? "selected" }}>Đã nghỉ</option>
                  </select>
                </div>
                <!-- textcity -->
                <div class="form-group">
                  <label>Mô tả</label>
                  <textcity class="form-control" rows="4" name="description" id="descriptions">{{ old('description', $detail->description) }}</textcity>
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