@extends('layout')
@section('content')
<div class="content-wrapper">
  
<!-- Content Header (Page header) -->
<section class="content-header" style="padding-top: 10px;">
  <h1 style="text-transform: uppercase;">    
    CHI PHÍ - NGÀY {{$arrSearch['use_date_from']}}
  </h1>
  
</section>

<!-- Main content -->
<section class="content">
  
  <div class="row">
    <div class="col-md-12">
      <!-- <div id="content_alert"></div> -->
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      <a href="{{ route('cost.create',['date_use' => $date_use]) }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">        
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('cost.index') }}" id="searchForm">
            <div class="row">
            <div class="form-group col-xs-6">
              <label for="type">Loại chi phí</label>
              <select class="form-control select2" name="cate_id" id="cate_id">
                <option value="">--Tất cả--</option>
                @foreach($cateList as $cate)
                <option value="{{ $cate->id }}" {{ $arrSearch['cate_id'] == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-xs-6">
              <label for="nguoi_chi">Người chi</label>
              <select class="form-control" name="nguoi_chi" id="nguoi_chi">
                <option value="">--Tất cả--</option>
                <option value="1" {{ $nguoi_chi == 1 ? "selected" : "" }}>CTY</option>
                <option value="2" {{ $nguoi_chi == 2 ? "selected" : "" }}>ĐIỀU HÀNH</option>
                <option value="3" {{ $nguoi_chi == 3 ? "selected" : "" }}>CÔNG NỢ</option>
              </select>
            </div> 
            </div>
            <div class="row">
             <div class="form-group  col-xs-12">
              <select class="form-control select2" name="time_type" id="time_type">
                <option value="">-Thời gian-</option>
                <option value="1" {{ $time_type == 1 ? "selected" : "" }}>Theo tháng</option>
                <option value="2" {{ $time_type == 2 ? "selected" : "" }}>Khoảng ngày</option>
                <option value="3" {{ $time_type == 3 ? "selected" : "" }}>Ngày cụ thể </option>
              </select>
            </div> 
            @if($time_type == 1)
            <div class="form-group  chon-thang  col-xs-6">
                <select class="form-control select2" id="month_change" name="month">
                  <option value="">--Tháng--</option>
                  @for($i = 1; $i <=12; $i++)
                  <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                  @endfor
                </select>
              </div>
              <div class="form-group  chon-thang col-xs-6">                
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
            
            <div class="form-group chon-ngay col-xs-6">
              <label for="use_date_from">&nbsp;&nbsp;&nbsp;@if($time_type == 2) Từ ngày @else Ngày @endif </label>
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="Từ ngày" value="{{ $arrSearch['use_date_from'] }}" >
            </div>
           
            @if($time_type == 2)
            <div class="form-group chon-ngay den-ngay col-xs-6">
              <label for="use_date_to">&nbsp;&nbsp;&nbsp;Đến ngày</label>
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}">
            </div>
             @endif
            @endif
            </div>
            <button type="submit" class="btn btn-success btn-sm">Lọc</button>
          </form>         
        </div>
      </div>
      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>  
          <div class="table-responsive">
            <div style="font-size: 18px;padding: 10px; border-bottom: 1px solid #ddd">
              Tổng: <span class="value">{{ $items->total() }} mục </span> - Tổng tiền: <span style="color:red">{{ number_format($total_actual_amount) }}</span>           
            </div>
            <ul style="padding: 10px">
             @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
                <li style="border-bottom: 1px solid #ddd; padding-bottom: 10px; padding-top: 10px; font-size:17px;">
                  @if($item->costType)
                  <a href="{{ route( 'cost.edit', [ 'id' => $item->id ]) }}">{{ $item->costType->name }}</a> - {{ date('d/m', strtotime($item->date_use)) }} 
                  @else
                  {{ $item->cost_type_id }}
                  @endif
                  @if($item->partner)
                  - {{ $item->partner->name }}
                  @endif
                  <br>
                    @if($item->nguoi_chi == 1)
                    <i class="  glyphicon glyphicon-user"></i> Người chi: CTY
                    @elseif($item->nguoi_chi == 2)
                    <i class="  glyphicon glyphicon-user"></i> Người chi: ĐIỀU HÀNH
                    @elseif($item->nguoi_chi == 3)
                    <i class="  glyphicon glyphicon-user"></i> <span style="color: red">CÔNG NỢ</span>
                    @endif 
                    @if($item->booking_id)
                    <br>
                    <i class="glyphicon glyphicon-off"></i><span style="color: red"> PTT{{ $item->booking_id }}</span>
                    @endif                     
                    <br>               
                    <i class="  glyphicon glyphicon-usd"></i>{{ number_format($item->amount) }} x {{ number_format($item->price) }} = {{ number_format($item->total_money) }}
                           <br>                      
                    @if($item->notes)
                    <span style="color:red">{!! nl2br($item->notes) !!}</span>
                    @endif    
                    <div class="clearfix" style="margin-top: 3px; margin-bottom: 3px"></div>
                   @if($item->image_url)
                  <img src="{{ config('plantotravel.upload_url').$item->image_url }}" height="80"  width="80" style="border: 1px solid red" class="img-unc">
                  @endif
                  @if($item->costType)
                  <a style="float: right" onclick="return callDelete('{{ $item->costType->name . " - ".number_format($item->total_money) }}','{{ route( 'cost.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  <a style="float: right; margin-right: 5px" href="{{ route( 'cost.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  <div class="clearfix"></div>
                </li>              
              @endforeach
            @else
            <li>
              <p>Không có dữ liệu.</p>
            </li>
            @endif
            </ul>
          
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
<input type="hidden" id="table_name" value="articles">
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
<style type="text/css">
  .form-group{
    margin-bottom: 10px !important;
  }
</style>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    }); 
  });
</script>
@stop