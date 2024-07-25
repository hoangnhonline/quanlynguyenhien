@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Loại phòng
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('room.index', ['hotel_id' => $hotel_id]) }}">Loại phòng</a></li>
      <li class="active">Thêm mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('room.index', ['hotel_id' => $hotel_id]) }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('room.store') }}" id="dataForm" class="productForm">
    <input type="hidden" name="is_copy" value="1">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Thêm mới</h3>
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
                <div>

                  <!-- Nav tabs -->
                  <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Thông tin chi tiết</a></li>
                    <li role="presentation"><a href="#tiennghi" aria-controls="settings" role="tab" data-toggle="tab">Tiện nghi</a></li>
                  </ul>

                  <!-- Tab panes -->
                  <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="home">
                      <input type="hidden" name="type" value="1">
                      <div class="form-group">
                        <label for="email">Khách sạn</label>
                        <select class="form-control select2" name="hotel_id" id="hotel_id">
                          <option value="">--Chọn--</option>
                          @foreach($hotelList as $hotel)
                          <option value="{{ $hotel->id }}" {{ $hotel->id == old('hotel_id', $hotel_id) ? "selected" : "" }}>{{ $hotel->name }}</option>
                          @endforeach
                        </select>
                      </div>
                        <div class="form-group" >
                          <label>Loại phòng <span class="red-star">*</span></label>
                          <input type="text" class="form-control req" name="name" id="name" value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label>Số phòng thực tế (áp dụng cho loại phòng là villa)</label>
                            <select class="form-control select2" name="so_phong" id="so_phong">
                              <option value="1" {{ old('so_phong') == 1 ? "selected" : "" }}>1</option>
                              <option value="2" {{ old('so_phong') == 2 ? "selected" : "" }}>2</option>
                              <option value="3" {{ old('so_phong') == 3 ? "selected" : "" }}>3</option>
                              <option value="4" {{ old('so_phong') == 4 ? "selected" : "" }}>4</option>
                              <option value="5" {{ old('so_phong') == 5 ? "selected" : "" }}>5</option>
                              <option value="6" {{ old('so_phong') == 6 ? "selected" : "" }}>6</option>
                            </select>
                          </div>
                        <input type="hidden" name="status" value="1">
                       <!--  <div class="row">
                          <div class="col-md-4 form-group">
                            <label>Trạng thái</label>
                            <select class="form-control" name="status" id="status">
                              <option value="1" {{ old('status') == 1 ? "selected" : "" }}>Hiển thị</option>
                              <option value="2" {{ old('status') == 2 ? "selected" : "" }}>Ẩn</option>
                            </select>
                          </div>
                          <div class="col-md-4 form-group">
                            <label>Số phòng</label>
                            <input type="text" class="form-control" name="quantity" id="quantity" value="{{ old('quantity') }}">
                          </div>
                          <div class="col-md-4 form-group">
                            <label>Ăn sáng</label>
                            <select class="form-control" name="breakfast" id="breakfast">
                              <option value="1" {{ old('breakfast') == 1 ? "selected" : "" }}>Có</option>
                              <option value="0" {{ old('breakfast') == 0 ? "selected" : "" }}>Không</option>
                            </select>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 form-group">
                            <label>Người lớn tối đa</label>
                            <input type="text" class="form-control" name="adults" id="adults" value="{{ old('adults') }}">
                          </div>
                          <div class="col-md-4 form-group">
                            <label>Trẻ em tối đa</label>
                            <input type="text" class="form-control" name="children" id="children" value="{{ old('children') }}">
                          </div>
                          <div class="col-md-4 form-group">
                            <label>Số đêm nhỏ nhất</label>
                            <input type="text" class="form-control" name="min_stay" id="min_stay" value="{{ old('min_stay', 1) }}">
                          </div>
                        </div>  -->
                        <div class="row">
                          <!-- <div class="col-md-4 form-group">
                            <label>Số giường phụ</label>
                            <input type="text" class="form-control" name="extra_bed" id="extra_bed" value="{{ old('extra_bed') }}">
                          </div>
                          <div class="col-md-4 form-group">
                            <label>Phí giường phụ</label>
                            <input type="text" class="form-control" name="extra_bed_charges" id="extra_bed_charges" value="{{ old('extra_bed_charges') }}">
                          </div>      -->
                           <div class="col-md-12 form-group">
                            <div class="checkbox" style="padding-top: 20px;">
                              <label>
                                <input type="checkbox" name="is_hot" value="1" {{ old('is_hot') == 1 ? "checked" : "" }}>
                                <span style="color:red">NỔI BẬT</span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="form-group" style="margin-top:10px;margin-bottom:10px; display: none;">
                          <label class="col-md-3 row">Ảnh đại diện ( 850x450 px)</label>
                          <div class="col-md-9">
                            <img id="thumbnail_banner" src="{{ old('image_url') ? Helper::showImage(old('image_url')) : asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="300">
                            <button class="btn btn-default btn-sm btnSingleUpload" data-set="image_url" data-image="thumbnail_banner" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                            <input type="hidden" name="image_url" id="image_url" value="{{ old('image_url') }}"/>
                          </div>
                          <div style="clear:both"></div>
                        </div>
                        <div class="form-group" style="margin-top: 15px !important; display: none;">
                          <label>Mô tả</label>
                          <button class="btnUploadEditor btn btn-info" type="button" style="float:right;margin-bottom: 3px !important;" data-editor="description">Chèn ảnh</button>
                          <div class="clearfix"></div>
                          <textarea class="form-control" rows="4" name="description" id="description">{{ old('description') }}</textarea>
                        </div>

                        <div style="margin-bottom:10px;clear:both"></div>
                        <div class="clearfix"></div>
                    </div><!--end thong tin co ban-->
                    <input type="hidden" id="editor" value="">
                     <div role="tabpanel" class="tab-pane" id="tiennghi">
                        <div class="form-group" style="margin-top:10px;margin-bottom:10px">
                          @foreach($roomAmen as $amen)
                          <div class="col-md-4" style="text-align:left">
                           <div class="form-group">
                            <div class="checkbox">
                              <label>
                                <input type="checkbox" name="amenities[]" value="{{ $amen->id }}" {{ in_array($amen->id, old('amenities', [])) ? "checked" : "" }}>
                                <span>{{ $amen->name }}</span>
                              </label>
                            </div>
                          </div>
                          </div>
                          @endforeach
                          <div style="clear:both"></div>
                        </div>

                     </div>
                  </div>

                </div>

            </div>
            <div class="box-footer">
              <button type="button" class="btn btn-default" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i></button>
              <button type="submit" class="btn btn-primary" id="btnSave">Lưu</button>
              <a class="btn btn-default" class="btn btn-primary" href="{{ route('room.index', ['hotel_id' => $hotel_id])}}">Hủy</a>
            </div>

        </div>
        <!-- /.box -->

      </div>
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<input type="hidden" id="route_upload_tmp_image_multiple" value="{{ route('image.tmp-upload-multiple') }}">
<input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">
<style type="text/css">
  .nav-tabs>li.active>a{
    color:#FFF !important;
    background-color: #444345 !important;
  }
  .error{
    border : 1px solid red;
  }
  .select2-container--default .select2-selection--single{
    height: 35px !important;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice{
    color: red !important;
    font-size: 20px !important;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover{
    color: red !important;

    font-size:20px !important;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__rendered{
    font-size:20px !important;
  }
</style>
@stop
@section('javascript_page')
<script type="text/javascript">

    $(document).ready(function(){
       $(".select2").select2();
      $('#parent_id').change(function(){
        location.href="{{ route('room.create') }}?parent_id=" + $(this).val();
      })

      $('#dataForm').submit(function(){
        $('#btnSave').hide();
        $('#btnLoading').show();
      });
    });

</script>
@stop
