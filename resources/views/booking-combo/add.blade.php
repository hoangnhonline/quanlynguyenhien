@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Main content -->


        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 style="text-transform: uppercase;">
                Tạo combo
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('booking-combo.index') }}" style="margin-bottom:5px">Quay
                lại</a>
            <a class="btn btn-success btn-sm" href="{{ route('booking-combo.index') }}" style="margin-bottom:5px">Danh
                sách combo</a>
            <form role="form" method="POST" action="{{ route('booking-combo.store') }}" id="dataForm">
                <div class="row">
                    <!-- left column -->

                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="box box-primary">

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
                                               id="from_date" value="{{ old('from_date') }}" autocomplete="off">
                                    </div>
                                    <div
                                        class="form-group col-xs-12 col-md-6 col-xs-12">
                                        <label>Ngày kết thúc <span class="red-star">*</span></label>
                                        <input type="text" class="form-control datepicker" name="to_date"
                                               id="to_date" value="{{ old('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-xs-4">
                                        <label>KHÁCH SẠN <span class="red-star">*</span></label>
                                        <select class="form-control select2" name="hotel_id" id="hotel_id">
                                            <option value="">--Chọn--</option>
                                            @foreach($hotelList as $hotel)
                                                <option
                                                    value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? "selected" : "" }}>{{ $hotel->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label>Loại phòng</label>
                                        <select class="form-control select2" name="room_id" id="room_id">
                                            <option value="">--Chọn--</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label>Giá mỗi người <span class="red-star">*</span></label>
                                        <input type="text" class="form-control number" name="room_price" id="room_price"
                                               value="{{ old('room_price') }}">
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-xs-6">
                                        <label>TOUR <span class="red-star">*</span></label>
                                        <select class="form-control select2" name="tour_id" id="tour_id">
                                            <option value="">--Chọn--</option>
                                            @foreach($tourList as $tour)
                                                <option
                                                    value="{{ $tour->id }}" {{ old('tour_id') == $tour->id ? "selected" : "" }} price="{{$tour->price}}">{{ $tour->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-6">
                                        <label>Giá mỗi người <span class="red-star">*</span></label>
                                        <input type="text" class="form-control number" name="tour_price" id="tour_price"
                                               value="{{ old('tour_price') }}">
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-xs-6">
                                        <label> ĂN<span class="red-star">*</span></label>
                                        <select class="form-control select2" name="set_id" id="set_id">
                                            <option value="">--Chọn--</option>
                                            @foreach($restaurantSetList as $set)
                                                <option
                                                    value="{{ $set->id }}" {{ old('set_id') == $set->id ? "selected" : "" }} price="{{$set->price}}">{{ $set->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-6">
                                        <label>Giá mỗi người <span class="red-star">*</span></label>
                                        <input type="text" class="form-control number" name="set_price" id="set_price"
                                               value="{{ old('set_price') }}">
                                    </div>
                                </div>
                                    <hr/>
                                <div class="row">
                                    <div class="form-group col-xs-12">
                                        <label>TỔNG TIỀN <span class="red-star">*</span></label>
                                        <input type="text" class="form-control number" name="total_price"
                                               id="total_price" value="{{ old('total_price') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Trạng thái <span class="red-star">*</span></label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="1" {{ old('status') == 1 ? "selected" : "" }}>Mới</option>
                                        <option value="2" {{ old('status') == 2 ? "selected" : "" }}>Hoàn tất</option>
                                        <option value="3" {{ old('status') == 3 ? "selected" : "" }}>Hủy</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Ghi chú</label>
                                    <textarea class="form-control" rows="4" name="notes"
                                              id="notes">{{ old('notes') }}</textarea>
                                </div>

                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" id="btnSave" class="btn btn-primary btn-sm">Lưu</button>
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
            var roomId = "{{old('room_id')}}";
            var inited = false;
            $('#hotel_id').on('change', function () {
                var id = $('#hotel_id').val();
                var url = "{{route('booking-combo.getHotelRooms')}}?id=" + id;
                $.ajax(url).success(function (response) {
                    rooms = response;
                    $('#room_id').html('');
                    $('#room_id').append('<option value="">--Chọn--</option>');
                    response.forEach(function (item) {
                        $('#room_id').append('<option value="' + item.id + '"' + (roomId == item.id ? ' selected' : '')  +'">'+ item.name +'</option>');
                    })
                    $('#room_id').select2();
                    if(!inited){
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
                rooms.forEach(function(room){
                    if(room.id == id){
                        price = room.price;
                    }
                })
                $('#room_price').val(price).trigger('change')
                setPrice()
            })

            $('#tour_id').on('change', function () {
                var id = $('#tour_id').val();
                var date = $('#from_date').val();
                if(!date){
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
                $('#set_id').find('option').each(function(){
                    if($(this).attr('value') == id){
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
    </script>
@stop
