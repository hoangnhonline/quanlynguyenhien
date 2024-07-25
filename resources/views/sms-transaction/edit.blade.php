@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Cập nhật SMS Giao dịch
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('sms-transaction.index') }}">Tài khoản</a></li>
      <li class="active">Tạo mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('sms-transaction.index') }}" style="margin-bottom:5px">Quay lại</a>
    <form role="form" method="POST" action="{{ route('sms-transaction.update') }}" id="formData">
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
                <!-- text input -->
                <div class="row">
                 <div class="form-group col-md-12">
                   <table class="table table-bordered">
                    <tr>
                      <th width="150">Số lệnh</th>
                      <td>{{ $detail->transaction_no }}</td>
                    </tr>
                    <tr>
                      <th>Số tiền</th>
                      <td>{{ number_format($detail->so_tien) }}</td>
                    </tr>
                    <tr>
                      <th>Phân loại</th>
                      <td>{{ $detail->cate_id > 0  ? $arrPhanLoai[$detail->cate_id] : "" }}</td>
                    </tr>
                    <tr>
                      <th>Tài khoản</th>
                      <td>{{ $detail->tai_khoan_doi_tac }}</td>
                    </tr>
                    <tr>
                      <th>Ngân hàng</th>
                      <td>
                        {{ $detail->ngan_hang_doi_tac }}
                      </td>
                    </tr>
                    <tr>
                      <th>Tên</th>
                      <td>{{ $detail->ten_doi_tac }}</td>
                    </tr>
                    <tr>
                      <th>Nội dung</th>                      
                      <td>{{ $detail->noi_dung }}</td>
                    </tr>
                    <tr>
                      <th>Ngày</th>                      
                      <td>{{ date('d/m/Y', strtotime($detail->ngay_giao_dich)) }}</td>
                    </tr>
                    <?php 
                    if($detail->type == 1){
                        $dau = "+";
                    }else{
                        $dau = "-";
                    }
                    $sms_luu = "TK 0938766885 GD: ".$dau.number_format($detail->so_tien)."VND ".date('d/m/Y', strtotime($detail->ngay_giao_dich))." 00:00 SD:0VND ND: ".$detail->noi_dung." Tu: ". $detail->tai_khoan_doi_tac ." ".$detail->ten_doi_tac;
                    ?>
                    <tr>
                      <th>SMS</th>                      
                      <td>{{ $sms_luu }}</td>
                    </tr>
                  </table>
                 </div> 
                 <div class="form-group col-md-3" >                  
                  <label>CODE NỘP TIỀN TOUR</label>
                  <input type="text" class="form-control req" name="code_nop_tien" id="code_nop_tien" value="{{ old('code_nop_tien', $detail->code_nop_tien) }}">
                </div> 
                <div class="form-group col-md-3" >                  
                  <label>CODE NỘP KHOẢN THU KHÁC</label>
                  <input type="text" class="form-control req" name="code_nop_khac" id="code_nop_khac" value="{{ old('code_nop_khac', $detail->code_nop_khac) }}">
                </div>   
                 <div class="form-group col-md-3" >                  
                  <label>CODE ỨNG TIỀN CHI PHÍ</label>
                  <input type="text" class="form-control req" name="code_ung_cost" id="code_ung_cost" value="{{ old('code_ung_cost', $detail->code_ung_cost) }}">
                </div>  
                <div class="form-group col-md-3" >                  
                  <label>CODE ỨNG TIỀN YCTT</label>
                  <input type="text" class="form-control req" name="code_ung_pay" id="code_ung_pay" value="{{ old('code_ung_pay', $detail->code_ung_pay) }}">
                </div>  
                 <div class="form-group col-md-12" >                  
                  <label>Các mã YCTT (cách nhau dấu phẩy ,)</label>
                  <input type="text" class="form-control req" name="code_yctt" id="code_yctt" value="{{ old('code_yctt', $strPay) }}">
                </div>  
                <div class="form-group col-md-12" >                  
                  <label>Các mã Chi Phí (cách nhau dấu phẩy ,)</label>
                  <input type="text" class="form-control req" name="code_cost" id="code_cost" value="{{ old('code_cost', $strCost) }}">
                </div> 
                </div>   
                 <div class="row  col-md-12">
                   <div class="col-md-6" style="background-color: #f7dedc">
                     <table class="table table-bordered">
                       <tr>
                         <th>Mã booking</th>
                         <th>Số tiền</th>
                       </tr>
                       @for($i = 0; $i<20; $i++)
                       <?php 
                        if(old('booking_id'.$i)){
                          $code = old('booking_id'.$i);
                        }else{
                          $code = isset($arrBk[$i]) ? $arrBk[$i]->code : "";
                        }
                        if(old('amount_booking'.$i)){
                          $value = old('amount_booking'.$i);
                        }else{
                          $value = isset($arrBk[$i]) ? $arrBk[$i]->amount : "";
                        }
                       ?>
                       <tr>
                         <td>
                           <input type="text" name="booking_id[]" class="form-control" autocomplete="off" placeholder="Mã booking" maxlength="7" value="{{ $code }}">
                         </td>
                         <td>
                           <input type="text" maxlength="12" name="amount_booking[]" class="form-control number" autocomplete="off" placeholder="Số tiền" value="{{ $value }}" >
                         </td>
                       </tr>
                       @endfor
                     </table>
                   </div>   
                   <div class="col-md-6"></div>             
                   
                 </div>        
               
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
