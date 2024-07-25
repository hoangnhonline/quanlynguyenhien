@extends('layout')
@section('content')
<div class="content-wrapper">


<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="text-transform: uppercase;">
    Quản lý đặt tour
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking.index', ['type' => $type]) }}">
      @if($type == 1)
    Tour
    @elseif($type == 2)

    @endif</a></li>
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

      <a href="{{ route('booking.create-short') }}" class="btn btn-success btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">

        <div class="panel-body" style="padding: 5px !important;">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking.index') }}" id="searchForm" style="margin-bottom: 0px;">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="PTT ID" value="{{ $arrSearch['id_search'] }}" style="width: 70px">
            </div>
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="created_at" placeholder="Ngày đặt" value="{{ $arrSearch['created_at'] }}" style="width: 100px">
            </div>

            <div class="form-group">
              <select class="form-control select2" name="tour_id" id="tour_id">
                <option value="">--Tour--</option>
                @foreach($tourSystem as $tour)
                <option value="{{ $tour->id }}" {{ $arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
                  <select class="form-control select2" id="tour_cate" name="tour_cate" >
                      <option value="">--Loại tour--</option>
                      <option value="1" {{ $arrSearch['tour_cate'] == 1 ? "selected" : "" }}>4 đảo</option>
                      <option value="2" {{ $arrSearch['tour_cate'] == 2 ? "selected" : "" }}>2 đảo</option>
                  </select>
                </div>
            <div class="form-group">
              <select class="form-control select2" name="time_type" id="time_type">
                <option value="">--Thời gian--</option>
                <option value="1" {{ $time_type == 1 ? "selected" : "" }}>Theo tháng</option>
                <option value="2" {{ $time_type == 2 ? "selected" : "" }}>Khoảng ngày</option>
                <option value="3" {{ $time_type == 3 ? "selected" : "" }}>Ngày cụ thể </option>
              </select>
            </div>
            @if($time_type == 1)
            <div class="form-group  chon-thang">
                <select class="form-control select2" id="month_change" name="month">
                  <option value="">--THÁNG--</option>
                  @for($i = 1; $i <=12; $i++)
                  <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                  @endfor
                </select>
              </div>
              <div class="form-group  chon-thang">
                <select class="form-control select2" id="year_change" name="year">
                  <option value="">--Năm--</option>
                  <option value="2020" {{ $year == 2020 ? "selected" : "" }}>2020</option>
                  <option value="2021" {{ $year == 2021 ? "selected" : "" }}>2021</option>
                  <option value="2022" {{ $year == 2022 ? "selected" : "" }}>2022</option>
                  <option value="2023" {{ $year == 2023 ? "selected" : "" }}>2023</option>
                  <option value="2024" {{ $year == 2024 ? "selected" : "" }}>2024</option>
                </select>
              </div>
            @endif
            @if($time_type == 2 || $time_type == 3)
            <div class="form-group chon-ngay">
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['use_date_from'] }}" style="width: 110px">
            </div>
            @if($time_type == 2)
            <div class="form-group chon-ngay den-ngay">
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" style="width: 110px">
            </div>
             @endif
            @endif
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)


            <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }} - {{ $user->phone }}</option>
                @endforeach
              </select>
            </div>

            @endif

            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                <option value="">--Người thu tiền--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_tien'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>

            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <div class="form-group">
              <button type="button" id="btnReset" class="btn btn-default btn-sm">Reset</button>
            </div>
            <div>
              @if($arrSearch['tour_id'] != 4)
              <div class="form-group">
              <input type="checkbox" name="tour_type[]" id="tour_type_1" {{ in_array(1, $arrSearch['tour_type']) ? "checked" : "" }} value="1">
              <label for="tour_type_1">GHÉP({{ $ghep }})</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_2" {{ in_array(2, $arrSearch['tour_type']) ? "checked" : "" }} value="2">
              <label for="tour_type_2">VIP({{ $vip }}-{{ $tong_vip }}NL)&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_3" {{ in_array(3, $arrSearch['tour_type']) ? "checked" : "" }} value="3">
              <label for="tour_type_3">THUÊ CANO({{ $thue }})&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @endif
              <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">Mới</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn Tất</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @if($arrSearch['tour_id'] != 4)
            <div class="form-group" style="border-right: 1px solid #9ba39d; float: right">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="no_cab" id="no_cab" {{ $arrSearch['no_cab'] == 1 ? "checked" : "" }} value="1">
              <label for="no_cab">Không cáp&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d; float: right">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="no_meals" id="no_meals" {{ $arrSearch['no_meals'] == 1 ? "checked" : "" }} value="1">
              <label for="no_meals">Không ăn&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @endif
            <div class="form-group" style="">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="price_net" id="price_net" {{ $arrSearch['price_net'] == 1 ? "checked" : "" }} value="1">
              <label for="price_net">Giá NET</label>
            </div>
            </div>
          </form>
        </div>
      </div>
      <div class="panel">
        <div class="panel-body">
          <ul style="padding: 0px;">
          @foreach($arrHDV as $hdv_id => $arrBK)
          <li style="display: inline;
    float: left;
    list-style: none; height: 45px;">
          @if($hdv_id > 0)
          <span data-id="{{ $hdv_id }}" class="label label-default hdv @if($hdv_id == $arrSearch['hdv_id']) selected @endif" style="padding: 10px 5px;margin-right: 10px; font-size: 12px">{{ isset($arrHDVDefault[$hdv_id]) ? $arrHDVDefault[$hdv_id]->name : $hdv_id }}[{{ count($arrBK)}}]</span>
          @else
          <span data-id="" class="label label-default hdv" style="padding: 10px;margin-right: 10px; font-size: 12px">CHƯA CHỌN HDV[{{ count($arrBK)}}]</span>
          @endif
          </li>
          @endforeach
        </ul>
        </div>
      </div>
      @if(Auth::user()->role == 1 && !Auth::user()->view_only && !empty($arrDs))
      <div class="panel">
        <div class="panel-body">
          <ul style="padding: 0px;">
          @foreach($arrDs as $user_id => $dsAdults)

          <li style="display: inline;float: left;list-style: none; height: 45px;">

          <span data-id="{{ $hdv_id }}" class="label label-sm @if($dsAdults > 30) label-danger @else label-default @endif" style="padding: 5px 3px;margin-right: 5px; font-size: 11px">{{ isset( $arrUser[$user_id]) ? $arrUser[$user_id]->name : "Không xác định" }} - [{{ $dsAdults }}]</span>

          </li>

          @endforeach
        </ul>
        </div>
      </div>
      @endif
      <div class="panel" style="margin-bottom: 15px;">
        <div class="panel-body" style="padding: 5px;">
          <div class="table-responsive">
          <table class="table table-bordered" id="table_report" style="margin-bottom:0px;font-size: 14px;">
              <tr style="background-color: #f4f4f4">
                <th class="text-center">Tổng BK</th>
                <th class="text-center">NL/TE</th>
                <th class="text-center">Ăn NL/TE</th>
                <th class="text-center">Cáp NL/TE</th>
                <th class="text-right">Thực thu</th>
                <th class="text-right">HDV thu</th>
                <th class="text-right">Điều hành</th>
                <th class="text-right">Tổng cọc</th>
                <th class="text-right">HH sales</th>
                <th class="text-right">Doanh số</th>
              </tr>
              <tr>
                <td class="text-center">{{ number_format($items->total()) }}</td>
                <td class="text-center">{{ number_format($tong_so_nguoi ) }} / {{ number_format($tong_te ) }}</td>
                <td class="text-center">{{ number_format($tong_phan_an ) }} / {{ number_format($tong_phan_an_te ) }}</td>
                <td class="text-center">{{ number_format($cap_nl ) }} / {{ number_format($cap_te ) }}</td>
                <td class="text-right">{{ number_format($tong_thuc_thu ) }}</td>
                <td class="text-right">{{ number_format($tong_hdv_thu ) }}</td>
                <td class="text-right">{{ number_format($tong_thao_thu ) }}</td>
                <td class="text-right">{{ number_format($tong_coc ) }}</td>
                <td class="text-right">{{ number_format($tong_hoa_hong_sales ) }}</td>
                <td class="text-right">{{ number_format($tong_doanh_so ) }}</td>
              </tr>
          </table>

        </div>
        </div>
      </div>
      <div class="box">


        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select style="font-size: 11px;" class="form-control select2 multi-change-column-value" data-column="hdv_id">
              <option value="">--SET HDV--</option>
              @foreach($listUser as $user)
              @if($user->hdv==1)
              <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endif
              @endforeach
            </select>
            <select class="form-control select2 multi-change-column-value" data-column="status">
                <option value="">--SET TRẠNG THÁI--</option>
                <option value="1">Mới</option>
                <option value="2">Hoàn tất</option>
                <option value="3">Hủy</option>
              </select>
             <select class="form-control select2 multi-change-column-value" data-column="nguoi_thu_tien">
                <option value="">--SET THU TIỀN--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}">{{ $col->name }}</option>
                @endforeach
              </select>
              <select class="form-control select2 multi-change-column-value"  data-column="cano_id">
                <option value="">--SET CANO--</option>
                @foreach($canoList as $cano)
                <option value="{{ $cano->id }}">{{ $cano->name }}</option>
                @endforeach
              </select>

          </div>
          <div class="form-group" style="float: right">
            <a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a>
          </div>
        </div>
        @endif
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr style="background-color: #f4f4f4">
              <th style="width: 1%" class="text-center" ><input type="checkbox" id="check_all" value="1"></th>
              <th style="width: 1%"></th>
              <th width="200">Tên KH</th>
              <th style="width: 200px">Nơi đón</th>
              <th class="text-center" width="80">NL/TE/EB</th><th class="text-right" width="100">Tổng tiền/Cọc<br> HH Sales</th>
              <th class="text-right" width="140" >Thực thu</th>
              <th class="text-center" width="60">Ngày đi</th>
              <th class="text-center" width="90">HDV</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $l = 0; ?>
              @foreach( $items as $item )
                <?php $l ++; ?>
              <tr class="booking" id="row-{{ $item->id }}" data-id="{{ $item->id }}" data-date="{{ $item->use_date }}" style="border-bottom: 1px solid #000 !important;@if($item->status == 3) background-color: #f77e7e; @endif">
                <td class="text-center" style="line-height: 30px">
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">

                  <a href="{{ route('view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>
                  <br>{{ date('d/m H:i', strtotime($item->created_at)) }}

                </td>
                <td style="text-align: center;white-space: nowrap; line-height: 30px;"><strong style="color: red;">PTT{{ $item->id }}</strong>
                  <br>
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>

                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif

                </span></td>
                <td style="position: relative; line-height: 30px;">



                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  @if($item->status == 1)
                  <a style="font-weight: bold" href="{{ route( 'booking.edit', $arrEdit) }}">{{ $item->name }}</a>
                  @endif
                  @if($item->status != 3)
                     - <a href="tel:{{ $item->phone }}" style="font-weight: bold">{{ $item->phone }}</a>

                  @if($item->tour_id)
                  <br><label class="label" style="background-color:{{ $tourSystemName[$item->tour_id]['bg_color'] }}">{{ $tourSystemName[$item->tour_id]['name'] }}</label>
                  @endif
                  @if($item->tour_cate == 2 && $item->tour_id == 1)
                  <br><label class="label label-info">2 đảo</label>
                  @endif

                  @if($item->tour_type == 3)
                  <br><label class="label label-warning">Thuê cano</label>
                  @elseif($item->tour_type == 2)
                  <br><label class="label label-danger">Tour VIP</label>
                  @endif
                    <br><i style="font-style: italic;" class="glyphicon glyphicon-user"></i>
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only)
               <i>
                  @if($item->user)
                    {{ $item->user->name }}
                  @else
                    {{ $item->user_id }}
                  @endif
                  @if($item->ctv)
                    - {{ $item->ctv->name }}
                  @endif
               </i>

                  @endif

                  @endif


                </td>

                <td style="line-height: 22px;">
                  @if($item->status != 3)
                  @if($item->location && !$arrSearch['chua_thuc_thu'])
                  {{ $item->location->name }} [{{ $item->location_id }}]
                  <br>
                 <!--  {{ $item->location->address }} -->
                  @else
                  {{ $item->address }}
                  @endif
                  <span style="color:red; font-size:12px">
                    @if($item->ko_cap_treo)
                    KHÔNG CÁP<br>
                    @endif
                    {{ $item->notes }}</span>
                    @if($item->export == 1)
                    <span class="label label-default">Đã gửi</span>
                    @else
                    <span class="label label-danger">Chưa gửi</span>
                    @endif
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->export == 2)
                    <input type="checkbox" name="" class="change_status" value="1" data-id="{{ $item->id }}">
                    <br>
                    @endif
                  @endif
                </td>
                 <td class="text-center">
                  @if($item->status != 3)
                    {{ $item->adults }} / {{ $item->childs }} / {{ $item->infants }}
                    <br>
                    <?php
                    $meals = $item->meals;
                    if($meals > 0){
                      $meals+= $item->meals_te/2;
                    }

                    ?>
                    <i class="  glyphicon glyphicon-briefcase"></i> {{ $meals }}
                  @endif

                </td>


                <td class="text-right">
                  @if($item->status != 3)
                  {{ number_format($item->total_price) }}/{{ number_format($item->tien_coc) }}
                  <br>
                  <span style="color: #06b7a4; font-weight: bold">HH: {{ number_format($item->hoa_hong_sales) }}</span>
                  <input type="text" class="form-control hoa_hong_sales number" placeholder="HH sales" data-id="{{ $item->id }}" value="{{ $item->hoa_hong_sales ? number_format($item->hoa_hong_sales) : "" }}" style="text-align: right;width: 90%;float:right;margin-top:5px">
                  @if($item->total_price_child > 0)
                  <br><span style="color:green">TE +{{ number_format($item->total_price_child) }}</span>
                  @endif

                  @if($item->extra_fee > 0)
                  <br><span style="color:blue">+{{ number_format($item->extra_fee) }}</span>
                  @endif
                  @if($item->discount > 0)
                  <br><span style="color:red">-{{ number_format($item->discount) }}</span>
                  @endif


                  @endif
                </td>
                <td class="text-right" style="position: relative;">


                  @if($item->status != 3)
                    @if($item->nguoi_thu_tien)
                    <span style="color: red">{{ $collecterNameArr[$item->nguoi_thu_tien] }}</span>
                    @endif
                    {{ number_format($item->tien_thuc_thu) }}
                    <br> HDV thu: <span style="color: blue">{{ number_format($item->hdv_thu) }} </span>
                  @endif
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                    <p style="position: absolute;bottom: 0px; right: 5px">
                      <input id="price_net_{{ $item->id }}" type="checkbox" class="change_price_net" value="1" data-id="{{ $item->id }}" {{ $item->price_net == 1 ? "checked" : "" }}>
                    <label for="price_net_{{ $item->id }}">Giá NET</label></p>
                    @endif
                </td>
                <td class="text-center">
                  {{ date('d/m', strtotime($item->use_date)) }}
                </td>
                <td class="text-center">
                  @if($item->tour_id == 4)
                   <!--  @if($item->mail_hotel == 0)
                    <a href="{{ route('mail-preview', ['id' => $item->id, 'tour_id' => 4]) }}" class="btn btn-sm btn-success" >
                      <i class="  glyphicon glyphicon-envelope"></i> Book Tour
                    </a>
                    @else
                    <p class="label label-info" style="margin-bottom: 5px; clear:both">Đã mail John</p>
                    @endif -->
                  @else
                    @if($item->status != 3 && $arrSearch['is_edit'] == 1)

                      @if($item->hdv_id > 0 )
                      <strong>{{ $item->hdv->name }}</strong>
                      @endif

                    @else
                      @if($item->hdv_id > 0)
                      {{ $item->hdv->name }}
                      @<?php else: ?>
                      - HDV -
                      @endif
                      @if($item->cano_id > 0)
                      <br> - {{ $item->cano->name }}
                      @else
                      <br> - Cano -
                      @endif
                    @endif
                  @endif

                </td>
                <td style="white-space:nowrap; position: relative;">
                  @if($item->status != 3)
                    @php
                    $countUNC = $item->payment->count();
                    $strpayment = "";
                    $tong_payment = 0;
                    foreach($item->payment as $p){
                      $strpayment .= "+". number_format($p->amount)." - ".date('d/m', strtotime($p->pay_date));
                      if($p->type == 1){
                        $strpayment .= " - UNC"."<br>";
                      }else{
                        $strpayment .= " - auto"."<br>";
                      }
                      $tong_payment += $p->amount;
                    }
                    if($countUNC > 0)
                    $strpayment .= "Tổng: ".number_format($tong_payment);
                    @endphp

                  <a data-toggle="tooltip" data-html="true" title="{!! $strpayment !!}" href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm">{{ $countUNC > 0 ?? $countUNC }} <span class="glyphicon glyphicon-usd"></span></a>
                    @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                    @if($item->status == 1)
                    <a href="{{ route( 'booking.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                    @endif
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1 && Auth::user()->id == 1)
                    <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                    @endif
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                    <br><input id="hoan_tat_{{ $item->id }}" type="checkbox" name="" class="change_status_bk" value="2" data-id="{{ $item->id }}">
                    <label for="hoan_tat_{{ $item->id }}">Hoàn tất</label>
                    @endif
                  @endif
                  <br>
                  <a style="font-size: 15px" target="_blank" href="https://plantotravel.vn/booking/{{ Helper::mahoa('mahoa', $item->id) }}">Danh sách ({{ $item->customers->count() }})</a>
                  <br />
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <br><a style="font-size: 14px" target="_blank" href="{{ route('history.booking', ['id' => $item->id]) }}">Xem lịch sử</a>
                  @endif
                </td>
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
          @if(Auth::user()->role == 1 && !Auth::user()->view_only)
          <div class="form-inline" style="padding: 5px">
            <div class="form-group">
              <select class="form-control select2 multi-change-column-value" id="hdv_id" name="hdv_id" data-column="hdv_id">
                <option value="">--SET HDV--</option>
                @foreach($listUser as $user)
                @if($user->hdv==1)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endif
                @endforeach
              </select>
              <select class="form-control select2 multi-change-column-value" name="status" id="status">
                  <option value="">--SET TRẠNG THÁI--</option>
                  <option value="1">Mới</option>
                  <option value="2">Hoàn tất</option>
                  <option value="3">Hủy</option>
                </select>
              <select class="form-control select2 multi-change-column-value" name="nguoi_thu_tien" id="nguoi_thu_tien">
                  <option value="">--SET THU TIỀN--</option>
                  @foreach($collecterList as $col)
                  <option value="{{ $col->id }}">{{ $col->name }}</option>
                  @endforeach
              </select>
              <select class="form-control select2 multi-change-column-value"  data-column="cano_id">
                <option value="">--SET CANO--</option>
                @foreach($canoList as $cano)
                <option value="{{ $cano->id }}">{{ $cano->name }}</option>
                @endforeach
              </select>

            </div>
          </div>
          @endif

        </div>

      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
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
<input type="hidden" id="table_name" value="articles">
@stop
<style type="text/css">
  .hdv{
    cursor: pointer;
  }
  .hdv:hover, .hdv.selected{
    background-color: #06b7a4;
    color: #FFF
  }
  label{
    cursor: pointer;
  }
  #table_report th td {padding: 2px !important;}
  #searchForm, #searchForm input{
    font-size: 13px;
  }
  .form-control{
    font-size: 13px !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered{

    font-size: 12px !important;
  }
  tr.error{
    background-color:#ffe6e6
  }
</style>
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    });
  });
