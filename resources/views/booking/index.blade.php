@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="text-transform: uppercase;">
    Quản lý đặt tour
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking.index', ['type' => $type]) }}">
      @if($type == 1)
    Tour
    @elseif($type == 2)

    @endif</a></li>
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
      @if(Auth::user()->hotline_team == 0)
      <a href="{{ route('booking.create', ['type' => $type]) }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      @endif
      <div class="panel panel-default">

        <div class="panel-body" style="padding: 5px !important;">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking.index') }}" id="searchForm" style="margin-bottom: 0px;">
            {{-- @include('partials.block-search-date') --}}
            <div class="form-group">
                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
            </div>
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="PTT ID" value="{{ $arrSearch['id_search'] }}" style="width: 70px">
            </div>
            <div class="form-group">
              <input type="text" class="form-control datepicker" autocomplete="off" name="created_at" placeholder="Ngày đặt" value="{{ $arrSearch['created_at'] }}" style="width: 120px">
            </div>
            <div class="form-group">
              <select class="form-control select2" name="tour_id" id="tour_id">
                <option value="">--Tour--</option>
                @foreach($tourSystem as $tour)
                <option value="{{ $tour->id }}" {{ $arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                @endforeach
              </select>
            </div>
           <!--  <div class="form-group">
              <select class="form-control select2" id="tour_cate" name="tour_cate" >
                  <option value="">--Loại tour--</option>
                  <option value="1" {{ $arrSearch['tour_cate'] == 1 ? "selected" : "" }}>4 đảo</option>
                  <option value="2" {{ $arrSearch['tour_cate'] == 2 ? "selected" : "" }}>2 đảo</option>
              </select>
            </div>  -->

            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">--Sales--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="user_id_manage" id="user_id_manage">
                <option value="">--Người phụ trách--</option>
                <option value="84" {{ $arrSearch['user_id_manage'] == 84 ? "selected" : "" }}>Lâm Như</option>
                <option value="451" {{ $arrSearch['user_id_manage'] == 451 ? "selected" : "" }}>Thảo Lê</option>
                <option value="219" {{ $arrSearch['user_id_manage'] == 219 ? "selected" : "" }}>Trang Tạ</option>
                <option value="333" {{ $arrSearch['user_id_manage'] == 333 ? "selected" : "" }}>Group Tour</option>
              </select>
            </div>
            @endif
            @if($ctvList->count())
            <div class="form-group">
              <select class="form-control select2" name="ctv_id" id="ctv_id">
                <option value="">--Người book--</option>
                @foreach($ctvList as $ctv)
                <option value="{{ $ctv->id }}" {{ $arrSearch['ctv_id'] == $ctv->id ? "selected" : "" }}>{{ $ctv->name }}</option>
                @endforeach
              </select>
            </div>
            @endif

           @if(Auth::user()->role == 1 && !Auth::user()->view_only)
          <div class="form-group">
            <select class="form-control select2" id="hdv_id" name="hdv_id">
              <option value="">--HDV--</option>
              @foreach($listHDV as $user)
              <option value="{{ $user->id }}" @if($arrSearch['hdv_id'] == $user->id) selected @endif>{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <select class="form-control select2"  data-column="cano_id" name="cano_id">
              <option value="">--CANO--</option>
              @foreach($canoList as $cano)
              <option value="{{ $cano->id }}" {{ $arrSearch['cano_id'] == $cano->id ? "selected" : "" }}>{{ $cano->name }}</option>
              @endforeach
            </select>
          </div>
          @endif
          @if(Auth::user()->role == 1 && !Auth::user()->view_only)
            <div class="form-group">
            <select class="form-control" name="hdv0" id="hdv0">
              <option value="">--TT CHỌN HDV--</option>
              <option value="2" {{ $arrSearch['hdv0'] == 2 ? "selected" : "" }}>Đã chọn HDV</option>
              <option value="1" {{ $arrSearch['hdv0'] == 1 ? "selected" : "" }}>Chưa chọn HDV</option>
            </select>
          </div>
          <div class="form-group">
            <select class="form-control" name="cano0" id="cano0">
              <option value="">--TT CHỌN CANO--</option>
              <option value="2" {{ $arrSearch['cano0'] == 2 ? "selected" : "" }}>Đã chọn CANO</option>
              <option value="1" {{ $arrSearch['cano0'] == 1 ? "selected" : "" }}>Chưa chọn CANO</option>
            </select>
          </div>
          @endif
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <div class="form-group">
              <button type="button" id="btnReset" class="btn btn-default btn-sm">Reset</button>
            </div>
            <div>
              @if($arrSearch['tour_id'] != 4)
              <div class="form-group">
              <input type="checkbox" name="tour_type[]" id="tour_type_1" {{ in_array(1, $arrSearch['tour_type']) ? "checked" : "" }} value="1">
              <label for="tour_type_1">GHÉP({{ $ghep }})</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_2" {{ in_array(2, $arrSearch['tour_type']) ? "checked" : "" }} value="2">
              <label for="tour_type_2">VIP({{ $vip }}-{{ $tong_vip }}NL)&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="tour_type[]" id="tour_type_3" {{ in_array(3, $arrSearch['tour_type']) ? "checked" : "" }} value="3">
              <label for="tour_type_3">THUÊ CANO({{ $thue }})&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @endif
              <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">Mới</label>
            </div>
            <div class="form-group">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn Tất</label>
            </div>

            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @if($arrSearch['tour_id'] != 4)
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="no_cab" id="no_cab" {{ $arrSearch['no_cab'] == 1 ? "checked" : "" }} value="1">
              <label for="no_cab">Không cáp&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              &nbsp;&nbsp;&nbsp;<input type="checkbox" name="no_meals" id="no_meals" {{ $arrSearch['no_meals'] == 1 ? "checked" : "" }} value="1">
              <label for="no_meals">Không ăn&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            @endif
            <div class="form-group" style="border-right: 1px solid #9ba39d;float: right;">
              &nbsp;&nbsp;&nbsp;<input type="checkbox"name="short" id="short" {{ $arrSearch['short'] == 1 ? "checked" : "" }} value="1">
              <label for="short">Short&nbsp;&nbsp;&nbsp;&nbsp;</label>
            </div>
            </div>
          </form>
        </div>
      </div>
      <div class="panel">
        <div class="panel-body">
          <ul style="padding: 0px;">
          @foreach($arrHDV as $hdv_id => $arrBK)
          <li style="display: inline;
    float: left;
    list-style: none; height: 45px;">
          @if($hdv_id > 0)
          <span data-id="{{ $hdv_id }}" class="label label-default hdv @if($hdv_id == $arrSearch['hdv_id']) selected @endif" style="padding: 10px 5px;margin-right: 10px; font-size: 12px">{{ isset($arrHDVDefault[$hdv_id]) ? $arrHDVDefault[$hdv_id]->name : $hdv_id }}[{{ count($arrBK)}}]</span>
          @else
          <span data-id="" class="label label-default hdv" style="padding: 10px;margin-right: 10px; font-size: 12px">CHƯA CHỌN HDV[{{ count($arrBK)}}]</span>
          @endif
          </li>
          @endforeach
        </ul>
        </div>
      </div>
      @if(Auth::user()->role == 1 && !Auth::user()->view_only && !empty($arrDs))
      <div class="panel">
        <div class="panel-body">
          <ul style="padding: 0px;">
          @foreach($arrDs as $user_id => $dsAdults)

          <li style="display: inline;float: left;list-style: none; height: 45px;">

          <span data-id="{{ $hdv_id }}" class="label label-sm @if($dsAdults > 30) label-danger @else label-default @endif" style="padding: 5px 3px;margin-right: 5px; font-size: 11px">{{ isset( $arrUser[$user_id]) ? $arrUser[$user_id]->name : "Không xác định" }} - [{{ $dsAdults }}]</span>

          </li>

          @endforeach
        </ul>
        </div>
      </div>
      @endif
      <div class="panel" style="margin-bottom: 15px;">
        <div class="panel-body" style="padding: 5px;">
          <div class="table-responsive">
          <table class="table table-bordered" id="table_report" style="margin-bottom:0px;font-size: 14px;">
              <tr style="background-color: #f4f4f4">
                <th class="text-center">Tổng BK</th>
                <th class="text-center">NL/TE</th>
                <th class="text-center">Ăn NL/TE</th>
                <th class="text-center">Cáp NL/TE</th>
                <th class="text-right">Thực thu</th>
                <th class="text-right">HDV thu</th>
                <th class="text-right">Điều hành</th>
                <th class="text-right">Tổng cọc</th>
                <th class="text-right">HH sales</th>
                <th class="text-right">Doanh số</th>
              </tr>
              <tr>
                <td class="text-center">{{ number_format($items->total()) }}</td>
                <td class="text-center">{{ $tong_so_nguoi }} / {{ $tong_te }}</td>
                <td class="text-center">{{ $tong_phan_an }} / {{ $tong_phan_an_te }}</td>
                <td class="text-center">{{ $cap_nl }} / {{ $cap_te }}</td>
                <td class="text-right">{{ number_format($tong_thuc_thu ) }}</td>
                <td class="text-right">{{ number_format($tong_hdv_thu ) }}</td>
                <td class="text-right">{{ number_format($tong_thao_thu ) }}</td>
                <td class="text-right">{{ number_format($tong_coc ) }}</td>
                <td class="text-right">{{ number_format($tong_hoa_hong_sales ) }}</td>
                <td class="text-right">{{ number_format($tong_doanh_so ) }}</td>
              </tr>
          </table>

        </div>
        </div>
      </div>
      <div class="box">


        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
        <div class="form-inline" style="padding: 5px">
          <div class="form-group">
            <select style="font-size: 11px;" class="form-control select2 multi-change-column-value" data-column="hdv_id">
              <option value="">--SET HDV--</option>
              @foreach($listUser as $user)
              @if($user->hdv==1)
              <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endif
              @endforeach
            </select>
              <select class="form-control select2 multi-change-column-value"  data-column="cano_id">
                <option value="">--SET CANO--</option>
                @foreach($canoList as $cano)
                <option value="{{ $cano->id }}">{{ $cano->name }}</option>
                @endforeach
              </select>
              <select class="form-control select2 multi-change-column-value"  data-column="tour_no">
                <option value="">--Tour số--</option>
                @for($i = 1; $i<=10; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
                @endfor
              </select>
              <select class="form-control select2 multi-change-column-value"  data-column="export">
                <option value="">--TT GỬI--</option>
                <option value="2">Chưa gửi</option>
                <option value="1">Đã gửi</option>
              </select>
          </div>
          <div class="form-group" style="float: right">
            @if($time_type==3)
              @if($items->isNotEmpty())
                <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('booking.insurance', ['ids' => $items->pluck('id')->toArray()]) }}">Bảo hiểm</a>
              @endif
            <a href="javascript:;" class="btn btn-info btn-sm" id="btnExportGui">Export Gửi</a>
            <a href="https://plantotravel.vn/sheet/do.php?day={{ $day }}&month={{ $month_do }}" target="_blank" class="btn btn-danger btn-sm">So sánh</a>
            @endif
          </div>
        </div>
        @else
        <div class="form-group" style="float: right">
          <a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export Excel</a>
        </div>
        @endif
        <div class="clearfix"></div>
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr style="background-color: #f4f4f4">
              <th style="width: 1%" class="text-center"><input type="checkbox" id="check_all" value="1"></th>
              <th style="width: 1%"></th>
              <th width="200">Tên KH</th>
              <th style="width: 200px">Nơi đón</th>
              <th class="text-center" width="80">NL/TE/EB</th><th class="text-right" width="100">Tổng tiền/Cọc<br> HH Sales</th>
              <th class="text-right" width="140" >Thực thu</th>
              <th class="text-center" width="60">Ngày đi</th>
              <th class="text-center" width="90">HDV</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $l = 0; ?>
              @foreach( $items as $item )
                <?php $l ++;
                $allowEdit =  !Auth::user()->view_only && $item->use_date >= $settingArr['date_block_booking'] ? true : false;
                ?>
              <tr class="booking" id="row-{{ $item->id }}" data-id="{{ $item->id }}" data-date="{{ $item->use_date }}" style="border-bottom: 1px solid #000 !important;@if($item->status == 3) background-color: #f77e7e; @endif">
                <td class="text-center" style="line-height: 30px">
                  @if($allowEdit)
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                  @endif
                  <a href="{{ route('view-pdf', ['id' => $item->id])}}" target="_blank">PDF</a>
                  <br>{{ date('d/m H:i', strtotime($item->created_at)) }}
                  <span class="label label-sm label-danger" id="error_unc_{{ $item->id }}"></span>
                </td>
                <td style="text-align: center;white-space: nowrap; line-height: 30px;"><strong style="color: red;">PTT{{ $item->id }}</strong>
                  <br>
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif

                </span>
                  <br>
                   @if($item->maxis)
                    @foreach($item->maxis as $maxis)
                    <p class="img-maxi" style="background-color: pink; color: #000;margin-top: 5px;padding: 0px 5px; margin-bottom: 5px;"
                    data-image="{{ !empty($maxis->maxi) ? $maxis->maxi->thumbnail->image_url : '' }}"
                    >{{ !empty($maxis->maxi) ? $maxis->maxi->name : '' }}</p>
                    @endforeach
                  @endif
              </td>
                <td style="position: relative; line-height: 30px;">



                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp

                  <span class="name">{{ $item->name }}</span>

                  @if($item->status != 3)
                     - <a href="tel:{{ $item->phone }}" style="font-weight: bold">{{ $item->phone }}</a>
                  @if($item->tour_id)
                  <br><label class="label" style="background-color:{{ @$tourSystemName[$item->tour_id]['bg_color'] }}">{{ @$tourSystemName[$item->tour_id]['name'] }}</label>
                  @endif
                  @if($item->tour_cate == 2 && $item->tour_id == 1)
                  <br><label class="label label-info">2 đảo</label>
                  @endif

                  @if($item->tour_type == 3)
                  <br><label class="label label-warning">Thuê cano</label>
                  @elseif($item->tour_type == 2)
                  <br><label class="label label-danger">Tour VIP</label>
                  @endif
                   @if($item->ctv)
                    - {{ $item->ctv->name }}
                  @endif
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                     <br><i style="font-style: italic;" class="glyphicon glyphicon-user"></i>
                       <i>
                          @if($item->user)
                            {{ $item->user->name }}
                          @else
                            {{ $item->user_id }}
                          @endif
                       </i>
                  @endif
                    @if($item->source == 'website')
                        <br>
                        <span style="color: red">
                            <i style="font-style: italic;" class="glyphicon glyphicon-globe"></i>
                            <i>
                                Từ website
                            </i>
                        </span>
                    @endif
                  @endif
                  <p style="color:#f0ad4e; font-style: italic;" id="error_{{ $item->id }}"></p>
                  <span class="label label-sm label-danger" id="error_unc_{{ $item->id }}"></span>

                </td>

                <td style="line-height: 22px;">
                  @if($item->status != 3)
                  @if($item->location && !$arrSearch['chua_thuc_thu'])
                  {{ $item->location->name }}[{{$item->location_id}}]@if(!empty($item->location->pickup_time)) <strong style="color: red">(đón {{$item->location->pickup_time}})</strong> @endif
                  <br>
                 <!--  {{ $item->location->address }} -->
                  @else
                  {{ $item->address }}
                  @endif
                  <span style="color:red; font-size:12px">
                    @if($item->ko_cap_treo)
                    KHÔNG CÁP<br>
                    @endif
                    {{ $item->notes }}</span>
                    @if($item->export == 1)
                    <span class="label label-default">Đã gửi</span>
                    @else
                    <span class="label label-danger">Chưa gửi</span>
                    @endif
                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->export == 2)
                    <input type="checkbox" name="" class="change_status" value="1" data-id="{{ $item->id }}">
                    <br>
                    @endif
                  @endif
                </td>
                 <td class="text-center">
                  @if($item->status != 3)
                    {{ $item->adults }} / {{ $item->childs }} / {{ $item->infants }}
                    <br>
                    <?php
                    $meals = $item->meals;
                    if($meals > 0){
                      $meals+= $item->meals_te/2;
                    }

                    ?>
                    <i class="glyphicon glyphicon-briefcase"></i> {{ $meals }}
                  @endif

                </td>


                <td class="text-right">
                  @if($item->status != 3)
                  {{ number_format($item->total_price) }}/{{ number_format($item->tien_coc) }}
                  <br>
                  <span style="color: #06b7a4; font-weight: bold">HH: {{ number_format($item->hoa_hong_sales) }}</span>
                  @endif
                  @if($item->status < 3 && Auth::user()->role == 1 && !Auth::user()->view_only && $allowEdit)
                    @if(!in_array($item->user_id, [33,18]) && $item->price_net == 0)
                    <input type="text" class="form-control hoa_hong_sales number" placeholder="HH sales" data-id="{{ $item->id }}" value="{{ $item->hoa_hong_sales ? number_format($item->hoa_hong_sales) : "" }}" style="text-align: right;width: 90%;float:right;margin-top:5px">
                    <br>
                      @if($item->user)
                      <p style="color: #3c8dbc">
                      {{ Helper::getLevel($item->user->level) }}</p>
                    @endif
                      @endif
                  @endif
                </td>
                <td class="text-right">
                {{ number_format($item->tien_thuc_thu) }}
                <br> HDV thu: <span style="color: blue">{{ number_format($item->hdv_thu) }} </span>
                @if(Auth::user()->role == 1 && !Auth::user()->view_only && $allowEdit)
                  <br><input id="nguoi_thu_tien_{{ $item->id }}" type="checkbox" name="" class="change-column-value" data-column=nguoi_thu_tien value="5" data-id="{{ $item->id }}" {{ $item->nguoi_thu_tien == 5 ? "checked" : "" }}>
                    <label for="nguoi_thu_tien_{{ $item->id }}">Điều hành</label>
                    <br><input id="price_net_{{ $item->id }}" type="checkbox" class="change_price_net" value="1" data-id="{{ $item->id }}" {{ $item->price_net == 1 ? "checked" : "" }}>
                    <label for="price_net_{{ $item->id }}">Giá NET</label>
                    @endif
                </td>
                <td class="text-center">
                  {{ date('d/m', strtotime($item->use_date)) }}
                </td>
                <td class="text-center">
                  @if($item->tour_id == 4)
                    <!-- @if($item->mail_hotel == 0)
                    <a href="{{ route('mail-preview', ['id' => $item->id, 'tour_id' => 4]) }}" class="btn btn-sm btn-success" >
                      <i class="  glyphicon glyphicon-envelope"></i> Book Tour
                    </a>
                    @else
                    <p class="label label-info" style="margin-bottom: 5px; clear:both">Đã mail John</p>
                    @endif -->
                  @else
                    @if($item->status != 3 && $arrSearch['is_edit'] == 1)
                      @if(Auth::user()->role == 1 && !Auth::user()->view_only && $allowEdit)
                      <select style="width: 150px !important; margin-bottom: 5px" class="form-control select2 change-column-value" data-column="hdv_id" data-id="{{ $item->id }}">
                        <option value="">--HDV--</option>
                        @foreach($listUser as $user)
                        @if($user->hdv==1)
                        <option value="{{ $user->id }}" @if($item->hdv_id == $user->id) selected @endif>{{ $user->name }}</option>
                        @endif
                        @endforeach
                      </select>
                      <div style="clear:both" style="margin-bottom: 5px"></div>
                      <select class="form-control select2 change-column-value"  data-column="cano_id" data-id="{{ $item->id }}">
                        <option value="">--CANO--</option>
                        @foreach($canoList as $cano)
                        <option value="{{ $cano->id }}" {{ $item->cano_id == $cano->id ? "selected" : "" }}>{{ $cano->name }}</option>
                        @endforeach
                      </select>
                      @else
                      @if($item->hdv_id > 0 )
                      <strong>{{ $item->hdv->name }}</strong>
                      @endif
                      @endif
                    @else
                      @if($item->hdv_id > 0)
                      {{ $item->hdv->name }}
                      @else
                      - HDV -
                      @endif
                      @if($item->cano_id > 0)
                      <br> - {{ $item->cano->name }}
                      @else
                      <br> - Cano -
                      @endif
                    @endif
                  @endif
                </td>

                <!-- <td class="text-right">
                  {{ number_format($item->hoa_hong_cty) }}
                </td> -->

                <td style="white-space:nowrap; position: relative;" class="text-right">
                  @if($item->status != 3)
                    @if($allowEdit)
                    @php
                    $countUNC = $item->payment->count();
                    $strpayment = "";
                    $tong_payment = 0;
                    foreach($item->payment as $p){
                      $strpayment .= "+". number_format($p->amount)." - ".date('d/m', strtotime($p->pay_date));
                      if($p->type == 1){
                        $strpayment .= " - UNC"."<br>";
                      }else{
                        $strpayment .= " - auto"."<br>";
                      }
                      $tong_payment += $p->amount;
                    }
                    if($countUNC > 0)
                    $strpayment .= "Tổng: ".number_format($tong_payment);
                    @endphp
                    <a href="{{ route( 'booking-xe-free.create', ['booking_id' => $item->id]) }}" class="btn btn-primary btn-sm"><span class="fa fa-car" title="Xe đưa đón miễn phí"></span>
                    @if($item->dontienfree->count() > 0)
                      [ {{ $item->dontienfree->count() }} ]
                      @endif
                    </a>

                      <a data-toggle="tooltip" data-html="true" title="{!! $strpayment !!}" href="{{ route( 'booking-payment.index', ['booking_id' => $item->id] ) }}" class="btn btn-info btn-sm">{{ $countUNC > 0 ?? $countUNC }} <span class="glyphicon glyphicon-usd"></span></a>
                        @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp

                        <a href="{{ route( 'booking.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                      @endif
                  @endif
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only && $allowEdit)
                  <br><input id="check_unc_{{ $item->id }}" type="checkbox" name="" class="change-column-value" value="{{ $item->check_unc == 1 ? 0 : 1 }}" data-id="{{ $item->id }}" data-column="check_unc" {{ $item->check_unc == 1 ? "checked" : "" }}>
                  <label for="check_unc_{{ $item->id }}">Đã check UNC</label>
                  <br><input id="is_send_{{ $item->id }}" type="checkbox" name="" class="change-column-value" value="{{ $item->is_send == 1 ? 0 : 1 }}" data-id="{{ $item->id }}" data-column="is_send" {{ $item->is_send == 1 ? "checked" : "" }}>
                    <label for="is_send_{{ $item->id }}">Gửi bên khác</label>
                  @endif
                  @if(Auth::user()->role == 1 && !Auth::user()->view_only )
                  <br><a style="font-size: 14px" target="_blank" href="{{ route('history.booking', ['id' => $item->id]) }}">Xem lịch sử</a>
                  @endif
                  <br>
                  <a style="font-size: 15px" target="_blank" href="https://plantotravel.vn/booking/{{ Helper::mahoa('mahoa', $item->id) }}">Danh sách ({{ $item->customers->count() }})</a>
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
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          @if(Auth::user()->role == 1 && !Auth::user()->view_only)
          <div class="form-inline" style="padding: 5px">
            <div class="form-group">
              <select class="form-control select2 multi-change-column-value" id="hdv_id" name="hdv_id" data-column="hdv_id">
                <option value="">--SET HDV--</option>
                @foreach($listHDV as $user)

                <option value="{{ $user->id }}">{{ $user->name }}</option>

                @endforeach
              </select>

              <select class="form-control select2 multi-change-column-value"  data-column="cano_id">
                <option value="">--SET CANO--</option>
                @foreach($canoList as $cano)
                <option value="{{ $cano->id }}">{{ $cano->name }}</option>
                @endforeach
              </select>
              <select class="form-control select2 multi-change-column-value"  data-column="tour_no">
                <option value="">--Tour số--</option>
                @for($i = 1; $i<=10; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
                @endfor
              </select>
              <select class="form-control select2 multi-change-column-value"  data-column="export">
                <option value="">--TT GỬI--</option>
                <option value="2">Chưa gửi</option>
                <option value="1">Đã gửi</option>
              </select>
            </div>
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
<div class="modal fade" id="maxiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="maxi_img" style="width: 100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="table_name" value="articles">
@stop
<style type="text/css">
  .hdv{
    cursor: pointer;
  }
  .hdv:hover, .hdv.selected{
    background-color: #06b7a4;
    color: #FFF
  }
  label{
    cursor: pointer;
  }
  #table_report th td {padding: 2px !important;}
  #searchForm, #searchForm input{
    font-size: 13px;
  }
  .form-control{
    font-size: 13px !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered{

    font-size: 12px !important;
  }
  tr.error{
    background-color:#ffe6e6
  }
</style>
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    });
    $('p.img-maxi').click(function(){
      $('#maxi_img').attr('src', "https://plantotravel.vn/" + $(this).data('image'));
      $('#maxiModal').modal('show');
    });
  });
