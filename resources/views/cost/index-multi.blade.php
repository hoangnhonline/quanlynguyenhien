@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Chi phí
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'cost.index' ) }}">Chi phí</a></li>
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
      <a href="{{ route('cost.create',['month' => $month, 'cate_id' => $cate_id]) }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('cost.index') }}" id="searchForm">
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="ID" value="{{ $arrSearch['id_search'] }}" style="width: 100px">
            </div>
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="code_ung_tien" placeholder="Code nộp" value="{{ $arrSearch['code_ung_tien'] }}" style="width: 100px">
            </div>
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="code_chi_tien" placeholder="Code chi" value="{{ $arrSearch['code_chi_tien'] }}" style="width: 100px">
            </div>
            <div class="form-group">

              <select class="form-control select2 search-form-change" name="city_id" id="city_id">
                <option value="">--Tỉnh/Thành--</option>
                @foreach($cityList as $city)
                <option value="{{ $city->id }}"  {{ $city_id == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2 search-form-change" name="type" id="type">
                <option value="">--Phân loại--</option>
                @foreach($costCate as $item)
                <option value="{{ $item->id }}"  {{ $type == $item->id ? "selected" : "" }}>{{ $item->name }}</option>
                @endforeach
              </select>
            </div>
            @if($arrSearch['multi'] == 0)
            <div class="form-group">
              <select class="form-control select2 search-form-change" name="cate_id" id="cate_id">
                <option value="">--Loại phí--</option>
                @foreach($cateList as $cate)
                <option value="{{ $cate->id }}"  {{ $arrSearch['cate_id'] == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                @endforeach
              </select>
            </div>
            @endif

            <div class="form-group search-form-change" id="load_doi_tac">
              @if(!empty($partnerList ) || $partnerList->count() > 0)


                <select class="form-control select2" id="partner_id" name="partner_id">
                  <option value="">--Chi tiết--</option>
                  @foreach($partnerList as $cate)
                  <option value="{{ $cate->id }}" {{ $partner_id == $cate->id ? "selected" : "" }}>
                    {{ $cate->name }}
                  </option>
                  @endforeach
                </select>

            @endif
            </div>
            <div class="form-group">
              <select class="form-control select2 search-form-change" name="nguoi_chi" id="nguoi_chi">
                <option value="">--Người chi--</option>
                @foreach($collecterList as $payer)
                <option value="{{ $payer->id }}" {{ $nguoi_chi == $payer->id ? "selected" : "" }}>{{ $payer->name }}</option>
                @endforeach
              </select>
            </div>

              <div class="form-group">
                  <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
              </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_fixed" id="is_fixed" {{ $arrSearch['is_fixed'] == 1 ? "checked" : "" }} value="1" class="search-form-change">
              <label for="is_fixed">Cố định&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="hoang_the" id="hoang_the" {{ $arrSearch['hoang_the'] == 1 ? "checked" : "" }} value="1" class="search-form-change">
              <label for="hoang_the" style="color: red">Hoàng Thể&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <p>
              @foreach($cateList as $cate)
            <input type="checkbox" name="cate_id[]" id="cate_id{{ $cate->id }}" value="{{ $cate->id }}" {{ in_array($cate->id, $arrSearch['cate_id']) ? "checked" : "" }}>
              <label style="cursor: pointer;" for="cate_id{{ $cate->id }}">{{ $cate->name }}</label>
                @endforeach
            </p>

            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>
      <p style="text-align: right;"><a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a>
      <div class="box">
        <form action="{{ route('cost.parse-sms') }}" method="post" style="display: none;">
          {{ csrf_field() }}
          <div class="input-group" style="padding: 15px;">
            <input type="text" name="sms" placeholder="Nhập SMS ..." class="form-control">
            <span class="input-group-btn">
            <button type="submit" class="btn btn-warning btn-flat">Parse SMS</button>
             </span>
          </div>
        </form>
        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} mục )</span> - Tổng tiền: <span style="color:red">{{ number_format($total_actual_amount) }} </span>- Số lượng: <span style="color:red">{{ $total_quantity }} </span></h3>
        </div>
        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
              <button class="btn btn-success btn-sm" id="btnContentUng">LẤY ND ỨNG TIỀN</button>
            </div>
            <div class="form-group">
              <button class="btn btn-warning btn-sm" id="btnContentChi">LẤY ND CHI TIỀN</button>
            </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="is_fixed">
                <option value="">--SET CỐ ĐỊNH--</option>
                <option value="0">Ko cố định</option>
                <option value="1">Cố định</option>
            </select>
          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="nguoi_chi">
                <option value="">--SET NGƯỜI CHI--</option>
                @foreach($collecterList as $payer)
                <option value="{{ $payer->id }}">{{ $payer->name }}</option>
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
          <div class="form-group">
              <select class="form-control select2 multi-change-column-value" data-column="cate_id">
                <option value="">--SET LOẠI CHI PHÍ--</option>
                @foreach($cateList as $cate)
                <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2 multi-change-column-value" data-column="type">
                <option value="">--SET PHÂN LOẠI--</option>
                <option value="1">Tour đảo</option>
                <option value="2">Rạch Vẹm</option>
                <option value="3">Grand World</option>
                <option value="5">Bãi Sao-2 đảo</option>
                <option value="4">Chi phí chung</option>
              </select>
            </div>

        </div>
        @endif
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr>
              <th style="width: 1%"><input type="checkbox" id="check_all" value="1"></th>
              <th style="width: 1%">#</th>
              <th class="text-left">Tạo lúc</th>
              <th class="text-left">Ngày</th>
              <th class="text-center">Tỉnh/Thành</th>
              <th class="text-left">Nội dung</th>
              <th class="text-center">UNC</th>
              <th class="text-center">Số lượng</th>
              <th class="text-right">Giá</th>
              <th class="text-right">Tổng tiền</th>
              <th width="1%" style="white-space: nowrap;" class="text-center">Người chi</th>
              <th class="text-center" style="white-space: nowrap;" width="1%">Trạng thái</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr class="cost" id="row-{{ $item->id }}">
                <td>
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                </td>
                <td><span class="order">{{ $i }}</span></td>
                <td class="text-left">
                  <strong style="color: red">{{ $item->id }}</strong><br>
                    {{ date('H:i d/m', strtotime($item->created_at)) }}
                </td>
                <td class="text-left">
                    {{ date('d/m/y', strtotime($item->date_use)) }}
                </td>
                <td class="text-center">
                  <label class="label" style="background-color:{{ @$tourSystemName[$item->tour_id]['bg_color'] }}">{{ @$tourSystemName[$item->tour_id]['name'] }}</label>

                                                <br>
                  @if($item->type == 1)
                  Tour đảo
                  @elseif($item->type == 2)
                  Rạch Vẹm
                  @elseif($item->type == 3)
                  Grand World
                  @elseif($item->type == 5)
                  Bãi Sao-2 đảo
                  @elseif($item->type == 4)
                  Chi phí chung
                  @endif
                  <br>
                  @if($item->city_id == 1)
                  <span style="color: green">Phú Quốc</span>
                  @elseif($item->city_id == 3)
                  <span style="color: yellow">HCM</span>
                  @else
                  <span style="color: blue">Đà Nẵng</span>
                  @endif
                </td>
                <td>
                  @if($item->costType)
                  <?php
                  $str = $item->partner_id;
                  ?>
                  <a href="https://plantotravel.vn/cost/{{ Helper::mahoa('mahoa', $str ) }}">{{ $item->costType->name }}</a>
                  @endif
                  @if($item->partner)
                  - {{ $item->partner->name }}
                  @endif
                  @if($item->is_fixed == 1)
                  <label class="label label-success">Cố định</label>
                  @endif
                  <p style="color:red; font-style: italic">{{ $item->notes }}</p>
                  @if($item->unc_type == 2 && $item->image_url)
                  <p style="color: blue; font-style: italic;">
                    {{ $item->image_url }}
                  </p>
                  @endif
                </td>
                <td class="text-center">
                  @if($item->image_url && $item->unc_type == 1)
                  <span style="color: blue; cursor: pointer;" class="img-unc" data-src="{{ config('plantotravel.upload_url').$item->image_url }}">XEM ẢNH</span>
                  @endif
                </td>
                <td class="text-center">{{ $item->amount }}</td>
                <td class="text-right">{{ number_format($item->price) }}</td>
                <td class="text-right">
                  {{ number_format($item->total_money) }}
                </td>
                <td class="text-center" style="white-space: nowrap;">
                  @if($item->nguoi_chi)
                  {{ $collecterNameArr[$item->nguoi_chi] }}
                  @endif
                </td>
                <td style="white-space: nowrap;">
                  @if($item->code_ung_tien)
                    @if($item->time_ung_tien)
                    <label class="label label-success">Đã nộp tiền</label>
                    @endif
                      <span style="font-weight: bold; color: #00a65a" title="Mã nộp tiền">{{ $item->code_ung_tien }}</span>
                    @endif

                    @if($item->time_chi_tien)
                      <br>
                      <label class="label label-danger">Đã chi tiền</label>
                      @endif
                      @if($item->code_chi_tien)
                      <span style="font-weight: bold; color: red" title="Mã chi tiền">{{ $item->code_chi_tien }}</span>
                    @endif
                </td>
                <td style="white-space:nowrap">
                  <a href="{{ route( 'cost.copy', [ 'id' => $item->id ]) }}" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-duplicate"></span></a>
                  @if(!$item->code_ung_tien && !$item->time_chi_tien)
                  <a href="{{ route( 'cost.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                  @if($item->costType)
                  <a onclick="return callDelete('{{ $item->costType->name . " - ".number_format($item->total_money) }}','{{ route( 'cost.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @else
                  <a onclick="return callDelete('{{ number_format($item->total_money) }}','{{ route( 'cost.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  @endif
                  @endif <!-- Đã nộp tiền thì ko đc chỉnh sửa hay xóa -->

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
          <div style="text-align:center">
           {{ $items->appends( $arrSearch )->links() }}
          </div>
        </div>
        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="is_fixed">
                <option value="">--SET CỐ ĐỊNH--</option>
                <option value="0">Ko cố định</option>
                <option value="1">Cố định</option>
            </select>
          </div>
          <div class="form-group">
            <select class="form-control select2 multi-change-column-value" data-column="nguoi_chi">
                <option value="">--SET NGƯỜI CHI--</option>
                @foreach($collecterList as $payer)
                <option value="{{ $payer->id }}">{{ $payer->name }}</option>
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
          <div class="form-group">
              <select class="form-control select2 multi-change-column-value" data-column="cate_id">
                <option value="">--SET LOẠI CHI PHÍ--</option>
                @foreach($cateList as $cate)
                <option value="{{ $cate->id }}">{{ $cate->name }}</option>
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ĐÓNG</button>
      </div>
    </div>
  </div>
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
<input type="hidden" id="table_name" value="articles">
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
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

    $('tr.cost').click(function(){
      $(this).find('.check_one').attr('checked', 'checked');
    });
    $("#check_all").click(function(){
        $('input.check_one').not(this).prop('checked', this.checked);
    });
    $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('cost.export') }}").submit().attr('action', oldAction);
      });
    // $('#partner_id').on('change', function(){
    //   $(this).parents('form').submit();
    // });
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
  });
  $(document).ready(function(){
    $('.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).data('src'));
      $('#uncModal').modal('show');
    });
  });
</script>
@stop
