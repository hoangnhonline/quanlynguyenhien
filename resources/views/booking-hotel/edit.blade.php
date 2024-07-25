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
    @include('partials.booking-comments')
    <form role="form" method="POST" action="{{ route('booking-hotel.update') }}" id="dataForm">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <div id="content_alert"></div>
        @if(Session::has('message'))
        <p class="alert alert-info" >{{ Session::get('message') }}</p>
        @endif
        <!-- general form elements -->
        <div class="box box-primary">
          <input type="hidden" name="id" value="{{ $detail->id }}">
          <input type="hidden" name="type" value="2">
          <input type="hidden" name="city_id" value="{{ $city_id }}">
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
              @if($detail->payment->count() > 0)
                <fieldset class="scheduler-border">
                  <legend class="scheduler-border">THANH TOÁN</legend>

                      <table class="table table-bordered table-responsive" style="margin-bottom: 0px;">
                        @foreach($detail->payment as $p)
                        <tr>

                          <td>
                            @if($p->type == 1)
                            <img src="{{ Helper::showImageNew(str_replace('uploads/', '', $p->image_url))}}" width="80" style="border: 1px solid red" class="img-unc" >
                            @else
                            @if($p->notes)
                            + {{$p->notes}}<br>
                            @endif
                            @if($p->sms)
                            + {{$p->sms}}
                            @endif
                            @endif
                          </td>

                        </tr>
                         @endforeach
                      </table>

              </fieldset>
              @endif
              <div class="row">
                 <div class="form-group col-sm-4">
                  <label>Thành phố</label>
                  <select class="form-control select2" name="city_id" id="city_id">
                    <option value="">--Chọn--</option>
                    @foreach($cityList as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $detail->city_id) == $city->id  ? "selected" : "" }}>{{ $city->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-sm-4">
                  <label>Khách sạn</label>
                  <select class="form-control select2" name="hotel_id" id="hotel_id">
                    <option value="">--Chọn--</option>
                    @foreach($hotelList as $hotel)
                    <option value="{{ $hotel->id }}" {{ old('hotel_id', $detail->hotel_id) == $hotel->id  ? "selected" : "" }}>{{ $hotel->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-sm-4">
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
              <div class="form-group col-md-12">
                <label style="font-weight: bold; color: red">
                  <input type="checkbox" id="is_vat" name="is_vat" value="1" {{ old('is_vat', $detail->is_vat) == 1 ? "checked" : "" }}>
                  XUẤT HÓA ĐƠN VAT
                </label>
            </div>
              <div class="form-group">
                <label>Booking liên quan</label>
                <select class="form-control select2" id="related" name="related_id[]" multiple="multiple">

                  @foreach($arrBooking as $booking)
                  <option value="{{ $booking->id }}" {{ in_array($booking->id, $relatedIdArr) ? "selected" : "" }}>{{ Helper::showCode($booking) }} - {{ $booking->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control" name="status" id="status">
                        <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>Mới</option>
                        <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Hoàn tất</option>
                        <option value="4" {{ old('status', $detail->status) == 4 ? "selected" : "" }}>Dời ngày</option>
                        <option value="3" {{ old('status', $detail->status) == 3 ? "selected" : "" }}>Hủy</option>
                      </select>
                  </div>
                  @if($city_id == 1)
                   <div class="form-group col-md-6">
                     <label>Mail book phòng</label>
                      <select class="form-control" name="mail_hotel" id="mail_hotel">
                        <option value="1" {{ old('mail_hotel', $detail->status) == 1 ? "selected" : "" }}>Đã mail book</option>
                        <option value="0" {{ old('mail_hotel', $detail->mail_hotel) == 0 ? "selected" : "" }}>Chưa mail</option>
                      </select>
                  </div>
                  @endif
                </div>
                @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                <div class="row">

                  <div class="form-group col-xs-6">
                      <label>Hoa hồng CTY</label>
                      <input type="text" name="hoa_hong_cty" id="hoa_hong_cty" class="form-control number" value="{{ old('hoa_hong_cty', $detail->hoa_hong_cty) }}">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Hoa hồng sales</label>
                      <input type="text" name="hoa_hong_sales" id="hoa_hong_sales" class="form-control number" value="{{ old('hoa_hong_sales', $detail->hoa_hong_sales) }}">
                  </div>
                </div>
                @endif
                <div class="row">
                    <div class="form-group col-xs-12 col-md-6">
                      <label>Tên khách hàng <span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $detail->name ) }}" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                   <div class="form-group col-xs-12 col-md-6">
                      <label>Facebook</label>
                      <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook', $detail->facebook) }}">
                    </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-6" >
                    <label>Điện thoại <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                  </div>
                  <div class="form-group col-xs-6">
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
                  <div class="form-group col-xs-6" >
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
                  <div class="form-group col-xs-6">
                  <label>Check-out <span class="red-star">*</span></label>
                  <input type="text" class="form-control datepicker" name="checkout" id="checkout" value="{{ $checkout }}" autocomplete="off">
                </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-4">
                      <label>Người lớn <span class="red-star">*</span></label>
                      <select class="form-control select2" name="adults" id="adults">
                        @for($i = 1; $i <= 150; $i++)
                        <option value="{{ $i }}" {{ old('adults', $detail->adults) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
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
                <p style="color: blue;font-weight: bold;text-decoration: underline;text-transform: uppercase;margin-top: 15px;">Danh sách phòng: </p><span style="color:red">Nếu không có loại phòng để chọn vui lòng liên hệ Admin</span>
                <div class="mt15">
                <?php
                $locationSelected = $roomsList->count();
                $mocshow = $locationSelected;

                for($i = 1; $i <= 10; $i++){

                  if(old('room_id.'.($i-1)) > $locationSelected){
                    $mocshow = $i;
                  }
                }
                ?>
                @for($k = 1; $k <= 10; $k++)

                @php
                $ticket_type_id = $amount = $price = $price_sell = $total = $commission = null;
                $key = $k-1;

                @endphp

                <div class="rooms-row row-dia-diem mb10 {{ $k > $mocshow ? "dia-diem-hidden" : "" }}" style="background-color: #e6e6e6; padding:10px;border-radius: 5px">
                    @php
                    $room_name = isset($roomArr[$key]) ? $roomArr[$key]['room_name'] : old('room_name.'.$key);
                    $room_id = isset($roomArr[$key]) ? $roomArr[$key]['room_id'] : old('room_id.'.$key);
                    $room_amount = isset($roomArr[$key]) ? $roomArr[$key]['room_amount'] : old('room_amount.'.$key);
                    $room_nights = isset($roomArr[$key]) ? $roomArr[$key]['nights'] : old('room_nights.'.$key);
                    $original_price = isset($roomArr[$key]) ? $roomArr[$key]['original_price'] : old('original_price.'.$key);
                    $price_sell = isset($roomArr[$key]) ? $roomArr[$key]['price_sell'] : old('price_sell.'.$key);
                    $total_price = isset($roomArr[$key]) ? $roomArr[$key]['total_price'] : old('total_price.'.$key);
                    $notes = isset($roomArr[$key]) ? $roomArr[$key]['room_notes'] : old('room_notes.'.$key);
                    @endphp
                    <div class="row">
                      <div class="form-group col-xs-8 col-md-3"  style="padding-right: 5px;" >
                          <label>Loại phòng: </label>
                          <span style="color:blue">{{ $room_name }}</span>
                          <input type="hidden" name="room_name[]" id="room_name_{{ $key }}" value="{{ $room_name }}">
                          <select class="form-control select2" name="room_id[]" id="room_id_{{ $key }}">
                            <option value="">--Chọn--</option>
                            @foreach($roomArrHotel as $room)
                            <option value="{{ $room['id'] }}" {{ old('room_id.'.$key, $room_id) == $room['id'] ? "selected" : "" }}>{{ $room['name'] }}</option>
                            @endforeach

                          </select>
                      </div>
                      <div class="form-group col-xs-4 col-md-2" >
                          <label>Số lượng</label>
                          <select class="form-control room_amount" name="room_amount[]" id="room_amount_{{ $key }}">
                            <option value="0">0</option>
                            @for($i = 1; $i <= 50; $i++)
                            <option value="{{ $i }}" {{ $room_amount == $i ? "selected" : "" }}>{{ $i }}</option>
                            @endfor
                          </select>
                      </div>
                      <div class="form-group col-xs-4 col-md-2" style="padding-right: 5px;" >
                          <label>Số đêm</label>
                          <select class="form-control room_night" name="room_nights[]" id="room_nights_{{ $key }}">
                            <option value="0">0</option>
                            @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ $room_nights == $i ? "selected" : "" }}>{{ $i }}</option>
                            @endfor
                          </select>
                      </div>
                      <div class="form-group col-xs-4 col-md-2" style="padding-right: 5px;" >
                          <label>Giá bán</label>
                          <input type="text" name="price_sell[]" id="price_sell_{{ $key }}" class="form-control number room_price" value="{{ $price_sell }}" autocomplete="off">
                      </div>
                      <div class="form-group col-xs-4 col-md-3" >
                          <label>Tổng tiền</label>
                          <input type="text" name="room_total_price[]" id="total_price_{{ $key }}" class="form-control number room_price_total" value="{{ $total_price }}" autocomplete="off">
                      </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-4 col-md-4" style="padding-right: 5px;" >
                            <label>Giá gốc</label>
                          <input type="text" name="original_price[]" id="original_price_{{ $key }}" class="form-control number" value="{{ $original_price }}" autocomplete="off">
                        </div>
                        <div class="form-group col-xs-8 col-md-8" >
                            <label>Ghi chú</label>
                            <input type="text" name="room_notes[]" id="room_notes_{{ $key }}" class="form-control" value="{{ $notes }}" placeholder="Ghi chú">
                        </div>
                    </div>
                  </div>
                  @endfor
                  <div class="row">
                   <div class="col-md-12">
                     <button type="button" class="btn btn-warning" id="btnAddLocation"><i class="fa fa-plus"></i> Thêm loại phòng</button>
                   </div>
                 </div>

                <div class="row">
                  <div class="form-group col-xs-6">
                      <label>Phụ thu</label>
                    <input type="text" class="form-control number" name="extra_fee" id="extra_fee" value="{{ old('extra_fee', $detail->extra_fee) }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Nội dung phụ thu</label>
                      <input type="text" class="form-control" name="extra_fee_notes" id="extra_fee_notes" value="{{ old('extra_fee_notes', $detail->extra_fee_notes) }}" autocomplete="off">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-6">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc', $detail->tien_coc) }}" autocomplete="off">
                  </div>


                  <div class="form-group col-xs-6">
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
                  <div class="form-group col-xs-12">
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price', $detail->total_price) }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai', $detail->con_lai) }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
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
                        <option value="{{ $user->id }}" {{ old('user_id', $detail->user_id) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  @else
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  @endif
                  <div class="form-group col-xs-6">
                     <label>Người book </label>
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
                  <div class="form-group col-xs-12" >
                      <label>Ngày đặt</label>
                      <input type="text" class="form-control datepicker" name="book_date" id="book_date" value="{{ $book_date }}" autocomplete="off">
                  </div>
                </div>
                <div class="form-group">
                  <label>Danh sách khách</label>
                  <textarea style="text-transform: uppercase;" class="form-control" rows="6" name="danh_sach" id="danh_sach" >{{ old('danh_sach', $detail->danh_sach) }}</textarea>
                </div>
                <div class="row">
                  <div class="form-group col-xs-6 col-md-6">
                      <label>Đón bay</label>
                    <input type="text" name="don_bay" id="don_bay" class="form-control" value="{{ old('don_bay', $detail->don_bay) }}" placeholder="" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6 col-md-6" >
                      <label>Tiễn bay</label>
                      <input type="text" name="tien_bay" id="tien_bay" class="form-control" value="{{ old('tien_bay', $detail->tien_bay) }}" placeholder="" autocomplete="off">
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
                @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                <div style="background-color: #ffcccc;padding: 10px">
                  <div class="row">
                    <div class="form-group col-md-4 col-xs-12">
                      <label>PTT Tổng tiền gốc</label>
                      <input type="text" class="form-control number" id="ptt_tong_tien_goc" value="{{ old('ptt_tong_tien_goc', $detail->ptt_tong_tien_goc) }}" readonly="readonly">
                    </div>
                     <div class="form-group col-md-4 col-xs-12">
                      <label>PTT tổng phụ thu</label>
                      <input type="text" class="form-control number" name="ptt_tong_phu_thu" id="ptt_tong_phu_thu" value="{{ old('ptt_tong_phu_thu', $detail->ptt_tong_phu_thu) }}" autocomplete="off">
                    </div>
                    <div class="form-group col-xs-12 col-md-4">
                      <label>Trạng thái thanh toán</label>
                      <select class="form-control select2" name="ptt_pay_status" id="ptt_pay_status">

                        <option value="0" {{ old('ptt_pay_status', $detail->ptt_pay_status) == 1 ? "selected" : "" }}>Chưa thanh toán</option>
                        <option value="1" {{ old('ptt_pay_status', $detail->ptt_pay_status) == 1 ? "selected" : "" }}>Đã cọc</option>
                        <option value="2" {{ old('ptt_pay_status', $detail->ptt_pay_status) == 2 ? "selected" : "" }}>Đã thanh toán</option>
                      </select>
                    </div>
                    <div class="form-group col-md-4 col-xs-12">
                      <label>PTT tiền cọc</label>
                      <input type="text" class="form-control number" name="ptt_tien_coc" id="ptt_tien_coc" value="{{ old('ptt_tien_coc', $detail->ptt_tien_coc) }}" autocomplete="off">
                    </div>
                    @php
                      if($detail->ptt_ngay_coc){
                          $ptt_ngay_coc = old('ptt_ngay_coc', date('d/m/Y', strtotime($detail->ptt_ngay_coc)));
                      }else{
                          $ptt_ngay_coc = old('ptt_ngay_coc');
                      }
                    @endphp
                    <div class="form-group col-md-4 col-xs-12" >
                        <label>Ngày PTT cọc</label>
                        <input type="text" class="form-control datepicker" name="ptt_ngay_coc" id="ptt_ngay_coc" value="{{ $ptt_ngay_coc }}" autocomplete="off">
                    </div>
                    <div class="form-group col-md-4 col-xs-12">
                      <label>Người chi cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_chi_coc" id="nguoi_chi_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_chi_coc', $detail->nguoi_chi_coc) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>


                    @php
                      if($detail->ptt_pay_date){
                          $ptt_pay_date = old('ptt_pay_date', date('d/m/Y', strtotime($detail->ptt_pay_date)));
                      }else{
                          $ptt_pay_date = old('ptt_pay_date');
                      }
                    @endphp
                    <div class="form-group col-xs-12 col-md-4" >
                        <label>PTT còn lại</label>
                        <input type="text" class="form-control number" name="ptt_con_lai" id="ptt_con_lai" value="{{ old('ptt_con_lai', $detail->ptt_con_lai) }}" autocomplete="off">
                    </div>
                    <div class="form-group col-xs-12 col-md-4" >
                        <label>Ngày PTT thanh toán</label>
                        <input type="text" class="form-control datepicker" name="ptt_pay_date" id="ptt_pay_date" value="{{ $ptt_pay_date }}" autocomplete="off">
                    </div>
                  <div class="form-group col-xs-12 col-md-4">
                      <label>Người Chi tiền<span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_chi_tien" id="nguoi_chi_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_chi_tien', $detail->nguoi_chi_tien) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  </div>
                  @endif
                </div>

            </div>

            <div class="box-footer">
              <button type="button" id="btnSubmit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking-hotel.index')}}">Hủy</a>
            </div>

        </div>
        <!-- /.box -->

      </div>
      </div>
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
    $('#btnAddLocation').click(function(){
      $('.dia-diem-hidden:first').removeClass('dia-diem-hidden');
      $('.select2').select2();
    });
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
        url : '{{ route('booking-hotel.related')}}',
        type : 'GET',
        data: {
          hotel_id : $('#hotel_id').val()
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
<script type="text/javascript">
  $(document).ready(function(){
    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    });
    $('#ptt_tien_coc').blur(function(){
        var ptt_tien_coc = 0;
        if($('#ptt_tien_coc').val() != ''){
         ptt_tien_coc = parseInt($('#ptt_tien_coc').val());
        }
        var ptt_tong_tien_goc = parseInt($('#ptt_tong_tien_goc').val());
        $('#ptt_con_lai').val(ptt_tong_tien_goc - ptt_tien_coc);
    });
  });
</script>
@stop
