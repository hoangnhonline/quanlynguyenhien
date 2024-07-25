@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Khách chụp ảnh Grand World
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'grandworld-schedule.index' ) }}">Khách chụp ảnh Grand World</a></li>
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
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('grandworld-schedule.index') }}" id="searchForm">

           <div class="form-group">
              <select class="form-control select2" name="camera_id" id="camera_id">
                <option value="">--Thợ chụp--</option>
                @foreach($cameraList as $cam)
                <option value="{{ $cam->id }}" {{ $arrSearch['camera_id'] == $cam->id  ? "selected" : "" }}>{{ $cam->name }}</option>
                @endforeach
              </select>
            </div>
            @if($time_type == 1)
            <div class="form-group  chon-thang">
                <label>&nbsp;&nbsp;&nbsp;THÁNG</label>
                <select class="form-control select2" id="month_change" name="month">
                  <option value="">--Chọn--</option>
                  @for($i = 1; $i <=12; $i++)
                  <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                  @endfor
                </select>
              </div>
              <div class="form-group  chon-thang">
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

            <div class="form-group chon-ngay">
              <label for="use_date_from">&nbsp;&nbsp;&nbsp;@if($time_type == 2) Từ ngày @else Ngày @endif </label>
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="Từ ngày" value="{{ $arrSearch['use_date_from'] }}" style="width: 100px">
            </div>

            @if($time_type == 2)
            <div class="form-group chon-ngay den-ngay">
              <label for="use_date_to">&nbsp;&nbsp;&nbsp;Đến ngày</label>
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" style="width: 100px">
            </div>
             @endif
            @endif
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>
      <p style="text-align: right;"><a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} mục )</span> - Tổng tiền: <span style="color:red">{{ number_format($total_actual_amount) }} </span>- Số lượng: <span style="color:red">{{ $total_quantity }} </span></h3>
        </div>
        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="nguoi_chi">
                <option value="">--SET NGƯỜI CHI--</option>
                <option value="1">CTY</option>
                <option value="2">ĐIỀU HÀNH</option>
                <option value="3">CÔNG NỢ</option>
              </select>
          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="city_id">
                <option value="">--SET TỈNH/THÀNH--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
              </select>
          </div>
          <div class="form-group">
              <select class="form-control select2 multi-change-column-value" data-column="cate_id">
                <option value="">--SET LOẠI CHI PHÍ--</option>
                @foreach($cateList as $cate)
                <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                @endforeach
              </select>
            </div>
        </div>
        @endif
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->links() }}
          </div>
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr>
              <th style="width: 1%"><input type="checkbox" id="check_all" value="1"></th>
              <th style="width: 1%">#</th>
              <th class="text-left">Ngày</th>
              <th class="text-center">Tỉnh/Thành</th>
              <th class="text-left">Nội dung</th>
              <th class="text-center">UNC</th>
              <th class="text-center">Số lượng</th>
              <th class="text-right">Giá</th>
              <th class="text-right">Tổng tiền</th>
              <th width="1%" style="white-space: nowrap;" class="text-center">Người chi</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr class="cost" id="row-{{ $item->id }}">
                <td>
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                </td>
                <td><span class="order">{{ $i }}</span></td>
                <td class="text-left">
                    {{ date('d/m/Y', strtotime($item->date_use)) }}
                </td>
                <td class="text-center">

                </td>
                <td>

                  <p style="color:red; font-style: italic">{{ $item->notes }}</p>
                </td>
                <td class="text-center">
                  @if($item->image_url)
                  <span style="color: blue; cursor: pointer;" class="img-unc" data-src="{{ config('plantotravel.upload_url').$item->image_url }}">XEM ẢNH</span>
                  @endif
                </td>
                <td class="text-center">{{ $item->amount }}</td>
                <td class="text-right">{{ number_format($item->price) }}</td>
                <td class="text-right">
                  {{ number_format($item->total_money) }}
                </td>
                <td class="text-center" style="white-space: nowrap;">

                </td>
                <td style="white-space:nowrap">
                  <a href="{{ route( 'grandworld-schedule.copy', [ 'id' => $item->id ]) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-duplicate"></span></a>
                  <a href="{{ route( 'grandworld-schedule.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if($item->costType)
                  <a onclick="return callDelete('{{ $item->costType->name . " - ".number_format($item->total_money) }}','{{ route( 'grandworld-schedule.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @else
                  <a onclick="return callDelete('{{ number_format($item->total_money) }}','{{ route( 'grandworld-schedule.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif

                </td>
              </tr>
              @endforeach
            @else
            <tr>
              <td colspan="4">Không có dữ liệu.</td>
            </tr>
            @endif

          </tbody>
          </table>
          <div style="text-align:center">
            {{ $items->links() }}
          </div>
        </div>
        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="nguoi_chi">
                <option value="">--SET NGƯỜI CHI--</option>
                <option value="1">CTY</option>
                <option value="2">ĐIỀU HÀNH</option>
                <option value="3">CÔNG NỢ</option>
              </select>
          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="city_id">
                <option value="">--SET TỈNH/THÀNH--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
              </select>
          </div>
          <div class="form-group">
              <select class="form-control select2 multi-change-column-value" data-column="cate_id">
                <option value="">--SET LOẠI CHI PHÍ--</option>
                @foreach($cateList as $cate)
                <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                @endforeach
              </select>
            </div>
        </div>
        @endif
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
    $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              $.ajax({
                url : "{{ route('grandworld-schedule.change-value-by-column') }}",
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
    $('tr.cost').click(function(){
      $(this).find('.check_one').attr('checked', 'checked');
    });
    $("#check_all").click(function(){
        $('input.check_one').not(this).prop('checked', this.checked);
    });
    $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('grandworld-schedule.export') }}").submit().attr('action', oldAction);
      });
    // $('#partner_id').on('change', function(){
    //   $(this).parents('form').submit();
    // });
    $('#cate_id').change(function(){
        $.ajax({
          url : "{{ route('grandworld-schedule.ajax-doi-tac') }}",
          data: {
            cate_id : $(this).val()
          },
          type : "GET",
          success : function(data){
            if(data != 'null'){
              $('#load_doi_tac').html(data);
              if($('#partner_id').length==1){
                $('#partner_id').select2();
              }
            }
          }
        });
    });
  });
  $(document).ready(function(){
    $('.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).data('src'));
      $('#uncModal').modal('show');
    });
  });
</script>
@stop
