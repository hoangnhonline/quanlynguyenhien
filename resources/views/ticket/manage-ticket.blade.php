@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Main content -->

<!-- Content Header (Page header) -->
<section class="content-header" style="padding-top: 10px;">
  <h1 style="text-transform: uppercase;">
    Book vé
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
          <form class="form-inline" role="form" method="GET" action="{{ route('ticket.manage') }}" id="searchForm">
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" value="{{ $arrSearch['id_search'] }}" style="width: 70px"  placeholder="PTV ID">
            </div>
            <div class="form-group">
              <select class="form-control" name="time_type" id="time_type">
                <option value="">--Thời gian--</option>
                <option value="1" {{ $time_type == 1 ? "selected" : "" }}>Theo tháng</option>
                <option value="2" {{ $time_type == 2 ? "selected" : "" }}>Khoảng ngày</option>
                <option value="3" {{ $time_type == 3 ? "selected" : "" }}>Ngày cụ thể </option>
              </select>
            </div>
            @if($time_type == 1)
            <div class="form-group  chon-thang">
                <select class="form-control" id="month_change" name="month">
                  <option value="">--THÁNG--</option>
                  @for($i = 1; $i <=12; $i++)
                  <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                  @endfor
                </select>
              </div>
              <div class="form-group  chon-thang">
                <select class="form-control" id="year_change" name="year">
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
            <div class="form-group chon-ngay">
              <input type="text" class="form-control datepicker" autocomplete="off" name="book_date_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['book_date_from'] }}" style="width: 120px">
            </div>

            @if($time_type == 2)
            <div class="form-group chon-ngay den-ngay">
              <input type="text" class="form-control datepicker" autocomplete="off" name="book_date_to" placeholder="Đến ngày" value="{{ $arrSearch['book_date_to'] }}" style="width: 120px">
            </div>
             @endif
             @endif

            <div class="form-group">
              <select class="form-control" name="status" id="status">
                <option value="">--Trạng thái--</option>
                <option value="1" {{ $arrSearch['status'] == 1 ? "selected" : "" }}>Mới</option>
                <option value="2" {{ $arrSearch['status'] == 2 ? "selected" : "" }}>Hoàn tất</option>
                <option value="3" {{ $arrSearch['status'] == 3 ? "selected" : "" }}>Hủy</option>
              </select>
            </div>

            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>

            @endif
            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                <option value="">-- Người thu tiền -- </option>
                <option value="1" {{ $nguoi_thu_tien == 1 ? "selected" : "" }}>Sales</option>
                <option value="2" {{ $nguoi_thu_tien == 2 ? "selected" : "" }}>CTY</option>
                <option value="3" {{ $nguoi_thu_tien == 3 ? "selected" : "" }}>Đại lý</option>
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" style="width: 120px" maxlength="11" placeholder="Điện thoại">
            </div>
            <input type="hidden" name="sort_by" id="sort_by" value="{{ $arrSearch['sort_by'] }}">
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title col-md-8">Danh sách ( <span class="value">{{ $items->total() }} booking )</span>
            - Hoa hồng cty : <span id="tong_hoa_hong_cty"></span>
            - Hoa hồng sales : <span id="tong_hoa_hong_sales"></span>
          </h3>


        </div>

        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
          <table class="table table-bordered" id="table-list-data">
            <tr>
              <th style="width: 1%; white-space: nowrap;">PTT CODE<br>Ngày book</th>
              <th width="200">Tên KH / Điện thoại</th>
              <th width="1%">UNC</th>
              <th width="1%" style="white-space: nowrap;">Ngày giao</th>
              <th width="150">Nơi giao</th>
              <th style="">Loại vé</th>





              <th class="text-right" width="100">Hoa hồng</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
              <?php
 $hoa_hong_cty = $tong_cong_no = 0;
              ?>
            @if( $items->count() > 0 )
              <?php $i = 0;

              ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}" style="border-bottom: 1px solid #ddd !important;">
                <td><span class="order"><strong style="color: red;font-size: 16px">PTV{{ $item->id }}</strong></span><br>
                {{ date('d/m/y', strtotime($item->book_date)) }}
              </td>
                <td>
                  <br>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a style="font-size:17px" href="{{ route( 'booking.edit', $arrEdit) }}">{{ $item->name }} / {{ $item->phone }}</a>
                  <br>
                   @if($item->user)
                  <span style="font-weight: bold;font-size:16px">{{ $item->user->name }}</span>
                  @endif
                  &nbsp; &nbsp; &nbsp;
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-success">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif
                </td>
                <td>
                  @foreach($item->payment as $p)
                  @if($p->type == 1)
                  <img src="{{ Helper::showImageNew($p->image_url)}}" width="80" style="border: 1px solid red" class="img-unc" >
                  @else
                  <br>+ {{number_format($p->amount) }} lúc {{ date('d/m/Y', strtotime($p->created_at)) }}
                  @endif
                  @endforeach
                </td>
                <td class="text-center">
                  {{ date('d/m/y', strtotime($item->use_date)) }}
                </td>
                <td>
                	{{ $item->address}}

                  <br>
                  <span style="color:red">{{ $item->notes }}</span>

                </td>
                <td>
                  <h4 style="color: red; font-style: italic;">
                    @if($item->nguoi_thu_tien == 1)
                    @if($item->user)
                    {{ $item->user->name }} thu tiền
                    @else
                    Sales thu tiền
                    @endif
                    @elseif($item->nguoi_thu_tien == 2)
                    CTY thu tiền
                    @else
                    Đại lý thu tiền
                    @endif
                  </h4>
                  <table class="table table-bordered" id="table-list-data-child">
                    <tr style="background-color: #ddd">
                      <th width="35%">Loại vé</th>
                      <th width="5%" class="text-center">SL</th>
                      <th width="20%" class="text-right">Giá vốn</th>
                      <th width="20%" class="text-right">Giá bán</th>
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
                        <br>
                        {{ number_format($tien_von) }}
                        @php
                        if($item->nguoi_thu_tien == 2) $cong_no_row += ($r->amount*$r->price);
                        @endphp
                      @endif
                    </td>
                    <td class="text-right">
                      @if($item->status != 3)
                      {{ number_format($r->price_sell) }}
                      <br>
                      {{ number_format($tien_ban) }}
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
                    </td>
                    <td class="text-right">
                      <strong style="color: #3c8dbc">{{ number_format($tong_ban) }}</strong>
                    </td>
                    <td class="text-right">
                      <strong>{{ number_format($tong_lai) }}</strong>
                    </td>
                  </tr>
                  </table>

                </td>
                <td class="text-right">
                  <span style="color: #3c8dbc; font-weight: bold;">{{ number_format($tong_hoa_hong) }}</span>
                </td>

                  <td style="white-space:nowrap">
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a href="{{ route( 'ticket.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                </td>
              </tr>
              @php
              if($item->status != 3)
              {
                if($item->nguoi_thu_tien != 2){
                  $hoa_hong_cty+= $tong_hoa_hong;
                }
                $tong_cong_no += $cong_no_row;
              }
              @endphp

              @endforeach
              <tr>
                <td colspan="8" style="text-align: right;">
                  <h3>
                    Tổng công nợ: <span style="color: red">-{{ number_format($tong_cong_no) }}</span> - Tổng hoa hồng: <span style="color: red">{{ number_format($hoa_hong_cty) }}</span> -
                    CHỐT CÔNG NỢ: <span style="color: red">{{ number_format($hoa_hong_cty-$tong_cong_no) }}</span>
                  </h3>
                </td>

              </tr>
            @else
            <tr>
              <td colspan="8">Không có dữ liệu.</td>
            </tr>
            @endif

          </tbody>
          </table>
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
<style type="text/css">
  #div_search_fast{
    display: none;
  }
</style>
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
    $('#tong_hoa_hong_sales, #tong_hoa_hong_cty').html('{{ number_format($hoa_hong_cty/2) }}');
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
