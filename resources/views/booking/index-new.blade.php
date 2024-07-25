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
      <a href="{{ route('booking.create', ['type' => $type]) }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
      <a href="{{ route('booking.create-short') }}" class="btn btn-success btn-sm" style="margin-bottom:5px">Tạo nhanh</a>
      @endif

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

          <span data-id="{{ $user_id }}" class="label label-sm @if($dsAdults > 30) label-danger @else label-default @endif" style="padding: 5px 3px;margin-right: 5px; font-size: 11px">{{ isset( $arrUser[$user_id]) ? $arrUser[$user_id]->name : "Không xác định" }} - [{{ $dsAdults }}]</span>

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
              <tr style="background-color: #ffff99">
                <th class="text-center" width="20%">Tổng BK</th>
                <th class="text-center" width="20%">NL/TE</th>
                <th class="text-center" width="20%">Ăn NL/TE</th>
                <th class="text-center" width="20%">Cáp NL/TE</th>
                <th class="text-right" width="20%">HH sales</th>
              </tr>
              <tr>
                <td class="text-center">{{ number_format($items->total()) }}</td>
                <td class="text-center">{{ $tong_so_nguoi }} / {{ $tong_te }}</td>
                <td class="text-center">{{ $tong_phan_an }} / {{ $tong_phan_an_te }}</td>
                <td class="text-center">{{ $cap_nl }} / {{ $cap_te }}</td>
                <td class="text-right">{{ number_format($tong_hoa_hong_sales ) }}</td>
              </tr>
          </table>
        </div>
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
              <th>Tiền thực thu</th>
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
              <select class="form-control select2 multi-change-column-value"  data-column="export">
                <option value="">--TT GỬI--</option>
                <option value="2">Chưa gửi</option>
                <option value="1">Đã gửi</option>
              </select>
          </div>
          <div class="form-group" style="float: right">
            <a href="javascript:;" class="btn btn-success btn-sm" id="btnContentNop">LẤY ND NỘP TIỀN</a>
            <a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Excel</a>
            @if($time_type==3)
          @if($items->isNotEmpty())
              <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('booking.insurance', ['ids' => $items->pluck('id')->toArray()]) }}">Bảo hiểm</a>
          @endif
            <a href="javascript:;" class="btn btn-info btn-sm" id="btnExportGui">Export Gửi</a>
            <a href="javascript:;" class="btn btn-warning btn-sm" id="btnExportCustomer">DS Khách</a>
            <!-- <a href="https://plantotravel.vn/sheet/do.php?day={{ $day }}&month={{ $month_do }}" target="_blank" class="btn btn-danger btn-sm">So sánh</a> -->
            @endif
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
                <?php $l ++;
                $allowEdit = $item->use_date >= $settingArr['date_block_booking'] ? true : false;
                 ?>
              <tr class="booking" id="row-{{ $item->id }}" data-id="{{ $item->id }}" data-date="{{ $item->use_date }}" style="border-bottom: 1px solid #000 !important;@if($item->status == 3) background-color: #f77e7e; @endif">
                <td class="text-center" style="line-height: 30px">
                  @if($allowEdit)
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                  @endif

                  <a href="{{ route('view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>
                  <br>{{ date('d/m H:i', strtotime($item->created_at)) }}
                  <span class="label label-sm label-danger" id="error_unc_{{ $item->id }}"></span>
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

                  <span class="name">{{ $item->name }}</span>

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
                  <p style="color:#f0ad4e; font-style: italic;" id="error_{{ $item->id }}"></p>


                </td>

                <td style="line-height: 22px; position: relative;">
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


                    <p style="position: absolute; bottom: 0px; right: 5px; padding-top: 10px">
                      @if($item->export == 1)
                        <span class="label label-default">Đã gửi</span>
                      @else
                        @if(Auth::user()->role == 1 && !Auth::user()->view_only && $allowEdit)
                        <input type="checkbox" class="change-column-value-booking" data-column="export" value="1" data-id="{{ $item->id }}" style="margin-top: -5px">
                        <span class="label label-danger">Chưa gửi</span>
                        @endif
                      @endif
                    </p>
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
                  @if($item->tour_id == 1)
                  <br >CAP: {{ $item->cap_nl }} / {{ $item->cap_te }}
                  @endif
                </td>


                <td class="text-right">
                  @if($item->status != 3)
                  {{ number_format($item->total_price) }}/{{ number_format($item->tien_coc) }}
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
                  <br>
                  <span style="color: #06b7a4; font-weight: bold">HH: {{ number_format($item->hoa_hong_sales) }}</span>
                  @if($item->status < 3)
                    @if(!in_array($item->user_id, [33,18]) && $item->price_net == 0 && $allowEdit)
                    <input type="text" class="form-control change-column-value-booking number" data-column="hoa_hong_sales" placeholder="HH sales" data-id="{{ $item->id }}" value="{{ $item->hoa_hong_sales ? number_format($item->hoa_hong_sales) : "" }}" style="text-align: right;width: 90%;float:right;margin-top:5px">
                    <br>
                      @if($item->user)
                     <p style="color: #3c8dbc;clear: both;" >
                      {{ Helper::getLevel($item->user->level) }}</p>
                    @endif
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
                   @if($item->code_nop_tien)

                   <br>   <span style="font-weight: bold; color: #00a65a" title="Mã nộp tiền">{{ $item->code_nop_tien }}</span>
                    @if($item->time_nop_tien)
                    <label class="label label-success">Đã nộp tiền</label>
                    @endif
                    @endif

                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $allowEdit)
                    <p style="position: absolute;bottom: 0px; right: 5px"><input id="price_net_{{ $item->id }}" type="checkbox" class="change-column-value-booking" value="{{ $item->price_net == 1 ? 0 : 1 }}" data-id="{{ $item->id }}" data-column="price_net" {{ $item->price_net == 1 ? "checked" : "" }}>
                    <label for="price_net_{{ $item->id }}">Giá NET</label></p>
                    @endif
                </td>
                <td class="text-center">
                  {{ date('d/m', strtotime($item->use_date)) }}
                </td>
                <td class="text-center">


                      @if($item->hdv_id > 0)
                        {{ $item->hdv->name }}
                      @else
                        - HDV -
                      @endif

                      @if($item->cano_id > 0)
                        <br> - {{ $item->cano->name }}
                      @else
                        <br> - Cano -
                      @endif

                </td>

                <td style="white-space:nowrap; position: relative;">
                  @if($item->status != 3)
                  @if($allowEdit)
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

                    <a href="{{ route( 'booking-xe-free.create', ['booking_id' => $item->id]) }}" class="btn btn-primary btn-sm"><span class="fa fa-car" title="Xe đưa đón miễn phí"></span>
                    @if($item->dontienfree->count() > 0)
                      [ {{ $item->dontienfree->count() }} ]
                      @endif
                    </a>

                  <a data-toggle="tooltip" data-html="true" title="{!! $strpayment !!}" href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span> @if($countUNC> 0)[ {{ $countUNC }} ]@endif</a>


                    @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp

                    <a href="{{ route( 'booking.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>

                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1 && Auth::user()->id == 1)
                    <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                    @endif
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                    <br><input id="hoan_tat_{{ $item->id }}"  data-table="booking" type="checkbox" data-table="booking" data-column="status" class="change-column-value-booking" value="2" data-id="{{ $item->id }}">
                    <label for="hoan_tat_{{ $item->id }}">Hoàn tất</label>
                    @endif
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                    <br><input id="check_unc_{{ $item->id }}" type="checkbox" name="" class="change-column-value-booking" value="{{ $item->check_unc == 1 ? 0 : 1 }}" data-table="booking" data-id="{{ $item->id }}" data-column="check_unc" {{ $item->check_unc == 1 ? "checked" : "" }}>
                    <label for="check_unc_{{ $item->id }}">Đã check UNC</label>
                    <br>
                    <select class="form-control select2 change-column-value-booking" data-id="{{ $item->id }}" data-column="cty_send"  data-table="booking" style="width: 120px !important">
                        <option value="0">--GỬI--</option>
                        <option value="1" {{ old('cty_send', $item->cty_send) == 1  ? "selected" : "" }}>Rooty</option>
                        <option value="2" {{ old('cty_send', $item->cty_send) == 2  ? "selected" : "" }}>Funny</option>
                        <option value="4" {{ old('cty_send', $item->cty_send) == 4  ? "selected" : "" }}>Nguyễn Hiền</option>
                        <option value="5" {{ old('cty_send', $item->cty_send) == 5  ? "selected" : "" }}>Phúc Thủy</option>
                    </select>

                    @endif
                  @endif
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <br><a style="font-size: 14px" target="_blank" href="{{ route('history.booking', ['id' => $item->id]) }}">Xem lịch sử</a>
                  @endif
                  <br>
                  <a style="font-size: 15px" target="_blank" href="https://plantotravel.vn/booking/{{ Helper::mahoa('mahoa', $item->id) }}">Danh sách ({{ $item->customers->count() }})</a>
              @endif  <!--allowEdit-->
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
              <select class="form-control select2 multi-change-column-value"  data-column="export">
                <option value="">--TT GỬI--</option>
                <option value="2">Chưa gửi</option>
                <option value="1">Đã gửi</option>
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

