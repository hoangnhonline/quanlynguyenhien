@extends('layout')
@section('content')
<div class="content-wrapper">

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="text-transform: uppercase;">

    Đặt khách sạn

  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking-hotel.index') }}">

    Đặt khách sạn
    </a></li>
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
      <a href="{{ route('booking-hotel.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking-hotel.index') }}" id="searchForm">

            <div class="row">
            <div class="form-group col-xs-4">
              <input type="text" class="form-control" autocomplete="off" name="id_search" value="{{ $arrSearch['id_search'] }}"  placeholder="PTH ID">
            </div>
            <div class="form-group col-xs-4" style="padding-right: 3px">
              <input type="text" class="form-control datepicker" autocomplete="off" name="book_date" value="{{ $arrSearch['book_date_from'] }}"  placeholder="Ngày đặt từ">
            </div>
            <div class="form-group  col-xs-4" style="padding-right: 3px">
              <input type="text" class="form-control datepicker" autocomplete="off" name="book_date_to" value="{{ $arrSearch['book_date_to'] }}"  placeholder="Đến ngày">
            </div>
            </div>
            <div class="row">
              <div class="form-group @if($time_type == 3) col-xs-6 @else col-xs-4 @endif" style="padding-right: 0px">
                <select class="form-control" name="time_type" id="time_type">
                  <option value="">--Ngày checkin--</option>
                  <option value="1" {{ $time_type == 1 ? "selected" : "" }}>Theo tháng</option>
                  <option value="2" {{ $time_type == 2 ? "selected" : "" }}>Khoảng ngày</option>
                  <option value="3" {{ $time_type == 3 ? "selected" : "" }}>Ngày cụ thể </option>
                </select>
              </div>
              @if($time_type == 1)
            <div class="form-group col-xs-4 chon-thang" style="padding-right: 5px">
                <select class="form-control " id="month_change" name="month">
                  <option value="">--THÁNG--</option>
                  @for($i = 1; $i <=12; $i++)
                  <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                  @endfor
                </select>
              </div>
              <div class="form-group col-xs-4 chon-thang" style="padding-left: 5px">
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
              <div class="form-group @if($time_type == 3) col-xs-6 @else col-xs-4 @endif"  style="@if($time_type!=3)padding-right: 5px; @endif padding-left: 5px">
                <input type="text" class="form-control datepicker" autocomplete="off" name="checkin_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['checkin_from'] }}">
              </div>

              @if($time_type == 2)
              <div class="form-group col-xs-4" style="padding-left: 0px">
                <input type="text" class="form-control datepicker" autocomplete="off" name="checkin_to" placeholder="Đến ngày" value="{{ $arrSearch['checkin_to'] }}" >
              </div>
              @endif
            @endif
            </div>
            @if (Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="row">
              <div class="form-group  col-xs-6">
                <input type="text" class="form-control datepicker" autocomplete="off" name="checkout_from" value="{{ $arrSearch['checkout_from'] }}"  placeholder="Check out">
              </div>
              <div class="form-group  col-xs-6">
                <input type="text" class="form-control datepicker" autocomplete="off" name="checkout_to" value="{{ $arrSearch['checkout_to'] }}" placeholder="Đến ngày">
              </div>
            </div>

            @endif
            <div class="row">
            <div class="form-group col-md-12">
              <select class="form-control select2" name="hotel_id" id="hotel_id">
                <option value="">--Khách sạn--</option>
                @foreach($hotelList as $hotel)
                <option value="{{ $hotel->id }}" {{ $arrSearch['hotel_id'] == $hotel->id ? "selected" : "" }}>{{ $hotel->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row">
          <div class="form-group col-md-12">
              <select class="form-control select2" name="hotel_book" id="hotel_book">
                <option value="">--Đối tác--</option>
                @foreach($partnerList as $hotel)
                <option value="{{ $hotel->id }}" {{ $arrSearch['hotel_book'] == $hotel->id ? "selected" : "" }}>{{ $hotel->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

            <div class="row">
            {{-- <div class="form-group   col-xs-6">
              <select class="form-control" name="status" id="status">
                <option value="">--Trạng thái--</option>
                <option value="1" {{ $arrSearch['status'] == 1 ? "selected" : "" }}>Mới</option>
                <option value="2" {{ $arrSearch['status'] == 2 ? "selected" : "" }}>Hoàn tất</option>
                <option value="3" {{ $arrSearch['status'] == 3 ? "selected" : "" }}>Hủy</option>
              </select>
            </div>  --}}
             @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group   col-xs-12">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="-1" {{ $arrSearch['user_id'] == -1 ? "selected" : "" }}>--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            @endif


          </div>
          <div class="row">
            <div class="form-group">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" style="width: 120px" maxlength="11" placeholder="Điện thoại">
            </div>
          </div>
          <div class="row" style="font-size: 12px;">
            <div class="form-group col-xs-4">
              <input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">MỚI</label>
            </div>
            <div class="form-group col-xs-4">
              <input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn tất</label>
            </div>
            <!-- <div class="form-group col-xs-4">
              <input type="checkbox" name="status[]" id="status_4" {{ in_array(4, $arrSearch['status']) ? "checked" : "" }} value="4">
              <label for="status_4">Dời ngày</label>
            </div>  -->
            <div class="form-group col-xs-4">
              <input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">HỦY</label>
            </div>

          </div>
            <input type="hidden" name="sort_by" id="sort_by" value="{{ $arrSearch['sort_by'] }}">
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title col-md-8">Danh sách ( <span class="value">{{ $items->total() }} booking )</span>
            Hoa hồng cty : {{ number_format($tong_hoa_hong_cty) }} - Hoa hồng sales : {{ number_format($tong_hoa_hong_sales) }}
          </h3>
          <div class="col-md-4 text-right form-inline">
            <div class="form-group">
            <label>Sắp xếp theo: </label>
            <select id="sort_by_change" class="form-control">
              <option value="created_at" {{ $arrSearch['sort_by'] == 'created_at' ? "selected" : "" }}>Ngày Book</option>
              <option value="checkin" {{ $arrSearch['sort_by'] == 'checkin' ? "selected" : "" }}>Ngày Check-in</option>
            </select>
          </div>
          </div>

        </div>

        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
            <ul style="padding: 10px">
              @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <li id="row-{{ $item->id }}" style="border-bottom: 1px solid #CCC;padding-top: 10px;padding-bottom: 10px">
                <p><strong style="color: red;font-size: 16px">PTH{{ $item->id }}</strong></span> @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif  @if(Auth::user()->role == 1 && !Auth::user()->view_only)

                  @if($item->user)
                  {{ $item->user->name }}
                  @endif
                @endif - Ngày đặt: {{ date('d/m', strtotime($item->book_date)) }}
              </p>
                <p>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a style="font-size:17px" href="{{ route( 'booking-hotel.edit', $arrEdit) }}">{{ $item->name }} / {{ $item->phone }}</a>
                  {{ $item->email }}
                  @if($item->ctv)
                    - CTV: {{ $item->ctv->name }}
                  @endif
                </p>
                <p>
                  @if($item->hotel)
                  {{ $item->hotel->name }}
                  @endif
                </p>
                <p>
                  @foreach($item->payment as $p)
                  <img src="{{ config('plantotravel.upload_url').$p->image_url }}" width="80" style="border: 1px solid red" class="img-unc" >
                  @endforeach

                </p>
                 <p class="text-left">
                  NL/TE/EB: <strong>{{ $item->adults }}</strong>/<strong>{{ $item->childs }}</strong>/<strong>{{ $item->infants }}</strong>
                  - Book: <?php
                  $hotel_book = 'Trực tiếp KS';
                  if($item->hotel_book){
                    $hotel_book = $item->hotelBook->name;
                  }
                  echo "<span style='color:red'>".$hotel_book.'</span>';
                  ?>
                </p>
                <p class="text-left">
                  @php
                  $error_original_price  = false;
                  $total_original_price = 0;
                  @endphp
                  @foreach($item->rooms as $r)
                  @php
                 // dd($r);
                  $total_original_price += $r->original_price*$r->room_amount*$r->nights;
                  if($r->original_price== 0){
                    $error_original_price  = true;
                  }
                  @endphp
                  @endforeach
                  Tổng tiền: {{ number_format($item->total_price) }} - Cọc: {{ number_format($item->tien_coc) }} - Còn lại: <b>{{ number_format($item->con_lai) }}</b>
                  <br>
                  Tổng gốc: {{ number_format($total_original_price) }}
                </p>
                <p class="text-left">
                  C/I: <strong>{{ date('d/m', strtotime($item->checkin)) }}</strong> - C/O
                  <strong>{{ date('d/m', strtotime($item->checkout)) }}</strong>
                </p>
                <p style="font-weight: bold;color: red">

                  @if($item->booking_code)
                   {{ $item->booking_code }}
                  @else
                  <input type="text" class="bk_code" style="width: 100px" data-id="{{ $item->id }}" placeholder="Code">
                  @endif


                    @if($item->mail_hotel == 0)
                    <a href="{{ route('mail-preview', ['id' => $item->id]) }}" class="btn btn-sm btn-success" >
                      <i class="  glyphicon glyphicon-envelope"></i> Book phòng
                    </a>
                    @else
                    <span class="label label-info" style="margin-bottom: 5px; clear:both">Đã mail book</span>
                    <div class="clearfix" style="margin-bottom: 5px"></div>
                    @if($item->mail_customer == 0)
                    <a href="{{ route('mail-confirm', ['id' => $item->id]) }}" class="btn btn-sm btn-success" >
                      <i class="  glyphicon glyphicon-envelope"></i> Gửi xác nhận
                    </a>
                    @else
                    <span class="label label-info" style="margin-bottom: 5px; clear:both">Đã mail khách</span>
                    @endif
                    @endif
                    <span style="float: right;">
                <a href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a href="{{ route( 'booking-hotel.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-hotel.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  </span>
                </p>
                <br>
                  <input type="radio" name="nguoi_thu_coc{{ $item->id }}" class="change-value" value="1" id="nguoi_thu_coc{{ $item->id }}_1" data-col="nguoi_thu_coc" data-value="1" data-id="{{ $item->id }}" {{ $item->nguoi_thu_coc == 1 ? "checked=checked" : "" }}> <label for="nguoi_thu_coc{{ $item->id }}_1">PTT thu cọc</label><br>
                  <input type="radio" name="nguoi_thu_coc{{ $item->id }}" class="change-value" value="2" id="nguoi_thu_coc{{ $item->id }}_2" data-col="nguoi_thu_coc" data-value="2" data-id="{{ $item->id }}" {{ $item->nguoi_thu_coc == 2 ? "checked" : "" }}> <label for="nguoi_thu_coc{{ $item->id }}_2">CTV thu cọc</label>
              </li>
              @endforeach
            @else
            <li>Không có dữ liệu.</li>
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
<style type="text/css">
  .form-group{
    margin-bottom: 10px !important;
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
    $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
      });
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
      $('.change-value').click(function(){
          var obj = $(this);
          var col = obj.data('col');
          var value = obj.data('value');
          $.ajax({
            url : "{{ route('booking.change-value-by-column') }}",
            type : 'GET',
            data : {
              id : obj.data('id'),
              col : col,
              value : value
            },
            success: function(){
              window.location.reload();
            }
          });
        });
    });
  </script>
@stop
