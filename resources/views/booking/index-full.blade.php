@extends('layout')
@section('content')
<div class="content-wrapper">


<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="text-transform: uppercase;">
    Quản lý đặt tour
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="{{ route( 'booking.index') }}">
     
    Tour
    </a></li>
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
   
      <a href="{{ route('booking.create') }}" class="btn btn-info btn-sm" style="margin-bottom:5px;">Tạo mới</a>
      
      <div class="panel panel-default">

        <div class="panel-body" style="padding: 5px !important;">
          <form class="form-inline" role="form" method="GET" action="{{ route('booking.index') }}" id="searchForm" style="margin-bottom: 0px;">

            {{-- @include('partials.block-search-date') --}}
             <input type="hidden" name="type" value="{{ $type }}">
             <div class="form-group">
                <input type="text" class="form-control daterange" autocomplete="off" name="range_date" value="{{ $arrSearch['range_date'] ?? "" }}" />
            </div>
            <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" name="id_search" placeholder="Mã" value="{{ $arrSearch['id_search'] }}" style="width: 80px">
            </div>
            
            <div class="form-group">
              <input type="text" class="form-control" name="phone" value="{{ $arrSearch['phone'] }}" placeholder="Số ĐT"  style="width: 100px">
            </div>         

            <div class="form-group">
              <select class="form-control select2" name="tour_id" id="tour_id">
                <option value="">--Tour--</option>
                @foreach($tourSystem as $tour)
                <option value="{{ $tour->id }}" {{ $arrSearch['tour_id'] == $tour->id ? "selected" : "" }}>{{ $tour->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
            <select class="form-control select2" name="tour_no">
                <option value="">--Tour số--</option>
                @for($i = 1; $i<=10; $i++)
                <option value="{{ $i }}" {{ $arrSearch['tour_no'] == $i ? "selected" : "" }}>Tour {{ $i }}</option>
                @endfor
              </select>
            </div>
         

            <div class="form-group">
              <select class="form-control select2" name="user_id" id="user_id">
                <option value="">--Đối tác--</option>
                @foreach($listUser as $user)
                <option value="{{ $user->id }}" {{ $arrSearch['user_id'] == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <select class="form-control select2" name="nguoi_thu_tien" id="nguoi_thu_tien">
                <option value="">--Thu tiền--</option>
                @foreach($collecterList as $col)
                <option value="{{ $col->id }}" {{ $arrSearch['nguoi_thu_tien'] == $col->id ? "selected" : "" }}>{{ $col->name }}</option>
                @endforeach
              </select>
            </div>
       
      
          <div class="form-group">
            <select class="form-control select2"  data-column="cano_id" name="cano_id">
              <option value="">--CANO--</option>
            
            </select>
          </div>
      

          
            
          <div class="form-group">
            <select class="form-control select2" name="cano0" id="cano0">
              <option value="">--TT CHỌN CANO--</option>
              <option value="2" {{ $arrSearch['cano0'] == 2 ? "selected" : "" }}>Đã chọn CANO</option>
              <option value="1" {{ $arrSearch['cano0'] == 1 ? "selected" : "" }}>Chưa chọn CANO</option>
            </select>
          </div>
          <div class="form-group">
          <select class="form-control select2" name="cty_send" id="cty_send">
                <option value="">--GỬI TOUR--</option>
                <option value="1" {{ $arrSearch['cty_send'] == 1 ? "selected" : "" }}>Rooty</option>
                <option value="2" {{ $arrSearch['cty_send'] == 2 ? "selected" : "" }}>Funny</option>
                <option value="3" {{ $arrSearch['cty_send'] == 3 ? "selected" : "" }}>Group Tour</option>
                <option value="4" {{ $arrSearch['cty_send'] == 4  ? "selected" : "" }}>Nguyễn Hiền</option>
                <option value="5" {{ $arrSearch['cty_send'] == 5  ? "selected" : "" }}>Phúc Thủy</option>
              </select>
              </div>
          
            <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px">Lọc</button>
            <div class="form-group">
              <button type="button" id="btnReset" class="btn btn-default btn-sm">Reset</button>
            </div>
            <div>
              @if($arrSearch['tour_id'] != 4)
              <div class="form-group">
              <input type="checkbox" name="tour_type[]" id="tour_type_1" {{ in_array(1, $arrSearch['tour_type']) ? "checked" : "" }} value="1">
              <label for="tour_type_1">GHÉP({{ $ghep }})</label>
            </div>
            <div class="form-group">
              <input type="checkbox" name="tour_type[]" id="tour_type_2" {{ in_array(2, $arrSearch['tour_type']) ? "checked" : "" }} value="2">
              <label for="tour_type_2">VIP({{ $vip }}-{{ $tong_vip }}NL)</label>
            </div>
            <div class="form-group" style="border-right: 1px solid #9ba39d">
              <input type="checkbox" name="tour_type[]" id="tour_type_3" {{ in_array(3, $arrSearch['tour_type']) ? "checked" : "" }} value="3">
              <label for="tour_type_3">THUÊ CANO({{ $thue }})</label>
            </div>
            @endif
              <div class="form-group">
              <input type="checkbox" name="status[]" id="status_1" {{ in_array(1, $arrSearch['status']) ? "checked" : "" }} value="1">
              <label for="status_1">Mới</label>
            </div>
            <div class="form-group">
              <input type="checkbox" name="status[]" id="status_2" {{ in_array(2, $arrSearch['status']) ? "checked" : "" }} value="2">
              <label for="status_2">Hoàn Tất</label>
            </div>
            <div class="form-group filter">
              <input type="checkbox" name="status[]" id="status_3" {{ in_array(3, $arrSearch['status']) ? "checked" : "" }} value="3">
              <label for="status_3">Huỷ</label>
            </div>
            <div class="form-group filter">
              <input type="checkbox" name="no_cab" id="no_cab" {{ $arrSearch['no_cab'] == 1 ? "checked" : "" }} value="1">
              <label for="no_cab">Không cáp</label>
            </div>
            <div class="form-group filter">
              <input type="checkbox" name="no_meals" id="no_meals" {{ $arrSearch['no_meals'] == 1 ? "checked" : "" }} value="1">
              <label for="no_meals">Không ăn</label>
            </div>           
            </div>
          </form>
        </div>
      </div>      
      
      <div class="panel" style="margin-bottom: 15px;">
        <div class="panel-body" style="padding: 5px;">
          <div class="table-responsive">
          <table class="table table-bordered" id="table_report" style="margin-bottom:0px;font-size: 14px;">
              <tr style="background-color: #ffff99">
                <th class="text-center" width="20%">Tổng BK</th>
                <th class="text-center" width="20%">NL/TE</th>
                <th class="text-center" width="20%">Ăn NL/TE</th>
                <th class="text-center" width="20%">Cáp NL/TE</th>              
              </tr>
              <tr>
                <td class="text-center">{{ number_format($items->total()) }}</td>
                <td class="text-center">{{ $tong_so_nguoi }} / {{ $tong_te }}</td>
                <td class="text-center">{{ $tong_phan_an }} / {{ $tong_phan_an_te }}</td>
                <td class="text-center">{{ $cap_nl }} / {{ $cap_te }}</td>               
              </tr>
          </table>
        </div>
      
        </div>
      </div>
      <div class="box">


       
        <div class="form-inline" style="padding: 5px">

          <div class="form-group" style="float: left;">
            <select class="form-control select2 multi-change-column-value"  data-column="tour_no">
              <option value="">--Tour số--</option>
              @for($i = 1; $i<=10; $i++)
              <option value="{{ $i }}">Tour {{ $i }}</option>
              @endfor
            </select>           

          </div>
       
          <div class="clearfix"></div>
        </div>
       
        <!-- /.box-header -->
        <div class="box-body">
          <div style="text-align:center">
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          <div class="table-responsive">
          <table class="table table-bordered table-hover" id="table-list-data">
            <tr style="background-color: #f4f4f4">
              <th style="width: 1%" class="text-center" ><input type="checkbox" id="check_all" value="1"></th>
              <th style="width: 1%"></th>
              <th width="200">Tên KH</th>
               <th class="text-center" width="60">Ngày đi</th>  
              <th style="width: 200px">Nơi đón</th>
              <th class="text-center" width="80">NL/TE/EB</th>
              <th class="text-right" width="100">Tổng tiền</th>
              <th class="text-right" width="100">Cano</th>
              <th width="1%;white-space:nowrap">Thao tác</th>
            </tr>
            <tbody>
            @if( $items->count() > 0 )
              <?php $l = 0; ?>
              @foreach( $items as $item )
                
              <tr class="booking" id="row-{{ $item->id }}" data-id="{{ $item->id }}" data-date="{{ $item->use_date }}" style="border-bottom: 1px solid #000 !important;@if($item->status == 3) background-color: #f77e7e; @endif">
                <td class="text-center" style="line-height: 30px">
              
                  <input type="checkbox" id="checked{{ $item->id }}" class="check_one" value="{{ $item->id }}">
                  <br>{{ date('d/m H:i', strtotime($item->created_at)) }}
                  <span class="label label-sm label-danger" id="error_unc_{{ $item->id }}"></span>
                </td>
                <td style="text-align: center;white-space: nowrap; line-height: 30px;">
                  @if($item->tour_no)
                    <span class="label label-sm
                    @if($item->tour_no == 1)
                    label-success
                    @elseif($item->tour_no == 2)
                    label-info
                    @elseif($item->tour_no == 3)
                    label-warning
                    @else
                    label-default
                    @endif
                    ">Tour {{ $item->tour_no }}</span>
                  @endif
                  <br>
                  <strong style="color: red;">NH{{ $item->id }}</strong>
                  <br>
                  @if($item->status == 1)
                  <span class="label label-info">MỚI</span>
                  @elseif($item->status == 2)
                  <span class="label label-default">HOÀN TẤT</span>
                  @elseif($item->status == 3)
                  <span class="label label-danger">HỦY</span>
                  @endif

                </span>
                 
              </td>
                <td style="position: relative; line-height: 30px;">



                  @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp

                  <span class="name">{{ $item->name }}</span>

                  @if($item->status != 3)
                     - <a href="tel:{{ $item->phone }}" style="font-weight: bold">{{ $item->phone }}</a>

                    @if($item->tour_id)
                     
                       <br><label class="label" style="background-color:{{ @$tourSystemName[$item->tour_id]['bg_color'] }}">{{ @$tourSystemName[$item->tour_id]['name'] }}</label>
                   
                    @endif
                    @if($item->tour_cate == 2 && $item->tour_id == 1)
                    <br><label class="label label-info">2 đảo</label>
                    @endif

                    @if($item->tour_type == 3)
                    <br><label class="label label-warning">Thuê cano</label>
                    @elseif($item->tour_type == 2)
                    <br><label class="label label-danger">Tour VIP</label>
                    @endif <!--loai tour-->
                    <br><i style="font-style: italic;" class="glyphicon glyphicon-user"></i>
                 
               <i>
                  @if($item->user)
                    {{ $item->user->name }}              
                  @endif                  
               </i>

                  @endif

                 
                </td>
                <td class="text-center">
                  {{ date('d/m', strtotime($item->use_date)) }}
                </td>
                <td style="line-height: 22px; position: relative;">
                  
                  @if($item->location)
                    {{ $item->location->name }} 
                  @endif
                  <br>
                
                
                  <span style="color:red; font-size:12px">
                    @if($item->ko_cap_treo)
                    KHÔNG CÁP<br>
                    @endif
                    {{ $item->notes }}</span>


                </td>
                 <td class="text-center">
                  @if($item->status != 3)
                    {{ $item->adults }} / {{ $item->childs }} / {{ $item->infants }}
                    <br>
                    <?php
                    $meals = $item->meals;
                    if($meals > 0){
                      $meals+= $item->meals_te/2;
                    }

                    ?>
                    <i class="  glyphicon glyphicon-briefcase"></i> {{ $meals }}
                  @endif
                  @if($item->tour_id == 1)
                  <br >CAP: {{ $item->cap_nl }} / {{ $item->cap_te }}
                  @endif
                </td>


                <td class="text-right" style="position: relative;">


                  @if($item->status != 3)
                    @if($item->nguoi_thu_tien)
                    Người thu tiền: <span style="color: red">{{ $collecterNameArr[$item->nguoi_thu_tien] }}</span>
                    @endif
                    {{ number_format($item->total_price) }}                   
                  @endif                   
             
                </td>
                
                <td class="text-center">
                      @if($item->cano_id > 0 && !empty($item->cano))
                        <br> - {{ $item->cano->name }}
                      @else
                        <br> - Cano -
                      @endif

                </td>

                <td style="white-space:nowrap; position: relative;">
                  @if($item->status != 3)
            

                    @php $arrEdit = array_merge(['id' => $item->id], $arrSearch) @endphp

                    <a href="{{ route( 'booking.edit', $arrEdit ) }}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>

                    @if(Auth::user()->role == 1 && !Auth::user()->view_only && $item->status == 1 && Auth::user()->id == 1)
                    <a onclick="return callDelete('{{ $item->title }}','{{ route( 'booking.destroy', [ 'id' => $item->id ]) }}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>
                    @endif
                    
                  @endif                
             
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
            {{ $items->appends( $arrSearch )->links() }}
          </div>
          @if(Auth::user()->role == 1 && !Auth::user()->view_only)
          <div class="form-inline" style="padding: 5px">
            <div class="form-group">
              <select class="form-control select2 multi-change-column-value"  data-column="tour_no">
                <option value="">--Tour số--</option>
                @for($i = 1; $i<=10; $i++)
                <option value="{{ $i }}">Tour {{ $i }}</option>
                @endfor
              </select>            
              <select class="form-control select2 multi-change-column-value" data-column="status">
                  <option value="">--SET TRẠNG THÁI--</option>
                  <option value="1">Mới</option>
                  <option value="2">Hoàn tất</option>
                  <option value="3">Hủy</option>
                </select>
              <select class="form-control select2 multi-change-column-value" data-column="nguoi_thu_tien">
                  <option value="">--SET THU TIỀN--</option>
                  @foreach($collecterList as $col)
                  <option value="{{ $col->id }}">{{ $col->name }}</option>
                  @endforeach
              </select>                           
            </div>
          </div>
          @endif

        </div>

      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
</section>
<!-- /.content -->
</div>
<div class="modal fade" id="uncModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="unc_img" style="width: 100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="maxiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="maxi_img" style="width: 100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
@stop
<style type="text/css">
  .hdv{
    cursor: pointer;
  }
  .hdv:hover, .hdv.selected{
    background-color: #06b7a4;
    color: #FFF
  }
  label{
    cursor: pointer;
  }
  #table_report th td {padding: 2px !important;}
  #searchForm, #searchForm input{
    font-size: 13px;
  }
  .form-control{
    font-size: 13px !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered{

    font-size: 12px !important;
  }
  tr.error{
    background-color:#ffe6e6
  }
</style>
<div class="modal fade" id="confirmChiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header bg-green">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4>LẤY NỘI DUNG TT HH</h4>
      </div>
      <div class="modal-body" id="loadConfirmChi">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnYcChi">LẤY ND TT HH</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">ĐÓNG</button>
      </div>
    </div>
  </div>
</div>
@section('js')
<script type="text/javascript">
  $(document).ready(function(){
    $('img.img-unc').click(function(){
      $('#unc_img').attr('src', $(this).attr('src'));
      $('#uncModal').modal('show');
    });
    $('p.img-maxi').click(function(){
      $('#maxi_img').attr('src', "https://plantotravel.vn/" + $(this).data('image'));
      $('#maxiModal').modal('show');
    });
  });
</script>
<script type="text/javascript">
    $(document).ready(function(){
      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
      $('tr.booking').each(function(){
        var tr = $(this);
        var id = tr.data('id');
        var use_date = tr.data('date');
        var today = new Date();
        if(use_date < "{{ date('Y-m-d') }}"){
          // $.ajax({
          //   url : '{{ route('booking.checkError') }}?id=' + id,
          //   type : 'GET',
          //   success : function(data){
          //     $('#error_' + id).text(data);
          //   }
          // });
          $.ajax({
            url : '{{ route('booking.check-unc') }}?id=' + id,
            type : 'GET',
            success : function(data){
              $('#error_unc_' + id).text(data);
            }
          });
        }
      });
      @endif
      $('#searchForm input[type=checkbox]').change(function(){
        $('#searchForm').submit();
      });


      $('#btnExport').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.cong-no-tour') }}").submit().attr('action', oldAction);
      });
      $('#btnExportCustomer').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('booking.export-customer') }}").submit().attr('action', oldAction);
      });
      $('#btnExportGui').click(function(){
        var oldAction = $('#searchForm').attr('action');
        $('#searchForm').attr('action', "{{ route('export.gui-tour') }}").submit().attr('action', oldAction);
      });
      $('#temp').click(function(){
        $(this).parents('form').submit();
      });
    	$('.change_status').click(function(){
		      var obj = $(this);
		      $.ajax({
		        url : "{{ route('change-export-status') }}",
		        type : 'GET',
		        data : {
		          id : obj.data('id')
		        },
		        success: function(){
		          window.location.reload();
		        }
		      });
		    });
       $('.change-column-value-booking').change(function(){
          var obj = $(this);
          ajaxChange(obj.data('id'), obj);
       });
       $('.hoa_hong_sales').blur(function(){
          var obj = $(this);
          ajaxChange(obj.data('id'), obj);
       });
       $('.multi-change-column-value').change(function(){
          var obj = $(this);
          $('.check_one:checked').each(function(){
              ajaxChange($(this).val(), obj);
          });

       });
      $('.hdv').click(function(){
        var hdv_id = $(this).data('id');
        if(hdv_id != ""){
          $('#hdv_id').val($(this).data('id'));
          $('#searchForm').submit();
        }

      });

      $('#btnReset').click(function(){
        $('#searchForm select').val('');
        $('#searchForm').submit();
      });

      $('#btnContentChi').click(function(){
          var obj = $(this);
          var str_id = '';
          $('.check_one:checked').each(function(){
              str_id += $(this).val() + ',';
          });
          if(str_id != ''){
            $.ajax({
              url : "{{ route('booking.get-confirm-chi') }}",
              type : 'GET',
              data : {
                str_id : str_id
              },
              success: function(data){
                $('#loadConfirmChi').html(data);
                $('#confirmChiModal').modal('show');
              }
            });
          }

       });

      $('#btnYcChi').click(function(){
          var obj = $(this);
          var str_id = '';
          $('.check_one:checked').each(function(){
              str_id += $(this).val() + ',';
          });

          if(str_id != ''){
            $.ajax({
              url : "{{ route('booking.get-content-chi') }}",
              type : 'GET',
              data : {
                str_id : str_id
              },
              success: function(data){
                $('#noi_dung_chi').html(data);
                $('#btnYcChi').hide();
              }
            });
          }

       });


    });
    function ajaxChange(id, obj){
        $.ajax({
            url : "{{ route('booking.change-value-by-column') }}",
            type : 'GET',
            data : {
              id : id,
              col : obj.data('column'),
              value: obj.val()
            },
            success: function(data){
                console.log(data);
            }
          });
      }

  </script>
@stop