</script>
<script type="text/javascript">
    $(document).ready(function(){
      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
      // $('tr.booking').each(function(){
      //   var tr = $(this);
      //   var id = tr.data('id');
      //   var use_date = tr.data('date');
      //   var today = new Date();
      //   if(use_date < "{{ date('Y-m-d') }}"){
      //     $.ajax({
      //       url : '{{ route('booking.checkError') }}?id=' + id,
      //       type : 'GET',
      //       success : function(data){
      //         $('#error_' + id).text(data);
      //       }
      //     });
      //   }
      // });
      @endif
      $('tr.booking').each(function(){
        var tr = $(this);
        var id = tr.data('id');
        var use_date = tr.data('date');
        var today = new Date();
        if(use_date < "{{ date('Y-m-d') }}"){
          $.ajax({
            url : '{{ route('booking.check-unc') }}?id=' + id,
            type : 'GET',
            success : function(data){
              $('#error_unc_' + id).text(data);
            }
          });
        }
      });
      $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
      });

      $("#check_all").click(function(){
          $('input.check_one').not(this).prop('checked', this.checked);
      });
      $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.cong-no-tour') }}").submit().attr('action', oldAction);
      });
      $('#btnExportGui').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.gui-tour') }}").submit().attr('action', oldAction);
      });
      $('#temp').click(function(){
        $(this).parents('form').submit();
      });
    	$('.change_status').click(function(){
		      var obj = $(this);
		      $.ajax({
		        url : "{{ route('change-export-status') }}",
		        type : 'GET',
		        data : {
		          id : obj.data('id')
		        },
		        success: function(){
		          window.location.reload();
		        }
		      });
		    });
       $('.change_status_bk').click(function(){
          var obj = $(this);
          $.ajax({
            url : "{{ route('change-status') }}",
            type : 'GET',
            data : {
              id : obj.data('id')
            },
            success: function(){
              //window.location.reload();
            }
          });
        });
       $('.change-column-value').change(function(){
          var obj = $(this);
          if(obj.data('column') == 'cano_id'){
           // alert('Tất cả các booking cùng HDV sẽ được gán chung vào cano này');
          }
          $.ajax({
            url : "{{ route('booking.change-value-by-column') }}",
            type : 'GET',
            data : {
              id : obj.data('id'),
              col : obj.data('column'),
              value: obj.val()
            },
            success: function(data){
                console.log(data);
            }
          });
       });
       $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              $.ajax({
                url : "{{ route('booking.change-value-by-column') }}",
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
      $('.hoa_hong_sales').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('save-hoa-hong')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            hoa_hong_sales : obj.val()
          },
          success : function(doc){

          }
        });

      });
      $('.hdv').click(function(){
        var hdv_id = $(this).data('id');
        if(hdv_id != ""){
          $('#hdv_id').val($(this).data('id'));
          $('#searchForm').submit();
        }

      });
      $('.change_tien_thuc_thu').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'tien_thuc_thu'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
       $('.change_tien_coc').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'tien_coc'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
      $('.change_total_price').blur(function(){
        var obj = $(this);
        $.ajax({
          url:'{{ route('booking.change-value-by-column') }}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : obj.val(),
            col : 'total_price'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
      $('.change_price_net').click(function(){
        var obj = $(this);
        var price_net = 0;
        if(obj.prop('checked') == true){
          price_net = 1
        }
        $.ajax({
          url:'{{ route('booking.change-value-by-column')}}',
          type:'GET',
          data: {
            id : obj.data('id'),
            value : price_net,
            col : 'price_net'
          },
          success : function(doc){
            console.log(data);
          }
        });
        });
      $('#btnReset').click(function(){
        $('#searchForm select').val('');
        $('#searchForm').submit();
      });
    });
  </script>
@stop
