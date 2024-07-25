@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Chi phí
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('cost.index') }}">Chi phí</a></li>
      <li class="active">Cập nhật</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('cost.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('cost.update') }}" id="dataForm">
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
              <input type="hidden" name="type" id="type" value="1">
              <div class="row">
              <div class="form-group col-md-4">
                @php
                    if($detail->use_date){
                        $use_date = old('use_date', date('d/m/Y', strtotime($detail->use_date)));
                    }else{
                        $use_date = old('use_date');
                    }
                  @endphp
                <label for="email">Ngày</label>
                 <input type="text" name="date_use" class="form-control datepicker" value="{{ old('date_use', $use_date) }}" autocomplete="off">
                </div>
              <div class="form-group col-md-4">                    
                  <label>Loại chi phí<span class="red-star">*</span></label>
                  <select class="form-control select2" id="cate_id" name="cate_id">     
                      <option value="">--Chọn--</option>      
                      @foreach($cateList as $cate)
                      <option value="{{ $cate->id }}" {{ old('cate_id') == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                      @endforeach
                  </select>
                </div>   
                <div class="form-group col-md-4">
                <label for="email">PTT CODE</label>
                 <input type="text" name="booking_id" class="form-control" value="{{ old('booking_id') }}" autocomplete="off">
                </div>
              </div>
               <!--  <div class="form-group">
                <label for="email">Nội dung</label>
                 <input type="text" name="content" class="form-control" value="{{ old('content' ) }}" autocomplete="off">
                </div> -->
                
                <div class="row tinh-toan" >
                  <div class="form-group col-md-4">
                    <label for="amount">Số lượng</label>
                    <input type="text" name="amount" class="form-control amount" placeholder="Số lượng" value="{{ old('amount', $detail->amount) }}">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="price">Giá</label>
                    <input type="text" name="price" class="form-control number gia" placeholder="Giá" value="{{ old('price', $detail->price) }}">
                  </div>                  
                  <div class="form-group col-md-4">
                    <label for="total_money">Tổng tiền</label>
                    <input type="text" name="total_money" class="form-control number total" placeholder="Tổng tiền" value="{{ old('total_money', $detail->total_money) }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="notes">Ghi chú</label>
                  <textarea class="form-control" name="notes" placeholder="Ghi chú" id="notes">{!! old('notes', $detail->notes) !!}</textarea>
                </div>
          
                     
            
            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('cost.index')}}">Hủy</a>
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