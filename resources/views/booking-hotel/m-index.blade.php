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
      <div id="content_alert"></div>
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      @if(Auth::user()->hotline_team == 0)
      <a href="{{ route('booking-hotel.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      @endif
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking-hotel.index') }}" id="searchForm">
            <div class="row">
               <div class="form-group col-xs-12">
              <select class="form-control select2" name="city_id" id="city_id">
                <option value="">--Tỉnh/thành--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}" {{ $arrSearch['city_id'] == $city->id  ? "selected" : "" }}>{{ $city->name }}
                </option>
                @endforeach
              </select>
            </div>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="row">
              
              <div class="form-group col-xs-6">
                <input type="text" class="form-control datepicker" autocomplete="off" name="ptt_ngay_coc" value="{{ $arrSearch['ptt_ngay_coc'] }}" placeholder="PTT ngày cọc">
              </div>
              <div class="form-group col-xs-6">
                <input type="text" class="form-control datepicker" autocomplete="off" name="ptt_pay_date" value="{{ $arrSearch['ptt_pay_date'] }}" placeholder="PTT ngày TT">
              </div>

            </div>
            <div class="row">

              <div class="form-group col-xs-6">
                <input type="text" class="form-control" autocomplete="off" name="id_search" value="{{ $arrSearch['id_search'] }}"  placeholder="PTH ID">
              </div>
              <div class="form-group col-xs-6">
                <select class="form-control select2" name="ptt_pay_status" id="ptt_pay_status">
                  <option value="-1" {{ $arrSearch['ptt_pay_status'] == -1 ? "selected" : "" }}>--PTT TT--</option>
                  <option value="0" {{ $arrSearch['ptt_pay_status'] == 0 ? "selected" : "" }}>PTT chưa TT</option>
                  <option value="1" {{ $arrSearch['ptt_pay_status'] == 1 ? "selected" : "" }}>PTT đã cọc</option>
                  <option value="2" {{ $arrSearch['ptt_pay_status'] == 2 ? "selected" : "" }}>PTT đã TT</option>
                </select>
              </div>
            </div>

            @endif
            <div class="row">
              <div class="form-group col-xs-12">
                <select id="search_by" name="search_by" class="form-control select2">
                  <option value="">--Tìm theo--</option>
                  <option value="checkin" {{ $arrSearch['search_by'] == 'checkin' ? "selected" : "" }}>Ngày checkin</option>
                  <option value="checkout" {{ $arrSearch['search_by'] == 'checkout' ? "selected" : "" }}>Ngày checkout</option>
                  <option value="book_date" {{ $arrSearch['search_by'] == 'book_date' ? "selected" : "" }}>Ngày đặt</option>
                </select>
              </div>

            </div>
            <div class="row">

          <div class="form-group">
                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
            </div>
            </div>
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
            @if($city_id == 1)
          <div class="form-group col-md-12">
              <select class="form-control select2" name="hotel_book" id="hotel_book">
                <option value="">--Đối tác--</option>
                @foreach($partnerList as $hotel)
                <option value="{{ $hotel->id }}" {{ $arrSearch['hotel_book'] == $hotel->id ? "selected" : "" }}>{{ $hotel->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          @endif


             @if(Auth::user()->role == 1 && !Auth::user()->view_only)
             <div class="row">
            <div class="form-group   col-xs-12">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="-1" {{ $arrSearch['user_id'] == -1 ? "selected" : "" }}>--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            </div>
            @endif




            <div class="form-group col-xs-12">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" maxlength="11" placeholder="Điện thoại">
            </div>


            <div class="form-group col-xs-4">
              <input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">MỚI</label>
            </div>
            <div class="form-group col-xs-4">
              <input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn tất</label>
            </div>
            @if($city_id == 1)
            <div class="form-group col-xs-4">
              <input type="checkbox" name="status[]" id="status_4" {{ in_array(4, $arrSearch['status']) ? "checked" : "" }} value="4">
              <label for="status_4">Dời ngày</label>
            </div>
            @endif
            <div class="form-group col-xs-4">
              <input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">HỦY</label>
            </div>

            <input type="hidden" name="sort_by" id="sort_by" value="{{ $arrSearch['sort_by'] }}">
            <div class="col-xs-12">
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            </div>
          </form>
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          @if($city_id == 1)
          <h3 class="box-title col-md-8">Danh sách ( <span class="value">{{ $items->total() }} booking )</span>
            Hoa hồng cty : {{ number_format($tong_hoa_hong_cty) }} - Hoa hồng sales : {{ number_format($tong_hoa_hong_sales) }}
          </h3>
          @else
          <h3 class="box-title col-md-8">Danh sách ( <span class="value">{{ $items->total() }} booking )</span>
          </h3>
          @endif
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
              <li id="row-{{ $item->id }}" style="border-bottom: 1px solid #CCC;padding-top: 10px;padding-bottom: 10px" class="hotel-row">
                <p><strong style="color: red;font-size: 16px">PTH{{ $item->id }}</strong></span> @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @elseif($item->status == 4)
                  <span class="label label-warning">DỜI NGÀY</span>
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
                <p style="font-weight: bold; text-transform: uppercase;">
                  @if($item->hotel)
                  {{ $item->hotel->name }}
                  @endif
                </p>
                <p>
                  @foreach($item->payment as $p)
                    @if($p->type == 1)
                    <img src="{{ Helper::showImageNew($p->image_url)}}" width="80" style="border: 1px solid red" class="img-unc" >
                    @else
                      @if($p->sms)
                      <p class="alert-success sms" style="max-width: 300px; white-space: normal;">{{$p->sms}}</p>
                      @endif<br>
                    @endif
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
                  <?php
                    $error_original_price  = false;
                    $total_original_price = 0;
                    if($item->ptt_tong_tien_phong > 0){
                      $total_original_price = $item->ptt_tong_tien_phong;
                    }else{
                      foreach($item->rooms as $r){

                        $total_original_price += $r->original_price*$r->room_amount*$r->nights;
                        if($r->original_price== 0){
                          $error_original_price  = true;
                        }
                      }

                    }
                  ?>

                  - Tổng tiền: {{ number_format($item->total_price) }}
                  <br>- Cọc: {{ number_format($item->tien_coc) }} <br>- Còn lại: <b>{{ number_format($item->con_lai) }}</b>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <div style="background-color: #fffae6;padding: 10px">
                    PTT tổng tiền phòng: {{ number_format($total_original_price) }}
                    <br>
                    PTT tổng phụ thu: {{ number_format($item->ptt_tong_phu_thu) }}
                    <br>
                    <?php
                    if($item->ptt_tong_tien_goc > 0){
                      $ptt_tong_tien_goc = $item->ptt_tong_tien_goc;
                    }else{
                      $ptt_tong_tien_goc = $total_original_price + $item->ptt_tong_phu_thu;
                    }
                    ?>
                    PTT tổng thanh toán: <label class="label label-warning">{{ number_format($ptt_tong_tien_goc) }}</label>
                  </div>
                  @endif
                </p>
                <p class="text-left">
                  C/I: <strong>{{ date('d/m', strtotime($item->checkin)) }}</strong> - C/O
                  <strong>{{ date('d/m', strtotime($item->checkout)) }}</strong>
                </p>
                <p style="font-weight: bold;color: red">
                  @if($city_id == 1)
                  @if($item->booking_code)
                   {{ $item->booking_code }}
                  @else
                  <input type="text" class="bk_code" style="width: 100px" data-id="{{ $item->id }}" placeholder="Code">
                  @endif

                   <?php $countUNC = $item->payment->count(); ?>
                    @if($item->mail_hotel == 0)
                        <a href="{{ route('mail-preview', ['id' => $item->id]) }}" class="btn btn-sm btn-success" >
                          <i class="  glyphicon glyphicon-envelope"></i> Book phòng
                        </a>
                    @endif
                    @if($item->mail_hotel == 1)

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

                    @endif <!--end city_id-->

                    <span style="float: right;">
                <a href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a href="{{ route( 'booking-hotel.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-hotel.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  </span>
                </p>
                @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <br><input type="checkbox" class="change-column-value" value="2" data-table="booking" data-id="{{ $item->id }}" data-reload="1" data-column="status" data-action="Hoàn tất" data-name="PTH{{$item->id}} - {{ $item->name }} - {{ $item->hotel->name }}"> <label>Hoàn tất</label>
                  @endif

                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)

                    @if($item->check_unc == 0)
                    <br><input id="check_unc_{{ $item->id }}" type="checkbox" name="" class="change-column-value" value="{{ $item->check_unc == 1 ? 0 : 1 }}" data-id="{{ $item->id }}" data-table="booking" data-reload="1" data-column="check_unc"
                    data-action="Đã check UNC" data-name="PTH{{$item->id}} - {{ $item->name }} - {{ $item->hotel->name }}"
                    {{ $item->check_unc == 1 ? "checked" : "" }}>
                    <label for="check_unc_{{ $item->id }}">Đã check UNC</label>
                    @else
                    <br> <label class="label label-success">Đã check UNC</label>
                    @endif

                    @if($item->ptt_pay_status != 2)
                      <br>
                      <input id="check_ptt_tt_{{ $item->id }}" type="checkbox"
                      class="change-column-value" value="2" data-table="booking" data-id="{{ $item->id }}" data-column="ptt_pay_status" data-reload="1"
                      data-action="Đã thanh toán cho khách sạn/đối tác" data-name="PTH{{$item->id}} - {{ $item->name }} - {{ $item->hotel->name }} "
                      {{ $item->ptt_pay_status == 2 ? "checked" : "" }}>
                      <label for="check_ptt_tt_{{ $item->id }}">PTT đã TT</label>
                    @endif

                    @if($item->ptt_pay_status == 0)
                    <br> <label class="label label-info">PTT chưa TT</label>
                    @elseif($item->ptt_pay_status == 1)
                    <br> <label class="label label-warning">PTT đã cọc</label>
                    @elseif($item->ptt_pay_status == 2)
                    <br> <label class="label label-success">PTT đã thanh toán</label>
                    @endif

                  @endif
                  <div class="clearfix"></div>
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
