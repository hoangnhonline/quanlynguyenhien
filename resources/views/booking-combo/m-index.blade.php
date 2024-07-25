@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Main content -->

        <!-- Content Header (Page header) -->
        <section class="content-header" style="padding-top: 10px;">
            <h1 style="text-transform: uppercase;">
                Quản lý đặt combo
            </h1>

        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    @if(Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    <a href="{{ route('booking-combo.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Bộ lọc</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-inline" role="form" method="GET" action="{{ route('booking-combo.index') }}" id="searchForm">
                                <div class="form-group">
                                    <input type="text" class="form-control" autocomplete="off" name="id_search"
                                           value="{{ $arrSearch['id_search'] }}" style="width: 70px" placeholder="ID">
                                </div>
                                <div class="clearfix"></div>
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
                                <div class="form-group">
                                    &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3"
                                                             {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
                                    <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                            </form>
                        </div>
                    </div>
                    <div class="box">

                        <div class="box-header with-border">
                            <h3 class="box-title col-md-8">Danh sách ( <span
                                    class="value">{{ $items->total() }} combo )</span>
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
                                        <th style="width: 1%; white-space: nowrap;">ID</th>
                                        <th style="white-space: nowrap;">Từ ngày - Đến ngày</th>
                                        <th style="">Khách sạn</th>
                                        <th style="">Tour</th>
                                        <th style="">Set ăn</th>
                                        <th style="">Tổng tiền</th>
                                        <th width="1%;white-space:nowrap">Thao tác</th>
                                    </tr>
                                    <tbody>
                                    @if( $items->count() > 0 )
                                        <?php $i = 0;

                                        ?>
                                        @foreach( $items as $item )
                                            <?php $i++; ?>
                                            <tr id="row-{{ $item->id }}"
                                                style="border-bottom: 1px solid #ddd !important;">
                                                <td><span class="order"><strong
                                                            style="color: red;font-size: 16px">{{ $item->id }}</strong></span>
                                                </td>
                                                <td>
                                                    {{ date('d/m/y', strtotime($item->from_date)) }}
                                                    - {{ date('d/m/y', strtotime($item->to_date)) }}
                                                </td>
                                                <td>
                                                    {{$item->room->name}}<br/>
                                                    {{number_format($item->room_price)}}đ
                                                </td>
                                                <td>
                                                    {{$item->tour->name}}<br/>
                                                    {{number_format($item->tour_price)}}đ
                                                </td>
                                                <td>
                                                    {{$item->set->name}}<br/>
                                                    {{number_format($item->set_price)}}đ
                                                </td>
                                                <td>
                                                    {{number_format($item->total_price)}}đ
                                                </td>
                                                <td style="white-space:nowrap">
                                                    @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                                                    <a href="{{ route( 'booking-combo.edit', $arrEdit ) }}"
                                                       class="btn btn-warning btn-sm"><span
                                                            class="glyphicon glyphicon-pencil"></span></a>
                                                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1)
                                                        <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-combo.destroy', [ 'id' => $item->id ]) }}');"
                                                           class="btn btn-danger btn-sm"><span
                                                                class="glyphicon glyphicon-trash"></span></a>
                                                    @endif
                                                </td>
                                            </tr>

                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8">Không có dữ liệu.</td>
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
    <!-- Modal -->
    <div class="modal fade" id="uncModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
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
@stop
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#sort_by_change').change(function () {
                $('#sort_by').val($(this).val());
                $('#searchForm').submit();
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
        });
    </script>
@stop
