@extends('layout')
@section('content')
<div class="content-wrapper">


<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="text-transform: uppercase;">
    Quản lý đặt tour
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking.index', ['type' => $type]) }}">
      @if($type == 1)
    Tour
    @elseif($type == 2)

    @endif</a></li>
    <li class="active">Danh sách</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">

    <div class="col-md-12">
      <div id="content_alert"></div>
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      <a href="{{ route('booking.create', ['type' => $type]) }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">

        <div class="panel-body" style="padding: 5px !important;">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking.index') }}" id="searchForm" style="margin-bottom: 0px;">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="PTT ID" value="{{ $arrSearch['id_search'] }}" style="width: 70px">
            </div>
            <input type="hidden" name="tour_id" value="1">
            <div class="form-group">
                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
            </div>
           @if(Auth::user()->role == 1 && !Auth::user()->view_only)
          <div class="form-group">
            <select class="form-control select2" id="hdv_id" name="hdv_id">
              <option value="">--HDV--</option>
              @foreach($listHDV as $user)
              <option value="{{ $user->id }}" @if($arrSearch['hdv_id'] == $user->id) selected @endif>{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <select class="form-control select2"  data-column="cano_id" name="cano_id">
              <option value="">--CANO--</option>
              @foreach($canoList as $cano)
              <option value="{{ $cano->id }}" {{ $arrSearch['cano_id'] == $cano->id ? "selected" : "" }}>{{ $cano->name }}</option>
              @endforeach
            </select>
          </div>
           <div class="form-group">
              <select style="width: 150px" class="form-control select2" name="call_status" id="call_status">
                <option value="">--TT GỌI--</option>
                <option value="1" {{ $arrSearch['call_status'] == 1 ? "selected" : "" }}>Chưa gọi</option>
                <option value="2" {{ $arrSearch['call_status'] == 2 ? "selected" : "" }}>Gọi OK</option>
                <option value="3" {{ $arrSearch['call_status'] == 3 ? "selected" : "" }}>Chưa nghe máy</option>
                <option value="4" {{ $arrSearch['call_status'] == 4 ? "selected" : "" }}>Thuê bao</option>
                <option value="5" {{ $arrSearch['call_status'] == 5 ? "selected" : "" }}>Nhầm số</option>
                <option value="6" {{ $arrSearch['call_status'] == 6 ? "selected" : "" }}>Dời ngày</option>
                <option value="7" {{ $arrSearch['call_status'] == 7 ? "selected" : "" }}>Khách hủy</option>
              </select>
          </div>
          @endif
          @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
            <select class="form-control" name="hdv0" id="hdv0">
              <option value="">--TT CHỌN HDV--</option>
              <option value="2" {{ $arrSearch['hdv0'] == 2 ? "selected" : "" }}>Đã chọn HDV</option>
              <option value="1" {{ $arrSearch['hdv0'] == 1 ? "selected" : "" }}>Chưa chọn HDV</option>
            </select>
          </div>
          <div class="form-group">
            <select class="form-control" name="cano0" id="cano0">
              <option value="">--TT CHỌN CANO--</option>
              <option value="2" {{ $arrSearch['cano0'] == 2 ? "selected" : "" }}>Đã chọn CANO</option>
              <option value="1" {{ $arrSearch['cano0'] == 1 ? "selected" : "" }}>Chưa chọn CANO</option>
            </select>
          </div>
          @endif
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <div class="form-group">
              <button type="button" id="btnReset" class="btn btn-default btn-sm">Reset</button>
            </div>
            <div>
              @if($arrSearch['tour_id'] != 4)
              <div class="form-group">
              <input type="checkbox" name="tour_type[]" id="tour_type_1" {{ in_array(1, $arrSearch['tour_type']) ? "checked" : "" }} value="1">
              <label for="tour_type_1">GHÉP({{ $ghep }})</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_2" {{ in_array(2, $arrSearch['tour_type']) ? "checked" : "" }} value="2">
              <label for="tour_type_2">VIP({{ $vip }}-{{ $tong_vip }}NL)&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_3" {{ in_array(3, $arrSearch['tour_type']) ? "checked" : "" }} value="3">
              <label for="tour_type_3">THUÊ CANO({{ $thue }})&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @endif
              <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">Mới</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn Tất&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
              @if($arrSearch['tour_id'] != 4)
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="no_cab" id="no_cab" {{ $arrSearch['no_cab'] == 1 ? "checked" : "" }} value="1">
              <label for="no_cab" style="color: red; ">KHÔNG CÁP&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @endif
            <div class="form-group" style="float: right;">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="short" id="short" {{ $arrSearch['short'] == 1 ? "checked" : "" }} value="1">
              <label for="short">Short&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            </div>
          </form>
        </div>
      </div>
      <div class="panel" style="margin-bottom: 15px;">
        <div class="panel-body" style="padding: 5px;">
          <div class="table-responsive">
          <table class="table table-bordered" id="table_report" style="margin-bottom:0px;font-size: 14px;">
              <tr style="background-color: #f4f4f4">
                <th class="text-left">Tổng BK</th>
                <th class="text-left">NL/TE</th>
                <th class="text-left">Ăn NL/TE</th>
                <th class="text-left">Cáp NL/TE</th>

              </tr>
              <tr>
                <td class="text-left">{{ number_format($items->total()) }}</td>
                <td class="text-left">{{ number_format($tong_so_nguoi ) }} / {{ number_format($tong_te ) }}</td>
                <td class="text-left">{{ number_format($tong_phan_an ) }} / {{ number_format($tong_phan_an_te ) }}</td>
                <td class="text-left">{{ number_format($cap_nl ) }} / {{ number_format($cap_te ) }}</td>

              </tr>
          </table>

        </div>
        </div>
      </div>
      <div class="panel">
        <div class="panel-body" style="padding-bottom: 0px;">
          <ul style="padding: 0px;">
          @foreach($arrHDV as $hdv_id => $arrBK)
          <li style="display: inline;
    float: left;
    list-style: none; height: 45px;">
          @if($hdv_id > 0)
          <span data-id="{{ $hdv_id }}" class="label label-success hdv @if($hdv_id == $arrSearch['hdv_id']) selected @endif" style="padding: 10px 5px;margin-right: 10px; font-size: 12px">{{ isset($arrHDVDefault[$hdv_id]) ? $arrHDVDefault[$hdv_id]->name : $hdv_id }} [{{ count($arrBK)}}]</span>
          @else
          <span data-id="" class="label label-danger hdv" style="padding: 10px;margin-right: 10px; font-size: 12px">CHƯA CHỌN HDV [{{ count($arrBK)}}]</span>
          @endif
          </li>
          @endforeach
        </ul>
        </div>
      </div>

      <div class="box">


        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select style="font-size: 11px;" class="form-control select2 multi-change-column-value" data-column="hdv_id">
              <option value="">--SET HDV--</option>
              @foreach($listUser as $user)
              @if($user->hdv==1)
              <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endif
              @endforeach
            </select>
              <select class="form-control select2 multi-change-column-value"  data-column="cano_id">
                <option value="">--SET CANO--</option>
                @foreach($canoList as $cano)
                <option value="{{ $cano->id }}">{{ $cano->name }}</option>
                @endforeach
              </select>
          </div>
        </div>
        @endif
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr style="background-color: #f4f4f4">
              <th style="width: 1%" class="text-center"><input type="checkbox" id="check_all" value="1"></th>
              <th style="width: 1%"></th>
              <th style="width: 1%"></th>
              <th width="200">Tên KH</th>
              <th style="width: 1%">Thứ tự</th>
              <th style="width: 150px">TT Gọi</th>
              <th style="width: 200px">Nơi đón</th>
              <th class="text-center" width="80">NL/TE/EB</th>
              <th class="text-center" width="80">Phần ăn</th>
              <th class="text-center" width="60">Ngày đi</th>
              <th class="text-center" width="90">HDV/Cano</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $l = 0; ?>
              @foreach( $items as $item )
                <?php $l ++; ?>
              <tr class="booking" id="row-{{ $item->id }}" data-id="{{ $item->id }}" data-date="{{ $item->use_date }}" style="border-bottom: 1px solid #000 !important;@if($item->status == 3) background-color: #f77e7e; @endif @if($item->ko_cap_treo == 0) background-color: #cdf7f7;  @endif"

                >
                <td class="text-center" style="line-height: 30px">
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}"
                  data-adults="{{ $item->adults }}"
                  data-childs="{{ $item->childs }}"
                  data-infants="{{ $item->infants }}"
                  data-location="{{ $item->location_id }}"
                  >
                </td>
                <td><i class="fa fa-arrows move" aria-hidden="true" style="font-size: 20px; cursor: pointer;"></i></td>
                <td style="text-align: center;white-space: nowrap; line-height: 30px;"><strong style="color: red;">PTT{{ $item->id }}</strong>
                  <br>
                  @if($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif

                </span></td>
                <td style="position: relative; line-height: 30px;">
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp

                  <a style="font-weight: bold" href="{{ route( 'booking.edit', $arrEdit) }}">{{ $item->name }}</a>

                  @if($item->status != 3)
                     - <a href="tel:{{ $item->phone }}" style="font-weight: bold">{{ $item->phone }}</a>
                  @if($item->tour_cate == 2)
                  <br><label class="label label-info">2 đảo</label>
                  @endif

                  @if($item->tour_type == 3)
                  <br><label class="label label-warning">Thuê cano</label>
                  @elseif($item->tour_type == 2)
                  <br><label class="label label-danger">Tour VIP</label>
                  @endif
                  @endif

                </td>
                <td>
                  <input type="text" style="width:70px" class="display_order form-control" value="{{ $item->display_order }}" placeholder="Thứ tự">
                </td>
                <td  style="width: 150px">
                  <select style="width: 150px" class="form-control select2 change-column-value"  data-column="call_status" data-id="{{ $item->id }}">
                    <option value="">--TT GỌI--</option>
                    <option value="1" {{ $item->call_status == 1 ? "selected" : "" }}>Chưa gọi</option>
                    <option value="2" {{ $item->call_status == 2 ? "selected" : "" }}>Gọi OK</option>
                    <option value="3" {{ $item->call_status == 3 ? "selected" : "" }}>Chưa nghe máy</option>
                    <option value="4" {{ $item->call_status == 4 ? "selected" : "" }}>Thuê bao</option>
                    <option value="5" {{ $item->call_status == 5 ? "selected" : "" }}>Nhầm số</option>
                    <option value="6" {{ $item->call_status == 6 ? "selected" : "" }}>Dời ngày</option>
                    <option value="7" {{ $item->call_status == 7 ? "selected" : "" }}>Khách hủy</option>
                  </select>
                </td>
                <td style="line-height: 22px;">
                  @if($item->status != 3)
                  @if($item->location && !$arrSearch['chua_thuc_thu'])
                  {{ $item->location->name }}[{{ $item->location_id }}]
                  <br>
                  @else
                  {{ $item->address }}
                  @endif
                  <span style="color:red; font-size:12px">
                    @if($item->ko_cap_treo)
                    KHÔNG CÁP
                    @endif
                    {{ $item->notes }}</span>
                  @endif
                </td>
                 <td class="text-center">
                  @if($item->status != 3)
                    {{ $item->adults }} / {{ $item->childs > 0 ? $item->childs : "-" }} / {{ $item->infants > 0 ? $item->infants : "-" }}
                    <br>

                  @endif

                </td>
                <td class="text-center">
                  <?php
                    $meals = $item->meals;
                    if($meals > 0){
                      $meals+= $item->meals_te/2;
                    }

                    ?>
                    {{ $meals }}
                </td>


                <td class="text-center">
                  {{ date('d/m', strtotime($item->use_date)) }}
                </td>
                <td class="text-center">
                  @if($item->tour_id == 4)
                   <!--  @if($item->mail_hotel == 0)
                    <a href="{{ route('mail-preview', ['id' => $item->id, 'tour_id' => 4]) }}" class="btn btn-sm btn-success" >
                      <i class="  glyphicon glyphicon-envelope"></i> Book Tour
                    </a>
                    @else
                    <p class="label label-info" style="margin-bottom: 5px; clear:both">Đã mail John</p>
                    @endif -->
                  @else
                    @if($item->status != 3 && $arrSearch['is_edit'] == 1)
                      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                      <select style="width: 150px !important; margin-bottom: 5px" class="form-control select2 change-column-value" data-column="hdv_id" data-id="{{ $item->id }}">
                        <option value="">--HDV--</option>
                        @foreach($listUser as $user)
                        @if($user->hdv==1)
                        <option value="{{ $user->id }}" @if($item->hdv_id == $user->id) selected @endif>{{ $user->name }}</option>
                        @endif
                        @endforeach
                      </select>
                      <div style="clear:both" style="margin-bottom: 5px"></div>
                      <select class="form-control select2 change-column-value"  data-column="cano_id" data-id="{{ $item->id }}">
                        <option value="">--CANO--</option>
                        @foreach($canoList as $cano)
                        <option value="{{ $cano->id }}" {{ $item->cano_id == $cano->id ? "selected" : "" }}>{{ $cano->name }}</option>
                        @endforeach
                      </select>
                      @else
                      @if($item->hdv_id > 0 )
                      <strong>{{ $item->hdv->name }}</strong>
                      @endif
                      @endif
                    @else
                      @if($item->hdv_id > 0)
                      {{ $item->hdv->name }}
                      @else
                      - HDV -
                      @endif
                      @if($item->cano_id > 0)
                      <br> - {{ $item->cano->name }}
                      @else
                      <br> - Cano -
                      @endif
                    @endif
                  @endif
                </td>

                <!-- <td class="text-right">
                  {{ number_format($item->hoa_hong_cty) }}
                </td> -->


              </tr>
              @endforeach
            @else
            <tr>
              <td colspan="9">Không có dữ liệu.</td>
            </tr>
            @endif

          </tbody>
          </table>
          </div>
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          @if(Auth::user()->role == 1 && !Auth::user()->view_only)
          <div class="form-inline" style="padding: 5px">
            <div class="form-group">
              <select class="form-control select2 multi-change-column-value" id="hdv_id" name="hdv_id" data-column="hdv_id">
                <option value="">--SET HDV--</option>
                @foreach($listUser as $user)
                @if($user->hdv==1)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endif
                @endforeach
              </select>

              <select class="form-control select2 multi-change-column-value"  data-column="cano_id">
                <option value="">--SET CANO--</option>
                @foreach($canoList as $cano)
                <option value="{{ $cano->id }}">{{ $cano->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          @endif

        </div>

      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
</section>
<!-- /.content -->
</div>
<div class="modal fade" id="uncModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="unc_img" style="width: 100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
<div id="target_info" style="background-color: red; width: 200px; height: 60px"></div>
<input type="hidden" id="table_name" value="articles">
@stop
<style type="text/css">
  .hdv{
    cursor: pointer;
  }
  .hdv:hover, .hdv.selected{
    background-color: #f39c12;
    color: #FFF
  }
  label{
    cursor: pointer;
  }
  #table_report th td {padding: 2px !important;}
  #searchForm, #searchForm input{
    font-size: 13px;
  }
  .form-control{
    font-size: 13px !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered{

    font-size: 12px !important;
  }
  tr.error{
    background-color:#ffe6e6
  }
  #target_info{
    border-radius: 5px;
    color: #FFF;
    font-weight: bold;
    padding: 5px;
  }

</style>
@section('js')
<script type="text/javascript">
  jQuery(function($) {
    $(window).scroll(function fix_element() {
      if($('.check_one:checked').length > 0){
         $('#target_info').css(
          $(window).scrollTop() > 1
            ? { 'position': 'fixed', 'top': '10px', 'left' : '30%' }
            : { 'position': 'relative', 'top': 'auto' }
        );
      }

        return fix_element;
    }());
  });
  $(document).ready(function(){

    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    });
  });
