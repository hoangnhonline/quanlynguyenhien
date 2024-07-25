@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Doanh thu khác
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('revenue.index') }}">Doanh thu khác</a></li>
      <li class="active">Tạo mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ $back_url ?? route('revenue.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('revenue.store') }}" id="dataForm">
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
              <div class="form-group">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="not_kpi" value="1" {{ old('not_kpi') == 1 ? "checked" : "" }}>
                      <span style="color: red">KHÔNG TÍNH KPI</span>
                    </label>
                  </div>               
                </div>
                <div class="form-group">
                  <label for="status">Trạng thái</label>
                  <select class="form-control select2" name="status" id="status">
                    <option value="">--Chọn--</option>
                    <option value="1"  {{ old('status') == 1 ? "selected" : "" }}>Đã thu</option>
                    <option value="2"  {{  old('status') == 2 ? "selected" : "" }}>Chưa thu</option>
                  </select>
                </div>
                <div class="form-group">
                    <label for="email">Tỉnh/Thành</label>
                    <select class="form-control select2" name="city_id" id="city_id">
                      <option value="">--Chọn--</option>
                      @foreach($cityList as $city)
                      <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                      @endforeach
                    </select>
                  </div>              
                 <!-- text input -->
                <div class="form-group">
                  <label>Nội dung thu <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="content" id="contents" value="{{ old('content') }}">
                </div>
                <div class="row">
                  <div class="form-group col-xs-4">
                    <label>Số tiền<span class="red-star">*</span></label>
                    <input type="text" autocomplete="off" class="form-control number" name="amount" id="amount" value="{{ old('amount') }}">
                  </div>  
                  <div class="form-group col-xs-4">
                        <label>Người thu tiền <span class="red-star">*</span></label>
                        <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                          <option value="">--Chọn--</option>
                          @foreach($collecterList as $col)
                          <option value="{{ $col->id }}" {{ old('nguoi_thu_tien') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                          @endforeach
                        </select>
                    </div>
                  <div class="form-group col-xs-4">
                    <label>Ngày thu<span class="red-star">*</span></label>
                    <input type="text" autocomplete="off"  class="form-control datepicker" name="pay_date" id="pay_date" value="{{ old('pay_date') }}">
                  </div>
                </div>
                <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                  <label class="col-md-3 row">Hình ảnh </label>    
                  <div class="col-md-9">
                    <img id="thumbnail_image" src="{{ old('image_url') ? Helper::showImage(old('image_url')) : URL::asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="145" height="85">
                    
                    <input type="file" id="file-image" style="display:none" />
                 
                    <button class="btn btn-default" id="btnUploadImage" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                  </div>
                  <div style="clear:both"></div>
                  <input type="hidden" name="image_url" id="image_url" value="{{ old('image_url') }}"/>          
                  <input type="hidden" name="image_name" id="image_name" value="{{ old('image_name') }}"/>
                </div>                            
            </div>                        
            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('revenue.index')}}">Hủy</a>
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
<input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('#btnUploadImage').click(function(){        
        $('#file-image').click();
      });      
      var files = "";
      $('#file-image').change(function(e){
        $('#thumbnail_image').attr('src', "{{ URL::asset('admin/dist/img/loading.gif') }}");
         files = e.target.files;
         
         if(files != ''){
           var dataForm = new FormData();        
          $.each(files, function(key, value) {
             dataForm.append('file', value);
          });   
          
          dataForm.append('date_dir', 1);
          dataForm.append('folder', 'tmp');

          $.ajax({
            url: $('#route_upload_tmp_image').val(),
            type: "POST",
            async: false,      
            data: dataForm,
            processData: false,
            contentType: false,
            beforeSend : function(){
              $('#thumbnail_image').attr('src', "{{ URL::asset('admin/dist/img/loading.gif') }}");
            },
            success: function (response) {
              if(response.image_path){
                $('#thumbnail_image').attr('src',$('#upload_url').val() + response.image_path);
                $( '#image_url' ).val( response.image_path );
                $( '#image_name' ).val( response.image_name );
              }
              console.log(response.image_path);
                //window.location.reload();
            },
            error: function(response){                             
                var errors = response.responseJSON;
                for (var key in errors) {
                  
                }
                //$('#btnLoading').hide();
                //$('#btnSave').show();
            }
          });
        }
      });
  });
</script>
@stop