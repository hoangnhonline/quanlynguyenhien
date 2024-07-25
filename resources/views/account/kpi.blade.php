@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      SET KPI CHO {{ $detail->name }}
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
    <form role="form" method="POST" action="{{ route('account.store-kpi') }}" id="formData">
      <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="row">
      <!-- left column -->

      <div class="col-md-7">
        <div id="content_alert"></div>
        @if(Session::has('message'))
        <p class="alert alert-info" >{{ Session::get('message') }}</p>
        @endif
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

                <div class="row">
                  <p class="col-md-12" style="color: red;font-style: italic; font-weight: bold">Chọn tháng và năm để xem chi tiết hoặc điều chỉnh KPI đã thiết lập.</p>
                  <div class="form-group col-md-6">                
                    <select class="form-control select2 " id="month_change" name="month_apply">
                      <option value="">--THÁNG--</option>
                      @for($i = 1; $i <=12; $i++)
                      <option value="{{ $i }}" {{ old('month_apply', $month) == $i ? "selected" : "" }}>Tháng {{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                      @endfor
                    </select>
                </div>
                <div class="form-group col-md-6">                
                  <select class="form-control select2" id="year_change" name="year_apply">
                    <option value="">--NĂM--</option>                  
                    <option value="2023" {{ old('year_apply', $year) == 2023 ? "selected" : "" }}>Năm 2023</option>
                    <option value="2024" {{ old('year_apply', $year) == 2024 ? "selected" : "" }}>Năm 2024</option>
                    <option value="2025" {{ old('year_apply', $year) == 2025 ? "selected" : "" }}>Năm 2025</option>
                  </select>
                </div>
                </div>              
                
                <div class="form-group" style="margin-top: 15px;">
                  <table class="table table-bordered table-hover">
                    <tr style="background-color: #ccc">
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
                        <input type="text" name="amount[]" value="{{ isset($kpiArr[$tour->id]) ? $kpiArr[$tour->id] : "" }}" class="number form-control" >
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
      $('#month_change, #year_change').change(function(){
        location.href = "{{ route('account.kpi', ['id' => $detail->id]) }}?month=" + $('#month_change').val() + '&year=' + $('#year_change').val()
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
