@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    CODE CHI TIỀN CHƯA THANH TOÁN
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'cost.group-code' ) }}">Chi phí</a></li>
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
      <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" method="GET" action="{{ route('cost.group-code') }}" id="searchForm">
          <div class="form-group">
            <input type="text" name="code_chi_tien" value="{{ $code_chi_tien }}" class="form-control" placeholder="Code chi tiền" maxlength="20" autocomplete="off">
          </div>
          <button class="btn btn-info" type="submit">Lọc</button>
          </form>
      </div>
      </div>
      @endif
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( {{ $items->count() }} )</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive list-mobile">
              <table class="table table-borderd table-striped">
                <tr>
                  <th class="text-center">STT</th>
                  <th class="text-right">CODE</th>
                  <th class="text-right">TỔNG TIỀN</th>
                  <th class="text-center">LOẠI CP</th>
                  <th class="text-center">ĐỐI TÁC</th>
                  <th></th>
                </tr>
                <?php $i = $total = 0; ?>
                @foreach($items as $item)
                <?php $i++;

                $total += $item->tong_tien;


                ?>
                <tr>
                  <td class="text-center" width="1%">{{ $i }}</td>
                  <td class="text-right">
                    {{ $item->code_chi_tien }}
                  </td>
                  <td class="text-right">
                    {{ number_format($item->tong_tien) }}
                  </td>
                  <td class="text-center">
                    @if($item->cate_id)
                    {{ $item->costType->name }}
                    @endif
                  </td>
                  <td class="text-center">
                    @if($item->partner)
                    {{ $item->partner->name }}
                    @endif
                  </td>
                  <td style="width: 300px;">
                  @if($item->bank_info_id)
                  <?php
                  $bank = App\Models\BankInfo::find($item->bank_info_id);
                  ?>
                   <a href="https://img.vietqr.io/image/{{str_replace(' ', '', strtolower($bank->bank_name))}}-{{$bank->bank_no}}-compact2.png?amount={{$item->tong_tien}}&accountName={{$bank->account_name}}&addInfo=EXP {{ $item->code_chi_tien }} - {{$item->tong_tien}}"
                                       class="btn btn-primary btn-sm btn-qrcode"><span
                                            class="glyphicon glyphicon-qrcode"></span></a>
                  @else

                    <select class="form-control select2 change-column-value" name="bank_info_id" id="bank_info_id" data-id="{{ $item->code_chi_tien }}" data-column="bank_info_id" data-table="cost">
                      <option value="">--Tài khoản nhận tiền--</option>
                      @foreach($bankInfoList as $cate)
                      <option value="{{ $cate->id }}" {{ old('bank_info_id', $item->bank_info_id) == $cate->id ? "selected" : "" }}>{{ $cate->name }} - {{ $cate->bank_name }} - {{ $cate->bank_no }}</option>
                      @endforeach
                    </select>
                  @endif
                  </td>
                </tr>
                @endforeach

              </table>
              <p style="color: red; font-size: 20px; font-weight: bold; margin: 15px;" id="noi_dung_ung"></p>
              <span>{{ number_format($total) }}</span>
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
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="text-align: center;">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4>QR CODE</h4>
                </div>
                <div class="modal-body">
                    <img src=""/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ĐÓNG</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){


    $('.btn-qrcode').click(function (e) {
        e.preventDefault();
        $('#qrCodeModal').find('img').attr('src', $(this).attr('href'));
        $('#qrCodeModal').modal('show');
    });

});
</script>
@stop
