@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    THỐNG KÊ TỔNG QUAN
  </h1>
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
          <form class="form-inline" role="form" method="GET" action="{{ route('report.general') }}" id="searchForm">
            {{-- @include('partials.block-search-date') --}}
            <div class="form-group">
                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
            </div>
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>

          </form>
        </div>
      </div>
      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive" >

            <table class="table table-bordered">
                <tr>
                  <th></th>
                  <th style="width: 26%" class="text-right">Tour</th>
                  <th style="width: 26%" class="text-right">Khách sạn</th>
                  <th style="width: 18%" class="text-right">Vé vui chơi</th>
                  <th style="width: 10%" class="text-right">Xe</th>
                  <th style="width: 10%" class="text-right">Vé máy bay</th>
                </tr>
                <tr>
                  <th>Tổng booking</th>
                  <td class="text-right">
                    <b>{{  isset($arrTotalByType['total_bk'][1]) ? number_format($arrTotalByType['total_bk'][1]) : '-' }}</b>
                    <table class="table" style="margin-top: 20px; display: none;">
                      <tr>
                        <td></td>
                        <td width="25%">Ghép</td>
                        <td width="20%">Vip</td>
                        <td width="25%">Thuê cano</td>
                      </tr>
                      @foreach($arrCountByTour[1] as $tour_id => $item)
                      <tr>
                        <td class="text-left">
                          {{ $tourSystemName[$tour_id]['name'] }}
                        </td>
                        <td>
                          <b>{{ isset($item[1]) ? number_format($item[1]) : "-" }}</b>
                        </td>
                        <td>
                          <b>{{ isset($item[2]) ? number_format($item[2]) : "-" }}</b>
                        </td>
                        <td>
                          <b>{{ isset($item[3]) ? number_format($item[3]) : "-" }}</b>
                        </td>
                      </tr>
                      @endforeach
                    </table>
                  </td>
                  <td class="text-right">
                    <b>{{ isset($arrTotalByType['total_bk'][2]) ? number_format($arrTotalByType['total_bk'][2]) : '-' }}</b>
                  </td>
                  <td class="text-right">
                    <b>{{  isset($arrTotalByType['total_bk'][3]) ? number_format($arrTotalByType['total_bk'][3]) : '-' }}</b>
                  </td>
                  <td class="text-right">
                    <b>{{  isset($arrTotalByType['total_bk'][4]) ? number_format($arrTotalByType['total_bk'][4]) : '-' }}</b>
                  </td>
                  <td class="text-right">
                    <b>{{  isset($arrTotalByType['total_bk'][6]) ? number_format($arrTotalByType['total_bk'][6]) : '-' }}</b>
                  </td>
                </tr>
                <tr>
                  <th>Doanh thu</th>
                  <td class="text-right">
                    <b>{{  isset($arrTotalByType['doanh_thu'][1]) ? number_format($arrTotalByType['doanh_thu'][1]) : '-' }}</b>
                    <table class="table" style="margin-top: 20px; display: none;">
                      <tr>
                        <td></td>
                        <td width="25%">Ghép</td>
                        <td width="20%">Vip</td>
                        <td width="25%">Thuê cano</td>
                      </tr>
                      @foreach($arrDoanhThu[1] as $tour_id => $item)
                      <tr>
                        <td class="text-left">
                          {{ $tourSystemName[$tour_id]['name'] }}
                        </td>
                        <td>
                          <b>{{ isset($item[1]) ? number_format($item[1]) : "-" }}</b>
                        </td>
                        <td>
                          <b>{{ isset($item[2]) ? number_format($item[2]) : "-" }}</b>
                        </td>
                        <td>
                          <b>{{ isset($item[3]) ? number_format($item[3]) : "-" }}</b>
                        </td>
                      </tr>
                      @endforeach
                    </table>

                  </td>
                  <td class="text-right">
                    <b>{{ isset($arrTotalByType['doanh_thu'][2]) ? number_format($arrTotalByType['doanh_thu'][2]) : '-' }}</b>
                  </td>
                  <td class="text-right">
                    <b>{{  isset($arrTotalByType['doanh_thu'][3]) ? number_format($arrTotalByType['doanh_thu'][3]) : '-' }}</b>
                  </td>
                  <td class="text-right">
                    <b>{{  isset($arrTotalByType['doanh_thu'][4]) ? number_format($arrTotalByType['doanh_thu'][4]) : '-' }}</b>
                  </td>
                  <td class="text-right">
                    <b>{{  isset($arrTotalByType['doanh_thu'][6]) ? number_format($arrTotalByType['doanh_thu'][6]) : '-' }}</b>
                  </td>
                </tr>


              @foreach($arrLevel as $level)
              <tr>
                <td class="text-left">{{ Helper::getLevel($level) }} - {{ $level }}</td>
                <td style="width: 26%" class="text-right">
                  @if(isset($arrDoanhThuLevel[1][$level]))
                    {{ number_format($arrDoanhThuLevel[1][$level]) }}
                    @php
                    $percent = $arrDoanhThuLevel[1][$level]*100/$arrTotalByType['doanh_thu'][1];
                    @endphp
                    ({{ number_format($percent, 2, '.', '') }}%)
                  @endif
                </td>
                <td style="width: 26%" class="text-right">
                  @if(isset($arrDoanhThuLevel[2][$level]))
                    {{ number_format($arrDoanhThuLevel[2][$level]) }}
                    @php
                    $percent = $arrDoanhThuLevel[2][$level]*100/$arrTotalByType['doanh_thu'][2];
                    @endphp
                    ({{ number_format($percent, 2, '.', '') }}%)
                  @endif
                </td>
                <td style="width: 18%" class="text-right">
                  @if(isset($arrDoanhThuLevel[3][$level]))
                    {{ number_format($arrDoanhThuLevel[3][$level]) }}
                    @php
                    $percent = $arrDoanhThuLevel[3][$level]*100/$arrTotalByType['doanh_thu'][3];
                    @endphp
                    ({{ number_format($percent, 2, '.', '') }}%)
                  @endif
                </td>
                <td style="width: 10%" class="text-right">
                  @if(isset($arrDoanhThuLevel[4][$level]))
                    {{ number_format($arrDoanhThuLevel[4][$level]) }}
                    @php
                    $percent = $arrDoanhThuLevel[4][$level]*100/$arrTotalByType['doanh_thu'][4];
                    @endphp
                    ({{ number_format($percent, 2, '.', '') }}%)
                  @endif
                </td>
                <td style="width: 10%" class="text-right">
                  @if(isset($arrDoanhThuLevel[6][$level]))
                    {{ number_format($arrDoanhThuLevel[6][$level]) }}
                    @php
                    $percent = $arrDoanhThuLevel[6][$level]*100/$arrTotalByType['doanh_thu'][6];
                    @endphp
                    ({{ number_format($percent, 2, '.', '') }}%)
                  @endif
                </td>
              </tr>
              @endforeach

            </table>
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
	.table-list-data td,.table-list-data th{
    border: 1px solid #000 !important;
    font-weight: bold;
    color: #000
  }
  tr.vip{
    background-color: #02fa7a
  }
  tr.thue-cano{
    background-color: #ebd405
  }
</style>
<input type="hidden" id="table_name" value="articles">
@stop
@section('js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
  $(document).ready(function(){
    $("input[name='time_type']").change(function(){
        $('#searchForm').submit();
    });
  });

</script>
@stop
