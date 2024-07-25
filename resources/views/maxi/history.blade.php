@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Danh sách cho mượn <span class="hot">{{ $detail->name }}</span>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route( 'maxi.index' ) }}">Maxi</a></li>
                <li class="active">Danh sách cho mượn</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    @if(Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    <a href="#" id="addNewButton" class="btn btn-info btn-sm" style="margin-bottom:5px">
                        <i class="fa fa-plus" aria-hidden="true"></i> Thêm mới
                    </a>

                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Tổng số dòng: <span class="value">( {{ $items->total() }} )</span>
                            </h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            @php
                              $month = date('m');
                                $next_month = $month == 12 ? 1 : $month + 1;                                
                              $list=array();
                              $year = date('Y');
                               $next_year = $month == 12 ? $year+1 : $year;
                              $listDay = [];
                              for($d=1; $d<=31; $d++)
                              {
                                  $time=mktime(12, 0, 0, $month, $d, $year);
                                  if (date('m', $time)==$month)
                                      $listDay[]=date('d', $time);
                              }
                              @endphp
                              <div style="text-align: left;margin-top: 10px;" class="col-md-6">
                                <label style="font-size: 20px">THÁNG {{ $month }}/{{ $year }} </label>
                             <p class="clearfix" style="margin-top: 10px"></p>
                                <?php $i = 0; ?>
                              @foreach($listDay as $day)
                              <?php $i++; 
                              $date_format = $year."-".$month."-".$day;
                              $class = in_array($date_format, $arrSelected) ? "selected"  : "";

                              ?>    

                              <span class="cham-cong {{ $class }}" data-date="{{ $day }}/{{ $month }}/{{ $year }}"

                               data-month="{{ $month }}" data-year="{{ $year }}" data-value="{{ $day }}" style="padding: 10px; border: 1px solid #CCC; cursor: pointer; display: block;float: left;width: 39px;"                             
                               >{{ $day }}</span>

                              @if($i%7 == 0)                              
                              <div class="clearfix"></div>
                              @endif
                              @endforeach
                              <div class="clearfix"></div>
                              </div>
                              @php
                              $listDay2 = [];
                              for($d=1; $d<=31; $d++)
                              {
                                  $time=mktime(12, 0, 0, $next_month, $d, $next_year);
                                  if (date('m', $time)==$next_month)
                                      $listDay2[]=date('d', $time);
                              }
                              @endphp
                              <div style="text-align: left;margin-top: 10px;" class="col-md-6">
                                <label style="font-size: 20px">THÁNG {{ $next_month }}/{{ $next_year }} </label>
                             <p class="clearfix" style="margin-top: 10px"></p>
                                <?php $i = 0; ?>
                              @foreach($listDay2 as $day)
                              <?php $i++; 
                              $date_format = $next_year."-".$next_month."-".$day;
                              $class = in_array($date_format, $arrSelected) ? "selected"  : "";

                              ?>    

                              <span class="cham-cong {{ $class }}" data-date="{{ $day }}/{{ $next_month }}/{{ $next_year }}"

                               data-month="{{ $next_month }}" data-year="{{ $next_year }}" data-value="{{ $day }}" style="padding: 10px; border: 1px solid #CCC; cursor: pointer; display: block;float: left;width: 39px;"                             
                               >{{ $day }}</span>

                              @if($i%7 == 0)                              
                              <div class="clearfix"></div>
                              @endif
                              @endforeach
                              <div class="clearfix"></div>
                              </div>
                              <div class="clearfix" style="clear:both; margin-top: 30px;"></div>

                            <table class="table table-bordered" id="table-list-data" style="margin-top: 20px;">
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th>Booking ID</th>
                                    <th>Ngày mượn</th>
                                    <th width="1%;white-space:nowrap">Thao tác</th>
                                </tr>
                                <tbody>
                                @if( $items->count() > 0 )
                                    <?php $i = 0; ?>
                                    @foreach( $items as $item )
                                        <?php $i++; ?>
                                        <tr id="row-{{ $item->id }}">
                                            <td><span class="order">{{ $i }}</span></td>
                                            <td>{{$item->booking_id}}</td>
                                            <td>{{ date('d/m/Y', strtotime($item->date)) }}</td>
                                            <td style="white-space:nowrap;" class="text-right">
                                                <a onclick="return callDelete('{{ $item->booking_id }}','{{ route( 'maxi.history.destroy', [ 'id' => $item->id ]) }}');"
                                                   class="btn btn-danger btn-sm"><span
                                                        class="glyphicon glyphicon-trash"></span></a>
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
                                {{ $items->appends(request()->query())->links() }}

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

    {{-- Modal --}}
    <div class="modal-maxi modal fade" id="addBookingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="padding-top: 10%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Thêm mới Booking</h4>
                </div>
                <div class="modal-body">
                    <!-- Form để nhập booking ID và ngày mượn -->
                    <form id="addBookingForm" method="POST" action="{{ route('maxi.history.store') }}">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label for="bookingId">Booking ID <span class="red-star">*</span></label>
                            <select class="form-control select2" id="related" multiple="multiple" name="related_id[]" style="width: 100%">
                                @foreach($arrBooking as $booking)
                                    <option value="{{ $booking->id }}">{{ Helper::showCode($booking) }}
                                        - {{ $booking->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="use_date">Ngày</label>
                            <input type="text" class="form-control" id="date" name="date" value="{{ old('date') }}">
                        </div>
                        <input type="hidden" name="maxi_id" value="{{ $maxi_id }}">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary" id="saveBooking">Lưu</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
    </div>
<style type="text/css">
    .selected {
        background-color: red;
        color: #FFF;
    }
</style>
@stop
@section('js')

    <script type="text/javascript">
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

        $(document).ready(function () {
            // Hiển thị modal khi nhấn nút "Thêm mới"
            $('#addNewButton').click(function (e) {
                e.preventDefault(); // Ngăn chặn hành động mặc định của liên kết
                $('#addBookingModal').modal('show');
            });
            $('.cham-cong').click(function(){
                $('#addBookingModal #date').val($(this).data('date'));
                $('#addBookingModal').modal('show');
            });
        });

        $("#date").datepicker({
            defaultDate: "today",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            dateFormat: "dd/mm/yy",
            yearRange: 'c-100:c+100',
            onSelect: function (dateText) {
                $('.selected_date').text(dateText)
                $('.selected_date_row').show()
                changeDate();
            }
        })
    </script>
@stop
