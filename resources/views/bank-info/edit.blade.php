@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Tài khoản ngân hàng đối tác
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('bank-info.index') }}">Tài khoản ngân hàng đối tác</a></li>
      <li class="active">Cập nhật</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('bank-info.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('bank-info.update') }}" id="dataForm">
      <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="row">
      <!-- left column -->

      <div class="col-md-12">
        <div id="content_alert"></div>
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Cập nhật</h3>
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

            <input type="hidden" name="type" value="1">
           <!-- text input -->
          <div class="col-md-12">
            <div class="form-group">
              <label>Tên đối tác<span class="red-star">*</span></label>
              <input type="text" autocomplete="off" class="form-control" id="name" value="{{ old('name', $detail->name) }}" name="name"></textarea>
            </div>
            <div class="form-group">
              <label>Tên ngân hàng<span class="red-star">*</span></label>
              <input type="text" autocomplete="off" class="form-control"  value="{{ old('bank_name', $detail->bank_name) }}"></textarea>
            </div>
            <div class="form-group">
              <label>Tên ngân hàng<span class="red-star">*</span></label>
                <select class="form-control select2" id="add_bank_name" name="bank_name" style="width: 100%">
                  <option value="">--Chọn--</option>
                    @foreach($vietNameBanks as $bank)
                        <option value="{{$bank['shortName']}}" {{ $bank['shortName'] == old('bank_name', $detail->bank_name) ? "selected" : "" }}>{{$bank['shortName']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
              <label>Chủ tài khoản<span class="red-star">*</span></label>
              <input type="text" autocomplete="off" class="form-control" id="account_name" value="{{ old('account_name', $detail->account_name) }}" name="account_name"></textarea>
            </div>
            <div class="form-group">
              <label>Số tài khoản<span class="red-star">*</span></label>
              <input type="text" autocomplete="off" class="form-control" id="bank_no" value="{{ old('bank_no', $detail->bank_no) }}" name="bank_no"></textarea>
            </div>
            <div class="form-group">
              <label>Chi nhánh</label>
              <input type="text" autocomplete="off" class="form-control" id="bank_branch" value="{{ old('bank_branch', $detail->bank_branch) }}" name="bank_branch"></textarea>
            </div>

            <div class="box-footer col-md-12">
              <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('cost.index')}}">Hủy</a>
            </div>
        </div>
        <!-- /.box -->

      </div>
      <div class="col-md-7">

    </div>
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">
<div id="modalNewBankInfo" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    <form method="POST" action="{{ route('bank-info.ajax-save')}}" id="formAjaxBankInfo">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tạo mới tài khoản ngân hàng đối tác</h4>
      </div>
      <div class="modal-body" id="contentTag">
          <input type="hidden" name="type" value="1">
           <!-- text input -->
          <div class="col-md-12">
            <div class="form-group">
              <label>Tên đối tác<span class="red-star">*</span></label>
              <input type="text" autocomplete="off" class="form-control" id="add_name" value="{{ old('name') }}" name="name"></textarea>
            </div>
            <div class="form-group">
              <label>Tên ngân hàng<span class="red-star">*</span></label>
                <select class="form-control" id="add_bank_name" name="bank_name">
                    @foreach($vietNameBanks as $bank)
                        <option value="{{$bank['shortName']}}">{{$bank['shortName']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
              <label>Chủ tài khoản<span class="red-star">*</span></label>
              <input type="text" autocomplete="off" class="form-control" id="add_account_name" value="{{ old('account_name') }}" name="account_name"></textarea>
            </div>
            <div class="form-group">
              <label>Số tài khoản<span class="red-star">*</span></label>
              <input type="text" autocomplete="off" class="form-control" id="add_bank_no" value="{{ old('bank_no') }}" name="bank_no"></textarea>
            </div>
            <div class="form-group">
              <label>Chi nhánh</label>
              <input type="text" autocomplete="off" class="form-control" id="bank_branch" value="{{ old('bank_branch') }}" name="bank_branch"></textarea>
            </div>


          </div>
          <div classs="clearfix"></div>
      </div>
      <div style="clear:both"></div>
      <div class="modal-footer" style="text-align:center">
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveBankAjax"> Save</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="btnCloseModalTag">Close</button>
      </div>
      </form>
    </div>

  </div>
</div>
@stop
@section('js')
<script type="text/javascript">
  $(document).on('click', '#btnSaveBankAjax', function(){
    $(this).attr('disabled', 'disabled');
      $.ajax({
        url : $('#formAjaxBankInfo').attr('action'),
        data: $('#formAjaxBankInfo').serialize(),
        type : "post",
        success : function(id){
          $('#btnCloseModalTag').click();
          $.ajax({
            url : "{{ route('bank-info.ajax-list') }}",
            data: {
              id : id
            },
            type : "get",
            success : function(data){
                $('#bank_info_id').html(data);
                $('#bank_info_id').select2('refresh');
            }
          });
        },error: function (error) {
          var errrorMess = jQuery.parseJSON(error.responseText);
          if(errrorMess.message == 'The given data was invalid.'){
            alert('Nhập đầy đủ thông tin có dấu *');
            $('#btnSaveBankAjax').removeAttr('disabled');
          }
          //console.log(error);
      }
      });
   });
  $(document).ready(function(){

  });
  $(document).ready(function(){
    $('.tinh-toan .amount, .tinh-toan .gia').blur(function(){
      var parent = $(this).parents('.tinh-toan');
      tinhtoangia(parent);
    });
  });
  function tinhtong(){
    var tong = 0;
    $('.total').each(function(){
      var total = parseInt($(this).val());
      if(total > 0){
        tong += total;
      }
    });
    $('#total_money').val(tong);
  }
  function tinhtoangia(parent){
      var amount = parent.find('.amount').val();
      var gia = parent.find('.gia').val();
      var total = gia*amount;
      parent.find('.total').val(total);
      tinhtong();
  }
</script>
<script type="text/javascript">
  $(document).ready(function(){
     $('#btnAddBankInfo').click(function(){
          $('#modalNewBankInfo').modal('show');
      });
    $('#cate_id').click(function(){
      $.ajax({
        url : "{{ route('media.ajax-store') }}",
        type : 'GET',
        data : $('#formAjax').serialize(),
        success: function(data){
          alert('Lưu thành công!');
          window.location.reload();
        }
      });
    });
    $('#btnUploadUnc').click(function(){
        $('#file-unc').click();
      });
      var files = "";
      $('#file-unc').change(function(e){
        $('#thumbnail_unc').attr('src', "{{ URL::asset('admin/dist/img/loading.gif') }}");
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
              $('#thumbnail_unc').attr('src', "{{ URL::asset('admin/dist/img/loading.gif') }}");
            },
            success: function (response) {
              if(response.image_path){
                $('#thumbnail_unc').attr('src',$('#upload_url').val() + response.image_path);
                $( '#unc_url' ).val( response.image_path );
                $( '#unc_name' ).val( response.image_name );
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
