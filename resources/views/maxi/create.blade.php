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
                <li><a href="{{ route('maxi.index') }}">Maxi</a></li>
                <li class="active">Thêm mới</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('maxi.index') }}" style="margin-bottom:5px">Quay lại</a>
            <form role="form" method="POST" action="{{ route('maxi.store') }}" id="dataForm" class="productForm">
                <input type="hidden" name="is_copy" value="1">
                <div class="row">
                    <!-- left column -->

                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Thêm mới</h3>
                            </div>
                            <!-- /.box-header -->
                            {!! csrf_field() !!}
                            <div class="box-body">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div>

                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#home" aria-controls="home"
                                                                                  role="tab" data-toggle="tab">Thông tin
                                                chi tiết</a></li>
                                        <li role="presentation"><a href="#settings" aria-controls="settings" role="tab"
                                                                   data-toggle="tab">Hình ảnh</a></li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">

                                        <div role="tabpanel" class="tab-pane active" id="home">
                                            <input type="hidden" name="type" value="1">
                                            {{--                                         Tên mẫu , thứ tự hiển thị --}}
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label>Tên mẫu <span class="red-star">*</span></label>
                                                    <input type="text" class="form-control req" name="name" id="name"
                                                           value="{{ old('name') }}">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Thứ tự hiển thị <span class="red-star">*</span></label>
                                                    <input type="number" class="form-control req" maxlength="11"
                                                           name="display_order" id="price_infant"
                                                           value="{{ old('display_order') }}">
                                                </div>
                                            </div>
                                            {{--                                            Trạng thái khả dụng --}}
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label>Trạng thái khả dụng <span class="red-star">*</span></label>
                                                    <select class="form-control" name="status" id="status">
                                                        <option
                                                            value="1" {{ old('status') == 1 ? "selected" : "" }}>
                                                            Đã cho mượn
                                                        </option>
                                                        <option
                                                            value="0" {{ old('status') == 0 || old('status') == NULL ? "selected" : "" }}>
                                                            Chưa cho mượn
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            {{--                                            Ghi chú --}}
                                            <div class="form-group" style="margin-top: 15px !important;">
                                                <label>ghi chú</label>
                                                <button class="btnUploadEditor btn btn-info" type="button"
                                                        style="float:right;margin-bottom: 3px !important;"
                                                        data-editor="notes">Chèn ảnh
                                                </button>
                                                <div class="clearfix"></div>
                                                <textarea class="form-control ckeditor" rows="2" name="note" style="height: 20px"
                                                          id="content">{{ old('note') }}</textarea>
                                            </div>

                                            <div style="margin-bottom:10px;clear:both"></div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <input type="hidden" id="editor" value="">

                                        {{--Hình ảnh --}}
                                        <div role="tabpanel" class="tab-pane" id="settings">
                                            <div class="form-group" style="margin-top:10px;margin-bottom:10px">

                                                <div class="col-md-12">
                                                    <button class="btn btn-success btnMultiUpload" type="button"><span
                                                            class="glyphicon glyphicon-upload"
                                                            aria-hidden="true"></span> Chọn hình ảnh (1200 x 800px)
                                                    </button>
                                                    <div class="clearfix"></div>
                                                    <div id="div-image" style="margin-top:10px"></div>
                                                </div>
                                                <div style="clear:both"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="box-footer">
                                <button type="button" class="btn btn-default" id="btnLoading" style="display:none"><i
                                        class="fa fa-spin fa-spinner"></i></button>
                                <button type="submit" class="btn btn-primary" id="btnSave">Lưu</button>
                                <a class="btn btn-default" class="btn btn-primary"
                                   href="{{ route('maxi.index')}}">Hủy</a>
                            </div>

                        </div>
                        <!-- /.box -->

                    </div>
                </div>
            </form>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <input type="hidden" id="route_upload_tmp_image_multiple" value="{{ route('image.tmp-upload-multiple') }}">
    <input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">

@stop
@section('js')

@stop
