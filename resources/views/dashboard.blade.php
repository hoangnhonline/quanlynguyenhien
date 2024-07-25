@extends('layout') @section('content')

<div class="content-wrapper" style="min-height: 926px;">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content" style="padding-top: 50px;">
        <!-- /.row -->
    <div id="content_alert"></div>

         <div class="clearfix"></div>
          <div class="row">

            <div class="col-md-2 col-sm-6 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-superpowers"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">TOUR HÔM NAY</span>
                        <a href="{{ route('booking.index', ['type' => 1]) }}">
                            <span class="info-box-number">{{ count($allTour) }}</span>
                        </a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            @if(Auth::user()->is_limit == 0)
            <!-- /.col -->
            <div class="col-md-2 col-sm-6 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-building-o"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">BOOKING KS CHECK-IN HÔM NAY</span>
                        <a href="{{ route('booking-hotel.index', ['checkin_from' => date('d/m/Y')]) }}">
                            <span class="info-box-number">{{ count($allHotel) }}</span>
                        </a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            @endif
            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>
            @if(Auth::user()->is_limit == 0)
            <div class="col-md-2 col-sm-6 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-ticket"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">BOOKING VÉ GIAO HÔM NAY</span>
                        <a href="{{ route('booking-ticket.index') }}">
                            <span class="info-box-number">{{ count($allTicket) }}</span>
                        </a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <div class="col-md-2 col-sm-6 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-cab"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">ĐẶT XE HÔM NAY</span>
                        <a href="{{ route('booking-car.index') }}">
                            <span class="info-box-number">{{ count($allCar) }}</span>
                        </a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>


            @if(Auth::user()->role < 3)
            <div class="col-md-2 col-sm-6 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-user"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">QUẢN LÝ NHÂN VIÊN</span>
                        <a href="{{ route('staff.index') }}">
                            <span class="info-box-number">{{ $nvCount }}</span>
                        </a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            @endif
            @endif
            <!-- /.col -->
        </div>
    </section>
    <!-- /.content -->
    <section class="content">
      <div class="row">
        <div class="col-md-12">
           <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('dashboard') }}" id="searchForm">
              <div class="form-group">
                  <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
              </div>
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>
        </div>
      </div>
      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive" id="tblReportCate">
            <table class="table table-hover">
              <tr>
                <th width="20%" class="bg-info"></th>
                <th width="20%" colspan="3" class="text-center bg-red">SỐ BOOKING</th>
                <th width="20%" colspan="3" class="text-center bg-yellow">GHÉP</th>
                <th width="20%" colspan="3" class="text-center bg-green">VIP</th>
                <th width="20%" colspan="3" class="text-center bg-blue">THUÊ CANO</th>
              </tr>
              <tr class="bg-success">
                <th class="bg-success">TOUR</th>
                <th class="text-center">GHÉP</th>
                <th class="text-center">VIP</th>
                <th class="text-center">THUÊ CANO</th>
                <th class="text-center">NL</th>
                <th class="text-center">TE</th>
                <th class="text-center">EB</th>
                <th class="text-center">NL</th>
                <th class="text-center">TE</th>
                <th class="text-center">EB</th>
                <th class="text-center">NL</th>
                <th class="text-center">TE</th>
                <th class="text-center">EB</th>
              </tr>
              @foreach($arrTourByCate as $tour_id => $arrDetail)
              <tr>
                <th>{{ isset($tourSystem[$tour_id]) ? $tourSystem[$tour_id] : $tour_id }}</th>
                <td>
                  @if(isset($arrDetail[1]))
                  {{ $arrDetail[1]['total'] }}
                  @else
                  -
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[2]))
                  {{ $arrDetail[2]['total'] }}
                  @else
                  -
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[3]))
                  {{ $arrDetail[3]['total'] }}
                  @else
                  -
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[1]))
                  {{ $arrDetail[1]['adults'] }}
                  @else
                  -
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[1]))
                    @if($arrDetail[1]['childs'] > 0 ) {{ $arrDetail[1]['childs'] }} @else - @endif
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[1]))
                  @if($arrDetail[1]['infants'] > 0 ) {{ $arrDetail[1]['infants'] }} @else - @endif
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[2]))
                  {{ $arrDetail[2]['adults'] }}
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[2]))
                    @if(isset($arrDetail[2]['childs']) && $arrDetail[2]['childs'] > 0 ) {{ $arrDetail[2]['childs'] }} @else - @endif
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[2]))
                   @if(isset($arrDetail[2]['infants']) && $arrDetail[2]['infants'] > 0 ) {{ $arrDetail[2]['infants'] }} @else - @endif
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[3]))
                  {{ isset($arrDetail[3]['adults']) ? $arrDetail[3]['adults'] : '-' }}
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[3]))
                    @if(isset($arrDetail[3]['childs']) && $arrDetail[3]['childs'] > 0 ) {{ $arrDetail[3]['childs'] }} @else - @endif
                  @endif
                </td>
                <td>
                  @if(isset($arrDetail[3]))
                    @if(isset($arrDetail[3]['infants']) && $arrDetail[3]['infants'] > 0 ) {{ $arrDetail[3]['infants'] }} @else - @endif
                  @endif
                </td>
              </tr>
              @endforeach
            </table>
          </div>
        </div>
      </div>
        @if($time_type != 3)
          <div class="table-responsives" style="margin-bottom: 20px;">
            <div style="min-height: 50px !important;">
                <canvas id="myChartLine"></canvas>
            </div>
          </div>
          @endif


        </div>
        </div><!--row-->
  <div class="row">
    <div class="col-md-12">
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif

      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <!-- <p style="color: red; font-weight: bold">CHI PHÍ THÁNG {{ $month }}</p> -->
            <table class="table table-hover">
                <thead>
              <tr>
                <th width="1%" class="text-center">STT</th>
                <th class="text-center">Ngày</th>
                <th class="text-center">Phần ăn</th>
                <th class="text-center">Khách ghép</th>
                <th class="text-center">VIP</th>
                <th class="text-center">Thuê cano</th>
              </tr>
              </thead>
              <tbody>
              @php
              $i = 0;
              @endphp
               @foreach($arrResult as $day => $arr)
               @php $i++; @endphp
              <tr>
                <td class="text-center">{{ $i }}</td>

                <td  class="text-center">
                    {{ $day }}
                </td>
                <td  class="text-center">
                    {{ $arr['meals'] }}
                </td>
                <td class="text-center">
                     {{ isset($arr[1]) ? $arr[1] : "" }}
                </td>
                <td class="text-center">
                    {{ $arr[2] ?? "" }}
                </td>
                <td class="text-center">
                    {{ $arr[3] ?? "" }}
                </td>
              </tr>
               @endforeach
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- /.col -->
  </div>