</script>
<script type="text/javascript">
    $(document).ready(function(){

      $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
      });
      $('tr.booking').click(function(){
        $(this).find('.check_one').attr('checked', 'checked');
      });
      $("#check_all").click(function(){
          $('input.check_one').not(this).prop('checked', this.checked);
      });
      $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.cong-no-tour') }}").submit().attr('action', oldAction);
      });
      $('#btnExportCustomer').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('booking.export-customer') }}").submit().attr('action', oldAction);
      });
      $('#btnExportGui').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.gui-tour') }}").submit().attr('action', oldAction);
      });
      $('#temp').click(function(){
        $(this).parents('form').submit();
      });
      $('.change_status').click(function(){
          var obj = $(this);
          $.ajax({
            url : "{{ route('change-export-status') }}",
            type : 'GET',
            data : {
              id : obj.data('id')
            },
            success: function(){
              window.location.reload();
            }
          });
        });
       $('.change_status_bk').click(function(){
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
       $('.change-column-value').change(function(){
          var obj = $(this);
          if(obj.data('column') == 'cano_id'){
           // alert('Tất cả các booking cùng HDV sẽ được gán chung vào cano này');
          }
          $.ajax({
            url : "{{ route('booking.change-value-by-column') }}",
            type : 'GET',
            data : {
              id : obj.data('id'),
              col : obj.data('column'),
              value: obj.val()
            },
            success: function(data){
                console.log(data);
            }
          });
       });
       $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              $.ajax({
                url : "{{ route('booking.change-value-by-column') }}",
                type : 'GET',
                data : {
                  id : $(this).val(),
                  col : obj.data('column'),
                  value: obj.val()
                },
                success: function(data){

                }
              });
          });

       });
      $('.hoa_hong_sales').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('save-hoa-hong')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            hoa_hong_sales : obj.val()
          },
          success : function(doc){

          }
        });

      });
      $('.hdv').click(function(){
        var hdv_id = $(this).data('id');
        if(hdv_id != ""){
          $('#hdv_id').val($(this).data('id'));
          $('#searchForm').submit();
        }

      });
      $('.change_tien_thuc_thu').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'tien_thuc_thu'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
       $('.change_tien_coc').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'tien_coc'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
      $('.change_total_price').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column') }}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'total_price'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
      $('.change_price_net').click(function(){
        var obj = $(this);
        var price_net = 0;
        if(obj.prop('checked') == true){
          price_net = 1
        }
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : price_net,
            col : 'price_net'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
      $('#btnReset').click(function(){
        $('#searchForm select').val('');
        $('#searchForm').submit();
      });
    });
  </script>
@stop
