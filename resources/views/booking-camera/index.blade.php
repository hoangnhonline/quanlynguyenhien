@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="text-transform: uppercase;">
    Quản lý đặt lịch chụp ảnh
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking-camera.index', ['type' => 5]) }}">
     Đặt chụp ảnh</a></li>
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
      <a href="{{ route('booking-camera.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking-camera.index') }}" id="searchForm">
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="PTC ID" value="{{ $arrSearch['id_search'] }}" style="width: 70px">
            </div>
            <div class="form-group">
              <select class="form-control select2" name="camera_id" id="camera_id">
                <option value="">--Thợ chụp--</option>
                @foreach($cameraList as $cam)
                <option value="{{ $cam->id }}" {{ $arrSearch['camera_id'] == $cam->id  ? "selected" : "" }}>{{ $cam->name }}</option>
                @endforeach
              </select>
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
              <div class="form-group chon-thang">
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
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['use_date_from'] }}" style="width: 120px">
            </div>

            @if($time_type == 2)
            <div class="form-group chon-ngay den-ngay">
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" style="width: 120px">
            </div>
             @endif
            @endif

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
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" placeholder="Số ĐT"  style="width: 120px">
            </div>
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
                <label for="status_4">Hoàn Tất</label>
              </div>
              <div class="form-group" style="border-right: 1px solid #9ba39d">
                &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
                <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
              </div>

            </div>
          </form>
        </div>
      </div>
       <!--  <p style="text-align: right;"><a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a> -->
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} booking )</span>
            - Hoa hồng sales : {{ number_format($tong_hoa_hong_sales) }} - Hoa hồng chụp ảnh : {{ number_format($tong_hoa_hong_chup) }}
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
              {{-- <th style="width: 1%"><input type="checkbox" id="check_all" value="1"></th> --}}
              <th style="width: 1%"></th>
              <th width="200">Tên KH</th>
              <th class="text-center" width="100">Ngày chụp</th>
              <th width="100">UNC</th>
              <th style="width: 200px">Địa điểm</th>
              <th class="text-center" width="80">NL/TE/EB</th>
              <th class="text-right" width="100">Tổng tiền/Cọc</th>
              <th class="text-right" width="100">CÒN LẠI</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
                 @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
              <tr class="booking-cam" id="row-{{ $item->id }}">
                {{-- <td class="text-center" style="line-height: 30px">
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">

                  <a href="{{ route('view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>

                </td> --}}
                <td style="text-align: center;"><span class="order">{{ $i }}<br>
                  {{ date('d/m', strtotime($item->created_at)) }}<br>
                  <a href="{{ route('view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>
                </span></td>
                <td>
                  <strong style="color: red;font-size: 16px">PTC{{ $item->id }}
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif </strong>
                  <br>
                   <a style="font-size:17px" href="{{ route( 'booking-camera.edit', $arrEdit) }}">{{ $item->name }}</a> - {{ $item->phone }}</a>
                  <br>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  Sales:
                  @if($item->user)
                  {{ $item->user->name }}
                  @else
                    {{ $item->user_id }}
                  @endif

                @endif

                </td>
                <td class="text-center">
                  {{ date('d/m', strtotime($item->use_date)) }} - {{ $item->time_pickup }}
                  <br>
                  <strong style="color: blue"></strong>
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
                  @if($item->location)
                  {{ $item->location->name }} [{{ $item->location_id }}]
                  @else
                  {{ $item->address }}
                  @endif

                  <br>
                  <span style="color:red">
                    {{ $item->notes }}</span>

                </td>
                 <td class="text-center">
                  {{ $item->adults }} / {{ $item->childs }} / {{ $item->infants }}

                </td>
                <td class="text-right">
                  {{ number_format($item->total_price) }}/{{ number_format($item->tien_coc) }}
                </td>
                <td class="text-right">
                  {{ number_format($item->con_lai) }}
                </td>
                <td style="white-space:nowrap; position: relative;">
                <a href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a href="{{ route( 'booking-camera.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                  <a onclick="return callDelete('PTC{{ $item->id }} - {{ $item->name }}','{{ route( 'booking-camera.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  <p style="clear: both; text-align: right;"><a target="_blank" href="{{ route('history.booking', ['id' => $item->id]) }}">Xem lịch sử</a></p>
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
<input type="hidden" id="table_name" value="articles">
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
  });
</script>
<script type="text/javascript">
    $(document).ready(function(){
      $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.cong-no-tour') }}").submit().attr('action', oldAction);
      });
      $('#temp').click(function(){
        $(this).parents('form').submit();
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
       $('.change_status_bk').click(function(){
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
      $('.change_tien_thuc_thu').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'tien_thuc_thu'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });

        $('tr.booking-cam').click(function(){
          $(this).find('.check_one').attr('checked', 'checked');
        });
        $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              $.ajax({
                url : "{{ route('booking.change-value-by-column') }}",
                type : 'GET',
                data : {
                  id : $(this).val(),
                  col : obj.data('column'),
                  value: obj.val()
                },
                success: function(data){

                }
              });
          });

        });


    });
  </script>
@stop
