@extends('layout')
@section('content')
@php
$time_type = $arrSearch['time_type'];
$flag_pay = $userRole == 1 && $time_type == 1 && $arrSearch['status'] == 2 ? 1 : 0
@endphp
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Mã giảm giá
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'coupon-code.index' ) }}">Mã giảm giá</a></li>
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
      <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <button class="btn btn-info btn-sm" id="btnAddCode" style="margin-bottom:5px">LẤY LINK TẠO MÃ</button>
        <!-- general form elements -->
        <div class="box box-primary" id="resList">
          <div class="box-header with-border">
            <h3 class="box-title">Danh sách nhà xe</h3>
          </div>
            <div class="box-body">
              <form role="form" method="POST" action="{{ route('coupon-code.store') }}" id="dataForm">
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


                    <div class="row">
                      <?php $dem = 0; ?>
                  @foreach($partnerList as $res)
                  <?php
                  $dem++;
                  $rsGiam = App\Helpers\Helper::tinhPhanTram($res->phan_tram_chi, 0, 0, 1);
                  $phan_tram_co_tai = $rsGiam['khach'];
                  $rsGiamTuLai = App\Helpers\Helper::tinhPhanTram($res->phan_tram_chi, 0, 1, 1);
                  $phan_tram_tu_lai = $rsGiamTuLai['khach'];
                  ?>
                  <div class="col-md-3 col-xs-12" style="margin-bottom: 15px;">
                    <div class="res-detail" style="background-color: #f2f2f2;padding: 15px; border-radius: 5px;">
                      <p style=" font-size:15px;text-transform: uppercase; font-weight: bold; height: 45px; overflow-y: hidden;"><a style="color: #06b7a4;" href="https://plantotravel.vn/coupon/list/{{ Helper::mahoa('mahoa', $res->id) }}" target="_blank" title="Link quản lí mã giảm giá của nhà hàng">{{ $res->name }}</a></p>
                      <a href="https://plantotravel.vn/coupon/{{ Helper::mahoa('mahoa', Auth::user()->code) }}/{{ Helper::mahoa('mahoa', $res->id) }}" target="_blank" style="font-size: 16px; color: #eea236"><i class="fa fa-gift" aria-hidden="true"></i> Lấy mã giảm giá - {{ $phan_tram_co_tai }}% </a>
                   <i class="fa fa-copy copyText" aria-hidden="true" title="Click để copy link" data-link="https://plantotravel.vn/coupon/{{ Helper::mahoa('mahoa', Auth::user()->code) }}/{{ Helper::mahoa('mahoa', $res->id) }}" style="cursor: pointer;"></i>
                  <p style="margin-top: 10px">
                    <a href="https://plantotravel.vn/coupontl/{{ Helper::mahoa('mahoa', Auth::user()->code) }}/{{ Helper::mahoa('mahoa', $res->id) }}" target="_blank" style="font-size: 16px; color: #0066ff"><i class="fa fa-gift" aria-hidden="true"></i> Khách thuê xe máy / oto tự lái - {{ $phan_tram_tu_lai }}% </a>
                  <i class="fa fa-copy copyText" aria-hidden="true" title="Click để copy link" data-link="https://plantotravel.vn/coupon/{{ Helper::mahoa('mahoa', Auth::user()->code) }}/{{ Helper::mahoa('mahoa', $res->id) }}" style="cursor: pointer;"></i>
                  </p>
                    </div>
                  </div>
                  @if($dem%4 == 0)
                </div><div class="row">
                  @endif
                  @endforeach

                </div>

              </form>
          </div>
        </div>
        <!-- /.box -->

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('coupon-code.index') }}" id="searchForm">

              <div class="form-group">
                  <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
              </div>
            <div class="form-group">
              <select class="form-control select2" name="partner_id" id="partner_id">
                <option value="">--Đối tác--</option>
                @foreach($partnerList as $shop)
                <option value="{{ $shop->id }}" {{ $partner_id == $shop->id ? "selected" : "" }}>{{ $shop->name }}</option>
                @endforeach

              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">--Sales--</option>
                @foreach($salesList as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="status" id="status">
                <option value="">--Trạng thái--</option>
                <option value="1" {{ $arrSearch['status'] == 1 ? "selected" : "" }}>Chưa sử dụng</option>
                <option value="2" {{ $arrSearch['status'] == 2 ? "selected" : "" }}>Đã sử dụng</option>
              </select>
            </div>
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>
      <div class="panel" style="margin-bottom: 15px;">
        <div class="panel-body" style="padding: 5px;">
          <div class="table-responsive">
          <table class="table table-bordered" id="table_report" style="margin-bottom:0px;font-size: 14px;">
              <tr style="background-color: #f4f4f4">
                <th class="text-center">Tổng code</th>
                <th class="text-center">Đã sử dụng</th>
                <th class="text-center">Chưa sử dụng</th>
                <th class="text-center">Tự lái/xe máy</th>
                <th class="text-right">Tổng tiền</th>
                <th class="text-right">HH khách</th>
                <th class="text-right">HH TX</th>
                <th class="text-right">HH CTY</th>
                <th class="text-right">HH sales</th>
                <th class="text-right">Công nợ NH</th>
                <th class="text-right">NH đã TT</th>
                <th class="text-right">Công nợ CTY</th>
                <th class="text-right">CTY đã TT</th>
              </tr>
              <tr>
                <td class="text-center">
                  {{ number_format($arr['total']) }}
                </td>
                <td class="text-center">{{ number_format($arr['da_sd']) }}</td>
                <td class="text-center">{{ number_format($arr['chua_sd']) }}</td>
                <td class="text-center">{{ number_format($arr['tu_lai']) }}</td>
                <td class="text-right">{{ number_format($arr['total_money']) }}</td>
                <td class="text-right">{{ number_format($arr['hh_khach']) }}</td>
                <td class="text-right">{{ number_format($arr['hh_tx']) }}</td>
                <td class="text-right">{{ number_format($arr['hh_cty']) }}</td>
                <td class="text-right">{{ number_format($arr['hh_sales']) }}</td>
                <td class="text-right">{{ number_format($arr['cong_no_cty']) }}</td>
                <td class="text-right">{{ number_format($arr['da_tt_cty']) }}</td>
                <td class="text-right">{{ number_format($arr['cong_no_sales']) }}</td>
                <td class="text-right">{{ number_format($arr['da_tt_sales']) }}</td>
              </tr>
          </table>

        </div>
        </div>
      </div>
      <div class="box">

        @if($flag_pay == 1)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="is_pay_cty" data-table="coupon_code">
                <option value="">--SET CÔNG NỢ NHÀ HÀNG--</option>
                <option value="1">Đã thanh toán</option>
            </select>
          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="is_pay_sales" data-table="coupon_code">
                <option value="">--SET CÔNG NỢ SALES--</option>
                <option value="1">Đã thanh toán</option>
              </select>
          </div>
        </div>
        @endif
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->links() }}
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-hover" id="table-list-data">
              <tr>
                @if($flag_pay == 1)
                <th style="width: 1%"><input type="checkbox" id="check_all" value="1"></th>
                @endif
                <th style="width: 1%">STT</th>
                <th class="text-left">Mã giảm giá</th>
                @if($arrSearch['status'] == 1)
                <th>Nhà hàng</th>
                <th>Sales</th>
                <th>Ngày tạo</th>
                @else
                <th>Sales</th>
                @endif
                <th width="120px">Trạng thái</th>
                @if($arrSearch['status'] != 1)
                  <th>Tổng tiền</th>
                  <th class="text-right">HH khách</th>
                  @if($userRole == 1)
                  <th class="text-right">HH TX</th>
                  <th class="text-right">HH CTY</th>
                  @endif
                  <th class="text-right">HH Sales</th>
                @endif
              </tr>
              <tbody>
              @if( $items->count() > 0 )
                <?php $i = 0; ?>
                @foreach( $items as $item )
                  <?php $i ++; ?>
                <tr id="row-{{ $item->id }}">
                  @if($flag_pay == 1)
                  <td>
                    <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                  </td>
                  @endif
                  <td class="text-center"><span class="order">{{ $i }}</span></td>
                  <td class="text-left">
                      <p class="tr-value"><span  style="font-weight: bold; color: #06b7a4; font-size: 15px">
                        {{ $item->code }}
                      </span>
                      </p>
                      @if($arrSearch['status'] != 1)
                      <p class="tr-value text-bold restaurant" data-id="{{ $item->partner_id }}"><i class="fa fa-map-marker"></i> {{ $item->restaurant->name }}
                       </p>
                       @endif
                  </td>
                  @if($arrSearch['status'] == 1)
                  <td>
                    <p class="tr-value text-bold restaurant" data-id="{{ $item->partner_id }}">
                      {{ $item->restaurant->name }}
                    </p>
                  </td>
                  <td>
                    <p class="sales text-bold" data-id="{{ $item->user_id }}" style="cursor: pointer;">
                      {{ $item->user->name }}
                    </p>
                  </td>
                  <td>
                    <p class="tr-value">{{ date('H:i d/m', strtotime($item->created_at)) }} </p>
                  </td>
                  @else
                  <td>
                    <p class="sales text-bold" data-id="{{ $item->user_id }}" style="cursor: pointer;">
                      {{ $item->user->name }}
                    </p>
                    <p class="tr-value"><i class="fa fa-calendar-minus-o"></i> {{ date('H:i d/m', strtotime($item->created_at)) }} </p>
                  </td>
                  @endif
                  <td>
                    <p class="tr-value">
                    @if($item->status == 1)
                    <label class="label label-success">Chưa sử dụng</label>
                    @else
                    <label class="label label-danger">Đã sử dụng</label>
                    @endif
                  </p>

                    @if($item->status == 2)
                   <p class="tr-value"> <i class="fa fa fa-clock-o"></i> {{ date('H:i d/m', strtotime($item->time_used)) }}</p>
                    @endif
                  </td>
                  @if($arrSearch['status'] != 1)
                    <td class="text-right">
                      @if($item->total_money)
                      {{ number_format($item->total_money) }}
                      @endif
                    </td>
                    <td class="text-right">
                      @if($item->hh_khach)
                      {{ number_format($item->hh_khach) }}
                      @else
                      -
                      @endif
                    </td>
                    @if($userRole == 1)
                      <td class="text-right">
                        @if($item->hh_tx)
                        {{ number_format($item->hh_tx) }}
                        @else
                          @if($item->tu_lai == 1)
                          <label class="label label-info">Tự lái</label>
                          @else
                          -
                          @endif
                        @endif
                      </td>
                      <td class="text-right">
                        @if($item->hh_cty)
                        {{ number_format($item->hh_cty) }}
                        @else
                        -
                        @endif
                      </td>
                    @endif
                    <td class="text-right">
                      @if($item->hh_sales)
                      {{ number_format($item->hh_sales) }}
                      @else
                      -
                      @endif
                    </td>
                  @endif
                </tr>
                @endforeach
              @else
              <tr>
                <td colspan="4">Không có dữ liệu.</td>
              </tr>
              @endif

            </tbody>
            </table>
          </div>
          <div style="text-align:center">
            {{ $items->links() }}
          </div>
        </div>
        @if($flag_pay == 1)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="is_pay_cty" data-table="coupon_code">
                <option value="">--SET CÔNG NỢ NHÀ HÀNG--</option>
                <option value="1">Đã thanh toán</option>
            </select>
          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="is_pay_sales" data-table="coupon_code">
                <option value="">--SET CÔNG NỢ SALES--</option>
                <option value="1">Đã thanh toán</option>
              </select>
          </div>
        </div>
        @endif
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
</section>
<!-- /.content -->
</div>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('#resList').hide();
    $('#btnAddCode').click(function(){
      $('#resList').toggle();
    });
    $('i.copyText').click(function(){
      navigator.clipboard.writeText($(this).data('link'));
      alert("Đã copy");
    });
    $('p.sales').click(function(){
      $('#user_id').val($(this).data('id'));
      $('#searchForm').submit();
    });
    $('p.restaurant').click(function(){
      $('#partner_id').val($(this).data('id'));
      $('#searchForm').submit();
    });
    $('.multi-change-column-value').change(function(){
          var obj = $(this);
          var table = obj.data('table');
          var column = obj.data('column');
          var value = obj.val();
          $('.check_one:checked').each(function(){
              $.ajax({
                url : "{{ route('coupon-code.change-value') }}",
                type : 'GET',
                data : {
                  id : $(this).val(),
                  col : column,
                  value: value,
                  table : table
                },
                success: function(data){
                    console.log(data);
                }
              }); // end ajax
          });// end each

       }); // end change
    $('tr.cost').click(function(){
      $(this).find('.check_one').attr('checked', 'checked');
    });
    $("#check_all").click(function(){
        $('input.check_one').not(this).prop('checked', this.checked);
    });
  });
</script>
@stop
