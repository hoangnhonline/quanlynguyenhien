@extends('layout')
@section('content')
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      Đặt tour
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking.index', ['type' => $type]) }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking.index', ['type' => $type]) }}" style="margin-bottom:5px">Xem danh sách booking</a>


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
                <div class="form-group col-md-6">
                  <label>Loại tour<span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_id" name="tour_id">
                  <option value="">--Tất cả--</option>
                  @foreach($tourList as $tour)
                  @if($tour->city_id == 2)
                  <option value="{{ $tour->id }}" {{ old('tour_id') == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                  @endif
                  @endforeach
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label>Hình thức <span class="red-star">*</span></label>
                  <select class="form-control" id="tour_type" name="tour_type">
                      <option value="1" {{ old('tour_type') == 1 ? "selected" : "" }}>Tour ghép</option>
                      <option value="2" {{ old('tour_type') == 2 ? "selected" : "" }}>Tour VIP</option>
                  </select>
                </div>
                </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label>Tên khách hàng <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                </div>
                <div class="form-group col-md-3">
                  <label>Điện thoại <span class="red-star">*</span></label>
                  <input type="text" maxlength="20" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">
                </div>
                <div class="form-group col-md-3">
                  <label>Điện thoại 2</label>
                  <input type="text"  maxlength="20" class="form-control" name="phone_1" id="phone_1" value="{{ old('phone_1') }}">
                </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-6" >
                    <label>Ngày đi <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ old('use_date') }}" autocomplete="off">
                  </div>
                  <div class="col-xs-5 input-group" style="margin-top: 4px; padding-left: 15px;">
                  <label>Nơi đón <span class="red-star">*</span></label>

                  <select class="form-control select2" name="location_id" id="location_id">
                    <option value="">--Chọn--</option>
                    @foreach($listTag as $location)
                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                    @endforeach
                  </select>
                  <span class="input-group-btn">
                    <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddTag" type="button" data-value="3">
                      Thêm mới
                    </button>
                  </span>
                </div>
                </div>
                @if(Auth::user()->is_tour == 0)
                <div class="form-group">
                  <label>Facebook</label>
                  <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook') }}">
                </div>
                @endif
                <div class="row">
                  <div class="form-group col-md-12">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="don_cang" name="don_cang" value="1" {{ old('don_cang') == 1 ? "checked" : "" }}>
                        ĐÓN TẠI CẢNG
                      </label>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-4" >
                      <label>Người lớn <span class="red-star">*</span></label>
                      <select class="form-control select2" name="adults" id="adults">
                        @for($i = 1; $i <= 100; $i++)
                        <option value="{{ $i }}" {{ old('adults') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Trẻ em ( 1m - 1m4 ) <span class="red-star">*</span></label>
                      <select class="form-control" name="childs" id="childs">
                        @for($i = 0; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('childs') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Em bé (miễn phí)</label>
                      <select class="form-control" name="infants" id="infants">
                        @for($i = 0; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('infants') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-6" style="display: none;">
                      <label>Thành tiền NL<span class="red-star">*</span></label>
                      <input type="text" name="total_price_adult" id="total_price_adult" class="form-control number" value="{{ old('total_price_adult') }}">
                  </div>
                </div>
                <div class="row">

                  <input type="hidden" name="ngay_coc">
                  <div class="form-group col-xs-4"  style="display: none;">
                      <label>Thành tiền TE<span class="red-star">*</span></label>
                      <input type="text" name="total_price_child" id="total_price_child" class="form-control number" value="{{ old('total_price_child') }}">
                  </div>

                </div>

                <div class="row">
                  <div class="form-group col-xs-6">
                      <label>Phụ thu</label>
                      <input type="text" name="extra_fee" id="extra_fee" class="form-control number" value="{{ old('extra_fee') }}">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Giảm giá</label>
                      <input type="text" name="discount" id="discount" class="form-control number" value="{{ old('discount') }}">
                  </div>
                </div>
                <input type="hidden" name="city_id" value="2">
                <div class="row">
                  <div class="form-group col-xs-6">
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price') }}">
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
                <div class="row">
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc') }}">
                  </div>
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_coc') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-xs-4" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai') }}">
                  </div>
                </div>
                <div class="row">
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <div class="form-group col-xs-5" style="padding-right: 0px">
                     <label>Sales <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="0">--Chọn--</option>
                        @foreach($listUser as $user)
                        @if($user->city_id == 2)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                        @endif
                        @endforeach
                      </select>
                  </div>
                  @else
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  @endif
                  <input type="hidden" name="book_date" value="">
                </div>
                <div class="form-group" style="display: none;">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control" name="status" id="status">
                        <option value="1" {{ old('status') == 1 ? "selected" : "" }}>Mới</option>
                        <option value="2" {{ old('status') == 2 ? "selected" : "" }}>Hoàn tất</option>
                        <option value="3" {{ old('status') == 3 ? "selected" : "" }}>Hủy</option>
                      </select>
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

    $('#adults, #childs, #meals, #tien_coc, #discount, #extra_fee').change(function(){
      setPrice();
    });
    $('#tien_coc').blur(function(){
      setPrice();
    });
    $('#ko_cap_treo').click(function(){
      setPrice();
    });
  });
  function setPrice(){
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
      var tien_an = parseInt($('#meals').val())*200000;
      var total_price = total_price_adult + total_price_child + extra_fee - discount + tien_an;
      $('#total_price').val(total_price);

      $('#con_lai').val(total_price - tien_coc);
  }

  function priceGhep(){
    var cap_treo_lon = 300000;
    var cap_treo_nho = 215000;
    var tour_id = $('#tour_id').val();

    var ko_cap = $('#ko_cap_treo').is(':checked');
    console.log(ko_cap);
    if(tour_id == 3){ // RẠCH VẸM
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
      if(ko_cap == true){
        var tour_price = 600000;
      }else{
        var tour_price = 900000;
      }

      var adults = parseInt($('#adults').val());
      var childs = parseInt($('#childs').val());
      var total_price_child = 0;
      var meals_plus = 0;
      if(childs > 0){
        var meals = $('#meals').val();
        if(ko_cap == true){
          if( meals > 0 ){
            total_price_child = 335000*childs;
          }else{
            total_price_child = 235000*childs;
          }
          // ko cap treo
        }else{
          if( meals > 0 ){

            total_price_child = 550000*childs;
          }else{
            total_price_child = 450000*childs;
          }
        }// co cap treo

      }
      //cal price adult
      var total_price_adult = adults*tour_price;
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
  }
</script>
@stop
