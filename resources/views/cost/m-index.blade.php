@extends('layout')
@section('content')
<div class="content-wrapper">

<!-- Content Header (Page header) -->
<section class="content-header" style="padding-top: 10px;">
  <h1 style="text-transform: uppercase;">
    CHI PHÍ @if(isset($arrSearch['use_date_from'])) - NGÀY {{$arrSearch['use_date_from']}} @endif
  </h1>

</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-md-12">
      <div id="content_alert"></div>
      <!-- <div id="content_alert"></div> -->
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      <a href="{{ route('cost.create',['date_use' => $date_use]) }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('cost.index') }}">
            <div class="row">
               <div class="form-group col-xs-4">
                <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="ID" value="{{ $arrSearch['id_search'] }}">
              </div>
              <div class="form-group col-xs-4" style="padding-left: 0px">
                <input type="text" class="form-control" autocomplete="off" name="code_ung_tien" placeholder="Code ứng" value="{{ $arrSearch['code_ung_tien'] }}">
              </div>
              <div class="form-group col-xs-4" style="padding-left: 0px">
                <input type="text" class="form-control" autocomplete="off" name="code_chi_tien" placeholder="Code chi" value="{{ $arrSearch['code_chi_tien'] }}">
              </div>

              <div class="form-group col-xs-6">
              <select class="form-control select2" name="city_id" id="city_id">
                <option value="">--Tỉnh/Thành--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}"  {{ $city_id == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-xs-6">
              <select class="form-control select2" name="type" id="type">
                <option value="">--Phân loại--</option>
                @foreach($costCate as $item)
                <option value="{{ $item->id }}"  {{ $type == $item->id ? "selected" : "" }}>{{ $item->name }}</option>
                @endforeach
              </select>
            </div>

            </div>
            <div class="form-group">
              <select class="form-control select2" name="tour_id" id="tour_id">
                <option value="">--Tour--</option>
                @foreach($tourSystem as $tour)
                <option value="{{ $tour->id }}" {{ $arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="row">
            <div class="form-group col-xs-6">
              <select class="form-control select2" name="cate_id" id="cate_id">
                <option value="">--Loại chi phí--</option>
                @foreach($cateList as $cate)
                <option value="{{ $cate->id }}" {{ $arrSearch['cate_id'] == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-xs-6" id="load_doi_tac">
              @if(!empty($partnerList ) || $partnerList->count() > 0)
            <div class="form-group ">
                <select class="form-control select2" id="partner_id" name="partner_id">
                  <option value="">--Chi tiết--</option>
                  @foreach($partnerList as $cate)
                  <option value="{{ $cate->id }}" {{ $partner_id == $cate->id ? "selected" : "" }}>
                    {{ $cate->name }}
                  </option>
                  @endforeach
                </select>
            </div>
            @endif
            </div>

            </div>
            <div class="row">
              <div class="form-group col-xs-12">
              <select class="form-control select2" name="nguoi_chi" id="nguoi_chi">
                <option value="">--Người chi--</option>
                @foreach($collecterList as $payer)
                <option value="{{ $payer->id }}" {{ $nguoi_chi == $payer->id ? "selected" : "" }}>{{ $payer->name }}</option>
                @endforeach
              </select>
            </div>
            </div>
            <div class="row">
             <div class="form-group  col-xs-12">
              <select class="form-control select2" name="time_type" id="time_type">
                <option value="">-Thời gian-</option>
                <option value="1" {{ $time_type == 1 ? "selected" : "" }}>Theo tháng</option>
                <option value="2" {{ $time_type == 2 ? "selected" : "" }}>Khoảng ngày</option>
                <option value="3" {{ $time_type == 3 ? "selected" : "" }}>Ngày cụ thể </option>
              </select>
            </div>
            @if($time_type == 1)
            <div class="form-group  chon-thang  col-xs-6">
                <select class="form-control select2" id="month_change" name="month">
                  <option value="">--Tháng--</option>
                  @for($i = 1; $i <=12; $i++)
                  <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                  @endfor
                </select>
              </div>
              <div class="form-group  chon-thang col-xs-6">
                <select class="form-control select2" id="year_change" name="year">
                  <option value="">--Năm--</option>
                  <option value="2020" {{ $year == 2020 ? "selected" : "" }}>2020</option>
                  <option value="2021" {{ $year == 2021 ? "selected" : "" }}>2021</option>
                  <option value="2022" {{ $year == 2022 ? "selected" : "" }}>2022</option>
                  <option value="2023" {{ $year == 2023 ? "selected" : "" }}>2023</option>
                  <option value="2024" {{ $year == 2024 ? "selected" : "" }}>2024</option>
                </select>
              </div>
            @endif
            @if($time_type == 2 || $time_type == 3)

            <div class="form-group chon-ngay col-xs-6">
              <label for="use_date_from">&nbsp;&nbsp;&nbsp;@if($time_type == 2) Từ ngày @else Ngày @endif </label>
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="Từ ngày" value="{{ $arrSearch['use_date_from'] }}" >
            </div>

            @if($time_type == 2)
            <div class="form-group chon-ngay den-ngay col-xs-6">
              <label for="use_date_to">&nbsp;&nbsp;&nbsp;Đến ngày</label>
              <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}">
            </div>
             @endif
            @endif
            </div>
            <button type="submit" class="btn btn-success btn-sm">Lọc</button>
          </form>
        </div>
      </div>
      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
             @if(Auth::user()->role == 1 && !Auth::user()->view_only)
             <div class="form-inline" style="padding: 5px">


                <div class="form-group  col-xs-6">
                  <select class="form-control select2 multi-change-column-value" data-column="cate_id">
                    <option value="">--SET LOẠI CHI PHÍ--</option>
                    @foreach($cateList as $cate)
                    <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group  col-xs-6">
                  <select class="form-control select2 multi-change-column-value" data-column="nguoi_chi">
                      <option value="">--SET NGƯỜI CHI--</option>
                      @foreach($collecterList as $payer)
                      <option value="{{ $payer->id }}">{{ $payer->name }}</option>
                      @endforeach
                    </select>
                </div>

              </div>
              @endif
            <div style="font-size: 18px;padding: 10px; border-bottom: 1px solid #ddd">
              Tổng: <span class="value">{{ $items->total() }} mục </span> - Tổng tiền: <span style="color:red">{{ number_format($total_actual_amount) }}</span>
            </div>
            <div class="row" style="padding: 10px; border-bottom: 1px solid #ccc">
              <div class="col-xs-2">
                <input type="checkbox" id="check_all" value="1">
              </div>
              <div class="form-group col-xs-10 text-right">
                  <button class="btn btn-success btn-sm" id="btnContentUng">LẤY ND ỨNG TIỀN</button>

                  <button class="btn btn-warning btn-sm" id="btnContentChi">LẤY ND CHI TIỀN</button>
                </div>
            </div>
            <ul style="padding: 10px">
             @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
                <li style="border-bottom: 1px solid #ddd; padding-bottom: 10px; padding-top: 10px; font-size:17px;">
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                  <strong style="color: red">{{ $item->id }}</strong>-
                    {{ date('H:i d/m', strtotime($item->created_at)) }}
                    @if($item->status == 1)
                        <label class="label label-danger label-sm">Chưa thanh
                            toán</label>
                    @elseif($item->status == 2)
                        <label class="label label-success label-sm">Đã thanh
                            toán</label>
                    @else
                      <label class="label label-warning label-sm">Thanh
                            toán sau</label>
                    @endif
                    <br>
                    <label class="label" style="background-color:{{ @$tourSystemName[$item->tour_id]['bg_color'] }}">{{ @$tourSystemName[$item->tour_id]['name'] }}</label>

                                                <br>
                  @if($item->costType)
                  <a href="{{ route( 'cost.edit', [ 'id' => $item->id ]) }}">{{ $item->costType->name }}</a> - {{ date('d/m', strtotime($item->date_use)) }}
                  @else
                  {{ $item->cost_type_id }}
                  @endif
                  @if($item->partner)
                  - {{ $item->partner->name }}
                  @endif
                  <br>

                    <i class="  glyphicon glyphicon-user"></i> Người chi:
                    @if($item->nguoi_chi)
                    {{ $collecterNameArr[$item->nguoi_chi] }}
                    @endif
                    @if($item->booking_id)
                    <br>
                    <i class="glyphicon glyphicon-off"></i><span style="color: red"> PTT{{ $item->booking_id }}</span>
                    @endif
                    <br>
                    <i class="  glyphicon glyphicon-usd"></i>{{ number_format($item->amount) }} x {{ number_format($item->price) }} = {{ number_format($item->total_money) }}
                           <br>
                    @if($item->notes)
                    <span style="color:red">{!! nl2br($item->notes) !!}</span>
                    @endif
                    @if($item->unc_type == 2 && $item->image_url)
                    <p style="color: blue; font-style: italic;">
                      {{ $item->image_url }}
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
                    <div class="clearfix" style="margin-top: 3px; margin-bottom: 3px"></div>
                   @if($item->image_url && $item->unc_type == 1)
                  <img src="{{ config('plantotravel.upload_url').$item->image_url }}" height="80"  width="80" style="border: 1px solid red" class="img-unc">
                  @endif

                  @if($item->code_ung_tien)
                    @if($item->time_ung_tien)
                    <label class="label label-success">Đã ứng tiền</label>
                    @endif
                      <span style="font-weight: bold; color: #00a65a" title="Mã ứng tiền">{{ $item->code_ung_tien }}</span>
                    @endif

                    @if($item->time_chi_tien)
                      <br>
                      <label class="label label-danger">Đã chi tiền</label>
                      @endif
                      @if($item->code_chi_tien)
                      <span style="font-weight: bold; color: red" title="Mã chi tiền">{{ $item->code_chi_tien }}</span>
                    @endif


                  @if(!$item->code_ung_tien && !$item->time_chi_tien)
                    @if($item->bank_info_id)
                    <a href="https://img.vietqr.io/image/{{str_replace(' ', '', strtolower($item->bank->bank_name))}}-{{$item->bank->bank_no}}-compact2.png?amount={{$item->total_money}}&accountName={{$item->bank->account_name}}&addInfo=COST {{ $item->id }} {{$item->noi_dung_ck}}"
                                         class="btn btn-primary btn-sm btn-qrcode"><span
                                              class="glyphicon glyphicon-qrcode"></span></a>
                    @endif
                    @if($item->costType)
                    <a style="float: right" onclick="return callDelete('{{ $item->costType->name . " - ".number_format($item->total_money) }}','{{ route( 'cost.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                    @endif
                    <a style="float: right; margin-right: 5px" href="{{ route( 'cost.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                    <div class="clearfix"></div>
                  @endif <!--Đã nộp tiền thì ko đc sửa / xóa-->
                </li>
              @endforeach
            @else
            <li>
              <p>Không có dữ liệu.</p>
            </li>
            @endif
            </ul>

          </div>
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
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
<div class="modal fade" id="confirmUngModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
  <div class="modal-dialog modal-sm" role="document">
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
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="text-align: center;">
                <div class="modal-header bg-green">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4>QR CODE</h4>
                </div>
                <div class="modal-body">
                    <img src="" style="width: 100% !important;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ĐÓNG</button>
                </div>
            </div>
        </div>
    </div>
<style type="text/css">
  .form-group{
    margin-bottom: 10px !important;
  }
</style>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $("#check_all").click(function(){
        $('input.check_one').not(this).prop('checked', this.checked);
    });
    $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              $.ajax({
                url : "{{ route('cost.change-value-by-column') }}",
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
    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    });
    $('#cate_id').change(function(){
        $.ajax({
          url : "{{ route('cost.ajax-doi-tac') }}",
          data: {
            cate_id : $(this).val(),
            city_id : {{ $city_id }}
          },
          type : "GET",
          success : function(data){
            if(data != 'null'){
              $('#load_doi_tac').html(data);
              if($('#partner_id').length==1){
                $('#partner_id').select2();
              }
            }
          }
        });
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
              url : "{{ route('cost.get-confirm-ung') }}",
              type : 'GET',
              data : {
                str_id : str_id
              },
              success: function(data){
                $('#loadConfirm').html(data);
                $('#confirmUngModal').modal('show');
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
              url : "{{ route('cost.get-confirm-chi') }}",
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
              url : "{{ route('cost.get-content-ung') }}",
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
              url : "{{ route('cost.get-content-chi') }}",
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