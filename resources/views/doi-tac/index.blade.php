@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Đối tác
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'doi-tac.index' ) }}">Đối tác</a></li>
    <li class="active">Danh sách</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div id="content_alert"></div>
      
      <a href="{{ route('doi-tac.create') }}" class="btn btn-success btn-sm" style="margin-bottom:5px">Tạo đối Tác</a>
      
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif

      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
      <div class="panel-body">
      
        <form class="form-inline" role="form" method="GET" action="{{ route('doi-tac.index') }}" id="searchForm">
          <div class="form-group">
            <select class="form-control select2 search-form-change" name="status" id="status">
              <option value="" >--Trạng thái-</option>
              <option value="1" {{ $status == 1 ? "selected" : "" }}>Hoạt động</option>
              <option value="2" {{ $status == 2 ? "selected" : "" }}>Tạm khóa</option>
            </select>
          </div>
          
          <div class="form-group">
            <input type="text" name="phone" value="{{ $phone }}" class="form-control" placeholder="Điện thoại" maxlength="10" autocomplete="off">
          </div>
        
          <button class="btn btn-info" type="submit">Lọc</button>
          </form>
      </div>
      </div>
      @endif

      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( {{ $items->total() }} )</h3>
        </div>
       
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-bordered  table-hover" id="table-list-data">
              <tr>
                <th style="width: 1%"><input type="checkbox" id="check_all" value="1"></th>
                <th style="width: 1%">#</th>
                <th>Tên hiển thị</th>                
                <th class="text-center">Điện thoại</th>            
                <th style="white-space:nowrap; width: 1%">Thao tác</th>
              </tr>
              <tbody>
              @if( $items->count() > 0 )
                <?php $i = 0; ?>
                @foreach( $items as $item )
                  <?php $i ++; ?>
                  <tr class="cost"  id="row-{{ $item->id }}">
                    <td>
                    <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                  </td>
                    <td><span class="order">{{ $i }}</span></td>
                    <td>
                      <a href="{{ route( 'doi-tac.edit', [ 'id' => $item->id ]) }}">{{ $item->name }}</a>
                      
                    </td>
                   
                    </td>
                   
                    <td class="text-center">{{ $item->phone }}</td>
                   
                    
                   
                    <td style="white-space:nowrap; text-align: right;">
                      
                      <a href="{{ route( 'doi-tac.update-status', ['status' => $item->status == 1 ? 2 : 1 , 'id' => $item->id ])}}" class="btn btn-sm {{ $item->status == 1 ? "btn-warning" : "btn-info" }}"
                      @if( $item->status == 2)
                      onclick="return confirm('Bạn chắc chắn muốn MỞ khóa tài khoản này? '); "
                      @else
                      onclick="return confirm('Bạn chắc chắn muốn KHÓA tài khoản này? '); "
                      @endif
                      >{{ $item->status == 1 ? "Khóa TK" : "Mở khóa TK" }}</a>


                      <a href="{{ route( 'doi-tac.edit', [ 'id' => $item->id ]) }}" class="btn-sm btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>
                      <a href="{{ route( 'doi-tac.kpi', [ 'id' => $item->id ]) }}" class="btn-sm btn btn-info">Set KPI</a>
                      <a onclick="return callDelete('{{ $item->fullname }}','{{ route( 'doi-tac.destroy', [ 'id' => $item->id ]) }}');" class="btn-sm btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>


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
   $('tr.cost').click(function(){
      $(this).find('.check_one').attr('checked', 'checked');
    });
    $("#check_all").click(function(){
        $('input.check_one').not(this).prop('checked', this.checked);
    });
    $('.multi-change-column-value').change(function(){
      var obj = $(this);
      $('.check_one:checked').each(function(){
          $.ajax({
            url : "{{ route('change-value-by-column-general') }}",
            type : 'GET',
            data : {
              id : $(this).val(),
              col : obj.data('column'),
              value: obj.val(),
              table: 'users'
            },
            success: function(data){

            }
          });
      });

   });
  $('#role, #level, #user_id_manage, #debt_type').change(function(){
    $(this).parents('form').submit();
  });
  $('.change-column-value').change(function(){
          var obj = $(this);
          $.ajax({
            url : "{{ route('change-value-by-column-general') }}",
            type : 'GET',
            data : {
              id : obj.data('id'),
              col : obj.data('column'),
              value: obj.val(),
              table : obj.data('table')
            },
            success: function(data){
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