<aside class="control-sidebar control-sidebar-light control-sidebar-open">

  <div class="panel panel-warning">
  <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc nâng cao</h3>
        </div>
        <div class="panel-body">
          <form role="form" class="form-inline search-form-booking" method="GET" action="{{ route('booking.index') }}" id="searchForm" style="margin-bottom: 0px;">
            <div class="row">

              <div class="col-md-12">
              @include('partials.block-search-date')
              </div>
            </div>
             <input type="hidden" name="type" value="{{ $type }}">
            <div class="row">
              <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="PTT ID" value="{{ $arrSearch['id_search'] }}">
              </div>

              <div class="form-group">
                <input type="text" class="form-control" autocomplete="off" name="code_nop_tien" placeholder="Code nộp tiền" value="{{ $arrSearch['code_nop_tien'] }}">
              </div>
              <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="created_at" placeholder="Ngày đặt" value="{{ $arrSearch['created_at'] }}">
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" placeholder="Số ĐT">
            </div>
          </div>
            </div> <!--row-->

            <div class="row">
              <div class="col-md-12">
                <div class="form-group" style="width: 48%">
                  <select class="form-control select2" name="tour_id" id="tour_id" style="width: 100% !important;">
                    <option value="">--Tên tour--</option>
                    @foreach($tourSystem as $tour)
                    <option value="{{ $tour->id }}" {{ $arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group" style="width: 48%">
                  <select class="form-control select2" id="tour_cate" name="tour_cate"  style="width: 100% !important;">
                      <option value="">--Loại tour--</option>
                      <option value="1" {{ $arrSearch['tour_cate'] == 1 ? "selected" : "" }}>4 đảo</option>
                      <option value="2" {{ $arrSearch['tour_cate'] == 2 ? "selected" : "" }}>2 đảo</option>
                  </select>
                </div>
              </div>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="row">
              <div class="col-md-12">
            <div class="form-group" style="width: 24%">
                <select class="form-control select2" name="user_id" id="user_id" style="width: 100% !important;">
                  <option value="">--Sales--</option>
                  @foreach($listUser as $user)
                  <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }} - {{ $user->phone }}</option>
                  @endforeach
                </select>
              </div>
             <div class="form-group" style="width: 24%">
            <select class="form-control select2" name="level" id="level" style="width: 100% !important;">
              <option value="" >--Phân loại sales--</option>
              <option value="1" {{ $level == 1 ? "selected" : "" }}>CTV Group</option>
              <option value="2" {{ $level == 2 ? "selected" : "" }}>ĐỐI TÁC</option>
              <option value="6" {{ $level == 6 ? "selected" : "" }}>NV SALES</option>
              <option value="7" {{ $level == 7 ? "selected" : "" }}>GỬI BẾN</option>
            </select>
          </div>


            <div class="form-group" style="width: 24%">
              <select class="form-control select2" name="user_id_manage" id="user_id_manage" style="width: 100% !important;">
                <option value="">--Người phụ trách--</option>
                <option value="84" {{ $arrSearch['user_id_manage'] == 84 ? "selected" : "" }}>Lâm Như</option>
                <option value="451" {{ $arrSearch['user_id_manage'] == 451 ? "selected" : "" }}>Thảo Lê</option>
                <option value="219" {{ $arrSearch['user_id_manage'] == 219 ? "selected" : "" }}>Trang Tạ</option>
                <option value="333" {{ $arrSearch['user_id_manage'] == 333 ? "selected" : "" }}>Group Tour</option>
              </select>
            </div>

            <div class="form-group" style="width: 24%">
              <select class="form-control select2" name="ctv_id" id="ctv_id" style="width: 100% !important;">
                <option value="">--Người book--</option>
                @foreach($ctvList as $ctv)
                <option value="{{ $ctv->id }}" {{ $arrSearch['ctv_id'] == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
            </div>
            @endif
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
           @if(Auth::user()->role == 1 && !Auth::user()->view_only)
          <div class="form-group">
            <select class="form-control select2" id="hdv_id" name="hdv_id">
              <option value="">--HDV--</option>
              @foreach($listHDV as $user)
              <option value="{{ $user->id }}" @if($arrSearch['hdv_id'] == $user->id) selected @endif>{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <select class="form-control select2"  data-column="cano_id" name="cano_id">
              <option value="">--CANO--</option>
              @foreach($canoList as $cano)
              <option value="{{ $cano->id }}" {{ $arrSearch['cano_id'] == $cano->id ? "selected" : "" }}>{{ $cano->name }}</option>
              @endforeach
            </select>
          </div>
          @endif

                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
            <select class="form-control" name="hdv0" id="hdv0">
              <option value="">--TT CHỌN HDV--</option>
              <option value="2" {{ $arrSearch['hdv0'] == 2 ? "selected" : "" }}>Đã chọn HDV</option>
              <option value="1" {{ $arrSearch['hdv0'] == 1 ? "selected" : "" }}>Chưa chọn HDV</option>
            </select>
          </div>
          <div class="form-group">
            <select class="form-control" name="cano0" id="cano0">
              <option value="">--TT CHỌN CANO--</option>
              <option value="2" {{ $arrSearch['cano0'] == 2 ? "selected" : "" }}>Đã chọn CANO</option>
              <option value="1" {{ $arrSearch['cano0'] == 1 ? "selected" : "" }}>Chưa chọn CANO</option>
            </select>
          </div>
          <select class="form-control select2" name="cty_send" id="cty_send">
                <option value="">--GỬI TOUR--</option>
                <option value="1" {{ $arrSearch['cty_send'] == 1 ? "selected" : "" }}>Rooty</option>
                <option value="2" {{ $arrSearch['cty_send'] == 2 ? "selected" : "" }}>Funny</option>
                <option value="3" {{ $arrSearch['cty_send'] == 3 ? "selected" : "" }}>Group Tour</option>
                <option value="4" {{ $arrSearch['cty_send'] == 4  ? "selected" : "" }}>Nguyễn Hiền</option>
                <option value="5" {{ $arrSearch['cty_send'] == 5  ? "selected" : "" }}>Phúc Thủy</option>
              </select>
          @endif
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
            <!-- <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_4" {{ in_array(4, $arrSearch['status']) ? "checked" : "" }} value="4">
              <label for="status_4">Dời ngày</label>
            </div> -->
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d; display: none;">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_grandworld" id="is_grandworld" {{ $arrSearch['is_grandworld'] == 1 ? "checked" : ""  }} value="1">
              <label for="is_grandworld" style="color: red">CHỤP GRAND WORLD&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @if($arrSearch['tour_id'] != 4)
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="no_cab" id="no_cab" {{ $arrSearch['no_cab'] == 1 ? "checked" : "" }} value="1">
              <label for="no_cab">Không cáp&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="no_meals" id="no_meals" {{ $arrSearch['no_meals'] == 1 ? "checked" : "" }} value="1">
              <label for="no_meals">Không ăn&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @endif
            <div class="form-group" style="">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="price_net" id="price_net" {{ $arrSearch['price_net'] == 1 ? "checked" : "" }} value="1">
              <label for="price_net">Giá NET</label>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group" style="float: right;">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="hh0" id="hh0" {{ $arrSearch['hh0'] == 1 ? "checked" : "" }} value="1">
              <label for="hh0">Chưa tính HH</label>
            </div>
            <div class="form-group" style="float: right;">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="unc0" id="unc0" {{ $arrSearch['unc0'] == 1 ? "checked" : "" }} value="1">
              <label for="unc0">Chưa check <span style="color: red">UNC</span></label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d;float: right;">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="co_coc" id="co_coc" {{ $arrSearch['co_coc'] == 1 ? "checked" : "" }} value="1">
              <label for="co_coc">Có cọc&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d;float: right;">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="is_edit" id="is_edit" {{ $arrSearch['is_edit'] == 1 ? "checked" : "" }} value="1">
              <label for="is_edit">Edit&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d; float: right">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="error" id="error" {{ $arrSearch['error'] == 1 ? "checked" : "" }} value="1">
              <label for="error">Error&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d; float: right">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="export" id="export" {{ $arrSearch['export'] == 2 ? "checked" : "" }} value="2">
              <label for="alone">Chưa gửi&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d; float: right">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="thuc_thu" id="thuc_thu" {{ $arrSearch['thuc_thu'] == 1 ? "checked" : "" }} value="1">
              <label for="thuc_thu">Chưa thực thu&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d; float: right">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_send" id="is_send" {{ $arrSearch['is_send'] == 1 ? "checked" : "" }} value="1">
              <label for="is_send">Gửi bên khác&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @endif
            <div class="form-group" style="border-right: 1px solid #9ba39d;float: right;">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="short" id="short" {{ $arrSearch['short'] == 1 ? "checked" : "" }} value="1">
              <label for="short">Short&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            </div>
          </form>
        </div>
      </div>
</aside>
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
      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
      $('tr.booking').each(function(){
        var tr = $(this);
        var id = tr.data('id');
        var use_date = tr.data('date');
        var today = new Date();
        if(use_date < "{{ date('Y-m-d') }}"){
          // $.ajax({
          //   url : '{{ route('booking.checkError') }}?id=' + id,
          //   type : 'GET',
          //   success : function(data){
          //     $('#error_' + id).text(data);
          //   }
          // });
          $.ajax({
            url : '{{ route('booking.check-unc') }}?id=' + id,
            type : 'GET',
            success : function(data){
              $('#error_unc_' + id).text(data);
            }
          });
        }
      });
      @endif
      $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
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
       $('.change-column-value-booking').change(function(){
          var obj = $(this);
          ajaxChange(obj.data('id'), obj);
       });
       $('.hoa_hong_sales').blur(function(){
          var obj = $(this);
          ajaxChange(obj.data('id'), obj);
       });
       $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              ajaxChange($(this).val(), obj);
          });

       });
      $('.hdv').click(function(){
        var hdv_id = $(this).data('id');
        if(hdv_id != ""){
          $('#hdv_id').val($(this).data('id'));
          $('#searchForm').submit();
        }

      });

      $('#btnReset').click(function(){
        $('#searchForm select').val('');
        $('#searchForm').submit();
      });

    });
    function ajaxChange(id, obj){
        $.ajax({
            url : "{{ route('booking.change-value-by-column') }}",
            type : 'GET',
            data : {
              id : id,
              col : obj.data('column'),
              value: obj.val()
            },
            success: function(data){
                console.log(data);
            }
          });
      }

  </script>
@stop
