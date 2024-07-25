@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Tài xế
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('drivers.index') }}">Tài xế</a></li>
      <li class="active">Tạo mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div id="content_alert"></div>
    <a class="btn btn-default btn-sm" href="{{ $back_url ?? route('drivers.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('drivers.store') }}" id="dataForm" enctype="multipart/form-data">
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
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="is_verify" value="1" {{ old('is_verify') == 1 ? "checked" : "" }}>
                      <span style="color: red; font-weight: bold;">Đã ký HĐ</span>
                    </label>
                  </div>               
                </div>
                <div class="form-group">
                  <label for="type">Tỉnh/thành</label>
                  <select class="form-control select2" name="city_id" id="city_id">
                    @foreach($cityList as $city)
                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>Khu vực: </label>
                  <div class="row">
                    <?php $i = 0; ?>
                  @foreach($areaList as $area)
                  <?php $i++; ?>
                  <div class="col-md-3">
                    <input id="area_id{{ $area->id }}" type="checkbox" name="area_id[]" value="{{ $area->id }}"> 
                    <label style="cursor: pointer;" for="area_id{{ $area->id }}">{{ $area->name }}</label>
                  </div>
                @if($i%4 == 0) </div><div class="row">@endif
                  @endforeach
                  </div>
                </div>
                <div class="form-group">
                  <label for="type">Loại xe</label>
                  <select class="form-control select2" name="car_cate_id" id="car_cate_id">
                    <option value="">--Chọn--</option>
                    @foreach($carCateList as $cate)
                    <option value="{{ $cate->id }}" {{ $car_cate_id == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                    @endforeach
                  </select>
                </div>
               
                 <!-- text input -->
                  <div class="form-group">
                    <label>Tên hiển thị <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                  </div>
                  <div class="form-group">
                    <label>Số điện thoại<span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">
                  </div>                   
                <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                  <label class="col-md-3 row">Ảnh tài xế </label>    
                  <div class="col-md-9">
                    <img id="thumbnail_image" src="{{ old('image_url') ? Helper::showImage(old('image_url')) : asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="145" height="85">                 
                    <button class="btn btn-default btn-sm btnSingleUpload" data-set="image_url" data-image="thumbnail_image"  type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                  </div>
                  <div style="clear:both"></div>
                </div>
                <div style="clear:both"></div> 
                <div class="form-group" style="margin-top:20px;margin-bottom:10px">  
                         
                    <div class="col-md-12" style="padding-left: 0px">
                      <input type="file" id="file-image" value="Chọn hình ảnh" multiple="true" name="images[]" style="display: none;" />
           
            
                      <button class="btn btn-success" id="btnUploadImage" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload hình ảnh xe</button> <span id="so_anh" style="font-weight: bold;font-size: 18px"></span>
                      <div class="clearfix"></div>
                      <div id="div-image" style="margin-top:10px"></div>
                    </div>
                    <div style="clear:both"></div>
                  </div>
                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="3" name="notes" id="notes">{{ old('notes') }}</textarea>
                </div>  
                
                <input type="hidden" name="image_url" id="image_url" value="{{ old('image_url') }}"/>
                                            
            </div>                        
            <div class="box-footer">
              <button type="button" class="btn btn-default" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i></button>
              <button type="submit" class="btn btn-primary" id="btnSave">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('drivers.index')}}">Hủy</a>
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
  $(document).ready(function(){
    $('#btnUploadImage').click(function(){        
        $('#file-image').click();
      }); 
    $('#file-image').change(function(e){ 
        var so_anh = e.target.files.length;
        if(so_anh > 0){
          $('#so_anh').html('[Đã chọn '+ so_anh +' ảnh]');         
        }   
      });
   $('#dataForm').submit(function(){        
        $('#btnSave').hide();
        $('#btnLoading').show();
      });  
  });
</script>
@stop