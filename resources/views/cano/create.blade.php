@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Cano
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('cano.index') }}">Danh sách Cano</a></li>
                <li class="active">Tạo mới</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('cano.index') }}" style="margin-bottom:5px">Quay lại</a>
            <form role="form" method="POST" action="{{ route('cano.store') }}" id="dataForm">
                <div class="row">
                    <!-- left column -->

                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Tạo mới</h3>
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
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Tên Cano<span class="red-star">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="{{ old('name') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="steersman_id">Tài công <span class="red-star">*</span></label>
                                        <select class="form-control select2" name="steersman_id" id="steersman_id">
                                            @foreach ($data['steersman'] as $steerman)
                                                <option value="{{ $steerman->id }}"
                                                    {{ $steerman->id == old('steersman_id') ? 'selected' : '' }}>
                                                    {{ $steerman->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="kind_of_property">Loại tài sản <span class="red-star">*</span></label>
                                        <select class="form-control" name="kind_of_property" id="kind_of_property">
                                            @foreach (Helper::getConstant('cano_kind_of_property') as $val => $label)
                                                <option value="{{ $val }}"
                                                    {{ $val == old('kind_of_property') ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="type">Loại Cano <span class="red-star">*</span></label>
                                        <select class="form-control" name="type" id="type">
                                            @foreach (Helper::getConstant('cano_type') as $val => $label)
                                                <option value="{{ $val }}"
                                                    {{ $val == old('type') ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="seats">Số chỗ ngồi <span class="red-star">*</span></label>
                                        <input type="text" class="form-control" name="seats" id="seats"
                                            value="{{ old('seats') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="kind_of_property">Hình ảnh Cano</label>
                                        <div class="col-md-12 box">
                                            <div class="box-body text-center">
                                                <button class="btn btn-primary btnMultiUpload" type="button"><span
                                                        class="glyphicon glyphicon-upload" aria-hidden="true"></span>
                                                    Upload</button>
                                                <div class="clearfix"></div>
                                                <div id="div-image" style="margin-top:10px">
                                                    @if (!empty(old('certificate_of_insurance_img')))
                                                        @foreach (old('certificate_of_insurance_img') as $k => $image)
                                                            <div class="col-md-3">
                                                                <img class="img-thumbnail"
                                                                    src="{{ Helper::showImage($image) }}"
                                                                    style="width:100%">

                                                                <div class="checkbox">
                                                                    <button class="btn btn-danger btn-sm remove-image"
                                                                        type="button" data-value="{{ $image }}"
                                                                        data-id="{{ $image }}">Xóa</button>
                                                                </div>
                                                                <input type="hidden" name="certificate_of_insurance_img[]"
                                                                    value="{{ $image }}">
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="kind_of_property">Hình ảnh đăng kiểm</label>
                                        <div class="col-md-12 box">
                                            <div class="box-body text-center">
                                                <button class="btn btn-primary btnMultiUpload" type="button"
                                                    data-thumbnail-choose="0" data-target-upload="#div-image1"
                                                    data-name="certificate_of_registry_img">
                                                    <span class="glyphicon glyphicon-upload" aria-hidden="true"></span>
                                                    Upload</button>
                                                <div class="clearfix"></div>
                                                <div id="div-image1" style="margin-top:10px">
                                                    @if (!empty(old('certificate_of_registry_img')))
                                                        @foreach (old('certificate_of_registry_img') as $k => $image)
                                                            <div class="col-md-3">
                                                                <img class="img-thumbnail"
                                                                    src="{{ Helper::showImage($image) }}"
                                                                    style="width:100%">

                                                                <div class="checkbox">
                                                                    <button class="btn btn-danger btn-sm remove-image"
                                                                        type="button" data-value="{{ $image }}"
                                                                        data-id="{{ $image }}">Xóa</button>
                                                                </div>
                                                                <input type="hidden" name="image_tmp_url[]"
                                                                    value="{{ $image }}">
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="kind_of_property">Hình ảnh giấy bảo hiểm</label>
                                        <div class="col-md-12 box">
                                            <div class="box-body text-center">
                                                <button class="btn btn-primary btnMultiUpload" type="button"
                                                    data-thumbnail-choose="0" data-target-upload="#div-image2"
                                                    data-name="certificate_of_insurance_img">
                                                    <span class="glyphicon glyphicon-upload" aria-hidden="true"></span>
                                                    Upload</button>
                                                <div class="clearfix"></div>
                                                <div id="div-image2" style="margin-top:10px">
                                                    @if (!empty(old('certificate_of_insurance_img')))
                                                        @foreach (old('certificate_of_insurance_img') as $k => $image)
                                                            <div class="col-md-3">
                                                                <img class="img-thumbnail"
                                                                    src="{{ Helper::showImage($image) }}"
                                                                    style="width:100%">

                                                                <div class="checkbox">
                                                                    <button class="btn btn-danger btn-sm remove-image"
                                                                        type="button" data-value="{{ $image }}"
                                                                        data-id="{{ $image }}">Xóa</button>
                                                                </div>
                                                                <input type="hidden"
                                                                    name="certificate_of_insurance_img[]"
                                                                    value="{{ $image }}">
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both"></div>
                                <div class="form-group">
                                    <label>Ẩn/hiện</label>
                                    <select class="form-control" name="status" id="status">
                                        @foreach (Helper::getConstant('status') as $val => $label)
                                            <option value="{{ $val }}"
                                                {{ $val == (old('status') ?? 1) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                                <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm"
                                    href="{{ route('cano.index') }}">Hủy</a>
                            </div>
                        </div>
                        <!-- /.box -->
                    </div>
            </form>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">

@stop
