@extends('layout')
@section('content')
<div class="content-wrapper">

<!-- Content Header (Page header) -->
<section class="content-header" style="padding-top: 10px;">
  <h1 style="text-transform: uppercase;">
    XE NGÀY {{$arrSearch['use_date_from']}}
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
      <a href="{{ route('booking-car.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo booking</a>
      <a href="{{ route('drivers.create') }}" target="_blank" class="btn btn-primary btn-sm" style="margin-bottom:5px">Thêm tài xế</a>
      <div class="panel panel-default">
        <div class="panel-body">

          <form class="form-inline" role="form" method="GET" action="{{ route('booking-car.index') }}" id="searchForm">
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


            <div class="form-group col-xs-12">
              <select class="form-control select2" name="partner_id" id="partner_id">
                <option value="">--Nhà xe--</option>
                @foreach($nhaxeList as $nhaxe)
                <option value="{{ $nhaxe->id }}" {{ $arrSearch['partner_id'] == $nhaxe->id  ? "selected" : "" }}>{{ $nhaxe->name }}</option>
                @endforeach
              </select>
            </div>
              <div class="form-group col-xs-6">
                <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="PTX ID" value="{{ $arrSearch['id_search'] }}">
              </div>

              <div class="form-group col-xs-6">
                <select class="form-control select2" name="car_cate_id" id="car_cate_id">
                  <option value="">-Loại xe-</option>
                  @foreach($carCate as $cate)
                <option value="{{ $cate->id }}" {{ $arrSearch['car_cate_id'] == $cate->id  ? "selected" : "" }}>{{ $cate->name }}</option>
                @endforeach
                </select>
              </div>

            </div>

              <div class="form-group">
                  <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
              </div>
            <div class="row">

            <div class="form-group  col-xs-12">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" placeholder="Số ĐT">

            </div>

            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only || Auth::user()->id == 23)
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
              <div class="form-group col-xs-4">
                <input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
                <label for="status_1">Mới</label>
              </div>
              <div class="form-group col-xs-4">
                <input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
                <label for="status_2">Hoàn Tất</label>
              </div>
              <!-- <div class="form-group col-xs-4">
                <input type="checkbox" name="status[]" id="status_4" {{ in_array(4, $arrSearch['status']) ? "checked" : "" }} value="4">
                <label for="status_4">Dời ngày</label>
              </div> -->
              <div class="form-group col-xs-4" >
                <input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
                <label for="status_3">Huỷ</label>
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
                <li class="mb10" style="border-bottom: 1px solid #ddd; padding-bottom: 10px; padding: 10px; font-size:17px; @if($item->driver_id == 0) background-color:#dee0e3 @endif">
                    @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                    @if($item->status == 1)
                    <span class="label label-info">MỚI</span>
                    @elseif($item->status == 2)
                    <span class="label label-default">HOÀN TẤT</span>
                    @elseif($item->status == 3)
                    <span class="label label-danger">HỦY</span>
                    @endif

                    <span style="color:#06b7a4; text-transform: uppercase;"><span style="color: red">PTX{{ $item->id }}</span>
                    <br> {{ $item->name }} - <i class="glyphicon glyphicon-phone" style="font-size: 13px"></i> <a href="tel:{{ $item->phone }}" target="_blank">{{ $item->phone }}</a> </span>
                  @if($item->status != 3)
                    @php
                    $arrEdit = array_merge(['id' => $item->id], $arrSearch)
                    @endphp

                  <!---user-->
                  @if($item->user)
                  - <span style="font-style: italic;">{{ $item->user->name }}</span>
                  @else
                    {{ $item->user_id }}
                  @endif
                  <!---Nhà xe-->
                  @if($item->partner_id > 0)
                    <br>Nhà xe: <strong>{{ $nhaxeName[$item->partner_id] }} - <a href="tel:{{ $item->partnerNhaXe->phone }}">{{ $item->partnerNhaXe->phone }}</a></strong>  <br>
                  @endif


                   <br>  <i class="  glyphicon glyphicon-calendar"></i> {{ date('d/m', strtotime($item->use_date)) }} - {{ $item->time_pickup }}
                    <br>

                    @foreach($item->locationList as $lo)
                  <i class="glyphicon glyphicon-map-marker"></i> {{ $lo->location->name }}<br>
                  @endforeach
                    <br>
                    <i class="glyphicon glyphicon-user"></i> NL: <b>{{ $item->adults }}</b> / TE: {{ $item->childs }} / EB: {{ $item->infants }}
                    <br>
                    <i class="  glyphicon glyphicon-usd"></i> Tổng thu: {{ number_format($item->con_lai) }} @if($item->tien_coc > 0)- Cọc: {{ number_format($item->tien_coc) }} @endif @if($item->discount > 0)
                    @endif
                    <br>
                    @if($item->notes)
                    <span style="color:red; font-size: 14px; font-style: italic;">{!! nl2br($item->notes) !!}</span>
                    @endif
                    @endif
                    <hr>
                    @if((Auth::user()->role == 1 && !Auth::user()->view_only || Auth::user()->id == 23) && $item->status == 1)
                  <a style="float:right; margin-left: 2px" onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-car.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  @if($item->status != 3)
                  <a style="float:right; margin-left: 2px" class="btn btn-sm btn-success" href="{{ route('view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>
                   <a style="float:right; margin-left: 2px" href="{{ route( 'booking-car.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>

                    <a style="float:right; margin-left: 2px" href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}&back_url={{ urlencode(Request::fullUrl()) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>

                      <a style="float: left;" target="_blank" href="{{ route('history.booking', ['id' => $item->id]) }}">Xem lịch sử</a>     @endif
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
  label{
    cursor: pointer;
  }
</style>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
    });
    $('#btnReset').click(function(){
      $('#searchForm select').val('');
      $('#searchForm').submit();
    });
    $('.change-column-value').change(function(){
          var obj = $(this);
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
    $('#no_driver').change(function(){
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
  });
</script>
@stop
