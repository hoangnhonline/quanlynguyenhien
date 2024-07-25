@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Danh sách Cano
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('cano.index') }}">Cano</a></li>
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
                    <a href="{{ route('cano.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px"><i
                            class="fa fa-plus" aria-hidden="true"></i> Tạo mới</a>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Bộ lọc</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-inline" role="form" method="GET" action="{{ route('cano.index') }}"
                                id="searchForm">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="s" id="s"
                                        value="{{ old('s', $filters['s'] ?? null) }}" placeholder="Tìm kiếm">
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="steersman_id" id="steersman_id">
                                        <option value="">--Tài công--</option>
                                        @foreach ($data['steersman'] as $steerman)
                                            <option value="{{ $steerman->id }}"
                                                {{ $steerman->id == old('steersman_id', $filters['steersman_id'] ?? null) ? 'selected' : '' }}>
                                                {{ $steerman->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="kind_of_property" id="kind_of_property">
                                        <option value="">--Loại tài sản--</option>
                                        @foreach (Helper::getConstant('cano_kind_of_property') as $val => $label)
                                            <option value="{{ $val }}"
                                                {{ $val == old('kind_of_property', $filters['kind_of_property'] ?? null) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="type" id="type">
                                        <option value="">--Loại Cano--</option>
                                        @foreach (Helper::getConstant('cano_type') as $val => $label)
                                            <option value="{{ $val }}"
                                                {{ $val == old('type', $filters['type'] ?? null) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <select class="form-control" name="status" id="status">
                                    <option value="">--Trạng thái --</option>
                                    @foreach (Helper::getConstant('status') as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ isset($filters['status']) && $val == old('status', $filters['status'] ?? null) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                            </form>
                        </div>
                    </div>

                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-header with-border">
                            <h3 class="box-title">Danh sách ( <span class="value">{{ $canoes->total() }} cano )</span>
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered" id="table-list-data">
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th>Hình</th>
                                    <th>Tên Cano</th>
                                    <th>Loại tài sản</th>
                                    <th>Loại Cano</th>
                                    <th>Số chỗ ngồi</th>
                                    <th>Người lái</th>
                                    <th style="width: 10%">Trạng thái</th>
                                    <th width="1%;white-space:nowrap">Thao tác</th>
                                </tr>
                                <tbody>
                                    @if ($canoes->count() > 0)
                                        <?php $i = 0; ?>
                                        @foreach ($canoes as $cano)
                                            <?php $i++; ?>
                                            <tr id="row-{{ $cano->id }}">
                                                <td><span class="order">{{ $i }}</span></td>
                                                <td>
                                                    @if (isset($cano->thumbnail->image_url))
                                                        <img width="50" class="lazy"
                                                            src="{{ Helper::showImage($cano->thumbnail->image_url) }}"
                                                            alt="{{ $cano->name }}" srcset="">
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $cano->name }}
                                                </td>
                                                <td>
                                                    {{ Helper::getConstant('cano_kind_of_property')[$cano->kind_of_property] }}
                                                </td>
                                                <td>
                                                    {{ Helper::getConstant('cano_type')[$cano->type] }}
                                                </td>
                                                <td>
                                                    {{ $cano->seats }}
                                                </td>
                                                <td>
                                                    {{ $cano->steerman->name }}
                                                </td>
                                                <td>
                                                    {{ Helper::getConstant('status')[$cano->status] }}
                                                </td>

                                                <td style="white-space:nowrap">
                                                    <a href="{{ route('cano.edit', ['id' => $cano->id]) }}"
                                                        class="btn btn-warning btn-sm"><span
                                                            class="glyphicon glyphicon-pencil"></span></a>

                                                    <a onclick="return callDelete('{{ $cano->name }}','{{ route('cano.destroy', ['id' => $cano->id]) }}');"
                                                        class="btn btn-danger btn-sm"><span
                                                            class="glyphicon glyphicon-trash"></span></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="text-center">
                                            <td colspan="9">Không có dữ liệu.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div style="text-align:center"> {{ $canoes->links() }}</div>
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
