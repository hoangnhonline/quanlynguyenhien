@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Khách hàng đánh giá
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'media-rating.index' ) }}">Khách hàng đánh giá</a></li>
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
      <a href="{{ route('media.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px">Tạo mới</a>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bộ lọc</h3>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" method="GET" action="{{ route('media-rating.index') }}" id="searchForm">

              <div class="form-group">
                  <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
              </div>
            <div class="form-group">
                    <select class="form-control select2" name="user_id" id="user_id">
                      <option value="">--Người chụp--</option>
                      @foreach($userList as $u)
                      <option value="{{ $u->id }}" {{ $user_id == $u->id ? "selected" : "" }}>{{ $u->name }}</option>
                      @endforeach
                    </select>
                </div>
            <div class="form-group  chon-thang">
                <select class="form-control select2" id="stars" name="stars">
                  <option value="">--Số sao--</option>
                  <option value="1" {{ $stars == 1 ? "selected" : "" }}>1 sao</option>
                  <option value="2" {{ $stars == 2 ? "selected" : "" }}>2 sao</option>
                  <option value="3" {{ $stars == 3 ? "selected" : "" }}>3 sao</option>
                  <option value="4" {{ $stars == 4 ? "selected" : "" }}>4 sao</option>
                  <option value="5" {{ $stars == 5 ? "selected" : "" }}>5 sao</option>
                </select>
              </div>
            <button type="submit" class="btn btn-info btn-sm">Lọc</button>
          </form>
        </div>
      </div>
      <div class="box">

        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( <span class="value">{{ $items->total() }} links )</span></h3>
        </div>

        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( ['use_date' => $use_date, 'ip' => $ip, 'user_id' => $user_id, 'stars' => $stars] )->links() }}
          </div>
          <div class="table-responsive">
          <table class="table table-bordered" id="table-list-data">
            <tr>
              <th style="width: 1%">#</th>
              <th>IP / Time</th>
              <th>Số điện thoại</th>
              <th>Người chụp</th>
              <th>Ngày chụp</th>
              <th>Số sao</th>
              <th width="40%">Đánh giá/góp ý</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $i = 0; ?>
              @foreach( $items as $item )
                <?php $i ++; ?>
              <tr id="row-{{ $item->id }}" @if($item->stars < 3) style="background-color: red; color: #fff" @endif>
                <td><span class="order">{{ $i }}</span></td>
                <td>
                 <b>{{ $item->ip }}</b><br>
                 {{ date('d/m H:i', $item->visit) }}
                </td>
                <td>{{ $item->phone }}</td>
                <td>{{ $item->user->name }}</td>
                <td>{{ date('d/m/Y', strtotime($item->use_date)) }}</td>
                <td>
                  @if($item->stars)
                  <b>{{ $item->stars }} sao </b>
                  @endif
                </td>

                <td>
                 @if($item->content)
                  {!! $item->content !!}
                  @endif
                </td>

                <td style="white-space:nowrap">
                  <a onclick="return callDelete('{{ $item->link }}','{{ route( 'media.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>

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
            {{ $items->appends( ['use_date' => $use_date, 'ip' => $ip, 'user_id' => $user_id, 'stars' => $stars] )->links() }}
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
<input type="hidden" id="table_name" value="articles">
@stop
