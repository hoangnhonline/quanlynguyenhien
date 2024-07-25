@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 class="text-center">
    THỐNG KÊ KHÁCH NĂM 2022
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif     
     
      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive" class="text-center" >

            <table class="table table-bordered" style="width: 500px; margin: 0 auto;">
              <tr>
                <th class="text-center">Tháng</th>
                @foreach($arrLevel as $level)
                <th class="text-right">{{ Helper::getLevel($level) }}</th>
                @endforeach
              </tr>
              @foreach($arrMonth as $month)                
                <tr>
                  
                  <td class="text-center">{{ $month }}</td>
                  @foreach($arrLevel as $level)
                  <td class="text-right">{{ number_format($arrResult[$level][$month]) }}</td>
                  @endforeach
                </tr>
                @endforeach
              
            </table>
            <div class="clearfix"></div>
            <div class="col-md-6" style="margin-bottom: 20px">
                <canvas id="myChart" width="400" height="400"></canvas>
            </div>
            <div class="clearfix"></div>
            
          </div>
          <div class="table-responsives">
            <div class="col-md-12" style="margin-bottom: 20px; height: 50px !important;">
                <canvas id="myChartLine"></canvas>
            </div>
          </div>
          <div class="table-responsive">
            <!-- <p style="color: red; font-weight: bold">CHI PHÍ THÁNG {{ $month }}</p> -->
           
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
@php
$totalLevel = count($arrLevel);
@endphp