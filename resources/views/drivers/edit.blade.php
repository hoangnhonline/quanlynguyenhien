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
      <li class="active">Cập nhật</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('drivers.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('drivers.update') }}" id="dataForm" enctype="multipart/form-data">
      <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <div id="content_alert"></div>
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
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="is_verify" value="1" {{ old('is_verify', $detail->is_verify) == 1 ? "checked" : "" }}>
                      <span style="color: red; font-weight: bold;">Đã ký HĐ</span>
                    </label>
                  </div>               
                </div> 
                  <div class="form-group">
                  <label for="type">Thành phố</label>
                  <select class="form-control select2" name="city_id" id="city_id">
                    @foreach($cityList as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $detail->city_id) == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                    @endforeach                    
                  </select>
                </div>
                <div class="form-group">
                  <label for="type">Loại xe</label>
                  <select class="form-control select2" name="car_cate_id" id="car_cate_id">
                    <option value="">--Chọn--</option>
                    @foreach($carCateList as $cate)
                    <option value="{{ $cate->id }}" {{ old('car_cate_id', $detail->car_cate_id) == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
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
                    <input {{ in_array($area->id, $arrSelectedArr) ? "checked" : "" }} id="area_id{{ $area->id }}" type="checkbox" name="area_id[]" value="{{ $area->id }}"> 
                    <label style="cursor: pointer;" for="area_id{{ $area->id }}">{{ $area->name }}</label>
                  </div>
                @if($i%4 == 0) </div><div class="row">@endif
                  @endforeach
                  </div>
                </div>
                
                 <!-- text input -->
                <div class="form-group">
                  <label>Tên tài xế <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $detail->name) }}">
                </div>
                <div class="form-group">
                  <label>Số điện thoại<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                </div>                 
                <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                  <label class="col-md-3 row">Ảnh tài xế </label>    
                  <div class="col-md-9">
                    <img id="thumbnail_image" src="{{ $detail->image_url ? Helper::showImage($detail->image_url ) : asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="145" height="85">
                    <button class="btn btn-default btn-sm btnSingleUpload" data-set="image_url" data-image="thumbnail_image"  type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                  </div>
                  <div style="clear:both"></div>
                </div>
                <div class="form-group" style="margin-top:20px;margin-bottom:10px">  
                         
                          <div class="col-md-12" style="padding-left: 0px">
                            <input type="file" id="file-image" value="Chọn hình ảnh" multiple="true" name="images[]" style="display: none;" />
                 
                  
                            <button class="btn btn-success" id="btnUploadImage" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload hình ảnh xe</button> <span id="so_anh" style="font-weight: bold;font-size: 18px"></span>
                            <div class="clearfix"></div>
                            <div id="div-image" style="margin-top:10px">
                              @if(!empty($imageArr)) 
                              <?php $k = 0; ?>
                              <div class="row">
                              @foreach($imageArr as $img)
                              <?php $k++; ?>
                              <div class="col-md-3 col-xs-3 ">
                                <div class="divImg">
                                <img src="{{ Helper::showImageNew($img->image_url) }}" class="img-responsive">
                                <input type="hidden" name="imgOld[]" value="{{ $img->id }}">
                                 <button type="button" class="btn btn-sm btn-danger removeImg" data-id="{{ $img->id }}" value="Xóa">Xóa</button>
                                 </div>
                              </div>
                              @if($k%4 == 0) </div><div class="row"> @endif
                              @endforeach
                              </div>
                              @endif
                            </div>
                          </div>
                          <div style="clear:both"></div>
                        </div>
                <input type="hidden" name="image_url" id="image_url" value="{{ $detail->image_url }}"/>
                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="3" name="notes" id="notes">{{ old('notes', $detail->notes) }}</textarea>
                </div>  
                                          
            </div>                        
            <div class="box-footer">
              <button type="button" class="btn btn-default" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i></button>
              <button type="submit" class="btn btn-primary" id="btnSave">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('drivers.index')}}">Hủy</a>
            </div>
            
        </div>
        <!-- /.box -->     

      </div>
      

      </div>
      <!--/.col (left) -->      
    </div>
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<style type="text/css">
  .divImg{
    background-color: #ccc;
    text-align: center;
    padding: 10px;
    border-radius: 5px;
    margin-right: 10px;
    position: relative;
  }
  .divImg img{
    margin: 0 auto;
  }
  .removeImg{
    position: absolute;
    top: 5px;
    right: 5px;
  }
  #div-image .row{
    margin-bottom: 5px !important;
  }
</style>
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