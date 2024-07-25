@extends('layout')
@section('content')
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1 style="text-transform: uppercase;">
      Đặt tour tại <span class="hot">{{ $cityName[$city_id] }}</span>
    </span></h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('booking-dn.index') }}" style="margin-bottom:5px">Quay lại</a>
    <a class="btn btn-success btn-sm" href="{{ route('booking-dn.index') }}" style="margin-bottom:5px">Xem danh sách booking</a>
    <form role="form" method="POST" action="{{ route('booking-dn.store') }}" id="dataForm">
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
              <input type="hidden" name="type" value="1">

              <div class="row">

                <div class="form-group col-xs-6">
                  <label>Tour<span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_id" name="tour_id">
                      @foreach($tourSystem as $tour)
                      <option value="{{ $tour->id }}" {{ old('tour_id', $tour_id) == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                      @endforeach
                  </select>
                </div>

                <div class="form-group col-xs-6">
                  <label>Hình thức <span class="red-star">*</span></label>
                  <select class="form-control select2" id="tour_type" name="tour_type">
                      <option value="1" {{ old('tour_type') == 1 ? "selected" : "" }}>Tour ghép</option>
                      <option value="2" {{ old('tour_type') == 2 ? "selected" : "" }}>Tour riêng</option>
                  </select>
                </div>

                </div>
                <div class="row">
                  <div class="form-group col-xs-12">
                    <label>Đối tác<span class="red-star">*</span></label>
                    <select class="form-control select2" id="partner_id" name="partner_id">
                        @foreach($partnerList as $partner)
                        <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? "selected" : "" }}>{{ $partner->name }}</option>
                        @endforeach
                    </select>
                  </div>
                </div>
               <div class="row">
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <div class="form-group col-xs-12">
                     <label>Sales <span class="red-star">*</span></label>
                      <select class="form-control select2" name="user_id" id="user_id">
                        <option value="0">--Chọn--</option>
                        @foreach($listUser as $user)
                        <option data-level="{{ $user->level }}" value="{{ $user->id }}" {{ old('user_id') == $user->id ? "selected" : "" }}>{{ $user->name }} - {{ Helper::getLevel($user->level) }}</option>
                        @endforeach
                      </select>
                  </div>

                  @else
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  @endif
                  <input type="hidden" name="book_date" value="">
                </div>

                <div class="row">
                  <div class="form-group col-xs-6">
                    <label>Tên KH <span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6">
                    <label>Điện thoại <span class="red-star">*</span></label>
                    <input type="text" maxlength="11" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" autocomplete="off">
                  </div>
                   <div class="form-group col-xs-12">
                  <label>Facebook</label>
                  <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook') }}" autocomplete="off">
                </div>
                   <div class="form-group col-xs-12">
                    <label>Ngày đi <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="use_date" id="use_date" value="{{ old('use_date') }}" autocomplete="off">
                  </div>
                </div>
                <div class="row">
                   </div><div class="row">
                  <div class="col-xs-12 input-group" style="padding-left: 15px;padding-right: 15px">

                  <label>Nơi đón <span class="red-star">*</span></label>

                  <select class="form-control select2" name="location_id" id="location_id">
                    <option value="">--Chọn--</option>
                    @foreach($listTag as $location)
                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? "selected" : "" }}>{{ $location->name }}</option>
                    @endforeach
                  </select>
                  <span class="input-group-btn">
                    <button style="margin-top:24px" class="btn btn-primary btn-sm" id="btnAddTag" type="button" data-value="3">
                      Thêm
                    </button>
                  </span>
                </div>
                </div>
                <div class="mt15 rooms-row row-dia-diem" style="background-color: #e6e6e6; padding:10px;border-radius: 5px">
                <div class="row">

                  <div class="form-group col-xs-6">
                      <label>Người lớn</label>
                      <select class="form-control amount select2" name="adults" id="adults">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('adults') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-6">
                        <label>Giá gốc</label>
                      <input type="text" name="adult_cost" id="adult_cost" class="form-control cost number" value="{{ old('adult_cost') }}"  autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                  <div class="form-group col-xs-6" >
                      <label>Giá bán</label>
                      <input type="text" name="price_adult" id="price_adult" class="form-control number price" value="{{ old('price_adult') }}"  autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Tổng tiền</label>
                      <input type="text" name="total_price_adult" id="total_price_adult" class="form-control number total" value="{{ old('total_price_adult') }}" autocomplete="off">
                  </div>
                </div>

                </div>
                <div class="mt15 rooms-row row-dia-diem" style="background-color: #e6e6e6; padding:10px;border-radius: 5px">
                <div class="row">

                  <div class="form-group col-xs-6" >
                      <label>Trẻ em</label>
                      <select class="form-control amount select2" name="childs" id="childs">
                        <option value="0">0</option>
                        @for($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}" {{ old('childs') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                  <div class="form-group col-xs-6">
                        <label>Giá gốc</label>
                      <input type="text" name="child_cost" id="child_cost" class="form-control number cost" value="{{ old('child_cost') }}" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                  <div class="form-group col-xs-6">
                      <label>Giá bán</label>
                      <input type="text" name="price_child" id="price_child" class="form-control number price" value="{{ old('price_child') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Tổng tiền</label>
                      <input type="text" name="total_price_child" id="total_price_child" class="form-control number total" value="{{ old('total_price_child') }}" autocomplete="off">
                  </div>
                </div>

                </div>
                <div class="row">
                  <div class="form-group col-xs-12">
                      <label>Em bé (< 1m)</label>
                      <select class="form-control select2" name="infants" id="infants">
                        @for($i = 0; $i <= 20; $i++)
                        <option value="{{ $i }}" {{ old('infants') == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-4 col-xs-6">
                      <label>TỔNG TIỀN GỐC <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_cost" id="total_cost" value="{{ old('total_cost') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-md-4 col-xs-6">
                      <label>TỔNG TIỀN BÁN <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="total_price" id="total_price" value="{{ old('total_price') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-md-4 col-xs-12">
                      <label>Hoa hồng <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="hoa_hong_cty" id="hoa_hong_cty" value="{{ old('hoa_hong_cty') }}" autocomplete="off">
                  </div>


                </div>
                <div class="row">
                  <div class="form-group col-xs-6" >
                      <label>Tiền cọc</label>
                    <input type="text" class="form-control number" name="tien_coc" id="tien_coc" value="{{ old('tien_coc') }}" autocomplete="off">
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>Người thu cọc <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                          @if(in_array($col->id, [1, 3]))
                          <option value="{{ $col->id }}" {{ old('nguoi_thu_coc') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                          @endif
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group col-xs-6" >
                      <label>CÒN LẠI <span class="red-star">*</span></label>
                      <input type="text" style="border: 1px solid red" class="form-control number" name="con_lai" id="con_lai" value="{{ old('con_lai') }}" autocomplete="off">
                  </div>

                  <div class="form-group col-xs-6">
                      <label>Người thu tiền <span class="red-star">*</span></label>
                      <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                        <option value="">--Chọn--</option>
                        @foreach($collecterList as $col)
                          @if(in_array($col->id, [1, 3]))
                          <option value="{{ $col->id }}" {{ old('nguoi_thu_tien') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                          @endif
                        @endforeach
                      </select>
                  </div>
                </div>

                <div class="form-group" style="display: none;">
                     <label>Trạng thái <span class="red-star">*</span></label>
                      <select class="form-control select2" name="status" id="status">
                        <option value="1" {{ old('status') == 1 ? "selected" : "" }}>Mới</option>
                        <option value="2" {{ old('status') == 2 ? "selected" : "" }}>Hoàn tất</option>
                        <option value="3" {{ old('status') == 3 ? "selected" : "" }}>Hủy</option>
                      </select>
                  </div>

                <div class="form-group">
                  <label>Ghi chú</label>
                  <textarea class="form-control" rows="6" name="notes" id="notes">{{ old('notes') }}</textarea>
                </div>

            </div>

            <div class="box-footer">
              <button type="button" class="btn btn-default btn-sm" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i> Đang xử lý...</button>
              <button type="submit" id="btnSave" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking-dn.index')}}">Hủy</a>
            </div>

        </div>
        <!-- /.box -->

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
          <input type="hidden" name="type" value="1">
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
<style type="text/css">
  fieldset.scheduler-border-2{
    border: 1px groove #ddd !important;
    padding: 0 5px 5px 5px !important;
    margin: 0 0 5px 0 !important;
    -webkit-box-shadow: 0px 0px 0px 0px #000;
    box-shadow: 0px 0px 0px 0px #000;
  }
</style>
@stop
@section('js')
<script type="text/javascript">

  $(document).on('click','#btnSave', function(){

    if(parseInt($('#tien_coc').val()) > 0 && $('#nguoi_thu_coc').val() == ''){
      alert('Bạn chưa chọn người thu cọc');
      return false;
    }
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
function calPrice(){

    var total_price = 0;
    var total_cost = 0;
    $('.rooms-row').each(function(){
      var row = $(this);
      var amount = parseInt(row.find('.amount').val());
      var cost = parseInt(row.find('.cost').val());
      var price = parseInt(row.find('.price').val());

      if(amount > 0 && cost > 0 && price > 0){
        var total_price_row = amount*price;
        var total_cost_row = amount*cost;
        row.find('.total').val(total_price_row);
        total_price += total_price_row;
        total_cost += total_cost_row;
      }

    });

    $('#total_price').val(total_price);
    $('#total_cost').val(total_cost);

    //tien_coc
    var tien_coc = 0;
    if($('#tien_coc').val() != ''){
     tien_coc = parseInt($('#tien_coc').val());    }


    $('#con_lai').val(total_price - tien_coc);
    if(total_price > 0 && total_cost > 0){
      $('#hoa_hong_cty').val(total_price - total_cost);
    }

}
function getTourPrice(){
  if($('#partner_id').val() > 0 && $('#use_date').val() != ''){
    $.ajax({
        url : "{{ route('booking-dn.ajax-get-price') }}",
        type : 'GET',
        data: {
          tour_id : $('#tour_id').val(),
          use_date : $('#use_date').val(),
          partner_id : $('#partner_id').val(),
        },
        success: function(data){
          var obj = JSON.parse(data);
          $('#adult_cost').val(obj.adult_cost);
          $('#child_cost').val(obj.child_cost);
        }
      });
  }

}
  $(document).ready(function(){
    $('.amount, .price, .cost, #tien_coc').change(function(){
      calPrice();
    });
    $('#dataForm').submit(function(){
      $('#btnSave').hide();
      $('#btnLoading').show();
    });
    $('#tour_id').change(function(){
      location.href="{{ route('booking-dn.create')}}?tour_id=" + $(this).val();
    });
    $('#use_date').change(function(){
      getTourPrice();
    });
    $('#btnAddTag').click(function(){
          $('#tagTag').modal('show');
      });


  });

</script>
@stop
