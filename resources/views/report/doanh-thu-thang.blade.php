@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    DOANH SỐ TOUR THÁNG {{ $month }}/{{ $year }}
  </h1>
 <!--  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'food.index' ) }}">Món ăn</a></li>
    <li class="active">Danh sách</li>
  </ol> -->
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      <div class="panel panel-default">        
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('report.doanh-thu-thang') }}" id="searchForm">
             <div class="form-group  chon-thang">      
                <label for="month">THÁNG</label>          
                <select class="form-control select2" id="month_change" name="month">
                  <option value="">--CHỌN--</option>
                  @for($i = 1; $i <=12; $i++)
                  <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                  @endfor
                </select>
              </div>    
              <div class="form-group  chon-thang">                
                <select class="form-control select2" id="year_change" name="year">
                  <option value="">--NĂM--</option>
                  <option value="2020" {{ $year == 2020 ? "selected" : "" }}>2020</option>
                  <option value="2021" {{ $year == 2021 ? "selected" : "" }}>2021</option>
                  <option value="2022" {{ $year == 2022 ? "selected" : "" }}>2022</option>
                  <option value="2023" {{ $year == 2023 ? "selected" : "" }}>2023</option>
                  <option value="2024" {{ $year == 2024 ? "selected" : "" }}>2024</option>
                </select>
              </div>       
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
          </form>         
        </div>
      </div>
      <div class="box">
        <div class="box-body">
          <div class="col-md-6">
            <table class="table table-bordered table-hover" style="font-size: 16px;font-weight: bold;">
            <tr>
              <th colspan="2" class="text-left" style="background-color: #06b7a4;color:#FFF">NỘI DUNG THU</th>
              <th class="text-center" style="background-color: #06b7a4;color:#FFF">KPI</th>
            </tr>

            <tr>
              <td>
                Tiền thu tour
              </td>
              <td class="text-right">
                {{ number_format($tong_thuc_thu) }}
              </td>
              <td class="text-center"><span class="badge">KPI</span></td>
            </tr>
            <?php  $tong_khac = $tong_kpi = 0; ?>
            @foreach($revenueAll as $re)
            <tr>
              <td>
                {{ $re->content }}
              </td>
              <td class="text-right">
                {{ number_format($re->amount) }}
              </td>
              <td class="text-center">
                @if($re->not_kpi == 0)
                <span class="badge">KPI</span>
                @endif
              </td>
            </tr>
            <?php $tong_khac+=$re->amount;
            if($re->not_kpi == 0){
              $tong_kpi += $re->amount;  
            }
            

            ?>
            @endforeach
            <tr>
            <td>
                TỔNG THU
              </td>
              <td class="text-right"  style="color: red">
                {{ number_format($tong_thuc_thu + $tong_khac) }}
              </td>
              <td class="text-center"  style="color: red">
                {{ number_format($tong_thuc_thu + $tong_kpi) }}
              </td>
            </tr>
            
          </table></div>
          <div class="col-md-6">
            <table class="table table-bordered table-hover" style="font-size: 16px;font-weight: bold;">
            <tr>
              <th colspan="2" class="text-center" style="background-color: #06b7a4;color:#FFF">CHI</tr>
            </tr>
            <tr>
            <td>
                Hoa hồng sales
              </td>
              <td class="text-right">
                {{ number_format($tong_hoa_hong_sales) }}
              </td>
            </tr>
            <tr>
            <td>
                Chi phí
              </td>
              <td class="text-right">
                {{ number_format($tong_chi) }}
              </td>
            </tr>
            <tr>
            <td>
                Tổng chi
              </td>
              <td class="text-right" style="color: red">
                {{ number_format($tong_chi + $tong_hoa_hong_sales) }}
              </td>
            </tr>
          </table>
          <table class="table table-bordered table-hover" style="font-size: 16px;font-weight: bold;">
            <tr>
              <th colspan="2" class="text-center" style="background-color: #06b7a4;color:#FFF">CÔNG NỢ</tr>
            </tr>
            <tr>
              <td>Công nợ tour</td>
              <td class="text-right">{{ number_format($tong_cong_no) }}</td>
            </tr>
           <!--  <?php  $tong_debt = 0; ?>
            @foreach($debtAll as $re)
            <tr>
              <td>
                {{ $re->content }}
              </td>
              <td class="text-right">
                {{ number_format($re->amount) }}
              </td>
            </tr>
            <?php $tong_debt+=$re->amount; ?>
            @endforeach           <tr>
            <td>
                Tổng công nợ
              </td>
              <td class="text-right" style="color: red">
                {{ number_format($tong_debt) }}
              </td>
            </tr> -->
          </table>
          </div>
          
        </div><!--thu-->
        
      </div>
      
      <div class="box">
      <div class="box-body">
      <table class="table table-bordered table-hover" style="font-size: 16px;font-weight: bold;">
        <?php 
        if($month == 12 && $year == 2022){
          $tong_loi_nhuan = $tong_thuc_thu + $tong_khac - $tong_chi - $tong_hoa_hong_sales - 15595000;
        }else{
          $tong_loi_nhuan = $tong_thuc_thu + $tong_khac - $tong_chi - $tong_hoa_hong_sales - $tong_debt;  
        }
        $tong_loi_nhuan_kpi = $tong_thuc_thu + $tong_kpi - $tong_chi - $tong_hoa_hong_sales - $tong_debt; 
        
        ?>
            <tr style="background-color:red; color:#fff">
            <td>
                Tổng lợi nhuận
              </td>
              <td class="text-right">
                {{ number_format($tong_loi_nhuan) }}
              </td>
              <td class="text-right">
                KPI: {{ number_format($tong_loi_nhuan_kpi) }} <br>
                Công nợ: {{ number_format($tong_cong_no) }}
              </td>
            </tr>
            @if($year == 2022 && $month == 9)
            <tr>
              <td>A Phương</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.4) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>Hoàng</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.4) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>Trung</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.1) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>A Cường</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.1) }}</td>
              <td></td>
            </tr>
            @elseif($year > 2022 || ($year == 2022 && $month > 9))
            <tr>
              <td>A Phương</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.45) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>Hoàng</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.45) }}</td>
              <td></td>
            </tr>            
            <tr>
              <td>A Cường</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.1) }}</td>
              <td></td>
            </tr>
            @else
            <tr>
              <td>A Phương</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.325) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>Hoàng</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.325) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>Trung</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.25) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>A Cường</td>
              <td class="text-right">{{ number_format($tong_loi_nhuan*0.1) }}</td>
              <td></td>
            </tr>
            @endif
          </table>
          </div>
        </box>
     
      <div class="box">

       
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
          <table class="table table-bordered table-hover table-list-data" id="table-list-data">
            <tr>                      
              <th class="text-center">Ngày</th>             
              <th class="text-center">NL</th>
              <th class="text-center">TE</th>
              <th class="text-center">ĂN</th>
              <th class="text-center">CAP NL</th>
              <th class="text-center">CAP TE</th>
              <th class="text-right">Thực thu</th>
              <th class="text-right">Cọc</th>
              <th class="text-right">Hoa hồng sales</th>
              <th class="text-right">Chi phí</th>
              <th class="text-right">Cố định</th>
              <th class="text-right">Còn lại</th>
            </tr>
            <tbody>
           
              <?php $i = $tong_loi_nhuan = 0; ?>
              @for($i = 1; $i<=$maxDay; $i++)
                <?php 
                $arr = isset($arrDay[$i]) ? $arrDay[$i] : [];                
                ?>               
                @if(!empty($arr))
              <tr>
               
                <td class="text-center">
                   {{ $i }}
                </td>
                <td class="text-center">   
                  <a target="_blank" href="{{ route('booking.index', ['time_type' => 3, 'type'=> 1, 'use_date_from' => $i."/".$month.'/'.$year])}}">               
                  {{ $arr['adults'] }}
                </a>
                </td>
                <td class="text-center">    
                <a target="_blank" href="{{ route('booking.index', ['time_type' => 3, 'type'=> 1, 'use_date_from' => $i."/".$month.'/'.$year])}}">              
                  {{ $arr['childs'] }}
                </a>
                </td>
                <td class="text-center">                  
                  <a target="_blank" href="{{ route('cost.index', ['time_type' => 3, 'cate_id'=> 5, 'use_date_from' => $i."/".$month.'/'.$year])}}">
                  {{ $arr['meals'] }}
                  </a>
                </td>
                <td class="text-center">
                  <a target="_blank" href="{{ route('cost.index', ['time_type' => 3, 'cate_id'=> 4, 'use_date_from' => $i."/".$month.'/'.$year])}}">
                  {{ $arr['cap_nl'] }}
                </a>
                </td>
                <td class="text-center">
                  <a target="_blank" href="{{ route('cost.index', ['time_type' => 3, 'cate_id'=> 11, 'use_date_from' => $i."/".$month.'/'.$year])}}">
                  {{ $arr['cap_te'] }}
                </a>
                </td>
                <td class="text-right">
                  {{ number_format($arr['tien_thuc_thu']) }}
                </td>
                <td class="text-right">
                  {{ number_format($arr['tien_coc']) }}
                </td>
                <td class="text-right">
                  {{ number_format($arr['hoa_hong_sales']) }}
                </td>
                <td class="text-right">
                  <?php 
                  $cost = isset($arrCostTour[$i]) ? ($arrCostTour[$i]['total']) : 0;
                  ?>
                  <a target="_blank" href="{{ route('cost.index', ['time_type' => 3, 'use_date_from' => $i."/".$month.'/'.$year])}}">{{ number_format($cost) }}</a>
                </td>
                <td class="text-right">
                  {{ number_format($costPerDay) }}
                </td>
                <td class="text-right">
                  <?php 
                  $loi_nhuan_ngay = $arr['tien_thuc_thu'] + $arr['tien_coc'] - $arr['hoa_hong_sales'] - $cost - $costPerDay;
                  if($i < (date('d') - 1)){
                    $tong_loi_nhuan += $loi_nhuan_ngay;
                  }               
                  ?>
                    {{ number_format($loi_nhuan_ngay) }}
                </td>
              </tr> 
              @endif
              @endfor
              {{ number_format($tong_loi_nhuan) }}

          </tbody>
          </div>
          </table>
        </div>        
      </div><!-- /.box -->  
       <div class="box">       
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <p style="color: red; font-weight: bold">CHI PHÍ THÁNG {{ $month }}</p>
            <table class="table table-bordered table-hover table-list-data">
              <tr style="background-color: #06b7a4;color:#FFF">
                <th class="text-center">Ngày</th>
                @foreach($cateList as $cate)
                <th class="text-right">
                  {{ $cate->name }}
                </th>
                @endforeach
                <th class="text-right">Tổng</th>
              </tr>
              @php
              $tong_chi_phi_theo_ngay  = 0;ksort($arrCost);
              @endphp
              
              @foreach($arrCost as $day => $arr)
              <tr>
                <th class="text-center">{{ $day }}</th>
                @foreach($cateList as $cate)
                <td class="text-right">
                  <a target="_blank" href="{{ route('cost.index', ['time_type' => 3,'cate_id'=> $cate->id, 'use_date_from' => $day."/".$month.'/'.$year])}}">
                  {{ isset($arr[$cate->id]) ? number_format($arr[$cate->id]['total']) : '' }}
                  </a>
                </td>
                @endforeach                
                <th class="text-right">

                  {{ isset($arrCost[$day]) ? number_format($arrCost[$day]['total']) : "" }}
                </th>
              </tr>
              @php
              $tong_chi_phi_theo_ngay += isset($arrCost[$day]) ? $arrCost[$day]['total'] : 0;
              @endphp
              @endforeach
              <tr>
                <th colspan="{{ $cateList->count() + 1 }}" class="text-right"><h4>Tổng chi phí</h4></th>
                <td >
                  <h4 style="color: red">{{ number_format($tong_chi_phi_theo_ngay) }}</h4>

                </td>
              </tr>
            </table>
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
<style type="text/css">
	table a{
		color: #000;
    font-weight: bold;
	}
  .table-list-data td,.table-list-data th{
    border: 1px solid #000 !important;
    font-weight: bold;
    color: #000
  }
</style>
<input type="hidden" id="table_name" value="articles">
@stop