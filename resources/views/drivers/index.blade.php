@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Tài xế
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'drivers.index' ) }}">Tài xế</a></li>
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
      <a href="{{ route('drivers.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('drivers.index') }}" id="searchForm">
            <div class="form-group">              
              <select class="form-control select2" name="city_id" id="city_id">
                <option value="">-Tỉnh/Thành-</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}" {{ $arrSearch['city_id'] == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">              
              <select class="form-control select2" name="area_id" id="area_id">
                <option value="">--Khu vực--</option>
                @foreach($areaList as $area)
                <option value="{{ $area->id }}" {{ $arrSearch['area_id'] == $area->id ? "selected" : "" }}>{{ $area->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">              
              <select class="form-control select2" name="car_cate_id" id="car_cate_id">
                <option value="">--Loại xe--</option>
                @foreach($carCateList as $cate)
                <option value="{{ $cate->id }}" {{ $arrSearch['car_cate_id'] == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="form-group">
              <input type="text" class="form-control" name="name" value="{{ $arrSearch['name'] }}" placeholder="Tên"  style="width: 100px">
            </div>
            <div class="form-group">              
              <select class="form-control select2" name="is_verify" id="is_verify">
                <option value="-1">Tất cả</option>
                <option value="0" {{ $arrSearch['is_verify'] == 0 ? "selected" : "" }}>Chưa ký HĐ</option>
                <option value="1" {{ $arrSearch['is_verify'] == 1 ? "selected" : "" }}>Đã ký HĐ</option>
              </select>
            </div>             
            
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>         
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ({{ $items->total() }} tài xế)</h3>
        </div>
        
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">            
              {{ $items->appends($arrSearch)->links() }}
          </div>
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr>
              <th style="width: 1%">#</th>
              <th width="150px">Hình ảnh</th>
              <th>Tên</th> 
              <th>Loại xe</th>
              <th>Điện thoại</th>                                            
              <th>Khu vực</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}">
                <td><span class="order">{{ $i }}</span></td>
                <td>
                  <img class="lazy" width="120" data-original="{{ Helper::showImage($item->image_url) }}" class="img-responsive">
                </td>
                <td>                  
                  <a href="{{ route( 'drivers.edit', [ 'id' => $item->id ]) }}">{{ $item->name }} 
                    @if($item->is_verify == 1)
                    <span class="badge badge-success" style="background-color: green">Hợp đồng</span>
                    @endif
                  </a> 
                  <br>
                  @if($item->notes)
                  <br><span style="color: red; font-style: italic;">{{ $item->notes }}</span>
                  @endif               
                </td>   
                <td>
                  @if($item->car)
                  {{ $item->car->name }}
                  @endif
                </td>
                <td>
                  @if($item->phone)
                  <a href="tel:{{ $item->phone }}">{{ $item->phone }}</a>
                  @endif
                </td>
                <td>
                  <ul>
                    @foreach($item->area as $area)
                    <li>{{ $area->area->name }}</li>
                    @endforeach
                  </ul>
                </td>
                             
                <td style="white-space:nowrap">                
                  <a href="{{ route( 'drivers.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  <a onclick="return callDelete('{{ $item->name }}','{{ route( 'drivers.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
             
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
          <div style="text-align:center">
              {{ $items->appends($arrSearch)->links() }}
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
  $('.change-order').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('drivers.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'Tài xế'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
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
            updateOrder("cate_child", strOrder);
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