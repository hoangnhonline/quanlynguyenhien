@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    THỐNG KÊ CHI TIẾT TOUR
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
          <form class="form-inline" role="form" method="GET" action="{{ route('report.detail-by-type') }}" id="searchForm">
            <div class="form-group" style="margin-right: 15px">
              <select class="form-control select2" name="type" id="type"  style="width: 200px">
                <option value="1" {{ $type == 1 ? "selected" : "" }}>Tour</option>
                <option value="2" {{ $type == 2 ? "selected" : "" }}>Khách sạn</option>
                <option value="3" {{ $type == 3 ? "selected" : "" }}>Vé VCGT</option>
              </select>
            </div>
            {{-- @include('partials.block-search-date')                       --}}
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

            <table class="table table-bordered table-hover">

                  <tr >
                    <td></td>
                    <td></td>
                    <td class="bg-primary text-right">Tổng
                      <br>
                      {{ number_format($tong_doanh_thu) }}
                      <br>
                      {{ number_format($tong_nl) }} NL

                    </td>
                    @foreach($arrDate as $date)
                    <td class="text-right bg-primary" style="vertical-align: middle;">{{ date('d', strtotime($date)) }}
                      <br>
                      {{ isset($arrBk[$date]['tong_doanh_thu']) ? number_format($arrBk[$date]['tong_doanh_thu']) : "0" }}
                      <br>
                      <span style="white-space: nowrap;">{{ isset($arrBk[$date]['tong_nl']) ? number_format($arrBk[$date]['tong_nl']) : "0" }} NL   </span>
                    </td>
                    @endforeach
                  </tr>
                  @foreach($arrLevel as $level)

                  <?php
                  if($level == 2) $bg = 'class=bg-warning';
                  elseif($level == 6) $bg = 'class=bg-success';
                  elseif($level == 1) $bg = 'class=bg-info';
                  ?>
                  <tr {{ $bg }}  data-level="{{ $level }}">
                    <td  rowspan="5"><p style="width: 100px !important; font-weight: bold;">{{ Helper::getLevel($level) }}</p></td>
                    <td style="white-space: nowrap;font-weight: bold;">Số booking</td>
                    <td class="text-right">{{ $arrByLevel[$level]['so_booking'] }}</td>
                    @foreach($arrDate as $date)
                    <td class="text-right">@if(isset($arrByLevel[$level][$date]) &&  $arrByLevel[$level][$date]['so_booking'] > 0) {{ $arrByLevel[$level][$date]['so_booking'] ."/". $arrBk[$date]['so_booking'] }} @else - @endif
                      @if(isset($arrByLevel[$level][$date]) && $arrBk[$date]['so_booking'] > 0)
                      <br>({{ round($arrByLevel[$level][$date]['so_booking']*100/$arrBk[$date]['so_booking']) }}%)
                      @endif

                    </td>
                    @endforeach



                  </tr>
                  <tr {{ $bg }}>
                    <td style="font-weight: bold;">Doanh thu</td>
                    <td class="text-right">{{ number_format($arrByLevel[$level]['doanh_thu']) }}</td>
                    @foreach($arrDate as $date)
                    <td class="text-right">
                      @if(isset($arrByLevel[$level][$date]) )
                      {{ number_format($arrByLevel[$level][$date]['doanh_thu']) }}
                       @else
                      -
                      @endif
                      @if(isset($arrByLevel[$level][$date]) && $arrBk[$date]['tong_doanh_thu'] > 0)
                      <br>({{ round($arrByLevel[$level][$date]['doanh_thu']*100/$arrBk[$date]['tong_doanh_thu']) }}%)
                      @endif



                    </td>
                    @endforeach
                  </tr>
                  <tr {{ $bg }}>
                    <td style="font-weight: bold;">NL</td>
                    <td class="text-right">{{ number_format($arrByLevel[$level]['nl']) }}</td>
                    @foreach($arrDate as $date)
                    <td class="text-right">{{ isset($arrByLevel[$level][$date]) ? number_format($arrByLevel[$level][$date]['nl']) : '-' }}</td>
                    @endforeach
                  </tr>
                  <tr {{ $bg }}>
                    <td style="font-weight: bold;">TE</td>
                    <td class="text-right">{{ number_format($arrByLevel[$level]['te']) }}</td>
                    @foreach($arrDate as $date)
                    <td class="text-right">{{ isset($arrByLevel[$level][$date]) && $arrByLevel[$level][$date]['te'] > 0 ? number_format($arrByLevel[$level][$date]['te']) : '-' }}</td>
                    @endforeach
                  </tr>
                  <tr {{ $bg }}>
                    <td style="font-weight: bold;">EB</td>
                    <td class="text-right">{{ number_format($arrByLevel[$level]['eb']) }}</td>
                    @foreach($arrDate as $date)
                    <td class="text-right">

                    {{ isset($arrByLevel[$level][$date]) && $arrByLevel[$level][$date]['eb'] > 0 ? number_format($arrByLevel[$level][$date]['eb']) : '-' }}</td>
                    @endforeach
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
