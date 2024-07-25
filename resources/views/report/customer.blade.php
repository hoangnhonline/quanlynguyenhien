@extends('layout')
@section('content')
    <style>
        .source-2{
            position: relative;
            padding-left: 40px !important;
        }
        .source-2:after{
            content: '';
            width: 1px;
            height: 100%;
            position: absolute;
            top: 0;
            left: 10px;
            border-left: 1px dashed #ccc;
        }
        .source-2:before{
            content: '';
            width: 20px;
            height: 1px;
            position: absolute;
            border-top: 1px dashed #ccc;
            top: 17px;
            left: 10px;
        }

        .source-2.last-child:after{
            height: 50%;
        }
    </style>
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
                            <form class="form-inline" role="form" method="GET" action="{{ route('report.customer') }}"
                                  id="searchForm">
                                <div class="form-group">
                                    <select class="form-control select2" name="time_type" id="time_type">
                                        <option value="">--Thời gian--</option>
                                        <option value="4" {{ $time_type == 6 ? "selected" : "" }}>Tuần trước</option>
                                        <option value="4" {{ $time_type == 4 ? "selected" : "" }}>Tuần này</option>
                                        <option value="5" {{ $time_type == 5 ? "selected" : "" }}>Tháng này</option>
                                        <option value="1" {{ $time_type == 1 ? "selected" : "" }}>Theo tháng</option>
                                        <option value="2" {{ $time_type == 2 ? "selected" : "" }}>Khoảng ngày</option>
                                        <option value="3" {{ $time_type == 3 ? "selected" : "" }}>Ngày cụ thể</option>
                                    </select>
                                </div>
                                @if($time_type == 1)
                                    <div class="form-group  chon-thang">
                                        <select class="form-control select2" id="month_change" name="month">
                                            <option value="">--THÁNG--</option>
                                            @for($i = 1; $i <=12; $i++)
                                                <option
                                                    value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
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
                                        <input type="text" class="form-control datepicker" autocomplete="off"
                                               name="contact_date_from"
                                               placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif "
                                               value="{{ $arrSearch['contact_date_from'] }}" style="width: 120px">
                                    </div>
                                    @if($time_type == 2)
                                        <div class="form-group chon-ngay den-ngay">
                                            <input type="text" class="form-control datepicker" autocomplete="off"
                                                   name="contact_date_to" placeholder="Đến ngày"
                                                   value="{{ $arrSearch['contact_date_to'] }}" style="width: 120px">
                                        </div>
                                    @endif
                                @endif

                                @if(Auth::user()->role < 3)
                                    <div class="form-group ">
                                        <select class="form-control select2 search-form-change" name="user_id" id="user_id">
                                            <option value="">--User--</option>
                                            @foreach($listUser as $user)
                                                <option value="{{ $user->id }}" {{ @$arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="form-group ">
                                    <select class="form-control select2" name="source" id="source">
                                        <option value="">-- Chọn nguồn --</option>
                                        @foreach($sources as $source)
                                            <option
                                                value="{{$source->id}}" {{ @$arrSearch['source'] == $source->id ? "selected" : "" }}>{{$source->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group ">
                                    <select class="form-control select2" name="source2" id="source2">
                                        <option value="">-- Chọn nguồn 2 --</option>
                                        @foreach($sources2 as $source2)
                                            <optgroup label="{{$source2->name}}">
                                                @foreach($source2->childs as $source)
                                                    <option
                                                        value="{{$source->id}}" {{@$arrSearch['source2'] == $source->id ? "selected" : "" }}>{{$source->name}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control select2 search-form-change" name="status" id="status">
                                        <option value="">-- Trạng thái-- </option>
                                        <option value="1" {{ $arrSearch['status'] == 1 ? "selected" : "" }}>Đang tư vấn</option>
                                        <option value="2" {{ $arrSearch['status'] == 2 ? "selected" : "" }}>Đã chốt</option>
                                        <option value="3" {{ $arrSearch['status'] == 3 ? "selected" : "" }}>Đã hoàn thành</option>
                                        <option value="4" {{ $arrSearch['status'] == 4 ? "selected" : "" }}>Không chốt được</option>
                                        <option value="5" {{ $arrSearch['status'] == 5 ? "selected" : "" }}>Không có nhu cầu</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 search-form-change" name="product_type" id="product_type">
                                        <option value="">-- Sản phẩm quan tâm -- </option>
                                        @php
                                            $types = ['1' => 'Tour', '2' => 'Combo', '3' => 'Khách sạn', '4' => 'Vé tham quan', '5'=> 'Xe']
                                        @endphp
                                        @foreach($types as $key => $type)
                                            <option
                                                value="{{$key}}" {{ $arrSearch['product_type'] == $key ? "selected" : "" }}>{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" >
                                    <input type="checkbox" style="cursor: pointer;" name="ads" id="ads" {{ $arrSearch['ads'] == 1 ? "checked" : "" }} value="1" class="search-form-change">
                                    <label for="is_send" style="cursor: pointer; color: red" >ADS</label>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                                <button type="button" class="btn btn-info btn-sm" id="export" style="margin-top: -5px">Export</button>
                            </form>
                        </div>
                    </div>
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered table-hover summary-table" style="font-size: 16px;font-weight: bold;">
                                        <tr>
                                            <th colspan="3" class="text-center" style="background-color: #06b7a4;color:#FFF">TỔNG KẾT</th>
                                        </tr>
                                        <tr style="background-color: #06b7a4;color:#FFF">
                                            <th>NGUỒN</th>
                                            <th class="text-right">SỐ KHÁCH</th>
                                            <th class="text-right">DOANH THU</th>
                                        </tr>
                                        <tbody>
                                            <tr>
                                                <td>Tổng</td>
                                                <td class="text-right">{{ array_sum($sourceCount) }}</td>
                                                <td class="text-right">{{ number_format(array_sum($sourceSummary)) }}</td>
                                            </tr>
                                        </tbody>
                                        @foreach($sources as $source)
                                            <tbody>
                                                <tr>
                                                    <td>{{ $source->name }}</td>
                                                    <td class="text-right">{{ !empty($sourceCount[$source->id]) ? $sourceCount[$source->id] : 0 }}</td>
                                                    <td class="text-right">{{ !empty($sourceSummary[$source->id]) ? number_format($sourceSummary[$source->id] ): 0 }}</td>
                                                </tr>
                                                @if(!empty($source->childs))
                                                    @foreach($source->childs as $source2)
                                                        <tr>
                                                            <td class="source-2{{$source2->id == count($source->childs) - 1 ? ' last-child': ''}}">{{ $source2->name }}</td>
                                                            <td class="text-right">{{ !empty($source2Count[$source2->id]) ? $source2Count[$source2->id] : 0 }}</td>
                                                            <td class="text-right">{{ !empty($source2Summary[$source2->id]) ? number_format($source2Summary[$source2->id] ): 0 }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered table-hover" style="font-size: 16px;font-weight: bold;">
                                        <tr>
                                            <th colspan="2" class="text-center" style="background-color: #06b7a4;color:#FFF">TRẠNG THÁI</th>
                                        </tr>
                                        @foreach(array_keys($statusSummary) as $status)
                                            @php
                                                $number = !empty($statusSummary[$status]) ? $statusSummary[$status] : 0;
                                                $percent = count($items) ? round($number * 100 /  count($items), 2) : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ App\Helpers\Helper::getCustomerStatus($status) }}</td>
                                                <td class="text-right">{{ !empty($statusSummary[$status]) ? $statusSummary[$status] : 0 }} ({{$percent}}%)</td>
                                            </tr>
                                        @endforeach
                                    </table>

                                    <table class="table table-bordered table-hover" style="font-size: 16px;font-weight: bold;">
                                        <tr>
                                            <th colspan="2" class="text-center" style="background-color: #06b7a4;color:#FFF">SẢN PHẨM QUAN TÂM</th>
                                        </tr>
                                        @foreach(array_keys($productTypeCount) as $type)
                                            @php
                                                $number = !empty($productTypeCount[$type]) ? $productTypeCount[$type] : 0;
                                                $percent = count($items) ? round($number * 100 /  count($items), 2) : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ !empty($types[$type]) ? $types[$type] : 'Khác' }}</td>
                                                <td class="text-right">{{ !empty($productTypeCount[$type]) ? $productTypeCount[$type] : 0 }} ({{$percent}}%)</td>
                                            </tr>
                                        @endforeach
                                    </table>

                                    <table class="table table-bordered table-hover" style="font-size: 16px;font-weight: bold;">
                                        <tr>
                                            <th colspan="2" class="text-center" style="background-color: #06b7a4;color:#FFF">THEO CHƯƠNG TRÌNH QUẢNG CÁO</th>
                                        </tr>
                                        @foreach(array_keys($adsCount) as $ads)
                                            @php
                                                $number = !empty($adsCount[$ads]) ? $adsCount[$ads] : 0;
                                                $percent = count($items) ? round($number * 100 /  count($items), 2) : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $ads }}</td>
                                                <td class="text-right">{{ !empty($adsCount[$ads]) ? $adsCount[$ads] : 0 }} ({{$percent}}%)</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                            <p style="margin-top: 20px; color: red">Chi tiết theo user</p>
                           <div class="table-responsive">
                               <table class="table table-bordered table-condensed" id="export-table">
                                   <tr style="background-color: #06b7a4; color: #FFF;">
                                       <th>Khách hàng</th>
                                       <th>Số ĐT/Email</th>
                                       <th>Nguồn</th>
                                       <th>Trạng thái</th>
                                       <th>Ngày liên hệ</th>
                                       <th>Sản phẩm/Dịch vụ</th>
                                       <th>Mô tả / Ghi chú</th>
                                       <th>Số SP/DV Chốt</th>
                                       @foreach($sources as $source)
                                           <th>Doanh thu {{ $source->name }}</th>
                                       @endforeach
                                       <th>Tổng theo user</th>
                                   </tr>
                                   <tbody>
                                   @foreach( $data as $user)
                                       <tr style="background-color: #ff9900; color: #fff; font-weight: bold; font-size: 24px">
                                           <td colspan="7">{{$user['name']}}</td>
                                           <td>{{$user['total_bookings']}}</td>
                                           @foreach($sources as $source)
                                               <td>
                                                   {{ number_format(!empty($user[$source->id] )? $user[$source->id] : 0) }}
                                               </td>
                                           @endforeach
                                           <td>{{number_format(!empty($user['total_price']) ? $user['total_price'] : 0)}}</td>
                                       </tr>
                                       @foreach($user['items'] as $item)
                                           <tr id="row-{{ $item->id }}">
                                               <td style="white-space: nowrap">
                                                   {{ $item->name }}
                                               </td>
                                               <td style="white-space: nowrap">{{ $item->phone }} @if($item->phone_2)
                                                       - {{ $item->phone_2 }}
                                                   @endif
                                                   <br>
                                                   @if($item->email)
                                                       {{ $item->email }}
                                                   @endif
                                               </td>
                                               <td style="white-space: nowrap">
                                                   {{$item->sourceRef ? $item->sourceRef->name : ''}} @if(!empty($item->source2Ref)) - {{ $item->source2Ref->name }} @endif
                                                   @if(!empty($item->ads))
                                                       <br/><span style="color: red">ADS: {{$item->adsCampaign ? $item->adsCampaign->name : 'N/A' }}</span>
                                                   @endif
                                               </td>
                                               <td style="white-space: nowrap">
                                                   {{ App\Helpers\Helper::getCustomerStatus($item->status) }}
                                               </td>
                                               <td style="white-space:nowrap">{{ date('d/m/Y H:i', strtotime($item->contact_date)) }}</td>
                                               <td>
                                                   @if($item->product_type)
                                                       {{ $types[$item->product_type] }}
                                                   @endif
                                                   @if($item->product_id &&  !empty($item->product))
                                                       <br/><i class="fa fa-tag"></i> {{ $item->product->name }}
                                                   @endif
                                                   @if($item->demand )
                                                       <br/> <i style="color: red"> {!! $item->demand !!}</i>
                                                   @endif
                                               </td>
                                               <td>{!! $item->notes !!}</td>
                                               <td>
                                                   {{$item->bookings->count()}}
                                               </td>
                                               @php
                                                   $total = $item->bookings->sum('total_price');
                                               @endphp
                                               @foreach($sources as $source)
                                                   <td>
                                                       @if($item->source == $source->id && $total)
                                                           {{ number_format($total) }}
                                                       @endif
                                                   </td>
                                               @endforeach
                                               <td></td>
                                           </tr>
                                       @endforeach
                                   @endforeach
                                   </tbody>
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
