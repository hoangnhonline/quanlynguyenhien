@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                LỢI NHUẬN TOUR THÁNG {{ $month }}/{{ $year }}
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    @if(Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form class="form-inline" role="form" method="GET"
                                  action="{{ route('report.loi-nhuan-thang') }}" id="searchForm">
                                <div class="form-group  chon-thang">
                                    <label for="month">THÁNG</label>
                                    <select class="form-control select2" id="month_change" name="month">
                                        <option value="">--CHỌN--</option>
                                        @for($i = 1; $i <=12; $i++)
                                            <option
                                                value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group  chon-thang">
                                    <select class="form-control select2" id="year_change" name="year">
                                        <option value="">--NĂM--</option>
                                        <option value="2020" {{ $year == 2020 ? "selected" : "" }}>2020</option>
                                        <option value="2021" {{ $year == 2021 ? "selected" : "" }}>2021</option>
                                        <option value="2022" {{ $year == 2022 ? "selected" : "" }}>2022</option>
                                        <option value="2023" {{ $year == 2023 ? "selected" : "" }}>2023</option>
                                        <option value="2024" {{ $year == 2024 ? "selected" : "" }}>2024</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                            </form>
                        </div>
                    </div>
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <p style="color: red; font-weight: bold">LỢI NHUẬN THEO NGÀY {{ $month }}</p>
                                <table class="table table-bordered table-hover table-list-data">
                                    <tr style="background-color: #00acd6;color:#FFF">
                                        <th class="text-center">Ngày</th>
                                        <th class="text-center">Tour</th>
                                        <th class="text-right">Tổng Booking</th>
                                        <th class="text-right">Tổng NL</th>
                                        <th class="text-right">Tổng TE</th>
                                        <th class="text-right">Tổng Tiền</th>
                                        <th class="text-right">Hoa Hồng</th>
                                        <th class="text-right">Tổng Thực Thu</th>
                                        <th class="text-right">Chi Phí</th>
                                        <th class="text-right">Lợi Nhuận</th>
                                    </tr>
                                    @php
                                        $count_booking = 0;
                                        $sum_adults = 0;
                                        $sum_childs = 0;
                                        $sum_total_price = 0;
                                        $sum_hoa_hong_sales = 0;
                                        $sum_tien_thuc_thu = 0;
                                        $sum_tien_coc = 0;
                                        $sum_cost = 0;
                                        $sum_revenue = 0;
                                    @endphp
                                    @foreach($data as $date => $tours)
                                        @php
                                            $index = 0;
                                            $date_count_booking = 0;
                                            $date_sum_adults = 0;
                                            $date_sum_childs = 0;
                                            $date_sum_total_price = 0;
                                            $date_sum_hoa_hong_sales = 0;
                                            $date_sum_tien_thuc_thu = 0;
                                            $date_sum_tien_coc = 0;
                                            $date_sum_cost = 0;
                                            $date_sum_revenue = 0;
                                        @endphp
                                        @foreach($tours as $tourNo => $tour)
                                            @php
                                              $count_booking += $tour['count_booking'];
                                              $sum_adults += $tour['sum_adults'];
                                              $sum_childs += $tour['sum_childs'];
                                              $sum_total_price += $tour['sum_total_price'];
                                              $sum_hoa_hong_sales += $tour['sum_hoa_hong_sales'];
                                              $sum_tien_thuc_thu += $tour['sum_tien_thuc_thu'];
                                              $sum_tien_coc += $tour['sum_tien_coc'];
                                              $sum_cost += $tour['sum_cost'];
                                              $sum_revenue += $tour['sum_revenue'];

                                              $date_count_booking += $tour['count_booking'];
                                              $date_sum_adults += $tour['sum_adults'];
                                              $date_sum_childs += $tour['sum_childs'];
                                              $date_sum_total_price += $tour['sum_total_price'];
                                              $date_sum_hoa_hong_sales += $tour['sum_hoa_hong_sales'];
                                              $date_sum_tien_thuc_thu += $tour['sum_tien_thuc_thu'];
                                              $date_sum_tien_coc += $tour['sum_tien_coc'];
                                              $date_sum_cost += $tour['sum_cost'];
                                              $date_sum_revenue += $tour['sum_revenue'];
                                            @endphp
                                            <tr>
                                                @if($index === 0)
                                                    <td class="text-center align-content-center"
                                                        rowspan="{{count($tours) + 1}}">{{date('d', strtotime($date))}}</td>
                                                @endif
                                                <td class="text-center">
                                                    {{$tourNo}}
                                                </td>
                                                 <td class="text-right">{{$tour['count_booking']}}</td>
                                                <td class="text-right">{{$tour['sum_adults']}}</td>
                                                <td class="text-right">{{$tour['sum_childs']}}</td>
                                                <td class="text-right">{{number_format($tour['sum_total_price'])}}</td>
                                                <td class="text-right">{{number_format($tour['sum_hoa_hong_sales'])}}</td>
                                                <td class="text-right">{{number_format($tour['sum_tien_thuc_thu'] + $tour['sum_tien_coc'])}}</td>
                                                <td class="text-right">{{number_format($tour['sum_cost'])}}</td>
                                                <td class="text-right {{$tour['sum_revenue'] < 0 ? 'text-red' : ''}}">{{number_format($tour['sum_revenue'])}}</td>
                                            </tr>
                                            @php
                                                $index++;
                                            @endphp
                                        @endforeach
                                        <tr style="background-color: #ddeaee">
                                            <td class="text-center">
                                               Tổng
                                            </td>
                                            <td class="text-right">{{$date_count_booking}}</td>
                                            <td class="text-right">{{$date_sum_adults}}</td>
                                            <td class="text-right">{{$date_sum_childs}}</td>
                                            <td class="text-right">{{number_format($date_sum_total_price)}}</td>
                                            <td class="text-right">{{number_format($date_sum_hoa_hong_sales)}}</td>
                                            <td class="text-right">{{number_format($date_sum_tien_thuc_thu)}}</td>
                                            <td class="text-right">{{number_format($date_sum_cost)}}</td>
                                            <td class="text-right {{$date_sum_revenue < 0 ? 'text-red' : ''}}">{{number_format($date_sum_revenue)}}</td>
                                        </tr>
                                    @endforeach
                                    <tr style="background-color: #f7e4c3">
                                        <td class="text-left align-content-center" colspan="2">TỔNG</td>
                                        <td class="text-right">{{$count_booking}}</td>
                                        <td class="text-right">{{$sum_adults}}</td>
                                        <td class="text-right">{{$sum_childs}}</td>
                                        <td class="text-right">{{number_format($sum_total_price)}}</td>
                                        <td class="text-right">{{number_format($sum_hoa_hong_sales)}}</td>
                                        <td class="text-right">{{number_format($sum_tien_thuc_thu)}}</td>
                                        <td class="text-right">{{number_format($sum_cost)}}</td>
                                        <td class="text-right {{$sum_revenue < 0 ? 'text-red' : ''}}">{{number_format($sum_revenue)}}</td>
                                    </tr>
                                </table>
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
        table a {
            color: #000;
            font-weight: bold;
        }

        .table-list-data td, .table-list-data th {
            border: 1px solid #000 !important;
            font-weight: bold;
            color: #000
        }
    </style>
    <input type="hidden" id="table_name" value="articles">
@stop
