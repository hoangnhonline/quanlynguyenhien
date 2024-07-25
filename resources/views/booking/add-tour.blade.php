@extends('layout')
@section('content')
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      Đặt tour <span style="color:#f39c12">@if($tour_id == 4) CÂU MỰC @elseif($tour_id == 1) ĐẢO @elseif($tour_id == 3) RẠCH VẸM @endif
    </span></h1>
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
              <input type="hidden" name="customer_id" value="{{!empty($customerId) ? $customerId : ''}}">
              <div class="form-group">
                <label>Booking liên quan</label>
                <select class="form-control select2" id="related" multiple="multiple" name="related_id[]" >

                  @foreach($arrBooking as $booking)
                  <option value="{{ $booking->id }}">{{ Helper::showCode($booking) }} - {{ $booking->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="row" style="display: none;">
                  <div class="form-group col-md-6">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="is_grandworld" name="is_grandworld" value="1" {{ old('is_grandworld') == 1 ? "checked" : "" }}>
                        CHỤP GRAND WORLD
                      </label>
                  </div>
                  <div class="form-group col-md-6">
                        <input type="text" id="grandworld_date" autocomplete="off" class="form-control datepicker" name="grandworld_date" placeholder="Ngày chụp Grand World" value="{{ old('grandworld_date') }}">
                  </div>
              </div>
              <div class="row">
                  <div class="form-group col-md-4 col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="no_pickup" name="no_pickup" value="1" {{ old('no_pickup') == 1 ? "checked" : "" }}>
                        KHÔNG XE
                      </label>
                  </div>
                  <div class="form-group col-md-4 col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="ko_cap_treo" name="ko_cap_treo" value="1" {{ old('ko_cap_treo') == 1 ? "checked" : "" }}>
                        KHÔNG CÁP
                      </label>
                  </div>
                  <div class="form-group col-md-4 col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="is_vat" name="is_vat" value="1" {{ old('is_vat') == 1 ? "checked" : "" }}>
                        XUẤT VAT
                      </label>
                  </div>
              </div>
              <div class="row">
                <div class="form-group @if($tour_id != 4) col-xs-4 @else col-xs-12 @endif " style="padding-right: 0px">
                  <label>Tour<span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_id" name="tour_id">
                      @foreach($tourSystem as $tour)
                      <option value="{{ $tour->id }}" {{ old('tour_id', $tour_id) == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                      @endforeach
                  </select>
                </div>
                @if($tour_id != 4)
                <div class="form-group col-xs-4">
                  <label>Loại tour<span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_cate" name="tour_cate" >
                      <option value="1" {{ old('tour_cate') == 1 ? "selected" : "" }}>4 đảo</option>
                      <option value="2" {{ old('tour_cate') == 2 ? "selected" : "" }}>2 đảo</option>
                  </select>
                </div>
                <div class="form-group col-xs-4" style="padding-left: 0px;">
                  <label>Hình thức <span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_type" name="tour_type">
                      <option value="1" {{ old('tour_type') == 1 ? "selected" : "" }}>Tour ghép</option>
                      <option value="2" {{ old('tour_type') == 2 ? "selected" : "" }}>Tour VIP</option>
                      <option value="3" {{ old('tour_type') == 3 ? "selected" : "" }}>Thuê cano</option>
                  </select>
                </div>
                @endif
                </div>
               <div class="row">
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <div class="form-group col-xs-6">
                     <label>Sales <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="">--Chọn--</option>
                        @foreach($listUser as $user)
                        <option data-level="{{ $user->level }}" value="{{ $user->id }}" {{ old('user_id', $user_id_default) == $user->id ? "selected" : "" }}>{{ $user->name }} - {{ Helper::getLevel($user->level) }}</option>
                        @endforeach
                      </select>
                  </div>


                  <div class="form-group @if(Auth::user()->role == 1 && !Auth::user()->view_only) col-xs-6 @else col-xs-12 @endif">
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
                  <input type="hidden" name="book_date" value="">
                </div>

                <div class="row">
                  <div class="form-group col-md-3">
                    <label>Tên khách hàng <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', !empty($customer) ? $customer->name : '') }}">
                  </div>
                  <div class="form-group col-md-3 col-xs-4">
                    <label>Điện thoại <span class="red-star">*</span></label>
                    <input type="text" maxlength="20" class="form-control" name="phone" id="phone" value="{{ old('phone', !empty($customer) ? $customer->phone : '') }}">
                  </div>
                  <div class="form-group col-md-3 col-xs-4">
                    <label>Điện thoại 2 <span class="red-star">*</span></label>
                    <input type="text" maxlength="20" class="form-control" name="phone_1" id="phone_1" value="{{ old('phone_1', !empty($customer) ? $customer->phone_2 : '') }}">
                  </div>
                  <div class="form-group col-md-3 col-xs-4">
                    <label>SĐT sales</label>
                    <input type="text" maxlength="20" class="form-control" name="phone_sales" id="phone_sales" value="{{ old('phone_sales') }}">
                  </div>
                </div>
                <div class="row">
                   @if($tour_id != 4)
                <div class="form-group col-md-4">
                  <label>Facebook</label>
                  <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook') }}">
                </div>
                @endif
                  <div class="form-group col-md-4">
                    <label>Ngày đi <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ old('use_date') }}" autocomplete="off">
                  </div>
                  <div class="col-md-4 input-group" style="padding-left: 20px;padding-right: 20px">

                  <label>Nơi đón <span class="red-star">*</span></label>

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
                </div>

                <div class="row">
                  <div class="form-group col-xs-4">
                      <label>NL <span class="red-star">*</span></label>
                      <select class="form-control select2" name="adults" id="adults">
                        @for($i = 1; $i <= 150; $i++)
                        <option value="{{ $i }}" {{ old('adults') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>TE(< 1.4) <span class="red-star">*</span></label>
                      <select class="form-control select2" name="childs" id="childs">
                        @for($i = 0; $i <= 20; $i++)
                        <option value="{{ $i }}" {{ old('childs') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>EB(< 1m)</label>
                      <select class="form-control select2" name="infants" id="infants">
                        @for($i = 0; $i <= 20; $i++)
                        <option value="{{ $i }}" {{ old('infants') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>

                </div>
                 @if($tour_id != 4)
                <div class="row">
                  <div class="form-group col-xs-3">
                      <label>Ăn NL<span class="red-star">*</span></label>
                      <select class="form-control select2" name="meals" id="meals">
                        @for($i = 0; $i <= 150; $i++)
                        <option value="{{ $i }}" {{ old('meals') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Ăn TE<span class="red-star">*</span></label>
                      <select class="form-control select2" name="meals_te" id="meals_te">
                        @for($i = 0; $i <= 20; $i++)
                        <option value="{{ $i }}" {{ old('meals_te') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Cáp NL<span class="red-star">*</span></label>
                      <select class="form-control" name="cap_nl" id="cap_nl">
                        @for($i = 0; $i <= 150; $i++)
                        <option value="{{ $i }}" {{ old('cap_nl') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Cáp TE<span class="red-star">*</span></label>
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
                @endif
                <div class="row">
                  <input type="hidden" name="ngay_coc">
                  <div class="form-group col-md-7" style="display: none;">
                      <label>Thành tiền <span class="red-star">*</span></label>
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
                <div class="row">
                  <div class="form-group col-md-4 col-xs-4" >
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price') }}">
                  </div>
                  <div class="form-group col-md-4 col-xs-4">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc') }}">
                  </div>
                  <div class="form-group col-md-4 col-xs-4" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai') }}">
                  </div>


                </div>
                <div class="row">

                  <div class="form-group col-md-3 col-xs-6">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_coc') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-md-3 col-xs-6">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_tien') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                   <div class="form-group col-md-3 col-xs-6" >
                      <label>THỰC THU <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="tien_thuc_thu" id="tien_thuc_thu" value="{{ old('tien_thuc_thu') }}" style="border: 1px solid red">
                  </div>
                  <div class="form-group col-md-3 col-xs-6" >
                      <label>HDV thu hộ <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="hdv_thu" id="hdv_thu" value="{{ old('hdv_thu') }}" style="border: 1px solid red">
                  </div>
                </div>

                <div class="form-group" style="display: none;">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control select2" name="status" id="status">
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
                 @if($userRole == 1)
                <div class="row">
                  <div class="col-md-12 form-group">
                   <label>Gửi tour cho đối tác</label>
                   <select class="form-control select2" name="cty_send" >
                        <option value="">--Chọn--</option>
                        <option value="1" {{ old('cty_send') == 1  ? "selected" : "" }}>Rooty</option>
                        <option value="2" {{ old('cty_send') == 2  ? "selected" : "" }}>Funny</option>
                        <option value="3" {{ old('cty_send') == 3  ? "selected" : "" }}>Group Tour</option>
                        <option value="4" {{ old('cty_send') == 4  ? "selected" : "" }}>Nguyễn Hiền</option>
                        <option value="5" {{ old('cty_send') == 5  ? "selected" : "" }}>Phúc Thủy</option>
                    </select>
                  </div>
                </div>
                @endif
                <fieldset class="scheduler-border-2" style="padding: 10px !important; display: none;">
                  <legend class="scheduler-border">ĐÓN MIỄN PHÍ</legend>
                  <div class="row" style="margin-bottom: 10px">
                    <p class="col-md-12" style="color: red; font-style: italic;; font-weight: bold;">Đặt xe đón trong ngày vui lòng thông báo cho số hotline Phu Quoc Trans : 0911380111 sau khi tạo để được hỗ trợ tốt nhất.</p>
                    <input type="hidden" name="don_id" value="don_id">
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="don_location_id" id="don_location_id">
                          <option value="-">--Chọn điểm đi--</option>
                          @foreach($listTag as $location)
                          <option value="{{ $location->id }}" {{ old('don_location_id') == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="don_location_id_2" id="don_location_id_2">
                          <option value="-">--Chọn điểm đến--</option>
                          @foreach($listTag as $location)
                          <option value="{{ $location->id }}" {{ old('don_location_id_2') == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="don_car_cate_id" id="don_car_cate_id">
                          <option value="-">--Loại xe--</option>
                          @foreach($carCate as $cate)
                          <option value="{{ $cate->id }}" {{ old('don_car_cate_id') == $cate->id  ? "selected" : "" }}>{{ $cate->name }}</option>
                          @endforeach
                        </select>
                    </div>
                  </div>
                  <div class="row" style="margin-bottom: 10px">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control datepicker ngay-don-tien" name="don_ngay" id="don_ngay" autocomplete="off" placeholder="Ngày đón"  value="{{ old('don_ngay') }}">
                    </div>
                    <div class="col-md-6">
                        <select class="form-control-2 select2" name="don_gio" id="don_gio" style="width: 120px">
                            <option value="">--Giờ--</option>
                            @for($g = 1; $g <= 24; $g++)
                            <option value="{{ str_pad($g,2,"0", STR_PAD_LEFT) }}" {{ old('don_gio') == $g  ? "selected" : "" }}>{{ str_pad($g,2,"0", STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <select class="form-control-2 select2" name="don_phut" id="don_phut" style="width: 120px">
                            <option value="">--Phút--</option>
                            <option value="00" {{ old('don_phut') == 0  ? "selected" : "" }}>00</option>
                            <option value="15" {{ old('don_phut') == 15  ? "selected" : "" }}>15</option>
                            <option value="30" {{ old('don_phut') == 30  ? "selected" : "" }}>30</option>
                            <option value="45" {{ old('don_phut') == 45  ? "selected" : "" }}>45</option>
                        </select>
                    </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <textarea class="form-control" name="don_ghichu" id="don_ghichu" rows="4" placeholder="Ghi chú">{{ old('don_ghichu') }}</textarea>
                    </div>
                    </div>
              </fieldset>

              <fieldset class="scheduler-border-2" style="padding: 10px !important;margin-top: 20px !important; display: none;">
                  <legend class="scheduler-border">TIỄN MIỄN PHÍ</legend>

                  <div class="row" style="margin-bottom: 10px">
                    <p class="col-md-12" style="color: red; font-style: italic; font-weight: bold;">Đặt xe đón trong ngày vui lòng thông báo cho số hotline Phu Quoc Trans : 0911380111 sau khi tạo để được hỗ trợ tốt nhất.</p>
                    <input type="hidden" name="tien_id" value="tien_id">
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="tien_location_id" id="tien_location_id">
                          <option value="-">--Chọn điểm đi--</option>
                          @foreach($listTag as $location)
                          <option value="{{ $location->id }}" {{ old('tien_location_id') == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="tien_location_id_2" id="tien_location_id_2">
                          <option value="-">--Chọn điểm đến--</option>
                          @foreach($listTag as $location)
                          <option value="{{ $location->id }}" {{ old('tien_location_id_2') == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="tien_car_cate_id" id="tien_car_cate_id">
                          <option value="-">--Loại xe--</option>
                          @foreach($carCate as $cate)
                          <option value="{{ $cate->id }}" {{ old('tien_car_cate_id') == $cate->id  ? "selected" : "" }}>{{ $cate->name }}</option>
                          @endforeach
                        </select>
                    </div>
                  </div>
                  <div class="row" style="margin-bottom: 10px">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control datepicker ngay-don-tien" name="tien_ngay" id="tien_ngay"  autocomplete="off" placeholder="Ngày tiễn" value="{{ old('tien_ngay') }}">
                    </div>
                    <div class="col-md-6">
                        <select class="form-control-2 select2" name="tien_gio" style="width: 120px">
                            <option value="">Giờ</option>
                            @for($g = 1; $g <= 24; $g++)
                            <option value="{{ str_pad($g,2,"0", STR_PAD_LEFT) }}" {{ old('tien_gio') == $g  ? "selected" : "" }} >{{ str_pad($g,2,"0", STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <select class="form-control-2 select2" name="tien_phut" style="width: 120px">
                            <option value="">Phút</option>
                            <option value="00" {{ old('tien_phut') == 0  ? "selected" : "" }}>00</option>
                            <option value="15" {{ old('tien_phut') == 15  ? "selected" : "" }}>15</option>
                            <option value="30" {{ old('tien_phut') == 30  ? "selected" : "" }}>30</option>
                            <option value="45" {{ old('tien_phut') == 45  ? "selected" : "" }}>45</option>
                        </select>
                    </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <textarea class="form-control" name="tien_ghichu" id="tien_ghichu" rows="4" placeholder="Ghi chú">{{ old('tien_ghichu') }}</textarea>
                    </div>
                    </div>
              </fieldset>
            </div>

            <div class="box-footer">
              <button type="button" class="btn btn-default btn-sm" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i> Đang xử lý...</button>
              <button type="submit" id="btnSave" class="btn btn-primary btn-sm"  onclick="return checkToday();">Lưu</button>
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
<style type="text/css">
  fieldset.scheduler-border-2{
    border: 1px groove #ddd !important;
    padding: 0 5px 5px 5px !important;
    margin: 0 0 5px 0 !important;
    -webkit-box-shadow: 0px 0px 0px 0px #000;
    box-shadow: 0px 0px 0px 0px #000;
  }
</style>
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
    $('#related').select2({
      multiple: true
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

    $('#meals, #tien_coc, #discount, #extra_fee, #user_id').change(function(){
      var level = $("#user_id option:selected" ).data('level');
      console.log(level, levelLogin);
      if(level == 1 || levelLogin  == 1){
        setPrice();
      }
    });
    $('#adults, #childs').change(function(){
      if($('#ko_cap_treo').prop('checked') == true){
        $('#cap_nl').val(0);
        $('#cap_te').val(0);
      }else{
        $('#cap_nl').val($('#adults').val());
        $('#cap_te').val($('#childs').val());

      }
      var level = $("#user_id option:selected" ).data('level');
      console.log(level, levelLogin);
      if(level == 1 || levelLogin  == 1){
        setPrice();
      }
    });
    $('#tien_coc').blur(function(){
      var level = $("#user_id option:selected" ).data('level');
      console.log(level, levelLogin);
      if(level == 1 || levelLogin  == 1){
        setPrice();
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
      var level = $("#user_id option:selected" ).data('level');
      console.log(level, levelLogin);
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
    var cap_treo_lon = 400000;
    var cap_treo_nho = 260000;
    var tour_id = $('#tour_id').val();

    var ko_cap = $('#ko_cap_treo').is(':checked');
    var tour_price = 980000;
    var tour_price_child = 490000;
    console.log(ko_cap);
    if(tour_id == 3){ // RẠCH VẸM
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
  }
</script>
@stop
