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


            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" value="{{ $arrSearch['id_search'] }}" style="width: 70px"  placeholder="PTH ID">
            </div>
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="book_date" value="{{ $arrSearch['book_date_from'] }}" style="width: 100px" placeholder="Ngày đặt từ">
            </div>
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="book_date_to" value="{{ $arrSearch['book_date_to'] }}" style="width: 100px" placeholder="Đến ngày">
            </div>

            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="checkout_from" value="{{ $arrSearch['checkout_from'] }}" style="width: 110px" placeholder="Check out">
            </div>
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="checkout_to" value="{{ $arrSearch['checkout_to'] }}" style="width: 110px" placeholder="Đến ngày">
            </div>
            @endif
            <div class="form-group">
              <select class="form-control" name="time_type" id="time_type">
                <option value="">--Ngày checkin--</option>
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
                </select>
              </div>
            @endif
            @if($time_type == 2 || $time_type == 3)
            <div class="form-group chon-ngay">
              <input type="text" class="form-control datepicker" autocomplete="off" name="checkin_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['checkin_from'] }}" style="width: 110px">
            </div>

            @if($time_type == 2)
            <div class="form-group chon-ngay den-ngay">
              <input type="text" class="form-control datepicker" autocomplete="off" name="checkin_to" placeholder="Đến ngày" value="{{ $arrSearch['checkin_to'] }}" style="width: 110px">
            </div>
             @endif
            @endif
            <div class="form-group">
              <select class="form-control select2" name="hotel_id" id="hotel_id">
                <option value="">--Khách sạn--</option>
                @foreach($hotelList as $hotel)
                <option value="{{ $hotel->id }}" {{ $arrSearch['hotel_id'] == $hotel->id ? "selected" : "" }}>{{ $hotel->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="hotel_book" id="hotel_book">
                <option value="">--Đối tác--</option>
                @foreach($partnerList as $hotel)
                <option value="{{ $hotel->id }}" {{ $arrSearch['hotel_book'] == $hotel->id ? "selected" : "" }}>{{ $hotel->name }}</option>
                @endforeach
              </select>
            </div>
             @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
            <select class="form-control select2" name="level" id="level">
              <option value="" >--Level--</option>
              <option value="1" {{ $level == 1 ? "selected" : "" }}>Sales</option>
              <option value="2" {{ $level == 2 ? "selected" : "" }}>Đối tác</option>
              <option value="3" {{ $level == 3 ? "selected" : "" }}>Tài xế</option>
            </select>
          </div>

            <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="-1" {{ $arrSearch['user_id'] == -1 ? "selected" : "" }}>--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>



            @endif
            <div class="form-group">
              <select class="form-control select2" name="ctv_id" id="ctv_id">
                <option value="">--Người book--</option>
                @foreach($ctvList as $ctv)
                <option value="{{ $ctv->id }}" {{ $arrSearch['ctv_id'] == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" style="width: 120px" maxlength="11" placeholder="Điện thoại">
            </div>
            <input type="hidden" name="sort_by" id="sort_by" value="{{ $arrSearch['sort_by'] }}">
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <div>
              <div class="form-group">
              <input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">Mới</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn Tất</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_4" {{ in_array(4, $arrSearch['status']) ? "checked" : "" }} value="4">
              <label for="status_4">Dời ngày</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="hh0" id="hh0" {{ $arrSearch['hh0'] == 1 ? "checked" : "" }} value="1">
              <label for="hh0">Chưa tính HH&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
              <div class="form-group" style="float: right;">
                &nbsp;&nbsp;&nbsp;<input type="checkbox"name="unc0" id="unc0" {{ $arrSearch['unc0'] == 1 ? "checked" : "" }} value="1">
                <label for="unc0">Chưa check <span style="color: red">UNC</span></label>
              </div>
              @endif
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="error" id="error" {{ $arrSearch['error'] == 1 ? "checked" : "" }} value="1">
              <label for="error">Error&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>

            </div>
          </form>
          <div class="form-group" style="float: right">
            <a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a>
          </div>

        </div>
      </div>
      <div class="panel">
        <div class="panel-body">
          <ul style="padding: 0px;">
          @foreach($userArr as $user_id)
          <li style="display: inline;
    float: left;
    list-style: none; height: 45px;">
          @if(isset($arrUser[$user_id]))
          <span data-id="" class="label label-default" style="padding: 10px 5px;margin-right: 10px; font-size: 12px">{{ $arrUser[$user_id]->name }}</span>

          </li>
          @endif
          @endforeach
        </ul>
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title col-md-8">Danh sách ( <span class="value">{{ $items->total() }} booking )</span> - Hoa hồng cty : {{ number_format($tong_hoa_hong_cty) }} - Hoa hồng sales : {{ number_format($tong_hoa_hong_sales) }}
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
          <table class="table table-bordered" id="table-list-data">
            <tr>
              <th style="width: 1%; white-space: nowrap;">PTT CODE<br>Ngày book</th>
              <th width="200">Tên KH / Điện thoại / Email</th>
              <th>UNC</th>
              <th>Khách sạn</th>

              <th class="text-center" width="150">
            Checkin - Checkout <br>
              NL/TE/EB</th>
              <th>Tổng giá gốc</th>
              <th class="text-right" width="100">
                 <br>
                Tổng tiền/Cọc/Còn lại</th>

              <th class="text-right" width="100">HH CTY<br>HH Sales</th>
              <th style="width: 1%">CODE</th>
              <th width="1%;white-space:nowrap">Email</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}"
                @php
                if($item->checkin < date('Y-m-d') && $item->hoa_hong_cty <= 0 && $item->user_id != 18 && $item->status == 1){
                  echo 'style="background-color:#ffe6e6"';
                }
                @endphp
                >
                <td><span class="order"><strong style="color: red;font-size: 16px">@if($item->id > 6196) PTH{{ $item->id }} @else PTT{{ $item->id }} @endif</strong></span><br>
                {{ date('d/m/y', strtotime($item->book_date)) }}
              </td>
                <td>
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @elseif($item->status == 4)
                  <span class="label label-warning">DỜI NGÀY</span>
                  @endif
                  <br>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a style="font-size:17px" href="{{ route( 'booking-hotel.edit', $arrEdit) }}">{{ $item->name }}</a>
                  <br>
                  ĐT: {{ $item->phone }}
                  <br>
                  {{ $item->email }}<br>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                    @if($item->user)
                      Sales: {{ $item->user->name }}
                    @endif
                  @endif
                  @if($item->ctv)
                    - {{ $item->ctv->name }}
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
                <td>
                	@if($item->hotel)
                  <b>{{ $item->hotel->name }}</b>
                  <br>
                  @endif
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
                  {{ $r->room_name }} - {{ number_format($r->original_price) }} {{ number_format($r->price_sell) }}<br>
                  @endforeach
                  <br>
                  <span style="color:red">{{ $item->notes_hotel }}</span>
                  @if($item->notes)
                  <br> <span style="color: #f39c12; font-style: italic">{{ $item->notes }}</span>
                  @endif

                </td>

                 <td class="text-center">
                  <p style="font-weight: bold; color: #06b7a4">{{ date('d/m', strtotime($item->checkin)) }} - {{ date('d/m', strtotime($item->checkout)) }}</p>
                  <strong>{{ $item->adults }}</strong>/<strong>{{ $item->childs }}</strong>/<strong>{{ $item->infants }}</strong>
                  <?php
                  $hotel_book = 'Trực tiếp KS';
                  if($item->hotelBook){
                    $hotel_book = $item->hotelBook->name;
                  }
                  echo "<br><span style='color:red'>".$hotel_book.'</span>';
                  ?>
                </td>
                <td class="text-right">
                  {{ number_format($total_original_price) }}
                </td>
                <td class="text-right">

                  {{ number_format($item->total_price) }}/{{ number_format($item->tien_coc) }}<br>
                  <b>{{ number_format($item->con_lai) }}</b>
                  <br>

                  <input type="radio" name="nguoi_thu_coc{{ $item->id }}" class="change-value" value="1" id="nguoi_thu_coc{{ $item->id }}_1" data-col="nguoi_thu_coc" data-value="1" data-id="{{ $item->id }}" {{ $item->nguoi_thu_coc == 1 ? "checked=checked" : "" }}> <label for="nguoi_thu_coc{{ $item->id }}_1">PTT thu cọc</label><br>
                  <input type="radio" name="nguoi_thu_coc{{ $item->id }}" class="change-value" value="2" id="nguoi_thu_coc{{ $item->id }}_2" data-col="nguoi_thu_coc" data-value="2" data-id="{{ $item->id }}" {{ $item->nguoi_thu_coc == 2 ? "checked" : "" }}> <label for="nguoi_thu_coc{{ $item->id }}_2">CTV thu cọc</label>

                </td>


                <td class="text-right" @if($error_original_price) style="background-color: red; color: white" @endif>
                  {{ number_format($item->hoa_hong_cty) }}<br>
                  {{ number_format($item->hoa_hong_sales) }}
                </td>
                <td style="font-weight: bold;color: red">

                  @if($item->booking_code)
                   {{ $item->booking_code }}
                  @else
                  <input type="text" class="bk_code form-control" style="width: 100px" data-id="{{ $item->id }}">
                  @endif

                </td>
                  <td>
                    @if($item->mail_hotel == 0)
                    <a href="{{ route('mail-preview', ['id' => $item->id]) }}" class="btn btn-sm btn-success" >
                      <i class="  glyphicon glyphicon-envelope"></i> Book phòng
                    </a>
                    @else
                    <p class="label label-info" style="margin-bottom: 5px; clear:both">Đã mail book</p>
                    <div class="clearfix" style="margin-bottom: 5px"></div>
                    @if($item->mail_customer == 0)
                    <a href="{{ route('mail-confirm', ['id' => $item->id]) }}" class="btn btn-sm btn-success" >
                      <i class="  glyphicon glyphicon-envelope"></i> Gửi xác nhận
                    </a>
                    @else
                    <p class="label label-info" style="margin-bottom: 5px; clear:both">Đã mail khách</p>
                    <div class="clearfix"></div>
                    @endif
                    @endif

                  </td>
                  <td style="white-space:nowrap">
                <a href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a href="{{ route( 'booking-hotel.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-hotel.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif

                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <input type="checkbox" name="" class="change_status" value="2" data-id="{{ $item->id }}">
                  <br>
                  @endif
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <br><input id="check_unc_{{ $item->id }}" type="checkbox" name="" class="change-column-value" value="{{ $item->check_unc == 1 ? 0 : 1 }}" data-id="{{ $item->id }}" data-column="check_unc" {{ $item->check_unc == 1 ? "checked" : "" }}>
                  <label for="check_unc_{{ $item->id }}">Đã check UNC</label>
                  @endif
                </td>
              </tr>
              @endforeach
            @else
            <tr>
              <td colspan="9">Không có dữ liệu.</td>
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

      $('.change_status').click(function(){
          var obj = $(this);
          $.ajax({
            url : "{{ route('change-status') }}",
            type : 'GET',
            data : {
              id : obj.data('id')
            },
            success: function(){
              //window.location.reload();
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
              ///window.location.reload();
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
      $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.cong-no-hotel') }}").submit().attr('action', oldAction);
      });
    });
  </script>
@stop
