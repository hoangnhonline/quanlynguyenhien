@extends('layout')
@section('content')
<div class="content-wrapper">

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="text-transform: uppercase;">
    Quản lý đặt vé
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking-ticket.index') }}">
    Quản lý đặt vé
    <li class="active">Danh sách</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      <a href="{{ route('booking-ticket.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking-ticket.index') }}" id="searchForm">
          <div class="row">
              <div class="form-group col-xs-6">
              <select class="form-control select2" name="city_id" id="city_id">
                <option value="">--Tỉnh/thành--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}" {{ $arrSearch['city_id'] == $city->id  ? "selected" : "" }}>{{ $city->name }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-xs-6">
              <input type="text" class="form-control" autocomplete="off" name="code_nop_tien" placeholder="Code nộp tiền" value="{{ $arrSearch['code_nop_tien'] }}">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-xs-6">
              <input type="text" class="form-control" autocomplete="off" name="id_search" value="{{ $arrSearch['id_search'] }}"  placeholder="PTV ID">
            </div>
            <div class="form-group col-xs-6">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" maxlength="11" placeholder="Điện thoại">
            </div>

          </div>
            <div class="form-group">
              <select class="form-control select2" name="search_by" id="search_by">
                <option value="">--Tìm theo--</option>
                <option value="1" {{ $search_by == 1 ? "selected" : "" }}>Tìm theo ngày giao</option>
                <option value="2" {{ $search_by == 2 ? "selected" : "" }}>Tìm theo ngày đặt</option>
              </select>
            </div>

            <div class="form-group col-xs-12">
                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group col-xs-12">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>

            @endif
            @if($ctvList->count() > 0)
            <div class="form-group col-xs-12">
              <select class="form-control select2" name="ctv_id" id="ctv_id">
                <option value="">--Người book--</option>
                @foreach($ctvList as $ctv)
                <option value="{{ $ctv->id }}" {{ $arrSearch['ctv_id'] == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                @endforeach
              </select>
            </div>
            @endif
          </div>
            <div class="row">
              <div class="form-group col-xs-6">
                <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                  <option value="">--Thu tiền--</option>
                  @foreach($collecterList as $col)
                  <option value="{{ $col->id }}" {{ $nguoi_thu_tien == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-xs-6">
                <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                  <option value="">--Thu cọc--</option>
                  @foreach($collecterList as $col)
                  <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_coc'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>




              <div class="clearfix"></div>
              <div class="row">
                <div class="form-group col-xs-4">
                &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
                <label for="status_1">Mới</label>
              </div>
              <div class="form-group col-xs-4" style="padding-right: 0px">
                &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
                <label for="status_2">Hoàn Tất</label>
              </div>
              <div class="form-group col-xs-4">
                &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
                <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
              </div>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
              <div class="form-group col-xs-12">
                &nbsp;&nbsp;&nbsp;<input type="checkbox"name="unc0" id="unc0" {{ $arrSearch['unc0'] == 1 ? "checked" : "" }} value="1">
                <label for="unc0">Chưa check <span style="color: red">UNC</span></label>
              </div>
              @endif
            <div class="row">
            <div class="col-xs-12">
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button></div> </div>


          </form>
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title col-md-8">Danh sách ( <span class="value">{{ $items->total() }} booking )</span>
            - Hoa hồng @if($city_id == 1) cty @endif: <span id="tong_hoa_hong_cty"></span>
            @if($city_id == 1)
            - Hoa hồng sales : <span id="tong_hoa_hong_sales"></span>
            @endif
          </h3>
          <div class="form-group" style="float: right">
            <a href="javascript:;" class="btn btn-success btn-sm" id="btnContentNop">LẤY ND NỘP TIỀN</a>
          </div>

        </div>

        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">

              <ul style="padding: 5px;">
                <?php
               $hoa_hong_cty = $tong_cong_no = 0;
              ?>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>

                <li id="row-{{ $item->id }}" class="mb10" style="list-style: none;border-bottom: 1px solid #ddd; padding-bottom: 10px; padding: 10px; font-size:17px;background-color:#e6e6e6">
                @if($item->status == 1)
                    <span class="label label-info">MỚI</span>
                    @elseif($item->status == 2)
                    <span class="label label-default">HOÀN TẤT</span>
                    @elseif($item->status == 3)
                    <span class="label label-danger">HỦY</span>
                    @endif

                    <span style="color:#06b7a4; text-transform: uppercase;"><span style="color: red">PTV{{ $item->id }}</span>
                    <br> {{ $item->name }} - <i class="glyphicon glyphicon-phone" style="font-size: 13px"></i>
                    <a href="tel:{{ $item->phone }}" target="_blank">{{ $item->phone }}</a> </span>
                @if($item->user)
                  <br><i class="glyphicon glyphicon-user"></i> <span style="font-style: italic;">{{ $item->user->name }}</span>
                  @else
                    {{ $item->user_id }}
                  @endif
                  @foreach($item->payment as $p)
                  <img src="{{ config('plantotravel.upload_url').$p->image_url }}" width="80" style="border: 1px solid red" class="img-unc" >
                  @endforeach

                 <br>  <i class="  glyphicon glyphicon-calendar"></i> {{ date('d/m', strtotime($item->use_date)) }} - {{ $item->address}}

                </p>

                  <h4 style="color: red; font-style: italic;">
                    @if($item->nguoi_thu_tien)
                    <span style="color: red">{{ $collecterNameArr[$item->nguoi_thu_tien] }}</span>
                    @endif
                  </h4>
                  <table class="table table-bordered" id="table-list-data-child">
                    <tr style="background-color: #ddd">
                      <th width="35%">Loại vé</th>
                      <th width="5%" class="text-center">SL</th>
                      <th class="text-right">Giá vốn<br>Giá bán</th>

                      <th width="20%" class="text-right">Lãi</th>
                    </tr>
                  @php
                  $tong_hoa_hong = $cong_no_row = $tong_von = $tong_ban = $tong_lai = $tong_ve = 0;
                  @endphp
                  @foreach($item->tickets as $r)
                  @php

                    $hh = $r->amount*$r->price_sell - $r->amount*$r->price;
                    if($item->status != 3)
                    {
                      $tong_hoa_hong+= $hh;
                      $tong_von += $tien_von = $r->amount*$r->price;
                      $tong_ban += $tien_ban = $r->amount*$r->price_sell;
                      $tong_lai += $hh;
                      $tong_ve += $r->amount;
                    }
                  @endphp
                  <tr>
                    <td>
                      @if(isset($ticketTypeArr[$r->ticket_type_id]))
                      {{ $ticketTypeArr[$r->ticket_type_id] }}
                      @else
                      {{ $r->ticket_type_id }}
                      @endif
                    </td>

                    <td class="text-center">
                      {{ $r->amount }}
                    </td>
                    <td class="text-right">
                      @if($item->status != 3)
                        {{ number_format($r->price) }}

                        @php
                        if($item->nguoi_thu_tien == 2) $cong_no_row += ($r->amount*$r->price);
                        @endphp
                      @endif

                      @if($item->status != 3)
                      <br>{{ number_format($r->price_sell) }}

                      @endif
                    </td>
                    <td class="text-right">
                      @if($item->status != 3)
                     {{ number_format($hh) }}
                     @endif
                    </td>

                  </tr>

                  @endforeach
                  <tr style="background-color: #999">
                    <td class="text-left">
                      TỔNG
                    </td>
                    <td  class="text-center">
                      <strong>{{ $tong_ve }}</strong>
                    </td>
                    <td class="text-right">
                      <strong style="color: red">{{ number_format($tong_von) }}</strong>
                    <br>
                      <strong style="color: #3c8dbc">{{ number_format($tong_ban) }}</strong>
                    </td>
                    <td class="text-right">
                      <strong>{{ number_format($tong_lai) }}</strong>
                    </td>
                  </tr>
                  </table>



                <i class="  glyphicon glyphicon-usd"></i>
                Tổng tiền: {{ number_format($item->total_price) }}

                @if($item->tien_coc > 0)
                - Cọc: {{ number_format($item->tien_coc) }}
                @endif
                <br>
                    @if($item->notes)
                    <span style="color:red; font-size: 14px; font-style: italic;">{!! nl2br($item->notes) !!}</span>
                    @endif
                    @if(Auth::user()->role == 1)
                  <input type="text" class="form-control change-column-value-booking number" data-column="hoa_hong_sales" placeholder="HH sales" data-id="{{ $item->id }}" value="{{ $item->hoa_hong_sales ? number_format($item->hoa_hong_sales) : "" }}" style="text-align: right;width: 100%;float:right;margin-top:5px">
                  <input type="text" class="form-control change-column-value-booking number" data-column="hoa_hong_cty" placeholder="HH CTY" data-id="{{ $item->id }}" value="{{ $item->hoa_hong_cty ? number_format($item->hoa_hong_cty) : "" }}" style="text-align: right;width: 100%;float:right;margin-top:5px">
                  @endif
                  <div class="clearfix"></div>
                  <p style="white-space:nowrap" class="mt15 text-right">
                <a href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a href="{{ route( 'booking-ticket.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-ticket.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  <a class="btn btn-sm btn-success" href="{{ route('ticket.view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>
                </p>
              </li>
              @php
              if($item->status != 3)
              {

                $hoa_hong_cty+= $tong_hoa_hong;

                $tong_cong_no += $cong_no_row;
              }
              @endphp
              @endforeach
            @else
            <li>
              <p >Không có dữ liệu.</p>
            </li>
            @endif
            <ul>

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
<!-- Modal -->
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
@stop
@section('js')

<script type="text/javascript">

  $(document).ready(function(){
     $('#tong_hoa_hong_cty').html('{{ number_format($hoa_hong_cty) }}');
  $('img.img-unc').click(function(){
    $('#unc_img').attr('src', $(this).attr('src'));
    $('#uncModal').modal('show');
  });
    $('#sort_by_change').change(function(){
      $('#sort_by').val($(this).val());
      $('#searchForm').submit();
    });
  });
</script>
<script type="text/javascript">
    $(document).ready(function(){
      $('.bk_code').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('saveBookingCode')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            booking_code : obj.val()
          },
          success : function(doc){

          }
        });
      });
    });
  </script>
@stop
