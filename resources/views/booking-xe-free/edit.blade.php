@extends('layout')
@section('content')
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      Xe miễn phí <span style="color: red">PTT{{ $detailBooking->id }}: {{ $detailBooking->name }} - {{ $detailBooking->phone }}</span></span>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking-xe-free.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('booking-xe-free.update') }}" id="dataForm">
  		<input type="hidden" name="id" value="{{ $detail->id }}">
      <input type="hidden" name="booking_id" value="{{ $detailBooking->id }}">

    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <div id="content_alert"></div>
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-body">
              <div class="table-responsive">
          <table class="table table-bordered" id="table_report" style="margin-bottom:0px;font-size: 14px;">
              <tr style="background-color: #ffff99">
                <th>Ngày giờ</th>
                <th>Loại xe</th>
                <th>Nơi đón</th>
                <th>Nơi trả</th>
                <th>Tài xế</th>
                <th>Ghi chú</th>
                <th class="text-right">Chi phí</th>
              </tr>
              @foreach($bkList as $bk)
              <tr>
                <td>{{ $bk->use_time }} {{ date('d/m/Y', strtotime($bk->use_date)) }} </td>
                <td>
                  @if($bk->car_cate_id)
                    {{ $bk->carCate->name }}
                  @endif
                </td>
                <td>
                  @if($bk->location)
                  {{ $bk->location->name }}
                  @endif
                </td>
                <td>
                  @if($bk->location_id_2)
                    {{ $bk->location2->name }}
                  @endif
                </td>
                <td>
                  @if($bk->driver_id)
                    {{ $bk->driver->name }}
                  @endif
                </td>
                <td>
                  {{ $bk->notes }}
                </td>
                <td class="text-right">
                  {{ number_format($bk->cost) }}
                </td>
              </tr>
              @endforeach
          </table>
        </div>
            </div>
        </div>

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
                <p class="col-md-12" style="color: red; font-style: italic;; font-weight: bold;">Đặt xe trong ngày vui lòng thông báo cho số hotline Phu Quoc Trans : 0911380111 sau khi tạo để được hỗ trợ tốt nhất.</p>
                @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                <div class="form-group col-md-12" >
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
                <div class="form-group col-md-6" >
                   <label>Tiền xe</label>
                   <input type="text" maxlength="15" autocomplete="off" class="form-control number" name="cost" id="cost" value="{{ $detail->cost }}">
                  </div>
                @endif


                <div class="form-group @if($userRole == 1) col-md-6 @else col-md-12 @endif" >
                   <label>Trạng thái</label>
                    <select class="form-control select2" name="status" id="status">
                      <option value="">--Trạng thái--</option>
                      <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>Mới</option>
                      <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Hoàn tất</option>
                      <option value="3" {{ old('status', $detail->status) == 3 ? "selected" : "" }}>Hủy</option>
                    </select>
                  </div>
                <div class="form-group col-md-4">
                  <label>Loại xe<span class="red-star">*</span></label>
                  <select class="form-control select2" id="car_cate_id" name="car_cate_id">
                    <option value="">--Chọn--</option>
                      @foreach($cateList as $cate)
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
                $tmpTime = explode(':', $detail->use_time);
                $don_gio = $tmpTime[0];
                $don_phut = $tmpTime[1];
              @endphp
                <div class="form-group col-md-8" >
                    <label>Ngày đi <span class="red-star">*</span></label>
                    <br>
                    <input type="text" class="form-control-2 datepicker ngay-don-tien" name="use_date" id="use_date" value="{{ $use_date }}" autocomplete="off">
                    <select class="form-control-2 select2" name="don_gio" id="don_gio" style="width: 120px">
                        <option value="">Giờ</option>
                        @for($g = 1; $g <= 24; $g++)
                        <option value="{{ str_pad($g,2,"0", STR_PAD_LEFT) }}" {{ old('don_gio', $don_gio) == $g  ? "selected" : "" }}>{{ str_pad($g,2,"0", STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                    <select class="form-control-2 select2" name="don_phut" id="don_phut" style="width: 120px">
                        <option value="">Phút</option>
                        <option value="00" {{ old('don_phut', $don_phut) == 0  ? "selected" : "" }}>00</option>
                        <option value="15" {{ old('don_phut', $don_phut) == 15  ? "selected" : "" }}>15</option>
                        <option value="30" {{ old('don_phut', $don_phut) == 30  ? "selected" : "" }}>30</option>
                        <option value="45" {{ old('don_phut', $don_phut) == 45  ? "selected" : "" }}>45</option>
                    </select>
                  </div>

                </div>


                <div class="row" style="margin-top: 10px;">
                  <div class="input-group col-md-12" style="padding-left: 15px; padding-right: 15px; margin-bottom: 10px;">
                  <label>Nơi đón <span class="red-star">*</span></label>

                  <select class="form-control select2" name="location_id" id="location_id">
                    <option value="">--Chọn--</option>
                    @foreach($listTag as $location)
                    <option value="{{ $location->id }}" {{ old('location_id', $detail->location_id) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                    @endforeach
                  </select>
                  <span class="input-group-btn">
                    <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddTag" type="button" data-value="3">
                      Thêm
                    </button>
                  </span>
                </div>
                <div class="input-group col-md-12" style="padding-left: 15px; padding-right: 15px; margin-bottom: 10px">
                  <label>Nơi trả <span class="red-star">*</span></label>

                  <select class="form-control select2" name="location_id_2" id="location_id_2">
                    <option value="">--Chọn--</option>
                    @foreach($listTag as $location)
                    <option value="{{ $location->id }}" {{ old('location_id_2', $detail->location_id_2) == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                    @endforeach
                  </select>
                  <span class="input-group-btn">
                    <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddTag2" type="button" data-value="3">
                      Thêm
                    </button>
                  </span>
                </div>
                </div>
                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="6" name="notes" id="notes">{{ old('notes', $detail->notes) }}</textarea>
                </div>
            </div>

            <div class="box-footer">
              <button type="button" class="btn btn-default btn-sm" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i> Đang xử lý...</button>
              <button type="submit" id="btnSave" class="btn btn-primary btn-sm" onclick="return checkToday();">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking-xe-free.create', ['booking_id' => $detail->booking_id])}}">Hủy</a>
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
    <form method="POST" action="{{ route('location.ajax-save')}}" id="formAjaxTag">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tạo mới điểm đón</h4>
      </div>
      <div class="modal-body" id="contentTag">
          <input type="hidden" name="type" value="4">
           <!-- text input -->
          <div class="col-md-12">
            <div class="form-group">
              <label>Tên địa điểm<span class="red-star">*</span></label>
              <input type="text" class="form-control" id="add_address" value="{{ old('address') }}" name="str_tag"></textarea>
            </div>

          </div>
          <div classs="clearfix"></div>
      </div>
      <div style="clear:both"></div>
      <div class="modal-footer" style="text-align:center">
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveTagAjax"> Save</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="btnCloseModalTag">Close</button>
      </div>
      </form>
    </div>

  </div>
</div>
<div id="tagTag2" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    <form method="POST" action="{{ route('location.ajax-save')}}" id="formAjaxTag2">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tạo mới điểm đón</h4>
      </div>
      <div class="modal-body" id="contentTag">
          <input type="hidden" name="type" value="4">
           <!-- text input -->
          <div class="col-md-12">
            <div class="form-group">
              <label>Tên địa điểm<span class="red-star">*</span></label>
              <input type="text" class="form-control" id="add_address" value="{{ old('address') }}" name="str_tag">
            </div>
          </div>
          <div classs="clearfix"></div>
      </div>
      <div style="clear:both"></div>
      <div class="modal-footer" style="text-align:center">
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveTagAjax2"> Save</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="btnCloseModalTag2">Close</button>
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
  function checkToday(){
    $('.ngay-don-tien').each(function(){
      var date = $(this).val();
      if(date != ''){
        var tmpArrDate = date.split("/");
        // Create date from input value
        var inputDate = new Date(tmpArrDate[1] + "/" + tmpArrDate[0] + "/" + tmpArrDate[2]);
        console.log(tmpArrDate);
        // Get today's date
        var todaysDate = new Date();

        // call setHours to take the time out of the comparison
        if(inputDate.setHours(0,0,0,0) == todaysDate.setHours(0,0,0,0)) {
            alert('Anh/chị đang đặt cuốc xe TRONG NGÀY nên cần BẮT BUỘC thông báo cho điều hành xe theo số 0911380111 để đảm bảo.');
        }
        return false;
      }

    });
    return true;
  }
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
  $(document).ready(function(){
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


  });
</script>
@stop
