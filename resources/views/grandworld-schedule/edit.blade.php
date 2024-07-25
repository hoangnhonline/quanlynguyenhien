@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Chi phí
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('cost.index') }}">Chi phí</a></li>
      <li class="active">Cập nhật</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('cost.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('cost.update') }}" id="dataForm">
      <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
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
              <input type="hidden" name="type" id="type" value="1">
              <div class="form-group">
                    <label for="email">Tỉnh/Thành</label>
                    <select class="form-control" name="city_id" id="city_id">
                      <option value="">--Chọn--</option>
                      @foreach($cityList as $city)
                      <option value="{{ $city->id }}" {{ old('city_id', $detail->city_id) == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                      @endforeach
                    </select>
                  </div> 
              <div class="row">
                <div class="form-group col-xs-6">                    
                  <label>Loại chi phí<span class="red-star">*</span></label>
                  <select class="form-control select2" id="cate_id" name="cate_id">     
                      <option value="">--Chọn--</option>      
                      @foreach($cateList as $cate)
                      <option value="{{ $cate->id }}" {{ old('cate_id', $detail->cate_id) == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                      @endforeach
                  </select>
                </div>
              <div class="form-group col-xs-6">
                @php
                    if($detail->date_use){
                        $date_use = old('date_use', date('d/m/Y', strtotime($detail->date_use)));
                    }else{
                        $date_use = old('date_use');
                    }
                  @endphp
                <label for="email">Ngày</label>
                 <input type="text" name="date_use" class="form-control datepicker" value="{{ old('date_use', $date_use) }}" autocomplete="off">
                </div>                
              </div>
              <div class="row" id="load_doi_tac">
                  @if($partnerList->count() > 0)
                  <div class="form-group col-md-12">
                      <label>Chi tiết<span class="red-star">*</span></label>
                      <select class="form-control select2" id="partner_id" name="partner_id">     
                        <option value="">--Chọn--</option>      
                        @foreach($partnerList as $cate)
                        <option value="{{ $cate->id }}" {{ old('partner_id', $detail->partner_id) == $cate->id ? "selected" : "" }}>
                          {{ $cate->name }}
                        </option>
                        @endforeach
                      </select>
                  </div>
                  @endif
              </div>               
                <div class="row">
                  <div class="form-group col-md-6 col-xs-6">
                    <label for="email">PTT CODE</label>
                    <input type="text" name="booking_id" class="form-control" value="{{ old('booking_id',  $detail->booking_id) }}" autocomplete="off">
                  </div>
                  <div class="tinh-toan" >
                    <div class="form-group col-xs-6">
                      <label for="amount">Số lượng</label>
                      <input type="text" name="amount" class="form-control amount" placeholder="Số lượng" value="{{ old('amount', $detail->amount) }}">
                    </div>
                    <div class="form-group col-xs-6">
                      <label for="price">Giá</label>
                      <input type="text" name="price" class="form-control number gia" placeholder="Giá" value="{{ old('price', $detail->price) }}">
                    </div>                  
                    <div class="form-group col-xs-6">
                      <label for="total_money">Tổng tiền</label>
                      <input type="text" name="total_money" class="form-control number total" placeholder="Tổng tiền" value="{{ old('total_money', $detail->total_money) }}">
                    </div>
                  </div>
                </div>
                <div style="clear: both"></div>
                <p style="font-weight: bold; margin-top: 10px">NGƯỜI CHI TIỀN</p>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" id="inlineCheckbox2" {{ old('nguoi_chi', $detail->nguoi_chi) == 1 ? "checked" : "" }} name="nguoi_chi" value="1">
                  <label class="form-check-label" for="inlineCheckbox2" style="color: red">CTY</label>
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input {{ old('nguoi_chi', $detail->nguoi_chi) == 2 ? "checked" : "" }} class="form-check-input" type="radio" id="inlineCheckbox1" name="nguoi_chi" value="2">
                  <label class="form-check-label" for="inlineCheckbox1" style="color: red" >ĐIỀU HÀNH</label>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input {{ old('nguoi_chi', $detail->nguoi_chi) == 3 ? "checked" : "" }} class="form-check-input" type="radio" id="inlineCheckbox3" name="nguoi_chi" value="3">
                  <label class="form-check-label" for="inlineCheckbox3" style="color: red" >CÔNG NỢ</label>
                </div>
                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="is_fixed" value="1" {{ old('is_fixed', $detail->is_fixed) == 1 ? "checked" : "" }}>
                      <span style="color: red">Cố định</span>
                    </label>
                  </div>               
                </div>
                <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                  <label class="col-md-3 row">Hình ảnh </label>
                  <div class="col-md-9">
                    <img id="thumbnail_image" src="{{ old('image_url', $detail->image_url) ? Helper::showImageNew(old('image_url', $detail->image_url)) : URL::asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="145" height="85">
                    
                    <input type="file" id="file-image" style="display:none" />
                 
                    <button class="btn btn-default" id="btnUploadImage" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                  </div>
                  <div style="clear:both"></div>
                  <input type="hidden" name="image_url" id="image_url" value="{{ old('image_url', $detail->image_url) }}"/>          
                  <input type="hidden" name="image_name" id="image_name" value="{{ old('image_name') }}"/>
                </div>     
                <div class="form-group">
                  <label for="notes">Ghi chú</label>
                  <textarea class="form-control" name="notes" placeholder="Ghi chú" id="notes">{!! old('notes', $detail->notes) !!}</textarea>
                </div>
          
                     
            
            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('cost.index')}}">Hủy</a>
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
<input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){    
    $('#cate_id').change(function(){
        $.ajax({
          url : "{{ route('cost.ajax-doi-tac') }}",
          data: {
            cate_id : $(this).val(),
            city_id : $('#city_id').val()
          },
          type : "GET", 
          success : function(data){  
            if(data != 'null'){
              $('#load_doi_tac').html(data);
              if($('#partner_id').length==1){
                $('#partner_id').select2();  
              }              
            }
          }
        });
    });
    $('.tinh-toan .amount, .tinh-toan .gia').blur(function(){      
      var parent = $(this).parents('.tinh-toan');
      tinhtoangia(parent);
    });   
  });
  function tinhtong(){
    var tong = 0;
    $('.total').each(function(){
      var total = parseInt($(this).val());
      if(total > 0){
        tong += total;
      }
    });
    $('#total_money').val(tong);
  }
  function tinhtoangia(parent){ 
      var amount = parent.find('.amount').val();
      var gia = parent.find('.gia').val();
      var total = gia*amount;
      parent.find('.total').val(total);
      tinhtong();
  }
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