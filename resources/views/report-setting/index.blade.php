@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Đặt mục tiêu lợi nhuận
            </h1>

        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    @if(Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    <a href="{{ route('report-setting.create', ['city_id' => $city_id]) }}"
                       class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
                    <div class="box">

                        <div class="box-header with-border">
                            <h3 class="box-title">Danh sách</h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered" id="table-list-data">
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th width="50%">Thời gian</th>
                                    <th>Module</th>
                                    <th>Mục tiêu</th>
                                    <th width="1%;white-space:nowrap">Thao tác</th>
                                </tr>
                                <tbody>
                                @if( $items->count() > 0 )
                                        <?php $i = 0; ?>
                                    @foreach( $items as $item )
                                            <?php $i++; ?>
                                        <tr id="row-{{ $item->id }}">
                                            <td><span class="order">{{ $i }}</span></td>

                                            <td>
                                                <a href="{{ route( 'report-setting.edit', [ 'id' => $item->id ]) }}">{{ $item->month }}/{{ $item->year }}</a>
                                            </td>
                                            <td>
                                                <span style="text-transform: capitalize">{{ $item->module }}</span>
                                            </td>
                                            <td>
                                                {{ number_format($item->target) }}
                                            </td>
                                            <td style="white-space:nowrap">
                                                <a href="{{ route( 'report-setting.edit', [ 'id' => $item->id ]) }}"
                                                   class="btn btn-warning btn-sm"><span
                                                        class="glyphicon glyphicon-pencil"></span></a>


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
        function callDelete(name, url) {
            swal({
                title: 'Bạn muốn xóa "' + name + '"?',
                text: "Dữ liệu sẽ không thể phục hồi.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then(function () {
                location.href = url;
            })
            return flag;
        }

        $(document).ready(function () {
            $('#table-list-data tbody').sortable({
                placeholder: 'placeholder',
                handle: ".move",
                start: function (event, ui) {
                    ui.item.toggleClass("highlight");
                },
                stop: function (event, ui) {
                    ui.item.toggleClass("highlight");
                },
                axis: "y",
                update: function () {
                    var rows = $('#table-list-data tbody tr');
                    var strOrder = '';
                    var strTemp = '';
                    for (var i = 0; i < rows.length; i++) {
                        strTemp = rows[i].id;
                        strOrder += strTemp.replace('row-', '') + ";";
                    }
                    updateOrder("cate_child", strOrder);
                }
            });
        });

        function updateOrder(table, strOrder) {
            $.ajax({
                url: $('#route_update_order').val(),
                type: "POST",
                async: false,
                data: {
                    str_order: strOrder,
                    table: table
                },
                success: function (data) {
                    var countRow = $('#table-list-data tbody tr span.order').length;
                    for (var i = 0; i < countRow; i++) {
                        $('span.order').eq(i).html(i + 1);
                    }
                }
            });
        }
    </script>
@stop
