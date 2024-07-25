@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Yêu cầu thanh toán
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'payment-request.index' ) }}">Yêu cầu thanh toán</a></li>
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
      <a href="{{ route('payment-request.create',['bank_info_id' => $bank_info_id]) }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('payment-request.index') }}" id="searchForm">
            <div class="row">
            <div class="form-group col-xs-4">
                <input type="text" class="form-control" autocomplete="off" name="id" placeholder="ID" value="{{ $arrSearch['id'] }}">
              </div>
              <div class="form-group col-xs-4" style="padding-left: 0px;">
                <input type="text" class="form-control" autocomplete="off" name="code_ung_tien" placeholder="Code ứng" value="{{ $arrSearch['code_ung_tien'] }}">
              </div>
              <div class="form-group col-xs-4" style="padding-left: 0px;">
                <input type="text" class="form-control" autocomplete="off" name="code_chi_tien" placeholder="Code chi" value="{{ $arrSearch['code_chi_tien'] }}">
              </div>
              </div>
            <div class="form-group">
              <select class="form-control" name="status" id="status">
                <option value="">--Trạng thái--</option>
                <option value="1" {{ $status == 1 ? "selected" : "" }}>Chưa thanh toán</option>
                <option value="2" {{ $status == 2 ? "selected" : "" }}>Đã thanh toán</option>
              </select>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
              <select class="form-control select2" name="city_id" id="city_id">
                <option value="">--Tỉnh/Thành--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}" {{ $city_id == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                @endforeach
              </select>
            </div>
            @endif
            <div class="form-group">
              <select class="form-control select2" name="bank_info_id" id="bank_info_id">
                <option value="">--Tài khoản đối tác--</option>
                @foreach($bankInfoList as $cate)
                <option value="{{ $cate->id }}" {{ $arrSearch['bank_info_id'] == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                @endforeach
              </select>
            </div>
            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
              <select class="form-control" name="nguoi_chi" id="nguoi_chi">
                <option value="">--Người chi--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $nguoi_chi == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>
            @endif

              <div class="form-group">
                  <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
              </div>
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>
      <p style="text-align: right;"><a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} mục )</span> - Tổng tiền: <span style="color:red">{{ number_format($total_actual_amount) }}</span></h3>
        </div>
        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="nguoi_chi">
                <option value="">--SET NGƯỜI CHI--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}">{{ $col->name }}</option>
                @endforeach
              </select>
          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="city_id">
                <option value="">--SET TỈNH/THÀNH--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
              </select>
          </div>
        </div>
        @endif
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->links() }}
          </div>
          <div class="row" style="padding: 10px; border-bottom: 1px solid #ccc">

              <div class="form-group col-xs-12 text-right">
                  <button class="btn btn-success btn-sm" id="btnContentUng">LẤY ND ỨNG TIỀN</button>

                  <button class="btn btn-warning btn-sm" id="btnContentChi">LẤY ND CHI TIỀN</button>
                </div>
            </div>
          <div class="table-responsive list-mobile">


            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              <ul>
              @foreach( $items as $item )
                <?php $i ++; ?>
                <li>
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
              <strong style="color: red">{{ $item->id }}</strong> - {{ $item->user->name }} - {{ date('d/m/y', strtotime($item->date_pay)) }}  <br>

                    @if($item->status == 1)
                    <label class="label label-danger label-sm">Chưa thanh toán</label>
                    @if($item->urgent == 1)
                    <label class="label label-warning label-sm">GẤP</label>
                    @endif
                    @else
                    <label class="label label-success label-sm">Đã thanh toán</label>
                    @endif

                <br>

                  @if($item->bank)
                  {{ $item->bank->name }}<br>
                  {{ $item->bank->bank_name }}-{{ $item->bank->account_name }}-{{ $item->bank->bank_no }}
                  @endif

                  <p style="color:blue">{{ $item->notes }}</p>
                  @if($item->image_url && $item->unc_type == 2)
                  <p class="alert-success">
                   SMS: {{ $item->image_url }}
                  </p>
                  @endif
                  @if($item->sms_ung)
                  <p class="alert-warning sms">
                   SMS ỨNG : {{ $item->sms_ung }}
                  </p>
                  @endif
                  @if($item->sms_chi)
                  <p class="alert-success sms">
                   SMS CHI : {{ $item->sms_chi }}
                  </p>
                  @endif

                  @if($item->image_url)
                  <span style="color: blue; cursor: pointer;" class="img-unc" data-src="{{ config('plantotravel.upload_url').str_replace('uploads/', '', $item->image_url) }}">XEM ẢNH</span>
                  @endif

                  @if($item->unc_url && $item->unc_type == 1)
                  <span style="color: blue; cursor: pointer;" class="img-unc" data-src="{{ config('plantotravel.upload_url').str_replace('uploads/', '', $item->unc_url) }}">XEM ẢNH</span>
                  @endif
                  <br>
                  Tổng tiền: {{ number_format($item->total_money) }}

                  <br>
                  @if($item->nguoi_chi)
                  Người chi: {{ $collecterNameArr[$item->nguoi_chi] }}
                  @endif
                  <br>
                  {{ date('d/m/Y', strtotime($item->date_pay)) }}


                     @if($item->code_ung_tien || $item->time_ung_tien)
                      <br>
                      <p class="alert-warning" title="Code ứng tiền">
                        @if($item->time_ung_tien)
                        Đã ứng
                        @endif
                        @if($item->code_ung_tien)
                        - {{ $item->code_ung_tien }}
                        @endif
                      </p>
                      @endif

                      @if($item->code_chi_tien || $item->time_chi_tien)
                      <br>
                      <p class="alert-success" title="Code chi tiền">
                        @if($item->time_chi_tien)
                        Đã chi
                        @endif
                        @if($item->code_chi_tien)
                        - {{ $item->code_chi_tien }}
                        @endif
                      </p>
                      @endif
                  <p>
                  @if(!$item->code_ung_tien && !$item->time_chi_tien)
                    @if($item->bank_info_id)
                    <a href="https://img.vietqr.io/image/{{str_replace(' ', '', strtolower($item->bank->bank_name))}}-{{$item->bank->bank_no}}-compact2.png?amount={{$item->total_money}}&accountName={{$item->bank->account_name}}&addInfo=PAY {{ $item->id }} {{$item->content}}"
                       class="btn btn-primary btn-sm btn-qrcode"><span
                            class="glyphicon glyphicon-qrcode"></span></a>
                    @endif
                    @if($item->status == 1 || Auth::user()->role == 1 && !Auth::user()->view_only)
                    <a href="{{ route( 'payment-request.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a onclick="return callDelete('{{ number_format($item->total_money) }}','{{ route( 'payment-request.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                    @endif
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                          @if($item->acc_checked == 0)
                          <br><input id="acc_checked_{{ $item->id }}" type="checkbox" name="" class="change-column-value" value="{{ $item->acc_checked == 1 ? 0 : 1 }}" data-table="payment_request" data-id="{{ $item->id }}" data-column="acc_checked" {{ $item->acc_checked == 1 ? "checked" : "" }}>
                          <label for="acc_checked_{{ $item->id }}">KT đã check</label>
                          @else

                          <br><p class="label label-success mt10">KT đã check</p>
                          @endif
                      @endif
                  @endif
                  </p>
                </li>
              @endforeach
            </ul>
            @else

              <p>Không có dữ liệu.</p>

            @endif

          </div>

          <div style="text-align:center">
            {{ $items->links() }}
          </div>
        </div>
        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="nguoi_chi">
                <option value="">--SET NGƯỜI CHI--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}">{{ $col->name }}</option>
                @endforeach
              </select>
          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="city_id">
                <option value="">--SET TỈNH/THÀNH--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
              </select>
          </div>
        </div>
        @endif
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
</section>
<!-- /.content -->
</div>
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header bg-green">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4>LẤY NỘI DUNG CK ỨNG TIỀN</h4>
      </div>
      <div class="modal-body" id="loadConfirm">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnYcUng">LẤY ND CK ỨNG TIỀN</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">ĐÓNG</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="confirmChiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header bg-green">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4>LẤY NỘI DUNG CK CHI TIỀN</h4>
      </div>
      <div class="modal-body" id="loadConfirmChi">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnYcChi">LẤY ND CK CHI TIỀN</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">ĐÓNG</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="uncModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="unc_img" style="width: 100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content" style="text-align: center;">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4>QR CODE</h4>
                </div>
                <div class="modal-body" >
                    <img src="" style="width: 100% !important;" />
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
    $('#urgent').change(function(){
      $('#searchForm').submit();
    });
    $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              $.ajax({
                url : "{{ route('payment-request.change-value-by-column') }}",
                type : 'GET',
                data : {
                  id : $(this).val(),
                  col : obj.data('column'),
                  value: obj.val()
                },
                success: function(data){

                }
              });
          });

       });
    $('tr.cost').click(function(){
      $(this).find('.check_one').attr('checked', 'checked');
    });
    $("#check_all").click(function(){
        $('input.check_one').not(this).prop('checked', this.checked);
    });
    $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('payment-request.export') }}").submit().attr('action', oldAction);
      });
  });
  $(document).ready(function(){
    $('.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).data('src'));
      $('#uncModal').modal('show');
    });
    $('#btnContentUng').click(function(){
          var obj = $(this);
          var str_id = '';
          $('.check_one:checked').each(function(){
              str_id += $(this).val() + ',';
          });
          console.log(str_id);
          if(str_id != ''){
            $.ajax({
              url : "{{ route('payment-request.get-confirm-ung') }}",
              type : 'GET',
              data : {
                str_id : str_id
              },
              success: function(data){
                $('#loadConfirm').html(data);
                $('#confirmModal').modal('show');
              }
            });
          }

       });
    $('#btnContentChi').click(function(){
          var obj = $(this);
          var str_id = '';
          $('.check_one:checked').each(function(){
              str_id += $(this).val() + ',';
          });
          if(str_id != ''){
            $.ajax({
              url : "{{ route('payment-request.get-confirm-chi') }}",
              type : 'GET',
              data : {
                str_id : str_id
              },
              success: function(data){
                $('#loadConfirmChi').html(data);
                $('#confirmChiModal').modal('show');
              }
            });
          }

       });

    $('#btnYcUng').click(function(){
          var obj = $(this);
          var str_id = '';
          $('.check_one:checked').each(function(){
              str_id += $(this).val() + ',';
          });

          if(str_id != ''){
            $.ajax({
              url : "{{ route('payment-request.get-content-ung') }}",
              type : 'GET',
              data : {
                str_id : str_id
              },
              success: function(data){
                $('#noi_dung_ung').html(data);
                $('#btnYcUng').hide();
              }
            });
          }

       });
    $('#btnYcChi').click(function(){
          var obj = $(this);
          var str_id = '';
          $('.check_one:checked').each(function(){
              str_id += $(this).val() + ',';
          });

          if(str_id != ''){
            $.ajax({
              url : "{{ route('payment-request.get-content-chi') }}",
              type : 'GET',
              data : {
                str_id : str_id
              },
              success: function(data){
                $('#noi_dung_chi').html(data);
                $('#btnYcChi').hide();
              }
            });
          }

       });

    $('.btn-qrcode').click(function (e) {
        e.preventDefault();
        $('#qrCodeModal').find('img').attr('src', $(this).attr('href'));
        $('#qrCodeModal').modal('show');
    });
  });
</script>
@stop
