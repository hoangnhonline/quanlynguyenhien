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
      <input type="hidden" name="customer_id" value="{{!empty($customerId) ? $customerId : ''}}">
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
               <div class="row">
                <div class="form-group col-sm-4">
                  <label>Thành phố</label>
                  <select class="form-control select2" name="city_id" id="city_id">
                    <option value="">--Chọn--</option>
                    @foreach($cityList as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $city_id) == $city->id  ? "selected" : "" }}>{{ $city->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-sm-4">
                  <label>Khách sạn</label>
                  <select class="form-control select2" name="hotel_id" id="hotel_id">
                    <option value="">--Chọn--</option>
                    @foreach($cateList as $hotel)
                    <option value="{{ $hotel->id }}" {{ old('hotel_id', $hotel_id) == $hotel->id  ? "selected" : "" }}>{{ $hotel->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-sm-4">
                  <label>Đối tác</label>
                  <select class="form-control select2" name="hotel_book" id="hotel_book">
                    <option value="">Trực tiếp KS</option>
                    @if(!empty($relatedArr))
                    @foreach($relatedArr as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
                </div>
              <input type="hidden" name="type" value="2">
              <div class="form-group col-md-12">
                <label style="font-weight: bold; color: red">
                  <input type="checkbox" id="is_vat" name="is_vat" value="1" {{ old('is_vat') == 1 ? "checked" : "" }}>
                  XUẤT HÓA ĐƠN VAT
                </label>
            </div>
              <div class="form-group">
                <label>Booking liên quan</label>
                <select class="form-control select2" id="related" multiple="multiple" name="related_id[]" >

                  @foreach($arrBooking as $booking)
                  <option value="{{ $booking->id }}">{{ Helper::showCode($booking) }} - {{ $booking->name }}</option>
                  @endforeach
                </select>
              </div>

                <div class="row">
                    <div class="form-group col-xs-12 col-md-6">
                      <label>Tên khách hàng <span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="name" id="name" value="{{ old('name', !empty($customer) ? $customer->name : '') }}" onkeyup="this.value = this.value.toUpperCase();" autocomplete="off">
                    </div>
                   <div class="form-group col-xs-12 col-md-6">
                      <label>Facebook</label>
                      <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook') }}" autocomplete="off">
                    </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-6" >
                    <label>Điện thoại <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', !empty($customer) ? $customer->phone : '') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" id="email" value="{{ old('email', !empty($customer) ? $customer->email : '') }}" autocomplete="off">
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-6" >
                    <label>Check-in<span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="checkin" id="checkin" value="{{ old('checkin') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6">
                  <label>Check-out <span class="red-star">*</span></label>
                  <input type="text" class="form-control datepicker" name="checkout" id="checkout" value="{{ old('checkout') }}" autocomplete="off">
                </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-4">
                      <label>Người lớn <span class="red-star">*</span></label>
                      <select class="form-control select2" name="adults" id="adults">
                        @for($i = 1; $i <= 200; $i++)
                        <option value="{{ $i }}" {{ old('adults') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
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
                <span style="color:red">Nếu không có loại phòng để chọn vui lòng liên hệ Admin</span>
                <?php
                $mocshow = 1;
                for($i = 1; $i <= 10; $i++){
                  if(old('room_name.'.($i-1))){
                    $mocshow = $i;
                  }
                }
                ?>
                <div class="mb10 mt15" >
                @for($k = 1; $k <= 10; $k++)
                <?php
                $key = $k-1;
                ?>
                <div class="mb15 rooms-row row-dia-diem {{ $k > $mocshow ? "dia-diem-hidden" : "" }}" style="background-color: #e6e6e6; padding:10px;border-radius: 5px">
                <div class="row">
                  <div class="form-group col-xs-8 col-md-3" style="padding-right: 5px;" >
                      <label>Loại phòng</label>
                      <select class="form-control select2" name="room_id[]" id="room_id_{{ $key }}">
                        <option value="">--Chọn--</option>
                        @foreach($roomArr as $room)
                        <option value="{{ $room['id'] }}" {{ old('room_id.'.$key) == $room['id'] ? "selected" : "" }}>{{ $room['name'] }}</option>
                        @endforeach

                      </select>

                  </div>
                  <div class="form-group col-xs-4 col-md-2" >
                      <label>Số lượng</label>
                      <select class="form-control room_amount" name="room_amount[]" id="room_amount_{{ $key }}">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('room_amount.'.$key) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2"  style="padding-right: 5px;" >
                      <label>Số đêm</label>
                      <select class="form-control room_night" name="room_nights[]" id="room_nights_{{ $key }}">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('room_nights.'.$key) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2"  style="padding-right: 5px;" >
                      <label>Giá bán</label>
                      <input type="text" name="price_sell[]" id="price_sell_{{ $key }}" class="form-control number room_price" value="{{ old('price_sell.'.$key) }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-3" >
                      <label>Tổng tiền</label>
                      <input type="text" name="room_total_price[]" id="total_price_{{ $key }}" class="form-control number room_price_total" value="{{ old('room_total_price.'.$key) }}">
                  </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 5px;" >
                        <label>Giá gốc</label>
                      <input type="text" name="original_price[]" id="original_price_{{ $key }}" class="form-control number" value="{{ old('original_price.'.$key) }}">
                    </div>
                    <div class="form-group col-xs-8 col-md-8" >
                        <label>Ghi chú</label>
                        <input type="text" name="room_notes[]" id="room_notes_{{ $key }}" class="form-control" value="{{ old('room_notes.'.$key) }}" placeholder="Ghi chú">
                    </div>
                </div>
                </div>
               @endfor

                </div><!--phong-->
                <div class="row">
                   <div class="col-md-12">
                     <button type="button" class="btn btn-warning" id="btnAddLocation"><i class="fa fa-plus"></i> Thêm loại phòng</button>
                   </div>
                 </div>

                <div class="row">
                  <div class="form-group col-xs-6">
                      <label>Phụ thu</label>
                    <input type="text" class="form-control number" name="extra_fee" id="extra_fee" value="{{ old('extra_fee') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Nội dung phụ thu</label>
                      <input type="text" class="form-control" name="extra_fee_notes" id="extra_fee_notes" value="{{ old('extra_fee_notes') }}" autocomplete="off" autocomplete="off">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-6">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc') }}" autocomplete="off">
                  </div>
                  <!-- <div class="form-group col-xs-4" >
                      <label>Ngày cọc</label>
                      <input type="text" class="form-control datepicker" name="ngay_coc" id="ngay_coc" value="{{ old('ngay_coc') }}" autocomplete="off">
                  </div> -->
                  <div class="form-group col-md-6 col-xs-6">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_coc') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-12">
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input style="border: 1px solid red" type="text" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai') }}" autocomplete="off">
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
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <div class="form-group col-xs-6">
                     <label>Sales <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="0">--Chọn--</option>
                        @foreach($listUser as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $user_id_default) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-xs-6">
                     <label>Người book </label>
                      <select class="form-control select2" name="ctv_id" id="ctv_id">
                        <option value="">--Chọn--</option>
                        @foreach($ctvList as $ctv)
                        <option value="{{ $ctv->id }}" {{ old('ctv_id') == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  @else
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  @endif

                </div>
                <div class="row">
                <div class="form-group col-xs-6 " >
                      <label>Ngày đặt</label>
                      <input type="text" class="form-control datepicker" name="book_date" id="book_date" value="{{ old('book_date') }}" autocomplete="off">
                  </div>
                <div class="form-group col-xs-6">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control" name="status" id="status">
                        <option value="1" {{ old('status') == 1 ? "selected" : "" }}>Mới</option>
                        <option value="2" {{ old('status') == 2 ? "selected" : "" }}>Hoàn tất</option>
                        <option value="3" {{ old('status') == 3 ? "selected" : "" }}>Hủy</option>
                      </select>
                  </div>
                  </div>
                  <div class="form-group">
                  <label>Danh sách khách</label>
                  <textarea class="form-control" rows="6" name="danh_sach" id="danh_sach"  onkeyup="this.value = this.value.toUpperCase();">{{ old('danh_sach') }}</textarea>
                </div>
                <div class="row">
                  <div class="form-group col-xs-6 col-md-6">
                      <label>Đón bay</label>
                    <input type="text" name="don_bay" id="don_bay" class="form-control" value="{{ old('don_bay') }}" placeholder="" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6 col-md-6" >
                      <label>Tiễn bay</label>
                      <input type="text" name="tien_bay" id="tien_bay" class="form-control" value="{{ old('tien_bay') }}" placeholder="" autocomplete="off">
                  </div>
              </div>
                <div class="row">
                  <div class="form-group col-md-6">
                  <label>Ghi chú cho khách sạn</label>
                  <textarea class="form-control" rows="4" name="notes_hotel" id="notes_hotel" >{{ old('notes_hotel') }}</textarea autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                  <label>Ghi chú chung</label>
                  <textarea class="form-control" rows="4" name="notes" id="notes"  autocomplete="off">{{ old('notes') }}</textarea>
                </div>

                </div>
                @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                <div class="ke-toan" style="background-color: #ffcccc;padding: 10px">
                  <div class="row">
                    <div class="form-group col-md-4">
                      <label>Tổng phụ thu</label>
                      <input type="text" class="form-control number" name="ptt_tong_phu_thu" id="ptt_tong_phu_thu" value="{{ old('ptt_tong_phu_thu') }}" autocomplete="off">
                    </div>
                    <div class="form-group col-md-4">
                      <label>PTT tiền cọc</label>
                      <input type="text" class="form-control number" name="ptt_tien_coc" id="ptt_tien_coc" value="{{ old('ptt_tien_coc') }}" autocomplete="off">
                    </div>
                    <div class="form-group col-md-4" >
                        <label>Ngày PTT cọc</label>
                        <input type="text" class="form-control datepicker" name="ptt_ngay_coc" id="ptt_ngay_coc" value="{{ old('ptt_ngay_coc') }}" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">

                    <div class="form-group col-md-6" >
                        <label>Ngày PTT thanh toán</label>
                        <input type="text" class="form-control datepicker" name="ptt_pay_date" id="ptt_pay_date" value="{{ old('ptt_pay_date') }}" autocomplete="off">
                    </div>
                    <div class="form-group col-md-6">
                      <label>PTT Trạng thái thanh toán</label>
                      <select class="form-control select2" name="ptt_pay_status" id="ptt_pay_status">

                        <option value="0" {{ old('ptt_pay_status') == 1 ? "selected" : "" }}>Chưa thanh toán</option>
                        <option value="1" {{ old('ptt_pay_status') == 1 ? "selected" : "" }}>Đã cọc</option>
                        <option value="2" {{ old('ptt_pay_status') == 2 ? "selected" : "" }}>Đã thanh toán</option>
                      </select>
                    </div>
                  </div>
                </div>

                @endif
            </div>

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
</style>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('#hotel_id, #city_id').change(function(){
      location.href= "{{ route('booking-hotel.create')}}?customer_id={{!empty($customerId) ? $customerId : ''}}&hotel_id=" + $(this).val() + '&city_id=' + $('#city_id').val();
    });
    $('#btnAddLocation').click(function(){
      $('.dia-diem-hidden:first').removeClass('dia-diem-hidden');
      $('.select2').select2();
    });
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
        url : '{{ route('booking-hotel.related')}}',
        type : 'GET',
        data: {
          hotel_id : $('#hotel_id').val(),
          customer_id: $('#customer_id').val(),
        },
        success: function(data){
          $('#hotel_book').html(data);
          $('#hotel_book').select2('refresh');
        }
      });
    });
  });
  function setPrice(){
    var total_price = 0;
    $('.rooms-row').each(function(){
      var row = $(this);
      var room_amount = parseInt(row.find('.room_amount').val());
      var room_night = parseInt(row.find('.room_night').val());
      var room_price = parseInt(row.find('.room_price').val());
      console.log(room_amount, room_night, room_price);
      if(room_amount > 0 && room_night > 0 && room_price > 0){
        var room_price_total = room_amount*room_night*room_price;
        row.find('.room_price_total').val(room_price_total);
        total_price += room_price_total;
      }

    });
    console.log(total_price);
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
    console.log('total_price: ', total_price);
    $('#total_price').val(total_price);

    $('#con_lai').val(total_price - tien_coc);
  }
</script>
@stop