</section>
</div>
<style type="text/css">
  #tblReportCate tr td{
    text-align: center;
  }
</style>
@stop
@section('js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
  $(document).ready(function(){
    $("input[name='time_type']").change(function(){
        $('#searchForm').submit();
    });
  });
   @if($time_type != 3)
  const cty = document.getElementById('myChartLine');
  //cty.height = 300;
  cty.style.backgroundColor = '#feffd8a1';
  const stackedLine = new Chart(cty, {

    type: 'line',
    data:  {
        datasets: [
        @php
        $arrMau = [
          'red', 'green', 'blue', 'brown', 'pink', 'black', 'orange'
        ];
        @endphp
        <?php
        $vMax = 0;
        ?>
        @foreach($arrByDay as $level => $arr)
        {
            label: '{{ Helper::getLevel($level) }}',
            data: [
            @for($i = 1; $i <= $maxDay; $i++)
            @php
            $day_check = str_pad($i, 2, "0", STR_PAD_LEFT);
            $key_check = $year.'-'.$month.'-'.$day_check;
            if(!isset($arr[$key_check]))
            {
              $v = 0;
            }else{
              $v = $arr[$key_check][1];
            }
            if($vMax < $v){
              $vMax = $v + 5;
            }
            @endphp
            {{ $v }},
            @endfor
            ],
            backgroundColor: [
                '{{ $arrMau[$level-1] }}',
            ],
            borderColor: [
                '{{ $arrMau[$level-1] }}',
            ],
            borderWidth : 1
        },
        @endforeach
        ],
        labels: [
          @for($i = 1; $i <= $maxDay; $i++)
          '{{ $i }}/{{$month}}',
          @endfor
        ],
        maintainAspectRatio: false,

    },
    options: {
        scales: {
            y: { // defining min and max so hiding the dataset does not change scale range
              min: 0,
              max: {{ $vMax }},
              ticks: {
                // forces step size to be 50 units
                stepSize: 20,
                beginAtZero: true,

              }
            }
          }
    }
});
  @endif
</script>
@stop
