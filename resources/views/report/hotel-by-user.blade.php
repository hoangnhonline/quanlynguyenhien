@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                THỐNG KÊ KHÁCH SẠN THEO USER {{ $month }}/{{ $year }}
            </h1>
            <!--  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'food.index' ) }}">Món ăn</a></li>
    <li class="active">Danh sách</li>
  </ol> -->
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
                            <form class="form-inline" role="form" method="GET" action="{{ route('report.hotel-by-user') }}" id="searchForm">
                                <div class="form-group">
                                    <select class="form-control select2" name="level" id="level">
                                      <option value="" >--Phân loại sales--</option>
                                      <option value="1" {{ $level == 1 ? "selected" : "" }}>CTV Group</option>
                                      <option value="2" {{ $level == 2 ? "selected" : "" }}>ĐỐI TÁC</option>
                                      <option value="6" {{ $level == 6 ? "selected" : "" }}>NV SALES</option>
                                      <option value="7" {{ $level == 7 ? "selected" : "" }}>GỬI BẾN</option>
                                    </select>
                                  </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="user_id" id="user_id">
                                        <option value="" {{ $user_id == -1 ? "selected" : "" }}>--User--</option>
                                        @foreach($listUser as $user)
                                            <option value="{{ $user->id }}" {{ $user_id == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select id="search_by" name="search_by" class="form-control select2">
                                      <option value="">--Tìm theo--</option>
                                      <option value="checkin" {{ $arrSearch['search_by'] == 'checkin' ? "selected" : "" }}>Ngày checkin</option>
                                      <option value="book_date" {{ $arrSearch['search_by'] == 'book_date' ? "selected" : "" }}>Ngày đặt</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
                                </div>

                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                                <button type="button" class="btn btn-info btn-sm" id="export" style="margin-top: -5px">Export</button>
                            </form>
                        </div>
                    </div>
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            @if($user_id == -1)
                                <div class="table-responsive">
                                <p style="color: red; font-weight: bold">TỔNG DOANH THU THEO USER</p>
                                <table class="table table-bordered table-condensed table-striped table-hover" id="{{$user_id == -1 ? 'export-table' : ''}}">
                                    <tr>
                                        <th width="1%" class="text-center">STT</th>
                                        <th>User</th>
                                        @foreach($dates as $key => $date)
                                            <th class="text-center" style="background-color: #4e88e3; color: #fff; vertical-align: middle">{{ date('d/m', strtotime($key)) }}</th>
                                        @endforeach
                                        <th class="text-center" style="background-color: #4e88e3; color: #fff">Tổng lợi nhuận</th>
                                    </tr>
                                    @php $i = 0;
                                    @endphp
                                    @foreach($summary as $userId => $item)
                                        @php $i++; @endphp
                                        <tr>
                                            <td class="text-center" style="background-color: #fef2d0">{{ $i }}</td>
                                            <td style="white-space: nowrap; background-color: #fef2d0"><a href="{{request()->fullUrlWithQuery(['user_id' => $userId])}}">{{ !empty($item['name']) ? $item['name'] : 'N/A' }}</a></td>
                                            @foreach($dates as $key=>$total)
                                                <td class="text-center">
                                                    {{ !empty($item['detail'][$key]) ?number_format( $item['detail'][$key]) : '-' }}
                                                </td>
                                            @endforeach
                                            <td class="text-center">{{ number_format( $item['total']) }}</td>
                                        </tr>
                                    @endforeach
                                    @if($user_id == -1)
                                        <tr>
                                            <td class="text-left" style="background-color: #fef2d0;font-weight: bold" colspan="2">
                                                Tổng
                                            </td>
                                            @php
                                                $totalRevenue = 0;
                                            @endphp
                                            @foreach($dates as $key=>$total)
                                                @php
                                                    $totalRevenue += $total;
                                                @endphp
                                                <td class="text-center" style="font-weight: bold">
                                                    {{ !empty($total) ?number_format( $total) : '-' }}
                                                </td>
                                            @endforeach
                                            <td class="text-center" style="font-weight: bold">{{ number_format($totalRevenue) }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            @endif
                            @if(!empty($details))
                                <div class="table-responsive" style="margin-top: 20px">
                                    <p style="color: red; font-weight: bold; text-transform: uppercase">THỐNG KÊ BOOKING KS USER {{\App\User::find($user_id)->name}}</p>
                                    <table class="table table-bordered table-hover" id="{{$user_id >= 0 ? 'export-table' : ''}}">
                                        <tr>
                                            <th width="1%" class="text-center"></th>
                                            <th></th>
                                            @foreach($detailDates as $date)
                                                <th class="text-center" style="background-color: #4e88e3; color: #fff; vertical-align: middle">{{ date('d/m', strtotime($date)) }}</th>
                                            @endforeach
                                            <th class="text-center" style="background-color: #4e88e3; color: #fff">Tổng</th>
                                        </tr>
                                        @php $i = 0;
                                        @endphp
                                        @foreach($details as $stars => $item)
                                            @php $i++; @endphp
                                            <tr>
                                                <td rowspan="5" style="vertical-align: middle; white-space: nowrap; background-color: #fef2d0">{{ $stars }} sao</td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #fef2d0; white-space: nowrap">Số phòng</td>
                                                @foreach($detailDates as $date)
                                                    <td class="text-center">
                                                        {{ !empty($item['detail'][$date]['room_count']) ?number_format($item['detail'][$date]['room_count']) : '-' }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center">{{ number_format($item['room_count']) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #fef2d0; white-space: nowrap">Số đêm</td>
                                                @foreach($detailDates as $date)
                                                    <td class="text-center">
                                                        {{ !empty($item['detail'][$date]['nights']) ?number_format($item['detail'][$date]['nights']) : '-' }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center">{{ number_format($item['nights']) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #fef2d0; white-space: nowrap">Doanh thu</td>
                                                @foreach($detailDates as $date)
                                                    <td class="text-center">
                                                        {{ !empty($item['detail'][$date]['total']) ?number_format($item['detail'][$date]['total']) : '-' }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center">{{ number_format($item['total']) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #fef2d0; white-space: nowrap">Lợi nhuận</td>
                                                @foreach($detailDates as $date)
                                                    <td class="text-center">
                                                        {{ !empty($item['detail'][$date]['hoa_hong']) ?number_format($item['detail'][$date]['hoa_hong']) : '-' }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center">{{ number_format($item['hoa_hong']) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                        <?php //echo $str;?>
                                </div>
                            @endif


                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
        </section>
        <!-- /.content -->
    </div>
    <style type="text/css">
        .table-list-data td,.table-list-data th{
            border: 1px solid #000 !important;
            font-weight: bold;
            color: #000
        }
        tr.vip{
            background-color: #02fa7a
        }
        tr.thue-cano{
            background-color: #ebd405
        }
    </style>
    <input type="hidden" id="table_name" value="articles">
@stop
@section('js')
    <script type="text/javascript">
        $('#export').click(function (e){
            e.preventDefault();
            var tableToExcel = (function() {
                var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                return function(table, name) {
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    window.location.href = uri + base64(format(template, ctx))
                }
            })()('export-table', 'datatable')
        })
    </script>
@stop
