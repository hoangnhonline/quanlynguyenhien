<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>Hệ thống quản lý booking - Plan To Travel</title>
        <meta property="og:type" content="website" />
        <meta property="og:image" content="images/logo-plan-to-travel.png" />
        <meta name="robots" content="noindex" />
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />

        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{ asset('admin/bootstrap/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('https://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css') }}" />
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css?v=2.0" />
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css?v=2.0" />
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('admin/dist/css/AdminLTE.css?v=2.0') }}" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{ asset('admin/dist/css/skins/_all-skins.min.css?v=2.0') }}" />
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset('admin/plugins/iCheck/flat/blue.css?v=2.0') }}" />
        <link rel="stylesheet" href="{{ asset('admin/plugins/colorpicker/bootstrap-colorpicker.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin/dist/css/select2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin/dist/css/sweetalert2.min.css') }}" />
        <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
            fieldset.scheduler-border {
                border: 1px groove red !important;
                padding: 0 5px 5px 5px !important;
                margin: 0 0 5px 0 !important;
                -webkit-box-shadow: 0px 0px 0px 0px #000;
                box-shadow: 0px 0px 0px 0px #000;
            }

            legend.scheduler-border {
                font-size: 1.2em !important;
                font-weight: bold !important;
                text-align: left !important;
                width: auto;
                padding: 0 5px;
                border-bottom: none;
                margin-bottom: 0px;
            }
        </style>
    </head>
    <body class="skin-blue sidebar-collapse">
        <div class="wrapper">
            @include('partials.header')

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1 style="text-transform: uppercase;">
      CHẤM CÔNG <span style="color: #f39c12">{{ $detailUser->name }}</span>
    </h1>
   
  </section>
<style type="text/css">
  .co-di {
    background-color: #3c8dbc;
    color: #FFF;
  }
  .co-di-chua-lam{
    background-color: red;
    color: #FFF
  }
