@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                YÊU CẦU HOÀN TIỀN
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route( 'user-balance-withdraw.index' ) }}">Yêu cầu rút tiền</a></li>
                <li class="active">Danh sách</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div id="content_alert"></div>
                    @if(Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    @if($multi == 0)
                        <a href="{{ route('user-balance-withdraw.create',['month' => $month]) }}"
                           class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Bộ lọc</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-inline" role="form" method="GET"
                                  action="{{ route('user-balance-withdraw.index') }}" id="searchForm">
                                <div class="form-group">
                                    <input type="text" class="form-control" autocomplete="off" name="id_search"
                                           placeholder="ID" value="{{ $arrSearch['id_search'] }}" style="width: 100px">
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="status" id="status">
                                        <option value="">--Trạng thái--</option>
                                        <option value="1" {{ $arrSearch['status'] == 1 ? "selected" : "" }}>Chưa thanh
                                            toán
                                        </option>
                                        <option value="2" {{ $arrSearch['status'] == 2 ? "selected" : "" }}>Đã thanh
                                            toán
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 search-form-change" name="nguoi_chi"
                                            id="nguoi_chi">
                                        <option value="">--Người chi--</option>
                                        @foreach($collecterList as $payer)
                                            <option value="{{ $payer->id }}" {{ $nguoi_chi == $payer->id ? "selected" : "" }}>{{ $payer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
                                </div>
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                            </form>
                        </div>
                    </div>
                    <p style="text-align: right;"><a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export
                            Excel</a>
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} mục )</span> -
                                Tổng tiền: <span style="color:red">{{ number_format($total_actual_amount) }} </span>- Số
                                lượng: <span style="color:red">{{ number_format($total_quantity) }} </span></h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            @if($multi == 0)
                                <div style="text-align:center">
                                    {{ $items->appends( $arrSearch )->links() }}
                                </div>
                            @endif
                            <table class="table table-bordered table-hover" id="table-list-data">
                                <tr>
                                    <th style="width: 1%">Mã yêu cầu</th>
                                    <th class="text-left">Đối tác/Khách hàng</th>
                                    <th class="text-left">Tạo lúc</th>
                                    <th class="text-left">Ngày</th>
                                    <th class="text-left">Nội dung</th>
                                    <th class="text-center">UNC</th>
                                    <th class="text-right">Tổng tiền</th>
                                    <th width="1%" style="white-space: nowrap;" class="text-center">Người chi</th>
                                    <th class="text-center" style="white-space: nowrap;" width="1%">Trạng thái</th>
                                    <th width="1%;white-space:nowrap">Thao tác</th>
                                </tr>
                                <tbody>
                                @php
                                    $types = [
                                                \App\Models\UserBalanceHistory::TRANSACTION_TYPE_COMMISSION => 'Nạp tiền vào tài khoản',
                                                \App\Models\UserBalanceHistory::TRANSACTION_TYPE_WITHDRAW => 'Rút tiền từ tài khoản',
                                              ]
                                @endphp
                                @if( $items->count() > 0 )
                                    <?php $i = 0; ?>
                                    @foreach( $items as $item )
                                        <?php $i++; ?>
                                        <tr class="cost" id="row-{{ $item->id }}">
                                            <td><span class="order">{{  $item->id }}</span></td>
                                            <td>{{ $item->user->name }} (SD: {{number_format($item->user->balance)}} VNĐ)</td>
                                            <td class="text-left">
                                                {{ date('H:i d/m', strtotime($item->created_at)) }}
                                            </td>
                                            <td class="text-left">
                                                {{ date('d/m/y', strtotime($item->created_at)) }}
                                            </td>
                                            <td>
                                                Rút tiền từ tài khoản<br>
                                                <p style="color:red; font-style: italic">{{ $item->notes }}</p>
                                                @if($item->image_url && $item->unc_type == 2)
                                                    <p class="alert-success">
                                                        SMS: {{ $item->image_url }}
                                                    </p>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($item->image_url && $item->unc_type == 1)
                                                    <span style="color: blue; cursor: pointer;" class="img-unc"
                                                          data-src="{{ config('plantotravel.upload_url').$item->image_url }}">XEM ẢNH</span>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($item->amount) }}đ</td>
                                            <td class="text-center" style="white-space: nowrap;"
                                                data-id="{{ $item->nguoi_chi }}">
                                                @if($item->nguoi_chi)
                                                    {{ $collecterNameArr[$item->nguoi_chi] }}
                                                @endif
                                            </td>
                                            <td style="white-space: nowrap;">
                                                @if($item->status == \App\Models\UserBalanceWithdraw::STATUS_PENDING)
                                                    <label class="label label-danger label-sm">Chưa thanh toán</label>
                                                @elseif($item->status ==  \App\Models\UserBalanceWithdraw::STATUS_COMPLETED)
                                                    <label class="label label-success label-sm">Đã thanh
                                                        toán</label>
                                                @endif
                                            </td>
                                            <td style="white-space:nowrap">
                                                @if($item->status == \App\Models\UserBalanceWithdraw::STATUS_PENDING)
                                                    <a data-bank-no="{{ $item->account_number }}"
                                                       href="https://img.vietqr.io/image/{{str_replace(' ', '', strtolower($item->account_bank_name))}}-{{$item->account_number}}-compact2.png?amount={{$item->amount}}&accountName={{$item->account_name}}&addInfo={{$item->id}}"
                                                       data-id="{{$item->id}}"
                                                       class="btn {{$item->qrcode_clicked > 0 ? 'btn-warning' : 'btn-primary'}} btn-sm btn-qrcode">
                                                        <span class="glyphicon glyphicon-qrcode"></span><span class="clicked-qr">{{$item->qrcode_clicked ? ' (' .$item->qrcode_clicked . ')' : ''}}</span></a>
                                                    <a href="{{ route( 'user-balance-withdraw.edit', [ 'id' => $item->id ]) }}"
                                                       class="btn btn-warning btn-sm"><span
                                                            class="glyphicon glyphicon-pencil"></span></a>
                                                    <a onclick="return callDelete('Rút tiền từ tài khoản','{{ route( 'user-balance-withdraw.destroy', [ 'id' => $item->id ]) }}');"
                                                       class="btn btn-danger btn-sm"><span
                                                        class="glyphicon glyphicon-trash"></span></a>
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
                            @if($multi == 0)
                                <div style="text-align:center">
                                    {{ $items->appends( $arrSearch )->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
        </section>
        <!-- /.content -->
    </div>
    <div class="modal fade" id="uncModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ĐÓNG</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmUngModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">
                        ĐÓNG
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmChiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">
                        ĐÓNG
                    </button>
                </div>
            </div>
        </div>
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
        $(document).ready(function () {

            $('.btn-qrcode').click(function (e) {
                e.preventDefault();
                $('#qrCodeModal').find('img').attr('src', $(this).attr('href'));
                $('#qrCodeModal').modal('show');
                var button = $(this);
                button.removeClass('btn-primary').addClass('btn-warning');
            });

            $('tr.cost').click(function () {
                $(this).find('.check_one').attr('checked', 'checked');
            });
            $("#check_all").click(function () {
                $('input.check_one').not(this).prop('checked', this.checked);
            });
            $('#btnExport').click(function () {
                var oldAction = $('#searchForm').attr('action');
                {{--$('#searchForm').attr('action', "{{ route('user-balance-withdraw.export') }}").submit().attr('action', oldAction);--}}
            });
            // $('#partner_id').on('change', function(){
            //   $(this).parents('form').submit();
            // });
        });
        $(document).ready(function () {
            $('.img-unc').click(function () {
                $('#unc_img').attr('src', $(this).data('src'));
                $('#uncModal').modal('show');
            });
        });

    </script>
@stop
