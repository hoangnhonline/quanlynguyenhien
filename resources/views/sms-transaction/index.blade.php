@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    SMS Giao dịch
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'sms-transaction.index' ) }}">SMS Giao dịch</a></li>
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

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('sms-transaction.index') }}" id="searchForm">
            <div class="form-group">

              <select class="form-control select2 search-form-change" name="is_valid" id="is_valid">
                <option value="-1" {{ $arrSearch['is_valid'] == -1 ? "selected" : "" }}>--Tất cả--</option>
                <option value="1" {{ $arrSearch['is_valid'] == 1 ? "selected" : "" }}>Đã hợp lệ</option>
                <option value="0" {{ $arrSearch['is_valid'] == 0 ? "selected" : "" }}>Chưa hợp lệ</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2 search-form-change" name="is_process" id="is_process">
                <option value="-1" {{ $arrSearch['is_process'] == -1 ? "selected" : "" }}>--Xử lí AUTO--</option>
                <option value="0" {{ $arrSearch['is_process'] == 0 ? "selected" : "" }}>Chưa auto</option>
                <option value="1" {{ $arrSearch['is_process'] == 1 ? "selected" : "" }}>Đã auto</option>
                <option value="2" {{ $arrSearch['is_process'] == 2 ? "selected" : "" }}>Mới auto</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2 search-form-change" name="ke_toan" id="ke_toan">
                <option value="">--Kế toán--</option>
                <option value="1" {{ $arrSearch['ke_toan'] == 1 ? "selected" : "" }}>Thương Trần</option>
                <option value="2" {{ $arrSearch['ke_toan'] == 2 ? "selected" : "" }}>Như Ngọc</option>
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="tai_khoan_doi_tac" value="{{ $arrSearch['tai_khoan_doi_tac'] }}" placeholder="Số tài khoản">
          </div>
          <div class="form-group">
              <input type="text" class="form-control" name="ten_doi_tac" value="{{ $arrSearch['ten_doi_tac'] }}" placeholder="Tên tài khoản">
          </div>
            <div class="form-group">

              <select class="form-control select2 search-form-change" name="time_type" id="time_type">
                <option value="1" {{ $time_type == 1 ? "selected" : "" }}>Theo tháng</option>
                <option value="2" {{ $time_type == 2 ? "selected" : "" }}>Khoảng ngày</option>
                <option value="3" {{ $time_type == 3 ? "selected" : "" }}>Ngày cụ thể </option>
              </select>
            </div>
            @if($time_type == 1)
            <div class="form-group  chon-thang search-form-change">

                <select class="form-control select2" id="month_change" name="month">
                  <option value="">--THÁNG--</option>
                  @for($i = 1; $i <=12; $i++)
                  <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                  @endfor
                </select>
              </div>
              <div class="form-group  chon-thang">
                <select class="form-control select2 search-form-change" id="year_change" name="year">
                  <option value="">--Năm--</option>
                  <option value="2022" {{ $year == 2022 ? "selected" : "" }}>2022</option>
                  <option value="2023" {{ $year == 2023 ? "selected" : "" }}>2023</option>
                  <option value="2024" {{ $year == 2024 ? "selected" : "" }}>2024</option>
                </select>
              </div>
            @endif
            @if($time_type == 2 || $time_type == 3)

            <div class="form-group chon-ngay">

              <input type="text" class="form-control datepicker search-form-change" autocomplete="off" name="ngay_giao_dich_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif" value="{{ $arrSearch['ngay_giao_dich_from'] }}" style="width: 100px">
            </div>

            @if($time_type == 2)
            <div class="form-group chon-ngay den-ngay">

              <input type="text" class="form-control datepicker search-form-change" autocomplete="off" name="ngay_giao_dich_to" placeholder="Đến ngày" value="{{ $arrSearch['ngay_giao_dich_to'] }}" style="width: 100px">
            </div>
             @endif
            @endif
            <div class="form-group">
              <select class="form-control select2 search-form-change" name="city_id" id="city_id">
                <option value="">--Chi nhánh--</option>
                <option value="1" {{ $arrSearch['city_id'] == 1 ? "selected" : "" }}>Phú Quốc</option>
                <option value="2" {{ $arrSearch['city_id'] == 2 ? "selected" : "" }}>HCM</option>
                <option value="3" {{ $arrSearch['city_id'] == 3 ? "selected" : "" }}>Đà Nẵng</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2 search-form-change" name="cate_id" id="cate_id">
                <option value="-1">--Phân loại--</option>
                <option value="0" {{ $arrSearch['cate_id'] == 0 ? "selected" : "" }}>Chưa phân loại</option>
                @foreach($arrPhanLoai as $item_id => $item_name)
                <option value="{{ $item_id }}" {{ $arrSearch['cate_id'] == $item_id ? "selected" : "" }}>{{ $item_name }}</option>
                @endforeach
              </select>
            </div>


            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>
      <p style="text-align: right;"><a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ({{ count($items) }})</h3>
        </div>
        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="is_valid">
              <option value="-1">--SET HỢP LỆ--</option>
                <option value="1">Đã hợp lệ</option>
                <option value="0">Chưa hợp lệ</option>
              </select>
          </div>
          <div class="form-group">
              <select class="form-control select2 multi-change-column-value" data-column="is_process" >
                <option value="-1">--SET XỬ LÍ--</option>
                <option value="0">Chưa xử lí</option>
                <option value="1">Đã xử lí</option>
                <option value="2">Mới xử lí</option>
              </select>
            </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="city_id">
                <option value="">--SET CHI NHÁNH--</option>
                <option value="1">Phú Quốc</option>
                <option value="2">HCM</option>
                <option value="3">Đà Nẵng</option>
            </select>

          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="cate_id">
                <option value="">--SET PHÂN LOẠI--</option>
                @foreach($arrPhanLoai as $item_id => $item_name)
                <option value="{{ $item_id }}">{{ $item_name }}</option>
                @endforeach
              </select>
          </div>


        </div>
        @endif
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr>
              <th style="width: 1%"><input type="checkbox" id="check_all" value="1"></th>
              <th style="width: 1%">#</th>
              <th class="text-center">Ngày</th>
              <th class="text-left" width="150">Số lệnh</th>
              <th class="text-right">Phát sinh nợ</th>
              <th class="text-right">Phát sinh có</th>
              <th class="text-center">Phân loại</th>
              <th class="text-right" width="150">Tài khoản</th>
              <th class="text-center">Ngân hàng</th>
              <th class="text-left">Tên</th>
              <th class="text-left">Nội dung</th>
              <th></th>
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
                <td>{{ date('d/m', strtotime($item->ngay_giao_dich)) }}</td>
                <td>
                  {{ $item->transaction_no }}<br>
                  @if($item->is_valid == 1)
                  <label class="label label-success">Hợp lệ</label>
                  @else
                  <label class="label label-danger">Chưa hợp lệ</label>
                  @endif
                </td>
                <td class="text-right">{{ $item->type == 2 ? "-".number_format($item->so_tien) : "" }}</td>
                <td class="text-right">{{ $item->type == 1 ? "+".number_format($item->so_tien) : "" }}</td>
                <td class="text-center">
                  @if($item->cate_id)
                  {{ $arrPhanLoai[$item->cate_id] }}
                  @else
                  <label class="label label-default">Chưa phân loại</label>
                  @endif
                </td>
                <td class="text-right">{{ $item->tai_khoan_doi_tac }}
                  @if($item->ke_toan == 1)
                  <label class="label label-info">Thương Trần</label>
                  @elseif($item->ke_toan == 2)
                  <label class="label label-warning">Như Ngọc</label>
                  @endif
                </td>
                <td class="text-center">{{ $item->ngan_hang_doi_tac }}</td>
                <td>{{ $item->ten_doi_tac }}</td>

                <td>{{ $item->noi_dung }}</td>
                <td>
                   <a href="{{ route( 'sms-transaction.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
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
        </div>
        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="is_valid">
              <option value="-1">--SET HỢP LỆ--</option>
                <option value="1">Đã hợp lệ</option>
                <option value="0">Chưa hợp lệ</option>
              </select>
          </div>
          <div class="form-group">
              <select class="form-control select2 multi-change-column-value" data-column="is_process" >
                <option value="-1">--SET XỬ LÍ--</option>
                <option value="0">Chưa xử lí</option>
                <option value="1">Đã xử lí</option>
                <option value="2">Mới xử lí</option>
              </select>
            </div>
           <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="city_id">
                <option value="">--SET CHI NHÁNH--</option>
                <option value="1">Phú Quốc</option>
                <option value="2">HCM</option>
                <option value="3">Đà Nẵng</option>
            </select>

          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="cate_id">
                <option value="">--SET PHÂN LOẠI--</option>
                @foreach($arrPhanLoai as $item_id => $item_name)
                <option value="{{ $item_id }}">{{ $item_name }}</option>
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

@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              $.ajax({
                url : "{{ route('sms-transaction.change-value-by-column') }}",
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
  });
</script>
@stop
