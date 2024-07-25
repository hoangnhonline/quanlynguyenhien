@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 style="text-transform: uppercase;">
                DANH SÁCH ĐẶT XE MIỄN PHÍ
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('booking-xe-free.index') }}" style="margin-bottom:5px">Quay
                lại</a>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Bộ lọc</h3>
                </div>
                <div class="panel-body">
                    <form class="form-inline" role="form" method="GET" action="{{ route('booking-xe-free.index') }}"
                        id="searchForm">
                        <div class="form-group">
                            <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
                        </div>
                        @if (Auth::user()->role == 1 && !Auth::user()->view_only)
                            <div class="form-group">
                                <select class="form-control select2" name="driver_id" id="driver_id">
                                    <option value="">--Tài xế--</option>
                                    @foreach ($driverList as $driver)
                                        <option value="{{ $driver->id }}"
                                            {{ $arrSearch['driver_id'] == $driver->id ? 'selected' : '' }}>
                                            {{ $driver->name }}
                                            @if ($driver->is_verify == 1)
                                                - HĐ
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control select2" name="car_cate_id" id="car_cate_id">
                                    <option value="">--Loại xe--</option>
                                    @foreach ($carCate as $cate)
                                        <option value="{{ $cate->id }}"
                                            {{ $arrSearch['car_cate_id'] == $cate->id ? 'selected' : '' }}>
                                            {{ $cate->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <select class="form-control select2" name="user_id" id="user_id">
                                    <option value="">--Sales--</option>
                                    @foreach ($listUser as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $arrSearch['user_id'] == $user->id ? 'selected' : '' }}>{{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}"
                                placeholder="Số ĐT" style="width: 120px">
                        </div>
                        <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                        <div>
                            <div class="form-group">
                                <input type="checkbox" class="search-form-change" name="status[]" id="status_1"
                                    {{ in_array(1, $arrSearch['status']) ? 'checked' : '' }} value="1">
                                <label for="status_1">Mới</label>
                            </div>
                            <div class="form-group">
                                &nbsp;&nbsp;&nbsp;<input class="search-form-change" type="checkbox" name="status[]"
                                    id="status_2" {{ in_array(2, $arrSearch['status']) ? 'checked' : '' }} value="2">
                                <label for="status_2">Hoàn Tất</label>
                            </div>
                            <div class="form-group" style="border-right: 1px solid #9ba39d">
                                &nbsp;&nbsp;&nbsp;<input class="search-form-change" type="checkbox" name="status[]"
                                    id="status_3" {{ in_array(3, $arrSearch['status']) ? 'checked' : '' }} value="3">
                                <label for="status_3">Huỷ&nbsp;&nbsp;&nbsp;&nbsp;</label>
                            </div>
                            <div class="form-group">
                                &nbsp;&nbsp;&nbsp;<input class="search-form-change" type="checkbox" name="no_driver"
                                    id="no_driver" {{ $arrSearch['no_driver'] == 1 ? 'checked' : '' }} value="1">
                                <label for="no_driver" style="color: red">CHƯA CHỌN TÀI XẾ</label>
                            </div>


                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- left column -->
                <p class="col-md-12" style="color: red; font-style: italic;; font-weight: bold;">Đặt xe đón trong ngày vui
                    lòng thông báo cho số hotline Phu Quoc Trans : 0911380111 sau khi tạo để được hỗ trợ tốt nhất.</p>
                <div class="col-md-12">
                    <div id="content_alert"></div>
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title col-md-8">Danh sách ( <span class="value">{{ $items->count() }} booking
                                    )</span>
                                - Tổng chi phí <span class="hot">{{ number_format($total_cost) }}</span>
                            </h3>


                        </div>
                        <div class="box-body">
                            <div class="table-responsive">

                                <table class="table table-bordered" id="table_report"
                                    style="margin-bottom:0px;font-size: 14px;">
                                    <tr style="background-color: #ffff99">
                                        <th>STT</th>
                                        <th>Thời gian tạo</th>
                                        <th>Ngày giờ</th>
                                        <th>Mã BK</th>
                                        <th>Khách</th>
                                        <th>Loại xe</th>
                                        <th>Nơi đón</th>
                                        <th>Nơi trả</th>
                                        <th>Tài xế</th>
                                        <th>Trạng thái</th>
                                        <th class="text-right">Chi phí</th>
                                        @if(!Auth::user()->view_only)
                                        <th>Thao tác</th>
                                        @endif
                                    </tr>
                                    <?php $i = 0; ?>
                                    @foreach ($items as $bk)
                                        <?php $i++;
                                        $today = false;
                                        if ($bk->use_date == date('Y-m-d')) {
                                            $today = true;
                                        }
                                        ?>
                                        <tr @if ($today) style="background-color:#ffcccc" @endif>
                                            <td class="text-center">{{ $i }}</td>
                                            <td style="white-space: nowrap;">
                                                {{ date('H:i d/m', strtotime($bk->created_at)) }} </td>
                                            <td style="white-space: nowrap;">{{ $bk->use_time }}
                                                {{ date('d/m', strtotime($bk->use_date)) }} </td>
                                            <td>{{ $bk->booking_id }}
                                                <br>
                                                <span style="color: blue">{{ $bk->booking->user->name }}</span>
                                                -<a
                                                    href="tel:{{ $bk->booking->user->phone }}">{{ $bk->booking->user->phone }}</a>
                                            </td>
                                            <td>
                                                @if ($bk->name)
                                                    {{ $bk->name }} - <a
                                                        href="tel:{{ $bk->phone }}">{{ $bk->phone }}</a>
                                                @else
                                                    {{ $bk->booking->name }} - <a
                                                        href="tel:{{ $bk->booking->phone }}">{{ $bk->booking->phone }}</a>
                                                @endif
                                                <br>
                                                <span style="color: red; font-style: italic;">{{ $bk->notes }}</span>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                @if ($bk->car_cate_id)
                                                    {{ $bk->carCate->name }}
                                                @endif
                                                @if ($bk->booking)
                                                    <p style="font-weight: bold"> {{ $bk->booking->adults }} NL /
                                                        {{ $bk->booking->childs }} TE/ {{ $bk->booking->infants }} EB</p>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($bk->location)
                                                    {{ $bk->location->name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($bk->location_id_2)
                                                    {{ $bk->location2->name }}
                                                @endif
                                            </td>
                                            @if (Auth::user()->role == 1 && !Auth::user()->view_only)
                                                <td class="text-center"
                                                    @if ($bk->driver_id == 0) style="background-color: #6ce8eb" @endif>
                                                    @if ($bk->driver_id > 0)
                                                        <strong>{{ $bk->driver->name }}</strong>
                                                        @if ($bk->driver->phone)
                                                            <br><i class="glyphicon glyphicon-phone"></i> <a
                                                                href="tel:{{ $bk->driver->phone }}">{{ $bk->driver->phone }}</a>
                                                        @endif
                                                    @else
                                                        <select style="width: 100%"
                                                            class="form-control select2 change-column-value"
                                                            data-id="{{ $bk->id }}" data-column="driver_id"
                                                            data-table="don_tien_free">
                                                            <option value="">--Chọn tài xế--</option>
                                                            @foreach ($driverList as $driver)
                                                                <option value="{{ $driver->id }}">{{ $driver->name }}
                                                                    @if ($driver->is_verify == 1)
                                                                        - HĐ
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif

                                                </td>
                                            @else
                                                <td>
                                                    @if ($bk->driver_id > 0)
                                                        <strong>{{ $bk->driver->name }}</strong>
                                                        <br><i class="glyphicon glyphicon-phone"></i> <a
                                                            href="tel:{{ $bk->driver->phone }}">{{ $bk->driver->phone }}</a>
                                                    @else
                                                        Chưa chọn
                                                    @endif
                                                </td>
                                            @endif

                                            <td class="text-center">
                                                @if (Auth::user()->role == 1 && !Auth::user()->view_only)
                                                    <select class="form-control change-column-value"
                                                        data-id="{{ $bk->id }}" data-column="status"
                                                        data-table="don_tien_free">
                                                        <option value="">--Trạng thái--</option>
                                                        <option value="1" {{ $bk->status == 1 ? 'selected' : '' }}>
                                                            Mới</option>
                                                        <option value="2" {{ $bk->status == 2 ? 'selected' : '' }}>
                                                            Hoàn tất</option>
                                                        <option value="3" {{ $bk->status == 3 ? 'selected' : '' }}>
                                                            Hủy</option>
                                                    </select>
                                                @else
                                                    @if ($bk->status == 1)
                                                        <label class="label label-info">Mới</label>
                                                    @elseif($bk->status == 2)
                                                        <label class="label label-default">Hoàn tất</label>
                                                    @else
                                                        <label class="label label-danger">Hủy</label>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($bk->cost) }}
                                            </td>

                                            @if(!Auth::user()->view_only)
                                            <td class="text-center" style="white-space: nowrap;">

                                                @php
                                                    $countUNC = $bk->payment->count();
                                                    $strpayment = '';
                                                    $tong_payment = 0;
                                                    foreach ($bk->payment as $p) {
                                                        $strpayment .= '+' . number_format($p->amount) . ' - ' . date('d/m', strtotime($p->pay_date));
                                                        if ($p->type == 1) {
                                                            $strpayment .= ' - UNC' . '<br>';
                                                        } else {
                                                            $strpayment .= ' - auto' . '<br>';
                                                        }
                                                        $tong_payment += $p->amount;
                                                    }
                                                    if ($countUNC > 0) {
                                                        $strpayment .= 'Tổng: ' . number_format($tong_payment);
                                                    }
                                                @endphp
                                                <a data-toggle="tooltip" data-html="true" title="{!! $strpayment !!}"
                                                    href="{{ route('don-tien-free-payment.index', ['don_tien_id' => $bk->id]) }}"
                                                    class="btn btn-info btn-sm"><span
                                                        class="glyphicon glyphicon-usd"></span>
                                                    @if ($countUNC > 0)
                                                        [ {{ $countUNC }} ]
                                                    @endif
                                                </a>

                                                @if ($bk->status == 1 || Auth::user()->role == 1 && !Auth::user()->view_only)
                                                    <a href="{{ route('booking-xe-free.edit', $bk->id) }}"
                                                        class="btn btn-warning btn-sm"><span
                                                            class="glyphicon glyphicon-pencil"></span></a>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- /.box -->

                </div>

                <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@stop
