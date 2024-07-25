@extends('layout')
@section('content')
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      Đặt Xe : cập nhật <span style="color: red">PTX{{ $detail->id }}</span>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking-car.index') }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking-car.index') }}" style="margin-bottom:5px">Xem danh sách booking</a>
    <a href="{{ route('drivers.create') }}" class="btn btn-primary btn-sm" style="margin-bottom:5px">Thêm tài xế</a>

    <form role="form" method="POST" action="{{ route('booking-car.update') }}" id="dataForm">
      <input type="hidden" name="id" value="{{ $detail->id }}">
      <input type="hidden" name="tour_cate" value="1">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <div id="content_alert"></div>
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
              <input type="hidden" name="type" value="4">
              <div class="row">

                <div class="form-group col-md-6" >
                   <label>Tài xế</label>
                  <select class="form-control select2" id="driver_id" name="driver_id">
                    <option value="">--Chọn--</option>
                      @foreach($driverList as $driver)
                      <option value="{{ $driver->id }}" {{ old('driver_id', $detail->driver_id) == $driver->id  ? "selected" : "" }}>{{ $driver->name }}
                        @if($driver->is_verify == 1)
                        - HĐ
                        @endif
                      </option>
                      @endforeach
                  </select>
                </div>

                <div class="form-group col-md-6">
                   <label>Trạng thái <span class="red-star">*</span></label>
                    <select class="form-control" name="status" id="status">
                      <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>Mới</option>
                      <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Hoàn tất</option>
                      <!-- <option value="4" {{ old('status', $detail->status) == 4 ? "selected" : "" }}>Dời ngày</option> -->
                      <option value="3" {{ old('status', $detail->status) == 3 ? "selected" : "" }}>Hủy</option>
                    </select>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label>Loại xe<span class="red-star">*</span></label>
                  <select class="form-control select2" id="car_cate_id" name="car_cate_id">
                    <option value="">--Chọn--</option>
                      @foreach($carCate as $cate)
                      <option value="{{ $cate->id }}" {{ old('car_cate_id', $detail->car_cate_id) == $cate->id  ? "selected" : "" }}>{{ $cate->name }}</option>
                      @endforeach
                  </select>
                </div>
                @php
                    if($detail->use_date){
                        $use_date = old('use_date', date('d/m/Y', strtotime($detail->use_date)));
                    }else{
                        $use_date = old('use_date');
                    }
                  @endphp
                <div class="form-group col-md-4" >
                    <label>Ngày đi <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ $use_date }}" autocomplete="off">
                  </div>
                  <div class="form-group col-md-4" >
                    <label>Giờ đi <span class="red-star">*</span></label>
                    <br>
                    <select class="form-control-2 select2" name="don_gio" id="don_gio" style="width: 120px">
                        <option value="">Giờ</option>
                        @for($g = 1; $g <= 24; $g++)
                        <option value="{{ str_pad($g,2,"0", STR_PAD_LEFT) }}" {{ old('don_gio', $don_gio) == $g  ? "selected" : "" }}>{{ str_pad($g,2,"0", STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                    <select class="form-control-2 select2" name="don_phut" id="don_phut" style="width: 120px">
                        <option value="">Phút</option>
                        @for($g = 0; $g <= 59; $g++)
                        <option value="{{ str_pad($g,2,"0", STR_PAD_LEFT) }}" {{ old('don_phut', $don_phut) == $g  ? "selected" : "" }}>{{ str_pad($g,2,"0", STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                    @if($timeOld)
                    <span style="color: red">({{ $detail->time_pickup }})</span>
                    @endif
                  </div>
                </div>

              <div class="row">
                <div class="form-group col-md-4 col-xs-4">
                  <label>Tên KH <span class="red-star">*</span></label>
                  <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $detail->name) }}">
                </div>
                <div class="form-group col-md-4 col-xs-4">
                  <label>Điện thoại <span class="red-star">*</span></label>
                  <input type="text" maxlength="20" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                </div>
                <div class="form-group col-md-4 col-xs-4">
                  <label>Điện thoại 2</label>
                  <input type="text"  maxlength="20" class="form-control" name="phone_1" id="phone_1" value="{{ old('phone_1', $detail->phone_1) }}">
                </div>
                </div>
                <div style="background-color: #f7e4c3; padding:10px">
                <?php
                $mocshow = 2;
                for($i = 1; $i <= 10; $i++){
                  $locationSelected = isset($locationArr[$i-1]) ? $locationArr[$i-1] : null;
                  if(old('location_id.'.($i-1), $locationSelected) > 0){
                    $mocshow = $i;
                  }
                }
                ?>
                @for($d = 1; $d <= 10; $d++)
                <?php
                $locationSelected = isset($locationArr[$d-1]) ? $locationArr[$d-1] : null;
                 ?>
                <div class="row row-dia-diem mb10 {{ $d > $mocshow ? "dia-diem-hidden" : "" }}" >

                  <div class="input-group col-md-12" style="padding-left: 15px; padding-right: 15px">
                  <label>Điểm {{ $d }} <span class="red-star">*</span></label>

                  <select class="form-control select2 location" name="location_id[]" id="location_id_{{ $d }}">
                    <option value="">--Chọn--</option>
                    @foreach($listTag as $location)
                    <option value="{{ $location->id }}" {{ old('location_id.'.($d-1), $locationSelected) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                    @endforeach
                  </select>
                  <span class="input-group-btn">
                    <button style="margin-top:24px" class="btn btn-primary btn-sm btnAddTag" type="button" data-set="location_id_{{ $d }}">
                      Thêm
                    </button>
                  </span>
                </div>
              </div>
                @endfor
                <div class="row">
                   <div class="col-md-12">
                     <button type="button" class="btn btn-warning" id="btnAddLocation"><i class="fa fa-plus"></i> Thêm điểm</button>
                   </div>
                 </div>
                </div>

                <div class="row">
                  <div class="form-group col-xs-4">
                      <label>Người lớn <span class="red-star">*</span></label>
                      <select class="form-control select2" name="adults" id="adults">
                        @for($i = 1; $i <= 100; $i++)
                        <option value="{{ $i }}" {{ old('adults', $detail->adults) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Trẻ em<span class="red-star">*</span></label>
                      <select class="form-control" name="childs" id="childs">
                        @for($i = 0; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('childs', $detail->childs) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-4">
                      <label>Em bé</label>
                      <select class="form-control" name="infants" id="infants">
                        @for($i = 0; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ old('infants', $detail->infants) == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                </div>
                <input type="hidden" name="total_price_adult">
                <input type="hidden" name="total_price_child">
                <input type="hidden" name="meals">
                <input type="hidden" name="ngay_coc">
                <input type="hidden" name="extra_fee">
                <input type="hidden" name="discount">
                <input type="hidden" name="danh_sach">
                <div class="row">
                    <div class="form-group col-xs-6">
                        <label>TỔNG TIỀN <span class="red-star">*</span></label>
                      <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price', $detail->total_price) }}">
                    </div>
                    <div class="form-group col-md-6 col-xs-6">
                        <label>Tiền thực thu<span class="red-star">*</span></label>
                      <input style="border: 1px solid red" type="text" class="form-control number" name="tien_thuc_thu" id="tien_thuc_thu" value="{{ old('tien_thuc_thu', $detail->tien_thuc_thu) }}">
                    </div>

                  </div>
                  <div class="row">
                  <div class="form-group col-xs-6">
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc', $detail->tien_coc) }}">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_coc', $detail->nguoi_thu_coc) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>



                  <div class="form-group col-xs-6">
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" style="border: 1px solid red" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai', $detail->con_lai) }}">
                  </div>

                  <div class="form-group col-xs-6">
                      <label>Người thu $ <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                        <option value="{{ $col->id }}" {{ old('nguoi_thu_tien', $detail->nguoi_thu_tien) == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                  </div>
                </div>
                <div class="row">
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only || Auth::user()->id == 23)
                  <div class="input-group" style="padding-left: 15px; padding-right: 15px">
                     <label>Sales <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="">--Chọn--</option>
                        @foreach($listUser as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $detail->user_id) == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                        @endforeach
                      </select>
                      <span class="input-group-btn">
                        <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddSales" type="button" data-value="3">
                          Thêm
                        </button>
                      </span>
                  </div>
                  @else
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  @endif
                  <input type="hidden" name="book_date" value="">
                </div>


                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="6" name="notes" id="notes">{{ old('notes', $detail->notes) }}</textarea>
                </div>
            </div>

            <div class="box-footer">
              <button type="button" class="btn btn-default btn-sm" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i> Đang xử lý...</button>
              <button type="submit" id="btnSave" onclick="return checkLocation();" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking-car.index', ['type' => 4])}}">Hủy</a>
            </div>
        </div>
        <!-- /.box -->
      </div>
    </div>
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<div id="tagTag" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    <form method="POST" action="{{ route('location.ajax-save')}}" class="formTag">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tạo mới điểm đón</h4>
      </div>
      <div class="modal-body" id="contentTag">
          <input type="hidden" name="type" value="4">
          <input type="" name="data_set" id="data_set" value="">
           <!-- text input -->
          <div class="col-md-12">
            <div class="form-group">
              <label>Tên địa điểm<span class="red-star">*</span></label>
              <input type="text" class="form-control" id="add_address" value="{{ old('address') }}" name="str_tag"></textarea>
            </div>
            <input type="hidden" name="city_id" value="{{ $city_id }}">
          </div>
          <div classs="clearfix"></div>
      </div>
      <div style="clear:both"></div>
      <div class="modal-footer" style="text-align:center">
        <button type="button" class="btn btn-primary btn-sm btnSaveTagAjax" id="btnSaveTagAjax" data-set=""> Save</button>
        <button type="button" class="btn btn-default btn-sm btnCloseModalTag"  data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>

  </div>
</div>
<div id="modalSales" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    <form method="POST" action="{{ route('account.ajax-save')}}" id="formAjaxSales">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tạo mới sales</h4>
      </div>
      <div class="modal-body" id="contentTag">
          <input type="hidden" name="type" value="4">
           <!-- text input -->
          <div class="col-md-12">
            <div class="form-group">
              <label>Tên sales<span class="red-star">*</span></label>
              <input type="text" class="form-control" id="add_name" value="{{ old('add_name') }}" name="add_name">
            </div>
            <div class="form-group">
              <label>Số điện thoại<span class="red-star">*</span></label>
              <input type="text" class="form-control" id="add_phone" value="{{ old('add_phone') }}" name="add_phone">
            </div>
          </div>
          <div classs="clearfix"></div>
      </div>
      <div style="clear:both"></div>
      <div class="modal-footer" style="text-align:center">
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveSalesAjax"> Save</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="btnCloseModalSales">Close</button>
      </div>
      </form>
    </div>

  </div>
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
  $(document).on('click', '#btnSaveSalesAjax', function(){
  $(this).attr('disabled', 'disabled');
    $.ajax({
      url : $('#formAjaxSales').attr('action'),
      data: $('#formAjaxSales').serialize(),
      type : "post",
      success : function(sales_id){
        $('#btnCloseModalSales').click();
        $.ajax({
          url : "{{ route('account.ajax-list') }}",
          data: {
            sales_id : sales_id
          },
          type : "get",
          success : function(data){
              $('#user_id').html(data);
              $('#user_id').select2('refresh');

          }
        });
      }
    });
 });
$(document).on('click', '#btnSaveTagAjax', function(){
  $(this).attr('disabled', 'disabled');
    $.ajax({
      url : $('#formAjaxTag').attr('action'),
      data: $('#formAjaxTag').serialize(),
      type : "post",
      success : function(str_id){
        $('#btnCloseModalTag').click();
        $.ajax({
          url : "{{ route('location.ajax-list') }}",
          data: {
            str_id : str_id
          },
          type : "get",
          success : function(data){
              $('#location_id').html(data);
              $('#location_id').select2('refresh');

          }
        });
      }
    });
 });
$(document).on('click', '#btnSaveTagAjax2', function(){
  $(this).attr('disabled', 'disabled');
    $.ajax({
      url : $('#formAjaxTag2').attr('action'),
      data: $('#formAjaxTag2').serialize(),
      type : "post",
      success : function(str_id){
        $('#btnCloseModalTag2').click();
        $.ajax({
          url : "{{ route('location.ajax-list') }}",
          data: {
            str_id : str_id
          },
          type : "get",
          success : function(data){
              $('#location_id_2').html(data);
              $('#location_id_2').select2('refresh');

          }
        });
      }
    });
 });
$(document).on('click', '#btnSaveTagAjax', function(){
     saveTagAjax($(this).data('set'));
     $(this).attr('data-set', '');
   });
function saveTagAjax(obj_set){
  var form = $('.formTag');
  $.ajax({
      url : form.attr('action'),
      data: form.serialize(),
      type : "post",
      success : function(str_id){
        $('.btnCloseModalTag').click();
        $.ajax({
          url : "{{ route('location.ajax-list') }}",
          data: {
            str_id : str_id
          },
          type : "get",
          success : function(data){

              var obj_set = $('#' + $('#data_set').val());
              obj_set.html(data);
              obj_set.select2('refresh');

          }
        });
      }
    });
}
function checkLocation(){
  var count = 0;
  $('.location').each(function(){
    if($(this).val() > 0){
      count++;
    }
  });
  if(count < 2){
    alert('Chọn ít nhất 2 điểm đến!');
    return false;
  }
}
  $(document).ready(function(){
     $('#btnAddLocation').click(function(){
      $('.dia-diem-hidden:first').removeClass('dia-diem-hidden');
      $('.select2').select2();
    });
    $('#btnAddSales').click(function(){
          $('#modalSales').modal('show');
      });
    $('#dataForm').submit(function(){
      $('#btnSave').hide();
      $('#btnLoading').show();
    });
    $('#btnAddTag').click(function(){
          $('#tagTag').modal('show');
      });
    $('#btnAddTag2').click(function(){
          $('#tagTag2').modal('show');
      });

    $('#tien_coc').blur(function(){
      setPrice();
    });

  });
  function setPrice(){
    priceGhep();
  }
  function priceGhep(){
      //tien_coc
      var tien_coc = 0;
      if($('#tien_coc').val() != ''){
       tien_coc = parseInt($('#tien_coc').val());
      }

      var total_price = 0;
      if($('#total_price').val() != ''){
       total_price = parseInt($('#total_price').val());
      }
      $('#total_price').val(total_price);

      $('#con_lai').val(total_price - tien_coc);

  }
</script>
@stop
