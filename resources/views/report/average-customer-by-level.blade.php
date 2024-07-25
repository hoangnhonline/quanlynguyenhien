@extends('layout')
@section('content')
    <div class="content-wrapper">


        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 style="text-transform: uppercase;">
                Số lượng khách
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route( 'debt.report') }}">
                        Thống kê</a></li>
                <li class="active">Số lượng khách</li>
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

                        <div class="panel-body" style="padding: 5px !important;">
                            <form class="form-inline" role="form" method="GET" action="{{ route('report.average-guest-by-level') }}"
                                  id="searchForm" style="margin-bottom: 0px;">
                                <input type="hidden" name="type" value="{{ $type }}">
                                <div class="form-group">
                                    <select class="form-control select2" name="tour_id" id="tour_id">
                                        <option value="">--Tour--</option>
                                        @foreach($tourSystem as $tour)
                                            <option
                                                value="{{ $tour->id }}" {{ $arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
                                </div>
                                @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                                    <div class="form-group">
                                        <select class="form-control select2" name="level" id="level">
                                            <option value="">--Phân loại sales--</option>

                                            <option value="2" {{ $level == 2 ? "selected" : "" }}>Đối tác</option>

                                            <option value="7" {{ $level == 7 ? "selected" : "" }}>GỬI BẾN</option>
                                        </select>
                                    </div>
                                @endif
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                                <div class="form-group">
                                    <button type="button" id="btnReset" class="btn btn-default btn-sm">Reset</button>
                                </div>
                                <div>
                                    @if($arrSearch['tour_id'] != 4)
                                        <div class="form-group">
                                            <input type="checkbox" name="tour_type[]" id="tour_type_1"
                                                   {{ in_array(1, $arrSearch['tour_type']) ? "checked" : "" }} value="1">
                                            <label for="tour_type_1">GHÉP({{ $ghep }})</label>
                                        </div>
                                        <div class="form-group">
                                            &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_2"
                                                                     {{ in_array(2, $arrSearch['tour_type']) ? "checked" : "" }} value="2">
                                            <label for="tour_type_2">VIP({{ $vip }}-{{ $tong_vip }}NL)&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                        </div>
                                        <div class="form-group" style="border-right: 1px solid #9ba39d">
                                            &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_3"
                                                                     {{ in_array(3, $arrSearch['tour_type']) ? "checked" : "" }} value="3">
                                            <label for="tour_type_3">THUÊ CANO({{ $thue }}
                                                )&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_1"
                                                                 {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
                                        <label for="status_1">Mới</label>
                                    </div>
                                    <div class="form-group">
                                        &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_2"
                                                                 {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
                                        <label for="status_2">Hoàn Tất</label>
                                    </div>


                                </div>
                            </form>
                        </div>
                    </div>

                    <p style="text-align: right;"><a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export
                            Excel</a>
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="table-list-data">
                                    <tr style="background-color: #f4f4f4">
                                        <th width="140">Sales</th>
                                        <th class="text-center" width="80">NL</th>
                                        <th class="text-center" width="80">TE</th>
                                        <th class="text-center" width="80">ĂN NL</th>
                                        <th class="text-center" width="80">ĂN TE</th>
                                        <th class="text-center" width="80">CÁP NL</th>
                                        <th class="text-center" width="80">CÁP TE</th>
                                    </tr>
                                    <tbody>
                                    @if( count($items) )
                                            <?php $l = 0; ?>
                                        @foreach( $items as $item )
                                                <?php $l++; ?>
                                            <tr class="booking" id="row-{{ $item->booking_id }}"
                                                data-id="{{ $item->id }}" data-date="{{ $item->use_date }}"
                                                style="border-bottom: 1px solid #000 !important;@if($item->status == 3) background-color: #f77e7e; @endif">
                                                <td>
                                                    {{$item->user_name}}
                                                </td>

                                                <td class="text-center">
                                                    {{ $item->sum_adults }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->sum_childs}}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->sum_meals }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->sum_meals_te}}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->sum_cap_nl }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->sum_cap_te}}
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
<style type="text/css">
    .hdv {
        cursor: pointer;
    }

    .hdv:hover, .hdv.selected {
        background-color: #06b7a4;
        color: #FFF
    }

    label {
        cursor: pointer;
    }

    #table_report th td {
        padding: 2px !important;
    }

    #searchForm, #searchForm input {
        font-size: 13px;
    }

    .form-control {
        font-size: 13px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {

        font-size: 12px !important;
    }

    tr.error {
        background-color: #ffe6e6
    }
</style>
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#btnExport').click(function (e){
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
                })()('table-list-data', 'datatable')
            })

            $('#searchForm input[type=checkbox]').change(function () {
                $('#searchForm').submit();
            });
        });
    </script>
@stop