</style>

  <!-- Main content -->
  <section class="content">    
    <form role="form" method="POST" action="{{ route('media.store') }}" id="dataForm">
    <div class="row">
      <!-- left column -->

      <div class="col-md-6 col-xs-12">
        <!-- general form elements -->
        <div class="box box-primary">
         
          <!-- /.box-header -->               
            {!! csrf_field() !!}

            <div class="box-body">
              @if (count($errors) > 0)
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif 
                <div class="row">
                <div class="form-group col-xs-6">
                    <label>THÁNG</label>
                    <select class="form-control" id="month_change">
                      <option value="">--Chọn--</option>
                      @for($i = 1; $i <=12; $i++)
                      <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $month == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                      @endfor
                    </select>
                  </div>
                  <div class="form-group col-xs-6">
                    <label>NĂM</label>
                    <select class="form-control" id="year_change">
                      <option value="">--Chọn--</option>
                      <option value="2021" {{ $year == 2021 ? "selected" : "" }}>2021</option>
                      <option value="2022" {{ $year == 2022 ? "selected" : "" }}>2022</option>
                      <option value="2023" {{ $year == 2023 ? "selected" : "" }}>2023</option>
                      <option value="2024" {{ $year == 2024 ? "selected" : "" }}>2024</option>
                    </select>
                  </div>
                </div>
                
                  @php
                  $list=array();              
                  $year = date('Y');
                  $listDay = [];
                  for($d=1; $d<=31; $d++)
                  {
                      $time=mktime(12, 0, 0, $month, $d, $year);          
                      if (date('m', $time)==$month)       
                          $listDay[]=date('d', $time);
                  }
                  @endphp
                  <div style="text-align: left;margin-top: 10px;">
                    <label style="font-size: 20px">THÁNG {{ $month }}/{{ $year }} </label>
                 <p class="clearfix" style="margin-top: 10px"></p>
                    <?php $i = 0; ?>
                  @foreach($listDay as $day)
                  <?php $i++; ?>
                  @php
                 // dd($day);
                 $link_anh = $link_flycam = '';
                  if(in_array($day, array_keys($mediaDay))){
                    if($mediaDay[$day] == null){
                      $class = 'co-di-chua-lam';
                    }else{
                      $class = 'co-di';
                      $link_anh = isset($detailArr[$day][1]) ? $detailArr[$day][1]->link : '';
                      $link_flycam = isset($detailArr[$day][2]) ? $detailArr[$day][2]->link : '';
                    }
                    $di = 1;
                  }else{
                    $class = "ko_di";
                    $di = 0;
                  }      
                  $tour_id = isset($cateArr[$day]) ? $cateArr[$day] : 1;
                  $area_id = isset($areaArr[$day]) ? $areaArr[$day] : 1;
                  @endphp
                  <span class="cham-cong {{ $class }}" 

                   data-month="{{ $month }}" data-di="{{ $di }}" data-year="{{ $year }}" data-value="{{ $day }}" style="padding: 10px; border: 1px solid #CCC; cursor: pointer;"

                   data-anh="{{ $link_anh }}" data-flycam="{{ $link_flycam }}" data-cate="{{ $tour_id }}"  data-area="{{ $area_id }}"
                   >{{ $day }}</span>              
              
                  @if($i%7 == 0)
                  <hr>
                  @endif
                  @endforeach
                  </div>
                </div>           
                <div class="box-footer">
                  <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                  <a class="btn btn-default btn-sm" class="btn btn-primary btn-sm" href="{{ route('media.index')}}">Hủy</a>
                </div>
                
            </div>
            <!-- /.box -->     

          </div>
          <div class="col-md-7">
                 
        </div>
        </form>
        <!-- /.row -->
      </section>
      <!-- /.content -->
    </div>
    <!-- Button trigger modal -->
    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 style="font-weight: bold;color: #06b7a4; text-transform: uppercase;" class="modal-title" id="exampleModalLabel">NGÀY <span id="ngay_show"></span> - <span id="name_show" style="color: black; font-style: italic">{{ $detailUser->name }}</span></h5>        
          </div>
          <form id="formAjax">
          <div class="modal-body" >                    
            <input type="hidden" name="day" id="day" value="">
            <input type="hidden" name="month" id="month" value="">
            <input type="hidden" name="year" id="year" value="">
            <div class="row">         
                  <div id="div_da_di">
                    <div class="row" style="margin: 20px 0px">
                <div class="form-group">
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio" class="form-check-input" id="area_id1" name="area_id" value="1" checked>
                      <label class="form-check-label" for="area_id1">Tour đảo</label>
                    </div>
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio" class="form-check-input"  id="area_id2" name="area_id" value="2">
                      <label class="form-check-label" for="area_id2">Grand World</label>
                    </div>
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio" value="3"  id="area_id3"  name="area_id" class="form-check-input">
                      <label class="form-check-label" for="area_id3">Rạch Vẹm</label>
                    </div>
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio"  id="area_id4" value="4"  name="area_id" class="form-check-input">
                      <label class="form-check-label" for="area_id4">Hòn Thơm</label>
                    </div>
                    <div class="form-check col-md-3 col-xs-6">
                      <input type="radio" class="form-check-input"  id="area_id5" name="area_id" value="5">
                      <label class="form-check-label" for="area_id5">Bãi Sao - 2 Đảo</label>
                    </div>
                </div>
                </div>
                    <div class="form-group col-md-12" >                  
                      <label>LINK ẢNH<span class="red-star">*</span></label>
                      <input type="text" class="form-control" autocomplete="off" name="link_anh" id="link_anh" value="{{ old('link_anh') }}">
                    </div> 
                    <div class="form-group col-md-12" >                  
                      <label>LINK FLYCAM (nếu có)</label>
                      <input type="text" class="form-control" autocomplete="off" name="link_flycam" id="link_flycam" value="{{ old('link_flycam') }}">
                    </div> 
                    <input type="hidden" name="codeUser" id="codeUser" value="{{ $codeUser }}">
                </div>
            </div>
          </div>
          </form>
          <div class="modal-footer">       
            <button type="button" id="btnLuu" class="btn btn-primary">Lưu</button>
             <button type="button"  class="btn btn-secondary" data-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>

            <div style="display: none;" id="box_uploadimages">
                <div class="upload_wrapper block_auto">
                    <div class="note" style="text-align: center;">Nhấn <strong>Ctrl</strong> để chọn nhiều hình.</div>
                    <form id="upload_files_new" method="post" enctype="multipart/form-data" enctype="multipart/form-data" action="{{ route('ck-upload')}}">
                        <fieldset style="width: 100%; margin-bottom: 10px; height: 47px; padding: 5px;">
                            <legend><b>&nbsp;&nbsp;Chọn hình từ máy tính&nbsp;&nbsp;</b></legend>
                            <input style="border-radius: 2px;" type="file" id="myfile" name="myfile[]" multiple />
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <div class="clear"></div>
                            <div class="progress_upload" style="text-align: center; border: 1px solid; border-radius: 3px; position: relative; display: none;">
                                <div class="bar_upload" style="background-color: grey; border-radius: 1px; height: 13px; width: 0%;"></div>
                                <div class="percent_upload" style="color: #ffffff; left: 140px; position: absolute; top: 1px;">0%</div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            @include('customer.customer-notification-modal')
            <!-- /.content-wrapper -->
            <footer class="main-footer">
                <div class="pull-right hidden-xs"><b>Version</b> 2.3.5</div>
                <strong>Copyright &copy; 2014-2016 <a href="mailto:hoangnhonline@gmail.com">hoangnhonline@gmail.com</a>.</strong> All rights reserved.
            </footer>

            <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
            <input type="hidden" id="route_update_order" value="{{ route('update-order') }}" />
            <input type="hidden" id="route_get_slug" value="{{ route('get-slug') }}" />
            <div class="control-sidebar-bg"></div>
        </div>
        <input type="hidden" id="app_url" value="{{ env('APP_URL') }}" />
        <input type="hidden" id="url_open_kc_finder" value="{{ asset('admin/dist/js/kcfinder/browse.php?type=images') }}" />
        <input type="hidden" id="route-change-value" value="{{ route('change-value') }}" />
        <input type="hidden" id="get-child-route" value="{{ route('get-child') }}" />
        <input type="hidden" id="upload_url" value="{{ config('plantotravel.upload_url') }}" />
        <!-- ./wrapper -->

        <!-- jQuery 2.2.3 -->
        <script src="{{ asset('admin/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{ asset('https://code.jquery.com/ui/1.10.0/jquery-ui.js') }}"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge("uibutton", $.ui.button);
        </script>
        <script type="text/javascript">
            var public_url = '{{ env('APP_URL') }}';
        </script>
        <!-- Bootstrap 3.3.6 -->
        <script src="{{ asset('admin/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('admin/dist/js/ajax-upload.js') }}"></script>
        <script src="{{ asset('admin/dist/js/form.js') }}"></script>
        <script src="{{ asset('admin/dist/js/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('admin/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('admin/dist/js/es6-promise.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>

        <!-- Slimscroll -->
        <script src="{{ asset('admin/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ asset('admin/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
        <!-- AdminLTE App -->
        <script src="{{ asset('admin/dist/js/app.min.js') }}"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="{{ asset('admin/dist/js/pages/dashboard.js') }}"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="{{ asset('admin/dist/js/demo.js') }}"></script>
        <script src="{{ asset('admin/dist/js/lazy.js') }}"></script>
        <script src="{{ asset('admin/dist/js/number.js') }}"></script>
        <script src="{{ asset('admin/dist/js/ckeditor/ckeditor.js') }}"></script>

        <style type="text/css">
            .form-group label {
                margin-top: 5px;
            }
            @media (max-width: 767px) {
                .main-header .navbar {
                    margin-top: -15px;
                }
                .skin-blue .main-header .navbar .sidebar-toggle {
                    font-size: 22px !important;
                    padding-top: 3px;
                    margin-top: 5px;
                }
                #setting_top_2 {
                    font-size: 22px;
                }
                .mgb15 {
                    margin-bottom: 15px;
                }
            }
        </style>
        <script type="text/javascript" type="text/javascript">
            $(document).on("click", "#btnSaveNoti", function () {
                var content = CKEDITOR.instances["contentNoti"].getData();
                if (content != "") {
                    $.ajax({
                        url: $("#formNoti").attr("action"),
                        type: "POST",
                        data: {
                            data: $("#formNoti").serialize(),
                            content: content,
                        },
                        success: function (data) {
                            alert("Gửi tin nhắn thành công.");
                            $("#notifiModal").modal("hide");
                        },
                    });
                }
            });
            $(document).ready(function () {
                $.ajax({
                    url: "{{ route('booking.not-export') }}",
                    type: "GET",
                    success: function (data) {
                        $("#content_alert").append(data);
                    },
                });
                $("input.number").number(true, 0);
                $("img.lazy").lazyload();
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });

                $(".sendNoti").click(function () {
                    var customer_id = $(this).data("customer-id");
                    var order_id = $(this).data("order-id");
                    var notiType = $(this).data("type");
                    $("#customer_id_noti").val(customer_id);
                    $("#order_id_noti").val(order_id);
                    $("#notifiModal").modal("show");
                    $("#notifiModal  #type").val(notiType);
                    processNotiType(notiType);
                });
                $("#notifiModal  #type").change(function () {
                    processNotiType($(this).val());
                });
                CKEDITOR.editorConfig = function (config) {
                    config.toolbarGroups = [
                        { name: "clipboard", groups: ["clipboard", "undo"] },
                        { name: "editing", groups: ["find", "selection", "spellchecker", "editing"] },
                    ];

                    config.removeButtons = "Underline,Subscript,Superscript";
                };
                if ($("#contentNoti").length == 1) {
                    var editor2 = CKEDITOR.replace("contentNoti", {
                        language: "vi",
                        height: 100,
                        toolbarGroups: [{ name: "basicstyles", groups: ["basicstyles", "cleanup"] }, { name: "links", groups: ["links"] }, "/"],
                    });
                }
            });

            function processNotiType(type) {
                if (type == 1) {
                    $("#notifiModal #url-km").show();
                } else {
                    $("#notifiModal #url-km").hide();
                }
            }
        </script>
        <style type="text/css">
            .pagination > .active > a,
            .pagination > .active > a:focus,
            .pagination > .active > a:hover,
            .pagination > .active > span,
            .pagination > .active > span:focus,
            .pagination > .active > span:hover {
                z-index: 1 !important;
            }
            @if (\Request:: route()->getName() == "compare.index") .content-wrapper, .main-footer {
                margin-left: 0px !important;
            }
            @endif;
        </style>

        <script type="text/javascript">
  $(document).ready(function(){
    $('#user_id').change(function(){
      location.href='{{ route('diem-danh-public', ['code' => $codeUser]) }}' + '?user_id=' + $(this).val() + '&month=' + $('#month_change').val() + '&year=' + $('#year_change').val();
    });
    $('#year_change, #month_change').change(function(){
      var str ='{{ route('diem-danh-public', ['code' => $codeUser]) }}' + '?month=' + $('#month_change').val() + '&year=' + $('#year_change').val();      
      location.href=str;
    });
    $('#btnLuu').click(function(){

      var link_flycam = $('#link_flycam').val();
      var link_anh = $('#link_anh').val();
      var checked = $('#co_di').prop('checked');      
      if(link_flycam == '' && link_anh == '' && checked == false){
        alert('Bạn chưa nhập link!');
        return false;
      }
      $(this).attr('disabled', 'disabled');
      $.ajax({
        url : "{{ route('ajax-store-public') }}",
        type : 'GET',
        data : $('#formAjax').serialize(),
        success: function(data){
          alert('Lưu thành công!');
          window.location.reload();
        }
      });
    });
    $('.cham-cong').click(function(){

      if($(this).data('di') == 0){
        $('#div_di').show();
      }else{
        $('#div_di').hide();
      }      
      var ngay = $(this).data('value');
      var month = $(this).data('month');
      var year = $(this).data('year');
      $('#ngay_show').html(ngay);
      $('#day').val(ngay);
      $('#month').val(month);
      $('#year').val(year);
      $('#tour_id').val($(this).data('cate'));
      var area_id = $(this).data('area');
      $('#area_id'+area_id).attr('checked', 'checked');
      $('#link_anh').val($(this).data('anh'));
      $('#link_flycam').val($(this).data('flycam'));
      $('#detailModal').modal('show');
    });
  });
</script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(".datepicker").datepicker({
                    dateFormat: "dd/mm/yy",
                });
                $("#btnQuickSearch").click(function () {
                    if ($.trim($("#keyword").val()) != "") {
                        location.href = "{{ route('booking.index')}}?keyword=" + $("#keyword").val();
                    }
                });
                $("#keyword").on("keydown", function (e) {
                    if (e.which == 13) {
                        if ($.trim($("#keyword").val()) != "") {
                            location.href = "{{ route('booking.index')}}?keyword=" + $("#keyword").val();
                        }
                    }
                });
            });
        </script>
    </body>
</html>
