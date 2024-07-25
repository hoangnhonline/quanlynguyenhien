@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Main content -->

<!-- Content Header (Page header) -->
<section class="content-header" style="padding-top: 10px;">
  <h1 style="text-transform: uppercase;">
    Quản lý đặt vé
  </h1>

</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      @if(Auth::user()->hotline_team == 0)
      <a href="{{ route('booking-ticket.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      @endif
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking-ticket.index') }}" id="searchForm">
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" value="{{ $arrSearch['id_search'] }}" style="width: 70px"  placeholder="PTV ID">
            </div>
            <div class="form-group">
              <select class="form-control select2" name="city_id" id="city_id">
                <option value="">--Tỉnh/thành--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}" {{ $arrSearch['city_id'] == $city->id  ? "selected" : "" }}>{{ $city->name }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="code_nop_tien" placeholder="Code nộp tiền" value="{{ $arrSearch['code_nop_tien'] }}" style="width: 107px">
            </div>
            <div class="form-group">
              <select class="form-control select2" name="search_by" id="search_by">
                <option value="">--Tìm theo--</option>
                <option value="1" {{ $search_by == 1 ? "selected" : "" }}>Tìm theo ngày giao</option>
                <option value="2" {{ $search_by == 2 ? "selected" : "" }}>Tìm theo ngày đặt</option>
              </select>
            </div>
            <div class="form-group">
                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
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
            @if($ctvList->count() > 0)
            <div class="form-group">
              <select class="form-control select2" name="ctv_id" id="ctv_id">
                <option value="">--Người book--</option>
                @foreach($ctvList as $ctv)
                <option value="{{ $ctv->id }}" {{ $arrSearch['ctv_id'] == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                @endforeach
              </select>
            </div>
            @endif
            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                <option value="">-- Người thu tiền -- </option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $nguoi_thu_tien == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                <option value="">--Người thu cọc--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_coc'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" style="width: 120px" maxlength="11" placeholder="Điện thoại">
            </div>

            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
              <div class="form-group" style="float: right;">
                &nbsp;&nbsp;&nbsp;<input type="checkbox"name="unc0" id="unc0" {{ $arrSearch['unc0'] == 1 ? "checked" : "" }} value="1">
                <label for="unc0">Chưa check <span style="color: red">UNC</span></label>
              </div>
              @endif
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <div class="clearfix"></div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">Mới</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn Tất</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
          </form>

        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title col-md-8">Danh sách ( <span class="value">{{ $items->total() }} booking )</span>
            - Hoa hồng @if($city_id == 1) cty @endif : <span id="tong_hoa_hong_cty"></span>
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
          <table class="table table-bordered" id="table-list-data">
            <tr>
              <th style="width: 1%" class="text-center" ><input type="checkbox" id="check_all" value="1"></th>

              <th width="200">PTT CODE<br>Tên KH / Điện thoại</th>
              <th width="1%">UNC</th>
              <th width="1%" style="white-space: nowrap;">Ngày giao</th>
              <th width="150">Nơi giao</th>
              <th style="">Loại vé</th>
              <th class="text-right" width="100">Hoa hồng</th>
              @if(!Auth::user()->view_only)
              <th width="1%;white-space:nowrap">Thao tác</th>
              @endif
            </tr>
            <tbody>
              <?php
               $hoa_hong_cty = $tong_cong_no = $hoa_hong_hotline = $hoa_hong_sales = 0;
              ?>
            @if( $items->count() > 0 )
              <?php $i = 0;

              ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}" class="booking" style="border-bottom: 1px solid #ddd !important;">
                <td class="text-center" style="line-height: 30px">

                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">


                  <a href="{{ route('ticket.view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>
                  <br>{{ date('d/m H:i', strtotime($item->created_at)) }}
                  <span class="label label-sm label-danger" id="error_unc_{{ $item->id }}"></span>
                </td>

                <td>
                  <span class="order"><strong style="color: red;font-size: 16px">PTV{{ $item->id }}</strong></span><br>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a style="font-size:17px" href="{{ route( 'booking-ticket.edit', $arrEdit) }}">{{ $item->name }} / {{ $item->phone }}</a>
                  <br>
                   @if($item->user)
                  <span style="font-weight: bold;font-size:16px">{{ $item->user->name }}</span>

                  @endif
                  @if($item->ctv)
                    - {{ $item->ctv->name }}
                  @endif
                  &nbsp; &nbsp; &nbsp;
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-success">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif

                    @if($item->source == 'website')
                    <span class="label label-danger">Website</span>
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
                    @if($item->nguoi_thu_tien)
                    <span style="color: red">{{ $collecterNameArr[$item->nguoi_thu_tien] }}</span>
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
                      @if($item->source == 'website')
                          @foreach($item->webTickets as $r)
                              <tr>
                                  <td>
                                      {{$r->ticketType->name}}
                                  </td>

                                  <td class="text-center">
                                      NL: {{ $r->adults }}<br/>
                                      TE: {{ $r->childs }}
                                  </td>
                                  <td class="text-right">
                                      NL: {{ number_format($r->total_price_adult) }}<br/>
                                      TE: {{ number_format($r->total_price_child) }}<br/>
                                      = {{ number_format($r->total_amount) }}
                                  </td>
                                  <td class="text-right">
                                      {{ number_format($r->total_amount) }}
                                  </td>
                                  <td class="text-right">
                                      0
                                  </td>
                              </tr>
                          @endforeach
                      @else
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
                      if($item->user_id == 18){
                        $hoa_hong_hotline += $hh/2;
                      }
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
                      @endif
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
                <td class="text-right" width="150px">
                  <span style="color: #3c8dbc; font-weight: bold;">{{ number_format($tong_hoa_hong) }}</span>
                  @if(Auth::user()->role == 1)
                  <input type="text" class="form-control change-column-value-booking number" data-column="hoa_hong_sales" placeholder="HH sales" data-id="{{ $item->id }}" value="{{ $item->hoa_hong_sales ? number_format($item->hoa_hong_sales) : "" }}" style="text-align: right;width: 100%;float:right;margin-top:5px">
                  <input type="text" class="form-control change-column-value-booking number" data-column="hoa_hong_cty" placeholder="HH CTY" data-id="{{ $item->id }}" value="{{ $item->hoa_hong_cty ? number_format($item->hoa_hong_cty) : "" }}" style="text-align: right;width: 100%;float:right;margin-top:5px">
                  @endif
                  @if($item->code_nop_tien)

                   <br>   <span style="font-weight: bold; color: #00a65a" title="Mã nộp tiền">{{ $item->code_nop_tien }}</span>
                    @if($item->time_nop_tien)
                    <label class="label label-success">Đã nộp tiền</label>
                    @endif
                    @endif


                </td>

                  @if(!Auth::user()->view_only)
                  <td style="white-space:nowrap">
                  <a href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a href="{{ route( 'booking-ticket.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-ticket.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <br><input id="check_unc_{{ $item->id }}" type="checkbox" name="" class="change-column-value" value="{{ $item->check_unc == 1 ? 0 : 1 }}" data-id="{{ $item->id }}" data-column="check_unc" {{ $item->check_unc == 1 ? "checked" : "" }}>
                  <label for="check_unc_{{ $item->id }}">Đã check UNC</label>
                  @endif
                </td>
                  @endif
              </tr>
              @php
              if($item->status != 3)
              {

                $hoa_hong_cty += $tong_hoa_hong;
                $hoa_hong_sales += $item->hoa_hong_sales;
                $tong_cong_no += $cong_no_row;
              }
              @endphp

              @endforeach
              <tr>
                <td colspan="8" style="text-align: right;">
                  <h3>
                   Tổng hoa hồng: <span style="color: red">{{ number_format($hoa_hong_cty) }}</span>
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
    $('#tong_hoa_hong_cty').html('{{ number_format($hoa_hong_cty/2 + $hoa_hong_hotline) }}');
    $('#tong_hoa_hong_sales').html('{{ number_format($hoa_hong_sales) }}');
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
      $('.change-column-value').change(function(){
          var obj = $(this);
          if(obj.data('column') == 'cano_id'){
           // alert('Tất cả các booking cùng HDV sẽ được gán chung vào cano này');
          }
          $.ajax({
            url : "{{ route('booking.change-value-by-column') }}",
            type : 'GET',
            data : {
              id : obj.data('id'),
              col : obj.data('column'),
              value: obj.val()
            },
            success: function(data){
                console.log(data);
            }
          });
       });
      $('.change-column-value-booking').change(function(){
          var obj = $(this);
          ajaxChange(obj.data('id'), obj);
       });
    });
    function ajaxChange(id, obj){
        $.ajax({
            url : "{{ route('booking.change-value-by-column') }}",
            type : 'GET',
            data : {
              id : id,
              col : obj.data('column'),
              value: obj.val()
            },
            success: function(data){
                console.log(data);
            }
          });
      }
  </script>
@stop
