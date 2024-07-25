@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Lịch sử thanh toán : Xe miễn phí <span class="hot">{{ $detailBooking->booking->name }} - {{ $detailBooking->booking->phone }}</span>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking-xe-free.index' ) }}">
    Lịch sử thanh toán</a></li>
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
      <a href="{{ $back_url ?? route('booking-xe-free.index') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Quay lại</a>    
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Danh sách giao dịch</span></h3>
        </div>
        
        <!-- /.box-header -->
        <div class="box-body">
        
          <table class="table table-bordered" id="table-list-data">
            <tr>
              <th style="width: 1%">#</th>                        
              <th width="1%" style="white-space: nowrap;">Phân loại</th>
              <th>Ngày chuyển</th>             
              <th>Số tiền</th>
              <th>Hình ảnh/SMS</th>
              <th>Người thu/chi</th>
              <th style="white-space:nowrap" width="1%">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}">
                <td><span class="order">{{ $i }}</span></td>   
                 <td width="1%" class="text-center">
                  {{ $item->flow == 1 ? "THU" : "CHI" }}
                </td>
                <td width="150">
                  {{ date('d/m/Y', strtotime($item->pay_date)) }}
                </td>        
               
                <td>                  
                 {{ number_format($item->amount) }}<br>
                 
                </td>
                <td>
                  @if($item->type == 1)
                  <a href="{{ config('plantotravel.upload_url').$item->image_url }}" target="_blank"><img src="{{ config('plantotravel.upload_url').$item->image_url }}" height="200"></a>
                  @else
                    {{ $item->notes }}
                  @endif
                </td>
                <td>
                  <span class="hot">{{ isset($collecterNameArr[$item->collecter_id]) ? $collecterNameArr[$item->collecter_id] : "" }}</span>
                </td>
                <td style="white-space:nowrap">
                @if($item->type == 1)                            
                <!--   <a href="{{ route( 'booking-payment.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>  -->                
                  
                  <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking-payment.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
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
      <!-- /.box -->     
    </div>
    <!-- /.col -->  
  </div> 
  <form role="form" method="POST" action="{{ route('don-tien-free-payment.store') }}" id="dataForm">
    <div class="row">
       <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Tạo mới</h3>
            </div>
            <!-- /.box-header -->               
              {!! csrf_field() !!}

              <div class="box-body">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif                
                <input type="hidden" name="don_tien_id" value="{{ $detailBooking->id }}">
                  <div class="form-group" >                  
                    <label>Phân loại<span class="red-star">*</span></label>
                    <select name="flow" class="form-control select2">
                      <option value="">--Chọn--</option>
                      <option value="1" {{ old('flow') == 1 ? "selected" : "" }}>THU</option>
                      <option value="2" {{ old('flow') == 2 ? "selected" : "" }}>CHI</option>
                    </select>
                  </div>
                  <div class="form-group" >                  
                    <label>Ngày chuyển <span class="red-star">*</span></label>
                    <input type="text" class="form-control datepicker" name="pay_date" id="pay_date" value="{{ old('pay_date') }}"  autocomplete="off">
                  </div>                
                  <div class="form-group" >                  
                    <label>Số tiền <span class="red-star">*</span></label>
                    <input type="text" class="form-control number" name="amount" id="amount" value="{{ old('amount') }}" autocomplete="off">
                  </div> 
                  <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                  <label class="col-md-3 row">Hình ảnh </label>    
                  <div class="col-md-9">
                    <img id="thumbnail_image" src="{{ old('image_url') ? Helper::showImage(old('image_url')) : URL::asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="145" height="85">
                    
                    <input type="file" id="file-image" style="display:none" />
                 
                    <button class="btn btn-default" id="btnUploadImage" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                  </div>
                  <div style="clear:both"></div>
                  <input type="hidden" name="image_url" id="image_url" value="{{ old('image_url') }}"/>          
                  <input type="hidden" name="image_name" id="image_name" value="{{ old('image_name') }}"/>
                </div>                               
                  
                  <div style="clear:both"></div>              
            
                  <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea class="form-control" rows="6" name="notes" id="notes">{{ old('notes') }}</textarea>
                  </div>            
                  <div class="form-group">
                    <label>Người thu/chi</label>
                    <select class="form-control select2" name="collecter_id" id="collecter_id">
                      <option value="">--Chọn--</option>
                      @foreach($collecterList as $col)
                      <option value="{{ $col->id }}" {{ old('collecter_id') == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                      @endforeach
                    </select>
                  </div>                 
              </div>          
                                
              <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('booking-payment.index', ['don_tien_id' => $don_tien_id])}}">Hủy</a>
              </div>
              
          </div>
          <!-- /.box -->     

        </div>
    </div>
  </form>
</section>
<!-- /.content -->
</div>
<input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">
@stop

@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('#btnUploadImage').click(function(){        
        $('#file-image').click();
      });      
      var files = "";
      $('#file-image').change(function(e){
        $('#thumbnail_image').attr('src', "{{ URL::asset('admin/dist/img/loading.gif') }}");
         files = e.target.files;
         
         if(files != ''){
           var dataForm = new FormData();        
          $.each(files, function(key, value) {
             dataForm.append('file', value);
          });   
          
          dataForm.append('date_dir', 1);
          dataForm.append('folder', 'tmp');

          $.ajax({
            url: $('#route_upload_tmp_image').val(),
            type: "POST",
            async: false,      
            data: dataForm,
            processData: false,
            contentType: false,
            beforeSend : function(){
              $('#thumbnail_image').attr('src', "{{ URL::asset('admin/dist/img/loading.gif') }}");
            },
            success: function (response) {
              if(response.image_path){
                $('#thumbnail_image').attr('src',$('#upload_url').val() + response.image_path);
                $( '#image_url' ).val( response.image_path );
                $( '#image_name' ).val( response.image_name );
              }
              console.log(response.image_path);
                //window.location.reload();
            },
            error: function(response){                             
                var errors = response.responseJSON;
                for (var key in errors) {
                  
                }
                //$('#btnLoading').hide();
                //$('#btnSave').show();
            }
          });
        }
      });
  });
</script>
@stop