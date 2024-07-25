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
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking.index') }}" id="searchForm">
            <input type="hidden" name="type" value="{{ $type }}">
            <!-- <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="created_at" placeholder="Ngày tạo" value="{{ $arrSearch['created_at'] }}" style="width: 90px">
            </div> -->

            <div class="form-group">
              <select class="form-control" name="tour_type" id="tour_type">
                <option value="">--Hình thức--</option>
                <option value="1" {{ $arrSearch['tour_type'] == 1 ? "selected" : "" }}>Tour ghép</option>
                <option value="2" {{ $arrSearch['tour_type'] == 2 ? "selected" : "" }}>Tour VIP</option>
                <option value="3" {{ $arrSearch['tour_type'] == 3 ? "selected" : "" }}>Thuê cano</option>
              </select>
            </div>

            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="Từ ngày" value="{{ $arrSearch['use_date_from'] }}" style="width: 90px">
            </div>
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" style="width: 90px;">
            </div>
            <div class="form-group">
              <select class="form-control" name="status" id="status">
                <option value="">--Trạng thái--</option>
                <option value="1" {{ $arrSearch['status'] == 1 ? "selected" : "" }}>Mới</option>
                <option value="2" {{ $arrSearch['status'] == 2 ? "selected" : "" }}>Hoàn tất</option>
                <option value="3" {{ $arrSearch['status'] == 3 ? "selected" : "" }}>Hủy</option>
              </select>
            </div>

            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>

            @endif

            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                <option value="">--Thu tiền--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_tien'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>


            <div class="form-group">
              <input type="checkbox"name="hh0" id="hh0" {{ $arrSearch['hh0'] == 1 ? "checked" : "" }} value="1">
              <label for="hh0">Chưa HH</label>
            </div>

            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>


      <div class="box">



        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr>

              <th width="200">Tên KH</th>

              <th class="text-center" width="80">NL/TE/EB</th>
              <th class="text-right" style="width: 150px !important; white-space: nowrap;">HH Sales</th>

              <th class="text-right" width="100">Tổng tiền/Cọc</th>
              <th class="text-right" width="100">THỰC THU</th>

              <th style="width: 200px">Nơi đón</th>

            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $l = 0; ?>
              @foreach( $items as $item )
                <?php $l ++; ?>
              <tr class="booking" id="row-{{ $item->id }}" style="border-bottom: 1px solid #000 !important;@if($item->status == 3) display:none; @endif" >

                <td style="position: relative;">      <strong style="color: red;font-size: 16px">PTT{{ $item->id }}</strong>
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif
                  @if($item->tour_id)
                  <br><label class="label" style="background-color:{{ $tourSystemName[$item->tour_id]['bg_color'] }}">{{ $tourSystemName[$item->tour_id]['name'] }}</label>
                  @endif

                  @if($item->tour_type == 3)
                  <label class="label label-warning">Thuê cano</label>
                  @elseif($item->tour_type == 2)
                  <label class="label label-danger">Tour VIP</label>
                  @endif
                  - @if(Auth::user()->role == 1 && !Auth::user()->view_only)

                  @if($item->user)
                  {{ $item->user->name }}
                  @else
                    {{ $item->user_id }}
                  @endif

                @endif
                  <br>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp

                  <a style="font-size:17px" href="{{ route( 'booking.edit', $arrEdit) }}">{{ $item->name }}</a>

                </td>

                 <td class="text-center">
                  @if($item->status != 3)
                    {{ $item->adults }} / {{ $item->childs }} / {{ $item->infants }}
                    <br>
                    <i class="  glyphicon glyphicon-briefcase"></i> {{ $item->meals }}
                  @endif

                </td>
                <td class="text-right" style="width: 130px">
                  @if($item->status != 3)
                    <?php //var_dump($item->hoa_hong_sales); ?>
                    @if(!in_array($item->user_id, [33,18,7,21]))
                    @if(!$item->hoa_hong_sales && $item->status != 3)
                    <input style="text-align: right;" type="text" class="form-control hoa_hong_sales number" maxlength="4" data-id="{{ $item->id }}" value="{{ $item->hoa_hong_sales ? number_format($item->hoa_hong_sales) : "" }}">
                    @else
                     {{ number_format($item->hoa_hong_sales) }}
                    @endif
                    @endif


                  @endif
                </td>

                <td class="text-right">
                  @if($item->status != 3)
                  {{ number_format($item->total_price) }}/{{ number_format($item->tien_coc) }}
                  <br>
                  <span style="color: #06b7a4; font-weight: bold">HH: {{ number_format($item->hoa_hong_sales) }}</span>
                  @if($item->total_price_child > 0)
                  <br><span style="color:green">TE +{{ number_format($item->total_price_child) }}</span>
                  @endif

                  @if($item->extra_fee > 0)
                  <br><span style="color:blue">+{{ number_format($item->extra_fee) }}</span>
                  @endif
                  @if($item->discount > 0)
                  <br><span style="color:red">-{{ number_format($item->discount) }}</span>
                  @endif
                  @endif
                </td>
                <td class="text-right">
                  @if($item->status != 3)

                    @if($item->nguoi_thu_tien)
                    <span style="color: red">{{ $collecterNameArr[$item->nguoi_thu_tien] }}</span>
                    @endif

                     - {{ number_format($item->tien_thuc_thu) }}
                    <br> HDV thu: <span style="color: blue">{{ number_format($item->hdv_thu) }} </span>

                    @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                     <div class="form-group" style="margin-bottom: 5px">

                    <input type="text" style="text-align: right;margin-top: 10px; width: 100px" class="form-control change_tien_thuc_thu number" data-id="{{ $item->id }}" value="{{ $item->tien_thuc_thu ? number_format($item->tien_thuc_thu) : "" }}">
                  </div>
                    @endif
                  @endif
                </td>
                <td>
                  <span style="color:red; font-size:12px">
                    @if($item->ko_cap_treo)
                    KHÔNG ĐI CÁP TREO <br>
                    @endif
                    {{ $item->notes }}</span>

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
      $('tr.booking').click(function(){
        $(this).find('.check_one').attr('checked', 'checked');
      });
      $("#check_all").click(function(){
          $('input.check_one').not(this).prop('checked', this.checked);
      });
      $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.cong-no-tour') }}").submit().attr('action', oldAction);
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
            alert('Tất cả các booking cùng HDV sẽ được gán chung vào cano này');
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
    });
  </script>
@stop
