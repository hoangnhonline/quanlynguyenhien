@extends('layout')
@section('content')
    <style>
        .capture-container{
            margin-top: 10px;
            text-align: center;
            overflow: auto;
        }

        #capture{
            width: 600px;
            height: 600px;
            background: #f5da55;
            overflow: hidden;
            position: relative;
            margin: 0 auto;
        }

        #capture img{
            width: 100%
        }

        .item-container{
            width: 300px;
            height: 300px;
            position: absolute;
            z-index: 10;
            text-align: center;
            overflow: hidden;
        }

        .item-container .image{
            width: 300px;
            height: 300px;
            overflow: hidden;
            background-position: center;
            background-size: cover;
            display: inline-block;
        }

        .item-container .item-name{
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            position: absolute;
            z-index: 100;
            top: 190px;
            padding: 10px;
            width: 100%;
            text-shadow: 2px 5px 10px black;
            text-transform: uppercase;
        }

        .hotel-container{
            top: 0;
            left: 0;
            border-right: 2px solid #fff;
            border-bottom: 2px solid #fff;
        }

        .tour-container{
            top: 0;
            right: 0;
            border-left: 2px solid #fff;
            border-bottom: 2px solid #fff;
        }

        .set-container{
            bottom: 0;
            left: 0;
            border-right: 2px solid #fff;
            border-top: 2px solid #fff;
        }
        .price-container{
            bottom: 0;
            right: 0;
            border-left: 2px solid #fff;
            border-top: 2px solid #fff;
            background: #f5da55;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #capture .total-price{
            color: #fff;
            font-size: 48px;
            font-weight: bold;
            text-align: center;
            display: block;
            line-height: 48px;
            margin-bottom: 20px;
            margin-top: -20px;
        }

        #capture .contact{
            color: #fff;
            font-size: 22px;
            font-weight: bold;
            width: 100%;
            text-align: center;
            line-height: 30px;
        }

        #capture .contact .fa-stack{
            font-size: 15px;
            position: relative;
            top: -2px;
        }

        #capture .contact-item{
            margin-bottom: 5px;
        }

        .special-image{
            width: 120px !important;
            height: 120px;
            position: absolute;
            z-index: 10;
            top: 50%;
            left: 50%;
            margin-left: -60px;
            margin-top: -60px;
        }

        .price-notes{
            text-align: center;
            font-size: 14px;
            padding: 10px;
            line-height: 14px;
        }
    </style>
    <div class="content-wrapper">
        <!-- Main content -->

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 style="text-transform: uppercase;">
                Cập nhật combo
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('booking-combo.index') }}" style="margin-bottom:5px">Quay
                lại</a>
            <a class="btn btn-success btn-sm" href="{{ route('booking-combo.index') }}" style="margin-bottom:5px">Danh
                sách combo</a>
            <form role="form" method="POST" action="{{ route('booking-combo.update') }}" id="dataForm">
                <input type="hidden" name="id" value="{{ $detail->id }}">
                <div class="row">
                    <!-- left column -->

                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="box box-primary">

                            <div class="capture-container">
                                <div id="capture" style="">
                                    <div class="item-container hotel-container">
                                        <div class="image" style="background-image: url('/plantotravel{{!empty($detail->hotel->thumbnail) ?  $detail->hotel->thumbnail->image_url : ''}}')"></div>
                                        <div class="item-name">
                                            {{$detail->room->name}}
                                        </div>
                                    </div>
                                    <div class="item-container tour-container">
                                        <div class="image" style="background-image: url('/plantotravel{{$detail->tour->banner_url}}')"></div>
                                        <div class="item-name">
                                            {{$detail->tour->name}}
                                        </div>
                                    </div>
                                    <div class="item-container set-container">
                                        <div class="image" style="background-image: url('/plantotravel{{$detail->set->images_url}}')"></div>
                                        <div class="item-name">
                                            {{$detail->set->name}}
                                        </div>
                                    </div>
                                    <div class="item-container price-container">
                                        <div class="contact">
                                            <div class="total-price">
                                                <span style="font-size: 22px">Giá chỉ</span><br/>
                                                {{number_format($detail->total_price)}}đ
                                                @if($detail->notes)
                                                    <div class="price-notes">{{$detail->notes}}</div>
                                                @endif
                                            </div>
                                            <div class="contact-item">
                                        <span class="fa-stack fa-xs">
                                          <i class="fa fa-circle-thin fa-stack-2x"></i>
                                          <i class="fa fa-user fa-stack-1x fa-inverse"></i>
                                        </span>
                                                {{$detail->user->name}}
                                            </div>
                                            <div class="contact-item">
                                        <span class="fa-stack fa-xs">
                                          <i class="fa fa-circle-thin fa-stack-2x"></i>
                                          <i class="fa fa-phone fa-stack-1x fa-inverse"></i>
                                        </span>
                                                {{$detail->user->phone}}
                                            </div>

                                        </div>
                                    </div>
                                    <img src="/images/special-circle.png" class="special-image"/>
                                </div>
                            </div>
                            <div class="box-footer text-center">
                                <button type="button" id="capture_image" class="btn btn-primary btn-sm">Lưu ảnh</button>
                            </div>

                            <hr/>
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
                                    <div
                                        class="form-group col-xs-12 col-md-6 col-xs-12">
                                        <label>Ngày bắt đầu <span class="red-star">*</span></label>
                                        <input type="text" class="form-control datepicker" name="from_date"
                                               id="from_date"
                                               value="{{ old('from_date', date('d/m/Y', strtotime($detail->from_date))) }}"
                                               autocomplete="off">
                                    </div>
                                    <div
                                        class="form-group col-xs-12 col-md-6 col-xs-12">
                                        <label>Ngày kết thúc <span class="red-star">*</span></label>
                                        <input type="text" class="form-control datepicker" name="to_date"
                                               id="to_date"
                                               value="{{ old('to_date', date('d/m/Y', strtotime($detail->to_date))) }}"
                                               autocomplete="off">
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <label>KHÁCH SẠN <span class="red-star">*</span></label>
                                        <select class="form-control select2" name="hotel_id" id="hotel_id">
                                            <option value="">--Chọn--</option>
                                            @foreach($hotelList as $hotel)
                                                <option
                                                    value="{{ $hotel->id }}" {{ old('hotel_id', $detail->hotel_id) == $hotel->id ? "selected" : "" }}>{{ $hotel->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label>Loại phòng</label>
                                        <select class="form-control select2" name="room_id" id="room_id">
                                            <option value="">--Chọn--</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label>Giá mỗi người <span class="red-star">*</span></label>
                                        <input type="text" class="form-control number" name="room_price" id="room_price"
                                               value="{{ old('room_price', $detail->room_price) }}">
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label>TOUR <span class="red-star">*</span></label>
                                        <select class="form-control select2" name="tour_id" id="tour_id">
                                            <option value="">--Chọn--</option>
                                            @foreach($tourList as $tour)
                                                <option
                                                    value="{{ $tour->id }}"
                                                    {{ old('tour_id', $detail->tour_id) == $tour->id ? "selected" : "" }} price="{{$tour->price}}">{{ $tour->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label>Giá mỗi người <span class="red-star">*</span></label>
                                        <input type="text" class="form-control number" name="tour_price" id="tour_price"
                                               value="{{ old('tour_price', $detail->tour_price) }}">
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label> ĂN<span class="red-star">*</span></label>
                                        <select class="form-control select2" name="set_id" id="set_id">
                                            <option value="">--Chọn--</option>
                                            @foreach($restaurantSetList as $set)
                                                <option
                                                    value="{{ $set->id }}"
                                                    {{ old('set_id', $detail->set_id) == $set->id ? "selected" : "" }} price="{{$set->price}}">{{ $set->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label>Giá mỗi người <span class="red-star">*</span></label>
                                        <input type="text" class="form-control number" name="set_price" id="set_price"
                                               value="{{ old('set_price', $detail->set_price) }}">
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <label>TỔNG TIỀN <span class="red-star">*</span></label>
                                        <input type="text" class="form-control number" name="total_price"
                                               id="total_price" value="{{ old('total_price', $detail->total_price) }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Trạng thái <span class="red-star">*</span></label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>
                                            Mới
                                        </option>
                                        <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>
                                            Hoàn tất
                                        </option>
                                        <option value="3" {{ old('status', $detail->status) == 3 ? "selected" : "" }}>
                                            Hủy
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Ghi chú</label>
                                    <textarea class="form-control" rows="4" name="notes"
                                              id="notes">{{ old('notes', $detail->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                            <a class="btn btn-defaulD btn-sm" class="btn btn-primary btn-sm"
                               href="{{ route('booking-combo.index')}}">Hủy</a>
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
        $(document).on('click', '#btnSave', function () {
            if (parseInt($('#tien_coc').val()) > 0 && $('#nguoi_thu_coc').val() == '') {
                alert('Bạn chưa chọn người thu cọc');
                return false;
            }
        });

        $(document).ready(function () {
            var rooms = [];
            var roomId = "{{old('room_id', $detail->room_id)}}";
            var inited = false;
            $('#hotel_id').on('change', function () {
                var id = $('#hotel_id').val();
                var url = "{{route('booking-combo.getHotelRooms')}}?id=" + id;
                $.ajax(url).success(function (response) {
                    rooms = response;
                    $('#room_id').html('');
                    $('#room_id').append('<option value="">--Chọn--</option>');
                    response.forEach(function (item) {
                        $('#room_id').append('<option value="' + item.id + '"' + (roomId == item.id ? ' selected' : '') + '">' + item.name + '</option>');
                    })
                    $('#room_id').select2();
                    if (!inited) {
                        $('#room_id').val(roomId).trigger('change');
                    }
                })
            })
            if ($('#hotel_id').val()) {
                $('#hotel_id').trigger('change');
            } else {
                inited = true;
            }

            $('#room_id').on('change', function () {
                var id = $('#room_id').val();
                var price = 0;
                rooms.forEach(function (room) {
                    if (room.id == id) {
                        price = room.price;
                    }
                })
                $('#room_price').val(price).trigger('change')
                setPrice()
            })

            $('#tour_id').on('change', function () {
                var id = $('#tour_id').val();
                var date = $('#from_date').val();
                if (!date) {
                    date = "{{date('d/m/Y')}}";
                }
                var url = "{{route('booking-combo.calculatePrice')}}?tour_id=" + id + '&from_date=' + date;
                $.ajax(url).success(function (response) {
                    $('#tour_price').val(response.tour_price).trigger('change')
                    setPrice()
                })
            })

            $('#set_id').on('change', function () {
                var id = $('#set_id').val();
                var price = 0;
                $('#set_id').find('option').each(function () {
                    if ($(this).attr('value') == id) {
                        price = $(this).attr('price');
                    }
                })
                $('#set_price').val(price).trigger('change');
                setPrice()
            })
        });

        function setPrice() {
            var tourPrice = $('#tour_price').val().replace(',', '') || 0;
            var roomPrice = $('#room_price').val().replace(',', '') || 0;
            var setPrice = $('#set_price').val().replace(',', '') || 0;
            var total_price = parseInt(tourPrice) + parseInt(roomPrice) + parseInt(setPrice);
            $('#total_price').val(total_price);
        }

        $('#capture_image').click(function(e){
            e.preventDefault();
            html2canvas(document.querySelector("#capture"), {
                allowTaint : false,
                useCORS: true,
                imageTimeout: 5000,
            }).then(canvas => {
                var MIME_TYPE = "image/png";
                var fileName = "booking_combo.png";

                var imgURL = canvas.toDataURL(MIME_TYPE);

                var dlLink = document.createElement('a');
                dlLink.download = fileName;
                dlLink.href = imgURL;
                dlLink.dataset.downloadurl = [MIME_TYPE, dlLink.download, dlLink.href].join(':');

                document.body.appendChild(dlLink);
                dlLink.click();
                document.body.removeChild(dlLink);
            });
        })
        resize();
    </script>
@stop
