@extends('layout')
@section('content')
<div class="content-wrapper">

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="text-transform: uppercase;">

    Đặt khách sạn

  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking-hotel.index') }}">

    Đặt khách sạn
   </a></li>
    <li class="active">Danh sách</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div id="content_alert"></div>
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      @if(Auth::user()->hotline_team == 0)
      <a href="{{ route('booking-hotel.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      @endif
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking-hotel.index') }}" id="searchForm">
            <div class="form-group">
              <select class="form-control select2" name="city_id" id="city_id">
                <option value="">--Tỉnh/thành--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}" {{ $arrSearch['city_id'] == $city->id  ? "selected" : "" }}>{{ $city->name }}
                </option>
                @endforeach
              </select>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="ptt_ngay_coc" value="{{ $arrSearch['ptt_ngay_coc'] }}" style="width: 110px; color: red" placeholder="PTT ngày cọc">
            </div>
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="ptt_pay_date" value="{{ $arrSearch['ptt_pay_date'] }}" style="width: 110px; color: red" placeholder="PTT ngày TT">
            </div>
            <div class="form-group">
              <select class="form-control select2" name="ptt_pay_status" id="ptt_pay_status">
                <option value="-1" {{ $arrSearch['ptt_pay_status'] == -1 ? "selected" : "" }}>--PTT TT--</option>
                <option value="0" {{ $arrSearch['ptt_pay_status'] == 0 ? "selected" : "" }}>PTT chưa TT</option>
                <option value="1" {{ $arrSearch['ptt_pay_status'] == 1 ? "selected" : "" }}>PTT đã cọc</option>
                <option value="2" {{ $arrSearch['ptt_pay_status'] == 2 ? "selected" : "" }}>PTT đã TT</option>
              </select>
            </div>
            @endif
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" value="{{ $arrSearch['id_search'] }}" style="width: 70px"  placeholder="PTH ID">
            </div>
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="vat_code" placeholder="VAT CODE" value="{{ $arrSearch['vat_code'] }}" style="width: 120px">
            </div>
            <div class="form-group">
            <select id="search_by" name="search_by" class="form-control select2">
              <option value="">--Tìm theo--</option>
              <option value="checkin" {{ $arrSearch['search_by'] == 'checkin' ? "selected" : "" }}>Ngày checkin</option>
              <option value="checkout" {{ $arrSearch['search_by'] == 'checkout' ? "selected" : "" }}>Ngày checkout</option>
              <option value="book_date" {{ $arrSearch['search_by'] == 'book_date' ? "selected" : "" }}>Ngày đặt</option>
            </select>
          </div>
          <div class="form-group">
                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
            </div>
            @if($city_id == 1)
            <div class="form-group">
              <select class="form-control select2" name="hotel_book" id="hotel_book">
                <option value="">--Đối tác--</option>
                @foreach($partnerList as $hotel)
                <option value="{{ $hotel->id }}" {{ $arrSearch['hotel_book'] == $hotel->id ? "selected" : "" }}>{{ $hotel->name }}</option>
                @endforeach
              </select>
            </div>
            @endif

            <div class="form-group">
              <select class="form-control select2" name="hotel_id" id="hotel_id">
                <option value="">--Khách sạn--</option>
                @foreach($hotelList as $hotel)
                <option value="{{ $hotel->id }}" {{ $arrSearch['hotel_id'] == $hotel->id ? "selected" : "" }}>{{ $hotel->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                <option value="">--Thu cọc--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_coc'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach

              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                <option value="">--Thu tiền--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_tien'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>
            @if($userRole == 1)

            <div class="form-group">
              <select class="form-control select2" name="nguoi_chi_coc" id="nguoi_chi_coc">
                <option value="">--Chi cọc--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_chi_coc'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach

              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="nguoi_chi_tien" id="nguoi_chi_tien">
                <option value="">--Chi tiền--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_chi_tien'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="-1" {{ $arrSearch['user_id'] == -1 ? "selected" : "" }}>--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
            <select class="form-control select2" name="level" id="level">
              <option value="" >--Phân loại sales--</option>
              <option value="1" {{ $level == 1 ? "selected" : "" }}>CTV Group</option>
              <option value="2" {{ $level == 2 ? "selected" : "" }}>ĐỐI TÁC</option>
              <option value="6" {{ $level == 6 ? "selected" : "" }}>NV SALES</option>
              <option value="7" {{ $level == 7 ? "selected" : "" }}>GỬI BẾN</option>
            </select>
          </div>
            @endif
            @if($city_id == 1)
            <div class="form-group">
              <select class="form-control select2" name="ctv_id" id="ctv_id">
                <option value="">--Người book--</option>
                @foreach($ctvList as $ctv)
                <option value="{{ $ctv->id }}" {{ $arrSearch['ctv_id'] == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                @endforeach
              </select>
            </div>
            @endif
            <div class="form-group">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" style="width: 120px" maxlength="11" placeholder="Điện thoại">
            </div>
            <input type="hidden" name="sort_by" id="sort_by" value="{{ $arrSearch['sort_by'] }}">
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <div>
              <div class="form-group">
              <input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">Mới</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn Tất</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_4" {{ in_array(4, $arrSearch['status']) ? "checked" : "" }} value="4">
              <label for="status_4">Dời ngày</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="hh0" id="hh0" {{ $arrSearch['hh0'] == 1 ? "checked" : "" }} value="1">
              <label for="hh0">Chưa tính HH&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
              <div class="form-group" style="float: right;">
                &nbsp;&nbsp;&nbsp;<input type="checkbox"name="unc0" id="unc0" {{ $arrSearch['unc0'] == 1 ? "checked" : "" }} value="1">
                <label for="unc0">Chưa check <span style="color: red">UNC</span></label>
              </div>
              @endif
              <div class="form-group filter" style="">
              <input type="checkbox" name="is_vat" id="is_vat" {{ $arrSearch['is_vat'] == 1 ? "checked" : "" }} value="1">
              <label for="is_vat">VAT</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="error" id="error" {{ $arrSearch['error'] == 1 ? "checked" : "" }} value="1">
              <label for="error">Error&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>

            </div>
          </form>
          <div class="form-group" style="float: right">
            <a href="javascript:;" class="btn btn-success btn-sm" id="btnContentNop">LẤY ND NỘP TIỀN</a>
            <a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a>
          </div>

        </div>
      </div>
      <div class="panel">
        <div class="panel-body">
          @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="table-responsive" style="margin-top: 20px;">
          <table class="table table-bordered table-hover">
            <tr style="background-color: #ffff99">
              <th></th>
              @foreach($collecterList as $col)
              @if(!in_array($col->id, [9, 10, 11]))
              <th class="text-right">{{ $collecterNameArr[$col->id] }}</th>
              @endif
              @endforeach
              <th class="text-right">Tổng</th>
            </tr>
            <tr>
              <th>Tiền cọc</th>
              @foreach($collecterList as $col)
              @if(!in_array($col->id, [9, 10, 11]))
              <td class="text-right">{{ isset($arrThuCoc[$col->id]) && $arrThuCoc[$col->id] > 0 ? number_format($arrThuCoc[$col->id]) : '-' }}</td>
              @endif
              @endforeach
              <td class="text-right">
                {{ number_format($tong_coc) }}
              </td>
            </tr>
            <tr>
              <th>Tiền thu còn lại</th>
              @foreach($collecterList as $col)
              @if(!in_array($col->id, [9, 10, 11]))
              <td class="text-right">{{ isset($arrThuTien[$col->id]) && $arrThuTien[$col->id] > 0 ? number_format($arrThuTien[$col->id]) : '-' }}</td>
              @endif
              @endforeach
              <td class="text-right">
                {{ number_format($tong_thuc_thu) }}
              </td>
            </tr>
          </table>
        </div>
        @endif
          <ul style="padding: 0px;">
          @foreach($userArr as $user_id)
          <li style="display: inline;
    float: left;
    list-style: none; height: 45px;">
          @if(isset($arrUser[$user_id]))
          <span data-id="" class="label label-default" style="padding: 10px 5px;margin-right: 10px; font-size: 12px">{{ $arrUser[$user_id]->name }}</span>

          </li>
          @endif
          @endforeach
        </ul>
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title col-md-8">Danh sách ( <span class="value">{{ $items->total() }} booking )</span> - Hoa hồng cty : {{ number_format($tong_hoa_hong_cty) }} - Hoa hồng sales : {{ number_format($tong_hoa_hong_sales) }}
          </h3>
          <div class="col-md-4 text-right form-inline">
            <div class="form-group">
            <label>Sắp xếp theo: </label>
            <select id="sort_by_change" class="form-control">
              <option value="created_at" {{ $arrSearch['sort_by'] == 'created_at' ? "selected" : "" }}>Ngày Book</option>
              <option value="checkin" {{ $arrSearch['sort_by'] == 'checkin' ? "selected" : "" }}>Ngày Check-in</option>
            </select>
          </div>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
          <table class="table table-bordered" id="table-list-data">
            <tr>
              @if(!Auth::user()->view_only)
              <th style="width: 1%" class="text-center" ><input type="checkbox" id="check_all" value="1"></th>
              @endif
              <th style="width: 1%; white-space: nowrap;">PTT CODE<br>Ngày book</th>
              <th width="200">Tên KH / Điện thoại / Email</th>
              <th>Khách sạn</th>
              <th>Giá tiền</th>
              <th width="1%;white-space:nowrap">Email</th>
              @if(!Auth::user()->view_only)
              <th width="1%;white-space:nowrap">Thao tác</th>
              @endif
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}"
                @php
                if($item->checkin < date('Y-m-d') && $item->hoa_hong_cty <= 0 && $item->user_id != 18 && $item->status == 1){
                  echo 'style="background-color:#ffe6e6"';
                }
                @endphp

                @if($item->checkin < date('Y-m-d'))
                class="checked_in"
                @endif

                data-id="{{ $item->id }}"
                >
                @if(!Auth::user()->view_only)
                <td class="text-center" style="line-height: 30px">

                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">

                </td>
                 @endif
                <td><span class="order"><strong style="color: red;font-size: 16px">@if($item->id > 6196) PTH{{ $item->id }} @else PTT{{ $item->id }} @endif</strong></span><br>
                {{ date('d/m/y', strtotime($item->book_date)) }}

                @if($item->is_vat == 1)
                  <p style="margin-top: 15px;"> VAT CODE: <strong>{{ $item->vat_code }}</strong></p>
                  @endif

              </td>
                <td>
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @elseif($item->status == 4)
                  <span class="label label-warning">DỜI NGÀY</span>
                  @endif
                  <br>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a style="font-size:17px" href="{{ route( 'booking-hotel.edit', $arrEdit) }}">{{ $item->name }}</a>
                  <br>
                  ĐT: {{ $item->phone }}
                  <br>
                  {{ $item->email }}<br>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                    @if($item->user)
                      Sales: {{ $item->user->name }}
                    @endif
                  @endif
                  @if($item->ctv)
                    - {{ $item->ctv->name }}
                  @endif

                      @if($item->source == 'website')
                          <br>
                          <span style="color: red">
                            <i class="glyphicon glyphicon-globe"></i>
                            <i>
                                Từ website
                            </i>
                        </span>
                      @endif
                   <p class="alert alert-danger" id="error_unc_{{ $item->id }}" style="display: none;"></p>
                </td>

                <td>
                  <p style="font-weight: bold; color: #06b7a4">CI: {{ date('d/m', strtotime($item->checkin)) }} - CO: {{ date('d/m', strtotime($item->checkout)) }}</p>
                  <strong>{{ $item->adults }} NL</strong> / <strong>{{ $item->childs }} TE</strong> / <strong>{{ $item->infants }} EB</strong>
                  <?php
                  $hotel_book = 'Trực tiếp KS';
                  if($item->hotelBook){
                    $hotel_book = $item->hotelBook->name;
                  }

                  ?>

                  @php
                  $error_original_price  = false;
                  @endphp
                  @if($item->hotel)
                  <div class="table-responsive" style="margin-top: 10px;">
                    <table class="table table-list-data-child">
                      <tr>
                        <th colspan="3">
                          {{ $item->hotel->name }} - Book: <span style="color:red">{{ $hotel_book }}</span>
                        </th>
                      </tr>
                      @foreach($item->rooms as $r)
                      @php
                      if($r->original_price== 0){
                        $error_original_price  = true;
                      }
                      @endphp
                      <tr>
                        @if($r->room_id)
                        <td>{{ $r->room->name }}</td>
                        @else
                        <td>{{ $r->room_name }}</td>
                        @endif
                        <td>{{ number_format($r->original_price) }}</td>
                        <td>{{ number_format($r->price_sell) }}</td>
                      </tr>
                      @endforeach
                    </table>
                  </div>
                  @endif
                  <span style="color:red">{{ $item->notes_hotel }}</span>
                  @if($item->notes)
                  <br> <span style="color: #f39c12; font-style: italic">{{ $item->notes }}</span>
                  @endif

                  @if($item->payment->count() > 0)
                  <div style="background-color: #ccc; padding: 5px;">
                    @foreach($item->payment as $p)
                    @if($p->type == 1)
                    <img src="{{ Helper::showImageNew($p->image_url)}}" width="80" style="border: 1px solid red" class="img-unc" >
                    @else
                      @if($p->sms)
                      <p class="alert-success sms">{{$p->sms}}</p>
                      @endif<br>
                    @endif
                    @endforeach
                  </div>
                  @endif
                </td>

                <td class="text-left" style="white-space: nowrap;">
                  <table class="table table-list-data-child">
                        <tr>
                          <td>Tổng tiền</td>
                          <td class="text-right">{{ number_format($item->total_price) }}</td>
                        </tr>
                        <tr>
                          <td>Tiền cọc</td>
                          <td class="text-right">{{ number_format($item->tien_coc) }}</td>
                        </tr>
                        <tr>
                          <td>Còn lại</td>
                          <td class="text-right"><label class="label label-warning" style="font-size: 12px;">{{ number_format($item->con_lai) }}</label></td>
                        </tr>
                    </table>

                  <?php
                    $error_original_price  = false;
                    $total_original_price = 0;
                    if($item->ptt_tong_tien_phong > 0){
                      $total_original_price = $item->ptt_tong_tien_phong;
                    }else{
                      foreach($item->rooms as $r){

                        $total_original_price += $r->original_price*$r->room_amount*$r->nights;
                        if($r->original_price== 0){
                          $error_original_price  = true;
                        }
                      }

                    }
                  ?>
                  <div style="background-color: #fffae6">
                    <?php
                    if($item->ptt_tong_tien_goc > 0){
                      $ptt_tong_tien_goc = $item->ptt_tong_tien_goc;
                    }else{
                      $ptt_tong_tien_goc = $total_original_price + $item->ptt_tong_phu_thu;
                    }
                    ?>
                    <table class="table table-list-data-child">
                        <tr>
                          <td>PTT tổng tiền</td>
                          <td class="text-right">{{ number_format($total_original_price) }}</td>
                        </tr>
                        <tr>
                          <td>PTT tổng phụ thu</td>
                          <td class="text-right">{{ number_format($item->ptt_tong_phu_thu) }}</td>
                        </tr>
                        <tr>
                          <td>PTT tổng thanh toán</td>
                          <td class="text-right"><label class="label label-warning" style="font-size: 12px;">{{ number_format($ptt_tong_tien_goc) }}</label></td>
                        </tr>
                    </table>

                  </div>
                </td>
                  <td style="white-space: nowrap;">

                    @if($item->mail_hotel == 0)

                      <a href="{{ route('mail-preview', ['id' => $item->id]) }}" class="btn btn-sm btn-success" >
                        <i class="  glyphicon glyphicon-envelope"></i> Book phòng
                      </a>

                    @endif
                     @if($item->mail_hotel == 1)
                    <p class="label label-info" style="margin-bottom: 5px; clear:both">Đã mail book</p>
                    <div class="clearfix" style="margin-bottom: 5px"></div>
                    @if(!Auth::user()->view_only && $item->mail_customer == 0)
                    <a href="{{ route('mail-confirm', ['id' => $item->id]) }}" class="btn btn-sm btn-success" >
                      <i class="  glyphicon glyphicon-envelope"></i> Gửi xác nhận
                    </a>
                    @else
                    <p class="label label-info" style="margin-bottom: 5px; clear:both">Đã mail khách</p>
                    <div class="clearfix"></div>
                    @endif
                    @endif

                    @if($item->booking_code)
                   <p style="color: blue; font-weight: bold;font-size: 14px; margin-top: 5px;">CODE: {{ $item->booking_code }}</p>

                  @elseif(!Auth::user()->view_only)
                    <input type="text" class="bk_code form-control" style="width: 100px; color:red; margin-top: 10px; margin-bottom: 10px;" data-id="{{ $item->id }}" placeholder="CODE">
                  @endif
                  @if($item->nguoi_thu_coc)
                  + Cọc :
                  <span style="color: red">{{ $collecterNameArr[$item->nguoi_thu_coc] }}</span>
                  @endif
                  @if($item->nguoi_thu_tien)
                  <br>+ Thu tiền:
                  <span style="color: red">{{ $collecterNameArr[$item->nguoi_thu_tien] }}</span>
                  @endif
                  <div style="background-color: @if($error_original_price) red @else #ccc @endif ;padding: 5px; white-space: nowrap; margin-top: 10px;" >
                    HH CTY: <span style="font-weight: bold;">{{ number_format($item->hoa_hong_cty) }}</span><br>
                    HH Sales: <span style="font-weight: bold;">{{ number_format($item->hoa_hong_sales) }}</span>
                  </div>
                  </td>
                  @if(!Auth::user()->view_only)
                  <td style="white-space:nowrap">
                  <a href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a href="{{ route( 'booking-hotel.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-hotel.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif

                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <br><input type="checkbox" class="change-column-value" value="2" data-table="booking" data-id="{{ $item->id }}" data-reload="1" data-column="status" data-action="Hoàn tất" data-name="PTH{{$item->id}} - {{ $item->name }} - {{ $item->hotel->name }}"> <label>Hoàn tất</label>
                  @endif

                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)

                    @if($item->check_unc == 0)
                    <br><input id="check_unc_{{ $item->id }}" type="checkbox" name="" class="change-column-value" value="{{ $item->check_unc == 1 ? 0 : 1 }}" data-id="{{ $item->id }}" data-table="booking" data-reload="1" data-column="check_unc"
                    data-action="Đã check UNC" data-name="PTH{{$item->id}} - {{ $item->name }} - {{ $item->hotel->name }}"
                    {{ $item->check_unc == 1 ? "checked" : "" }}>
                    <label for="check_unc_{{ $item->id }}">Đã check UNC</label>
                    @else
                    <br> <label class="label label-success">Đã check UNC</label>
                    @endif

                    @if($item->ptt_pay_status != 2)
                      <br>
                      <input id="check_ptt_tt_{{ $item->id }}" type="checkbox"
                      class="change-column-value" value="2" data-table="booking" data-id="{{ $item->id }}" data-column="ptt_pay_status" data-reload="1"
                      data-action="Đã thanh toán cho khách sạn/đối tác" data-name="PTH{{$item->id}} - {{ $item->name }} - {{ $item->hotel->name }} "
                      {{ $item->ptt_pay_status == 2 ? "checked" : "" }}>
                      <label for="check_ptt_tt_{{ $item->id }}">PTT đã TT</label>
                    @endif

                    @if($item->ptt_pay_status == 0)
                    <br> <label class="label label-info">PTT chưa TT</label>
                    @elseif($item->ptt_pay_status == 1)
                    <br> <label class="label label-warning">PTT đã cọc</label>
                    @elseif($item->ptt_pay_status == 2)
                    <br> <label class="label label-success">PTT đã thanh toán</label>
                    @endif

                  @endif
                </td>
                  @endif
              </tr>
              @endforeach
            @else
            <tr>
              <td colspan="9">Không có dữ liệu.</td>
            </tr>
            @endif

          </tbody>
          </table>
          </div>
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
        </div>
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
</section>
<!-- /.content -->
</div>
<!-- Modal -->
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
<div class="modal fade" id="confirmNopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header bg-green">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4>LẤY NỘI DUNG CK NỘP TIỀN</h4>
      </div>
      <div class="modal-body" id="loadConfirm">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnYcNop">LẤY ND CK NỘP TIỀN</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">ĐÓNG</button>
      </div>
    </div>
  </div>
</div>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('tr.checked_in').each(function(){
        var tr = $(this);
        var id = tr.data('id');
        $.ajax({
          url : '{{ route('booking-hotel.check-payment') }}?id=' + id,
          type : 'GET',
          success : function(data){
            if(data){
              $('#error_unc_' + id).text(data).show();
            }

          }
        });

      });
    $("#check_all").click(function(){
          $('input.check_one').not(this).prop('checked', this.checked);
      });
    $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
      });
  $('img.img-unc').click(function(){
    $('#unc_img').attr('src', $(this).attr('src'));
    $('#uncModal').modal('show');
  });
    $('#sort_by_change').change(function(){
      $('#sort_by').val($(this).val());
      $('#searchForm').submit();
    });
  });
</script>
<script type="text/javascript">
    $(document).ready(function(){


      $('.bk_code').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('saveBookingCode')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            booking_code : obj.val()
          },
          success : function(doc){

          }
        });
      });

      $('.change_status').click(function(){
          var obj = $(this);
          $.ajax({
            url : "{{ route('change-status') }}",
            type : 'GET',
            data : {
              id : obj.data('id')
            },
            success: function(){
              //window.location.reload();
            }
          });
        });
      $('.change-value').click(function(){
          var obj = $(this);
          var col = obj.data('col');
          var value = obj.data('value');
          $.ajax({
            url : "{{ route('booking.change-value-by-column') }}",
            type : 'GET',
            data : {
              id : obj.data('id'),
              col : col,
              value : value
            },
            success: function(){
              ///window.location.reload();
            }
          });
        });

      $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.cong-no-hotel') }}").submit().attr('action', oldAction);
      });
    });
  </script>
@stop
