@extends('layout')
@section('content')
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      Đặt khách sạn : cập nhật <span style="color: red">PTH{{ $detail->id }}</span>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking-hotel.index') }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking-hotel.index') }}" style="margin-bottom:5px">Xem danh sách booking</a>
    <a href="{{ route( 'booking-payment.index', ['booking_id' => $detail->id] ) }}" class="btn btn-danger btn-sm" style="margin-bottom:5px">Lịch sử thanh toán</a>
    <form role="form" method="POST" action="{{ route('booking-hotel.update') }}" id="dataForm">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        @if(Session::has('message'))
        <p class="alert alert-info" >{{ Session::get('message') }}</p>
        @endif
        <!-- general form elements -->
        <div class="box box-primary">
          <input type="hidden" name="id" value="{{ $detail->id }}">
          <input type="hidden" name="type" value="2">
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
                @foreach($detail->payment as $p)
                  @if($p->type == 1)
                  <img src="{{ Helper::showImageNew(str_replace('uploads/', '', $p->image_url))}}" width="80" style="border: 1px solid red" class="img-unc" >
                  @else
                  <br>+ {{$p->notes}}
                  @endif
                  @endforeach


              </div>
              <div class="row">
                <div class="form-group col-md-6">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control" name="status" id="status">
                        <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>Mới</option>
                        <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Hoàn tất</option>
                        <!-- <option value="4" {{ old('status', $detail->status) == 4 ? "selected" : "" }}>Dời ngày</option> -->
                        <option value="3" {{ old('status', $detail->status) == 3 ? "selected" : "" }}>Hủy</option>
                      </select>
                  </div>
                   <div class="form-group col-md-6">
                     <label>Mail book phòng</label>
                      <select class="form-control" name="mail_hotel" id="mail_hotel">
                        <option value="1" {{ old('mail_hotel', $detail->status) == 1 ? "selected" : "" }}>Đã mail book</option>
                        <option value="0" {{ old('mail_hotel', $detail->mail_hotel) == 0 ? "selected" : "" }}>Chưa mail</option>
                      </select>
                  </div>
                </div>
                <div class="row">

                  <div class="form-group col-xs-5" style="padding-right: 0px">
                      <label>Hoa hồng CTY</label>
                      <input type="text" name="hoa_hong_cty" id="hoa_hong_cty" class="form-control number" value="{{ old('hoa_hong_cty', $detail->hoa_hong_cty) }}">
                  </div>
                  <div class="form-group col-xs-7" >
                      <label>Hoa hồng sales</label>
                      <input type="text" name="hoa_hong_sales" id="hoa_hong_sales" class="form-control number" value="{{ old('hoa_hong_sales', $detail->hoa_hong_sales) }}">
                  </div>
                </div>

                <div class="row">
                    <div class="form-group col-xs-12 col-md-5" style="padding-right: 0px">
                      <label>Tên khách hàng <span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $detail->name ) }}" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                   <div class="form-group col-xs-12 col-md-7">
                      <label>Facebook</label>
                      <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook', $detail->facebook) }}">
                    </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-5"  style="padding-right: 0px">
                    <label>Điện thoại <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                  </div>
                  <div class="form-group col-xs-7">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" id="email" value="{{ old('email', $detail->email) }}">
                  </div>
                </div>

                <div class="row">
                  @php
                    if($detail->checkin){
                        $checkin = old('checkin', date('d/m/Y', strtotime($detail->checkin)));
                    }else{
                        $checkin = old('checkin');
                    }
                  @endphp
                  <div class="form-group col-xs-5" style="padding-right: 0px" >
                    <label>Check-in<span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="checkin" id="checkin" value="{{ $checkin }}" autocomplete="off">
                  </div>
                  @php
                    if($detail->checkout){
                        $checkout = old('checkout', date('d/m/Y', strtotime($detail->checkout)));
                    }else{
                        $checkout = old('checkout');
                    }
                  @endphp
                  <div class="form-group col-xs-7">
                  <label>Check-out <span class="red-star">*</span></label>
                  <input type="text" class="form-control datepicker" name="checkout" id="checkout" value="{{ $checkout }}" autocomplete="off">
                </div>
                </div>
                 <div class="row">
                <div class="form-group col-md-7">
                  <label>Khách sạn</label>
                  <select class="form-control select2" name="hotel_id" id="hotel_id">
                    <option value="">--Chọn--</option>
                    @foreach($hotelList as $hotel)
                    <option value="{{ $hotel->id }}" {{ old('hotel_id', $detail->hotel_id) == $hotel->id  ? "selected" : "" }}>{{ $hotel->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-md-5">
                  <label>Đối tác</label>
                  <select class="form-control select2" name="hotel_book" id="hotel_book">
                    <option value="">Trực tiếp KS</option>
                      @if(!empty($relatedArr))
                      @foreach($relatedArr as $r)
                      <option value="{{ $r->id }}" {{ $r->id == old('hotel_book', $detail->hotel_book) ? "selected" : "" }}>{{ $r->name }}</option>
                      @endforeach
                      @endif
                  </select>
                </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Người lớn <span class="red-star">*</span></label>
                      <select class="form-control select2" name="adults" id="adults">
                        @for($i = 1; $i <= 150; $i++)
                        <option value="{{ $i }}" {{ old('adults', $detail->adults) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Trẻ em</label>
                      <select class="form-control select2" name="childs" id="childs">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('childs', $detail->childs) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Em bé </label>
                      <select class="form-control select2" name="infants" id="infants">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('infants', $detail->infants) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>

                </div>
                 <hr>
                <p style="color: blue;font-weight: bold;text-decoration: underline;text-transform: uppercase;margin-top: 15px;">Danh sách phòng:</p>
                <div class="rooms-row">
                    @php
                    $room_name0 = isset($roomArr[0]) ? $roomArr[0]['room_name'] : old('room_name.0');
                    $room_amount0 = isset($roomArr[0]) ? $roomArr[0]['room_amount'] : old('room_amount.0');
                    $room_nights0 = isset($roomArr[0]) ? $roomArr[0]['nights'] : old('room_nights.0');
                    $original_price0 = isset($roomArr[0]) ? $roomArr[0]['original_price'] : old('original_price.0');
                    $price_sell0 = isset($roomArr[0]) ? $roomArr[0]['price_sell'] : old('price_sell.0');
                    $total_price0 = isset($roomArr[0]) ? $roomArr[0]['total_price'] : old('total_price.0');
                    $notes0 = isset($roomArr[0]) ? $roomArr[0]['room_notes'] : old('room_notes.0');
                    @endphp
                    <div class="row">
                      <div class="form-group col-xs-8 col-md-3" style="padding-right: 0px">
                          <label>Loại phòng</label>
                          <input type="text" name="room_name[]" id="room_name_0" class="form-control" value="{{ $room_name0 }}">
                      </div>
                      <div class="form-group col-xs-4 col-md-2" >
                          <label>Số lượng</label>
                          <select class="form-control room_amount" name="room_amount[]" id="room_amount_0">
                            <option value="0">0</option>
                            @for($i = 1; $i <= 50; $i++)
                            <option value="{{ $i }}" {{ $room_amount0 == $i ? "selected" : "" }}>{{ $i }}</option>
                            @endfor
                          </select>
                      </div>
                      <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px" >
                          <label>Số đêm</label>
                          <select class="form-control room_night" name="room_nights[]" id="room_nights_0">
                            <option value="0">0</option>
                            @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ $room_nights0 == $i ? "selected" : "" }}>{{ $i }}</option>
                            @endfor
                          </select>
                      </div>
                      <div class="form-group col-xs-4 col-md-2" style="padding-right: 0px">
                          <label>Giá bán</label>
                          <input type="text" name="price_sell[]" id="price_sell_0" class="form-control number room_price" value="{{ $price_sell0 }}">
                      </div>
                      <div class="form-group col-xs-4 col-md-3" >
                          <label>Tổng tiền</label>
                          <input type="text" name="room_total_price[]" id="total_price_0" class="form-control number room_price_total" value="{{ $total_price0 }}">
                      </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-4 col-md-4" style="padding-right: 0px">
                            <label>Giá gốc</label>
                          <input type="text" name="original_price[]" id="original_price_0" class="form-control number" value="{{ $original_price0 }}">
                        </div>
                        <div class="form-group col-xs-8 col-md-8" >
                            <label>Ghi chú</label>
                            <input type="text" name="room_notes[]" id="room_notes_0" class="form-control" value="{{ $notes0 }}" placeholder="Ghi chú">
                        </div>
                    </div>
                  </div>
                  <hr>
                  <div class="rooms-row">
                    @php
                    $room_name1 = isset($roomArr[1]) ? $roomArr[1]['room_name'] : old('room_name.1');
                    $room_amount1 = isset($roomArr[1]) ? $roomArr[1]['room_amount'] : old('room_amount.1');
                    $room_nights1 = isset($roomArr[1]) ? $roomArr[1]['nights'] : old('room_nights.1');
                    $original_price1 = isset($roomArr[1]) ? $roomArr[1]['original_price'] : old('original_price.1');
                    $price_sell1 = isset($roomArr[1]) ? $roomArr[1]['price_sell'] : old('price_sell.1');
                    $total_price1 = isset($roomArr[1]) ? $roomArr[1]['total_price'] : old('total_price.1');
                    $notes1 = isset($roomArr[1]) ? $roomArr[1]['room_notes'] : old('room_notes.1');
                    @endphp
                    <div class="row">
                      <div class="form-group col-xs-8 col-md-3" style="padding-right: 1px">
                          <label>Loại phòng</label>
                          <input type="text" name="room_name[]" id="room_name_1" class="form-control" value="{{ $room_name1 }}">
                      </div>
                      <div class="form-group col-xs-4 col-md-2" >
                          <label>Số lượng</label>
                          <select class="form-control room_amount" name="room_amount[]" id="room_amount_1">
                            <option value="1">1</option>
                            @for($i = 1; $i <= 11; $i++)
                            <option value="{{ $i }}" {{ $room_amount1 == $i ? "selected" : "" }}>{{ $i }}</option>
                            @endfor
                          </select>
                      </div>
                      <div class="form-group col-xs-4 col-md-2" style="padding-right: 1px" >
                          <label>Số đêm</label>
                          <select class="form-control room_night" name="room_nights[]" id="room_nights_1">
                            <option value="1">1</option>
                            @for($i = 1; $i <= 50; $i++)
                            <option value="{{ $i }}" {{ $room_nights1 == $i ? "selected" : "" }}>{{ $i }}</option>
                            @endfor
                          </select>
                      </div>
                      <div class="form-group col-xs-4 col-md-2" style="padding-right: 1px">
                          <label>Giá bán</label>
                          <input type="text" name="price_sell[]" id="price_sell_1" class="form-control number room_price" value="{{ $price_sell1 }}">
                      </div>
                      <div class="form-group col-xs-4 col-md-3" >
                          <label>Tổng tiền</label>
                          <input type="text" name="room_total_price[]" id="total_price_1" class="form-control number room_price_total" value="{{ $total_price1 }}">
                      </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-4 col-md-4" style="padding-right: 1px">
                            <label>Giá gốc</label>
                          <input type="text" name="original_price[]" id="original_price_1" class="form-control number" value="{{ $original_price1 }}">
                        </div>
                        <div class="form-group col-xs-8 col-md-8" >
                            <label>Ghi chú</label>
                            <input type="text" name="room_notes[]" id="room_notes_1" class="form-control" value="{{ $notes1 }}" placeholder="Ghi chú">
                        </div>
                    </div>
                  </div>
                  <hr>
                  <div class="rooms-row">
                    @php
                    $room_name2 = isset($roomArr[2]) ? $roomArr[2]['room_name'] : old('room_name.2');
                    $room_amount2 = isset($roomArr[2]) ? $roomArr[2]['room_amount'] : old('room_amount.2');
                    $room_nights2 = isset($roomArr[2]) ? $roomArr[2]['nights'] : old('room_nights.2');
                    $original_price2 = isset($roomArr[2]) ? $roomArr[2]['original_price'] : old('original_price.2');
                    $price_sell2 = isset($roomArr[2]) ? $roomArr[2]['price_sell'] : old('price_sell.2');
                    $total_price2 = isset($roomArr[2]) ? $roomArr[2]['total_price'] : old('total_price.2');
                    $notes2 = isset($roomArr[2]) ? $roomArr[2]['room_notes'] : old('room_notes.2');
                    @endphp
                    <div class="row">
                      <div class="form-group col-xs-8 col-md-3" style="padding-right: 2px">
                          <label>Loại phòng</label>
                          <input type="text" name="room_name[]" id="room_name_2" class="form-control" value="{{ $room_name2 }}">
                      </div>
                      <div class="form-group col-xs-4 col-md-2" >
                          <label>Số lượng</label>
                          <select class="form-control room_amount" name="room_amount[]" id="room_amount_2">
                            <option value="2">2</option>
                            @for($i = 1; $i <= 50; $i++)
                            <option value="{{ $i }}" {{ $room_amount2 == $i ? "selected" : "" }}>{{ $i }}</option>
                            @endfor
                          </select>
                      </div>
                      <div class="form-group col-xs-4 col-md-2" style="padding-right: 2px" >
                          <label>Số đêm</label>
                          <select class="form-control room_night" name="room_nights[]" id="room_nights_2">
                            <option value="2">2</option>
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $room_nights2 == $i ? "selected" : "" }}>{{ $i }}</option>
                            @endfor
                          </select>
                      </div>
                      <div class="form-group col-xs-4 col-md-2" style="padding-right: 2px">
                          <label>Giá bán</label>
                          <input type="text" name="price_sell[]" id="price_sell_2" class="form-control number room_price" value="{{ $price_sell2 }}">
                      </div>
                      <div class="form-group col-xs-4 col-md-3" >
                          <label>Tổng tiền</label>
                          <input type="text" name="room_total_price[]" id="total_price_2" class="form-control number room_price_total" value="{{ $total_price2 }}">
                      </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-4 col-md-4" style="padding-right: 2px">
                            <label>Giá gốc</label>
                          <input type="text" name="original_price[]" id="original_price_2" class="form-control number" value="{{ $original_price2 }}">
                        </div>
                        <div class="form-group col-xs-8 col-md-8" >
                            <label>Ghi chú</label>
                            <input type="text" name="room_notes[]" id="room_notes_2" class="form-control" value="{{ $notes2 }}" placeholder="Ghi chú">
                        </div>
                    </div>
                  </div>
              <hr>
                <div class="rooms-row">
                @php
                $room_name3 = isset($roomArr[3]) ? $roomArr[3]['room_name'] : old('room_name.3');
                $room_amount3 = isset($roomArr[3]) ? $roomArr[3]['room_amount'] : old('room_amount.3');
                $room_nights3 = isset($roomArr[3]) ? $roomArr[3]['nights'] : old('room_nights.3');
                $original_price3 = isset($roomArr[3]) ? $roomArr[3]['original_price'] : old('original_price.3');
                $price_sell3 = isset($roomArr[3]) ? $roomArr[3]['price_sell'] : old('price_sell.3');
                $total_price3 = isset($roomArr[3]) ? $roomArr[3]['total_price'] : old('total_price.3');
                $notes3 = isset($roomArr[3]) ? $roomArr[3]['room_notes'] : old('room_notes.3');
                @endphp
                <div class="row">
                  <div class="form-group col-xs-8 col-md-3" style="padding-right: 1px">
                      <label>Loại phòng</label>
                      <input type="text" name="room_name[]" id="room_name_3" class="form-control" value="{{ $room_name3 }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-2" >
                      <label>Số lượng</label>
                      <select class="form-control room_amount" name="room_amount[]" id="room_amount_3">
                        <option value="1">1</option>
                        @for($i = 1; $i <= 11; $i++)
                        <option value="{{ $i }}" {{ $room_amount3 == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 1px" >
                      <label>Số đêm</label>
                      <select class="form-control room_night" name="room_nights[]" id="room_nights_3">
                        <option value="1">1</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ $room_nights3 == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4 col-md-2" style="padding-right: 1px">
                      <label>Giá bán</label>
                      <input type="text" name="price_sell[]" id="price_sell_3" class="form-control number room_price" value="{{ $price_sell3 }}">
                  </div>
                  <div class="form-group col-xs-4 col-md-3" >
                      <label>Tổng tiền</label>
                      <input type="text" name="room_total_price[]" id="total_price_3" class="form-control number room_price_total" value="{{ $total_price3 }}">
                  </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-4 col-md-4" style="padding-right: 1px">
                        <label>Giá gốc</label>
                      <input type="text" name="original_price[]" id="original_price_3" class="form-control number" value="{{ $original_price3 }}">
                    </div>
                    <div class="form-group col-xs-8 col-md-8" >
                        <label>Ghi chú</label>
                        <input type="text" name="room_notes[]" id="room_notes_3" class="form-control" value="{{ $notes3 }}" placeholder="Ghi chú">
                    </div>
                </div>
              </div>
              <hr>
            <hr>


                <div class="row">
                  <div class="form-group col-xs-5" style="padding-right: 0px">
                      <label>Phụ thu</label>
                    <input type="text" class="form-control number" name="extra_fee" id="extra_fee" value="{{ old('extra_fee', $detail->extra_fee) }}">
                  </div>
                  <div class="form-group col-xs-7" >
                      <label>Nội dung phụ thu</label>
                      <input type="text" class="form-control" name="extra_fee_notes" id="extra_fee_notes" value="{{ old('extra_fee_notes', $detail->extra_fee_notes) }}" autocomplete="off">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc', $detail->tien_coc) }}">
                  </div>
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        <option value="1" {{ old('nguoi_thu_coc', $detail->nguoi_thu_coc) == 1 ? "selected" : "" }}>Sales</option>
                        <option value="2" {{ old('nguoi_thu_coc', $detail->nguoi_thu_coc) == 2 ? "selected" : "" }}>CTY</option>
                      </select>
                  </div>
                  @php
                    if($detail->ngay_coc){
                        $ngay_coc = old('ngay_coc', date('d/m/Y', strtotime($detail->ngay_coc)));
                    }else{
                        $ngay_coc = old('ngay_coc');
                    }
                  @endphp
                  <div class="form-group col-xs-4" >
                      <label>Ngày cọc</label>
                      <input type="text" class="form-control datepicker" name="ngay_coc" id="ngay_coc" value="{{ $ngay_coc }}" autocomplete="off">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-4">
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price', $detail->total_price) }}">
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        <option value="1" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == 1 ? "selected" : "" }}>Sales</option>
                        <option value="2" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == 2 ? "selected" : "" }}>CTY</option>
                        <option value="4" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == 4 ? "selected" : "" }}>Công nợ</option>
                      </select>
                  </div>
                  <div class="form-group col-xs-4" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai', $detail->con_lai) }}">
                  </div>
                </div>
                <div class="row">
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                     <label>Sales <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="0">--Chọn--</option>
                        @foreach($listUser as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $detail->user_id) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                        @endforeach
                      </select>
                  </div>

                  @endif
                  <div class="form-group col-xs-4">
                     <label>Người book</label>
                      <select class="form-control select2" name="ctv_id" id="ctv_id">
                        <option value="">--Chọn--</option>
                        @foreach($ctvList as $ctv)
                        <option value="{{ $ctv->id }}" {{ old('ctv_id', $detail->ctv_id) == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  @php
                    if($detail->book_date){
                        $book_date = old('book_date', date('d/m/Y', strtotime($detail->book_date)));
                    }else{
                        $book_date = old('book_date');
                    }
                  @endphp
                  <div class="form-group @if(Auth::user()->role == 1 && !Auth::user()->view_only) col-xs-4 @else col-xs-12  @endif" >
                      <label>Ngày đặt</label>
                      <input type="text" class="form-control datepicker" name="book_date" id="book_date" value="{{ $book_date }}" autocomplete="off">
                  </div>
                </div>
                <div class="form-group">
                  <label>Danh sách khách</label>
                  <textarea style="text-transform: uppercase;" class="form-control" rows="6" name="danh_sach" id="danh_sach" >{{ old('danh_sach', $detail->danh_sach) }}</textarea>
                </div>
                <div class="row">
                  <div class="form-group col-xs-6 col-md-6" style="padding-right: 0px">
                      <label>Đón bay</label>
                    <input type="text" name="don_bay" id="don_bay" class="form-control" value="{{ old('don_bay', $detail->don_bay) }}" placeholder="">
                  </div>
                  <div class="form-group col-xs-6 col-md-6" >
                      <label>Tiễn bay</label>
                      <input type="text" name="tien_bay" id="tien_bay" class="form-control" value="{{ old('tien_bay', $detail->tien_bay) }}" placeholder="">
                  </div>
              </div>
                <div class="row">
                <div class="form-group col-md-6">
                  <label>Ghi chú cho khách sạn</label>
                  <textarea class="form-control" rows="4" name="notes_hotel" id="notes_hotel" >{{ old('notes_hotel', $detail->notes_hotel) }}</textarea>
                </div>
                <div class="form-group col-md-6">
                  <label>Ghi chú chung</label>
                  <textarea class="form-control" rows="4" name="notes" id="notes" >{{ old('notes', $detail->notes) }}</textarea>
                </div>
                </div>

            </div>

            <div class="box-footer">
              <button type="button" id="btnSubmit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking-hotel.index') }}">Hủy</a>
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
@stop

@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    @if($detail->status != 1 && Auth::user()->role != 1)
      $('#dataForm input, #dataForm select, #dataForm textarea').attr('disabled', 'disabled');
    @endif
    $('#btnSubmit').click(function(){
      setPrice();
      $('#dataForm').submit();
    });
    $('.room_price').blur(function(){
      setPrice();
    });
    $('.room_price, .room_amount, .room_night, #extra_fee, #tien_coc').change(function(){
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
<script type="text/javascript">
  $(document).ready(function(){
    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    });
  });
</script>
@stop
