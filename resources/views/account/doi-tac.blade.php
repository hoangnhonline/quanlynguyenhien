@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Đối tác lưu trú
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'account.doi-tac' ) }}">Thành viên</a></li>
    <li class="active">Danh sách</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div id="content_alert"></div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( {{ $items->total() }} )</h3>
        </div>       
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-bordered  table-hover" id="table-list-data">
              <tr>
                <th style="width: 1%"><input type="checkbox" id="check_all" value="1"></th>
                <th style="width: 1%">#</th>              
                <th>Ngày liên hệ</th>
                <th>Ngày ký HĐ</th>
                <th>NV Phụ trách</th>                
                <th>Trạng thái</th>                
                <th>Tên KS</th>
                <th class="text-center">Hạng sao</th>
                <th>Khu vực</th>                
                <th>Công suất</th>
                <th>Mặt biển</th>                
                <th>Hồ bơi</th>  
                <th>Địa chỉ</th>                
                <th class="text-center">Người phụ trách</th>
                <th class="text-center">Email</th>
                <th class="text-center">Điện thoại</th>
              </tr>
              <tbody>
              @if( $items->count() > 0 )
                <?php $i = 0; ?>
                @foreach( $items as $item )
                  <?php $i ++; ?>
                  <tr class="cost"  id="row-{{ $item->hotel_id }}">
                    <td>
                    <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                  </td> 
                    <td><span class="order">{{ $i }}</span></td>                   
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                      @if($item->userManage)
                      {{ $item->userManage->name }}
                      @endif
                    </td>                   
                    <td>
                      <!--trang thai-->
                    </td>
                    <!-- <td>{{ $item->email }}</td> -->
                    <td class="text-left">{{ $item->hotel->name }}</td>
                    <td class="text-center" >
                     {{ $item->hotel->stars }}
                    </td>
                    <th class="text-center">{{ $item->hotel->khu_vuc }}</th>
                    <th class="text-center">{{ $item->hotel->cong_suat }}</th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center">{{ $item->hotel->address }}</th>
                    <th class="text-center">{{ $item->hotel->name_contact ?? $item->name }}</th>
                    <th class="text-center">{{ $item->hotel->email_contact }}</th>
                    <th class="text-center">{{ $item->hotel->phone_contact }}</th>                    
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