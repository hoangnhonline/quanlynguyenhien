@extends('layout')
@section('content')
    <?php
    $is_disabled = ($detail->is_send == 1 && $detail->user_id_refer != Auth::user()->id && Auth::user()->role > 2) ? true : false;
    ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Khách hàng
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route( 'dashboard' ) }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('customer.index') }}">Khách hàng</a></li>
                <li class="active">Cập nhật</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <a class="btn btn-default btn-sm" href="{{ route('customer.index') }}"
               style="margin-bottom:5px">Quay lại</a>
            <a href="#" class="btn btn-success btn-sm btnCreateBooking" data-id="{{$detail->id}}" style="margin-bottom:5px">
                Tạo booking
            </a>
            <form role="form" method="POST" action="{{ route('customer.update') }}" id="dataForm">
                <div class="row">
                    <!-- left column -->
                    <input type="hidden" name="id" value="{{ $detail->id }}">
                    <div class="col-md-12">
                        <div id="content_alert"></div>
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                Chỉnh sửa
                            </div>
                            <!-- /.box-header -->
                            {!! csrf_field() !!}

                            <div class="box-body">
                                @if(Session::has('message'))
                                    <p class="alert alert-info">{{ Session::get('message') }}</p>
                                @endif
                                @if (count($errors) > 0)
                                    <div class="alerts alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if($detail->is_send == 1 && $detail->user_id_refer > 0)
                                    <div class="alerts alert-default" style="background-color: #ffe6e6; ">
                                        <h3 style="padding: 10px">Đã gửi cho <span class="noi-bat">{{ $detail->userRefer->name }} - {{ $detail->userRefer->phone }}</span>
                                            tư vấn vào lúc <span
                                                class="noi-bat">{{ date('d/m/Y H:i', strtotime($detail->time_send)) }}</span>
                                        </h3>
                                    </div>
                                @endif
                                @if($detail->bookings->count() > 0)
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <label>Danh sách booking</label>
                                            <table class="table table-primary">
                                                <tr>
                                                    <th>Mã Booking</th>
                                                    <th>Loại</th>
                                                    <th>Chi tiết</th>
                                                    <th>Tổng tiền</th>
                                                    <th>Ngày tạo</th>
                                                    <th></th>
                                                </tr>
                                                @foreach($detail->bookings as $booking)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('booking.edit', $booking->id) }}" target="_blank">
                                                                @switch($booking->type)
                                                                    @case(1)
                                                                        PTT{{ $booking->id }}
                                                                        @break
                                                                    @case(2)
                                                                        PTH{{ $booking->id }}
                                                                        @break
                                                                    @case(3)
                                                                        PTV{{ $booking->id }}
                                                                        @break
                                                                    @case(4)
                                                                        PTX{{ $booking->id }}
                                                                        @break
                                                                @endswitch
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @switch($booking->type)
                                                                @case(1)
                                                                    <span class="label label-success">Tour</span>
                                                                    @break
                                                                @case(2)
                                                                    <span class="label label-primary">Khách sạn</span>
                                                                    @break
                                                                @case(3)
                                                                    <span class="label label-warning">Vé vui chơi</span>
                                                                    @break
                                                                @case(4)
                                                                    <span class="label label-danger">Xe</span>
                                                                    @break
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            @switch($booking->type)
                                                                @case(1)
                                                                    @if($booking->tour_id)
                                                                        <label class="label" style="background-color:{{ $tourSystemName[$booking->tour_id]['bg_color'] }}">{{ $tourSystemName[$booking->tour_id]['name'] }}</label>
                                                                    @endif
                                                                    @if($booking->tour_cate == 2 && $booking->tour_id == 1)
                                                                        <label class="label label-info">2 đảo</label>
                                                                    @endif

                                                                    @if($booking->tour_type == 3)
                                                                        <label class="label label-warning">Thuê cano</label>
                                                                    @elseif($booking->tour_type == 2)
                                                                        <label class="label label-danger">Tour VIP</label>
                                                                    @endif
                                                                    @break
                                                                @case(2)
                                                                    <p style="font-weight: bold; color: #06b7a4">CI: {{ date('d/m', strtotime($booking->checkin)) }} - CO: {{ date('d/m', strtotime($booking->checkout)) }}</p>
                                                                    <strong>{{ $booking->adults }} NL</strong> / <strong>{{ $booking->childs }} TE</strong> / <strong>{{ $booking->infants }} EB</strong>
                                                                    <table class="table table-list-data-child" style="margin-top: 10px">
                                                                        <tr>
                                                                            <th colspan="3" style="padding-left: 0">
                                                                                {{ $booking->hotel->name }}
                                                                            </th>
                                                                        </tr>
                                                                        @foreach($booking->rooms as $r)
                                                                            @php
                                                                                if($r->original_price== 0){
                                                                                  $error_original_price  = true;
                                                                                }
                                                                            @endphp
                                                                            <tr>
                                                                                @if($r->room_id)
                                                                                    <td style="padding-left: 0">{{ $r->room->name }}</td>
                                                                                @else
                                                                                    <td>{{ $r->room_name }}</td>
                                                                                @endif
                                                                                <td>{{ $r->room_amount }}</td>
                                                                                <td>{{ number_format($r->price_sell) }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </table>
                                                                    @break
                                                                @case(3)
                                                                    <table class="table">
                                                                        <tr>
                                                                            <th width="35%">Loại vé</th>
                                                                            <th width="5%" class="text-center">SL</th>
                                                                            <th width="20%" class="text-right">Giá vốn</th>
                                                                            <th width="20%" class="text-right">Giá bán</th>
                                                                            <th width="20%" class="text-right">Lãi</th>
                                                                        </tr>
                                                                        @php
                                                                            $tong_hoa_hong = $cong_no_row = $tong_von = $tong_ban = $tong_lai = $tong_ve = $hoa_hong_hotline = 0;
                                                                            $ticketTypeArr = \App\Models\TicketTypeSystem::whereIn('status', [1, 2])->where('city_id', 1)->pluck('name', 'id')->toArray();
                                                                        @endphp
                                                                        @foreach($booking->tickets as $r)
                                                                            @php

                                                                                $hh = $r->amount*$r->price_sell - $r->amount*$r->price;
                                                                                if($booking->status != 3)
                                                                                {
                                                                                  $tong_hoa_hong+= $hh;
                                                                                  $tong_von += $tien_von = $r->amount*$r->price;
                                                                                  $tong_ban += $tien_ban = $r->amount*$r->price_sell;
                                                                                  $tong_lai += $hh;
                                                                                  $tong_ve += $r->amount;
                                                                                  if($booking->user_id == 18){
                                                                                    $hoa_hong_hotline += $hh/2;
                                                                                  }
                                                                                }
                                                                            @endphp
                                                                            <tr>
                                                                                <td>
                                                                                    @if(isset($ticketTypeArr[$r->ticket_type_id]))
                                                                                        {{ $ticketTypeArr[$r->ticket_type_id] }}
                                                                                    @else
                                                                                        {{ $r->ticket_type_id }}
                                                                                    @endif
                                                                                </td>

                                                                                <td class="text-center">
                                                                                    {{ $r->amount }}
                                                                                </td>
                                                                                <td class="text-right">
                                                                                    @if($booking->status != 3)
                                                                                        {{ number_format($r->price) }}
                                                                                        <br>
                                                                                        {{ number_format($tien_von) }}
                                                                                        @php
                                                                                            if($booking->nguoi_thu_tien == 2) $cong_no_row += ($r->amount*$r->price);
                                                                                        @endphp
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-right">
                                                                                    @if($booking->status != 3)
                                                                                        {{ number_format($r->price_sell) }}
                                                                                        <br>
                                                                                        {{ number_format($tien_ban) }}
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-right">
                                                                                    @if($booking->status != 3)
                                                                                        {{ number_format($hh) }}
                                                                                    @endif


                                                                                </td>

                                                                            </tr>
                                                                        @endforeach
                                                                    </table>

                                                                    @break
                                                                @case(4)
                                                                    {{ date('d/m', strtotime($booking->use_date)) }} - {{ $booking->time_pickup }}
                                                                    <br>
                                                                    <strong style="color: blue">{{ $booking->carCate->name }}</strong>
                                                                    <br>
                                                                    @foreach($booking->locationList as $lo)
                                                                        {{ $lo->location->name }}<br>
                                                                    @endforeach
                                                                    @break
                                                            @endswitch
                                                        </td>
                                                        <td>{{number_format($booking->total_price)}}</td>
                                                        <td>{{ date('d/m/y H:i', strtotime($booking->created_at)) }}</td>
                                                        <td>
                                                            @switch($booking->type)
                                                            @case(1)
                                                                <a href="{{ route('booking.edit', $booking->id) }}" target="_blank"
                                                                   class="btn btn-xs btn-primary"> Xem</a>
                                                                @break
                                                            @case(2)
                                                                <a href="{{ route('booking-hotel.edit', $booking->id) }}" target="_blank"
                                                                   class="btn btn-xs btn-primary"> Xem</a>
                                                                @break
                                                            @case(3)
                                                                <a href="{{ route('booking-ticket.edit', $booking->id) }}" target="_blank"
                                                                   class="btn btn-xs btn-primary"> Xem</a>
                                                                @break
                                                            @case(4)
                                                                <a href="{{ route('booking-car.edit', $booking->id) }}" target="_blank"
                                                                   class="btn btn-xs btn-primary"> Xem</a>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                    </tr>

                                                @endforeach
                                            </table>
                                            <hr/>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="form-group" >
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="ask_more" id="ask_more" value="1" {{ old('ask_more', $detail->ask_more) == 1 ? "checked" : "" }}>
                                                <span style="color:red; font-weight: bold">Tôi muốn biết thêm...</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-4" style="display: none">
                                        <label for="city_id">Nơi cần đi</label>
                                        <select class="form-control select2" name="city_id" id="city_id">
                                            <option value="">--Chọn--</option>
                                            @foreach($cityList as $city)
                                                <option
                                                    value="{{ $city->id }}" {{ old('city_id', $detail->city_id) == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @php
                                        $contact_date_hour = $contact_date_minute = "";
                                        if($detail->contact_date){
                                            $contact_date = old('contact_date', date('d/m/Y', strtotime($detail->contact_date)));
                                            $contact_date_hour = old('contact_date_hour', date('H', strtotime($detail->contact_date)));
                                            $contact_date_minute = old('contact_date_minute', date('i', strtotime($detail->contact_date)));
                                        }else{
                                            $contact_date = old('contact_date');
                                        }
                                    @endphp
                                    <div class="form-group col-xs-4">
                                        <label for="name">Họ tên</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                               value="{{ old('name', $detail->name) }}">
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label for="contact_date">Ngày liên hệ</label>
                                        <br>
                                        <input type="text" class="form-control-2 datepicker" id="contact_date"
                                               name="contact_date" value="{{ $contact_date }}" style="width: 120px"
                                               autocomplete="off">
                                        <select class="form-control-2 select2" name="contact_date_hour"
                                                style="width: 90px">
                                            <option value="">Giờ</option>
                                            @for($i = 0; $i < 24; $i++)
                                                <option
                                                    value="{{ $i }}" {{ $i == $contact_date_hour ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                        <select class="form-control-2 select2" name="contact_date_minute"
                                                style="width: 90px">
                                            <option value="">Phút</option>
                                            @for($i = 0; $i < 60; $i++)
                                                <option
                                                    value="{{ $i }}" {{ $i == $contact_date_minute ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-3">
                                        <label for="status">Trạng thái</label>
                                        <select class="form-control select2" name="status" id="status">
                                            <option
                                                value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>
                                                Đang tư vấn
                                            </option>
                                            <option
                                                value="6" {{ old('status', $detail->status) == 6 ? "selected" : "" }}>
                                                Sắp chốt
                                            </option>
                                            <option
                                                value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Đã
                                                chốt
                                            </option>
                                            <option
                                                value="3" {{ old('status', $detail->status) == 3 ? "selected" : "" }}>Đã
                                                hoàn thành
                                            </option>
                                            <option
                                                value="4" {{ old('status', $detail->status) == 4 ? "selected" : "" }}>
                                                Không chốt được
                                            </option>
                                            <option
                                                value="5" {{ old('status', $detail->status) == 5 ? "selected" : "" }}>
                                                Không có nhu cầu
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-1">
                                        <div class="checkbox" style="padding-top: 20px;">
                                            <label>
                                                <input type="checkbox" name="ads" id="ads" value="1" {{ old('ads', $detail->ads) ? "checked" : "" }}>
                                                <span style="color:red; font-weight: bold">ADS</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-xs-4" style="display: none">
                                        <div class="checkbox">
                                            @if($detail->is_send == 0)
                                                <label style="margin-top:25px;">
                                                    <input type="checkbox" name="is_send"
                                                           value="1" {{ old('is_send', $detail->is_send) == 1 ? "checked" : "" }}>
                                                    <span style="color:red; font-weight: bold;">GỬI TƯ VẤN</span>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                    <style>
                                        #ads_campaign_container .select2-container{
                                            width: 100% !important;
                                        }
                                    </style>
                                    <div class="row" style="{{old('ads', $detail->ads) ? '' : 'display:none'}}" id="ads_campaign_container">
                                        <div class="form-group col-xs-12">
                                            <label for="ads_campaign_id">Chiến dịch quảng cáo</label>
                                            <div style="width: 100%">
                                                <select class="form-control select2" name="ads_campaign_id" id="ads_campaign_id">
                                                    <option value="">-- Chọn chiến dịch --</option>
                                                    @foreach($adsCampaigns as $campaign)
                                                        <option
                                                            value="{{$campaign->id}}" {{ old('ads_campaign_id', $detail->ads_campaign_id) == $campaign->id ? "selected" : "" }}>{{$campaign->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <div class="row">
                                    <div class="form-group col-xs-4">
                                        <label for="address">Địa chỉ</label>
                                        <input type="text" class="form-control" name="address" id="address"
                                               value="{{ old('address', $detail->address) }}"
                                               placeholder="Địa chỉ/Khách hàng đến từ tỉnh/thành nào?"
                                               autocomplete="off">
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label for="phone">Số điện thoại</label>
                                        <input type="text" maxlength="10" class="form-control" name="phone" id="phone"
                                               value="{{ old('name', $detail->phone) }}">
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label for="phone">Số ĐT dự phòng</label>
                                        <input type="text" maxlength="10" class="form-control" name="phone_2"
                                               id="phone_2" value="{{ old('phone_2', $detail->phone_2) }}">
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="form-group col-xs-4">
                                        <label for="zalo">Zalo</label>
                                        <input type="text" class="form-control" name="zalo" id="zalo"
                                               value="{{ old('zalo', $detail->zalo) }}">
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" id="email"
                                               value="{{ old('email', $detail->email) }}">
                                    </div>
                                    @php
                                        if($detail->birthday){
                                            $birthday = old('birthday', date('d/m/Y', strtotime($detail->birthday)));
                                        }else{
                                            $birthday = old('birthday');
                                        }
                                    @endphp
                                    <div class="form-group col-xs-4">
                                        <label for="email">Ngày sinh</label>
                                        <input type="text" class="form-control datepicker" name="birthday" id="birthday"
                                               value="{{ $birthday }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xs-4">
                                        <label for="status">Nguồn</label>
                                        <select class="form-control select2" name="source" id="source">
                                            <option value="">-- Chọn nguồn --</option>
                                            @foreach($sources as $source)
                                                <option
                                                    value="{{$source->id}}" {{ old('source', $detail->source) == $source->id ? "selected" : "" }}>{{$source->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label for="status">Nguồn 2</label>
                                        <select class="form-control select2" name="source2" id="source2">
                                            <option value="">-- Chọn nguồn 2 --</option>
                                            @foreach($sources2 as $source2)
                                                <optgroup label="{{$source2->name}}" data-group="{{$source2->id}}">
                                                    @foreach($source2->childs as $source)
                                                        <option
                                                            value="{{$source->id}}" {{old('source2', $detail->source2) == $source->id ? "selected" : "" }} {{$source2->id != old('source', $detail->source) ? 'disabled=disabled' : ''}}>{{$source->name}}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label for="facebook">Link Facebook</label>
                                        <input type="text" class="form-control" name="facebook" id="facebook"
                                               value="{{ old('facebook', $detail->facebook) }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xs-6">
                                        <label for="status">Loại sản phẩm</label>
                                        <select class="form-control select2" name="product_type" id="product_type">
                                            <option value="">-- Chọn --</option>
                                            @php
                                                $types = ['1' => 'Tour', '2' => 'Combo', '3' => 'Khách sạn', '4' => 'Vé tham quan', '5'=> 'Xe']
                                            @endphp
                                            @foreach($types as $key => $type)
                                                <option
                                                    value="{{$key}}" {{ old('product_type', $detail->product_type) == $key ? "selected" : "" }}>{{$type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-6">
                                        <label for="status">Sản phẩm</label>
                                        @php
                                            $data = [];
                                            $selectedProductType = old('product_type', $detail->product_type);
                                            if(old('product_type', $detail->product_type)){
                                              switch ($selectedProductType){
                                                  case 1:
                                                      $data = \App\Models\TourSystem::all();
                                                      break;
                                                  case 2:
                                                      $data = \App\Models\Combo::all();
                                                      break;
                                                  case 3:
                                                      $data = \App\Models\Hotels::where('city_id', 1)->get();
                                                      break;
                                                  case 4:
                                                      $data = \App\Models\TicketCate::where('city_id', 1)->get();
                                                      break;
                                                  case 5:
                                                      $data = \App\Models\CarCate::where('type', 1)->get();
                                                      break;
                                              }
                                            }
                                        @endphp
                                        <select class="form-control select2" name="product_id" id="product_id">
                                            <option value="">-- Chọn --</option>
                                            @foreach($data as $item)
                                                <option
                                                    value="{{$item->id}}" {{ old('product_id', $detail->product_id) == $item->id ? "selected" : "" }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xs-4">
                                        <label>NL <span class="red-star">*</span></label>
                                        <select class="form-control select2" name="adults" id="adults">
                                            @for($i = 0; $i <= 150; $i++)
                                                <option
                                                    value="{{ $i }}" {{ old('adults', $detail->adults) == $i ? "selected" : "" }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label>TE(< 1.4) <span class="red-star">*</span></label>
                                        <select class="form-control select2" name="childs" id="childs">
                                            @for($i = 0; $i <= 20; $i++)
                                                <option
                                                    value="{{ $i }}" {{ old('childs', $detail->childs) == $i ? "selected" : "" }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label>EB(< 1m)</label>
                                        <select class="form-control select2" name="infants" id="infants">
                                            @for($i = 0; $i <= 20; $i++)
                                                <option
                                                    value="{{ $i }}" {{ old('infants', $detail->infants) == $i ? "selected" : "" }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="form-group col-xs-12">
                                        <label>Nhu cầu khách hàng</label>
                                        <textarea class="form-control ckeditor" rows="4" name="demand"
                                                  id="demand">{{ old('demand', $detail->demand) }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xs-12">
                                        <label>Ghi chú</label>
                                        <textarea class="form-control" rows="4" name="notes"
                                                  id="notes">{{ old('notes', $detail->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @if(!$is_disabled)
                                <div class="box-footer">
                                    <button type="submit" id="btnSubmit" class="btn btn-primary btn-sm">Lưu</button>
                                    <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm"
                                       href="{{ route('customer.index')}}">Hủy</a>
                                </div>
                            @endif

                        </div>
                        <!-- /.box -->

                    </div>
                </div>
            </form>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <div class="modal fade" id="henModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="text-align: left;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="henContent">

                </div>

            </div>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            @if($is_disabled)
            $('#dataForm .ckeditor').removeClass('ckeditor');
            $('#dataForm').removeAttr('action');
            $('#dataForm input, #dataForm select, #dataForm textarea, .select2, #btnSubmit').attr('disabled', 'disabled');
            @endif

            $('.btnCreateBooking').click(function(){
                var obj = $(this);
                $.ajax({
                    url : "{{ route('customer.create-booking') }}",
                    type : 'GET',
                    data : {
                        id : obj.data('id')
                    },
                    success: function(data){
                        $('#henContent').html(data);
                        $('#henModal').modal('show');
                        $('.select2').select2();
                        $(".datepicker").datepicker({
                            dateFormat: "dd/mm/yy",
                            changeMonth: true,
                            changeYear: true,
                            yearRange: '-100:+0'
                        });
                    }
                });
            });
            $('#ads').click(function () {
                if ($(this).is(':checked')) {
                    $('#ads_campaign_container').show();
                } else {
                    $('#ads_campaign_container').hide();
                }
            });

            $('#product_type').change(function (){
                var type = $(this).val();
                $.ajax({
                    url: '{{ route('customer.get-product') }}',
                    type: 'GET',
                    data: {type: type},
                    success: function (data) {
                        $('#product_id').val('');
                        $('#product_id').html('');
                        $('#product_id').append('<option value="">-- Chọn --</option>');
                        $.each(data, function (key, value) {
                            $('#product_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            })

            $('#source').change(function (){
                var val = $(this).val();
                $('#source2').find('optgroup option').prop('disabled', true);
                $('#source2').find('optgroup[data-group="'+ val +'"] option').prop('disabled', false);
                $('#source2').val('').select2()
            })
        });
    </script>
@stop
