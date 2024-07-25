@extends('layout')
@section('content')
<div class="content-wrapper">
 
    
  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">  
      Đặt tour : cập nhật <span style="color: red">PTT{{ $detail->id }}</span>
    </h1>    
  </section>

  <!-- Main content -->
  <section class="content">
    @if(isset($keyword))
    <a class="btn btn-default btn-sm" href="{{ route('booking.index', ['type' => $detail->type]) }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking.index', ['type' => $detail->type]) }}" style="margin-bottom:5px">Xem danh sách booking</a>
    <a href="{{ route( 'booking-payment.index', ['booking_id' => $detail->id] ) }}" class="btn btn-danger btn-sm" style="margin-bottom:5px">Lịch sử thanh toán</a>
    @else
    <a class="btn btn-default btn-sm" href="{{ route('booking.index', $arrSearch) }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking.index', $arrSearch) }}" style="margin-bottom:5px">Xem danh sách booking</a>
    <a href="{{ route( 'booking-payment.index', ['booking_id' => $detail->id] ) }}" class="btn btn-danger btn-sm" style="margin-bottom:5px">Lịch sử thanh toán</a>
    @endif
    <form role="form" method="POST" action="{{ route('booking.update') }}" id="dataForm">
      <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="row">
    
      <!-- left column -->

      <div class="col-md-12">      
        <!-- general form elements -->
        <div class="box box-primary">
          <input type="hidden" name="ngay_coc">
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
              @if($detail->payment->count() > 0)
              <fieldset class="scheduler-border">
                  <legend class="scheduler-border">THANH TOÁN</legend>
                  <div class="control-group">
                      <table class="table table-bordered table-responsive" style="margin-bottom: 0px;">
                        @foreach($detail->payment as $p) 
                        <tr>                  
                          <td>
                            @if($p->type == 1)
                            <img src="{{ Helper::showImageNew($p->image_url)}}" width="60" style="border: 1px solid red" class="img-unc" >
                            @else
                            + {{$p->notes}}<br>
                            @endif
                          </td>
                        </tr>
                        @endforeach
                      </table>
                  </div>
              </fieldset>
              @endif
                
                  
              </div>    
              <table class="table table-bordered">
                <tr>
                  <th width="200px">CODE</th>
                  <td>PTT{{ $detail->id }} - @if($detail->user)
                  {{ $detail->user->name }}
                  @else
                    {{ $detail->user_id }}
                  @endif
                    @if($detail->status == 1)
                    <span class="label label-info">MỚI</span>
                    @elseif($detail->status == 2)
                    <span class="label label-default">HOÀN TẤT</span>
                  
                    @elseif($detail->status == 3)
                    <span class="label label-danger">HỦY</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th width="200px">Tên</th>
                  <td>{{ $detail->name }} - {{ date('d/m/Y', strtotime($detail->use_date)) }}    </td>
                </tr>
                <tr>
                  <th width="200px">Số người</th>
                  <td>{{ $detail->adults }} NL @if($detail->childs > 0 )- {{ $detail->childs }} TE @endif</td>
                </tr>
                <tr>
                  <th width="200px">Cáp treo</th>
                  <td>
                    @if($detail->ko_cap_treo)
                    KHÔNG CÁP TREO
                    @else
                    {{ $detail->adults }} vé lớn - {{ $detail->childs }} vé nhỏ
                    @endif
                  </td>
                </tr>
                <tr>
                  <th width="200px">Phần ăn</th>
                  <td>
                    @if($detail->meals == 0)
                    KHÔNG ĂN TRƯA
                    @else
                    {{ $detail->meals }}
                    @endif
                  </td>
                </tr>
              </table>      
              <input type="hidden" name="type" value="1">
              <input type="hidden" name="tour_type" value="{{ $detail->tour_type }}">
              <input type="hidden" name="tour_id" value="{{ $detail->tour_id }}">
              <input type="hidden" name="name" value="{{ $detail->name }}">
              <input type="hidden" name="phone" value="{{ $detail->phone }}">
              <input type="hidden" name="address" value="{{ $detail->address }}">
              <input type="hidden" name="location_id" value="{{ $detail->location_id }}">
              <input type="hidden" name="address" value="{{ $detail->address }}">
              <input type="hidden" name="status" value="{{ $detail->status }}">
              <input type="hidden" name="total_price_adult" value="{{ $detail->total_price_adult }}">
              <input type="hidden" name="total_price_child" value="{{ $detail->total_price_child }}">              
                <input type="hidden" name="use_date" value="{{ date('d/m/Y', strtotime($detail->use_date)) }}">
                <input type="hidden" name="user_id" value="{{ $detail->user_id }}">
                <input type="hidden" name="book_date" value="{{ date('d/m/Y', strtotime($detail->book_date)) }}">
                <input type="hidden" name="hoa_hong_cty" value="{{ $detail->hoa_hong_cty }}">
                <div style="font-size: 20px; margin-top: 10px;">
                  @if($detail->user->level != 1)
                  <table class="table table-bordered" style="color: #008d4c">
                    <tr>
                      <td>Giá đón bến: 
                        700.000 VNĐ
                      </td>                   
                      <td>Giá đón trung tâm: 
                        800.000 VNĐ
                      </td>
                    </tr>
                  </table>
                  @endif
                </div>
                <div class="row">                 
                  
                  <div class="form-group col-xs-12">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control" name="status" id="status">                        
                        <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>Mới</option>
                        <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Hoàn tất</option>
                        <option value="3" {{ old('status', $detail->status) == 3 ? "selected" : "" }}>Hủy</option>
                      </select>
                  </div>
                </div>
                <div class="row">                 
                  <div class="form-group col-xs-5" >
                      <label>Hoa hồng sales</label>
                      <input type="text" name="hoa_hong_sales" id="hoa_hong_sales" class="form-control number" value="{{ old('hoa_hong_sales', $detail->hoa_hong_sales) }}">
                  </div>
                  <div class="col-xs-7 input-group"> 
                    @if($detail->location_id != 2958)
                    {{ $detail->address }}
                    @endif                 
                    <label>Nơi đón <span class="red-star">*</span></label>

                    <select class="form-control select2" name="location_id" id="location_id">
                      <option value="">--Chọn--</option>
                      @foreach($listTag as $location)        
                      <option value="{{ $location->id }}" {{ old('location_id',$detail->location_id) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                      @endforeach
                    </select>
                    <span class="input-group-btn">
                      <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddTag" type="button" data-value="3">
                        Thêm địa điểm
                      </button>
                    </span>
                  </div> 
                </div>

                <div class="row">
                  <div class="form-group col-md-12">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="ko_cap_treo" name="ko_cap_treo" value="1" {{ old('ko_cap_treo', $detail->ko_cap_treo) == 1 ? "checked" : "" }}>
                        KHÔNG ĐI CÁP TREO
                      </label>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-3">
                      <label>Người lớn</label>
                      <select class="form-control" name="adults" id="adults">
                        @for($i = 1; $i <= 100; $i++)            
                        <option value="{{ $i }}" {{ old('adults', $detail->adults) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Phần ăn</label>
                      <select class="form-control" name="meals" id="meals">
                        @for($i = 0; $i <= 100; $i++)            
                        <option value="{{ $i }}" {{ old('meals', $detail->meals) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Trẻ em<span class="red-star">*</span></label>
                      <select class="form-control" name="childs" id="childs">
                        @for($i = 0; $i <= 20; $i++)            
                        <option value="{{ $i }}" {{ old('childs', $detail->childs) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Em bé</label>
                      <select class="form-control" name="infants" id="infants">
                        @for($i = 0; $i <= 20; $i++)            
                        <option value="{{ $i }}" {{ old('infants', $detail->infants) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price', $detail->total_price) }}">
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Phụ thu đón</label>
                      <input type="text" name="extra_fee" id="extra_fee" class="form-control number" value="{{ old('extra_fee', $detail->extra_fee) }}">
                  </div>
                  <div class="form-group col-xs-4" >
                      <label>Giảm giá</label>
                      <input type="text" name="discount" id="discount" class="form-control number" value="{{ old('discount', $detail->discount) }}">
                  </div>                  
                </div>
                <div class="row">
                  <div class="form-group col-xs-6">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc', $detail->tien_coc) }}">
                  </div> 
                  <div class="form-group col-xs-6" style="padding-right: 0px">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_coc', $detail->nguoi_thu_coc) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                </div> 
                <div class="row">
                  <div class="form-group col-xs-4" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai', $detail->con_lai) }}">
                  </div>
                  <?php 
                 // $tien_thuc_thu = $detail->tien_thuc_thu == null ? $detail->con_lai : $detail->tien_thuc_thu;
                  ?>
                  <div class="form-group col-xs-4" >
                      <label>THỰC THU <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="tien_thuc_thu" id="tien_thuc_thu" value="{{ old('tien_thuc_thu', $detail->tien_thuc_thu) }}" style="border: 1px solid red">
                  </div>               
                  <div class="form-group col-xs-4">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien" style="border: 1px solid red">
                        <option value="">--Chọn--</option>                       
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach                        
                      </select>
                  </div>
                </div> 
                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="6" name="notes" id="notes">{{ old('notes', $detail->notes) }}</textarea>
                </div>     
            </div>         
            <div class="box-footer">
              <button type="button" class="btn btn-default btn-sm" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i> Đang xử lý...</button> 
              <button type="submit" id="btnSave" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking.index', $arrSearch)}}">Hủy</a>
            </div>
            
        </div>
        <!-- /.box -->     

      </div>
      
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>

<div class="modal fade" id="uncModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header">        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="unc_img" style="width: 100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
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
<style type="text/css">
  .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td{
    border: 1px solid #ccc !important;
  }
</style>
@stop
@section('js')
<script type="text/javascript">
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
    $('#dataForm').submit(function(){
      $('#btnSave').hide();
      $('#btnLoading').show();
    });
     $('#btnAddTag').click(function(){
          $('#tagTag').modal('show');
      });  

  	@if($detail->status == 3 && Auth::user()->id > 1)
  	 $('#dataForm input, #dataForm select, #dataForm textarea').attr('disabled', 'disabled');
  	@endif
    $('#adults, #childs, #meals, #tien_coc, #discount, #extra_fee').change(function(){
    //  setPrice();
    });
    $('#tien_coc').blur(function(){
     // setPrice();
    });
    $('#ko_cap_treo').click(function(){
      //setPrice();
    });
  });
  function setPrice(){
    if($('#tour_type').val() == 3){
      priceThueCano();
    }else{
      priceGhep();
    }    
  }

  function priceGhep(){    
    var tour_id = $('#tour_id').val();
    if(tour_id == 3){
      var tour_price = 790000;
      var adults = parseInt($('#adults').val());      
      var childs = parseInt($('#childs').val());
      var total_price_child = 0;
      

      if(childs > 0){
        var meals = $('#meals').val();
        if( meals > 0 ){
                  
          total_price_child = 200000*childs;
        }else{
          total_price_child = 100000*childs;
        }
      }   
      
      console.log('tien tre em: ', total_price_child);
      //cal price adult
      var total_price_adult = adults*tour_price;
      $('#total_price_child').val(total_price_child);
      $('#total_price_adult').val(total_price_adult);
      console.log('tien nguoi lon: ', total_price_adult);
      //phu thu
      var extra_fee = 0;
      if($('#extra_fee').val() != ''){
       extra_fee = parseInt($('#extra_fee').val());
      }
      console.log('phu thu: ', extra_fee);
      //giam gia 
      var discount = 0;
      if($('#discount').val() != ''){
       discount = parseInt($('#discount').val());
      }    
      console.log('giam gia: ', discount);
      //tien_coc
      var tien_coc = 0;
      if($('#tien_coc').val() != ''){
       tien_coc = parseInt($('#tien_coc').val());
      }        
      //tien an
      var tien_an = parseInt($('#meals').val())*200000;
      console.log('tien an: ', tien_an);
      var total_price = total_price_adult + total_price_child + extra_fee - discount + tien_an;    
      console.log('total_price: ', total_price);
      $('#total_price').val(total_price);

      $('#con_lai').val(total_price - tien_coc);
    }else{
      var ko_cap = $('#ko_cap_treo').is(':checked');
      var adults = parseInt($('#adults').val());      
      var childs = parseInt($('#childs').val());
      var total_price_child = 0;
      var meals_plus = 0;
      

      if(ko_cap == true){
        var tour_price = 500000;
        var tour_price_child = 250000;
      }else{
        var tour_price = 800000;
        var tour_price_child = 400000;
      } 
      
      var adults = parseInt($('#adults').val());      
      var childs = parseInt($('#childs').val());
      var total_price_child = 0;
      var meals_plus = 0;
      if(childs > 0){
        var meals = $('#meals').val();
        
        if( meals > 0 ){           
          total_price_child = (tour_price_child+100000)*childs;
        }else{
          total_price_child = tour_price_child*childs;
        }
          // ko cap treo
        
      }  
      //cal price adult
      var total_price_adult = adults*tour_price;
      $('#total_price_child').val(total_price_child);
      $('#total_price_adult').val(total_price_adult);
      console.log('tien nguoi lon: ', total_price_adult);
      //phu thu
      var extra_fee = 0;
      if($('#extra_fee').val() != ''){
       extra_fee = parseInt($('#extra_fee').val());
      }
      console.log('phu thu: ', extra_fee);
      //giam gia 
      var discount = 0;
      if($('#discount').val() != ''){
       discount = parseInt($('#discount').val());
      }    
      console.log('giam gia: ', discount);
      //tien_coc
      var tien_coc = 0;
      if($('#tien_coc').val() != ''){
       tien_coc = parseInt($('#tien_coc').val());
      }        
      //tien an
      var tien_an = parseInt($('#meals').val())*200000;
      console.log('tien an: ', tien_an);
      var total_price = total_price_adult + total_price_child + extra_fee - discount + tien_an;    
      console.log('total_price: ', total_price);
      $('#total_price').val(total_price);

      $('#con_lai').val(total_price - tien_coc);
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
      var tien_an = parseInt($('#meals').val())*200000;
      var total_price = total_price_adult + total_price_child + extra_fee - discount + tien_an;    
      $('#total_price').val(total_price);

      $('#con_lai').val(total_price - tien_coc);
  }
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    }); 
  });
</script>
@stop