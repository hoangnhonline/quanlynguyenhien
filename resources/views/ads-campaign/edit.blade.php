@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Chiến dịch quảng cáo
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('ads-campaign.index') }}">Danh sách Chiến dịch quảng cáo</a></li>
                <li class="active">Chỉnh sửa</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('ads-campaign.index') }}" style="margin-bottom:5px">Quay
                lại</a>
            <form role="form" method="POST" action="{{ route('ads-campaign.update') }}" id="dataForm">
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
                                    <div class="form-group col-md-12">
                                        <label for="name">Tên chiến dịch<span class="red-star">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name"
                                               value="{{ old('name', $detail->name) }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="from_date">Ngày bắt đầu <span class="red-star">*</span></label>
                                        <input type="text" class="form-control datepicker" name="from_date"
                                               id="from_date" autocomplete="off" placeholder="Ngày bắt đầu"
                                               value="{{ old('from_date', $detail->from_date->format('d/m/Y')) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="type">Ngày kết thúc <span class="red-star">*</span></label>
                                        <input type="text" class="form-control datepicker" name="to_date" id="to_date"
                                               autocomplete="off" placeholder="Ngày kết thúc"
                                               value="{{ old('to_date', $detail->to_date->format('d/m/Y')) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="seats">Ngân sách/ngày <span class="red-star">*</span></label>
                                        <input type="text" class="form-control number" name="budget" id="budget"
                                               value="{{ old('budget', $detail->budget) }}">
                                    </div>
                                </div>
                                <div style="clear:both"></div>
                                <div class="form-group">
                                    <label>Ẩn/hiện</label>
                                    <select class="form-control" name="status" id="status">
                                        @foreach (Helper::getConstant('status') as $val => $label)
                                            <option value="{{ $val }}"
                                                {{ $val == (old('status', $detail->status) ?? 1) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                                <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm"
                                   href="{{ route('ads-campaign.index') }}">Hủy</a>
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
