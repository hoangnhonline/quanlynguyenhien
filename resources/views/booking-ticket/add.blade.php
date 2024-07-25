@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Main content -->


  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      Đặt vé tham quan
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking-ticket.index') }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking-ticket.index') }}" style="margin-bottom:5px">Danh sách booking</a>
    <form role="form" method="POST" action="{{ route('booking-ticket.store') }}" id="dataForm">
      <input type="hidden" name="city_id" value="{{ $city_id }}">
      <input type="hidden" name="customer_id" value="{{!empty($customerId) ? $customerId : ''}}">
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
                    <div class="form-group col-xs-6">
                      <label>Tên khách hàng <span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="name" id="name" value="{{ old('name', !empty($customer) ? $customer->name : '') }}">
                    </div>
                   <div class="form-group col-xs-6" >
                      <label>Điện thoại <span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', !empty($customer) ? $customer->phone : '') }}">
                    </div>

                </div>
                <div class="row">
                  <div class="form-group col-xs-6" >
                    <label>Ngày giao <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ old('use_date') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6">
                    <label>Nơi giao</label>
                    <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}">
                  </div>
                </div>


                <?php
                $mocshow = 1;
                for($i = 1; $i <= 10; $i++){
                  if(old('ticket_type_id.'.($i-1)) > 0){
                    $mocshow = $i;
                  }
                }
                ?>
                <div class="mb10 mt15" >
                @for($k = 1; $k <= 10; $k++)

                <div class="mb15 rooms-row row-dia-diem {{ $k > $mocshow ? "dia-diem-hidden" : "" }}" style="background-color: #e6e6e6; padding:10px;border-radius: 5px">
                <div class="row">
                  <div class="form-group col-xs-12 col-md-4">
                      <label>Loại vé</label>
                      <select class="form-control select2 ticket_type" name="ticket_type_id[]" id="ticket_type_id{{ ($k-1) }}">
                        <option value="">--Chọn--</option>
                        @foreach($cateList as $hotel)
                        <option data-price="{{ number_format($hotel->price) }}" value="{{ $hotel->id }}" {{ old('ticket_type_id.'.($k-1)) == $hotel->id  ? "selected" : "" }}>{{ $hotel->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-xs-12 col-md-2" >
                      <label>Số lượng</label>
                      <select class="form-control room_amount select2" name="amount[]" id="amount{{ ($k-1) }}">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 100; $i++)
                        <option value="{{ $i }}" {{ old('amount.'.($k-1)) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-6 col-md-3">
                        <label>Giá gốc 1 vé</label>
                      <input type="text" name="price[]" id="price{{ ($k-1) }}" class="form-control number price" value="{{ old('price.'.($k-1)) }}">
                    </div>
                  <div class="form-group col-xs-6 col-md-3">
                      <label>Giá bán</label>
                      <input type="text" name="price_sell[]" id="price_sell{{ ($k-1) }}" class="form-control number room_price" value="{{ old('price_sell.'.($k-1)) }}">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-xs-6 col-md-6" >
                      <label>Tổng tiền</label>
                      <input type="text" name="total[]" id="total{{ ($k-1) }}" class="form-control number room_price_total" value="{{ old('total.'.($k-1)) }}">
                  </div>

                  <div class="form-group col-xs-6 col-md-6" >
                      <label>Tiền lãi</label>
                      <input type="text" name="commission[]" id="commission{{ ($k-1) }}" class="form-control number commission" value="{{ old('commission.'.($k-1)) }}" placeholder="">
                  </div>
                </div>
              </div>
                @endfor
                 <div class="row">
                   <div class="col-md-12">
                     <button type="button" class="btn btn-warning" id="btnAddLocation"><i class="fa fa-plus"></i> Thêm loại vé</button>
                   </div>
                 </div>
                </div><!--div bao-->
                <div class="row">
                  <div class="form-group col-xs-12">
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price') }}">
                  </div>
                  <div class="form-group col-xs-6">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc') }}">
                  </div>
                  <div class="form-group col-xs-6">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_coc') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>

                </div>
                <div class="row">
                  <div class="form-group col-xs-6" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai') }}">
                  </div>
                   <div class="form-group col-xs-6">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_tien') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                </div>
                <div class="row">
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <div class="form-group col-md-4 col-xs-12" >
                     <label>Sales <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="0">--Chọn--</option>
                        @foreach($listUser as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $user_id_default) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  @else
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  @endif
                  @if($ctvList->count() > 0)
                  <div class="form-group col-xs-12 col-md-4 ">
                     <label>Người book </label>
                      <select class="form-control select2" name="ctv_id" id="ctv_id">
                        <option value="">--Chọn--</option>
                        @foreach($ctvList as $ctv)
                        <option value="{{ $ctv->id }}" {{ old('ctv_id') == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  @endif
                    <div class="form-group @if(Auth::user()->role == 1 && !Auth::user()->view_only) col-xs-12 col-md-4 @else col-xs-12  @endif" >
                  <label>Ngày đặt</label>
                  <input type="text" class="form-control datepicker" name="book_date" id="book_date" value="{{ old('book_date') }}" autocomplete="off">
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
                  <textarea class="form-control" rows="4" name="notes" id="notes" >{{ old('notes') }}</textarea>
                </div>

                </div>
            </div>

            <div class="box-footer">
              <button type="submit" id="btnSave" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-defaulD btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking-ticket.index')}}">Hủy</a>
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
  $(document).on('click','#btnSave', function(){

    if(parseInt($('#tien_coc').val()) > 0 && $('#nguoi_thu_coc').val() == ''){
      alert('Bạn chưa chọn người thu cọc');
      return false;
    }
  });
  $(document).ready(function(){
    $('#btnAddLocation').click(function(){
      $('.dia-diem-hidden:first').removeClass('dia-diem-hidden');
      $('.select2').select2();
    });
    $('.room_price, .room_amount, #tien_coc').change(function(){
      setPrice();
    });
    $('.room_price, .room_amount, #tien_coc').blur(function(){
      setPrice();
    });
    $('.ticket_type').change(function(){
      var price = $(this).parents('.rooms-row').find('.ticket_type option:selected').data('price');
      $(this).parents('.rooms-row').find('.price').val(price);
      setPrice();
    });
  });
  function setPrice(){
    var total_price = 0;
    $('.rooms-row').each(function(){
      var row = $(this);
      var room_amount = parseInt(row.find('.room_amount').val());
      var room_price = parseInt(row.find('.room_price').val());
      var price = parseInt(row.find('.price').val());
      console.log(room_amount, room_price);
      if(room_amount > 0 && room_price > 0){
        var room_price_total = room_amount*room_price;
        row.find('.room_price_total').val(room_price_total);
        total_price += room_price_total;
        var room_price_old = room_amount*price;
        row.find('.commission').val(room_price_total-room_price_old);
      }

    });
    console.log(total_price);

    //tien_coc
    var tien_coc = 0;
    if($('#tien_coc').val() != ''){
     tien_coc = parseInt($('#tien_coc').val());
    }
    total_price = total_price;
    console.log('total_price: ', total_price);
    $('#total_price').val(total_price);

    $('#con_lai').val(total_price - tien_coc);
  }
</script>
@stop
