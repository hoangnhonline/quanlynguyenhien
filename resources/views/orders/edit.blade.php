@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Hóa đơn
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('orders.index') }}">Hóa đơn</a></li>
      <li class="active">Cập nhật</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('orders.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('orders.update') }}" id="dataForm">
      <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
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
              <?php 
              $date_use = date('d/m/Y', strtotime($detail->date_use));
              ?>
              <div class="row">       
                <div class="form-group col-md-4">
                <label for="email">&nbsp;&nbsp;&nbsp;Ngày</label>
                 <input type="text" name="date_use" class="form-control datepicker" value="{{ old('date_use',$date_use ) }}" autocomplete="off">
              </div> 
                <div class="form-group col-md-4" >
                  
                  <label>Bàn số<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="table_no" id="table_no" value="{{ old('table_no', $detail->table_no) }}">
                </div>
                <div class="form-group col-md-4" >                  
                  <label>Giờ vào<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="time_in" id="price" value="{{ old('time_in', $detail->time_in) }}">
                </div> 
                </div>
                
                <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                  <label class="col-md-3 row">Ảnh</label>    
                  <div class="col-md-9">
                    <img id="thumbnail_image" src="{{ $detail->image_url ? Helper::showImage($detail->image_url) : asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="145" height="85">                 
                    <button class="btn btn-default btn-sm btnSingleUpload" data-set="image_url" data-image="thumbnail_image" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                  </div>
                  <div style="clear:both"></div>
                </div>
                <input type="hidden" name="image_url" id="image_url" value="{{ old('image_url', $detail->image_url) }}"/> 
                <div class="form-group">
                  <textarea class="form-control" name="notes" placeholder="Ghi chú" id="notes">{!! old('notes', $detail->notes) !!}</textarea>
                </div>
                <div style="clear:both"></div> 
                @php
                $i = 0;
                @endphp 
                @foreach($detail->details as $orderDetail)
                <div class="row tinh-toan"  data-count="{{ $i }}">
                  <div class="form-group col-md-5">
                    <select class="form-control select2 food" name="food_id[]">
                      <option value="">--</option>
                      @foreach($foodList as $food)
                      <option value="{{ $food->id }}" data-price="{{ $food->price }}" {{ $food->id ==  old('food_id.'.$i, $orderDetail->food_id) ? "selected" : "" }}>{{ $food->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <input type="text" name="amount[]" class="form-control amount" placeholder="Số lượng" value="{{ old('amount.'.$i, $orderDetail->amount) }}">
                  </div>
                  <div class="form-group col-md-2">
                    <input type="text" name="price[]" class="form-control number gia" placeholder="Giá" value="{{ old('price.'.$i, $orderDetail->price) }}">
                  </div>
                  
                  <div class="form-group col-md-3">
                    <input type="text" name="total[]" class="form-control number total" placeholder="Thành tiền" value="{{ old('total.'.$i, $orderDetail->total) }}">
                  </div>
                </div>
                @php
                $i++;
                @endphp
                @endforeach            
                @for($j = $i; $j < 15; $j++)
                <div class="row tinh-toan" data-count="{{ $j }}">
                  <div class="form-group col-md-5">
                    <select class="form-control select2 food" name="food_id[]">
                      <option value="">--</option>
                      @foreach($foodList as $food)
                      <option value="{{ $food->id }}" data-price="{{ $food->price }}" {{ $food->id ==  old('food_id.'.$j) ? "selected" : "" }}>{{ $food->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <input type="text" name="amount[]" class="form-control amount" placeholder="Số lượng" value="{{ old('amount.'.$j, 1) }}">
                  </div>
                  <div class="form-group col-md-2">
                    <input type="text" name="price[]" class="form-control number gia" placeholder="Giá" value="{{ old('price.'.$j) }}">
                  </div>
                  
                  <div class="form-group col-md-3">
                    <input type="text" name="total[]" class="form-control number total" placeholder="Thành tiền" value="{{ old('total.'.$j) }}">
                  </div>
                </div>
                @endfor
                <div class="row">                  
                  <div class="form-group col-md-3" >                  
                  <label>Tổng tiền<span class="red-star">*</span></label>
                  <input type="text" class="form-control number total_money" name="total_money" id="total_money" value="{{ old('total_money', $detail->total_money) }}">
                </div>
                <div class="form-group col-md-3" >                  
                  <label>% giảm<span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="percent_discount" id="percent_discount" value="{{ old('percent_discount', $detail->percent_discount) }}">
                </div>
                <div class="form-group col-md-3" >                  
                  <label>Số tiền giảm<span class="red-star">*</span></label>
                  <input type="text" class="form-control number" name="discount" id="discount" value="{{ old('discount', $detail->discount) }}">
                </div> 
                <div class="form-group col-md-3" >                  
                  <label>Còn lại<span class="red-star">*</span></label>
                  <input type="text" class="form-control number" name="actual_amount" id="actual_amount" value="{{ old('actual_amount', $detail->actual_amount) }}">
                </div> 
                 
            </div>
            <div class="form-group" >                  
                  <label>Sales<span class="red-star">*</span></label>
                  <select class="form-control select2" name="sales_id" id="sales_id">
                    <option value="">--Chọn--</option>
                    @foreach($listUser as $u)
                    <option value="{{ $u->id }}" {{ $u->id == old('sales_id', $detail->sales_id) ?  "selected" : "" }}>{{ $u->name }}</option>
                    @endforeach
                  </select>
                </div>
            </div>          
            
            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('orders.index')}}">Hủy</a>
            </div>            
        </div>
        <!-- /.box -->     

      </div>
      <div class="col-md-7">
             
    </div>
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){    
    $('.tinh-toan .amount, .tinh-toan .gia').blur(function(){      
      var parent = $(this).parents('.tinh-toan');
      tinhtoangia(parent);
    });
    $('.tinh-toan .food').change(function(){
      var parent = $(this).parents('.tinh-toan');     
      var price = $(this).find(':selected').data('price');
      parent.find('.gia').val(price);
      tinhtoangia(parent);
    });
  });
  function tinhtong(){
    var tong = 0;
    $('.total').each(function(){
      var total = parseInt($(this).val());
      if(total > 0){
        tong += total;
      }
    });
    $('#total_money').val(tong);
  }
  function tinhtoangia(parent){ 
      var amount = parent.find('.amount').val();
      var gia = parent.find('.gia').val();
      var total = gia*amount;
      parent.find('.total').val(total);
      tinhtong();
  }
</script>
@stop