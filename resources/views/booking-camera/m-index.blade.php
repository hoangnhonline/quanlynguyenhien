@extends('layout')
@section('content')
<div class="content-wrapper">

<!-- Content Header (Page header) -->
<section class="content-header" style="padding-top: 10px;">
  <h1 style="text-transform: uppercase;">
    CHỤP ẢNH TỪ NGÀY {{$arrSearch['use_date_from']}}
  </h1>

</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-md-12">
      <div id="content_alert"></div>
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      <a href="{{ route('booking-camera.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-body">

          <form class="form-inline" role="form" method="GET" action="{{ route('booking-camera.index') }}" id="searchForm">
            <div class="row">

              <div class="form-group col-xs-6" style="padding-right: 0px;">
                <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="PTC ID" value="{{ $arrSearch['id_search'] }}">
              </div>
              <div class="form-group col-xs-6" style="padding-left: 5px;">
                <select class="form-control" name="camera_id" id="camera_id">
                  <option value="">--Thợ chụp--</option>
                  @foreach($cameraList as $cam)
                <option value="{{ $cam->id }}" {{ $arrSearch['camera_id'] == $cam->id  ? "selected" : "" }}>{{ $cam->name }}</option>
                @endforeach
                </select>
              </div>

            </div>
            <div class="row">
              <div class="form-group @if($time_type == 3) col-xs-6 @else col-xs-4 @endif" style="padding-right: 0px">
                <select class="form-control" name="time_type" id="time_type">
                  <option value="">--Thời gian--</option>
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
                <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['use_date_from'] }}">
              </div>

              @if($time_type == 2)
              <div class="form-group col-xs-4" style="padding-left: 0px">
                <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" >
              </div>
              @endif
            @endif
            </div>


            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="row">
              <div class="form-group col-xs-12">
                <select class="form-control select2" name="user_id" id="user_id">
                  <option value="">-Sales-</option>
                  @foreach($listUser as $user)
                  <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                  @endforeach
                </select>
              </div>

            </div>

             @endif
             <div class="row">
              <div class="form-group  col-xs-12">
                <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" placeholder="Số ĐT">
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
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <button type="button" id="btnReset" class="btn btn-danger btn-sm" style="margin-top: -5px">Reset</button>
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
              Tổng <span style="color: red">{{ $items->total() }}</span> booking
            </div>
            <ul style="padding: 10px">
             @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
                <li style="border-bottom: 1px solid #ddd; padding-bottom: 10px; padding-top: 10px; font-size:17px;">
                    @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                    @if($item->status == 1)
                    <span class="label label-info">MỚI</span>
                    @elseif($item->status == 2)
                    <span class="label label-default">HOÀN TẤT</span>
                    @elseif($item->status == 3)
                    <span class="label label-danger">HỦY</span>
                    @endif
                    <span style="color:#06b7a4; text-transform: uppercase;"><span style="color: red">PTT{{ $item->id }}</span> - {{ $item->name }}</span>

                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp


                      @if($item->user)
                  - <span style="font-style: italic;">{{ $item->user->name }}</span>
                  @else
                    {{ $item->user_id }}
                  @endif</span>
                  <br>

                    <i class="glyphicon glyphicon-phone"></i> <a href="tel:{{ $item->phone }}" target="_blank">{{ $item->phone }}</a>
                    <br>
                    Ngày chụp: {{ date('d/m', strtotime($item->use_date)) }} - {{ $item->time_pickup }}
                    <br>
                    <i class="glyphicon glyphicon-map-marker"></i>
                    @if($item->location)
                    {{ $item->location->name }}
                    @else
                    {{ $item->address }}
                    @endif
                    <br>
                    <i class="glyphicon glyphicon-user"></i> NL: <b>{{ $item->adults }}</b> / TE: {{ $item->childs }} / EB: {{ $item->infants }}
                    <br>
                    <i class="  glyphicon glyphicon-usd"></i> Tổng thu: {{ number_format($item->con_lai) }} @if($item->tien_coc > 0)- Cọc: {{ number_format($item->tien_coc) }} @endif @if($item->discount > 0)
                    @endif

                    @if($item->notes)
                    <span style="color:red">{!! nl2br($item->notes) !!}</span>
                    @endif

                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <a style="float:right; margin-left: 2px" onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-camera.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  <a style="float:right; margin-left: 2px" class="btn btn-sm btn-success" href="{{ route('view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>
                   <a style="float:right; margin-left: 2px" href="{{ route( 'booking-camera.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a style="float:right; margin-left: 2px" href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>
                      <a style="float: left;" target="_blank" href="{{ route('history.booking', ['id' => $item->id]) }}">Xem lịch sử</a>
                      <div style="clear: both;"></div>
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
<!-- Modal -->
<div id="capnhatModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" id="modal_content">

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
    $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
      });
    // setTimeout(function(){
    //   window.location.reload();
    // }, 30000);
    $('.hoa_hong_sales').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('save-hoa-hong')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            hoa_hong_sales : obj.val()
          },
          success : function(doc){

          }
        });
      });
  });
  $('.change_status').click(function(){
          var obj = $(this);
          $.ajax({
            url : "{{ route('change-export-status') }}",
            type : 'GET',
            data : {
              id : obj.data('id')
            },
            success: function(){
              window.location.reload();
            }
          });
        });
  $(document).ready(function(){
      $(document).on('click', '.btn-edit-bk', function(){
        var id = $(this).data('id');
        $.ajax({
          url: '{{ route('booking.info') }}',
          type: "GET",
          data: {
              id : id
          },
          success: function(data){
              $('#modal_content').html(data);
              $('#capnhatModal').modal('show');
          }
      });
      });
      $(document).on('click', '#btnSaveInfo', function(){
        var hdv_id = $('#hdv_id').val();
        var hdv_notes = $('#hdv_notes').val();
        var booking_id = $('#booking_id').val();
        var call_status = $('#call_status').val();
        $.ajax({
          url: '{{ route('booking.save-info') }}',
          type: "GET",
          data: {
              hdv_id : hdv_id,
              hdv_notes : hdv_notes,
              booking_id : booking_id,
              call_status : call_status
          },
          success: function(data){
              $('#capnhatModal').modal('hide');
              window.location.reload();
          }
      });
      });
      $('#btnReset').click(function(){
        $('#searchForm select').val('');
        $('#searchForm').submit();
      });
  });
</script>
@stop
