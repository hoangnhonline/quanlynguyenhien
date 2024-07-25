@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Giá tour : {{ $detail->name }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('tour-system.index', ['city_id' => $detail->city_id]) }}">Tour</a></li>
      <li class="active">Thêm mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('tour-system.index', ['city_id' => $detail->city_id]) }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('tour-system.store-price') }}" id="dataForm" class="productForm">    
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Thêm mới</h3>
          </div>
          <!-- /.box-header -->               
            {!! csrf_field() !!}          
            <div class="box-body">
              <input type="hidden" name="city_id" value="{{ $detail->city_id }}">
              <input type="hidden" name="tour_id" value="{{ $detail->id }}">
                @if (count($errors) > 0)
                  <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                  </div>
                @endif
                <p style="font-weight: bold; color: red;font-size: 15px;text-transform: uppercase;">Ghi chú : Nếu giá 1 ngày thì không cần nhập "Đến ngày" </p>
                <p>
                
                </p>
                <div class="row" style="margin-bottom: 5px">
                  <div class="form-group col-xs-6">               
                      <label>Đối tác <span class="red-star">*</span></label>
                      <select name="partner_id" class="form-control select2" id="partner_id">
                        <option value="">--Chọn--</option>
                        @foreach($partnerList as $partner)
                        <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? "selected" : "" }}>{{ $partner->name }}</option>
                        @endforeach
                      </select>                    
                  </div>
                  <div class="form-group col-xs-6">                  
                    <label>Hình thức <span class="red-star">*</span></label>
                    <select class="form-control select2" id="tour_type" name="tour_type">                      
                        <option value="1" {{ old('tour_type') == 1 ? "selected" : "" }}>Tour ghép</option>
                        <option value="2" {{ old('tour_type') == 2 ? "selected" : "" }}>Tour VIP</option>
                        <option value="3" {{ old('tour_type') == 3 ? "selected" : "" }}>Thuê cano</option>
                    </select>
                  </div>
                </div>
                
                  <?php 
                  $mocshow = 2;
                  for($i = 1; $i <= 10; $i++){                 
                    if(old('location_id.'.($i-1)) > 0){
                      $mocshow = $i;
                    }
                  }
                  ?>    
                  <div class="mb10" style="padding: 15px;">
                  @for($i = 1; $i <= 10; $i++)       
                  <?php 
                  $key = $i-1;
                  ?>
                  <div class="row mb10 mt10 row-dia-diem {{ $i > $mocshow ? "dia-diem-hidden" : "" }}"  style="background-color: #f7e4c3;padding: 10px">           
                      <div class=" col-md-3 form-group" >                  
                        <label>Từ ngày</label>
                        <input type="text" class="form-control req datepicker" autocomplete="off" name="from_date[]" id="from_date_{{ $i }}" value="{{ old('from_date.'.$key) }}">
                      </div> 
                      <div class=" col-md-3 form-group" >                  
                        <label>Đến ngày</label>
                        <input type="text" class="form-control req datepicker" autocomplete="off" name="to_date[]" id="to_date_{{ $i }}" value="{{ old('to_date.'.$key) }}" placeholder="">
                      </div> 
                      <div class=" col-md-3 form-group" >                  
                        <label>Giá cost NL</label>
                        <input type="text" class="form-control req number" name="adult_cost[]" id="adult_cost_{{ $i }}" value="{{ old('adult_cost.'.$key) }}" autocomplete="off">
                      </div>
                      <div class="col-md-3 form-group" >                  
                        <label>Giá cost TE</label>
                        <input type="text" class="form-control req number" name="child_cost[]" id="child_cost_{{ $i }}" value="{{ old('child_cost.'.$key) }}" autocomplete="off">
                      </div>
                  </div> 
                  @endfor
                  <div class="row">
                   <div class="">
                     <button type="button" class="btn btn-warning" id="btnAddLocation"><i class="fa fa-plus"></i> Thêm mốc thời gian</button>
                   </div>
                 </div> 
                
            </div>
            <div class="box-footer">              
              <button type="button" class="btn btn-default" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i></button>
              <input type="hidden" name="is_new" id="is_new" value="0">
              <button type="submit" class="btn btn-primary" id="btnSave">Lưu</button>
              
              <a class="btn btn-default" class="btn btn-primary" href="{{ route('tour-system.index')}}">Hủy</a>
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
  $(document).ready(function(){   
    $('#btnAddLocation').click(function(){
      $('.dia-diem-hidden:first').removeClass('dia-diem-hidden');
      $('.select2').select2();
    });
  });
</script>
@stop