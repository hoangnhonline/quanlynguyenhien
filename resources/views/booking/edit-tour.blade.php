@extends('layout')
@section('content')

<div class="content-wrapper">


  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1 style="text-transform: uppercase;">
      Đặt tour : cập nhật <span style="color: red">NH{{ $detail->id }}</span>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
  

    <a class="btn btn-default btn-sm" href="{{ route('booking.index', $arrSearch) }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking.index', $arrSearch) }}" style="margin-bottom:5px">Xem danh sách booking</a>    
 
    <form role="form" method="POST" action="{{ route('booking.update') }}" id="dataForm">
      <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="row">

      <!-- left column -->

      <div class="col-md-12">
        <div id="content_alert"></div>
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
               
              </div>             
              <div class="row">
                <?php 
                $not_pay = 0;
                if($detail->nguoi_thu_tien == 3){ // cong no
                  $not_pay = 1;
                }
                ?>
                <div class="form-check col-xs-4 col-md-4">
                    <input type="checkbox" class="form-check-input" value="1" {{ old('not_pay', $not_pay) == 1 ? "checked" : "" }} id="not_pay" name="not_pay">
                    <label class="form-check-label" for="not_pay"  style="color: green; font-weight:bold">KHÔNG THU</label>
                </div>
                  <div class="form-group col-md-4  col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="ko_cap_treo" name="ko_cap_treo" value="1" {{ old('ko_cap_treo', $detail->ko_cap_treo) == 1 ? "checked" : "" }}>
                        KHÔNG CÁP
                      </label>
                  </div>
                  <?php 
                  $ko_an = 0;
                  if($detail->meals == 0 && $detail->meals_te == 0){
                    $ko_an = 1;
                  }
                  ?>
                  <div class="form-group col-md-4  col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="ko_an" name="ko_an" value="1" {{ old('ko_an', $ko_an) == 1 ? "checked" : "" }}>
                        KHÔNG ĂN
                      </label>
                  </div>
                </div>            
            
              <div class="row">
                 
                  <div class="form-group col-md-4 col-xs-12">
                     <label>Đối tác <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="0">--Chọn--</option>
                        @foreach($listUser as $user)
                        <option data-level="{{ $user->level }}" value="{{ $user->id }}" {{ old('user_id', $detail->user_id) == $user->id ? "selected" : "" }}>{{ $user->name }} - {{ Helper::getLevel($user->level) }}</option>
                        @endforeach
                      </select>
                  </div>
                  @php
                    if($detail->use_date){
                        $use_date = old('use_date', date('d/m/Y', strtotime($detail->use_date)));
                    }else{
                        $use_date = old('use_date');
                    }
                  @endphp

                  <div class="form-group col-md-4 col-xs-6">
                    <label>Ngày đi <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ $use_date }}" autocomplete="off">
                  </div>
                  <div class="form-group col-md-4 col-xs-6">
                  <label>Hình thức <span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_type" name="tour_type">
                      <option value="1" {{ old('tour_type', $detail->tour_type) == 1 ? "selected" : "" }}>Tour ghép</option>
                      <option value="2" {{ old('tour_type', $detail->tour_type) == 2 ? "selected" : "" }}>Tour VIP</option>
                      <option value="3" {{ old('tour_type', $detail->tour_type) == 3 ? "selected" : "" }}>Thuê cano</option>
                  </select>
                </div>
                 
                
                 
                </div>  <!--row-->
              <div class="row">
               
                 
               
                </div><!--row -->
                <div class="row">
                  <div class="form-group col-md-4 col-xs-6">
                    <label>Tên khách hàng <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $detail->name) }}">
                  </div>
                  <div class="form-group col-md-4 col-xs-6">
                    <label>Điện thoại <span class="red-star">*</span></label>
                    <input type="text" maxlength="20" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                  </div>  
                  <div class="col-xs-12 col-md-4  input-group" style="padding-right:15px;padding-left: 15px; margin-bottom: 15px;">                   
                    <label>Nơi đón <span class="red-star">*</span></label>

                    <select class="form-control select2" name="location_id" id="location_id">
                      <option value="">--Chọn--</option>
                      @foreach($listTag as $location)
                      <option value="{{ $location->id }}" {{ old('location_id',$detail->location_id) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                      @endforeach
                    </select>
                    <span class="input-group-btn">
                      <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddTag" type="button" data-value="3">
                        Thêm
                      </button>
                    </span>
                  </div>                
                </div>
                

                <div class="row">
                  <div class="form-group col-xs-4">
                      <label>NL <span class="red-star">*</span></label>
                      <select class="form-control select2" name="adults" id="adults">
                        @for($i = 1; $i <= 150; $i++)
                        <option value="{{ $i }}" {{ old('adults', $detail->adults) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>TE <span class="red-star">*</span></label>
                      <select class="form-control select2" name="childs" id="childs">
                        @for($i = 0; $i <= 20; $i++)
                        <option value="{{ $i }}" {{ old('childs', $detail->childs) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>EB(dưới 1m)</label>
                      <select class="form-control select2" name="infants" id="infants">
                        @for($i = 0; $i <= 20; $i++)
                        <option value="{{ $i }}" {{ old('infants', $detail->infants) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>

                </div>
                
                <div class="row">
                  <div class="form-group col-xs-3">
                      <label>Ăn NL <span class="red-star">*</span></label>
                      <select class="form-control" name="meals" id="meals">
                        @for($i = 0; $i <= 150; $i++)            
                        <option value="{{ $i }}" {{ old('meals', $detail->meals) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Ăn TE <span class="red-star">*</span></label>
                      <select class="form-control" name="meals_te" id="meals_te">
                        @for($i = 0; $i <= 20; $i++)            
                        <option value="{{ $i }}" {{ old('meals_te', $detail->meals_te) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Cáp NL <span class="red-star">*</span></label>
                      <select class="form-control" name="cap_nl" id="cap_nl">
                        @for($i = 0; $i <= 150; $i++)            
                        <option value="{{ $i }}" {{ old('cap_nl', $detail->cap_nl) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Cáp TE <span class="red-star">*</span></label>
                      <select class="form-control" name="cap_te" id="cap_te">
                        @for($i = 0; $i <= 20; $i++)            
                        <option value="{{ $i }}" {{ old('cap_te', $detail->cap_te) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>                  
                </div>
                <div class="form-group">
                  <label>Danh sách khách</label>
                  <textarea class="form-control" rows="6" name="danh_sach" id="danh_sach">{{ old('danh_sach', $detail->danh_sach) }}</textarea>
                </div>
                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="6" name="notes" id="notes">{{ old('notes', $detail->notes) }}</textarea>
                </div>
            </div>

            <div class="box-footer">
              <button type="button" class="btn btn-default btn-sm" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i> Đang xử lý...</button>
              <button type="submit" id="btnSaves" class="btn btn-primary btn-sm" onclick="return checkToday();">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking.index', $arrSearch)}}">Hủy</a>
            </div>

        </div>
        <!-- /.box -->
      </div>
    </div>
    </form>
    
    
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
@stop
@section('js')
<script type="text/javascript">
  function checkToday(){
      $('.ngay-don-tien').each(function(){
        var date = $(this).val();
        if(date != ''){
          var tmpArrDate = date.split("/");
          // Create date from input value
          var inputDate = new Date(tmpArrDate[1] + "/" + tmpArrDate[0] + "/" + tmpArrDate[2]);
          console.log(tmpArrDate);
          // Get today's date
          var todaysDate = new Date();

          // call setHours to take the time out of the comparison
          if(inputDate.setHours(0,0,0,0) == todaysDate.setHours(0,0,0,0)) {
              alert('Anh/chị đang đặt cuốc xe TRONG NGÀY nên cần BẮT BUỘC thông báo cho điều hành xe theo số 0911380111 để đảm bảo.');
          }
          return false;
        }

      });
      return true;
    }
  var levelLogin = {{ Auth::user()->level }};
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
  	 //$('#dataForm input, #dataForm select, #dataForm textarea').attr('disabled', 'disabled');
  	@endif
    $('#meals, #tien_coc, #discount, #extra_fee, #user_id').change(function(){
      var level = $("#user_id option:selected" ).data('level');
      console.log(level);
      if(level == 1 || levelLogin  == 1){
        setPrice();
      }
    });
    $('#adults, #childs').change(function(){
      if($('#ko_cap_treo').prop('checked') == true){
        $('#cap_nl, #cap_te').val(0);
      }else{
        $('#cap_nl').val($('#adults').val());
        $('#cap_te').val($('#childs').val());
      }
      var level = $("#user_id option:selected" ).data('level');
      console.log(level);
      if(level == 1 || levelLogin  == 1){
        setPrice();
      }
    });
    $('#tien_coc').blur(function(){
      var level = $("#user_id option:selected" ).data('level');
      if(level == 1 || levelLogin  == 1){
        setPrice();
      }
    });
    $('#ko_cap_treo').click(function(){
      var checked = $(this).prop('checked');
      var checked = $(this).prop('checked');
      if(checked == true){
        $('#cap_nl, #cap_te').val(0);
      }else{
        $('#cap_nl').val($('#adults').val());
        $('#cap_te').val($('#childs').val());
      }
      var level = $("#user_id option:selected" ).data('level');
      if(level == 1 || levelLogin  == 1){
        setPrice();
      }
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
    var cap_treo_lon = 400000;
    var cap_treo_nho = 260000;
    if(tour_id == 3){
      var tour_price = 570000;
      var adults = parseInt($('#adults').val());
      var childs = parseInt($('#childs').val());
      var total_price_child = 0;


      if(childs > 0){
        var meals = $('#meals').val();
        if( meals > 0 ){

          total_price_child = 180000*childs;
        }else{
          total_price_child = 90000*childs;
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
      var tien_an = parseInt($('#meals').val())*180000;
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
      var tour_price = 980000;
      var tour_price_child = 490000;

      if(ko_cap == true){
        var tour_price = tour_price - cap_treo_lon;
        var tour_price_child = tour_price_child - cap_treo_nho;
      }

      var adults = parseInt($('#adults').val());
      var childs = parseInt($('#childs').val());
      var total_price_child = 0;
      var meals_plus = 0;
      if(childs > 0){
        var meals = $('#meals').val();

        if( meals > 0 ){
          total_price_child = (tour_price_child+110000)*childs;
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
      var tien_an = parseInt($('#meals').val())*220000;
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
      var tien_an = parseInt($('#meals').val())*220000;
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
