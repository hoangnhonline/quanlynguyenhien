@extends('layout')
@section('content')
<div class="content-wrapper">

<!-- Content Header (Page header) -->
<section class="content-header" style="position: relative;">
  <h1 style="text-transform: uppercase;">
    QUẢN LÝ TOUR
  </h1>
  <a href="{{ route('booking.create') }}" class="btn btn-info btn-sm" style="position: absolute;top:5px; right: 5px;">Tạo mới</a>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-md-12">   
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif    
      
  
      <div class="panel panel-default">
        <div class="panel-body">

          <form class="form-inline" role="form" method="GET" action="{{ route('booking.index') }}" id="searchForm">
            <input type="hidden" name="type" value="{{ $type }}">
             <div class="row">
               <div class="form-group col-xs-6"  style="padding-right: 0px;">
                <input type="text" class="form-control" autocomplete="off" name="id_search" value="{{ $arrSearch['id_search'] }}" placeholder="BOOKING ID" >
              </div>
              <div class="form-group col-xs-6" style="padding-left: 5px;">
                <input type="text" class="form-control" name="phone" autocomplete="off" value="{{ $arrSearch['phone'] }}" placeholder="Số ĐT">
              </div>
             </div>
            <div class="row">
            <div class="form-group col-xs-12">
                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
            </div>
            </div>
          <div class="row">
            <div class="form-group col-xs-6">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">-Đối tác-</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-xs-6" style="padding-left: 0px;">
              <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                <option value="">-Người thu tiền-</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_tien'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>
        </div>

         
            <div class="row" style="font-size: 12px;">
               <div class="form-group col-xs-4">
              <input type="checkbox" name="tour_type[]" id="tour_type_1" {{ in_array(1, $arrSearch['tour_type']) ? "checked" : "" }} value="1"><br>
              <label for="tour_type_1">GHÉP ({{ $ghep }} NL)</label>
            </div>
            <div class="form-group col-xs-4">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_2" {{ in_array(2, $arrSearch['tour_type']) ? "checked" : "" }} value="2"><br>
              <label for="tour_type_2">VIP ({{ $vip }}-{{ $tong_vip }}NL)</label>
            </div>
            <div class="form-group col-xs-4" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_3" {{ in_array(3, $arrSearch['tour_type']) ? "checked" : "" }} value="3"><br>
              <label for="tour_type_3">THUÊ CANO ({{ $thue }})</label>
            </div>
            </div>
           
            <div class="row">
              <div class="form-group col-xs-4">
                <input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
                <label for="status_1">MỚI</label>
              </div>
              <div class="form-group col-xs-4">
                <input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
                <label for="status_2">Hoàn tất</label>
              </div>
              <div class="form-group col-xs-4">
                <input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
                <label for="status_3">HỦY</label>
              </div>
              
              <div class="form-group col-xs-4">
                <input type="checkbox" name="no_cab" id="no_cab" {{ $arrSearch['no_cab'] == 1 ? "checked" : "" }} value="1">
                <label for="no_cab" style="color: red">Không cáp</label>
              </div>
              <div class="form-group col-xs-4">
                <input type="checkbox" name="no_meals" id="no_meals" {{ $arrSearch['no_meals'] == 1 ? "checked" : "" }} value="1">
                <label for="no_meals" style="color: red">Không ăn</label>
              </div>             
            </div>
            <button type="submit" class="btn btn-info btn-sm">Lọc</button>
            <button type="button" id="btnReset" class="btn btn-danger btn-sm">Reset</button>
          </form>
        </div>
      </div>
      <div style="background-color: #dbdbd5;" class="table-responsive">
          <table class="table table-bordered" id="table_report">
              <tr>
                <th class="text-center">Tổng BK</th>
                <th class="text-center">Tổng NL</th>
                <th class="text-center">Tổng TE</th>
                <th class="text-center">Ăn NL</th>
                <th class="text-center">Ăn TE</th>
                <th class="text-center">Cáp NL</th>
                <th class="text-center">Cáp TE</th>
        
                
              </tr>
              <tr>
                <td class="text-center">{{ number_format($items->count()) }}</td>
                <td class="text-center">{{ number_format($tong_so_nguoi ) }}</td>
                <td class="text-center">{{ number_format($tong_te ) }}</td>
                <td class="text-center">{{ number_format($tong_phan_an ) }}</td>
                <td class="text-center">{{ number_format($tong_phan_an_te ) }}</td>
                <td class="text-center">{{ number_format($cap_nl ) }}</td>
                <td class="text-center">{{ number_format($cap_te ) }}</td>          
                
              </tr>
          </table>
        </div>


        <div style="text-align:center; margin-top: 10px;">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive" style="font-size: 12px;border: none;">
            <ul style="padding: 0px; ">
             @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++;
                $allowEdit = $item->use_date >= $settingArr['date_block_booking'] ? true : false;
                ?>
                <li id="row-{{ $item->id }}" class="booking" style="padding: 10px;background-color: #fff; font-size:15px;margin-bottom: 10px; border-radius: 5px; color: #2c323f"  data-id="{{ $item->id }}" data-date="{{ $item->use_date }}">
                <span class="label label-sm label-danger" id="error_unc_{{ $item->id }}"></span>
                
                    @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp

                    <span style="color: red">
                        NH{{ $item->id }}</span>
                        @if($item->status == 1)
                    <span class="label label-info">MỚI</span>
                    @elseif($item->status == 2)
                    <span class="label label-default">HOÀN TẤT</span>
                    @elseif($item->status == 3)
                    <span class="label label-danger">HỦY</span>
                    @endif
                      @if($item->user)
                  - <span style="font-style: italic;">{{ $item->user->name }}</span>
                  @else
                    {{ $item->user_id }}
                  @endif</span>
                    <br>

                        <span class="name">
                       {{ $item->name }}</span>


                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp



                  <br>

                    <i class="glyphicon glyphicon-phone"></i> <a href="tel:{{ $item->phone }}" target="_blank">{{ $item->phone }}</a>
                    @if($item->tour_id)
                        @if($item->source == 'website')
                            <br><label class="label label-warning">{{ $item->tour->name }}</label>
                        @else
                            <br><label class="label" style="background-color:{{ @$tourSystemName[$item->tour_id]['bg_color'] }}">{{ @$tourSystemName[$item->tour_id]['name'] }}</label>
                        @endif
                    @endif
                    <br>
                    <i class="glyphicon glyphicon-map-marker"></i>
                    @if($item->location)
                    {{ $item->location->name }}
                    @else
                    {{ $item->address }}
                    @endif

                    <br>
                    <i class="glyphicon glyphicon-user"></i> NL: <b>{{ $item->adults }}</b> / TE: {{ $item->childs }} / EB: {{ $item->infants }} -
                    <?php
                    $meals = $item->meals;
                    if($meals > 0){
                      $meals+= $item->meals_te/2;
                    }

                    ?>
                    <i class="glyphicon glyphicon-briefcase"></i> {{ $meals }}
                    <br>

                    <i class="glyphicon glyphicon-usd"></i> {{ number_format($item->total_price) }}
                  </span>
                    
                    @if($item->ko_cap_treo)
                    <br><span style="color: red">KHÔNG CÁP</span>
                    @endif                  
                    <span style="color:red">{!! nl2br($item->notes) !!}</span>
                            
                  
                
                    <div class="clearfix"></div>
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1 && $allowEdit)
                  <a style="float:right; margin-left: 2px" onclick="return callDelete('{{ $item->title }}','{{ route( 'booking.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  
                   <a style="float:right; margin-left: 2px" href="{{ route( 'booking.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>

               
                
                      <div style="clear: both;"></div>
                </li>
              @endforeach
            @else
            <li>
              <p>Không có dữ liệu.</p>
            </li>
            @endif
            </ul>

          </div>
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
      </div>

    <!-- /.col -->
  </div>
