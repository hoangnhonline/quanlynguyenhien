@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    @if($partner == 1)
    Đối tác đặt phòng
    @else
    Khách sạn
    @endif
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'hotel.index' ) }}">Khách sạn</a></li>
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
      <a href="{{ route('hotel.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>     
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('hotel.index') }}">
            
            <div class="form-group">
              <label for="partner1" style="margin-right: 10px">
                <input id="partner1" type="radio" name="partner" value="0"  {{ $partner == 0 ? "checked" : "" }}> Hotel/Resort
              </label>
              <label for="partner2" style="margin-right: 10px">
                <input id="partner2" type="radio" name="partner" value="1"  {{ $partner == 1 ? "checked" : "" }}> Đối tác
              </label>
            </div>  
            <div class="form-group">             
              <select class="form-control select2" name="city_id" id="city_id">
                <option value="">--Tỉnh/Thành--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}" {{ $city_id == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                @endforeach
              </select>
            </div>                
            <div class="form-group">            
              <select class="form-control select2" name="stars" id="stars">
                <option value="">--Số sao--</option>
                @for($i = 1; $i <= 7; $i++)
                <option value="{{ $i }}" {{ $stars == $i ? "selected" : "" }}>{{ $i }}</option>
                @endfor
              </select>
            </div> 
            <div class="form-group">              
              <input type="text" class="form-control" placeholder="Tên" name="name" value="{{ $name }}">
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status" id="status" {{ $status == 2 ? "checked" : "" }} value="2">
              <label for="status" style="color: red">Ẩn&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div> 
            <button type="submit" class="btn btn-info btn-sm">Lọc</button>
          </form>         
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} khách sạn )</span></h3>
        </div>
        
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( ['name' => $name, 'city_id' => $city_id, 'stars' => $stars, 'partner' => $partner] )->links() }}
          </div>  
          <table class="table table-bordered" id="table-list-data">
            <tr>
              <th style="width: 1%">#</th>              
              <!-- <th>Ảnh đại diện</th> -->
              <th>Tên khách sạn</th>
              <th class="text-center">Số sao</th>
              <th class="text-center">Loại phòng</th>
              <th class="text-center" width="150px">Trạng thái</th>              
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}">
                <td><span class="order">{{ $i }}</span></td>       
                <td width="150" style="display: none;">
                  @if($item->thumbnail)
                  <img class="img-thumbnail lazy" data-original="{{ Helper::showImage($item->thumbnail->image_url)}}" width="145">
                  @endif
                </td>        
                <td style="vertical-align: top">                  
                  <a style="font-size:17px" href="{{ route( 'hotel.edit', [ 'id' => $item->id ]) }}">{{ $item->name }}</a>
                  
                  @if( $item->is_hot == 1 )
                  <label class="label label-danger">HOT</label>
                  @endif  

                </td>
                <td class="text-center">
                  {{ $item->stars }} sao
                </td>

                <td class="text-center">
                  <a href="{{ route('room.index', ['hotel_id' => $item->id]) }}"> 
                  @if($item->rooms->count() > 0)
                  <span style="color: red;font-weight: bold;">{{ $item->rooms->count() }}</span>
                  @endif
                  Loại phòng</a>
                </td>
                <td class="text-center">
                  <div class="form-group">                                          
                     @if($item->status == 1)                                         
                      <input style="cursor: pointer;" id="status_{{ $item->id }}" type="checkbox" name="status_{{ $item->id }}" class="change-column-value" data-table="hotels" data-reload="1" data-column="status" data-name="{{ $item->name }}" data-action="ẨN" value="2" data-id="{{ $item->id }}" {{ $item->status == 2 ? "checked" : "" }}> 
                    <label for="status_{{ $item->id }}">Ẩn</label>
                    @else
                    <input style="cursor: pointer;" id="status_{{ $item->id }}" type="checkbox" name="status_{{ $item->id }}" class="change-column-value" data-table="hotels" data-reload="1" data-column="status" data-name="{{ $item->name }}" data-action="HIỆN" value="1" data-id="{{ $item->id }}" {{ $item->status == 1 ? "checked" : "" }}> 
                    <label for="status_{{ $item->id }}">Hiện</label>
                    @endif
                  </div>
                </td>
                <td style="white-space:nowrap;vertical-align: top">                                
                  <a href="{{ route( 'hotel.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>                 
                  
                  <a onclick="return callDelete('{{ $item->title }}','{{ route( 'hotel.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  
                </td>
              </tr> 
              @endforeach
            @else
            <tr>
              <td colspan="4">Không có dữ liệu.</td>
            </tr>
            @endif

          </tbody>
          </table>
          <div style="text-align:center">
            {{ $items->appends( ['name' => $name, 'city_id' => $city_id, 'stars' => $stars, 'partner' => $partner] )->links() }}
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
@stop
@section('js')

<script type="text/javascript">
function callDelete(name, url){  
  swal({
    title: 'Bạn muốn xóa "' + name +'"?',
    text: "Dữ liệu sẽ không thể phục hồi.",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes'
  }).then(function() {
    location.href= url;
  })
  return flag;
}
$(document).ready(function(){
    $('#cate_id, #type, #city_id, input[name=partner], #status').change(function(){
      $(this).parents('form').submit();
    });
  $('#parent_id').change(function(){
    $.ajax({
        url: $('#route_get_cate_by_parent').val(),
        type: "POST",
        async: false,
        data: {          
            parent_id : $(this).val(),
            type : 'list'
        },
        success: function(data){
            $('#cate_id').html(data).select2('refresh');                      
        }
    });
  });
  $('.select2').select2();

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
            for (var i=0; i<rows.length; i++) {
                strTemp = rows[i].id;
                strOrder += strTemp.replace('row-','') + ";";
            }     
            updateOrder("loai_sp", strOrder);
        }
    });
});
function updateOrder(table, strOrder){
  $.ajax({
      url: $('#route_update_order').val(),
      type: "POST",
      async: false,
      data: {          
          str_order : strOrder,
          table : table
      },
      success: function(data){
          var countRow = $('#table-list-data tbody tr span.order').length;
          for(var i = 0 ; i < countRow ; i ++ ){
              $('span.order').eq(i).html(i+1);
          }                        
      }
  });
}
</script>
@stop