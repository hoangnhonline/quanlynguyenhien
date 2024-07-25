@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                YÊU CẦU HOÀN TIỀN
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route( 'user-balance-withdraw.index' ) }}">Yêu cầu rút tiền</a></li>
                <li class="active">Cập nhật</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('user-balance-withdraw.index') }}" style="margin-bottom:5px">Quay
                lại</a>
            <form role="form" method="POST" action="{{ route('user-balance-withdraw.update') }}" id="dataForm">
                <input type="hidden" name="id" value="{{ $detail->id }}">
                <div class="row">
                    <!-- left column -->

                    <div class="col-md-12">
                        <div id="content_alert"></div>
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Cập nhật</h3>
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
                                    <div class="form-group col-md-6 col-xs-6">
                                        <label>Loại giao dịch<span class="red-star">*</span></label>
                                        <select class="form-control select2" id="type" name="type">
                                            @php
                                                $types = [
                                                  \App\Models\UserBalanceHistory::TRANSACTION_TYPE_WITHDRAW => 'Rút tiền từ tài khoản',
                                                ];
                                            @endphp
                                            @foreach($types as $key=>$name)
                                                <option value="{{ $key }}" {{ old('type', $detail->type) == $key ? "selected" : "" }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="status">Trạng thái</label>
                                        <select class="form-control select2" name="status" id="status">
                                            <option value="{{\App\Models\UserBalanceWithdraw::STATUS_PENDING}}" {{ old('status', $detail->status) == \App\Models\UserBalanceWithdraw::STATUS_PENDING ? "selected" : "" }}>
                                                Chưa thanh toán
                                            </option>
                                            <option value="{{\App\Models\UserBalanceWithdraw::STATUS_COMPLETED}}" {{  old('status', $detail->status) == \App\Models\UserBalanceWithdraw::STATUS_COMPLETED ? "selected" : "" }}>
                                                Đã thanh toán
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="nguoi_chi">Đối tác</label>
                                        <select class="form-control select2" name="user_id" id="user_id">
                                            <option value="">--Chọn--</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id', $detail->user_id) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="nguoi_chi">Người chi</label>
                                        <select class="form-control select2" name="nguoi_chi" id="nguoi_chi">
                                            <option value="">--Chọn--</option>
                                            @foreach($collecterList as $payer)
                                                <option value="{{ $payer->id }}" {{ old('nguoi_chi', $detail->nguoi_chi) == $payer->id ? "selected" : "" }}>{{ $payer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-static my-3">
                                            <label class="ms-0">Chủ tài khoản</label>
                                            <input type="text" class="form-control" name="account_name" id="account_name" value="{{old('account_name', $detail->account_name)}}" onfocus="focused(this)" onfocusout="defocused(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-static my-3">
                                            <label class="ms-0">Số tài khoản</label>
                                            <input type="text" class="form-control" name="account_number" id="account_number" value="{{old('account_number', $detail->account_number)}}" onfocus="focused(this)" onfocusout="defocused(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-static my-3">
                                            <label class="ms-0">Tên ngân hàng</label>
                                            <select class="form-control select2" name="account_bank_name" id="account_bank_name">
                                                <option value="">Vui lòng chọn</option>
                                                @foreach(\App\Helpers\Helper::getVietNamBanks() as $bank)
                                                    <option value="{{$bank['short_name']}}" {{old('account_bank_name', $detail->account_bank_name) == $bank['short_name']? 'selected': ''}}>{{$bank['short_name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-static my-3">
                                            <label class="ms-0">Chi nhánh</label>
                                            <input type="text" class="form-control" name="account_bank_branch" id="account_bank_branch" value="{{old('account_bank_branch', $detail->account_bank_branch)}}" onfocus="focused(this)" onfocusout="defocused(this)">
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="col-md-6 col-xs-6">
                                        <div class="form-group ">
                                            <label for="total_money">Tổng tiền</label>
                                            <input type="text" name="amount" class="form-control number total"
                                                   placeholder="Tổng tiền" value="{{ old('amount', $detail->amount) }}">
                                        </div>

                                    </div>
                                    <div class="form-group col-md-6 col-xs-6">
                                        <label for="noi_dung_ck">Nội dung CK</label>
                                        <input type="text" name="noi_dung_ck" id="noi_dung_ck" class="form-control"
                                               placeholder="Nội dung CK (không dấu và ký tự đặc biệt)"
                                               value="{{ old('noi_dung_ck', $detail->code) }}">
                                    </div>
                                    <div class="form-group col-md-12" style="margin-top:10px;margin-bottom:10px">
                                        <label class="col-md-3 row">Hình ảnh </label>
                                        <div class="col-md-9">
                                            <img id="thumbnail_image"
                                                 src="{{ old('image_url') ? Helper::showImage(old('image_url', $detail->image_url)) : URL::asset('admin/dist/img/img.png') }}"
                                                 class="img-thumbnail" width="145" height="85">

                                            <input type="file" id="file-image" style="display:none"/>

                                            <button class="btn btn-default" id="btnUploadImage" type="button"><span
                                                        class="glyphicon glyphicon-upload" aria-hidden="true"></span>
                                                Upload
                                            </button>
                                        </div>
                                        <div style="clear:both"></div>
                                        <input type="hidden" name="image_url" id="image_url"
                                               value="{{ old('image_url', $detail->image_url) }}"/>
                                        <input type="hidden" name="image_name" id="image_name"
                                               value="{{ old('image_name') }}"/>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="notes">Ghi chú</label>
                                        <textarea class="form-control" name="notes" placeholder="Ghi chú" id="notes"
                                                  rows="3">{!! old('notes', $detail->notes) !!}</textarea>
                                    </div>
                                </div>


                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                                    <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm"
                                       href="{{ route('user-balance-withdraw.index')}}">Hủy</a>
                                </div>
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>
                </div>
            </form>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>

    <input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">
@stop
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#btnAddBankInfo').click(function () {
                $('#modalNewBankInfo').modal('show');
            });
            $(document).on('click', '#btnSaveBankAjax', function () {
                $(this).attr('disabled', 'disabled');
                $.ajax({
                    url: $('#formAjaxBankInfo').attr('action'),
                    data: $('#formAjaxBankInfo').serialize(),
                    type: "post",
                    success: function (id) {
                        $('#btnCloseModalTag').click();
                        $.ajax({
                            url: "{{ route('bank-info.ajax-list') }}",
                            data: {
                                id: id
                            },
                            type: "get",
                            success: function (data) {
                                $('#bank_info_id').html(data);
                                $('#bank_info_id').select2('refresh');
                            }
                        });
                    }, error: function (error) {
                        var errrorMess = jQuery.parseJSON(error.responseText);
                        if (errrorMess.message == 'The given data was invalid.') {
                            alert('Nhập đầy đủ thông tin có dấu *');
                            $('#btnSaveBankAjax').removeAttr('disabled');
                        }
                        //console.log(error);
                    }
                });
            });
            $('.tinh-toan .amount, .tinh-toan .gia').blur(function () {
                var parent = $(this).parents('.tinh-toan');
                tinhtoangia(parent);
            });
        });

        function tinhtong() {
            var tong = 0;
            $('.total').each(function () {
                var total = parseInt($(this).val());
                if (total > 0) {
                    tong += total;
                }
            });
            $('#total_money').val(tong);
        }

        function tinhtoangia(parent) {
            var amount = parent.find('.amount').val();
            var gia = parent.find('.gia').val();
            var total = gia * amount;
            parent.find('.total').val(total);
            tinhtong();
        }

        $(document).ready(function () {
            $('#btnUploadImage').click(function () {
                $('#file-image').click();
            });
            var files = "";
            $('#file-image').change(function (e) {
                $('#thumbnail_image').attr('src', "{{ URL::asset('admin/dist/img/loading.gif') }}");
                files = e.target.files;

                if (files != '') {
                    var dataForm = new FormData();
                    $.each(files, function (key, value) {
                        dataForm.append('file', value);
                    });

                    dataForm.append('date_dir', 1);
                    dataForm.append('folder', 'tmp');

                    $.ajax({
                        url: $('#route_upload_tmp_image').val(),
                        type: "POST",
                        async: false,
                        data: dataForm,
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            $('#thumbnail_image').attr('src', "{{ URL::asset('admin/dist/img/loading.gif') }}");
                        },
                        success: function (response) {
                            if (response.image_path) {
                                $('#thumbnail_image').attr('src', $('#upload_url').val() + response.image_path);
                                $('#image_url').val(response.image_path);
                                $('#image_name').val(response.image_name);
                            }
                            console.log(response.image_path);
                            //window.location.reload();
                        },
                        error: function (response) {
                            var errors = response.responseJSON;
                            for (var key in errors) {

                            }
                            //$('#btnLoading').hide();
                            //$('#btnSave').show();
                        }
                    });
                }
            });
        });
    </script>
@stop
