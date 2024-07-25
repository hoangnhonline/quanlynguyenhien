@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Chỉnh sửa tài công
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('steersman.index') }}">Chỉnh sửa tài công</a></li>
                <li class="active">Chỉnh sửa</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('steersman.index') }}" style="margin-bottom:5px">Quay
                lại</a>
            <form role="form" method="POST" action="{{ route('steersman.update') }}" id="dataForm">
                <div class="row">
                    <!-- left column -->

                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Chỉnh sửa</h3>
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
                                <input type="hidden" name="id" value="{{ $detail->id }}">
                                <div class="row">
                                    {{--                                    Tên tài công --}}
                                    <div class="form-group col-md-6">
                                        <label for="name">Tên tài công<span class="red-star">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="{{ old('name', $detail->name) }}">
                                    </div>

                                    {{--                                    Số năm kinh nghiệm --}}
                                    <div class="form-group col-md-6">
                                        <label for="name">Số năm kinh nghiệm<span class="red-star">*</span></label>
                                        <input type="text" class="form-control" name="experiences" id="experiences"
                                            value="{{ old('experiences', $detail->experiences) }}">
                                    </div>

                                </div>
                                <div class="row">
                                    {{--                                    Giới thiệu --}}
                                    <div class="form-group col-md-12">
                                        <label>Giới thiệu</label>
                                        <textarea class="form-control" rows="4" name="bio" id="bio">{{ old('bio', $detail->bio) }}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    {{--                                    Ảnh đại diện --}}

                                    <div class="form-group col-md-6" style="margin-top:10px;margin-bottom:10px">
                                        <label class="col-md-3 row">Ảnh đại diện</label>
                                        <div class="col-md-9">
                                            <img id="thumbnail_banner"
                                                src="{{ old('avatar', $detail->avatar) ? Helper::showImage(old('avatar', $detail->avatar)) : asset('admin/dist/img/img.png') }}"
                                                class="img-thumbnail" width="200">
                                            <button class="btn btn-default btn-sm btnSingleUpload" data-set="avatar"
                                                data-image="thumbnail_banner" type="button"><span
                                                    class="glyphicon glyphicon-upload" aria-hidden="true"></span>
                                                Upload</button>
                                            <input type="hidden" name="avatar" id="avatar"
                                                value="{{ old('avatar', $detail->avatar) }}" />
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    {{--                                    Ảnh bằng cấp --}}
                                    <div class="form-group col-md-6">
                                        <label for="kind_of_property">Hình ảnh bằng cấp</label>
                                        <div class="col-md-12 box">
                                            <div class="box-body text-center">
                                                <button class="btn btn-primary btnMultiUpload" type="button"
                                                    data-thumbnail-choose="0" data-target-upload="#div-image2"
                                                    data-name="degree_img">
                                                    <span class="glyphicon glyphicon-upload" aria-hidden="true"></span>
                                                    Upload</button>
                                                <div class="clearfix"></div>
                                                <div id="div-image2" style="margin-top:10px">
                                                    <div id="div-image1" style="margin-top:10px">
                                                        @if (!empty(old('degree_img', $detail->degree_img)))
                                                            @foreach (old('degree_img', $detail->degree_img) as $k => $image)
                                                                <div class="col-md-3">
                                                                    <img class="img-thumbnail"
                                                                        src="{{ Helper::showImage($image) }}"
                                                                        style="width:100%">

                                                                    <div class="checkbox">
                                                                        <button class="btn btn-danger btn-sm remove-image"
                                                                            type="button" data-value="{{ md5($image) }}"
                                                                            data-id="{{ md5($image) }}">Xóa</button>
                                                                    </div>
                                                                    <input type="hidden" name="degree_img[]"
                                                                        value="{{ $image }}">
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both"></div>

                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                                <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm"
                                    href="{{ route('steersman.index') }}">Hủy</a>
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
    <input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">

@stop
