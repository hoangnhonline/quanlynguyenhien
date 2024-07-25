@extends('layout')
@section('content')
    <div class="content-wrapper" style="min-height: 926px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 style="text-transform: uppercase;">
                Báo cáo đặt tour
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">Báo cáo đặt tour</li>
            </ol>
        </section>

        <div class="panel panel-default" style="margin: 30px 0">
            <div class="panel-body">
                <form class="form-inline" role="form" method="GET" action="{{ route('daily-report.index') }}" id="searchForm">
                    <div class="form-group">
                        <label for="time_type1">
                            <input id="time_type1" type="radio" name="time_type" value="1"  {{ $time_type == 1 ? "checked" : "" }}> Theo tháng
                        </label>
                        <label for="time_type2">
                            <input id="time_type2" type="radio" name="time_type" value="2"  {{ $time_type == 2 ? "checked" : "" }}> Khoảng ngày
                        </label>
                        <label for="time_type3">
                            <input id="time_type3" type="radio" name="time_type" value="3"  {{ $time_type == 3 ? "checked" : "" }}> Theo ngày
                        </label>
                    </div>
                    @if($time_type == 1)
                        <div class="form-group  chon-thang">
                            <select class="form-control select2" id="month_change" name="month">
                                <option value="">--THÁNG--</option>
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
                            <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['use_date_from'] }}" style="width: 120px">
                        </div>
                        @if($time_type == 2)
                            <div class="form-group chon-ngay den-ngay">
                                <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" style="width: 120px">
                            </div>
                        @endif
                    @endif

                    <div class="form-group ">
                        <select class="form-control select2" name="tour_id" id="tour_id">
                            <option value="">-- Chọn tour --</option>
                            @foreach($tours as $tour)
                                <option
                                    value="{{$tour->id}}" {{ @$arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{$tour->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <select class="form-control select2" id="tour_cate" name="tour_cate" >
                            <option value="">-- Chọn loại tour --</option>
                            <option value="1" {{ @$arrSearch['tour_cate'] == 1 ? "selected" : "" }}>4 đảo</option>
                            <option value="2" {{ @$arrSearch['tour_cate'] == 2 ? "selected" : "" }}>2 đảo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control select2" id="tour_type" name="tour_type">
                            <option value="">-- Chọn hình thức --</option>
                            <option value="1" {{ @$arrSearch['tour_type'] == 1 ? "selected" : "" }}>Tour ghép</option>
                            <option value="2" {{ @$arrSearch['tour_type'] == 2 ? "selected" : "" }}>Tour VIP</option>
                            <option value="3" {{ @$arrSearch['tour_type'] == 3 ? "selected" : "" }}>Thuê cano</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                </form>
            </div>
        </div>

        <!-- Main content -->
{{--        <div class="selected-date">--}}
{{--           {{$arrSearch['use_date_from']}}@if($time_type == 1 || $time_type == 2) - {{$arrSearch['use_date_to']}}@endif--}}
{{--        </div>--}}
        <section class="content">
            <div class="row">
                <div class="col-md-4">
                    <div class="chart-item-container">
                        <div class="chart">
                            <div id="chart_1"></div>
                        </div>
                        <div class="chart-des">
                            <h2>Loại nhuận đến hiện tại</h2>
                            <div class="label label-success">{{number_format($totalRevenue)}}vnđ</div>
                        </div>
                    </div>
                    <div class="chart-item-container">
                        <div class="chart">
                            <div id="chart_2"></div>
                        </div>
                        <div class="chart-des">
                            <h2>Tỷ suất lợi nhuận</h2>
                            <div class="label label-success">{{number_format($totalRevenue)}}vnđ</div>
                            <div class="label label-warning">{{number_format($totalIncoming)}}vnđ</div>
                        </div>
                    </div>
                    <div class="chart-item-container" style="margin-bottom: 0">
                        <div class="chart">
                            <canvas id="pie_chart_1"></canvas>
                        </div>
                        <div class="chart-des">
                            <h2>DT theo nguồn</h2>
                            @php
                                $colors = ['#6ce5e8', '#41b8d5', '#2d8bba', '#2f5f98', '#31356e', '#5e3967', '#411b4a', '#2d0f2e', '#1a0a1e', '#0d050f'];
                                $index = 0;
                            @endphp
                            @foreach($byLevelChartData as $key=>$value)
                                @php
                                    $color = @$colors[$index];
                                @endphp
                                <div class="label label-success" style="background-color: {{ $color }} !important;">{{ number_format($value) }}vnđ</div>
                                @php
                                    $index++;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="chart-item-container line-chart-container">
                        <div class="chart-des">
                            <h2>Doanh thu chi tiết</h2>
                            <div class="line-chart-legend">
                                <div class="legend-item">
                                    <div class="legend-value">
                                        {{number_format($totalIncoming / 1000000)}} M
                                    </div>
                                    <div class="legend-label">
                                        Tổng Thu
                                    </div>
                                </div>
                                <div class="legend-item" style="color: #41b8d5">
                                    <div class="legend-value">
                                        {{number_format($totalCost / 1000000)}} M
                                    </div>
                                    <div class="legend-label">
                                        Tổng Chi
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-value">
                                        {{number_format(($totalIncoming - $totalCost) / 1000000)}} M
                                    </div>
                                    <div class="legend-label">
                                        Lợi Nhuận
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-value">
                                        {{number_format($totalBooking)}}
                                    </div>
                                    <div class="legend-label">
                                        Booking
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-value">
                                        {{number_format($totalAdults)}}
                                    </div>
                                    <div class="legend-label">
                                        Người lớn
                                    </div>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-value">
                                        {{number_format($totalChilds)}}
                                    </div>
                                    <div class="legend-label">
                                        Trẻ em
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chart">
                            <canvas id="line_chart"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-item-container pie-chart-2">
                                <div class="chart">
                                    <canvas id="pie_chart_2"></canvas>
                                </div>
                                <div class="chart-des">
                                    <h2>DT theo sản phẩm</h2>
                                    @php
                                        $colors =  ['#5e3967', '#31356e', '#2f5f98', '#2d8bba', '#41b8d5', '#6ce5e8'];
                                        $index = 0;
                                    @endphp
                                    @foreach($byTourTypeChartData as $key=>$value)
                                        @php
                                            $color = $colors[$index];
                                        @endphp
                                        <div class="label label-success" style="background-color: {{ $color }} !important;">{{ number_format($value) }}vnđ</div>
                                        @php
                                            $index++;
                                        @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-item-container pie-chart-2">
                                <div class="chart">
                                    <canvas id="pie_chart_3"></canvas>
                                </div>
                                <div class="chart-des">
                                    <h2>SL khách theo SP</h2>
                                    @php
                                        $colors =  ['#5e3967', '#31356e', '#2f5f98', '#2d8bba', '#41b8d5', '#6ce5e8','#411b4a'];
                                        $index = 0;
                                    @endphp
                                    @foreach($byGuestsChartData as $key=>$value)
                                        @php
                                            $color = $colors[$index];
                                        @endphp
                                        <div class="label label-success" style="background-color: {{ $color }} !important;">{{ number_format($value) }} khách</div>
                                        @php
                                            $index++;
                                        @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <style type="text/css">
        body .content-wrapper{
            padding: 20px;
        }

        .content{
            background-color: #e1e7ff;
            padding: 20px 30px;
            border-radius: 20px;
        }
        #tblReportCate tr td{
            text-align: center;
        }
        .chart-item-container{
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            background-color: #cad9ff;
            padding: 15px;
            margin-bottom: 10px;
            color: #004e35;
        }

        .chart-item-container .label{
            display: block;
            border-radius: 20px;
            padding: 10px 15px;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .chart-item-container .label{
            background-color: #a7c2ff !important;
            color: #004e35;
        }

        .chart-item-container .label-success{
            background-color: #004e35 !important;
            color: #fff !important;
        }

        .chart{
            min-width: 50%;
            width: 50%;
        }

        .circle-progress {
            height: 180px !important;
        }

        .circle-progress-circle{
            stroke: #a7c2ff;
        }

        .circle-progress-value{
            stroke: #004e35;
        }

        .circle-progress-text{
            fill: #004e35;
            font-weight: bold;
        }

        .chart-des{
            padding-left: 15px;
            width: 100%;
            text-align: center;
        }

        .chart-des h2{
            font-weight: bold;
            font-size: 16px;
            margin-top: 0;
        }

        .line-chart-container{
            flex-direction: column;
        }

        .line-chart-container canvas{
            width: 100%;
            height: 100%;
        }

        .line-chart-container .chart{
            width: 100%;
            height: 330px;
        }

        .line-chart-legend{
            display: flex;
            flex-direction: row;
            margin-bottom: 15px;
            color: #004e35;
        }

        .legend-item{
            margin-right: 10px;
            flex: 1
        }

        .legend-value{
            font-weight: bold;
        }

        .pie-chart-2 .label{
            font-size: 12px;
            padding: 5px 15px
        }

        .row{
            margin-left: -5px;
            margin-right: -5px;
        }

        .col-md-4, .col-md-8, .col-md-6{
            padding-left: 5px;
            padding-right: 5px;
        }
        .selected-date{
            color: #004e35;
            background-color: #a7c2ff;
            padding: 10px 40px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            margin-left: 30px;
        }
    </style>
@stop
@section('js')
    <script type="text/javascript" src="{{asset('admin/dist/js/circle-progress/jquery.circle-progress.min.js')}}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script type="text/javascript" src="https://chartjs-plugin-piecharts-outlabels.netlify.app/dist/chartjs-plugin-piechart-outlabels.js"></script>
    <script>
        $(document).ready(function (){
            $("input[name='time_type']").change(function(){
                $('#searchForm').submit();
            });

            $('#chart_1').circleProgress({
                value: {{$totalRevenue * 100 / (!empty($target) ? $target->$target :500000000)}},
                max: 100,
                textFormat: 'percent'
            });

            $('#chart_2').circleProgress({
                value: {{$totalIncoming ? $totalRevenue * 100 / $totalIncoming : 0}},
                max: 100,
                textFormat: 'percent'
            });

            $('#chart_3').circleProgress({
                value: 64,
                max: 100,
                textFormat: 'percent'
            });

            //Center line chart
            const lineChartEl = document.getElementById('line_chart');
            const stackedLine = new Chart(lineChartEl, {
                type: 'line',
                data:  {
                    labels: @json($detailChartLabels),
                    datasets: [
                        {
                            label: 'Thu',
                            data: @json($detailIncommingChart),
                            fill: false,
                            borderColor: '#004e35',
                            cubicInterpolationMode: 'monotone',
                            tension: 0.4
                        },

                        {
                            label: 'Chi',
                            data: @json($detailCostChart),
                            fill: false,
                            borderColor: '#41b8d5',
                            cubicInterpolationMode: 'monotone',
                            tension: 0.4
                        }
                    ],
                    maintainAspectRatio: false,

                },
                options:{
                    plugins: {
                        legend: {
                            display: false
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            //Pie chart
            var params = {
                display: true,
                lineWidth: 1,
                padding: 6,
                textAlign: 'right',
                color: '#565656',
                backgroundColor: 'red',
                valuePrecision: 0,
                percentPrecision: 2,
                text: '%l',
            }
            //Pie chart
            const pieChart1El = document.getElementById('pie_chart_1');
            new Chart(pieChart1El, {
                type: 'pie',
                data:  {
                    labels: @json(array_keys($byLevelChartData)),
                    datasets: [
                        {
                            data: @json(array_values($byLevelChartData)),
                            backgroundColor: ['#6ce5e8', '#41b8d5', '#2d8bba', '#5e3967', '#31356e', '#2f5f98'],
                            borderColor: 'rgba(0,0,0,0)'
                        }
                    ],
                    maintainAspectRatio: false,

                },
                options:{
                    plugins: {
                        outlabels:params,
                        legend: {
                            display: false
                        }
                    },
                    elements: {
                        line: {
                            fill: false
                        },
                        point: {
                            hoverRadius: 7,
                            radius: 5
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                },
                plugins: [
                    ChartPieChartOutlabels
                ],
            });

            //Pie chart 2
            const pieChart2El = document.getElementById('pie_chart_2');
            new Chart(pieChart2El, {
                type: 'pie',
                data:  {
                    labels: @json(array_keys($byTourTypeChartData)),
                    datasets: [
                        {
                            data: @json(array_values($byTourTypeChartData)),
                            backgroundColor: ['#5e3967', '#31356e', '#2f5f98', '#2d8bba', '#41b8d5', '#6ce5e8'],
                            borderColor: 'rgba(0,0,0,0)'
                        }
                    ],
                    maintainAspectRatio: false,

                },
                options:{
                    plugins: {
                        outlabels:params,
                        legend: {
                            display: false
                        }
                    },
                    elements: {
                        line: {
                            fill: false
                        },
                        point: {
                            hoverRadius: 7,
                            radius: 5
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                },
            });



            //Pie chart 3
            const pieChart3El = document.getElementById('pie_chart_3');
            new Chart(pieChart3El, {
                type: 'pie',
                data:  {
                    labels: @json(array_keys($byGuestsChartData)),
                    datasets: [
                        {
                            data: @json(array_values($byGuestsChartData)),
                            backgroundColor: ['#5e3967', '#31356e', '#2f5f98', '#2d8bba', '#41b8d5', '#6ce5e8'],
                            borderColor: 'rgba(0,0,0,0)'
                        }
                    ],
                    maintainAspectRatio: false,

                },
                options:{
                    plugins: {
                        outlabels:params,
                        legend: {
                            display: false
                        }
                    },
                    elements: {
                        line: {
                            fill: false
                        },
                        point: {
                            hoverRadius: 7,
                            radius: 5
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                },
            });
        })
    </script>
@stop
