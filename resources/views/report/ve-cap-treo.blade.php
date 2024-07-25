@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    VÉ CÁP TREO THÁNG {{ $month }}/{{ $year }}
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
          <form class="form-inline" role="form" method="GET" action="{{ route('report.ve-cap-treo') }}" id="searchForm">
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
        <!-- /.box-header -->
        <div class="box-body">
          
          <table class="table table-bordered table-hover table-list-data" id="table-list-data">
            <tr>                      
              <th class="text-center">Ngày</th>            
             
              <th class="text-center">NL theo BK</th>
              <th class="text-center">NL theo CP</th>             
              <th class="text-right">Chi phí NL</th>
              <th class="text-center">TE theo BK</th>
              
              <th class="text-center">TE theo CP</th>             
              <th class="text-right">Chi phí TE</th>
              
              
            </tr>
            <tbody>
           
              <?php $i = 0; ?>
              @for($i = 1; $i<=$maxDay; $i++)
                <?php 
                $arr = isset($arrDay[$i]) ? $arrDay[$i] : [];          
               // dd($arrCost[$i]);
                ?>               
                @if(!empty($arr) && isset($arrCost[$i]))
                @php
                $nl_bk = $arr['cap_nl'];
                $nl_cp = isset($arrCost[$i][4]) ? $arrCost[$i][4]['amount'] : 0;

                $te_bk = $arr['cap_te'];
                $te_cp = isset($arrCost[$i][11]) ? $arrCost[$i][11]['amount'] : 0;
                $error = ($nl_bk != $nl_cp ) || $te_bk != $te_cp ? true : false;
                @endphp
              <tr style="background-color: {{ $error ? "red" : "" }}">
               
                <td class="text-center">
                   {{ $i }}
                </td>                
                 <td class="text-center">
                  {{ number_format($nl_bk) }}
                </td> 
                <td class="text-center">
                   {{ number_format($nl_cp) }}
                </td>
                <td  class="text-right">
                  {{ isset($arrCost[$i][11]) ? number_format($arrCost[$i][11]['total']) : '' }}
                </td> 
                <td class="text-center">
                 
                  {{ $te_bk }}
                
                </td>                  
                <td class="text-center">
                 
                  {{ $te_cp }}
                
                </td>  
                <td  class="text-right">
                  {{ isset($arrCost[$i][11]) ? number_format($arrCost[$i][11]['total']) : '' }}
                </td>               
              </tr> 
              @endif
              @endfor


          </tbody>
          </table>
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