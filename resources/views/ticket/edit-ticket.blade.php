@extends('layout')
@section('content')

<div class="content-wrapper">
  <!-- Main content -->
  <!-- Content Header (Page header) -->
  <section class="content-header" style="padding-top: 10px;">
  <h1 style="text-transform: uppercase;">  
      Cập nhật book vé
    </h1>    
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('ticket.manage') }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('ticket.manage') }}" style="margin-bottom:5px">Xem danh sách booking</a>
    <form role="form" method="POST" action="{{ route('ticket.update') }}" id="dataForm">
      <input type="hidden" name="id" value="{{ $detail->id }}">
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
              <div>
                @foreach($detail->payment as $p)
                  @if($p->type == 1)
                  <img src="{{ Helper::showImageNew($p->image_url)}}" width="80" style="border: 1px solid red" class="img-unc" >
                  @else
                  <br>+ {{$p->notes}}
                  @endif
                  @endforeach

                   
              </div>            
              <input type="hidden" name="type" value="3">
              <div class="form-group">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control" name="status" id="status">                        
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
                <div class="row">
                  <div class="form-group col-xs-6" >
                      <label>TỔNG TIỀN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price', $detail->total_price) }}">
                  </div>
                  <div class="form-group col-xs-6">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        <option value="1" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == 1 ? "selected" : "" }}>Sales</option>
                        <option value="2" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == 2 ? "selected" : "" }}>CTY</option>
                        <option value="3" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == 3 ? "selected" : "" }}>Đại lý</option>
                      </select>
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
              <a class="btn btn-defaulD btn-sm" class="btn btn-primary btn-sm" href="{{ route('ticket.manage')}}">Hủy</a>
            </div>
            
        </div>
        <!-- /.box -->     

      </div>
      
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<style type="text/css">
  #div_search_fast{
    display: none;
  }
</style>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('.room_price, .room_amount, #tien_coc').change(function(){     
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