</section>
<!-- /.content -->
</div>
<input type="hidden" id="table_name" value="articles">
<!-- Modal -->
<div id="capnhatModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" id="modal_content">

    </div>

  </div>
</div>
<style type="text/css">
  .form-group{
    margin-bottom: 10px !important;
  }
</style>
<div class="modal fade" id="maxiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="maxi_img" style="width: 100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('li.booking').each(function(){
        var tr = $(this);
        var id = tr.data('id');
        var use_date = tr.data('date');
        var today = new Date();
        if(use_date < "{{ date('Y-m-d') }}"){
          $.ajax({
            url : '{{ route('booking.check-unc') }}?id=' + id,
            type : 'GET',
            success : function(data){
              $('#error_unc_' + id).text(data);
            }
          });
        }
      });
    $('p.img-maxi').click(function(){
      $('#maxi_img').attr('src', "https://plantotravel.vn/" + $(this).data('image'));
      $('#maxiModal').modal('show');
    });
    $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
      });
    // setTimeout(function(){
    //   window.location.reload();
    // }, 30000);
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
  $(document).ready(function(){
      $(document).on('click', '.btn-edit-bk', function(){
        var id = $(this).data('id');
        $.ajax({
          url: '{{ route('booking.info') }}',
          type: "GET",
          data: {
              id : id
          },
          success: function(data){
              $('#modal_content').html(data);
              $('#capnhatModal').modal('show');
          }
      });
      });
      $(document).on('click', '#btnSaveInfo', function(){
        var hdv_id = $('#hdv_id').val();
        var hdv_notes = $('#hdv_notes').val();
        var booking_id = $('#booking_id').val();
        var call_status = $('#call_status').val();
        $.ajax({
          url: '{{ route('booking.save-info') }}',
          type: "GET",
          data: {
              hdv_id : hdv_id,
              hdv_notes : hdv_notes,
              booking_id : booking_id,
              call_status : call_status
          },
          success: function(data){
              $('#capnhatModal').modal('hide');
              window.location.reload();
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
