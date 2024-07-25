@extends('layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Khách hàng
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route( 'dashboard' ) }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('customer.index') }}">Khách hàng</a></li>
            <li class="active">Tạo mới</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <a class="btn btn-default btn-sm" href="{{ route('customer.index') }}"
            style="margin-bottom:5px">Quay lại</a>
        <form role="form" method="POST" action="{{ route('customer.store') }}" id="dataForm">
            <div class="row">
                <!-- left column -->

                <div class="col-md-12">
                    <div id="content_alert"></div>
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            Tạo mới
                        </div>
                        <!-- /.box-header -->
                        {!! csrf_field() !!}

                        <div class="box-body">
                            @if(Session::has('message'))
                            <p class="alert alert-info">{{ Session::get('message') }}</p>
                            @endif
                            @if (count($errors) > 0)
                            <div class="alerts alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <div class="form-group" >
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="ask_more" id="ask_more" value="1" {{ old('ask_more') == 1 ? "checked" : "" }}>
                                        <span style="color:red; font-weight: bold">Tôi muốn biết thêm...</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Booking liên quan</label>
                                <select class="form-control select2" id="related" multiple="multiple" name="related_id[]" >

                                    @foreach($arrBooking as $booking)
                                        <option value="{{ $booking->id }}">{{ Helper::showCode($booking) }} - {{ $booking->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-4" style="display: none">
                                    <label for="city_id">Thị trường quan tâm</label>
                                    <select class="form-control select2" name="city_id" id="city_id">
                                      <option value="">--Chọn--</option>
                                      @foreach($cityList as $city)
                                      <option value="{{ $city->id }}" {{ old('city_id', $city_id) == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                                      @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="name">Họ tên <span class="red-star">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="contact_date">Ngày liên hệ</label>
                                    <br>
                                    <input type="text" class="form-control-2 datepicker" id="contact_date" name="contact_date" value="{{ old('contact_date', date('d/m/Y')) }}" style="width: 120px" autocomplete="off">
                                    <select class="form-control-2 select2" name="contact_date_hour" style="width: 90px">
                                        <option value="">Giờ</option>
                                        @for($g = 0; $g < 24; $g++)
                                        <option value="{{ str_pad($g, 2, "0", STR_PAD_LEFT) }}" {{ $g == old('contact_date_hour', date('H', time())) ? "selected" : "" }}>{{ str_pad($g, 2, "0", STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                    <select class="form-control-2 select2" name="contact_date_minute" style="width: 90px">
                                        <option value="">Phút</option>
                                        @for($i = 0; $i < 60; $i++)
                                        <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $i == old('contact_date_minute', date('i', time())) ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group col-xs-4">
                                    <div class="checkbox" style="padding-top: 20px;">
                                        <label>
                                            <input type="checkbox" name="ads" id="ads" value="1" {{ old('ads') ? "checked" : "" }}>
                                            <span style="color:red; font-weight: bold">ADS</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-xs-4" style="display: none">
                                  <div class="checkbox">
                                    <label style="margin-top:25px;">
                                      <input type="checkbox" name="is_send" value="1" {{ old('is_send') == 1 ? "checked" : "" }}>
                                      <span style="color:red; font-weight: bold;">GỬI TƯ VẤN</span>
                                    </label>
                                  </div>
                                </div>
                            </div>
                            <style>
                                #ads_campaign_container .select2-container{
                                    width: 100% !important;
                                }
                            </style>
                            <div class="row" style="{{old('ads') ? '' : 'display:none'}}" id="ads_campaign_container">
                                <div class="form-group col-xs-12">
                                    <label for="ads_campaign_id">Chiến dịch quảng cáo</label>
                                    <div style="width: 100%">
                                        <select class="form-control select2" name="ads_campaign_id" id="ads_campaign_id">
                                            <option value="">-- Chọn chiến dịch --</option>
                                            @foreach($adsCampaigns as $campaign)
                                                <option
                                                    value="{{$campaign->id}}" {{ old('ads_campaign_id') == $campaign->id ? "selected" : "" }}>{{$campaign->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-4">
                                    <label for="address">Địa chỉ</label>
                                    <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}" autocomplete="off" placeholder="Địa chỉ khách hàng">
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="phone">Số điện thoại <span class="red-star">*</span></label>
                                    <input type="text" maxlength="10" class="form-control" name="phone" id="phone" value="{{ old('name') }}">
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="phone">Số ĐT dự phòng</label>
                                    <input type="text" maxlength="10" class="form-control" name="phone_2" id="phone_2" value="{{ old('phone_2') }}">
                                </div>
                            </div>
                            <div class="row">
                                <input type="hidden" name="status" value="1" id="status">
                                <div class="form-group col-xs-4">
                                    <label for="status">Nguồn</label>
                                    <select class="form-control select2" name="source" id="source">
                                        <option value="">-- Chọn nguồn --</option>
                                        @foreach($sources as $source)
                                            <option
                                                value="{{$source->id}}" {{ old('source', 1) == $source->id ? "selected" : "" }}>{{$source->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="status">Nguồn 2</label>
                                    <select class="form-control select2" name="source2" id="source2">
                                        <option value="">-- Chọn nguồn 2 --</option>
                                        @foreach($sources2 as $source2)
                                            <optgroup label="{{$source2->name}}" data-group="{{$source2->id}}">
                                                @foreach($source2->childs as $source)
                                                    <option
                                                        value="{{$source->id}}" {{old('source2', 7) == $source->id ? "selected" : "" }} {{$source2->id != old('source', 1) ? 'disabled=disabled' : ''}}>{{$source->name}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="facebook">Link Facebook</label>
                                    <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-6">
                                    <label for="status">Loại sản phẩm</label>
                                    <select class="form-control select2" name="product_type" id="product_type">
                                        <option value="">-- Chọn --</option>
                                        @php
                                            $types = ['1' => 'Tour', '2' => 'Combo']
                                        @endphp
                                        @foreach($types as $key => $type)
                                            <option
                                                value="{{$key}}" {{ old('source', null) == $key ? "selected" : "" }}>{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xs-6">
                                    <label for="status">Sản phẩm</label>
                                    <select class="form-control select2" name="product_id" id="product_id">
                                        <option value="">-- Chọn --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                              <div class="form-group col-xs-4">
                                  <label>NL <span class="red-star">*</span></label>
                                  <select class="form-control select2" name="adults" id="adults">
                                    @for($i = 0; $i <= 150; $i++)
                                    <option value="{{ $i }}" {{ old('adults') == $i ? "selected" : "" }}>{{ $i }}</option>
                                    @endfor
                                  </select>
                              </div>
                              <div class="form-group col-xs-4">
                                  <label>TE(< 1.4) <span class="red-star">*</span></label>
                                  <select class="form-control select2" name="childs" id="childs">
                                    @for($i = 0; $i <= 20; $i++)
                                    <option value="{{ $i }}" {{ old('childs') == $i ? "selected" : "" }}>{{ $i }}</option>
                                    @endfor
                                  </select>
                              </div>
                              <div class="form-group col-xs-4">
                                  <label>EB(< 1m)</label>
                                  <select class="form-control select2" name="infants" id="infants">
                                    @for($i = 0; $i <= 20; $i++)
                                    <option value="{{ $i }}" {{ old('infants') == $i ? "selected" : "" }}>{{ $i }}</option>
                                    @endfor
                                  </select>
                              </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-xs-4">
                                    <label for="zalo">Zalo</label>
                                    <input type="text" class="form-control" name="zalo" id="zalo" value="{{ old('zalo') }}">
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">
                                </div>
                                <div class="form-group col-xs-4">
                                    <label for="email">Ngày sinh</label>
                                    <input type="text" class="form-control datepicker" name="birthday" id="birthday" value="{{ old('birthday') }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-12">
                                  <label>Nhu cầu khách hàng</label>
                                  <textarea class="form-control ckeditor" rows="4" name="demand" id="demand">{{ old('demand') }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-12">
                                  <label>Ghi chú</label>
                                  <textarea class="form-control" rows="4" name="notes" id="notes">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                            <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm"
                                href="{{ route('customer.index')}}">Hủy</a>
                        </div>

                    </div>
                    <!-- /.box -->

                </div>
        </form>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#ads').click(function () {
                if ($(this).is(':checked')) {
                    $('#ads_campaign_container').show();
                } else {
                    $('#ads_campaign_container').hide();
                }
            });

            $('#product_type').change(function (){
                var type = $(this).val();
                $.ajax({
                    url: '{{ route('customer.get-product') }}',
                    type: 'GET',
                    data: {type: type},
                    success: function (data) {
                        $('#product_id').html('');
                        $('#product_id').append('<option value="">-- Chọn --</option>');
                        $.each(data, function (key, value) {
                            $('#product_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            })

            $('#source').change(function (){
                var val = $(this).val();
                $('#source2').find('optgroup option').prop('disabled', true);
                $('#source2').find('optgroup[data-group="'+ val +'"] option').prop('disabled', false);
                $('#source2').val('').select2()
            })
        });
    </script>
@endsection