</script>
<script type="text/javascript">
  $(document).ready(function(){
  $('#table-list-data tbody').sortable({
        placeholder: 'placeholder',
        handle: ".move",
        start: function (event, ui) {
                ui.item.toggleClass("highlight");
        },
        stop: function (event, ui) {
                ui.item.toggleClass("highlight");
        },
        axis: "y",
        update: function() {
            var rows = $('#table-list-data tbody tr');
            var strOrder = '';
            var strTemp = '';
            // for (var i=0; i<rows.length; i++) {
            //     strTemp = rows[i].id;
            //     strOrder += strTemp.replace('row-','') + ";";
            // }
            //updateOrder("loai_sp", strOrder);
        }
    });
});
    $(document).ready(function(){
      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
      // $('tr.booking').each(function(){
      //   var tr = $(this);
      //   var id = tr.data('id');
      //   var use_date = tr.data('date');
      //   var today = new Date();
      //   if(use_date < "{{ date('Y-m-d') }}"){
      //     $.ajax({
      //       url : '{{ route('booking.checkError') }}?id=' + id,
      //       type : 'GET',
      //       success : function(data){
      //         $('#error_' + id).text(data);
      //       }
      //     });
      //   }
      // });
      @endif
      $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
      });
      // $('tr.booking').click(function(){
      //   $(this).toggleClass('choose');
      //   var obj = $(this).find('.check_one');
      //   $(this).find('.check_one').attr('checked', 'checked');
      // });
      $("#check_all").click(function(){
          $('input.check_one').not(this).prop('checked', this.checked);
          if($(this).prop('checked')){
            $('tr.booking').addClass('choose');
          }else{
            $('tr.booking').removeClass('choose');
          }
      });
      $('.check_one').click(function(){
        var obj = $(this);
        if(obj.prop('checked')){
          obj.parents('tr').addClass('choose');
        }else{
          obj.parents('tr').removeClass('choose');
        }
        calPerson();
      });
      function calPerson(){
        var arrLocation = [];
        var totalAdults = totalChilds = totalInfants = 0;
        $('.check_one:checked').each(function(){
          var obj = $(this);
          totalAdults += parseInt(obj.data('adults'));
          totalChilds +=  parseInt(obj.data('childs'));
          totalInfants +=  parseInt(obj.data('infants'));
          console.log(obj.data('location'));
          console.log(typeof arrLocation[obj.data('location')]);
          if(typeof arrLocation[obj.data('location')] === 'undefined') {
              arrLocation.push(obj.data('location'));
          }
        });
        console.log(arrLocation.length);
        var str = "Đang chọn " + totalAdults + ' NL';
        if(totalChilds > 0){
          str += ', ' + totalChilds + ' TE';
        }
        if(totalInfants > 0){
          str += ', ' + totalInfants + ' EB';
        }
        str  += '<br>' + 'Điểm đón: ' + arrLocation.length;
        $('#target_info').html(str);
      }
      $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.cong-no-tour') }}").submit().attr('action', oldAction);
      });
      $('#btnExportGui').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.gui-tour') }}").submit().attr('action', oldAction);
      });
      $('#temp').click(function(){
        $(this).parents('form').submit();
      });
    	$('.change_status').click(function(){
		      var obj = $(this);
		      $.ajax({
		        url : "{{ route('change-export-status') }}",
		        type : 'GET',
		        data : {
		          id : obj.data('id')
		        },
		        success: function(){
		          window.location.reload();
		        }
		      });
		    });
       $('.change_status_bk').click(function(){
          var obj = $(this);
          $.ajax({
            url : "{{ route('change-status') }}",
            type : 'GET',
            data : {
              id : obj.data('id')
            },
            success: function(){
              //window.location.reload();
            }
          });
        });
       $('.change-column-value').change(function(){
          var obj = $(this);
          if(obj.data('column') == 'cano_id'){
           // alert('Tất cả các booking cùng HDV sẽ được gán chung vào cano này');
          }
          $.ajax({
            url : "{{ route('booking.change-value-by-column') }}",
            type : 'GET',
            data : {
              id : obj.data('id'),
              col : obj.data('column'),
              value: obj.val()
            },
            success: function(data){
                console.log(data);
            }
          });
       });
       $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              $.ajax({
                url : "{{ route('booking.change-value-by-column') }}",
                type : 'GET',
                data : {
                  id : $(this).val(),
                  col : obj.data('column'),
                  value: obj.val()
                },
                success: function(data){

                }
              });
          });

       });
      $('.hoa_hong_sales').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('save-hoa-hong')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            hoa_hong_sales : obj.val()
          },
          success : function(doc){

          }
        });

      });
      $('.hdv').click(function(){
        var hdv_id = $(this).data('id');
        if(hdv_id != ""){
          $('#hdv_id').val($(this).data('id'));
          $('#searchForm').submit();
        }

      });
      $('.change_tien_thuc_thu').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'tien_thuc_thu'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
       $('.change_tien_coc').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'tien_coc'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
      $('.change_total_price').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column') }}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'total_price'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
      $('.change_price_net').click(function(){
        var obj = $(this);
        var price_net = 0;
        if(obj.prop('checked') == true){
          price_net = 1
        }
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : price_net,
            col : 'price_net'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
      $('#btnReset').click(function(){
        $('#searchForm select').val('');
        $('#searchForm').submit();
      });
    });
  </script>
@stop
