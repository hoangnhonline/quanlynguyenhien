@extends('layout')
@section('content')
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      THUÊ XE MÁY
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking-xe-may.index') }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking-xe-may.index') }}" style="margin-bottom:5px">Xem danh sách booking</a>
    <a href="{{ route('partner.create', ['cost_type_id' => 53]) }}" target="_blank" class="btn btn-primary btn-sm" style="margin-bottom:5px">Thêm nhà xe</a>

    <form role="form" method="POST" action="{{ route('booking-xe-may.store') }}" id="dataForm">
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
              <input type="hidden" name="type" value="4">
              <input type="hidden" name="tour_cate" value="3">

              <div class="row">
                <div class="form-group col-md-6" >
                   <label>Nhà xe</label>
                  <select class="form-control select2" id="driver_id" name="driver_id">
                    <option value="">--Chọn--</option>
                      @foreach($nhaxeList as $driver)
                      <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id  ? "selected" : "" }}>{{ $driver->name }}
                        @if($driver->is_verify == 1)
                        - HĐ
                        @endif
                      </option>
                      @endforeach
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label>Loại xe<span class="red-star">*</span></label>
                  <select class="form-control select2" id="car_cate_id" name="car_cate_id">
                    <option value="">--Chọn--</option>
                      @foreach($cateList as $cate)
                      <option value="{{ $cate->id }}" {{ old('car_cate_id') == $cate->id  ? "selected" : "" }}>{{ $cate->name }}</option>
                      @endforeach
                  </select>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Số ngày thuê <span class="red-star">*</span></label>
                      <select class="form-control select2" name="adults" id="adults">
                        @for($i = 1; $i <= 100; $i++)
                        <option value="{{ $i }}" {{ old('adults') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                </div>
                <div class="form-group col-md-4" >
                    <label>Ngày giao xe<span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ old('use_date') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-md-4" >
                    <label>Giờ giao <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="time_pickup" id="time_pickup" value="{{ old('time_pickup') }}" autocomplete="off">
                  </div>
                </div>

              <div class="row">
                <div class="form-group col-md-4 col-xs-4"  style="padding-right: 0px">
                  <label>Tên KH <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                </div>
                <div class="form-group col-md-4 col-xs-4"  style="padding-right: 0px">
                  <label>Điện thoại <span class="red-star">*</span></label>
                  <input type="text" maxlength="20" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">
                </div>
                <div class="form-group col-md-4 col-xs-4">
                  <label>Điện thoại 2</label>
                  <input type="text"  maxlength="20" class="form-control" name="phone_1" id="phone_1" value="{{ old('phone_1') }}">
                </div>
                </div>
                <div class="row">

                  <div class="input-group col-md-12" style="padding-left: 15px; padding-right: 15px; margin-bottom: 15px;">
                  <label>Nơi giao <span class="red-star">*</span></label>

                  <select class="form-control select2" name="location_id" id="location_id">
                    <option value="">--Chọn--</option>
                    @foreach($listTag as $location)
                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                    @endforeach
                  </select>
                  <span class="input-group-btn">
                    <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddTag" type="button" data-value="3">
                      Thêm
                    </button>
                  </span>
                </div>
                <div class="input-group col-md-12" style="padding-left: 15px; padding-right: 15px; margin-bottom: 15px;">
                  <label>Nơi trả <span class="red-star">*</span></label>

                  <select class="form-control select2" name="location_id_2" id="location_id_2">
                    <option value="">--Chọn--</option>
                    @foreach($listTag as $location)
                    <option value="{{ $location->id }}" {{ old('location_id_2') == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                    @endforeach
                  </select>
                  <span class="input-group-btn">
                    <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddTag2" type="button" data-value="3">
                      Thêm
                    </button>
                  </span>
                </div>
                </div>

                <input type="hidden" name="total_price_adult">
                <input type="hidden" name="total_price_child">
                <input type="hidden" name="meals">
                <input type="hidden" name="ngay_coc">
                <input type="hidden" name="extra_fee">
                <input type="hidden" name="discount">
                <input type="hidden" name="danh_sach">
                <div class="row">
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price') }}">
                  </div>
                  <div class="form-group col-xs-4"  style="padding-right: 0px">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc') }}">
                  </div>
                  <div class="form-group col-xs-4" >
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        <option value="1" {{ old('nguoi_thu_coc') == 1 ? "selected" : "" }}>Sales</option>
                        <option value="2" {{ old('nguoi_thu_coc') == 2 ? "selected" : "" }}>CTY</option>
                        <option value="3" {{ old('nguoi_thu_coc') == 3 ? "selected" : "" }}>Nhà xe</option>
                        <option value="4" {{ old('nguoi_thu_coc') == 4 ? "selected" : "" }}>Điều hành</option>
                      </select>
                  </div>

                </div>
                <div class="row">

                  <div class="form-group col-xs-4"  style="padding-right: 0px">
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" style="border: 1px solid red" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai') }}">
                  </div>
                  <div class="form-group col-xs-4"  style="padding-right: 0px">
                      <label>Thực thu <span class="red-star">*</span></label>
                      <input type="text" style="border: 1px solid red" class="form-control number" name="tien_thuc_thu" id="tien_thuc_thu" value="{{ old('tien_thuc_thu') }}">
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Người thu $ <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        <option value="1" {{ old('nguoi_thu_tien') == 1 ? "selected" : "" }}>Sales</option>
                        <option value="2" {{ old('nguoi_thu_tien') == 2 ? "selected" : "" }}>CTY</option>
                        <option value="3" {{ old('nguoi_thu_tien') == 3 ? "selected" : "" }}>Nhà xe</option>
                        <option value="4" {{ old('nguoi_thu_tien') == 4 ? "selected" : "" }}>Điều hành</option>
                      </select>
                  </div>
                </div>
                <div class="row">
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only || Auth::user()->id == 23)
                  <div class="input-group" style="padding-left: 15px; padding-right: 15px">
                     <label>Sales <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="">--Chọn--</option>
                        @foreach($listUser as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $user_id_default) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                        @endforeach
                      </select>
                      <span class="input-group-btn">
                        <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddSales" type="button" data-value="3">
                          Thêm
                        </button>
                      </span>
                  </div>
                  @else
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  @endif
                  <input type="hidden" name="book_date" value="">
                </div>
                <div class="form-group" style="display: none;">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control select2" name="status" id="status">
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
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking-xe-may.index')}}">Hủy</a>
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
          <input type="hidden" name="type" value="4">
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
<div id="tagTag2" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    <form method="POST" action="{{ route('location.ajax-save')}}" id="formAjaxTag2">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tạo mới điểm đón</h4>
      </div>
      <div class="modal-body" id="contentTag">
          <input type="hidden" name="type" value="4">
           <!-- text input -->
          <div class="col-md-12">
            <div class="form-group">
              <label>Tên địa điểm<span class="red-star">*</span></label>
              <input type="text" class="form-control" id="add_address" value="{{ old('address') }}" name="str_tag">
            </div>
          </div>
          <div classs="clearfix"></div>
      </div>
      <div style="clear:both"></div>
      <div class="modal-footer" style="text-align:center">
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveTagAjax2"> Save</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="btnCloseModalTag2">Close</button>
      </div>
      </form>
    </div>

  </div>
</div>
<div id="modalSales" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    <form method="POST" action="{{ route('account.ajax-save')}}" id="formAjaxSales">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tạo mới sales</h4>
      </div>
      <div class="modal-body" id="contentTag">
          <input type="hidden" name="type" value="4">
           <!-- text input -->
          <div class="col-md-12">
            <div class="form-group">
              <label>Tên sales<span class="red-star">*</span></label>
              <input type="text" class="form-control" id="add_name" value="{{ old('add_name') }}" name="add_name">
            </div>
            <div class="form-group">
              <label>Số điện thoại<span class="red-star">*</span></label>
              <input type="text" class="form-control" id="add_phone" value="{{ old('add_phone') }}" name="add_phone">
            </div>
          </div>
          <div classs="clearfix"></div>
      </div>
      <div style="clear:both"></div>
      <div class="modal-footer" style="text-align:center">
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveSalesAjax"> Save</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="btnCloseModalSales">Close</button>
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
   $(document).on('click', '#btnSaveSalesAjax', function(){
    $(this).attr('disabled', 'disabled');
      $.ajax({
        url : $('#formAjaxSales').attr('action'),
        data: $('#formAjaxSales').serialize(),
        type : "post",
        success : function(sales_id){
          $('#btnCloseModalSales').click();
          $.ajax({
            url : "{{ route('account.ajax-list') }}",
            data: {
              sales_id : sales_id
            },
            type : "get",
            success : function(data){
                $('#user_id').html(data);
                $('#user_id').select2('refresh');

            }
          });
        }
      });
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
$(document).on('click', '#btnSaveTagAjax2', function(){
  $(this).attr('disabled', 'disabled');
    $.ajax({
      url : $('#formAjaxTag2').attr('action'),
      data: $('#formAjaxTag2').serialize(),
      type : "post",
      success : function(str_id){
        $('#btnCloseModalTag2').click();
        $.ajax({
          url : "{{ route('location.ajax-list') }}",
          data: {
            str_id : str_id
          },
          type : "get",
          success : function(data){
              $('#location_id_2').html(data);
              $('#location_id_2').select2('refresh');

          }
        });
      }
    });
 });
  $(document).ready(function(){
    $('#btnAddSales').click(function(){
          $('#modalSales').modal('show');
      });
    $('#dataForm').submit(function(){
      $('#btnSave').hide();
      $('#btnLoading').show();
    });
    $('#btnAddTag').click(function(){
          $('#tagTag').modal('show');
      });
    $('#btnAddTag2').click(function(){
          $('#tagTag2').modal('show');
      });

    $('#tien_coc').blur(function(){
      setPrice();
    });

  });
  function setPrice(){
    priceGhep();
  }
  function priceGhep(){
      //tien_coc
      var tien_coc = 0;
      if($('#tien_coc').val() != ''){
       tien_coc = parseInt($('#tien_coc').val());
      }

      var total_price = 0;
      if($('#total_price').val() != ''){
       total_price = parseInt($('#total_price').val());
      }
      $('#total_price').val(total_price);

      $('#con_lai').val(total_price - tien_coc);

  }
</script>
@stop
