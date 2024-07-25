@extends('layout')
@section('content')
    @php
        $sources = config('plantotravel.customer_sources');
    @endphp
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                BÁO CÁO KHÁCH HÀNG
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route( 'dashboard' ) }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">Báo cáo khách hàng</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div id="content_alert"></div>
                    @if(Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Bộ lọc</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-inline" role="form" method="GET" action="{{ route('report.weekly') }}"
                                  id="searchForm">
                                <div class="form-group">
                                    <select class="form-control select2" name="department_id" id="department_id">
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ $department->id == @$arraySearch['department_id'] ? "selected" : "" }}>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                                <button type="button" class="btn btn-info btn-sm" id="export" style="margin-top: -5px">Export</button>
                            </form>
                        </div>
                    </div>
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <p style="margin-top: 20px; color: red; font-size: 20px">1. Các công việc thường xuyên & định kỳ</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed">
                                    <tr style="background-color: #d9ead3; color: #000;">
                                        <th>STT</th>
                                        <th>MẢNG CHÍNH</th>
                                        <th>NỘI DUNG CÔNG VIỆC</th>
                                        <th>THỜI GIAN THỰC HIỆN</th>
                                        <th>YÊU CẦU CÔNG VIỆC</th>
                                        <th>MỨC ĐỘ HOÀN THÀNH</th>
                                    </tr>
                                    <tbody>
                                    @foreach($tasks as $index => $task)
                                        <tr>
                                            <td>{{$index + 1}}</td>
                                            <td>
                                                {{$task->task->name}}
                                            </td>
                                            <td>
                                                {{$task->content}}
                                            </td>
                                            <td>{{date('d/m/Y', strtotime($task->task_date))}}</td>
                                            <td>
                                                {{$task->notes}}
                                            </td>
                                            <td> {!! $task->percent !!}%</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p style="margin-top: 20px; color: red; font-size: 20px">2. Các công việc đang được chỉ đạo/giao thực hiện</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed">
                                    <tr style="background-color: #d9ead3; color: #000;">
                                        <th>STT</th>
                                        <th>BP/NGƯỜI GIAO VIỆC</th>
                                        <th>CÔNG VIỆC ĐƯỢC GIAO</th>
                                        <th>YÊU CẦU CÔNG VIỆC</th>
                                        <th>DEADLINE CÔNG VIỆC</th>
                                        <th>MỨC ĐỘ HOÀN THÀNH</th>
                                    </tr>
                                    <tbody>
                                        @foreach($createdTasks as $index => $task)
                                            <tr>
                                                <td>{{$index + 1}}</td>
                                                <td>
                                                    {{$task->task->createdUser->name}}
                                                </td>
                                                <td>
                                                    {{$task->content}}
                                                </td>
                                                <td>{{date('d/m/Y', strtotime($task->task_date))}}</td>
                                                <td>{{date('d/m/Y', strtotime($task->task_deadline))}}</td>
                                                <td> {!! $task->percent !!}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p style="margin-top: 20px; color: red; font-size: 20px">3. Các công việc tồn đọng & gặp khó khăn/có vấn đề cần báo cáo</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed">
                                    <tr style="background-color: #d9ead3; color: #000;">
                                        <th>STT</th>
                                        <th>CÔNG VIỆC TỔN ĐỌNG
                                            GẶP KHÓ KHĂN</th>
                                        <th>DIỄN GIẢI/MÔ TẢ</th>
                                        <th>DEADLINE
                                            CÔNG VIỆC</th>
                                        <th>NGƯỜI/BP
                                            NHẬN ĐỀ XUẤT</th>
                                        <th>KỲ VỌNG
                                            XỬ LÝ</th>
                                    </tr>
                                    <tbody>
                                        @foreach($overdueTasks as $index => $task)
                                            <tr>
                                                <td>{{$index + 1}}</td>
                                                <td>
                                                    {{$task->task->name}}
                                                </td>
                                                <td>
                                                    {{$task->content}}
                                                </td>
                                                <td>{{date('d/m/Y', strtotime($task->task_deadline))}}</td>
                                                <td> {{$task->staff->name}} </td>
                                                <td>{!! $task->content_result !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p style="margin-top: 20px; color: red; font-size: 20px">4. Báo cáo Doanh thu/Doanh số/Lợi nhuận tổng trong tuần</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed">
                                    <tr>
                                        <th colspan="6"></th>
                                        @foreach(array_keys($sources) as $key)
                                            <th colspan="3" style="background-color: #cfe2f3; color: orange;text-align: center">{{$key}}</th>
                                        @endforeach
                                    </tr>
                                    <tr style="background-color: #d9ead3; color: #000;">
                                        <th>STT</th>
                                        <th>MẢNG KINH DOANH</th>
                                        <th>SẢN PHẨM DỊCH VỤ</th>
                                        <th>DOANH SỐ</th>
                                        <th>DOANH THU</th>
                                        <th>LỢI NHUẬN</th>
                                        @foreach(array_keys($sources) as $key)
                                            <th>DOANH SỐ</th>
                                            <th>DOANH THU</th>
                                            <th>LỢI NHUẬN</th>
                                        @endforeach
                                    </tr>
                                    @php
                                        $stt = 1;
                                    @endphp
                                    <tbody>
                                        @foreach($tourSummary as $row)
                                            <tr>
                                                <td>{{$stt}}</td>
                                                <td>Dailytour</td>
                                                <td style="white-space: nowrap">{{$row['tour_name']}}</td>
                                                <td>{{number_format($row['total_price'])}}</td>
                                                <td></td>
                                                <td></td>
                                                @foreach(array_keys($sources) as $key)
                                                    <td>{{!empty($tourBySource[$row['tour_name']][$key]) ? number_format($tourBySource[$row['tour_name']][$key]) : ''}}</td>
                                                    <td></td>
                                                    <td></td>
                                                @endforeach
                                            </tr>
                                            @php
                                                $stt++;
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <td>{{$stt}}</td>
                                            <td>Booking phòng</td>
                                            <td style="white-space: nowrap">Booking phòng dịch vụ lẻ</td>
                                            <td>{{number_format($hotelSummary)}}</td>
                                            <td></td>
                                            <td></td>
                                            @foreach(array_keys($sources) as $key)
                                                <td>{{!empty($hotelBySource[$key]) ? number_format( $hotelBySource[$key]) : ''}}</td>
                                                <td></td>
                                                <td></td>
                                            @endforeach
                                        </tr>
                                        @php
                                            $stt++;
                                        @endphp

                                        @foreach($ticketSummary as $row)
                                            <tr>
                                                <td>{{$stt}}</td>
                                                <td style="white-space: nowrap">Vé VCGT - dịch vụ</td>
                                                <td style="white-space: nowrap">{{$row['ticket_name']}}</td>
                                                <td>{{number_format($row['total_price'])}}</td>
                                                <td></td>
                                                <td></td>
                                                @foreach(array_keys($sources) as $key)
                                                    <td>{{!empty($ticketBySource[$row['ticket_name']][$key]) ? number_format( $ticketBySource[$row['ticket_name']][$key]) : ''}}</td>
                                                    <td></td>
                                                    <td></td>
                                                @endforeach
                                            </tr>
                                            @php
                                                $stt++;
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <td>{{$stt}}</td>
                                            <td>Booking xe</td>
                                            <td style="white-space: nowrap">Xe có thu phí</td>
                                            <td>{{number_format($carSummary)}}</td>
                                            <td></td>
                                            <td></td>
                                            @foreach(array_keys($sources) as $key)
                                                <td>{{!empty($carBySource[$key]) ? number_format($carBySource[$key]) : ''}}</td>
                                                <td></td>
                                                <td></td>
                                            @endforeach
                                        </tr>
                                        @php
                                            $stt++;
                                        @endphp
                                    </tbody>
                                </table>
                            </div>
                            <p style="margin-top: 20px; color: red; font-size: 20px">5. Báo cáo Công nợ/Khoản chưa thu trong tuần</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed">
                                    <tr style="background-color: #d9ead3; color: #000;">
                                        <th>STT</th>
                                        <th>ĐỐI TÁC/KHÁCH HÀNG</th>
                                        <th>DIỄN GIẢI NGUYÊN NHÂN</th>
                                        <th>CÔNG NỢ</th>
                                        <th>ĐỀ XUẤT XỬ LÝ
                                            (BP/NGƯỜI)</th>
                                        <th>DEADLINE</th>
                                    </tr>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <!-- /.box -->
            </div>2
            <!-- /.col -->
    </section>
    <!-- /.content -->
    </div>
@stop
@section('js')
    <script type="text/javascript">
        $('#export').click(function (e) {
            e.preventDefault();
            var tableToExcel = (function () {
                var uri = 'data:application/vnd.ms-excel;base64,'
                    ,
                    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
                    , base64 = function (s) {
                        return window.btoa(unescape(encodeURIComponent(s)))
                    }
                    , format = function (s, c) {
                        return s.replace(/{(\w+)}/g, function (m, p) {
                            return c[p];
                        })
                    }
                return function (table, name) {
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    window.location.href = uri + base64(format(template, ctx))
                }
            })()('export-table', 'datatable')
        })
    </script>
@stop
