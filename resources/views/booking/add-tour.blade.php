@extends('layout')
@section('content')
<div class="content-wrapper">
  
  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">  
      Đặt tour</h1>    
  </section>
@php
date_default_timezone_set('Asia/Ho_Chi_Minh');
if(date('H:i') > '14:00'){
    $dateDefault = date('d/m/Y', strtotime("+1 day"));
}else{
    $dateDefault = date('d/m/Y');
}
@endphp
  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking.index') }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking.index') }}" style="margin-bottom:5px">Xem danh sách booking</a>
    <form role="form" method="POST" action="{{ route('booking.store') }}" id="dataForm">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <div id="content_alert"></div>
        <!-- general form elements -->
        <div class="box box-primary">
          
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
              <input type="hidden" name="type" value="1">
             
              <div class="row">
                <div class="form-group col-xs-4 col-md-4">
                  <label style="font-weight: bold; color: red">
                    <input type="checkbox" value="1" {{ old('not_pay') == 1 ? "checked" : "" }} id="not_pay" name="not_pay">
                    KHÔNG THU</label>
                </div>
                  <div class="form-group col-md-4  col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="ko_cap_treo" name="ko_cap_treo" value="1" {{ old('ko_cap_treo') == 1 ? "checked" : "" }}>
                        KHÔNG CÁP
                      </label>
                  </div>
                  <div class="form-group col-md-4  col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="ko_an" name="ko_an" value="1" {{ old('ko_an') == 1 ? "checked" : "" }}>
                        KHÔNG ĂN
                      </label>
                  </div>
                </div>
              <div class="row">
                <input type="hidden" name="tour_id" value="1">
                <input type="hidden" name="tour_cate" value="2">
                 <div class="form-group col-md-4 col-xs-12">
                   <label>Đối tác <span class="red-star">*</span></label>
                    <select class="form-control select2" name="user_id" id="user_id">
                      <option value="0">--Chọn--</option>
                      @foreach($listUser as $user)        
                      <option data-level="{{ $user->level }}" data-name="{{ $user->name }}" data-phone="{{ $user->phone }}" value="{{ $user->id }}" {{ old('user_id', $user_id_default) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                      @endforeach
                    </select>
                </div>
                 <div class="form-group col-md-4 col-xs-6">                    
                    <label>Ngày đi <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ old('use_date', $dateDefault) }}" autocomplete="off">
                  </div> 
                <div class="form-group col-md-4 col-xs-6">                  
                  <label>Hình thức <span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_type" name="tour_type">                      
                      <option value="1" {{ old('tour_type') == 1 ? "selected" : "" }}>Tour ghép</option>
                      <option value="2" {{ old('tour_type') == 2 ? "selected" : "" }}>Tour VIP</option>
                      <option value="3" {{ old('tour_type') == 3 ? "selected" : "" }}>Thuê cano</option>
                  </select>
                </div>
                
                  <input type="hidden" name="book_date" value="">
                </div>
             
                <div class="row">
                  <div class="form-group col-md-4 col-xs-6">                    
                    <label>Tên KH <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" autocomplete="off">
                  </div>  
                  <div class="form-group col-md-4 col-xs-6">                  
                    <label>Điện thoại <span class="red-star">*</span></label>
                    <input type="text" maxlength="20" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" autocomplete="off">
                  </div> 
                   <div class="col-md-4  col-xs-12">
                    <div class="input-group form-group"> 
                              
                    <label>Nơi đón <span class="red-star">*</span></label>

                    <select class="form-control select2" name="location_id" id="location_id">
                      <option value="">--Chọn--</option>
                      @foreach($listTag as $location)        
                      <option value="{{ $location->id }}" {{ old('location_id', 2947) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                      @endforeach
                    </select>
                    <span class="input-group-btn">
                      <button style="margin-top:30px" class="btn btn-primary btn-sm" id="btnAddTag" type="button" data-value="3">
                        Thêm  
                      </button>
                    </span>
                  </div>
                  </div>
                  
                </div>               
                <div class="row">
                  <div class="form-group col-xs-4">
                      <label>NL <span class="red-star">*</span></label>
                      <select class="form-control" name="adults" id="adults">
                        @for($i = 1; $i <= 150; $i++)            
                        <option value="{{ $i }}" {{ old('adults') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>TE(1m-1m4) <span class="red-star">*</span></label>
                      <select class="form-control" name="childs" id="childs">
                        @for($i = 0; $i <= 20; $i++)            
                        <option value="{{ $i }}" {{ old('childs') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>EB(dưới 1m)</label>
                      <select class="form-control" name="infants" id="infants">
                        @for($i = 0; $i <= 20; $i++)            
                        <option value="{{ $i }}" {{ old('infants') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  
                </div>
                 
                <div class="row">
                  <div class="form-group col-xs-3">
                      <label>Ăn NL <span class="red-star">*</span></label>
                      <select class="form-control" name="meals" id="meals">
                        @for($i = 0; $i <= 150; $i++)            
                        <option value="{{ $i }}" {{ old('meals') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Ăn TE <span class="red-star">*</span></label>
                      <select class="form-control" name="meals_te" id="meals_te">
                        @for($i = 0; $i <= 20; $i++)            
                        <option value="{{ $i }}" {{ old('meals_te') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Cáp NL <span class="red-star">*</span></label>
                      <select class="form-control" name="cap_nl" id="cap_nl">
                        @for($i = 0; $i <= 150; $i++)            
                        <option value="{{ $i }}" {{ old('cap_nl') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Cáp TE <span class="red-star">*</span></label>
                      <select class="form-control" name="cap_te" id="cap_te">
                        @for($i = 0; $i <= 20; $i++)            
                        <option value="{{ $i }}" {{ old('cap_nl') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>                  
                </div>
                        
                <div class="row">
                  <div class="form-group col-xs-6" >
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" autocomplete="off" name="total_price" id="total_price" value="{{ old('total_price') }}">
                  </div>                 
                  <div class="form-group col-xs-6">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_tien') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                </div>         
                <input type="hidden" name="status" value="1">                   
                <div class="form-group">
                  <label>Danh sách khách</label>
                  <textarea class="form-control" rows="6" name="danh_sach" id="danh_sach">{{ old('danh_sach') }}</textarea>
                </div>
                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="6" name="notes" id="notes">{{ old('notes') }}</textarea>
                </div>                  
            </div>          
                              
            <div class="box-footer">
              <button type="button" class="btn btn-default btn-sm" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i> Đang xử lý...</button>              
              <button type="submit" id="btnSave" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking.index')}}">Hủy</a>
            </div>
            
        </div>
        <!-- /.box -->     

      </div>
      
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<div id="tagTag" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    <form method="POST" action="{{ route('location.ajax-save')}}" id="formAjaxTag">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tạo mới điểm đón</h4>
      </div>
      <div class="modal-body" id="contentTag">
          <input type="hidden" name="type" value="1">
           <!-- text input -->
          <div class="col-md-12">
            <div class="form-group">
              <label>Tên địa điểm<span class="red-star">*</span></label>
              <input type="text" class="form-control" id="add_address" value="{{ old('address') }}" name="str_tag"></textarea>
            </div>
            
          </div>
          <div classs="clearfix"></div>
      </div>
      <div style="clear:both"></div>
      <div class="modal-footer" style="text-align:center">
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveTagAjax"> Lưu</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="btnCloseModalTag">Đóng</button>
      </div>
      </form>
    </div>

  </div>
</div>
@stop
@section('js')
<script type="text/javascript">
  var levelLogin = {{ Auth::user()->level }};
  console.log(levelLogin);
  $(document).on('click','#btnSave', function(){
    
    if(parseInt($('#tien_coc').val()) > 0 && $('#nguoi_thu_coc').val() == ''){
      alert('Bạn chưa chọn người thu cọc');
      return false;
    }
  });
$(document).on('click', '#btnSaveTagAjax', function(){ 
 
  $(this).attr('disabled', 'disabled');
    $.ajax({
      url : $('#formAjaxTag').attr('action'),
      data: $('#formAjaxTag').serialize(),
      type : "post", 
      success : function(str_id){          
        $('#btnCloseModalTag').click();
        $.ajax({
          url : "{{ route('location.ajax-list') }}",
          data: {
            str_id : str_id
          },
          type : "get", 
          success : function(data){
              $('#location_id').html(data);
              $('#location_id').select2('refresh');
              
          }
        });
      }
    });
 });
  $(document).ready(function(){
     $("#add_address").keyup(function(event) {
      console.log(event.keyCode);
      if (event.keyCode === 13) {
          $("#btnSaveTagAjax").click();
          return false;
      }
      
  });
    $('#content_alert').hide();
    $('#not_pay').change(function(){
        if($(this).is(':checked')){        
            $('#total_price').val(0).attr('disabled', 'disabled');            
        }else{            
             $('#total_price').val('').removeAttr('disabled');
        }
    });
    $('#dataForm').submit(function(){      
      $('#btnSave').hide();
      $('#btnLoading').show();
    });
    $('#tour_id').change(function(){
      location.href="{{ route('booking.create')}}?type=1&tour_id=" + $(this).val();
    });
    $('#btnAddTag').click(function(){
        $('#tagTag').modal('show');
    });
   
    $('#adults, #childs').change(function(){
      if($('#ko_cap_treo').prop('checked') == true){
        $('#cap_nl').val(0);
        $('#cap_te').val(0);
      }else{
        $('#cap_nl').val($('#adults').val());
        $('#cap_te').val($('#childs').val());
        
      }
      if($('#ko_an').prop('checked') == true){
        $('#meals').val(0);
        $('#meals_te').val(0);
      }else{
        $('#meals').val($('#adults').val());
        $('#meals_te').val($('#childs').val());        
      }
      
    });
    
    $('#ko_cap_treo').click(function(){
      var checked = $(this).prop('checked');
      if(checked == true){
        $('#cap_nl, #cap_te').val(0);
      }else{
        $('#cap_nl').val($('#adults').val());
        $('#cap_te').val($('#childs').val());        
      }   
           
    });
    $('#ko_an').click(function(){
      var checked = $(this).prop('checked');
      if(checked == true){
        $('#meals, #meals_te').val(0);
      }else{
        $('#meals').val($('#adults').val());
        $('#meals_te').val($('#childs').val());        
      }
          
    });
  });

</script>
@stop