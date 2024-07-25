@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    THỐNG KÊ ĐỐI TÁC @if($arrSearch['month'] && $arrSearch['time_type'] == 1){{ $arrSearch['month'] }}/ @endif{{ $year }}
  </h1>
 <!--  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'food.index' ) }}">Món ăn</a></li>
    <li class="active">Danh sách</li>
  </ol> -->
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('report.doi-tac-theo-nam') }}" id="searchForm">
            <div class="form-group">
              <select class="form-control select2" name="tour_id" id="tour_id">
                <option value="">--Tour--</option>
                @foreach($tourSystem as $tour)
                <option value="{{ $tour->id }}" {{ $arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                @endforeach
              </select>
            </div>
             <div class="form-group">
              <select class="form-control select2" name="time_type" id="time_type" style="width: 200px">
               <!--  <option value="">--Thời gian--</option> -->
                <!-- <option value="1" {{ $arrSearch['time_type'] == 1 ? "selected" : "" }}>Theo tháng</option> -->
                <option value="4" {{ $arrSearch['time_type'] == 4 ? "selected" : "" }}>Theo năm</option>
                <!-- <option value="2" {{ $arrSearch['time_type'] == 2 ? "selected" : "" }}>Khoảng ngày</option>
                <option value="3" {{ $arrSearch['time_type'] == 3 ? "selected" : "" }}>Ngày cụ thể </option> -->
              </select>
            </div>

            @if($arrSearch['time_type'] == 1)
            <div class="form-group  chon-thang">
              <select class="form-control select2" id="month_change" name="month">
                <option value="">--THÁNG--</option>
                @for($i = 1; $i <=12; $i++)
                <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $arrSearch['month'] == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                @endfor
              </select>
            </div>
            @endif
            @if($arrSearch['time_type'] == 1 || $arrSearch['time_type'] == 4)
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

            @if($arrSearch['time_type'] == 2 || $arrSearch['time_type'] == 3)
            <div class="form-group chon-ngay">
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="@if($arrSearch['time_type'] == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['use_date_from'] }}" style="width: 120px">
            </div>
            @if($arrSearch['time_type'] == 2)
            <div class="form-group chon-ngay den-ngay">
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" style="width: 120px">
            </div>
             @endif
            @endif
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
             <div class="form-group">
            <select class="form-control select2" name="level" id="level">
              <option value="" >--Phân loại sales--</option>
              <option value="1" {{ $level == 1 ? "selected" : "" }}>CTV</option>
              <option value="2" {{ $level == 2 ? "selected" : "" }}>ĐỐI TÁC</option>

              <option value="6" {{ $level == 6 ? "selected" : "" }}>NV SALES</option>
              <option value="7" {{ $level == 7 ? "selected" : "" }}>Gửi Bến</option>
            </select>
          </div>

            <!-- <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>      -->

            @endif
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <button type="button" class="btn btn-info btn-sm" id="export" style="margin-top: -5px">Export</button>
          </form>
        </div>
      </div>
      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table-bordered table table-hover" style="width: 1260px">
              <tr id="target">
                <th></th>

                @foreach($arrMonth as $month)
                <th class="text-center">{{ $month }}</th>
                @endforeach
              </tr>
              <tr>
                <th>Tổng đối tác</th>
                @foreach($arrMonth as $month)
                <td class="text-center">{{ isset($arrAllUserByMonth[$month]) ? ($arrAllUserByMonth[$month]) : '' }}</td>
                @endforeach
              </tr>
              <tr>
                <th>Tổng đối tác có khách</th>
                @foreach($arrMonth as $month)
                <td class="text-center">{{ isset($arrDoiTac[$month]) ? count($arrDoiTac[$month]) : '' }}</td>
                @endforeach
              </tr>
              <tr>
                <th>Đối tác mới<label class="label label-success">New</label></th>
                @foreach($arrMonth as $month)
                <td class="text-center">{{ isset($arrUserByMonth[$month]) ? count($arrUserByMonth[$month]) : '-' }}</td>
                @endforeach
              </tr>
              <tr>
                <th>Đối tác mới có booking </th>
                @foreach($arrMonth as $month)
                <td class="text-center">{{ isset($arrIdNew[$month]) ? count($arrIdNew[$month]) : '-' }}</td>
                @endforeach
              </tr>

            </table>

          </div>
          <div class="table-responsive">

            <table class="table table-bordered table-hover table-list-data" id="table-1" style="width: 1260px">
              <thead>
                <tr>
                  <th style="width: 70px !important;" class="text-center">STT</th>
                  <th class="text-left" style="width: 250px !important;">Tên đối tác</th>
                  @foreach($arrMonth as $month)
                  <th class="text-center" width="70px">{{ $month }}</th>
                  @endforeach
                </tr>
              </thead>

              @php $i = 0; @endphp
              @foreach($arrName as $user_id => $name)
              @php $i++; @endphp

              <tr>
                <td class="text-center">{{ $i }}</td>
                <td class="text-left">{{ $name }} <span style="color: red">{{ isset($arrLastDate[$user_id]) ? date('d/m/Y', strtotime($arrLastDate[$user_id])) : '' }}</span>

                   <!-- <input style="cursor: pointer;" id="status_{{ $user_id }}" type="checkbox" name="status_{{ $user_id }}" class="change-column-value" data-table="users" data-reload="1" data-column="status" value="0" data-id="{{ $user_id }}">  -->
                </td>
                @foreach($arrMonth as $month)
                <td class="text-center" @if(!$arrByDoiTac[$user_id][$month]) style="background-color:#ffe6e6" @endif>{{ $arrByDoiTac[$user_id][$month] }}
                    @if(isset($arrMinMonth[$user_id]) && $arrMinMonth[$user_id] == $month && $arrByDoiTac[$user_id][$month] > 0)
                    <label class="label label-success">New</label>
                    @endif
                </td>
                @endforeach
              </tr>
              @endforeach

            </table>

          </div>
          <div class="table-responsive">
              <table id="header-fixed" class="table table-bordered table-hover" style="width: 1260px"></table>
            </div>
        </div>
      </div>
    </div>
    <!-- /.col -->
  </div>
</section>
<!-- /.content -->
</div>
<style type="text/css">
  #header-fixed {
    position: fixed;
    top: 0px;
    display: none;
    background-color: green;
    color: #FFF
  }

  #table-1 td:hover, #table-1 tr:hover{
    background-color: #f7e4c3 !important;
  }
</style>
@stop
@section('js')
<script type="text/javascript">
  var tableOffset = $("#table-1").offset().top;
  var $header = $("#table-1 > thead").clone();
  var $fixedHeader = $("#header-fixed").append($header);

  $(window).bind("scroll", function() {
    var offset = $(this).scrollTop();

    if (offset >= tableOffset && $fixedHeader.is(":hidden")) {
      $fixedHeader.show();
    } else if (offset < tableOffset) {
      $fixedHeader.hide();
    }
  });

  $('#export').click(function (e){
      e.preventDefault();
      var tableToExcel = (function() {
          var uri = 'data:application/vnd.ms-excel;base64,'
              , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
              , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
              , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
          return function(table, name) {
              if (!table.nodeType) table = document.getElementById(table)
              var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
              window.location.href = uri + base64(format(template, ctx))
          }
      })()('table-1', 'datatable')
  })
</script>
@stop
