@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Cập nhật tài khoản
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('account.index') }}">Tài khoản</a></li>
      <li class="active">Tạo mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('account.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('account.update') }}" id="formData">
      <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="row">
      <!-- left column -->

      <div class="col-md-7">
        <div id="content_alert"></div>
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
                <!-- text input -->
                <div class="row">
                  <div class="form-group col-md-6">
                    <label>CODE <span class="red-star">*</span></label><span style="color: red; cursor: pointer; background-color: red; color: #FFF; padding: 5px;margin-left: 10px;" id="new_code">Tạo code mới</span>
                    <input type="text" class="form-control" name="code" id="code" value="{{ old('code', $detail->code) }}">
                  </div>
                  <div class="form-group col-md-6">
                        <label for="city_id">Tỉnh/Thành</label>
                        <select class="form-control select2" name="city_id" id="city_id">
                          <option value="">--Chọn--</option>
                          @foreach($cityList as $city)
                          <option value="{{ $city->id }}" {{ old('city_id', $detail->city_id) == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                          @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row">
                  
                  <div class="form-group col-md-12">
                    <label>Phân loại công nợ</label>
                    <select class="form-control select2" name="debt_type" id="debt_type">      
                      <option value="" >--Chọn--</option>                       
                      <option value="1" {{ old('debt_type', $detail->debt_type) == 1 ? "selected" : "" }}>Ngày</option>  
                      <option value="2" {{ old('debt_type', $detail->debt_type) == 2 ? "selected" : "" }}>Tuần</option>  
                      <option value="3" {{ old('debt_type', $detail->debt_type) == 3 ? "selected" : "" }}>Tháng</option>
                    </select>
                  </div> 
                </div>
                <div class="row">
                  <div class="form-group col-md-6">
                      <label>Số điện thoại <span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Tên hiển thị<span class="red-star">*</span></label>
                      <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $detail->name) }}">
                    </div> 
                </div>
                <div class="row">
                  <div class="form-group col-md-6">
                    <label>Email<span class="red-star">*</span></label>
                    <input type="text" class="form-control" name="email" id="email" value="{{ old('email', $detail->email) }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Trạng thái</label>
                    <select class="form-control select2" name="status" id="status">
                      <option value="1" {{ old('status', $detail->status) == 1 ? "selected" : "" }}>Hoạt động</option>
                      <option value="2" {{ old('status', $detail->status) == 2 ? "selected" : "" }}>Tạm khóa</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-6">
                    <label>NV phụ trách</label>
                    <select class="form-control" name="user_id_manage" id="user_id_manage">      
                      <option value="" >--Chọn--</option>                       
                      <option value="84" {{ old('user_id_manage', $detail->user_id_manage) == 84 ? "selected" : "" }}>Lâm Như</option>  
                      <option value="219" {{ old('user_id_manage', $detail->user_id_manage) == 219 ? "selected" : "" }}>Trang Tạ</option>  
                      <option value="333" {{ old('user_id_manage', $detail->user_id_manage) == 333 ? "selected" : "" }}>Group Tour</option>
                      <option value="451" {{ old('user_id_manage', $detail->user_id_manage) == 451 ? "selected" : "" }}>Thảo Lê</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label>Phân loại sales</label>
                    <select class="form-control select2" name="level" id="level">      
                      <option value="" >--Chọn level--</option>                       
                      <option value="1" {{ old('level', $detail->level) == 1 ? "selected" : "" }}>CTV GROUP</option>  
                      <option value="2" {{ old('level', $detail->level) == 2 ? "selected" : "" }}>ĐỐI TÁC</option>
                      <option value="6" {{ old('level', $detail->level) == 6 ? "selected" : "" }}>NV SALES</option>
                      <option value="7" {{ old('level', $detail->level) == 7 ? "selected" : "" }}>GỬI BẾN</option>
                    </select>
                  </div>
                </div>                   
                            
                <div class="clearfix"></div>                     
                
                <div class="form-group" style="margin-top:10px;margin-bottom:10px">  
                  <label class="col-md-3 row">Ảnh Avatar</label>    
                  <div class="col-md-9">
                    <img id="thumbnail_image" src="{{ $detail->image_url ? Helper::showImage($detail->image_url ) : asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="145" height="85">
                 
                    <button class="btn btn-default btn-sm btnSingleUpload" data-set="image_url" data-image="thumbnail_image" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                  </div>
                  <div style="clear:both"></div>
                </div>
                <div class="form-group">
                  <table class="table table-bordered">
                    <tr>
                      <th>Tour</th>
                      <th>Mốc KPI</th>
                    </tr>
                    @foreach($tourSystem as $tour)
                    <tr>
                      <td>
                        {{ $tour->name }}
                      </td>
                      <td>
                        <input type="hidden" name="tour_id[]" value="{{ $tour->id }}">
                        <input type="text" name="amount[]" value="{{ isset($arrKpi[$tour->id]) ? $arrKpi[$tour->id] : "" }}" class="number form-control" >
                      </td>
                    </tr>
                    @endforeach
                  </table>
                </div>
                <input type="hidden" name="image_url" id="image_url" value="{{ $detail->image_url }}"/>
            </div>
            <div class="box-footer">             
              <button type="submit" class="btn btn-primary btn-sm" id="btnSave">Lưu</button>
              <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('account.index')}}">Hủy</a>
            </div>
            
        </div>
        <!-- /.box -->     

      </div>
      
      <!--/.col (left) -->      
    </div>
    </form>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@stop
@section('js')
<script type="text/javascript">
    $(document).ready(function(){
      $('#new_code').click(function(){
        var code = makeid(5);
        $('#code').val(code);
      });
      $('#formData').submit(function(){
        $('#btnSave').html('<i class="fa fa-spinner fa-spin">').attr('disabled', 'disabled');
      });      
    });
    function makeid(length) {
     var result           = '';
     var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
     var charactersLength = characters.length;
     for ( var i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
     }
   return result;
}
</script>
@stop
