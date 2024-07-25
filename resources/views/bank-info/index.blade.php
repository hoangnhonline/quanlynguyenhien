@extends('layout')
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Tài khoản NH đối tác
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'bank-info.index' ) }}">Thành viên</a></li>
    <li class="active">Danh sách</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div id="content_alert"></div>
      @if(Session::has('message'))
      <p class="alert alert-info" >{{ Session::get('message') }}</p>
      @endif

      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
      <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" method="GET" action="{{ route('bank-info.index') }}" id="searchForm">
          <div class="form-group">
            <input type="text" name="id" value="{{ $id }}" class="form-control" placeholder="ID" maxlength="20" autocomplete="off">
          </div>
          <div class="form-group">
            <input type="text" name="bank_no" value="{{ $bank_no }}" class="form-control" placeholder="Số tài khoản" maxlength="20" autocomplete="off">
          </div>
          <button class="btn btn-info" type="submit">Lọc</button>
          </form>
      </div>
      </div>
      @endif
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Danh sách ( {{ $items->total() }} )</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive list-mobile">
              @if( $items->count() > 0 )
                <?php $i = 0; ?>
                <ul>
                @foreach( $items as $item )
                  <?php $i ++; ?>
                  <li>
                    Đối tác: <b>{{ $item->name }}</b> <br>
                    Chủ TK: <b>{{ $item->account_name }}</b> <br>
                    Số TK: <b>{{ $item->bank_no }}</b> <br>
                    Ngân hàng: <b>{{ $item->bank_name }}</b> <br>
                    <p class="text-right" style="position: absolute; right: 5px; top: 5px">
                      <a href="{{ route( 'bank-info.edit', [ 'id' => $item->id ]) }}" class="btn-sm btn btn-warning" ><span class="glyphicon glyphicon-pencil"></span></a>
                    </p>
                  </li>

                @endforeach

                </ul>
              @else

                <p>Không có dữ liệu.</td>

              @endif
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
@stop
