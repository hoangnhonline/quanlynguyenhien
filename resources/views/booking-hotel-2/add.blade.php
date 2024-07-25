@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Main content -->

  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      Đặt khách sạn
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking-hotel.index') }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking-hotel.index') }}" style="margin-bottom:5px">Xem danh sách booking</a>
    <form role="form" method="POST" action="{{ route('booking-hotel.store') }}" id="dataForm">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-body">
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Thông tin chi tiết</a></li>
            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Giấy tờ tùy thân</a></li>
          </ul>

          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
              {!! csrf_field() !!}


              @if (count($errors) > 0)
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
              <input type="hidden" name="type" value="2">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-5">
                      <label>Tên khách hàng <span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                   <div class="form-group col-xs-12 col-md-7">
                      <label>Facebook</label>
                      <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook') }}">
                    </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-5"  style="padding-right: 0px">
                    <label>Điện thoại <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">
                  </div>
                  <div class="form-group col-xs-7">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}">
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-5" style="padding-right: 0px" >
                    <label>Check-in<span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="checkin" id="checkin" value="{{ old('checkin') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-7">
                  <label>Check-out <span class="red-star">*</span></label>
                  <input type="text" class="form-control datepicker" name="checkout" id="checkout" value="{{ old('checkout') }}" autocomplete="off">
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-7">
                  <label>Khách sạn</label>
                  <select class="form-control select2" name="hotel_id" id="hotel_id">
                    <option value="">--Chọn--</option>
                    @foreach($cateList as $hotel)
                    <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id  ? "selected" : "" }}>{{ $hotel->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-md-5">
                  <label>Đối tác</label>
                  <select class="form-control" name="hotel_book" id="hotel_book">
                    <option value="">Trực tiếp KS</option>
                  </select>
                </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Người lớn <span class="red-star">*</span></label>
                      <select class="form-control select2" name="adults" id="adults">
                        @for($i = 1; $i <= 200; $i++)
                        <option value="{{ $i }}" {{ old('adults') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Trẻ em</label>
                      <select class="form-control select2" name="childs" id="childs">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('childs') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Em bé </label>
                      <select class="form-control select2" name="infants" id="infants">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('infants') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>

                </div>

                 <div id="div_phong">
                <p style="color: blue;font-weight: bold;text-decoration: underline;text-transform: uppercase;margin-top: 15px;">Danh sách phòng:</p>
                <div class="rooms-row">
                <div class="row">
                  <div class="form-group col-xs-8 col-md-3" style="padding-right: 0px">
                      <label>Loại phòng</label>
                      <input type="text" name="room_name[]" id="room_name_0" class="form-control" value="{{ old('room_name.0') }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-2" >
                      <label>Số lượng</label>
                      <select class="form-control room_amount" name="room_amount[]" id="room_amount_0">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('room_amount.0') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px" >
                      <label>Số đêm</label>
                      <select class="form-control room_night" name="room_nights[]" id="room_nights_0">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('room_nights.0') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px">
                      <label>Giá bán</label>
                      <input type="text" name="price_sell[]" id="price_sell_0" class="form-control number room_price" value="{{ old('price_sell.0') }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-3" >
                      <label>Tổng tiền</label>
                      <input type="text" name="room_total_price[]" id="total_price_0" class="form-control number room_price_total" value="{{ old('room_total_price.0') }}">
                  </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 0px">
                        <label>Giá gốc</label>
                      <input type="text" name="original_price[]" id="original_price_0" class="form-control number original_price" value="{{ old('original_price.0') }}">
                    </div>
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 0px">
                        <label>Tổng giá gốc</label>
                      <input type="text" name="total_original_price[]" id="total_original_price_1" class="form-control number total_original_price" value="{{ old('total_original_price.1') }}">
                    </div>
                    <div class="form-group col-xs-4 col-md-4" >
                        <label>Ghi chú</label>
                        <input type="text" name="room_notes[]" id="room_notes_0" class="form-control" value="{{ old('room_notes.0') }}" placeholder="Ghi chú">
                    </div>
                </div>
                </div>

                <div class="rooms-row">
                <div class="row">
                  <div class="form-group col-xs-8 col-md-3" style="padding-right: 0px">
                      <label>Loại phòng</label>
                      <input type="text" name="room_name[]" id="room_name_1" class="form-control" value="{{ old('room_name.1') }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-2" >
                      <label>Số lượng</label>
                      <select class="form-control room_amount" name="room_amount[]" id="room_amount_1">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('room_amount.1') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px" >
                      <label>Số đêm</label>
                      <select class="form-control room_night" name="room_nights[]" id="room_nights_1">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('room_nights.1') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px">
                      <label>Giá bán</label>
                      <input type="text" name="price_sell[]" id="price_sell_1" class="form-control number room_price" value="{{ old('price_sell.1') }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-3" >
                      <label>Tổng tiền</label>
                      <input type="text" name="room_total_price[]" id="total_price_1" class="form-control number room_price_total" value="{{ old('room_total_price.1') }}">
                  </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 5px">
                        <label>Giá gốc</label>
                      <input type="text" name="original_price[]" id="original_price_1" class="form-control number original_price" value="{{ old('original_price.1') }}">
                    </div>
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 0px">
                        <label>Tổng giá gốc</label>
                      <input type="text" name="total_original_price[]" id="total_original_price_1" class="form-control number total_original_price" value="{{ old('total_original_price.1') }}">
                    </div>
                    <div class="form-group col-xs-4 col-md-4" >
                        <label>Ghi chú</label>
                        <input type="text" name="room_notes[]" id="room_notes_1" class="form-control" value="{{ old('room_notes.1') }}" placeholder="Ghi chú">
                    </div>
                </div>
              </div>

                <div class="rooms-row">
                <div class="row">
                  <div class="form-group col-xs-8 col-md-3" style="padding-right: 0px">
                      <label>Loại phòng</label>
                      <input type="text" name="room_name[]" id="room_name_2" class="form-control" value="{{ old('room_name.2') }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-2" >
                      <label>Số lượng</label>
                      <select class="form-control room_amount" name="room_amount[]" id="room_amount_2">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('room_amount.2') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px" >
                      <label>Số đêm</label>
                      <select class="form-control room_night" name="room_nights[]" id="room_nights_2">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('room_nights.2') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px">
                      <label>Giá bán</label>
                      <input type="text" name="price_sell[]" id="price_sell_2" class="form-control number room_price" value="{{ old('price_sell.2') }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-3" >
                      <label>Tổng tiền</label>
                      <input type="text" name="room_total_price[]" id="total_price_2" class="form-control number room_price_total" value="{{ old('room_total_price.2') }}">
                  </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 5px">
                        <label>Giá gốc</label>
                      <input type="text" name="original_price[]" id="original_price_2" class="form-control number original_price" value="{{ old('original_price.2') }}">
                    </div>
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 0px">
                        <label>Tổng giá gốc</label>
                      <input type="text" name="total_original_price[]" id="total_original_price_2" class="form-control number total_original_price" value="{{ old('total_original_price.2') }}">
                    </div>
                    <div class="form-group col-xs-4 col-md-4" >
                        <label>Ghi chú</label>
                        <input type="text" name="room_notes[]" id="room_notes_2" class="form-control" value="{{ old('room_notes.2') }}" placeholder="Ghi chú">
                    </div>
                </div>
              </div>

                <div class="rooms-row">
                <div class="row">
                  <div class="form-group col-xs-8 col-md-3" style="padding-right: 0px">
                      <label>Loại phòng</label>
                      <input type="text" name="room_name[]" id="room_name_0" class="form-control" value="{{ old('room_name.0') }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-2" >
                      <label>Số lượng</label>
                      <select class="form-control room_amount" name="room_amount[]" id="room_amount_0">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('room_amount.0') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px" >
                      <label>Số đêm</label>
                      <select class="form-control room_night" name="room_nights[]" id="room_nights_0">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('room_nights.0') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px">
                      <label>Giá bán</label>
                      <input type="text" name="price_sell[]" id="price_sell_0" class="form-control number room_price" value="{{ old('price_sell.0') }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-3" >
                      <label>Tổng tiền</label>
                      <input type="text" name="room_total_price[]" id="total_price_0" class="form-control number room_price_total" value="{{ old('room_total_price.0') }}">
                  </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 0px">
                        <label>Giá gốc</label>
                      <input type="text" name="original_price[]" id="original_price_0" class="form-control number original_price" value="{{ old('original_price.0') }}">
                    </div>
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 0px">
                        <label>Tổng giá gốc</label>
                      <input type="text" name="total_original_price[]" id="total_original_price_3" class="form-control number" value="{{ old('total_original_price.3') }}">
                    </div>
                    <div class="form-group col-xs-4 col-md-4" >
                        <label>Ghi chú</label>
                        <input type="text" name="room_notes[]" id="room_notes_0" class="form-control" value="{{ old('room_notes.0') }}" placeholder="Ghi chú">
                    </div>
                </div>
                </div>

                </div><!--phong-->
                <hr>
                <div class="row">
                  <div class="form-group col-xs-5" style="padding-right: 0px">
                      <label>Phụ thu</label>
                    <input type="text" class="form-control number" name="extra_fee" id="extra_fee" value="{{ old('extra_fee') }}">
                  </div>
                  <div class="form-group col-xs-7" >
                      <label>Nội dung phụ thu</label>
                      <input type="text" class="form-control" name="extra_fee_notes" id="extra_fee_notes" value="{{ old('extra_fee_notes') }}" autocomplete="off">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc') }}">
                  </div>
                  <div class="form-group col-md-4 col-xs-4">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        <option value="1" {{ old('nguoi_thu_coc') == 1 ? "selected" : "" }}>Sales</option>
                        <option value="2" {{ old('nguoi_thu_coc') == 2 ? "selected" : "" }}>CTY</option>
                      </select>
                  </div>
                  <div class="form-group col-xs-4" >
                      <label>Ngày cọc</label>
                      <input type="text" class="form-control datepicker" name="ngay_coc" id="ngay_coc" value="{{ old('ngay_coc') }}" autocomplete="off">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-5" style="padding-right: 0px">
                      <label>TỔNG TIỀN GỐC<span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_cost" id="total_cost" value="{{ old('total_cost') }}">
                  </div>
                  <div class="form-group col-xs-5" style="padding-right: 0px">
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price') }}">
                  </div>

                  <div class="form-group col-xs-7" >
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
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                        @endforeach
                      </select>
                  </div>

                  @endif
                  <div class="form-group col-xs-7">
                     <label>Người book </label>
                      <select class="form-control select2" name="ctv_id" id="ctv_id">
                        <option value="">--Chọn--</option>
                        @foreach($ctvList as $ctv)
                        <option value="{{ $ctv->id }}" {{ old('ctv_id') == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-xs-12 " >
                      <label>Ngày đặt</label>
                      <input type="text" class="form-control datepicker" name="book_date" id="book_date" value="{{ old('book_date') }}" autocomplete="off">
                  </div>
                </div>
                <div class="form-group">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control" name="status" id="status">
                        <option value="1" {{ old('status') == 1 ? "selected" : "" }}>Mới</option>
                        <option value="2" {{ old('status') == 2 ? "selected" : "" }}>Hoàn tất</option>
                        <option value="3" {{ old('status') == 3 ? "selected" : "" }}>Hủy</option>
                      </select>
                  </div>
                  <div class="form-group">
                  <label>Danh sách khách</label>
                  <textarea class="form-control" rows="6" name="danh_sach" id="danh_sach"  onkeyup="this.value = this.value.toUpperCase();">{{ old('danh_sach') }}</textarea>
                </div>
                <div class="row">
                  <div class="form-group col-xs-6 col-md-6" style="padding-right: 0px">
                      <label>Đón bay</label>
                    <input type="text" name="don_bay" id="don_bay" class="form-control" value="{{ old('don_bay') }}" placeholder="">
                  </div>
                  <div class="form-group col-xs-6 col-md-6" >
                      <label>Tiễn bay</label>
                      <input type="text" name="tien_bay" id="tien_bay" class="form-control" value="{{ old('tien_bay') }}" placeholder="">
                  </div>
              </div>
                <div class="row">
                  <div class="form-group col-md-6">
                  <label>Ghi chú cho khách sạn</label>
                  <textarea class="form-control" rows="4" name="notes_hotel" id="notes_hotel" >{{ old('notes_hotel') }}</textarea>
                </div>
                <div class="form-group col-md-6">
                  <label>Ghi chú chung</label>
                  <textarea class="form-control" rows="4" name="notes" id="notes" >{{ old('notes') }}</textarea>
                </div>

                </div>

            </div><!--home-->
            <div role="tabpanel" class="tab-pane" id="settings">
              <div class="form-group" style="margin-top:10px;margin-bottom:10px">

                <div class="col-md-12" style="text-align:center">

                  <button class="btn btn-primary btnMultiUpload" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                  <div class="clearfix"></div>
                  <div id="div-image" style="margin-top:10px"></div>
                </div>
                <div style="clear:both"></div>
              </div>
            </div>
          </div>
        </div>
          <!-- /.box-header -->


            <div class="box-footer">
              <button type="button" id="btnSubmit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking-hotel.index')}}">Hủy</a>
            </div>

        </div>
        <!-- /.box -->

      </div>

    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<style type="text/css">
  .rooms-row{background-color: #F7F5F4; padding: 5px; margin-bottom: 15px;}
  .nav-tabs>li>a{
    border-color: #00a65a !important;
  }
  .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
    background-color: #00a65a !important;
    color: #fff !important;
  }
</style>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('#btnSubmit').click(function(){
      setPrice();
      $('#dataForm').submit();
    });
    $('.room_price, .room_amount, .room_night, #extra_fee, #tien_coc').change(function(){
      setPrice();
    });
    $('.room_price').blur(function(){
      setPrice();
    });
    $('#hotel_id').change(function(){
      $.ajax({
        url : '{{ route('booking.related')}}',
        type : 'GET',
        data: {
          hotel_id : $('#hotel_id').val()
        },
        success: function(data){
          $('#hotel_book').html(data);
        }
      });
    });
  });
  function setPrice(){
    var total_price = total_cost = 0;
    $('.rooms-row').each(function(){
      var row = $(this);
      var room_amount = parseInt(row.find('.room_amount').val());
      var room_night = parseInt(row.find('.room_night').val());
      var room_price = parseInt(row.find('.room_price').val());
      var original_price = parseInt(row.find('.original_price').val());
      console.log(room_amount, room_night, room_price);
      if(room_amount > 0 && room_night > 0 && room_price > 0){
        var room_price_total = room_amount*room_night*room_price;
        var room_price_total_original = room_amount*room_night*original_price;
        row.find('.room_price_total').val(room_price_total);
        row.find('.total_original_price').val(room_price_total_original);
        total_price += room_price_total;
        total_cost += room_price_total_original;
      }

    });
    //phu thu
    var extra_fee = 0;
    if($('#extra_fee').val() != ''){
     extra_fee = parseInt($('#extra_fee').val());
    }
    //tien_coc
    var tien_coc = 0;
    if($('#tien_coc').val() != ''){
     tien_coc = parseInt($('#tien_coc').val());
    }
    total_price = total_price + extra_fee;
    total_cost = total_cost + extra_fee;

    $('#total_price').val(total_price);
    $('#total_cost').val(total_cost);

    $('#con_lai').val(total_price - tien_coc);
  }
</script>
@stop
