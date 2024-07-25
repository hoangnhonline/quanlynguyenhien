@extends('layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Quản lí khách hàng
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route( 'dashboard' ) }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('customer.index') }}">Khách hàng</a></li>
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
                <a href="{{ route('customer.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Bộ lọc</h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-inline" role="form" method="GET" action="{{ route('customer.index') }}" id="searchForm">
                            <div class="form-group">
                                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}"  style="width: 150px"/>
                            </div>

                            <div class="form-group ">
                              <select class="form-control select2 search-form-change" name="city_id" id="city_id">
                                <option value="">--Tỉnh/Thành--</option>
                                @foreach($cityList as $city)
                                <option value="{{ $city->id }}" {{ $arrSearch['city_id'] == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                                @endforeach
                              </select>
                            </div>

                            @if(Auth::user()->role < 3)
                                <div class="form-group ">
                                    <select class="form-control select2 search-form-change" style="width: 150px" name="user_id" id="user_id">
                                        <option value="">--User--</option>
                                        @foreach($listUser as $user)
                                            <option value="{{ $user->id }}" {{ @$arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group ">
                                <select class="form-control select2" name="source" id="source">
                                    <option value="">-- Chọn nguồn --</option>
                                    @foreach($sources as $source)
                                        <option
                                            value="{{$source->id}}" {{ @$arrSearch['source'] == $source->id ? "selected" : "" }}>{{$source->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <select class="form-control select2" name="source2" id="source2" style="width: 200px">
                                    <option value="">-- Chọn nguồn 2 --</option>
                                    @foreach($sources2 as $source2)
                                        <optgroup label="{{$source2->name}}">
                                            @foreach($source2->childs as $source)
                                                <option
                                                    value="{{$source->id}}" {{@$arrSearch['source2'] == $source->id ? "selected" : "" }}>{{$source->name}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <select class="form-control select2 search-form-change" name="status" id="status">
                                    <option value="">-- Trạng thái-- </option>
                                    <option value="1" {{ $arrSearch['status'] == 1 ? "selected" : "" }}>Đang tư vấn</option>
                                    <option value="6" {{ $arrSearch['status'] == 6 ? "selected" : "" }}>Sắp chốt</option>
                                    <option value="2" {{ $arrSearch['status'] == 2 ? "selected" : "" }}>Đã chốt</option>
                                    <option value="3" {{ $arrSearch['status'] == 3 ? "selected" : "" }}>Đã hoàn thành</option>
                                    <option value="4" {{ $arrSearch['status'] == 4 ? "selected" : "" }}>Không chốt được</option>
                                    <option value="5" {{ $arrSearch['status'] == 5 ? "selected" : "" }}>Không có nhu cầu</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control select2 search-form-change" name="product_type" id="product_type">
                                    <option value="">-- Sản phẩm quan tâm -- </option>
                                    @php
                                        $types = ['1' => 'Tour', '2' => 'Combo', '3' => 'Khách sạn', '4' => 'Vé tham quan', '5'=> 'Xe']
                                    @endphp
                                    @foreach($types as $key => $type)
                                        <option
                                            value="{{$key}}" {{ $arrSearch['product_type'] == $key ? "selected" : "" }}>{{$type}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control search-form-change" name="phone" value="{{ $arrSearch['phone'] }}" placeholder="Số ĐT" maxlength="10">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control search-form-change" name="name" value="{{ $arrSearch['name'] }}" placeholder="Họ tên" autocomplete="off">
                            </div>
                            <div class="form-group" >
                              <input type="checkbox" style="cursor: pointer;" name="ads" id="ads" {{ $arrSearch['ads'] == 1 ? "checked" : "" }} value="1" class="search-form-change">
                              <label for="ads" style="cursor: pointer; color: red" >ADS</label>
                            </div>
                            <div class="form-group" >
                              <input type="checkbox" style="cursor: pointer;" name="ask_more" id="ask_more" {{ $arrSearch['ask_more'] == 1 ? "checked" : "" }} value="1" class="search-form-change">
                              <label for="ask_more" style="cursor: pointer; color: red" >Tôi muốn biết thêm...</label>
                            </div>
                            <!-- <div class="form-group">
                                <label for="code">&nbsp;&nbsp;Mã Code</label>
                                <input type="text" class="form-control" name="code" value="{{ $arrSearch['code'] }}">
                            </div> -->
                            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
                        </form>
                    </div>
                </div>
                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} Khách hàng
                                )</span></h3>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <a href="{{ route('customer.export') }}" class="btn btn-info btn-sm"
                            style="margin-bottom:5px;float:right" target="_blank">Export</a>
                        <div class="clearfix"></div>
                        <div style="text-align:center">
                            {{ $items->appends( $arrSearch )->links() }}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-list-data">
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th>Họ tên</th>
                                    <th>Số ĐT/Email</th>
                                    <th>Ngày liên hệ</th>
                                    <th>Sản phẩm quan tâm</th>
                                    <th>Trạng thái</th>
                                    <th>Booking</th>
                                    <th width="1%" style="white-space:nowrap">Thao tác</th>
                                </tr>
                                <tbody>
                                    @if( $items->count() > 0 )
                                    <?php $i = 0; ?>
                                    @foreach( $items as $item )
                                    <?php $i ++; ?>
                                    <tr id="row-{{ $item->id }}">
                                        <td><span class="order">{{ $i }}</span></td>
                                        <td>                                           
                                            {{ $item->name }}
                                            @if($item->facebook)
                                            <br><img src="{{ asset('images/fb.png') }}" width="15">
                                             <a href="{{ $item->facebook }}" style="
                                                display: inline-block;
                                                width: 280px;
                                                overflow: hidden;
                                                white-space: nowrap;
                                                text-overflow: ellipsis;
                                                position: relative;
                                                top: 6px;
                                            ">{{ $item->facebook }}</a>
                                            @endif
                                            @if(!empty($item->ads))
                                                <br/><span style="color: red"><i class="fa fa-safari"></i> ADS: {{$item->adsCampaign ? $item->adsCampaign->name : 'N/A' }}</span>
                                                @if($item->ask_more == 1)
                                                <br><span style="color: red">Tôi muốn biết thêm về doanh nghiệp.</span>
                                                @endif
                                            @endif
                                            @if(!empty($item->sourceRef))
                                                <br/><i class="fa fa-tag"></i> {{ $item->sourceRef->name }} @if(!empty($item->source2Ref)) - {{ $item->source2Ref->name }} @endif
                                            @endif
                                            @if(Auth::user()->role == 1 && !Auth::user()->view_only)
                                            <br><i class="glyphicon glyphicon-user"></i>
                                              @if($item->user)
                                                {{ $item->user->name }}
                                              @else
                                                {{ $item->user_id }}
                                              @endif
                                            @endif
                                            @if($item->notes)
                                                <br>
                                                <i class="glyphicon glyphicon-comment"></i>
                                                {!!  nl2br($item->notes)  !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->phone)
                                                <a href="tel:{{ $item->phone }}"><i class="fa fa-phone"></i> {{ $item->phone }}</a> @if($item->phone_2) - {{ $item->phone_2 }} @endif
                                            @endif
                                            @if($item->email)
                                            <br>
                                            <i class="fa fa-envelope"></i> <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>
                                            @endif
                                         </td>

                                        <td style="white-space:nowrap">{{ date('d/m/Y H:i', strtotime($item->contact_date)) }}
                                        </td>
                                        <td>
                                            @if($item->product_type)
                                                {{ $types[$item->product_type] }}
                                            @endif
                                            @if($item->product_id &&  !empty($item->product))
                                                <br/><i class="fa fa-tag"></i> {{ $item->product->name }}
                                            @endif
                                        </td>
                                        <td>{{ App\Helpers\Helper::getCustomerStatus($item->status) }}
                                            @if($item->status == 1)
                                            <br>
                                            <button style="font-size: 15px" class="btn btn-sm btn-info btnHen" type="button" data-id="{{ $item->id }}" >
                                                <i class="fa fa-calendar-check-o"></i> [ {{ count($item->appointments) }} ] Lịch hẹn
                                            </button>
                                            @endif
                                            @foreach($item->appointments as $index => $appointment)
                                                @php
                                                   $class = "label-info";
                                                   $ngay_hen = date('Y-m-d', strtotime($appointment->datetime));
                                                   if($ngay_hen == date('Y-m-d', strtotime("tomorrow")) || $ngay_hen == date('Y-m-d', time())){
                                                       $class="label-danger";
                                                   }
                                                @endphp
                                                <div style="margin-top: 10px"><label style="font-size: 13px" class="label {{ $class }}">L{{$index + 1}}: {{ date('d/m/Y H:i', strtotime($appointment->datetime)) }}</label></div>
                                            @endforeach
                                        </td>
                                        <td style="white-space: nowrap;">
                                            @php
                                                $countBooking = $item->bookings->count();
                                            @endphp
                                            @if($countBooking)
                                                <div style="margin-bottom: 5px">
                                                    @foreach($item->bookings as $booking)
                                                        @switch($booking->type)
                                                            @case(1)
                                                                <a href="{{ route('booking.edit', $booking->id) }}" target="_blank"
                                                                   class="label label-danger">PTT{{$booking->id}}</a>
                                                                @break
                                                            @case(2)
                                                                <a href="{{ route('booking-hotel.edit', $booking->id) }}" target="_blank"
                                                                   class="label label-danger"> PTH{{$booking->id}}</a>
                                                                @break
                                                            @case(3)
                                                                <a href="{{ route('booking-ticket.edit', $booking->id) }}" target="_blank"
                                                                   class="label label-danger"> PTV{{$booking->id}}</a>
                                                                @break
                                                            @case(4)
                                                                <a href="{{ route('booking-car.edit', $booking->id) }}" target="_blank"
                                                                   class="label label-danger"> PTX{{$booking->id}}</a>
                                                                @break
                                                        @endswitch
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="btn-group" style="width: 130px;">
                                                <button type="button" class="btn btn-success">Tạo booking</button>
                                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="{{ route('booking.create', ['customer_id' => $item->id]) }}" target="_blank">Tour</a></li>
                                                    <li><a href="{{ route('booking-hotel.create', ['customer_id' => $item->id]) }}" target="_blank">Khách sạn</a></li>
                                                    <li><a href="{{ route('booking-ticket.create', ['customer_id' => $item->id]) }}" target="_blank">Vé vui chơi</a></li>
                                                    <li><a href="{{ route('booking-car.create', ['customer_id' => $item->id]) }}" target="_blank">Xe</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td style="white-space:nowrap;" class="text-right">
                                            @if($item->is_send == 1 && $item->user_id_refer > 0 && $item->user_id_refer != Auth::user()->id && Auth::user()->role > 2)
                                            <a href="{{ route( 'customer.edit', [ 'id' => $item->id ]) }}"
                                                class="btn btn-default btn-sm">Xem chi tiết</a>

                                            @else
                                            <a href="{{ route( 'customer.edit', [ 'id' => $item->id ]) }}"
                                                class="btn btn-warning btn-sm"><span
                                                    class="glyphicon glyphicon-pencil"></span></a>
                                                    <!-- kiem tra đúng user tạo hoặc role == 1 hoặc chưa booking thì cho xoá-->
                                                @if(($item->created_user == Auth::user()->id || Auth::user()->role == 1) && $countBooking == 0)
                                                    <a onclick="return callDelete('{{ $item->title }}','{{ route( 'customer.destroy', [ 'id' => $item->id ]) }}');"
                                                       class="btn btn-danger btn-sm"><span
                                                            class="glyphicon glyphicon-trash"></span></a>
                                                @endif
                                            @endif

                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="7">Không có dữ liệu.</td>
                                    </tr>
                                    @endif

                                </tbody>
                            </table>
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
<div class="modal fade" id="henModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: left;">
       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="henContent">

      </div>

    </div>
  </div>
</div>
@stop
@section('js')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css">
<script type="text/javascript">
    $(document).ready(function(){
        $('.btnHen').click(function(){
          var obj = $(this);
          $.ajax({
            url : "{{ route('customer.hen') }}",
            type : 'GET',
            data : {
              id : obj.data('id')
            },
            success: function(data){
                $('#henContent').html(data);
                $('#henModal').modal('show');
                $('.select2').select2();
                $(".datepicker").datepicker({
                    dateFormat: "dd/mm/yy",
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '-100:+0'
                });
            }
          });
       });

        $('.btnCreateBooking').click(function(){
            var obj = $(this);
            $.ajax({
                url : "{{ route('customer.create-booking') }}",
                type : 'GET',
                data : {
                    id : obj.data('id')
                },
                success: function(data){
                    $('#henContent').html(data);
                    $('#henModal').modal('show');
                    $('.select2').select2();
                    $(".datepicker").datepicker({
                        dateFormat: "dd/mm/yy",
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '-100:+0'
                    });
                }
            });
        });
    });
    function callDelete(name, url) {
        swal({
            title: 'Bạn muốn xóa "' + name + '"?',
            text: "Dữ liệu sẽ không thể phục hồi.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then(function () {
            location.href = url;
        })
        return flag;
    }
    $(document).ready(function(){
        $('#status').change(function(){
            $('#frmContact').submit();
        });
    });
</script>
@stop
