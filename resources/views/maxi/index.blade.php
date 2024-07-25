@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Quản lí maxi
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route( 'maxi.index' ) }}">Maxi</a></li>
                <li class="active">Danh sách</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    @if(Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    
                             <p style="color: red; text-transform: uppercase;">CẦN THÊM MỚI/CHỈNH SỬA THÔNG TIN/UPLOAD HÌNH ẢNH <a target="_blank" href="https://plantotravel.vn/backend/maxi" >CLICK VÀO ĐÂY</a></p>
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Tổng số dòng: <span class="value">( {{ $items->total() }} )</span>
                            </h3>

                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered" id="table-list-data">
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th>Ảnh</th>
                                    <th>Tên</th>
                                    <th width="15%;white-space:nowrap">Trạng thái khả dụng</th>
{{--                                    <th>Danh sách ảnh</th>--}}
                                    <th width="1%;white-space:nowrap">Thao tác</th>
                                </tr>
                                <tbody>
                                @if( $items->count() > 0 )
                                        <?php $i = 0; ?>
                                    @foreach( $items as $item )
                                            <?php $i++; ?>
                                        <tr id="row-{{ $item->id }}">
                                            <td><span class="order">{{ $i }}</span></td>
                                            <td width="150">
                                                @if($item->thumbnail)
                                                    <img class="img-thumbnail"
                                                         src="https://plantotravel.vn/{{ $item->thumbnail->image_url }}"
                                                         width="145">
                                                @endif
                                            </td>
                                           <td>{{$item->name}}</td>
                                            <td>
                                                {{ $item->status == 1 ? "Đã cho mượn" : "Chưa cho mượn" }}
                                            </td>
{{--                                            <td></td>--}}
                                            <td style="white-space:nowrap;" class="text-right">
                                                <a href="{{ route('maxi.history', ['id' => $item->id]) }}"  class="btn btn-info btn-sm">
                                                    Lịch cho mượn</a>
                                                <!-- <a href="{{ route( 'maxi.edit', [ 'id' => $item->id ]) }}"
                                                   class="btn btn-warning btn-sm"><span
                                                        class="glyphicon glyphicon-pencil"></span></a>

                                                <a onclick="return callDelete('{{ $item->name }}','{{ route( 'maxi.destroy', [ 'id' => $item->id ]) }}');"
                                                   class="btn btn-danger btn-sm"><span
                                                        class="glyphicon glyphicon-trash"></span></a> -->

                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">Không có dữ liệu.</td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>
                            <div style="text-align:center">
                                {{ $items->appends(request()->query())->links() }}

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
    </script>
@stop
