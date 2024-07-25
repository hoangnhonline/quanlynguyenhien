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
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    @if($multi == 0 && !Auth::user()->view_only)
                        <a href="{{ route('cost.create',['month' => $month, 'cate_id' => $cate_id]) }}"
                           class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Bộ lọc</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-inline" role="form" method="GET" action="{{ route('cost.index') }}"
                                  id="searchForm">
                                <div class="form-group">
                                    <input type="text" class="form-control" autocomplete="off" name="id_search"
                                           placeholder="ID" value="{{ $arrSearch['id_search'] }}" style="width: 100px">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" autocomplete="off" name="code_ung_tien"
                                           placeholder="Code ứng" value="{{ $arrSearch['code_ung_tien'] }}"
                                           style="width: 100px">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" autocomplete="off" name="code_chi_tien"
                                           placeholder="Code chi" value="{{ $arrSearch['code_chi_tien'] }}"
                                           style="width: 100px">
                                </div>
                                <div class="form-group">              
                                    <select class="form-control select2" name="time_type" id="time_type">                
                                      <option value="1" {{ $time_type == 1 ? "selected" : "" }}>Theo tháng</option>
                                      <option value="2" {{ $time_type == 2 ? "selected" : "" }}>Khoảng ngày</option>
                                      <option value="3" {{ $time_type == 3 ? "selected" : "" }}>Ngày cụ thể </option>
                                    </select>
                                  </div> 
                                  @if($time_type == 1)
                                  <div class="form-group  chon-thang">                
                                      <select class="form-control select2" id="month_change" name="month">
                                        <option value="">--Tháng--</option>
                                        @for($i = 1; $i <=12; $i++)
                                        <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                                        @endfor
                                      </select>
                                    </div>
                                    <div class="form-group  chon-thang">                
                                      <select class="form-control select2" id="year_change" name="year">
                                        <option value="">--Năm--</option>                  
                                        <option value="2022" {{ $year == 2022 ? "selected" : "" }}>2022</option>
                                        <option value="2023" {{ $year == 2023 ? "selected" : "" }}>2023</option>
                                        <option value="2024" {{ $year == 2024 ? "selected" : "" }}>2024</option>
                                        <option value="2025" {{ $year == 2025 ? "selected" : "" }}>2025</option>
                                      </select>
                                    </div>
                                  @endif
                                  @if($time_type == 2 || $time_type == 3)
                                  
                                  <div class="form-group chon-ngay">              
                                    <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif" value="{{ $arrSearch['use_date_from'] }}" style="width: 100px">
                                  </div>
                                 
                                  @if($time_type == 2)
                                  <div class="form-group chon-ngay den-ngay">              
                                    <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" style="width: 100px">
                                  </div>
                                   @endif
                                  @endif
                                <div class="form-group">
                                    <select class="form-control select2" name="status" id="status">
                                        <option value="">--Trạng thái--</option>
                                        <option value="1" {{ $arrSearch['status'] == 1 ? "selected" : "" }}>Chưa thanh
                                            toán
                                        </option>
                                        <option value="2" {{ $arrSearch['status'] == 2 ? "selected" : "" }}>Đã thanh
                                            toán
                                        </option>
                                        <option value="3" {{ $arrSearch['status'] == 3 ? "selected" : "" }}>Thanh toán
                                            sau
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">

                                    <select class="form-control select2 search-form-change" name="city_id" id="city_id">
                                        <option value="">--Tỉnh/Thành--</option>
                                        @foreach($cityList as $city)
                                            <option
                                                value="{{ $city->id }}" {{ $city_id == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 search-form-change" name="type" id="type">
                                        <option value="">--Phân loại--</option>
                                        @foreach($costCate as $item)
                                            <option
                                                value="{{ $item->id }}" {{ $type == $item->id ? "selected" : "" }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="tour_id" id="tour_id"
                                            style="width: 150px;">
                                        <option value="">--Tour--</option>
                                        @foreach($tourSystem as $tour)
                                            <option
                                                value="{{ $tour->id }}" {{ $arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="tour_no">
                                        <option value="">--Tour số--</option>
                                        @for($i = 1; $i<=10; $i++)
                                            <option
                                                value="{{ $i }}" {{ $arrSearch['tour_no'] == $i ? "selected" : "" }}>
                                                Tour {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @if($arrSearch['multi'] == 0)
                                    <div class="form-group">
                                        <select class="form-control select2 search-form-change" name="cate_id"
                                                id="cate_id">
                                            <option value="">--Loại phí--</option>
                                            @foreach($cateList as $cate)
                                                <option
                                                    value="{{ $cate->id }}" {{ $arrSearch['cate_id'] == $cate->id ? "selected" : "" }}>{{ $cate->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group search-form-change" id="load_doi_tac">
                                    @if(!empty($partnerList ) || $partnerList->count() > 0)

                                        <select class="form-control select2" id="partner_id" name="partner_id">
                                            <option value="">--Chi tiết--</option>
                                            @foreach($partnerList as $cate)
                                                <option
                                                    value="{{ $cate->id }}" {{ $partner_id == $cate->id ? "selected" : "" }}>
                                                    {{ $cate->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                    @endif
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 search-form-change" name="nguoi_chi"
                                            id="nguoi_chi">
                                        <option value="">--Người chi--</option>
                                        @foreach($collecterList as $payer)
                                            <option
                                                value="{{ $payer->id }}" {{ $nguoi_chi == $payer->id ? "selected" : "" }}>{{ $payer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                
                                <div class="form-group" style="border-right: 1px solid #9ba39d">
                                    &nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_fixed" id="is_fixed"
                                                             {{ $arrSearch['is_fixed'] == 1 ? "checked" : "" }} value="1"
                                                             class="search-form-change">
                                    <label for="is_fixed">Cố định&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                </div>
                                <div class="form-group" style="border-right: 1px solid #9ba39d">
                                    &nbsp;&nbsp;&nbsp;<input type="checkbox" name="hoang_the" id="hoang_the"
                                                             {{ $arrSearch['hoang_the'] == 1 ? "checked" : "" }} value="1"
                                                             class="search-form-change">
                                    <label for="hoang_the" style="color: red">Hoàng Thể&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                </div>
                                <div class="form-group" style="border-right: 1px solid #9ba39d">
                                    &nbsp;&nbsp;&nbsp;<input type="checkbox" name="multi" id="multi"
                                                             {{ $arrSearch['multi'] == 1 ? "checked" : "" }} value="1"
                                                             class="search-form-change">
                                    <label for="multi">Nhiều loại</label>
                                </div>
                                <div class="form-group" style="border-right: 1px solid #9ba39d">
                                    &nbsp;&nbsp;&nbsp;<input type="checkbox" name="old" id="old"
                                                             {{ $arrSearch['old'] == 1 ? "checked" : "" }} value="1"
                                                             class="search-form-change">
                                    <label for="old">Dữ liệu cũ</label>
                                </div>
                                @if($arrSearch['multi'] == 1)
                                    <p>

                                        @foreach($cateList as $cate)
                                            <input type="checkbox" name="cate_id_multi[]" id="cate_id{{ $cate->id }}"
                                                   value="{{ $cate->id }}" {{ in_array($cate->id, $arrSearch['cate_id_multi']) ? "checked" : "" }}>
                                            <label style="cursor: pointer;"
                                                   for="cate_id{{ $cate->id }}">{{ $cate->name }}</label>
                                        @endforeach
                                    </p>
                                @endif

                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                            </form>
                        </div>
                    </div>
                    <p style="text-align: right;"><a href="javascript:;" class="btn btn-primary btn-sm" id="btnExport">Export
                            Excel</a>
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
                            <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} mục )</span> -
                                Tổng tiền: <span style="color:red">{{ number_format($total_actual_amount) }} </span>- Số
                                lượng: <span style="color:red">{{ $total_quantity }} </span></h3>
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
                                    <select style="width: 150px" class="form-control select2  multi-change-column-value"
                                            data-column="tour_id">
                                        <option value="">--Tour--</option>
                                        @foreach($tourSystem as $tour)
                                            <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 multi-change-column-value"
                                            data-column="tour_no">
                                        <option value="">--Tour số--</option>
                                        @for($i = 1; $i<=10; $i++)
                                            <option value="{{ $i }}">Tour {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 multi-change-column-value" data-column="status">
                                        <option value="">--SET TRẠNG THÁI--</option>
                                        <option value="1">Chưa thanh toán</option>
                                        <option value="2">Đã thanh toán</option>
                                        <option value="3">Thanh toán sau</option>
                                    </select>
                                </div>


                                <div class="form-group">
                                    <select class="form-control select2 multi-change-column-value"
                                            data-column="nguoi_chi">
                                        <option value="">--SET NGƯỜI CHI--</option>
                                        @foreach($collecterList as $payer)
                                            <option value="{{ $payer->id }}">{{ $payer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 multi-change-column-value"
                                            data-column="city_id">
                                        <option value="">--SET TỈNH/THÀNH--</option>
                                        @foreach($cityList as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 multi-change-column-value"
                                            data-column="cate_id">
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
                            @if($multi == 0)
                                <div style="text-align:center">
                                    {{ $items->appends( $arrSearch )->links() }}
                                </div>
                            @endif
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
                                    @if(!Auth::user()->view_only)
                                        <th width="1%;white-space:nowrap">Thao tác</th>
                                    @endif
                                </tr>
                                <tbody>
                                @if( $items->count() > 0 )
                                        <?php $i = 0; ?>
                                    @foreach( $items as $item )
                                            <?php $i++; ?>
                                        <tr class="cost" id="row-{{ $item->id }}">
                                            <td>
                                                @if(!$item->time_chi_tien)
                                                    <input type="checkbox" id="checked{{ $item->id }}" class="check_one"
                                                           value="{{ $item->id }}">
                                                @endif
                                            </td>
                                            <td><span class="order">{{ $i }}</span></td>
                                            <td class="text-left">
                                                <strong style="color: red">{{ $item->id }}</strong><br>
                                                {{ date('H:i d/m', strtotime($item->created_at)) }}
                                            </td>
                                            <td class="text-left">
                                                {{ date('d/m/y', strtotime($item->date_use)) }} <br>
                                                @if($item->city_id == 1)
                                                    <span style="color: green">Phú Quốc</span>
                                                @elseif($item->city_id == 3)
                                                    <span style="color: yellow">HCM</span>
                                                @else
                                                    <span style="color: blue">Đà Nẵng</span>
                                                @endif
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
                                            </td>
                                            <td class="text-center">
                                                @if($item->tour_id)
                                                <label class="label" style="background-color:{{ @$tourSystemName[$item->tour_id]['bg_color'] }}">{{ @$tourSystemName[$item->tour_id]['name'] }}</label>
                                                <br>
                                                @endif
                                                
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
                                                @if($item->tour_no)
                                                    <p class="label label-sm
                                              @if($item->tour_no == 1)
                                              label-success
                                              @elseif($item->tour_no == 2)
                                              label-info
                                              @elseif($item->tour_no == 3)
                                              label-warning
                                              @else
                                              label-default
                                              @endif
                                              ">Tour {{ $item->tour_no }}</p>
                                                                            @endif

                                               
                                            </td>
                                            <td>
                                                @if($item->costType)
                                                        <?php
                                                        $str = $item->partner_id;
                                                        ?>
                                                    <a href="https://plantotravel.vn/cost/{{ Helper::mahoa('mahoa', $str ) }}">{{ $item->costType->name }}</a>
                                                @endif
                                                @if($item->partner && !$item->user_id)
                                                    - {{ $item->partner->name }}
                                                @endif
                                                @if($item->user)
                                                    - {{ $item->user->name }}
                                                @endif
                                                @if($item->is_fixed == 1)
                                                    <label class="label label-success">Cố định</label>
                                                @endif
                                                <p style="color:red; font-style: italic">{{ $item->notes }}</p>
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
                                            </td>
                                            <td class="text-center">
                                                @if($item->image_url && $item->unc_type == 1)
                                                    <span style="color: blue; cursor: pointer;" class="img-unc"
                                                          data-src="{{ config('plantotravel.upload_url').$item->image_url }}">XEM ẢNH</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->amount }}</td>
                                            <td class="text-right">{{ number_format($item->price) }}</td>
                                            <td class="text-right">
                                                {{ number_format($item->total_money) }}
                                            </td>
                                            <td class="text-center" style="white-space: nowrap;"
                                                data-id="{{ $item->nguoi_chi }}">
                                                @if($item->nguoi_chi)
                                                    {{ $collecterNameArr[$item->nguoi_chi] }}
                                                @endif
                                            </td>
                                            <td style="white-space: nowrap;">
                                                @if($item->code_ung_tien)
                                                    @if($item->time_ung_tien)
                                                        <label class="label label-success">Đã ứng tiền</label>
                                                    @endif
                                                    <span style="font-weight: bold; color: #00a65a"
                                                          title="Mã ứng tiền">{{ $item->code_ung_tien }}</span>
                                                @endif

                                                @if($item->time_chi_tien)
                                                    <br>
                                                    <label class="label label-danger">Đã chi tiền</label>
                                                @endif
                                                @if($item->code_chi_tien)
                                                    <span style="font-weight: bold; color: red"
                                                          title="Mã chi tiền">{{ $item->code_chi_tien }}</span>
                                                @endif
                                            </td>

                                            @if(!Auth::user()->view_only)
                                                <td style="white-space:nowrap">
                                                    <a href="{{ route( 'cost.copy', [ 'id' => $item->id ]) }}"
                                                       class="btn btn-info btn-sm"><span
                                                            class="glyphicon glyphicon-duplicate"></span></a>

                                                    @if($item->bank_info_id)
                                                        <a data-bank-no="{{ $item->bank->bank_no }}"
                                                           href="https://img.vietqr.io/image/{{str_replace(' ', '', strtolower($item->bank->bank_name))}}-{{$item->bank->bank_no}}-compact2.png?amount={{$item->total_money}}&accountName={{$item->bank->account_name}}&addInfo=COST {{ $item->id }} {{$item->noi_dung_ck}}"
                                                           data-id="{{$item->id}}"
                                                           class="btn {{$item->qrcode_clicked > 0 ? 'btn-warning' : 'btn-primary'}} btn-sm btn-qrcode">
                                                            <span class="glyphicon glyphicon-qrcode"></span><span
                                                                class="clicked-qr">{{$item->qrcode_clicked ? ' (' .$item->qrcode_clicked . ')' : ''}}</span></a>
                                                    @endif
                                                    <a href="{{ route( 'cost.edit', [ 'id' => $item->id ]) }}"
                                                       class="btn btn-warning btn-sm"><span
                                                            class="glyphicon glyphicon-pencil"></span></a>
                                                    @if($item->costType)
                                                        <a onclick="return callDelete('{{ $item->costType->name . " - ".number_format($item->total_money) }}','{{ route( 'cost.destroy', [ 'id' => $item->id ]) }}');"
                                                           class="btn btn-danger btn-sm"><span
                                                                class="glyphicon glyphicon-trash"></span></a>
                                                    @else
                                                        <a onclick="return callDelete('{{ number_format($item->total_money) }}','{{ route( 'cost.destroy', [ 'id' => $item->id ]) }}');"
                                                           class="btn btn-danger btn-sm"><span
                                                                class="glyphicon glyphicon-trash"></span></a>
                                                    @endif
                                                </td>
                                            @endif
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
                        @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                            <div class="form-inline" style="padding: 5px">
                                <div class="form-group">
                                    <select class="form-control select2 multi-change-column-value"
                                            data-column="is_fixed">
                                        <option value="">--SET CỐ ĐỊNH--</option>
                                        <option value="0">Ko cố định</option>
                                        <option value="1">Cố định</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 multi-change-column-value"
                                            data-column="nguoi_chi">
                                        <option value="">--SET NGƯỜI CHI--</option>
                                        @foreach($collecterList as $payer)
                                            <option value="{{ $payer->id }}">{{ $payer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 multi-change-column-value"
                                            data-column="city_id">
                                        <option value="">--SET TỈNH/THÀNH--</option>
                                        @foreach($cityList as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2 multi-change-column-value"
                                            data-column="cate_id">
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
            $('.multi-change-column-value').change(function () {
                var obj = $(this);
                $('.check_one:checked').each(function () {
                    $.ajax({
                        url: "{{ route('cost.change-value-by-column') }}",
                        type: 'GET',
                        data: {
                            id: $(this).val(),
                            col: obj.data('column'),
                            value: obj.val()
                        },
                        success: function (data) {

                        }
                    });
                });

            });
            $('.btn-qrcode').click(function (e) {
                e.preventDefault();
                $('#qrCodeModal').find('img').attr('src', $(this).attr('href'));
                $('#qrCodeModal').modal('show');
                var button = $(this);
                button.removeClass('btn-primary').addClass('btn-warning');
                $.ajax({
                    url: "{{ route('cost.view-qr-code') }}?id=" + $(this).data('id'),
                    type: 'GET',
                    data: {
                        id: $(this).data('id')
                    },
                    success: function (response) {
                        console.log(response);
                        button.find('.clicked-qr').text(' (' + response.data + ')')
                    }
                });
            });
            $('#btnContentUng').click(function () {
                var obj = $(this);
                var str_id = '';
                $('.check_one:checked').each(function () {
                    str_id += $(this).val() + ',';
                });
                console.log(str_id);
                if (str_id != '') {
                    $.ajax({
                        url: "{{ route('cost.get-confirm-ung') }}",
                        type: 'GET',
                        data: {
                            str_id: str_id
                        },
                        success: function (data) {
                            $('#loadConfirm').html(data);
                            $('#confirmUngModal').modal('show');
                        }
                    });
                }

            });
            $('#btnContentChi').click(function () {
                var obj = $(this);
                var str_id = '';
                $('.check_one:checked').each(function () {
                    str_id += $(this).val() + ',';
                });
                if (str_id != '') {
                    $.ajax({
                        url: "{{ route('cost.get-confirm-chi') }}",
                        type: 'GET',
                        data: {
                            str_id: str_id
                        },
                        success: function (data) {
                            $('#loadConfirmChi').html(data);
                            $('#confirmChiModal').modal('show');
                        }
                    });
                }

            });

            $('#btnYcUng').click(function () {
                var obj = $(this);
                var str_id = '';
                $('.check_one:checked').each(function () {
                    str_id += $(this).val() + ',';
                });

                if (str_id != '') {
                    $.ajax({
                        url: "{{ route('cost.get-content-ung') }}",
                        type: 'GET',
                        data: {
                            str_id: str_id
                        },
                        success: function (data) {
                            $('#noi_dung_ung').html(data);
                            $('#btnYcUng').hide();
                        }
                    });
                }

            });
            $('#btnYcChi').click(function () {
                var obj = $(this);
                var str_id = '';
                $('.check_one:checked').each(function () {
                    str_id += $(this).val() + ',';
                });

                if (str_id != '') {
                    $.ajax({
                        url: "{{ route('cost.get-content-chi') }}",
                        type: 'GET',
                        data: {
                            str_id: str_id
                        },
                        success: function (data) {
                            $('#noi_dung_chi').html(data);
                            $('#btnYcChi').hide();
                        }
                    });
                }

            });

            $('tr.cost').click(function () {
                $(this).find('.check_one').attr('checked', 'checked');
            });
            $("#check_all").click(function () {
                $('input.check_one').not(this).prop('checked', this.checked);
            });
            $('#btnExport').click(function () {
                var oldAction = $('#searchForm').attr('action');
                $('#searchForm').attr('action', "{{ route('cost.export') }}").submit().attr('action', oldAction);
            });
            // $('#partner_id').on('change', function(){
            //   $(this).parents('form').submit();
            // });
            $('#cate_id').change(function () {
                $.ajax({
                    url: "{{ route('cost.ajax-doi-tac') }}",
                    data: {
                        cate_id: $(this).val(),
                        city_id: {{ $city_id }}
                    },
                    type: "GET",
                    success: function (data) {
                        if (data != 'null') {
                            $('#load_doi_tac').html(data);
                            if ($('#partner_id').length == 1) {
                                $('#partner_id').select2();
                            }
                        }
                    }
                });
            });
        });
        $(document).ready(function () {
            $('.img-unc').click(function () {
                $('#unc_img').attr('src', $(this).data('src'));
                $('#uncModal').modal('show');
            });
        });

    </script>
@stop
