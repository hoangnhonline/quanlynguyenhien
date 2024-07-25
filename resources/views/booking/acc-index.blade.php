@extends('layout')
@section('content')
<div class="content-wrapper">
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
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="PTT ID" value="{{ $arrSearch['id_search'] }}" style="width: 70px">
            </div>
            <div class="form-group">
              <select class="form-control select2" name="tour_id" id="tour_id">
                <option value="">--Tour--</option>
                <option value="1" {{ $arrSearch['tour_id'] == 1 ? "selected" : "" }}>Tour đảo</option>
                <option value="3" {{ $arrSearch['tour_id'] == 3 ? "selected" : "" }}>Rạch Vẹm</option>
                <option value="5" {{ $arrSearch['tour_id'] == 5 ? "selected" : "" }}>Grand World</option>
                <option value="6" {{ $arrSearch['tour_id'] == 6 ? "selected" : "" }}>Bãi Sao - 2 Đảo</option>
                <option value="7" {{ $arrSearch['tour_id'] == 7 ? "selected" : "" }}>Bãi Sao - ĐTH</option>
                <option value="8" {{ $arrSearch['tour_id'] == 8 ? "selected" : "" }}>Bãi Sao - Hòn Thơm</option>
                <option value="4" {{ $arrSearch['tour_id'] == 4 ? "selected" : "" }}>Câu Mực</option>
              </select>
            </div>
            <div class="form-group">
                <select class="form-control select2" id="tour_cate" name="tour_cate" >
                    <option value="">--Loại tour--</option>
                    <option value="1" {{ $arrSearch['tour_cate'] == 1 ? "selected" : "" }}>4 đảo</option>
                    <option value="2" {{ $arrSearch['tour_cate'] == 2 ? "selected" : "" }}>2 đảo</option>
                </select>
              </div>

            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="Từ ngày" value="{{ $arrSearch['use_date_from'] }}" style="width: 100px">
            </div>
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" style="width: 100px">
            </div>
            <div class="form-group">
              <select class="form-control" name="status" id="status">
                <option value="">--Trạng thái--</option>
                <option value="1" {{ $arrSearch['status'] == 1 ? "selected" : "" }}>Mới</option>
                <option value="2" {{ $arrSearch['status'] == 2 ? "selected" : "" }}>Hoàn tất</option>
                <option value="3" {{ $arrSearch['status'] == 3 ? "selected" : "" }}>Hủy</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_coc" id="nguoi_thu_coc">
                <option value="">--Người thu cọc--</option>
                <option value="1" {{ $arrSearch['nguoi_thu_coc'] == 1 ? "selected" : "" }}>Sales</option>
                <option value="2" {{ $arrSearch['nguoi_thu_coc'] == 2 ? "selected" : "" }}>CTY</option>
                <option value="3" {{ $arrSearch['nguoi_thu_coc'] == 3 ? "selected" : "" }}>HDV</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                <option value="">--Người thu tiền--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_tien'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="level" id="level">
                <option value="" >--Phân loại sales--</option>
                <option value="1" {{ $level == 1 ? "selected" : "" }}>Sales</option>
                <option value="2" {{ $level == 2 ? "selected" : "" }}>Đối tác</option>
                <option value="3" {{ $level == 3 ? "selected" : "" }}>Tài xế</option>
              </select>
            </div>
            <div class="form-group">
            <select class="form-control select2" id="hdv_id" name="hdv_id">
              <option value="">--HDV--</option>
              @foreach($listUser as $user)
              @if($user->hdv==1)
              <option value="{{ $user->id }}" @if($arrSearch['hdv_id'] == $user->id) selected @endif>{{ $user->name }}</option>
              @endif
              @endforeach
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
              <select class="form-control" name="hdv0" id="hdv0">
                <option value="2" {{ $arrSearch['hdv0'] == 2 ? "selected" : "" }}>Chưa chọn HDV</option>
                <option value="1" {{ $arrSearch['hdv0'] == 1 ? "selected" : "" }}>Đã chọn HDV</option>
              </select>
            </div>
              <div class="form-group">
                <input type="checkbox"name="ok" id="ok" {{ $arrSearch['ok'] == 1 ? "checked" : "" }} value="1">
                <label for="ok">OK</label>
              </div>
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>
        <!-- <p style="text-align: right;"><a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a> -->
      <div class="panel">
        <div class="panel-body">
          @foreach($arrHDV as $hdv_id => $arrBK)
            @if($hdv_id > 0)
            <span data-id="{{ $hdv_id }}" class="label label-default hdv @if($hdv_id == $arrSearch['hdv_id']) selected @endif" style="padding: 10px;margin-right: 10px; font-size: 13px">{{ $arrUser[$hdv_id]->name }}[{{ count($arrBK)}}]</span>
            @else
            <span data-id="" class="label label-default hdv" style="padding: 10px;margin-right: 10px; font-size: 13px">CHƯA CHỌN HDV[{{ count($arrBK)}}]</span>
            @endif
          @endforeach
        </div>
      </div>
      <div class="box">
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
        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} booking )</span>
            Số khách : {{ number_format($tong_so_nguoi )}} - Số phần ăn : {{ $tong_phan_an }} - Hoa hồng sales : {{ number_format($tong_hoa_hong_sales) }} - Tổng cọc : {{ number_format($tong_coc) }}  - Cáp NL: {{ $cap_nl }}  - Cáp TE: {{ $cap_te }}
          </h3>
        </div>

        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
          <table class="table table-bordered" id="table-list-data">
            <tr>
              <th style="width: 1%">#</th>
              <th style="width: 1%"><input type="checkbox" id="check_all" value="1"></th>
              <th width="200">Tên KH</th>
              <th width="100">UNC</th>
              <th style="width: 200px">Ghi chú</th>
              <th class="text-center" width="80">NL/TE/EB</th>
              <th class="text-right" style="width: 150px !important; white-space: nowrap;">HH Sales</th>

              <th class="text-right" width="100">Tổng tiền/Cọc</th>
              <th class="text-right" width="100">CÒN LẠI/<br>THỰC THU</th>

              <th class="text-center" width="100">Ngày đi</th>
              <th class="text-center" width="90">HDV</th>

              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}">
                <td>
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                </td>
                <td style="text-align: center;"><span class="order">{{ $i }}<br>
                  {{ date('d/m', strtotime($item->created_at)) }}
                </span></td>
                <td>      <strong style="color: red;font-size: 16px">PTT{{ $item->id }}</strong>
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
                  @elseif($item->tour_id == 2)
                  <label class="label label-info">2 đảo</label>
                  @endif
                  @if($item->tour_type == 3)
                  <label class="label label-warning">Thuê cano</label>
                  @elseif($item->tour_type == 2)
                  <label class="label label-danger">Tour VIP</label>
                  @endif


                  <br>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a style="font-size:17px" href="{{ route( 'booking.edit', $arrEdit) }}">{{ $item->name }}</a>
                  <br>
                  Sales: @if($item->user)
                  {{ $item->user->name }}
                  @else
                    {{ $item->user_id }}
                  @endif
                </td>
                <td>
                  @foreach($item->payment as $p)
                  @if($p->type == 1)
                  <img src="{{ Helper::showImageNew($p->image_url)}}" width="80" style="border: 1px solid red" class="img-unc" >
                  @else
                  <br>+ {{number_format($p->amount) }} lúc {{ date('d/m/Y', strtotime($p->created_at)) }}
                  @endif
                  @endforeach
                </td>
                <td>
                    @if($item->ko_cap_treo)
                    <span style="color:red">KHÔNG ĐI CÁP TREO</span><br>
                    @endif
                    <span style="color: red; font-style: italic">{{ $item->notes }}</span>
                </td>
                 <td class="text-center">
                  {{ $item->adults }} / {{ $item->childs }} / {{ $item->infants }}
                  <br>
                  <i class="  glyphicon glyphicon-briefcase"></i> {{ $item->meals }}
                </td>
                <td class="text-right" style="width: 130px">
                  @if(!in_array($item->user_id, [33,18,7,21]))
                  @if(!$item->hoa_hong_sales && $item->status != 3)
                  <!-- <input type="text" class="form-control hoa_hong_sales number" data-id="{{ $item->id }}" value="{{ $item->hoa_hong_sales ? number_format($item->hoa_hong_sales) : "" }}"> -->
                  @else
                   {{ number_format($item->hoa_hong_sales) }}
                  @endif
                  @endif



                </td>

                <td class="text-right">
                  {{ number_format($item->total_price) }}/{{ number_format($item->tien_coc) }}
                  @if($item->total_price_child > 0)
                  <br><span style="color:green">TE +{{ number_format($item->total_price_child) }}</span>
                  @endif

                  @if($item->extra_fee > 0)
                  <br><span style="color:blue">+{{ number_format($item->extra_fee) }}</span>
                  @endif
                  @if($item->discount > 0)
                  <br><span style="color:red">-{{ number_format($item->discount) }}</span>
                  @endif

                   <div class="form-group" style="margin-top: 5px">
                      <select class="form-control change-column-value" data-column="cap_nl" data-id="{{ $item->id }}">
                        <option value="">--CAP NL--</option>
                        @for($i = 0; $i <= 100; $i++)
                        <option value="{{ $i }}" {{ $i == $item->cap_nl ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>
                   <div class="form-group" style="margin-top: 5px">
                      <select class="form-control change-column-value" data-column="cap_te" data-id="{{ $item->id }}">
                        <option value="">--CAP TE--</option>
                        @for($i = 0; $i <= 30; $i++)
                        <option value="{{ $i }}" {{ $i == $item->cap_te ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                      </select>
                  </div>

                </td>
                <td class="text-right">
                  {{ number_format($item->con_lai) }}
                  <!-- <input type="text" style="text-align: right;margin-top: 10px; width: 100px" class="form-control change_tien_thuc_thu number" data-id="{{ $item->id }}" value="{{ $item->tien_thuc_thu ? number_format($item->tien_thuc_thu) : "" }}"> -->
                </td>
                <td class="text-center">
                  {{ date('d/m', strtotime($item->use_date)) }}
                </td>
                <td class="text-center">

                  <select style="width: 150px !important;" class="form-control select2 change-column-value" data-column="hdv_id" data-id="{{ $item->id }}">
                    <option value="">--HDV--</option>
                    @foreach($listUser as $user)
                    @if($user->hdv==1)
                    <option value="{{ $user->id }}" @if($item->hdv_id == $user->id) selected @endif>{{ $user->name }}</option>
                    @endif
                    @endforeach
                  </select>
                </td>
                <!-- <td class="text-right">
                  {{ number_format($item->hoa_hong_cty) }}
                </td> -->

                <td style="white-space:nowrap; position: relative;">
                <a href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"></span></a>
                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp
                  <a href="{{ route( 'booking.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
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
      $('.hdv').click(function(){
        var hdv_id = $(this).data('id');
        if(hdv_id != ""){
          $('#hdv_id').val($(this).data('id'));
          $('#searchForm').submit();
        }

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
                    console.log(data);
                }
              });
          });

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
    });
  </script>
@stop
