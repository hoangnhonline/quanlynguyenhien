@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Sự kiện</span>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'event.index' ) }}">Sự kiện</a></li>
    <li class="active">Danh sách</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif
      <a href="{{ route('event.create', ['cate_id' => $cate_id]) }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('event.index') }}" id="searchForm">            
            <div class="form-group">
              <label for="email">Danh mục </label>
              <select class="form-control" name="cate_id" id="cate_id">
                <option value="">--Tất cả--</option>
                @if( $cateList->count() > 0)
                  @foreach( $cateList as $value )
                  <option value="{{ $value->id }}" {{ $value->id == $cate_id ? "selected" : "" }}>{{ $value->name }}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="form-group">
                <label for="email">&nbsp;&nbsp;Tỉnh/Thành </label>
                <select class="form-control select2" name="city_id" id="city_id">
                  <option value="">--Tất cả--</option>
                  @if( $cityList->count() > 0)
                    @foreach( $cityList as $value )
                    <option value="{{ $value->id }}" {{ $value->id == $city_id ? "selected" : "" }}>{{ $value->name }}</option>
                    @endforeach
                  @endif
                </select>
              </div>              
            <div class="form-group">
              <label for="email">Từ khóa :</label>
              <input type="text" class="form-control" name="name" value="{{ $name }}">
            </div>
            <button type="submit" class="btn btn-default btn-sm">Lọc</button>
          </form>         
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} sự kiện )</span></h3>
        </div>
        
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( ['cate_id' => $cate_id, 'title' => $name] )->links() }}
          </div>  
          <table class="table table-bordered" id="table-list-data">
            <tr>
              <th style="width: 1%">#</th>                        
              <th>Thumbnail</th>             
              <th>Tên sự kiện</th>
              <th>Địa điểm</th>
              <th>Thời gian</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}">
                <td><span class="order">{{ $i }}</span></td>   
                 
                <td width="150">
                  <img class="img-thumbnail" src="{{ Helper::showImage($item->image_url)}}" width="145">
                </td>        
               
                <td>                  
                  <a style="font-size:17px" href="{{ route( 'event.edit', [ 'id' => $item->id ]) }}">{{ $item->name }}</a>                  
                  @if( $item->is_hot == 1 )
                  <label class="label label-danger">HOT</label>
                  @endif                 
                  <p>{!! $item->description !!}</p>
                </td>
                <td>
                  {{ $item->address }}
                </td>
                <td>{{ date('d/m/Y', strtotime($item->start_date)) }} - {{ date('d/m/Y', strtotime($item->end_date)) }}</td>
                <td style="white-space:nowrap">                                  
                  <a href="{{ route( 'event.edit', [ 'id' => $item->id ]) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>                 
                  
                  <a onclick="return callDelete('{{ $item->name }}','{{ route( 'event.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                  
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
          <div style="text-align:center">
            {{ $items->appends( ['cate_id' => $cate_id, 'title' => $name] )->links() }}
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
<input type="hidden" id="table_name" value="event">
@stop