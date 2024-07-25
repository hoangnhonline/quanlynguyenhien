@extends('layout')
@section('content')
<?php
$allowEdit = $detail->use_date >= $settingArr['date_block_booking'] || Auth::user()->id == 1 ? true : false;
?>
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
    @if($allowEdit)
    <a href="{{ route( 'booking-payment.index', ['booking_id' => $detail->id] ) }}" class="btn btn-danger btn-sm" style="margin-bottom:5px">Lịch sử thanh toán</a>
    @endif
    @endif
    @if($allowEdit)
    <a href="{{ route( 'booking-qrcode', ['booking_id' => $detail->id] ) }}" class="btn btn-info btn-sm" style="margin-bottom:5px">QR Code</a>
    @include('partials.booking-comments')
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


              </div>
              <input type="hidden" name="type" value="1">
              <div class="form-group">
                <label>Booking liên quan</label>
                <select class="form-control select2" id="related" name="related_id[]" multiple="multiple">

                  @foreach($arrBooking as $booking)
                  <option value="{{ $booking->id }}" {{ in_array($booking->id, $relatedIdArr) ? "selected" : "" }}>{{ Helper::showCode($booking) }} - {{ $booking->name }}</option>
                  @endforeach
                </select>
              </div>
              @if($userRole == 1)
              <div class="row">
                  <div class="form-group col-xs-6">
                    <label>HDV<span class="red-star">*</span></label>
                    <select class="form-control select2" id="hdv_id" name="hdv_id">
                      <option value="">--Chọn--</option>
                      @foreach($listHDV as $user)
                      <option value="{{ $user->id }}" {{ old('hdv_id', $detail->hdv_id) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-xs-6">
                    <label>CANO<span class="red-star">*</span></label>
                    <select class="form-control select2" name="cano_id">
                      <option value="">--Chọn--</option>
                      @foreach($canoList as $cano)
                      <option value="{{ $cano->id }}" {{ old('cano_id', $detail->cano_id) == $cano->id ? "selected" : "" }}>{{ $cano->name }}</option>
                      @endforeach
                    </select>
                  </div>
              </div>
              @endif
              <div class="row">
                  <div class="form-group col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="no_pickup" name="no_pickup" value="1" {{ old('no_pickup', $detail->no_pickup) == 1 ? "checked" : "" }}>
                        KHÔNG XE
                      </label>
                  </div>
                  <div class="form-group col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="ko_cap_treo" name="ko_cap_treo" value="1" {{ old('ko_cap_treo', $detail->ko_cap_treo) == 1 ? "checked" : "" }}>
                        KHÔNG CÁP
                      </label>
                  </div>
                  <div class="form-group col-md-4 col-xs-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="is_vat" name="is_vat" value="1" {{ old('is_vat', $detail->is_vat) == 1 ? "checked" : "" }}>
                        XUẤT VAT
                      </label>
                  </div>
              </div>
              <div class="row" style="display: none;">
                <!--  @if($detail->use_date > "2022-02-14")
                  <div class="form-group col-md-4">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="price_old" name="price_old" value="1" {{ old('price_old', $detail->price_old) == 1 ? "checked" : "" }}>
                        HỖ TRỢ GIÁ CŨ
                      </label>
                  </div>
                  @endif -->
                  <div class="form-group col-md-6">
                      <label style="font-weight: bold; color: red">
                        <input type="checkbox" id="is_grandworld" name="is_grandworld" value="1" {{ old('is_grandworld', $detail->is_grandworld) == 1 ? "checked" : "" }}>
                        CHỤP GRAND WORLD
                      </label>
                  </div>
                  @php
                    if($detail->grandworld_date){
                        $grandworld_date = old('grandworld_date', date('d/m/Y', strtotime($detail->grandworld_date)));
                    }else{
                        $grandworld_date = old('grandworld_date');
                    }
                  @endphp
                  <div class="form-group col-md-6">
                        <input type="text" id="grandworld_date" autocomplete="off" class="form-control datepicker" name="grandworld_date" placeholder="Ngày chụp Grand World" value="{{ $grandworld_date }}">

                  </div>
              </div><!--row-->

              <div class="row">
                <div class="form-group @if($detail->tour_id != 4) col-xs-4 @else col-xs-6 @endif ">
                  <label>Tour<span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_id" name="tour_id" @if($detail->tour_id == 4) readonly @endif>
                      @if($detail->source == 'website')
                          @foreach($tours as $tour)
                            <option value="{{ $tour->id }}" {{ old('tour_id', $detail->tour_id) == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                          @endforeach
                      @else
                          @foreach($tourSystem as $tour)
                              <option value="{{ $tour->id }}" {{ old('tour_id', $detail->tour_id) == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                          @endforeach
                      @endif
                  </select>
                </div>
                 @if($detail->tour_id == 1)
                <div class="form-group col-xs-4">
                  <label>Loại tour<span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_cate" name="tour_cate" >
                      <option value="1" {{ old('tour_cate', $detail->tour_cate) == 1 ? "selected" : "" }}>4 đảo</option>
                      <option value="2" {{ old('tour_cate', $detail->tour_cate) == 2 ? "selected" : "" }}>2 đảo</option>
                  </select>
                </div>
                @endif

                <div class="form-group @if($detail->tour_id != 4) col-md-4 @else col-md-6 @endif col-xs-4">
                  <label>Hình thức <span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_type" name="tour_type">
                      <option value="1" {{ old('tour_type', $detail->tour_type) == 1 ? "selected" : "" }}>Tour ghép</option>
                      <option value="2" {{ old('tour_type', $detail->tour_type) == 2 ? "selected" : "" }}>Tour VIP</option>
                      <option value="3" {{ old('tour_type', $detail->tour_type) == 3 ? "selected" : "" }}>Thuê cano</option>
                  </select>
                </div>

                </div><!--row-->
              <div class="row">
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <div class="form-group col-md-6 col-xs-6">
                     <label>Sales <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="0">--Chọn--</option>
                        @foreach($listUser as $user)
                        <option data-level="{{ $user->level }}" value="{{ $user->id }}" {{ old('user_id', $detail->user_id) == $user->id ? "selected" : "" }}>{{ $user->name }} - {{ Helper::getLevel($user->level) }}</option>
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
                </div>  <!--row-->
              <div class="row">
                <div class="form-group col-xs-6">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control select2" name="status" id="status">
                        <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>Mới</option>
                        <option value="4" {{ old('status', $detail->status) == 4 ? "selected" : "" }}>Đã tiếp nhận</option>
                        <option value="5" {{ old('status', $detail->status) == 5 ? "selected" : "" }}>Đang triển khai</option>
                        <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Hoàn tất</option>
                        <option value="3" {{ old('status', $detail->status) == 3 ? "selected" : "" }}>Hủy</option>
                      </select>
                  </div>

                @if(Auth::user()->id == 1)


                  <div class="form-group col-xs-6" style="display: none;">
                      <label>Hoa hồng CTY</label>
                      <input type="text" name="hoa_hong_cty" id="hoa_hong_cty" class="form-control number" value="{{ old('hoa_hong_cty', $detail->hoa_hong_cty) }}">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Hoa hồng sales</label>
                      <input type="text" name="hoa_hong_sales" id="hoa_hong_sales" class="form-control number" value="{{ old('hoa_hong_sales', $detail->hoa_hong_sales) }}">
                  </div>
                @endif
                </div><!--row -->
                <div class="row">
                  <div class="form-group col-md-3">
                    <label>Tên khách hàng <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $detail->name) }}">
                  </div>
                  <div class="form-group col-md-3 col-xs-4">
                    <label>Điện thoại <span class="red-star">*</span></label>
                    <input type="text" maxlength="20" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                  </div>
                  <div class="form-group col-md-3 col-xs-4">
                    <label>Điện thoại 2 <span class="red-star">*</span></label>
                    <input type="text" maxlength="20" class="form-control" name="phone_1" id="phone_1" value="{{ old('phone_1', $detail->phone_1) }}">
                  </div>
                  <div class="form-group col-md-3 col-xs-4">
                    <label>SĐT sales</label>
                    <input type="text" maxlength="20" class="form-control" name="phone_sales" id="phone_sales" value="{{ old('phone_sales', $detail->phone_sales) }}">
                  </div>
                </div>
                <div class="row">
                  @if($detail->tour_id != 4)
                <div class="form-group col-md-12">
                  <label>Facebook</label>
                  <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook', $detail->facebook) }}">
                </div>
                @endif
                @php
                    if($detail->use_date){
                        $use_date = old('use_date', date('d/m/Y', strtotime($detail->use_date)));
                    }else{
                        $use_date = old('use_date');
                    }
                  @endphp

                  <div class="form-group col-md-6">
                    <label>Ngày đi <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ $use_date }}" autocomplete="off">
                  </div>
                  <div class="col-md-6 input-group" style="padding-left: 20px;padding-right: 20px">
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
                @if($detail->tour_id != 4)
                <div class="row">
                  <div class="form-group col-xs-3">
                      <label>Ăn NL <span class="red-star">*</span></label>
                      <select class="form-control select2" name="meals" id="meals">
                        @for($i = 0; $i <= 150; $i++)
                        <option value="{{ $i }}" {{ old('meals', $detail->meals) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-3">
                      <label>Ăn TE <span class="red-star">*</span></label>
                      <select class="form-control select2" name="meals_te" id="meals_te">
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
                        <option value="{{ $i }}" {{ old('cap_nl', $detail->cap_te) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-7" style="display: none;">
                      <label>Thành tiền <span class="red-star">*</span></label>
                      <input type="text" name="total_price_adult" id="total_price_adult" class="form-control number" value="{{ old('total_price_adult', $detail->total_price_adult) }}">
                  </div>
                </div>
                @endif
                <div class="row">

                  <div class="form-group col-xs-7" style="display: none;">
                      <label>Thành tiền <span class="red-star">*</span></label>
                      <input type="text" name="total_price_child" id="total_price_child" class="form-control number" value="{{ old('total_price_child', $detail->total_price_child) }}">
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-6">
                      <label>Phụ thu đón</label>
                      <input type="text" name="extra_fee" id="extra_fee" class="form-control number" value="{{ old('extra_fee', $detail->extra_fee) }}">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Giảm giá</label>
                      <input type="text" name="discount" id="discount" class="form-control number" value="{{ old('discount', $detail->discount) }}">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-4">
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price', $detail->total_price) }}">
                  </div>
                  <div class="form-group col-xs-4" style="padding-right: 0px">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc', $detail->tien_coc) }}">
                  </div>
                  <div class="form-group col-xs-4" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai', $detail->con_lai) }}">
                  </div>

                </div>
                <div class="row">


                  <div class="form-group col-md-3 col-xs-6" style="padding-right: 0px">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_coc', $detail->nguoi_thu_coc) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-md-3 col-xs-6">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-md-3 col-xs-6" >
                      <label>THỰC THU<span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="tien_thuc_thu" id="tien_thuc_thu" value="{{ old('tien_thuc_thu', $detail->tien_thuc_thu) }}" style="border: 1px solid red">
                  </div>
                  <div class="form-group col-md-3 col-xs-6" >
                      <label>HDV Thu Hộ</label>
                      <input type="text" class="form-control number" name="hdv_thu" id="hdv_thu" value="{{ old('hdv_thu', $detail->hdv_thu) }}" style="border: 1px solid red">
                  </div>
                </div>

                @php
                  if($detail->book_date){
                      $book_date = old('book_date', date('d/m/Y', strtotime($detail->book_date)));
                  }else{
                      $book_date = old('book_date');
                  }
                @endphp
                <input type="hidden" class="form-control datepicker" name="book_date" id="book_date" value="{{ $book_date }}" autocomplete="off">
                <div class="form-group" style="display: none;">
                  <label>Danh sách khách</label>
                  <textarea class="form-control" rows="6" name="danh_sach" id="danh_sach">{{ old('danh_sach', $detail->danh_sach) }}</textarea>
                </div>
                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="6" name="notes" id="notes">{{ old('notes', $detail->notes) }}</textarea>
                </div>
                @if($userRole == 1)
                <div class="row">
                  <div class="col-md-12 form-group">
                   <label>Gửi tour cho đối tác</label>
                   <select class="form-control select2" name="cty_send" >
                        <option value="">--Chọn--</option>
                        <option value="1" {{ old('cty_send', $detail->cty_send) == 1  ? "selected" : "" }}>Rooty</option>
                        <option value="2" {{ old('cty_send', $detail->cty_send) == 2  ? "selected" : "" }}>Funny</option>
                        <option value="3" {{ old('cty_send', $detail->cty_send) == 3  ? "selected" : "" }}>Group Tour</option>
                        <option value="4" {{ old('cty_send', $detail->cty_send) == 4  ? "selected" : "" }}>Nguyễn Hiền</option>
                        <option value="5" {{ old('cty_send', $detail->cty_send) == 5  ? "selected" : "" }}>Phúc Thủy</option>
                    </select>
                  </div>
                </div>
                @endif
                <fieldset class="scheduler-border-2" style="padding: 10px !important; display: none;">
                  <legend class="scheduler-border">ĐÓN MIỄN PHÍ</legend>
                  <input type="hidden" name="don_id" value="{{ $arrDonTien['don_id'] }}">
                  <div class="row" style="margin-bottom: 10px">
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="don_location_id" id="don_location_id">
                          <option value="-">--Chọn điểm đi--</option>
                          @foreach($listTag as $location)
                          <option value="{{ $location->id }}" {{ old('don_location_id', $arrDonTien['don_location_id']) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="don_location_id_2" id="don_location_id_2">
                          <option value="-">--Chọn điểm đến--</option>
                          @foreach($listTag as $location)
                          <option value="{{ $location->id }}" {{ old('don_location_id_2', $arrDonTien['don_location_id_2']) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="don_car_cate_id" id="don_car_cate_id">
                          <option value="-">--Loại xe--</option>
                          @foreach($carCate as $cate)
                          <option value="{{ $cate->id }}" {{ old('don_car_cate_id', $arrDonTien['don_car_cate_id']) == $cate->id  ? "selected" : "" }}>{{ $cate->name }}</option>
                          @endforeach
                        </select>
                    </div>
                  </div>
                  <div class="row" style="margin-bottom: 10px">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control datepicker ngay-don-tien" name="don_ngay" id="don_ngay" autocomplete="off" placeholder="Ngày đón"  value="{{ old('don_ngay', $arrDonTien['don_ngay']) }}">
                    </div>
                    <div class="col-md-6">
                        <select class="form-control-2 select2" name="don_gio" id="don_gio" style="width: 120px">
                            <option value="">--Giờ--</option>
                            @for($g = 1; $g <= 24; $g++)
                            <option value="{{ str_pad($g,2,"0", STR_PAD_LEFT) }}" {{ old('don_gio', $arrDonTien['don_gio']) == $g  ? "selected" : "" }}>{{ str_pad($g,2,"0", STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <select class="form-control-2 select2" name="don_phut" id="don_phut" style="width: 120px">
                            <option value="">--Phút--</option>
                            <option value="00" {{ old('don_phut', $arrDonTien['don_phut']) == 0  ? "selected" : "" }}>00</option>
                            <option value="15" {{ old('don_phut', $arrDonTien['don_phut']) == 15  ? "selected" : "" }}>15</option>
                            <option value="30" {{ old('don_phut', $arrDonTien['don_phut']) == 30  ? "selected" : "" }}>30</option>
                            <option value="45" {{ old('don_phut', $arrDonTien['don_phut']) == 45  ? "selected" : "" }}>45</option>
                        </select>
                    </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <textarea class="form-control" name="don_ghichu" id="don_ghichu" rows="4" placeholder="Ghi chú">{{ old('don_ghichu', $arrDonTien['don_ghichu']) }}</textarea>
                    </div>
                    </div>
              </fieldset>

              <fieldset class="scheduler-border-2" style="padding: 10px !important;margin-top: 20px !important; display: none;">
                  <legend class="scheduler-border">TIỄN MIỄN PHÍ</legend>
                  <input type="hidden" name="tien_id" value="{{ $arrDonTien['tien_id'] }}">
                  <div class="row" style="margin-bottom: 10px">
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="tien_location_id" id="tien_location_id">
                          <option value="-">--Chọn điểm đi--</option>
                          @foreach($listTag as $location)
                          <option value="{{ $location->id }}" {{ old('tien_location_id', $arrDonTien['tien_location_id']) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="tien_location_id_2" id="tien_location_id_2">
                          <option value="-">--Chọn điểm đến--</option>
                          @foreach($listTag as $location)
                          <option value="{{ $location->id }}" {{ old('tien_location_id_2', $arrDonTien['tien_location_id_2']) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select class="form-control select2" name="tien_car_cate_id" id="tien_car_cate_id">
                          <option value="-">--Loại xe--</option>
                          @foreach($carCate as $cate)
                          <option value="{{ $cate->id }}" {{ old('tien_car_cate_id', $arrDonTien['tien_car_cate_id']) == $cate->id  ? "selected" : "" }}>{{ $cate->name }}</option>
                          @endforeach
                        </select>
                    </div>
                  </div>
                  <div class="row" style="margin-bottom: 10px">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control datepicker ngay-don-tien" name="tien_ngay" id="tien_ngay"  autocomplete="off" placeholder="Ngày tiễn" value="{{ old('tien_ngay', $arrDonTien['tien_ngay']) }}">
                    </div>
                    <div class="col-md-6">
                        <select class="form-control-2 select2" name="tien_gio" style="width: 120px">
                            <option value="">--Giờ--</option>
                            @for($g = 1; $g <= 24; $g++)
                            <option value="{{ str_pad($g,2,"0", STR_PAD_LEFT) }}" {{ old('tien_gio', $arrDonTien['tien_gio']) == $g  ? "selected" : "" }} >{{ str_pad($g,2,"0", STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <select class="form-control-2 select2" name="tien_phut" style="width: 120px">
                            <option value="">--Phút--</option>
                            <option value="00" {{ old('tien_phut', $arrDonTien['tien_phut']) == 0  ? "selected" : "" }}>00</option>
                            <option value="15" {{ old('tien_phut', $arrDonTien['tien_phut']) == 15  ? "selected" : "" }}>15</option>
                            <option value="30" {{ old('tien_phut', $arrDonTien['tien_phut']) == 30  ? "selected" : "" }}>30</option>
                            <option value="45" {{ old('tien_phut', $arrDonTien['tien_phut']) == 45  ? "selected" : "" }}>45</option>
                        </select>
                    </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <textarea class="form-control" name="tien_ghichu" id="tien_ghichu" rows="4" placeholder="Ghi chú">{{ old('tien_ghichu', $arrDonTien['tien_ghichu']) }}</textarea>
                    </div>
                    </div>

              </fieldset>

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
    <!-- /.row -->
    @else
    <h4 style="color: red; font-style: italic;">Booking đã hoàn tất, bạn không được chỉnh sửa!</h4>
    @endif
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
