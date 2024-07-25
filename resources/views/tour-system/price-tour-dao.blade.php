@extends('layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Giá tour : {{ $detailTour->name }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{ route('tour-system.index', ['city_id' => $detailTour->city_id]) }}">Tour</a></li>
      <li class="active">Thêm mới</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <a class="btn btn-default btn-sm" href="{{ route('tour-system.index', ['city_id' => $detailTour->city_id]) }}" style="margin-bottom:5px">Quay lại</a>
    
    <div class="row">
      <!-- left column -->
      
      <div class="col-md-12">
        @if(Session::has('message'))
        <p class="alert alert-info" >{{ Session::get('message') }}</p>
        @endif
        <div class="panel panel-default">
          <div class="panel-heading">
              <h3 class="panel-title">Bộ lọc</h3>
          </div>
          <div class="panel-body">
              <form class="form-inline" role="form" method="GET" action="{{ route('tour-system.price') }}" id="searchForm">
                  <input type="hidden" name="tour_id" value="{{ $tour_id }}">
                  <div class="form-group ">              
                      <select class="form-control select2 search-form-change" name="status" id="status">
                          <option value="">--Đối tác -- </option>
                          @foreach($partnerList as $partner)
                          <option value="{{ $partner->id }}" {{ $partner->id == $partner_id ? "selected" : "" }}>{{ $partner->name }}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="form-group">                  
                    <select class="form-control select2" name="level" id="level">      
                      <option value="" >--Phân loại sales--</option>
                      <option value="1" {{ $level == 1 ? "selected" : "" }}>CTV Group</option>  
                      <option value="2" {{ $level == 2 ? "selected" : "" }}>ĐỐI TÁC</option>      
                      <option value="6" {{ $level == 6 ? "selected" : "" }}>NV SALES</option>
                      <option value="7" {{ $level == 7 ? "selected" : "" }}>GỬI BẾN</option>
                    </select>
                  </div>
                  <div class="form-group ">                  
                 
                  <select class="form-control select2 search-form-change" id="tour_type" name="tour_type">
                      <option value="">--Hình thức--</option>                      
                      <option value="1" {{ $tour_type == 1 ? "selected" : "" }}>Tour ghép</option>
                      <option value="2" {{ $tour_type == 2 ? "selected" : "" }}>Tour VIP</option>
                      <option value="3" {{ $tour_type == 3 ? "selected" : "" }}>Thuê cano</option>
                  </select>
                </div>

                <div class="form-group">                                        
                  <select class="form-control select2 search-form-change" id="cano_type" name="cano_type">   
                      <option value="">--Loại cano--</option>                    
                      <option value="1" {{ $cano_type == 1 ? "selected" : "" }}>Cano SB</option>
                      <option value="2" {{ $cano_type == 2 ? "selected" : "" }}>Cano thường</option>
                  </select>
                </div>  
                <button type="submit" class="btn btn-info btn-sm">Lọc</button>                                           
              </form>
          </div>
      </div>
        <!-- general form elements -->
        <div class="box">
          <div class="box-body">
            
             <table class="table table-bordered" id="table-list-data">
                  <tr>
                      <th style="width: 1%">#</th>
                      <th class="text-left">Phân loại</th>
                      <th class="text-left">Giai đoạn</th>
                      <th class="text-center">Số lượng</th>
                      <th class="text-right">Giá NL</th>
                      <th class="text-right">Giá TE</th>
                      <th class="text-right">Cáp NL</th>
                      <th class="text-right">Cáp TE</th>
                      <th class="text-right">Ăn NL</th>
                      <th class="text-right">Ăn TE</th>
                      <th class="text-right">Phụ thu</th>
                      <th width="1%" style="white-space:nowrap">Thao tác</th>
                  </tr>
                  <tbody>
                      @if( $priceList->count() > 0 )
                      <?php $i = 0; ?>
                      @foreach( $priceList as $item )
                      <?php $i ++; ?>
                      <tr id="row-{{ $item->id }}">
                          <td><span class="order">{{ $i }}</span></td>
                          <td>{{ Helper::getLevel($item->level) }}</td>
                          <td class="text-left">
                             {{ date('d/m/y', strtotime($item->from_date)) }} - {{ date('d/m/y', strtotime($item->to_date)) }}
                          </td>     

                          <td class="text-center">
                            {{ $item->from_adult }} - {{ $item->to_adult }}
                          </td>                     
                          <td class="text-right">
                            {{ number_format($item->price) }}
                          </td>
                          <td class="text-right">
                            {{ number_format($item->price_child) }}
                          </td>
                          <td class="text-right">
                            {{ number_format($item->cap_nl) }}
                          </td>
                          <td class="text-right">
                            {{ number_format($item->cap_te) }}
                          </td>
                          <td class="text-right">
                            {{ number_format($item->meals) }}
                          </td>
                          <td class="text-right">
                            {{ number_format($item->meals_te) }}
                          </td>
                          <td class="text-right">
                            {{ number_format($item->extra_fee) }}
                          </td>
                          <td style="white-space:nowrap">
                              @if($userRole == 1)                              
                              <a href="{{ route( 'tour-system.price', [ 'id' => $item->id, 'tour_id' => $item->tour_id, 'level' => $item->level, 'partner_id' => $item->partner_id, 'tour_type' => $tour_type, 'cano_type' => $cano_type]) }}"
                                  class="btn btn-warning btn-sm" title="Edit info"><span
                                      class="glyphicon glyphicon-pencil"></span></a>
                              
                              <a onclick="return callDelete('{{ $item->title }}','{{ route( 'staff.destroy', [ 'id' => $item->id ]) }}');"
                                  class="btn btn-danger btn-sm"><span
                                      class="glyphicon glyphicon-trash"></span></a>
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
        </div>
        @if($id)        
          @include('tour-system.form.edit-price-tour-dao')
        @else
          @include('tour-system.form.add-price-tour-dao')
        @endif
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@stop
@section('js')
<script type="text/javascript">
  $(document).ready(function(){   
    $('#btnAddLocation').click(function(){
      $('.dia-diem-hidden:first').removeClass('dia-diem-hidden');
      $('.select2').select2();
    });
  });
</script>
@stop