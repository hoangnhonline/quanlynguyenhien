@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    ADS
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'ads.index' ) }}">ADS</a></li>
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
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('ads.index') }}" id="searchForm">
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="code" placeholder="CODE" value="{{ $arrSearch['code'] }}" style="width: 100px">
            </div>
            <div class="form-group">
              
                      
            <div class="form-group">            
              <select class="form-control select2 search-form-change" name="nguoi_yeu_cau" id="nguoi_yeu_cau">
                <option value="">--Phân loại--</option>
                <option value="1">Facebook</option>
                <option value="2">Google</option>
                <option value="3">Tiktok</option>
              </select>
            </div>            
            <div class="form-group">
              
              <select class="form-control select2 search-form-change" name="time_type" id="time_type">                
                <option value="1" {{ $time_type == 1 ? "selected" : "" }}>Theo tháng</option>                
                <option value="2" {{ $time_type == 2 ? "selected" : "" }}>Khoảng ngày</option>
                <option value="3" {{ $time_type == 3 ? "selected" : "" }}>Ngày cụ thể </option>
              </select>
            </div> 
            @if($time_type == 1)
            <div class="form-group  chon-thang search-form-change">
                
                <select class="form-control select2" id="month_change" name="month">
                  <option value="">--THÁNG--</option>
                  @for($i = 1; $i <=12; $i++)
                  <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                  @endfor
                </select>
              </div>
              <div class="form-group  chon-thang">                
                <select class="form-control select2 search-form-change" id="year_change" name="year">
                  <option value="">--Năm--</option>                 
                  <option value="2022" {{ $year == 2022 ? "selected" : "" }}>2022</option>
                  <option value="2023" {{ $year == 2023 ? "selected" : "" }}>2023</option>
                  <option value="2024" {{ $year == 2024 ? "selected" : "" }}>2024</option>
                </select>
              </div>
            @endif
            @if($time_type == 2 || $time_type == 3)
            
            <div class="form-group chon-ngay">
              
              <input type="text" class="form-control datepicker search-form-change" autocomplete="off" name="use_date_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif" value="{{ $use_date_from }}" style="width: 100px">
            </div>
           
            @if($time_type == 2)
            <div class="form-group chon-ngay den-ngay">
            
              <input type="text" class="form-control datepicker search-form-change" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $use_date_to }}" style="width: 100px">
            </div>
             @endif
            @endif
             
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>            
          </form>         
        </div>
      </div>
      
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} mục )</span></h3>
        </div>
       
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>  
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr>                   
              <th style="width: 1%"><input type="checkbox" id="check_all" value="1"></th>
              <th style="width: 1%">#</th>
              <th class="text-left">Tạo lúc</th>
              <th class="text-left">Ngày</th>
              <th class="text-center">Tỉnh/Thành</th>
              <th class="text-left">Nội dung</th>
              <th class="text-center">UNC</th>
              <th class="text-center">Số lượng</th>
              <th class="text-right">Giá</th>
              <th class="text-right">Tổng tiền</th>
              <th width="1%" style="white-space: nowrap;" class="text-center">Người chi</th>              
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr class="cost" id="row-{{ $item->id }}">                
                <td>
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                </td>                
                <td><span class="order">{{ $i }}</span></td>   
                <td class="text-left">  
                  <strong style="color: red">{{ $item->id }}</strong><br>
                    {{ date('H:i d/m', strtotime($item->created_at)) }}                          
                </td>
                <td class="text-left">  
                    {{ date('d/m/y', strtotime($item->date_use)) }} 
                </td>
                <td class="text-center">
                  @if($item->type == 1)
                  Tour đảo
                  @elseif($item->type == 2)
                  Rạch Vẹm
                  @elseif($item->type == 3)
                  Grand World
                  @elseif($item->type == 5)
                  Bãi Sao-2 đảo
                  @elseif($item->type == 4)
                  Chi phí chung
                  @endif
                  <br>
                  @if($item->city_id == 1)
                  <span style="color: green">Phú Quốc</span>
                  @elseif($item->city_id == 3)
                  <span style="color: yellow">HCM</span>
                  @else
                  <span style="color: blue">Đà Nẵng</span>
                  @endif
                </td>
                <td>
                  @if($item->costType)
                  <?php 
                  $str = $item->partner_id; 
                  ?>
                  <a href="https://plantotravel.vn/cost/{{ Helper::mahoa('mahoa', $str ) }}">{{ $item->costType->name }}</a>
                  @endif
                  @if($item->partner)
                  - {{ $item->partner->name }}
                  @endif
                  @if($item->is_fixed == 1)
                  <label class="label label-success">Cố định</label>
                  @endif
                  <p style="color:red; font-style: italic">{{ $item->notes }}</p>
                  @if($item->unc_type == 2 && $item->image_url)
                  <p style="color: blue; font-style: italic;">
                    {{ $item->image_url }}
                  </p>
                  @endif
                </td>
                <td class="text-center">
                  @if($item->image_url && $item->unc_type == 1)
                  <span style="color: blue; cursor: pointer;" class="img-unc" data-src="{{ config('plantotravel.upload_url').$item->image_url }}">XEM ẢNH</span>               
                  @endif
                </td>
                <td class="text-center">{{ $item->amount }}</td>
                <td class="text-right">{{ number_format($item->price) }}</td>
                <td class="text-right">
                  {{ number_format($item->total_money) }}                   
                </td>
                <td class="text-center" style="white-space: nowrap;">
                  @if($item->nguoi_chi)
                  {{ $collecterNameArr[$item->nguoi_chi] }}
                  @endif
                </td>   
                
              </tr> 
              @endforeach
            @else
            <tr>
              <td colspan="4">Không có dữ liệu.</td>
            </tr>
            @endif

          </tbody>
          </table>
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
@stop
@section('js')
@stop