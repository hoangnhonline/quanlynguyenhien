@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Main content -->

  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      Cập nhật đặt vé
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking-ticket.index') }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking-ticket.index') }}" style="margin-bottom:5px">Danh sách booking</a>
    <form role="form" method="POST" action="{{ route('booking-ticket.update') }}" id="dataForm">
      <input type="hidden" name="id" value="{{ $detail->id }}">
      <input type="hidden" name="city_id" value="{{ $city_id }}">
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
              @if($detail->payment->count() > 0)
                <fieldset class="scheduler-border">
                  <legend class="scheduler-border">THANH TOÁN</legend>

                      <table class="table table-bordered table-responsive" style="margin-bottom: 0px;">
                        @foreach($detail->payment as $p)
                        <tr>

                         <td>
                            @if($p->type == 1)
                            <img src="{{ Helper::showImageNew(str_replace('uploads/', '', $p->image_url))}}" width="80" style="border: 1px solid red" class="img-unc" >
                            @else
                            @if($p->notes)
                            + {{$p->notes}}<br>
                            @endif
                            @if($p->sms)
                            + {{$p->sms}}
                            @endif
                            @endif
                          </td>

                        </tr>
                         @endforeach
                      </table>

              </fieldset>
              @endif
              <input type="hidden" name="type" value="3">

                <div class="form-group">
                  <label>Trạng thái <span class="red-star">*</span></label>
                   <select class="form-control select2" name="status" id="status">
                     <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>Mới</option>
                     <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Hoàn tất</option>
                     <option value="3" {{ old('status', $detail->status) == 3 ? "selected" : "" }}>Hủy</option>
                   </select>
                </div>


                <div class="row">
                    <div class="form-group col-xs-6">
                      <label>Tên khách hàng <span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $detail->name) }}">
                    </div>
                   <div class="form-group col-xs-6"  >
                      <label>Điện thoại <span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                    </div>

                </div>
                @php
                    if($detail->use_date){
                        $use_date = old('use_date', date('d/m/Y', strtotime($detail->use_date)));
                    }else{
                        $use_date = old('use_date');
                    }
                  @endphp
                <div class="row">
                  <div class="form-group col-xs-6" >
                    <label>Ngày giao <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ $use_date }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6">
                    <label>Nơi giao</label>
                    <input type="text" class="form-control" name="address" id="address" value="{{ old('address', $detail->address) }}">
                  </div>
                </div>


                <div class="mt15">
                                    @if($detail->source != 'website')
                <?php
                $mocshow = 2;
                for($i = 1; $i <= 10; $i++){
                  $locationSelected = isset($ticketIdArr[$i-1]) ? $ticketIdArr[$i-1] : null;
                  if(old('ticket_type_id.'.($i-1), $locationSelected) > 0){
                    $mocshow = $i;
                  }
                }
                ?>
                @for($k = 1; $k <= 10; $k++)
                <?php
                $locationSelected = isset($ticketIdArr[$k-1]) ? $ticketIdArr[$k-1] : null;
                 ?>
                @php
                $ticket_type_id = $amount = $price = $price_sell = $total = $commission = null;
                $key = $k-1;
                if(isset($ticketArr[$key])){
                  $ticket_type_id = $ticketArr[$key]->ticket_type_id;
                  $amount = $ticketArr[$key]->amount;
                  $price = $ticketArr[$key]->price;
                  $price_sell = $ticketArr[$key]->price_sell;
                  $total = $ticketArr[$key]->total;
                  $commission = $ticketArr[$key]->commission;
                }

                @endphp

                <div class="rooms-row row-dia-diem mb10 {{ $k > $mocshow ? "dia-diem-hidden" : "" }}" style="background-color: #e6e6e6; padding:10px;border-radius: 5px">
                <div class="row">
                  <div class="form-group col-xs-12 col-md-4">
                      <label>Loại vé</label>
                      <select class="form-control select2 ticket_type" name="ticket_type_id[]" id="ticket_type_id{{ $key }}">
                        <option value="">--Chọn--</option>
                        @foreach($ticketType as $hotel)
                        <option data-price="{{ number_format($hotel->price) }}" value="{{ $hotel->id }}" {{ old('ticket_type_id.'.$key, $ticket_type_id) == $hotel->id  ? "selected" : "" }}>{{ $hotel->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-xs-12 col-md-2" >
                      <label>Số lượng</label>
                      <select class="form-control room_amount select2" name="amount[]" id="amount{{ $key }}">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 100; $i++)
                        <option value="{{ $i }}" {{ old('amount.'.$key, $amount) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-6 col-md-3" >
                        <label>Giá gốc 1 vé</label>
                      <input type="text" name="price[]" id="price{{ $key }}" class="form-control number price" value="{{ old('price.'.$key, $price) }}">
                    </div>
                  <div class="form-group col-xs-6 col-md-3" >
                      <label>Giá bán</label>
                      <input type="text" name="price_sell[]" id="price_sell{{ $key }}" class="form-control number room_price" value="{{ old('price_sell.'.$key, $price_sell) }}">
                  </div>

                </div>
                <div class="row">
                    <div class="form-group col-xs-6 col-md-6" >
                        <label>Tổng tiền</label>
                        <input type="text" name="total[]" id="total{{ $key }}" class="form-control number room_price_total" value="{{ old('total.'.$key, $total) }}">
                    </div>
                    <div class="form-group col-xs-6 col-md-6" >
                        <label>Tiền lãi</label>
                        <input style="border: 1px solid green" type="text" name="commission[]" id="commission{{ $key }}" class="form-control number commission" value="{{ old('commission.'.$key, $commission) }}" placeholder="">
                    </div>
                </div>
              </div>
                @endfor
	            <div class="row">
	               <div class="col-md-12">
	                 <button type="button" class="btn btn-warning" id="btnAddLocation"><i class="fa fa-plus"></i> Thêm loại vé</button>
	               </div>
	             </div>
             @else
		        @foreach($detail->webTickets as $ticket)
		            <div
		                class="rooms-row row-dia-diem mb10"
		                style="background-color: #e6e6e6; padding:10px;border-radius: 5px">
		                <div class="row">
		                    <div class="form-group col-xs-12">
		                        <label>{{$ticket->ticketType->ticketCate->name}}</label> <span class="label label-danger">Website</span>
		                        <br/>
		                        {{$ticket->ticketType->name}}
		                    </div>
		                </div>
		                <div class="row">
		                    <div class="form-group col-xs-12">
		                        <label>Số lượng</label><br/>
		                        NL: {{$ticket->adults}} x {{ number_format($ticket->total_price_adult) }}<br/>
		                        TE: {{$ticket->childs}} x {{ number_format($ticket->total_price_child) }}<br/>
		                        Tổng tiền: {{ number_format($ticket->total_amount) }}
		                    </div>
		                </div>
		            </div>
		        @endforeach
		    @endif
                <div class="row">
                   <div class="form-group col-xs-12" >
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price', $detail->total_price) }}">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc', $detail->tien_coc) }}">
                  </div>

                  <div class="form-group col-xs-6">
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_coc', $detail->nguoi_thu_coc) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                </div>
                <div class="row">
                 <div class="form-group col-xs-4" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" style="border: 1px solid red" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai', $detail->con_lai) }}">
                  </div>
                  <div class="form-group col-xs-4" >
                      <label>THỰC THU<span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="tien_thuc_thu" id="tien_thuc_thu" value="{{ old('tien_thuc_thu', $detail->tien_thuc_thu) }}" style="border: 1px solid red">
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Người thu tiền<span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
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
                        <option value="{{ $user->id }}" {{ old('user_id', $detail->user_id) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  @else
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  @endif

                  @if($ctvList->count() > 0)
                  <div class="form-group col-xs-12 col-md-4">
                     <label>Người book </label>
                      <select class="form-control select2" name="ctv_id" id="ctv_id">
                        <option value="">--Chọn--</option>
                        @foreach($ctvList as $ctv)
                        <option value="{{ $ctv->id }}" {{ old('ctv_id', $detail->ctv_id) == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                        @endforeach
                      </select>
                  </div>
                  @endif
                  @php
                    if($detail->book_date){
                        $book_date = old('book_date', date('d/m/Y', strtotime($detail->book_date)));
                    }else{
                        $book_date = old('book_date');
                    }
                  @endphp
                    <div class="form-group @if(Auth::user()->role == 1 && !Auth::user()->view_only) col-xs-12 col-md-4 @else col-xs-12 @endif" >
                  <label>Ngày đặt</label>
                  <input type="text" class="form-control datepicker" name="book_date" id="book_date" value="{{ $book_date }}" autocomplete="off">
              </div>
                </div>



                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="4" name="notes" id="notes" >{{ old('notes', $detail->notes) }}</textarea>
                </div>

                </div>
            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
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
