@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Giá phòng : {{ $detail->name }} - ({{ $detail->hotel->name }})
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('room.index', ['hotel_id' => $detail->hotel_id]) }}">Loại phòng</a></li>
      <li class="active">Thêm mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('room.index', ['hotel_id' => $detail->hotel_id]) }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('room.store-price') }}" id="dataForm" class="productForm">    
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
              <input type="hidden" name="hotel_id" value="{{ $detail->hotel->id }}">
              <input type="hidden" name="room_id" value="{{ $detail->id }}">
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
                
                @foreach($partnerHavePrice as $partner_id)
                @if(isset($partnerArr[$partner_id]))
                <button class="btn btn-sm  @if($partner_id == $partner_id_select) btn-default @else btn-info @endif @if($partner_id != $partner_id_select) btnSelectPartner @endif" type="button" data-value="{{ $partner_id }}">{{ $partnerArr[$partner_id] }}</button>
                @endif
                @endforeach
                
                </p>
                <div class="row" style="margin-bottom: 5px">
                  <div class="col-md-12">                    
                      <label>Đối tác</label>
                      <select name="partner_id" class="form-control select2" id="partner_id">
                        <option value="1" {{ $partner_id_select == 1 ? "selected" : "" }}>Trực tiếp KS</option>
                        @foreach($partnerList as $partner)
                        <option {{ $partner->id == $partner_id_select ? "selected" : "" }} value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                      </select>                    
                  </div>
                </div>
                @php $total = $priceList->count(); @endphp
                @if($total > 0)
                @php $i = 0; @endphp
                @foreach($priceList as $price)
                @php 
                    $fromDate = date('d/m/Y', strtotime($price->from_date));
                    $toDate = $price->from_date == $price->to_date ? '' :  date('d/m/Y', strtotime($price->to_date));
                @endphp
                
                <div class="row">                                 
                    <div class=" col-md-3 form-group" >                  
                      <label>Từ ngày</label>
                      <input type="text" class="form-control req datepicker" autocomplete="off" name="from_date[]" id="from_date_{{ $i }}" value="{{ old('from_date.'.$i, $fromDate) }}">
                    </div> 
                    <div class=" col-md-3 form-group" >                  
                      <label>Đến ngày</label>
                      <input type="text" class="form-control req datepicker" autocomplete="off" name="to_date[]" id="to_date_{{ $i }}" value="{{ old('to_date.'.$i, $toDate) }}">
                    </div> 
                    <div class=" col-md-3 form-group" >                  
                      <label>Giá</label>
                      <input type="text" class="form-control req number" name="price_goc[]" id="price_goc_{{ $i }}" value="{{ old('price_goc.'.$i, $price->price_goc) }}" autocomplete="off">
                    </div>
                    <div class=" col-md-3 form-group" >                  
                      <label>Giá bán</label>
                      <input type="text" class="form-control req number" name="price[]" id="price_{{ $i }}" value="{{ old('price.'.$i, $price->price) }}" autocomplete="off">
                    </div>
                </div> 
                @endforeach
                @endif
                @if($dateDefault->count() > 0 && $priceList->count() == 0)
                  @php $i = 0; $total = $dateDefault->count(); @endphp
                  @foreach($dateDefault as $row)
                  @php $i++; @endphp
                    <div class="row">                               
                      <div class=" col-md-3 form-group" >                  
                        <label>Từ ngày</label>
                        <input type="text" class="form-control req datepicker" autocomplete="off" name="from_date[]" id="from_date_{{ $i }}" value="{{ old('from_date.'.$i, date('d/m/Y', strtotime($row->from_date))) }}">
                      </div> 
                      <div class=" col-md-3 form-group" >                  
                        <label>Đến ngày</label>
                        <input type="text" class="form-control req datepicker" autocomplete="off" name="to_date[]" id="to_date_{{ $i }}" value="{{ old('to_date.'.$i, date('d/m/Y', strtotime($row->to_date))) }}" placeholder="">
                      </div> 
                      <div class=" col-md-3 form-group" >                  
                        <label>Giá</label>
                        <input type="text" class="form-control req number" name="price_goc[]" id="price_goc_{{ $i }}" value="{{ old('price_goc.'.$i, number_format($row->price_goc)) }}">
                      </div>
                      <div class=" col-md-3 form-group" >                  
                        <label>Giá bán</label>
                        <input type="text" class="form-control req number" name="price[]" id="price_{{ $i }}" value="{{ old('price.'.$i, number_format($row->price)) }}">
                      </div>
                  </div>
                  @endforeach

                  @for($i = $total ; $i < 10 - $total; $i++)
                  <div class="row">                                     
                      <div class=" col-md-3 form-group" >                  
                        <label>Từ ngày</label>
                        <input type="text" class="form-control req datepicker" autocomplete="off" name="from_date[]" id="from_date_{{ $i }}" value="{{ old('from_date.'.$i) }}">
                      </div> 
                      <div class=" col-md-3 form-group" >                  
                        <label>Đến ngày</label>
                        <input type="text" class="form-control req datepicker" autocomplete="off" name="to_date[]" id="to_date_{{ $i }}" value="{{ old('to_date.'.$i) }}" placeholder="">
                      </div> 
                      <div class=" col-md-3 form-group" >                  
                        <label>Giá</label>
                        <input type="text" class="form-control req number" name="price_goc[]" id="price_goc_{{ $i }}" value="{{ old('price_goc.'.$i) }}">
                      </div>
                      <div class=" col-md-3 form-group" >                  
                        <label>Giá bán</label>
                        <input type="text" class="form-control req number" name="price[]" id="price_{{ $i }}" value="{{ old('price.'.$i) }}">
                      </div>
                  </div> 
                  @endfor
                @else
                
                  @for($i = $total ; $i < 10 - $total; $i++)
                  <div class="row">                                     
                      <div class=" col-md-3 form-group" >                  
                        <label>Từ ngày</label>
                        <input type="text" class="form-control req datepicker" autocomplete="off" name="from_date[]" id="from_date_{{ $i }}" value="{{ old('from_date.'.$i) }}">
                      </div> 
                      <div class=" col-md-3 form-group" >                  
                        <label>Đến ngày</label>
                        <input type="text" class="form-control req datepicker" autocomplete="off" name="to_date[]" id="to_date_{{ $i }}" value="{{ old('to_date.'.$i) }}" placeholder="">
                      </div> 
                      <div class=" col-md-3 form-group" >                  
                        <label>Giá</label>
                        <input type="text" class="form-control req number" name="price_goc[]" id="price_goc_{{ $i }}" value="{{ old('price_goc.'.$i) }}">
                      </div>
                      <div class=" col-md-3 form-group" >                  
                        <label>Giá bán</label>
                        <input type="text" class="form-control req number" name="price[]" id="price_{{ $i }}" value="{{ old('price.'.$i) }}">
                      </div>
                  </div> 
                  @endfor
                @endif
                <div class="row" style="margin-bottom: 5px">
                  <div class="col-md-12">                    
                      <label>Copy giá cho đối tác: </label>
                      <select name="partner_id_new" class="form-control select2" id="partner_id_new">
                        <option value="0">--Không copy--</option>
                        <option value="1">Trực tiếp KS</option>
                        @foreach($partnerList as $partner)
                        <option {{ $partner->id == old('partner_id_new') ? "selected" : "" }} value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                      </select>                    
                  </div>
                </div>
            </div>
            <div class="box-footer">              
              <button type="button" class="btn btn-default" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i></button>
              <input type="hidden" name="is_new" id="is_new" value="0">
              <button type="submit" class="btn btn-primary" id="btnSave">Lưu</button>
              
              <a class="btn btn-default" class="btn btn-primary" href="{{ route('room.index')}}">Hủy</a>
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
    $('.btnSelectPartner').click(function(){
      if(confirm('Bạn muốn nhập giá đối tác "' + $(this).text() +'"') ){        
        window.location.href = "{{ URL::current() }}?partner_id=" + $(this).data('value');
      }
    });
    $('#partner_id').change(function(){
      window.location.href = "{{ URL::current() }}?partner_id=" + $('#partner_id').val();
    });
  });
</script>
@stop