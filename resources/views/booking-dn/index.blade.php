@extends('layout')
@section('content')
<div class="content-wrapper">


<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="text-transform: uppercase;">
    Đặt tour tại <span class="hot">{{ $cityName[$city_id] }}</span>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking-dn.index') }}">
     Tour ĐN</a></li>
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
      <a href="{{ route('booking-dn.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>

      <div class="panel panel-default">

        <div class="panel-body" style="padding: 5px !important;">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking-dn.index') }}" id="searchForm" style="margin-bottom: 0px;">
            @include('partials.block-search-date')
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="PTT ID" value="{{ $arrSearch['id_search'] }}" style="width: 70px">
            </div>
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="created_at" placeholder="Ngày đặt" value="{{ $arrSearch['created_at'] }}" style="width: 100px">
            </div>

            <div class="form-group">
              <select class="form-control select2" name="tour_id" id="tour_id">
                <option value="">--Tour--</option>
                @foreach($tourSystem as $tour)
                <option value="{{ $tour->id }}" {{ $arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                @endforeach
              </select>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)

            <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }} - {{ $user->phone }}</option>
                @endforeach
              </select>
            </div>
            @endif

            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                <option value="">--Thu cọc--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_coc'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach

              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                <option value="">--Thu tiền--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_tien'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" placeholder="Số ĐT"  style="width: 100px">
            </div>

            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <div class="form-group">

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

            @endif
              <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">Mới</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn Tất</label>
            </div>
            <!-- <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_4" {{ in_array(4, $arrSearch['status']) ? "checked" : "" }} value="4">
              <label for="status_4">Dời ngày</label>
            </div> -->
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="no_meals" id="no_meals" {{ $arrSearch['no_meals'] == 1 ? "checked" : "" }} value="1">
              <label for="no_meals">Không ăn&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>

            </div>
          </form>
        </div>
      </div>

      <div class="panel" style="margin-bottom: 15px;">
        <div class="panel-body" style="padding: 5px;">
          <div class="table-responsive">
          <table class="table table-bordered" id="table_report" style="margin-bottom:0px;font-size: 14px;">
              <tr style="background-color: #ffff99">
                <th class="text-center" width="20%">Tổng BK</th>
                <th class="text-center" width="20%">NL/TE</th>
                <th class="text-center" width="20%">Ăn NL/TE</th>
                <th class="text-right" width="20%">Hoa hồng</th>
              </tr>
              <tr>
                <td class="text-center">{{ number_format($items->total()) }}</td>
                <td class="text-center">{{ number_format($tong_so_nguoi ) }} / {{ number_format($tong_te ) }}</td>
                <td class="text-center">{{ number_format($tong_phan_an ) }} / {{ number_format($tong_phan_an_te ) }}</td>

                <td class="text-right">{{ number_format($tong_hoa_hong_cty ) }}</td>
              </tr>
          </table>
        </div>
        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="table-responsive" style="margin-top: 20px;">
          <table class="table table-bordered table-hover">
            <tr style="background-color: #ffff99">
              <th></th>
              @foreach($collecterList as $col)
              @if(in_array($col->id, [1, 3]))
              <th class="text-right">{{ $collecterNameArr[$col->id] }}</th>
              @endif
              @endforeach
              <th class="text-right">Tổng</th>
            </tr>
            <tr>
              <th>Tiền cọc</th>
              @foreach($collecterList as $col)
              @if(in_array($col->id, [1, 3]))
              <td class="text-right">{{ isset($arrThuCoc[$col->id]) && $arrThuCoc[$col->id] > 0 ? number_format($arrThuCoc[$col->id]) : '-' }}</td>
              @endif
              @endforeach
              <td class="text-right">
                {{ number_format($tong_coc) }}
              </td>
            </tr>
            <tr>
              <th>Tiền thực thu</th>
              @foreach($collecterList as $col)
              @if(in_array($col->id, [1, 3]))
              <td class="text-right">{{ isset($arrThuTien[$col->id]) && $arrThuTien[$col->id] > 0 ? number_format($arrThuTien[$col->id]) : '-' }}</td>
              @endif
              @endforeach
              <td class="text-right">
                {{ number_format($tong_thuc_thu) }}
              </td>
            </tr>
          </table>
        </div>
        @endif
        </div>
      </div>
      <div class="box">


        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">

            <select class="form-control select2 multi-change-column-value" data-column="status">
                <option value="">--SET TRẠNG THÁI--</option>
                <option value="1">Mới</option>
                <option value="2">Hoàn tất</option>
                <option value="3">Hủy</option>
              </select>
             <select class="form-control select2 multi-change-column-value" data-column="nguoi_thu_tien">
                <option value="">--SET THU TIỀN--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}">{{ $col->name }}</option>
                @endforeach
              </select>

          </div>
          <div class="form-group" style="float: right">
            <a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a>
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
              <th style="width: 1%" class="text-center" ><input type="checkbox" id="check_all" value="1"></th>
              <th style="width: 1%"></th>
              <th width="200">Tên KH</th>
              <th style="width: 200px">Nơi đón</th>
              <th class="text-center" width="80">NL/TE/EB</th><th class="text-right" width="100">Tổng tiền/Cọc<br> HH</th>
              <th class="text-center" width="60">Ngày đi</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $l = 0; ?>
              @foreach( $items as $item )
                <?php $l ++; ?>
              <tr class="booking" id="row-{{ $item->id }}" data-id="{{ $item->id }}" data-date="{{ $item->use_date }}" style="border-bottom: 1px solid #000 !important;@if($item->status == 3) background-color: #f77e7e; @endif">
                <td class="text-center" style="line-height: 30px">
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">

                  <a href="{{ route('view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>
                  <br>{{ date('d/m H:i', strtotime($item->created_at)) }}
                  <span class="label label-sm label-danger" id="error_unc_{{ $item->id }}"></span>
                </td>
                <td style="text-align: center;white-space: nowrap; line-height: 30px;"><strong style="color: red;">PTT{{ $item->id }}</strong>
                  <br>
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif

                </span></td>
                <td style="position: relative; line-height: 30px;">



                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a style="font-weight: bold" href="{{ route( 'booking-dn.edit', $arrEdit) }}">{{ $item->name }}</a>
                  @if($item->status != 3)
                     - <a href="tel:{{ $item->phone }}" style="font-weight: bold">{{ $item->phone }}</a>

                  @if($item->tour_id)
                  <br><label class="label" style="background-color:{{ $tourSystemName[$item->tour_id]['bg_color'] }}">{{ $tourSystemName[$item->tour_id]['name'] }}</label>
                  @endif

                  @if($item->tour_type == 2)
                  <br><label class="label label-danger">Tour riêng</label>
                  @endif

                    @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                    <br><i style="font-style: italic;" class="glyphicon glyphicon-user"></i>
               <i>
                  @if($item->user)
                    {{ $item->user->name }}
                  @else
                    {{ $item->user_id }}
                  @endif
               </i>

                  @endif

                  @endif
                  <p style="color:#f0ad4e; font-style: italic;" id="error_{{ $item->id }}"></p>


                </td>

                <td style="line-height: 22px; position: relative;">

                  @if($item->location)
                  {{ $item->location->name }}
                  <br>
                 <!--  {{ $item->location->address }} -->
                  @else
                  {{ $item->address }}
                  @endif
                  <span style="color:red; font-size:12px">{{ $item->notes }}</span>

                </td>
                 <td class="text-center">
                  @if($item->status != 3)
                    {{ $item->adults }} / {{ $item->childs }} / {{ $item->infants }}
                    <br>
                    <?php
                    $meals = $item->meals;
                    if($meals > 0){
                      $meals+= $item->meals_te/2;
                    }

                    ?>
                    <i class="  glyphicon glyphicon-briefcase"></i> {{ $meals }}
                  @endif
                </td>


                <td class="text-right">
                  @if($item->status != 3)
                    {{ number_format($item->total_price) }}/{{ number_format($item->tien_coc) }}
                    @if($item->total_price_child > 0)
                    <br><span style="color:green">TE +{{ number_format($item->total_price_child) }}</span>
                    @endif
                  @endif
                  <br>
                  <span style="color: #06b7a4; font-weight: bold">HH: {{ number_format($item->hoa_hong_cty) }}</span>
                </td>

                <td class="text-center">
                  {{ date('d/m', strtotime($item->use_date)) }}
                </td>


                <!-- <td class="text-right">
                  {{ number_format($item->hoa_hong_cty) }}
                </td> -->

                <td style="white-space:nowrap; position: relative;">
                  @if($item->status != 3)

                    @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                    <a href="{{ route( 'booking-dn.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1 && Auth::user()->id == 1)
                    <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-dn.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                    @endif


                  @endif

                  <br><a style="font-size: 14px" target="_blank" href="{{ route('history.booking', ['id' => $item->id]) }}">Xem lịch sử</a>


                </td>
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

              <select class="form-control select2 multi-change-column-value" name="status" id="status">
                  <option value="">--SET TRẠNG THÁI--</option>
                  <option value="1">Mới</option>
                  <option value="2">Hoàn tất</option>
                  <option value="3">Hủy</option>
                </select>
              <select class="form-control select2 multi-change-column-value" name="nguoi_thu_tien" id="nguoi_thu_tien">
                  <option value="">--SET THU TIỀN--</option>
                  @foreach($collecterList as $col)
                  <option value="{{ $col->id }}">{{ $col->name }}</option>
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
<input type="hidden" id="table_name" value="articles">
@stop
<style type="text/css">
  .hdv{
    cursor: pointer;
  }
  .hdv:hover, .hdv.selected{
    background-color: #06b7a4;
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
</style>
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    });
  });
</script>
<script type="text/javascript">
    $(document).ready(function(){

      $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
      });
      $('tr.booking').click(function(){
        $(this).find('.check_one').attr('checked', 'checked');
      });
      $("#check_all").click(function(){
          $('input.check_one').not(this).prop('checked', this.checked);
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
