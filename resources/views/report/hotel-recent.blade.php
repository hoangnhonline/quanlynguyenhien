@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    THỐNG KÊ KHÁCH SẠN {{ $month }}/{{ $year }}
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
          <form class="form-inline" role="form" method="GET" action="{{ route('report.hotel-recent') }}" id="searchForm">

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
          <div class="table-responsive">
            <!-- <p style="color: red; font-weight: bold">CHI PHÍ THÁNG {{ $month }}</p> -->
            <table class="table table-bordered table-hover table-list-data">
              <tr>
                <th width="1%" class="text-center">STT</th>
                <th>Tên KS</th>
                <th class="text-center">Số booking</th>
                <th class="text-center">Số loại phòng</th>
                <th class="text-center">Ngày checkin mới nhất</th>
              </tr>
              @php $i = 0;
              $str = '';
              @endphp
              @foreach($items as $item)
              @php
              $str .= $item->hotel_id.",";
              $roomCount = 0;
              if($item->hotel){
                $roomCount = $item->hotel->rooms->count();
              }
              if($roomCount == 0){
              $i++;
              }
              @endphp
              <tr class="{{ $roomCount > 0 ? "hidden" : "" }}">
                <td class="text-center">{{ $i }}</td>

                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->amount_booking }}</td>
                <td class="text-center"><a @if($roomCount == 0) style="color:red; font-size: 18px;" @endif href="{{ route('room.index', ['hotel_id' => $item->hotel_id]) }}" target="_blank">{{ $roomCount }}
                  <br>

                  <a href="{{ route('hotel.edit', $item->hotel_id) }}" target="_blank">Cập nhật thông tin</a>
                  | <a href="{{ route('room.index', ['hotel_id' => $item->hotel_id]) }}" target="_blank">Nhập loại phòng</a>

                </td>
                <td class="text-center">{{ date('d/m/Y', strtotime($item->checkin)) }}</td>
              </tr>
              @endforeach

            </table>
            <?php //echo $str;?>
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
