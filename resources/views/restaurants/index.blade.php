@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Nhà hàng
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'restaurants.index' ) }}">Nhà hàng</a></li>
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
      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
      <a href="{{ route('restaurants.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px"><i class="fa fa-plus" aria-hidden="true"></i> Tạo mới</a>
      @endif

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('restaurants.index') }}" id="searchForm">  <div class="form-group">

            <div class="form-group">
              <select class="form-control select2 search-form-change" name="city_id" id="city_id">
                <option value="">--Tỉnh/Thành--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}" {{ $city_id == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="name" value="{{ $name }}" placeholder="Tên nhà hàng...">
            </div>
             <div class="form-group" >
               <input type="checkbox" style="cursor: pointer;" name="co_chi" id="co_chi" {{ $co_chi == 1 ? "checked" : "" }} value="1" class="search-form-change">
                <label for="co_chi" style="cursor: pointer; color: red" >Có mã giảm giá</label>
              </div>
            <button class="btn btn-info" type="submit" style="margin-top:-5px"><i class="fa fa-search" aria-hidden="true"></i> Lọc</button>
          </form>
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} nhà hàng )</span></h3>
        </div>

        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( ['name' => $name, 'city_id' => $city_id] )->links() }}
          </div>
          <table class="table table-bordered" id="table-list-data">
            <tr>
              <th style="width: 1%">#</th>
              <th>Ảnh đại diện</th>
              <th>Tên nhà hàng</th>
              <th style="width: 1%; white-space: nowrap;">Thực đơn</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}">
                <td><span class="order">{{ $i }}</span></td>
                <td width="150">
                  @if($item->thumbnail)
                  <img class="img-thumbnail lazy" data-original="{{ Helper::showImage($item->thumbnail->image_url)}}" width="145">
                  @endif
                </td>
                <td style="#e8a23e">
                  <a style="font-size:17px" href="{{ route( 'restaurants.edit', [ 'id' => $item->id ]) }}">{{ $item->name }}</a>

                  @if( $item->is_hot == 1 )
                  <label class="label label-danger">HOT</label>
                  @endif
                  @if($item->co_chi == 1)
                  <br><a href="https://plantotravel.vn/coupon/{{ Helper::mahoa('mahoa', Auth::user()->code) }}/{{ Helper::mahoa('mahoa', $item->id) }}" target="_blank" style="font-size: 16px; color: #eea236"><i class="fa fa-gift" aria-hidden="true"></i> Lấy mã giảm giá </a>
                  <i class="fa fa-copy copyText" aria-hidden="true" title="Click để copy link" data-link="https://plantotravel.vn/coupon/{{ Helper::mahoa('mahoa', Auth::user()->code) }}/{{ Helper::mahoa('mahoa', $item->id) }}" style="cursor: pointer;"></i>
                  @endif
                </td>
                <td>
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <a href="{{ route( 'menu-cate.index', [ 'restaurant_id' => $item->id ]) }}" class="btn btn-info btn-sm" style="font-size: 16px">Thực đơn  @if($item->menuCate->count() > 0) [{{ $item->menuCate->count() }}] @endif </a>
                  @endif

                </td>
                <td style="white-space:nowrap;#e8a23e">
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                  <a href="{{ route( 'restaurants.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>

                  <a onclick="return callDelete('{{ $item->name }}','{{ route( 'restaurants.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif

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
            {{ $items->appends( ['name' => $name, 'city_id' => $city_id] )->links() }}
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
  $('i.copyText').click(function(){
    // Copy the text inside the text field
    navigator.clipboard.writeText($(this).data('link'));

    // Alert the copied text
    alert("Đã copy");
  });
     $('#cate_id, #type, #city_id, input[name=partner], #status, #stars').change(function(){
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
