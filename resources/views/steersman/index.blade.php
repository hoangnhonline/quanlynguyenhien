@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Danh sách tài công
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('steersman.index') }}">Danh sách tài công</a></li>
                <li class="active">Danh sách</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    @if (Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    <a href="{{ route('steersman.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px"><i
                            class="fa fa-plus" aria-hidden="true"></i> Tạo mới</a>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Bộ lọc</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-inline" role="form" method="GET" action="{{ route('steersman.index') }}"
                                id="searchForm">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="s" id="s"
                                        value="{{ old('s', $filters['s'] ?? null) }}" placeholder="Tìm kiếm tài công">
                                </div>
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                            </form>
                        </div>
                    </div>
                    <div class="box">

                        <div class="box-header with-border">
                            <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} tài công )</span>
                            </h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">

                            <table class="table table-bordered" id="table-list-data">
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th style="width: 10%">Họ tên</th>
                                    <th>Giới thiệu</th>
                                    <th style="width: 12%">Số năm kinh nghiệm</th>
                                    <th style="width: 10%">Ảnh đại diện</th>

                                    <th width="1%;white-space:nowrap">Thao tác</th>
                                </tr>
                                <tbody>
                                    @if ($items->count() > 0)
                                        <?php $i = 0; ?>
                                        @foreach ($items as $item)
                                            <?php $i++; ?>
                                            <tr id="row-{{ $item->id }}">
                                                <td><span class="order">{{ $i }}</span></td>
                                                <td>
                                                    @if (isset($item->avatar))
                                                        <img width="50" class="lazy"
                                                            src="{{ Helper::showImage($item->avatar) }}"
                                                            alt="{{ $item->name }}" srcset="">
                                                    @endif
                                                </td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->bio }}</td>
                                                <td>{{ $item->experiences }}</td>

                                                <td style="white-space:nowrap">
                                                    <a href="{{ route('steersman.edit', ['id' => $item->id]) }}"
                                                        class="btn btn-warning btn-sm"><span
                                                            class="glyphicon glyphicon-pencil"></span></a>

                                                    <a onclick="return callDelete('{{ $item->name }}','{{ route('steersman.destroy', ['id' => $item->id]) }}');"
                                                        class="btn btn-danger btn-sm"><span
                                                            class="glyphicon glyphicon-trash"></span></a>
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
                            <div style="text-align:center"> {{ $items->links() }}</div>
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
            }).then(function() {
                location.href = url;
            })
            return flag;
        }
    </script>
@stop
