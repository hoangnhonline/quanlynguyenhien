@extends('layout')
@section('content')
<div class="content-wrapper">
  
  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">  
      Đặt tour <span style="color:#f39c12">@if($tour_id == 4) CÂU MỰC @elseif($tour_id == 1) ĐẢO @elseif($tour_id == 3) RẠCH VẸM @endif
    </span></h1>    
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
    <form role="form" method="POST" action="{{ route('booking.store-short') }}" id="dataForm">
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
                 <div class="form-check col-xs-6">
                    <input type="checkbox" class="form-check-input" value="1" {{ old('don_ben') == 1 ? "checked" : "" }} id="don_ben" name="don_ben">
                    <label class="form-check-label" for="don_ben"  style="color: green; font-weight:bold">ĐÓN TẠI BẾN</label>
                </div>
                <div class="form-check col-xs-6">
                    <input type="checkbox" class="form-check-input" value="1" {{ old('not_pay') == 1 ? "checked" : "" }} id="not_pay" name="not_pay">
                    <label class="form-check-label" for="not_pay"  style="color: green; font-weight:bold">KHÔNG THU</label>
                </div>
              </div>
              <div class="row">
                  <div class="form-group col-md-6  col-xs-6">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="ko_cap_treo" name="ko_cap_treo" value="1" {{ old('ko_cap_treo') == 1 ? "checked" : "" }}>
                        KHÔNG CÁP
                      </label>
                  </div>
                  <div class="form-group col-md-6  col-xs-6">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="ko_an" name="ko_an" value="1" {{ old('ko_an') == 1 ? "checked" : "" }}>
                        KHÔNG ĂN
                      </label>
                  </div>
                </div>
              <div class="row">
                <input type="hidden" name="tour_id" value="1">
                <input type="hidden" name="tour_cate" value="2">
                <div class="form-group col-xs-12">
                   <label>Sales <span class="red-star">*</span></label>
                    <select class="form-control select2" name="user_id" id="user_id">
                      <option value="0">--Chọn--</option>
                      @foreach($listUser as $user)        
                      <option data-level="{{ $user->level }}" data-name="{{ $user->name }}" data-phone="{{ $user->phone }}" value="{{ $user->id }}" {{ old('user_id', $user_id_default) == $user->id ? "selected" : "" }}>{{ $user->name }} - {{ $user->phone }}</option>
                      @endforeach
                    </select>
                </div>
                 <div class="form-group col-md-4 col-xs-6">                    
                    <label>Ngày đi <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ old('use_date', $dateDefault) }}" autocomplete="off">
                  </div> 
                <div class="form-group col-md-4 col-xs-6">                  
                  <label>Hình thức <span class="red-star">*</span></label>
                  <select class="form-control" id="tour_type" name="tour_type">                      
                      <option value="1" {{ old('tour_type') == 1 ? "selected" : "" }}>Tour ghép</option>
                      <option value="2" {{ old('tour_type') == 2 ? "selected" : "" }}>Tour VIP</option>
                      <option value="3" {{ old('tour_type') == 3 ? "selected" : "" }}>Thuê cano</option>
                  </select>
                </div>
            
                
                <input type="hidden" name="ctv_id" value="333">
              
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
                 
                <div class="row" style="display: none;">
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
                  <div class="form-group col-xs-7" style="display: none;">
                      <label>Thành tiền <span class="red-star">*</span></label>
                      <input type="text" name="total_price_adult" id="total_price_adult" class="form-control number" value="{{ old('total_price_adult') }}">
                  </div>
                </div>
                        
                <div class="row">
                  <div class="form-group col-xs-6" >
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" autocomplete="off" name="total_price" id="total_price" value="{{ old('total_price') }}">
                  </div>
                  <div class="form-group col-md-6 col-xs-6" >
                      <label>HDV thu hộ <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="hdv_thu" id="hdv_thu" value="{{ old('hdv_thu') }}" style="border: 1px solid red">
                  </div>
                  <div class="form-group col-xs-3" style="display: none;" >
                      <label>TIỀN CỌC <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" autocomplete="off" name="tien_coc" id="tien_coc" value="{{ old('tien_coc') }}">
                  </div>
                  <div class="form-group col-md-3 col-xs-3"  style="display: none;">
                      <label>THỰC THU <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" autocomplete="off" name="tien_thuc_thu" id="tien_thuc_thu" value="{{ old('tien_thuc_thu') }}" style="border: 1px solid red">
                  </div>
                  <div class="form-group col-xs-3"  style="display: none;">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_tien') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                </div>
                <div class="row" style="display: none;">
                  <div class="form-group col-md-3 col-xs-6">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc') }}">
                  </div>
                  <div class="form-group col-md-3 col-xs-6">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_coc') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-md-3 col-xs-6" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai') }}">
                  </div>
                   
                </div>                
                <div class="form-group" style="display: none;">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control" name="status" id="status">                        
                        <option value="1" {{ old('status') == 1 ? "selected" : "" }}>Mới</option>
                        <option value="2" {{ old('status') == 2 ? "selected" : "" }}>Hoàn tất</option>
                        <option value="3" {{ old('status') == 3 ? "selected" : "" }}>Hủy</option>
                      </select>
                  </div>
                <div class="form-group" style="display: none;">
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
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking.index', ['type' => $type])}}">Hủy</a>
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
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveTagAjax"> Save</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="btnCloseModalTag">Close</button>
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
  $('#don_ben').change(function(){
        if($(this).is(':checked')){
            $("#location_id").val(2947).trigger('change');                        
        }else{
            $('#location_id').val('');
            
        }
    });
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
    $('#content_alert').hide();
    $('#not_pay').change(function(){
        if($(this).is(':checked')){
            //$('#div_total_price').hide();
            $('#total_price').val(0).attr('disabled', 'disabled');
            
        }else{
             //$('#div_total_price').show()
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
    $('#user_id').change(function(){
      if($('#don_ben').prop('checked') == true){
        var name = $("#user_id option:selected" ).data('name');
        var phone = $("#user_id option:selected" ).data('phone');
        $('#name').val(name);
        $('#phone').val(phone);
      }
    });
    $('#meals, #tien_coc, #discount, #extra_fee, #user_id').change(function(){
      var level = $("#user_id option:selected" ).data('level');
     
      //  setPrice();
      
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
      var level = $("#user_id option:selected" ).data('level');
      
      
       //setPrice();
      
    });
    $('#tien_coc').blur(function(){
      var level = $("#user_id option:selected" ).data('level');
    
       // setPrice();
      
      
    });
    $('#ko_cap_treo').click(function(){
      var checked = $(this).prop('checked');
      if(checked == true){
        $('#cap_nl, #cap_te').val(0);
      }else{
        $('#cap_nl').val($('#adults').val());
        $('#cap_te').val($('#childs').val());        
      }
      var level = $("#user_id option:selected" ).data('level');
      
       // setPrice();
           
    });
    $('#ko_an').click(function(){
      var checked = $(this).prop('checked');
      if(checked == true){
        $('#meals, #meals_te').val(0);
      }else{
        $('#meals').val($('#adults').val());
        $('#meals_te').val($('#childs').val());        
      }
      var level = $("#user_id option:selected" ).data('level');
   
       // setPrice();
          
    });
  });
  function setPrice(){
    return false;
    if($('#tour_type').val() == 3){
      priceThueCano();
    }else{      
      priceGhep();            
    }
  }
  function priceThueCano(){   
      var priceThue = function () {
        var adults = $('#adults').val();
        var price = null;
        $.ajax({
            'async': false,
            'type': "GET",
            'global': false,
            'dataType': 'html',
            'url': "{{ route('get-boat-prices') }}?no=" + adults,
            'data': { 'request': "", 'target': 'arrange_url', 'method': 'method_target' },
            'success': function (data) {
                price = data;
            }
        });
        return price;
    }();

      var adults = parseInt($('#adults').val());      
      var childs = parseInt($('#childs').val());
      var total_price_child = 0;
      var meals_plus = 0;
      if(childs > 0){
        var meals = $('#meals').val();
        
          if( meals > 0 ){           
            total_price_child = 150000*childs;
          }else{
            total_price_child = 50000*childs;
          }

      }   
      //cal price adult
      var total_price_adult = parseInt(priceThue);
      $('#total_price_child').val(total_price_child);
      $('#total_price_adult').val(total_price_adult);
      //phu thu
      var extra_fee = 0;
      if($('#extra_fee').val() != ''){
       extra_fee = parseInt($('#extra_fee').val());
      }
      //giam gia 
      var discount = 0;
      if($('#discount').val() != ''){
       discount = parseInt($('#discount').val());
      }    
      //tien_coc
      var tien_coc = 0;
      if($('#tien_coc').val() != ''){
       tien_coc = parseInt($('#tien_coc').val());
      }        
      //tien an
      var tien_an = parseInt($('#meals').val())*220000;
      var total_price = total_price_adult + total_price_child + extra_fee - discount + tien_an;    
      $('#total_price').val(total_price);

      $('#con_lai').val(total_price - tien_coc);
  }
  
  function priceGhep(){
    var cap_treo_lon = 390000;
    var cap_treo_nho = 255000;
    var meals_price = 220000;
    var meals_price_child = 110000;
    var adults = parseInt($('#adults').val());      
    var childs = parseInt($('#childs').val());

    var ko_cap = $('#ko_cap_treo').is(':checked');
    var ko_an = $('#ko_an').is(':checked');
    
    var price = 200000;
    var price_child = 100000;
 
    if(ko_cap != true){
      price += cap_treo_lon;
      price_child += cap_treo_nho;
    }
    if(ko_an != true){
      price += meals_price;
      price_child += meals_price_child;
    }
    var total_price = adults*price + childs*price_child;
    $('#total_price').val(total_price);
  }
</script>
@stop