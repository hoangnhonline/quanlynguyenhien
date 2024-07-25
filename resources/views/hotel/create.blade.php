@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Khách sạn   
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('hotel.index') }}">Khách sạn</a></li>
      <li class="active">Thêm mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('hotel.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('hotel.store') }}" id="dataForm" class="productForm">
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
                    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Hình ảnh</a></li>
                    <li role="presentation"><a href="#doitac" aria-controls="doitac" role="tab" data-toggle="tab">Đối tác</a></li>  
                    <li role="presentation"><a href="#lienhe" aria-controls="settings" role="tab" data-toggle="tab">Thông tin liên hệ</a></li>     
                    <li role="presentation" style="display: none;"><a href="#tiennghi" aria-controls="settings" role="tab" data-toggle="tab">Tiện nghi</a></li>  
                    <li role="presentation" style="display: none;"><a href="#meta" aria-controls="settings" role="tab" data-toggle="tab">Thông tin meta</a></li>
                    <li role="presentation"  style="display: none;"><a href="#chinhsach" aria-controls="settings" role="tab" data-toggle="tab">Chính sách</a></li>                    
                                   
                  </ul>

                  <!-- Tab panes -->
                  <div class="tab-content">
                   
                    <div role="tabpanel" class="tab-pane active" id="home">                         
                      <input type="hidden" name="type" value="1">  
                      <div class="form-group">
                        <label for="email">Tỉnh/Thành</label>
                        <select class="form-control select2" name="city_id" id="city_id">
                          <option value="">--Chọn--</option>
                          @foreach($cityList as $city)
                          <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                          @endforeach
                        </select>
                      </div>      
                       <div class="form-group">
                            <label>Loại hình</label>
                            <select class="form-control select2" name="hotel_type" id="hotel_type">
                              <option value="">--Chọn--</option>
                              @foreach($hotelType as $type)
                              <option value="{{ $type->id }}" {{ old('hotel_type') == $type->id ? "selected" : "" }}>{{ $type->name }}</option>
                              @endforeach
                            </select>
                          </div> 
                      <div class="row">                                                                             
                        <div class="form-group col-md-6" >                  
                          <label>Tên khách sạn <span class="red-star">*</span></label>
                          <input type="text" class="form-control req" name="name" id="name" value="{{ old('name') }}">
                        </div>
                        <div class="form-group col-md-6">                  
                          <label>Slug <span class="red-star">*</span></label>                  
                          <input type="text" class="form-control req" readonly="readonly" name="slug" id="slug" value="{{ old('slug') }}">
                        </div>
                      </div>
                        <div class="row">
                          <div class="col-md-4 form-group" >                  
                            <label>Khu vực<span class="red-star">*</span></label>
                            <input type="text" class="form-control req" name="khu_vuc" id="khu_vuc" value="{{ old('khu_vuc') }}">
                          </div>
                          <div class="form-group col-md-8">
                            <label>Địa chỉ</label>
                            <input type="input" id="address" class="form-control" name="address" value="{{ old('address') }}">
                        </div>                       
                        </div>  
                      <!--   <div class="row">
                          <div class="col-md-4 form-group" >                  
                            <label>Loại hoa hồng</label>
                            <select class="form-control" name="com_type" id="com_type">
                              <option value="1" {{ old('com_type') == 1 ? "selected" : "" }}>Ăn %</option>
                              <option value="2" {{ old('com_type') == 2 ? "selected" : "" }}>Tự kê</option>
                            </select>
                          </div>
                          <div class="form-group col-md-8">
                            <label>Số tiền kê</label>
                            <input type="input" id="com_value" class="form-control number" name="com_value" value="{{ old('com_value') }}" placeholder="Chỉ nhập khi loại hoa hồng là tự kê">
                        </div>
                         
                       
                        </div> --> 
                         <div class="form-group" style="margin: 20px 0 20px 0">
                          @foreach($featuredList as $obj)
                          <div class="col-md-3">
                            <label>
                              <input type="checkbox" {{ in_array($obj->id, old('featured_id', [])) ? "checked" : "" }} name="featured_id[]" value="{{ $obj->id }}">
                              {{ $obj->name }}
                            </label>
                          </div>
                          @endforeach
                        </div>                     
                        <div class="row">
                          <div class="col-md-4 form-group">
                            <label>Số sao</label>
                            <select class="form-control select2" name="stars" id="stars">
                              <option value="1" {{ old('star') == 1 ? "selected" : "" }}>1 sao</option>
                              <option value="2" {{ old('star') == 2 ? "selected" : "" }}>2 sao</option>
                              <option value="3" {{ old('star') == 3 ? "selected" : "" }}>3 sao</option>
                              <option value="4" {{ old('star') == 4 ? "selected" : "" }}>4 sao</option>
                              <option value="5" {{ old('star') == 5 ? "selected" : "" }}>5 sao</option>
                              <option value="6" {{ old('star') == 6 ? "selected" : "" }}>6 sao</option>
                              <option value="7" {{ old('star') == 7 ? "selected" : "" }}>7 sao</option>
                            </select>
                          </div>                          
                          <div class="col-md-4 form-group">
                            <label>Giá thấp nhất</label>
                            <input type="text" class="form-control number" maxlength="11" name="price_lowest" id="price_lowest" value="{{ old('price_lowest') }}">
                          </div>
                           <div class="col-md-4 form-group" >                  
                            <label>Video Youtube ID<span class="red-star">*</span></label>
                            <input type="text" class="form-control req" name="video_id" id="video_id" value="{{ old('video_id') }}">
                          </div>
                        </div>
                        
                        
                        <div class="form-group col-md-6">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="is_hot" value="1" {{ old('is_hot') == 1 ? "checked" : "" }}>
                              <span style="color:red">NỔI BẬT</span>
                            </label>
                          </div>               
                        </div>    
                        <div class="form-group col-md-6">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="partner" value="1" {{ old('partner') == 1 ? "checked" : "" }}>
                              <span style="color:red">ĐỐI TÁC</span>
                            </label>
                          </div>               
                        </div>    
                        <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                          <label class="col-md-3 row">Banner ( 1350x500 px)</label>    
                          <div class="col-md-9">
                            <img id="thumbnail_banner" src="{{ old('banner_url') ? Helper::showImage(old('banner_url')) : asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="300">                    
                            <button class="btn btn-default btn-sm btnSingleUpload" data-set="banner_url" data-image="thumbnail_banner" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                            <input type="hidden" name="banner_url" id="banner_url" value="{{ old('banner_url') }}"/>
                          </div>
                          <div style="clear:both"></div>
                        </div>                   
                        <div class="form-group" style="margin-top: 15px !important;">
                          <label>Giới thiệu</label>
                          <button class="btnUploadEditor btn btn-info" type="button" style="float:right;margin-bottom: 3px !important;" data-editor="description">Chèn ảnh</button>
                          <div class="clearfix"></div>
                          <textarea class="form-control" rows="4" name="description" id="description">{{ old('description') }}</textarea>
                        </div>                                       
                        
                        
                        <div style="margin-bottom:10px;clear:both"></div>
                        <div class="clearfix"></div>
                    </div><!--end thong tin co ban-->                    
                    <input type="hidden" id="editor" value="">                     
                     <div role="tabpanel" class="tab-pane" id="settings">
                        <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                         
                          <div class="col-md-12" style="text-align:center">
                         
                            <button class="btn btn-primary btnMultiUpload" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                            <div class="clearfix"></div>
                            <div id="div-image" style="margin-top:10px"></div>
                          </div>
                          <div style="clear:both"></div>
                        </div>

                     </div><!--end hinh anh--> 
                     <div role="tabpanel" class="tab-pane" id="doitac">
                        <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                          @foreach($partnerList as $partner)                         
                          <div class="col-md-4" style="text-align:left">
                           <div class="form-group">
                            <div class="checkbox">
                              <label>
                                <input type="checkbox" name="partner_id[]" value="{{ $partner->id }}" {{ in_array($partner->id, old('partner_id', [])) ? "checked" : "" }}>
                                <span>{{ $partner->name }}</span>
                              </label>
                            </div>               
                          </div>   
                          </div>
                          @endforeach
                          <div style="clear:both"></div>
                        </div>

                     </div><!--end hinh anh-->
                     <div role="tabpanel" class="tab-pane" id="lienhe">
                          <div class="form-group">
                            <label>Email khách sạn </label>
                            <input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}">
                          </div>
                          <div class="form-group">
                            <label>Website khách sạn </label>
                            <input type="text" class="form-control" name="website" id="website" value="{{ old('website') }}">
                          </div>
                          <div class="form-group">
                            <label>Số điện thoại </label>
                            <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">
                          </div>
                      </div>
                     <div role="tabpanel" class="tab-pane" id="tiennghi">
                        <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                          @foreach($hotelAmen as $amen)
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

                     </div><!--end hinh anh-->   
                     <div role="tabpanel" class="tab-pane" id="meta">
                      <div class="form-group">
                          <label>Meta title </label>
                          <input type="text" class="form-control" name="meta_title" id="meta_title" value="{{ old('meta_title') }}">
                        </div>
                        <div class="form-group">
                          <label>Meta desciption</label>
                          <textarea class="form-control" rows="6" name="meta_desc" id="meta_desc">{{ old('meta_desc') }}</textarea>
                        </div>   
                        <div class="form-group">
                          <label>Meta keywords</label>
                          <textarea class="form-control" rows="6" name="meta_keywords" id="meta_keywords">{{ old('meta_keywords') }}</textarea>
                        </div>                                                  
                      </div>
                      <div role="tabpanel" class="tab-pane" id="chinhsach">
                          <div class="row">
                              <div class="col-md-6 form-group" >                  
                              <label>Giờ nhận phòng</label>
                              <input type="text" class="form-control req" name="check_in" id="check_in" value="{{ old('check_in') }}">
                            </div>
                            <div class="col-md-6 form-group" >                  
                              <label>Giờ trả phòng</label>
                              <input type="text" class="form-control req" name="checkout" id="checkout" value="{{ old('checkout') }}">
                            </div>
                          </div>
                          <div class="form-group" >                  
                            <label>Phương thức thanh toán</label>
                            
                          </div>
                          <div class="form-group" >                  
                            <label>Chính sách hủy đổi</label>
                            <textarea class="form-control" name="policy" id="policy" rows="10">{{ old('policy') }}</textarea>
                          </div>
                          <div class="form-group" >                  
                            <label>Chính sách phụ thu</label>
                            <textarea class="form-control" name="surcharge" id="surcharge" rows="10">{{ old('surcharge') }}</textarea>
                          </div>
                      </div>
                      

                  </div>

                </div>
                  
            </div>
            <div class="box-footer">              
              <button type="button" class="btn btn-default" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i></button>
              <button type="submit" class="btn btn-primary" id="btnSave">Lưu</button>
              <a class="btn btn-default" class="btn btn-primary" href="{{ route('hotel.index')}}">Hủy</a>
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
        location.href="{{ route('hotel.create') }}?parent_id=" + $(this).val();
      })
      
      $('#dataForm').submit(function(){        
        $('#btnSave').hide();
        $('#btnLoading').show();
      });  
    });
    
</script>
@stop
