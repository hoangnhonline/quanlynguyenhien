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

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300&display=swap" rel="stylesheet" />
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        {{-- <link rel="stylesheet" href="{{ asset('admin/bootstrap/css/bootstrap.min.css') }}" /> --}}
        <link rel="stylesheet" href="{{ asset('https://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css') }}" />
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css?v=2.1') }}" />
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ asset('css/ionicons.min.css?v=2.1') }}" />
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('admin/dist/css/AdminLTE.css?v=3.8.9') }}" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{ asset('admin/dist/css/skins/_all-skins.min.css?v=2.2') }}" />
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset('admin/plugins/iCheck/flat/blue.css?v=2.0') }}" />
        <link rel="stylesheet" href="{{ asset('admin/plugins/colorpicker/bootstrap-colorpicker.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin/dist/css/select2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin/dist/css/sweetalert2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin/plugins/daterangepicker/daterangepicker.css') }}" />
        <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        @yield('css')
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
            fieldset.scheduler-border-2 {
                border: 1px groove #ddd !important;
                padding: 10px !important;
                margin: 0 0 5px 0 !important;
                -webkit-box-shadow: 0px 0px 0px 0px #000;
                box-shadow: 0px 0px 0px 0px #000;
                border-radius: 10px;
                margin-bottom: 20px !important;
            }

            legend.scheduler-border-2 {
                font-size: 1.2em !important;
                font-weight: bold !important;
                text-align: left !important;
                width: auto;
                padding: 0 5px;
                border-bottom: none;
                margin-bottom: 0px;
            }
            span.name{
                font-weight: bold;
                color: #3c8dbc;
            }
            .daterange {
                width: 230px !important;
            }
        </style>
    </head>
    <body class="skin-blue sidebar-mini  @if(Auth::user()->id != 510) sidebar-collapse @endif">
        <div class="wrapper">

            @if(Auth::user()->id == 23)
                @include('partials.header-car')
                @include('partials.sidebar-car')
            @elseif(Auth::user()->id == 510)
                @include('partials.header-hr')
                @include('partials.sidebar-hr')
            @else
                @include('partials.header')
                @if($city_id_default == 1)
                 @include('partials.sidebar')
                @else
                    @include('partials.sidebar-other')
                @endif
            @endif
            @yield('content')
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
                <div class="pull-right hidden-xs"><b>Version</b> 2.3.9</div>
                <strong>Copyright &copy; 2022 <a href="mailto:contact@plantotravel.vn">contact@plantotravel.vn</a>.</strong> All rights reserved.
            </footer>
<div class="modal fade" id="confirmNopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="text-align: center;">
       <div class="modal-header bg-green">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4>LẤY NỘI DUNG CK NỘP TIỀN</h4>
      </div>
      <div class="modal-body" id="loadConfirm">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnYcNop">LẤY ND CK NỘP TIỀN</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">ĐÓNG</button>
      </div>
    </div>
  </div>
</div>
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
        <input type="hidden" id="route_change_value_by_column" value="{{ route('change-value-by-column-general') }}" />
        <input type="hidden" id="route_booking_get_content_nop" value="{{ route('booking.get-content-nop') }}" />
        <input type="hidden" id="route_booking_get_content_nop_dt" value="{{ route('booking.get-content-nop-dt') }}" />
        <input type="hidden" id="route_booking_get_confirm_nop" value="{{ route('booking.get-confirm-nop') }}" />
        <!-- ./wrapper -->

        <!-- jQuery 2.2.3 -->
        <script src="{{ asset('admin/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{ asset('js/jquery-ui.js') }}"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge("uibutton", $.ui.button);
        </script>
        <script type="text/javascript">
            var public_url = '{{ env('APP_URL') }}';
        </script>
        <!-- Bootstrap 3.3.6 -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        {{-- <script src="{{ asset('admin/bootstrap/js/bootstrap.min.js') }}"></script> --}}
        <script src="{{ asset('admin/dist/js/ajax-upload.js') }}"></script>
        <script src="{{ asset('admin/dist/js/form.js') }}"></script>
        <script src="{{ asset('admin/dist/js/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('admin/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('admin/dist/js/es6-promise.min.js') }}"></script>
        <script src="{{ asset('js/moment.min.js') }}"></script>

        <!-- Slimscroll -->
        <script src="{{ asset('admin/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ asset('admin/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <!-- AdminLTE App -->
        <script src="{{ asset('admin/dist/js/app.min.js') }}"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="{{ asset('admin/dist/js/pages/dashboard.js?v=1.0.9') }}"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="{{ asset('admin/dist/js/demo.js') }}"></script>
        <script src="{{ asset('admin/dist/js/lazy.js') }}"></script>
        <script src="{{ asset('admin/dist/js/number.js') }}"></script>
        <script src="{{ asset('admin/dist/js/ckeditor/ckeditor.js') }}"></script>
        <script src="{{ asset('admin/dist/js/html2canvas.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>

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
            .bootstrap-datetimepicker-widget tr td{
                text-align: center !important;
            }
            .bootstrap-datetimepicker-widget tr td:hover {
                background-color: #00a65a !important;
                cursor: pointer !important;
                color: #FFF !important;
            }
            /*.table-condensed>tbody>tr>td, .table-condensed>tbody>tr>th, .table-condensed>tfoot>tr>td, .table-condensed>tfoot>tr>th, .table-condensed>thead>tr>td, .table-condensed>thead>tr>th{
                padding: 10px !important;
            }*/
            /*.table-condensed{
                margin-right: 15px !important;
            }*/
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
                    url: "{{ route('customer.noti') }}",
                    type: "GET",
                    data : {
                        id : {{ Auth::user()->id }}
                    },
                    success: function (data) {
                        $("#content_alert").append(data);
                    },
                });
                $('#contact_date').click();
                // $.ajax({
                //     url: "{{ route('booking.not-export') }}",
                //     type: "GET",
                //     success: function (data) {
                //         $("#content_alert").append(data);
                //     },
                // });

                $.ajax({
                    url: "{{ route('payment-request.urgent') }}",
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
                    error : function(jqXHR, textStatus, errorThrown) {
                        if(jqXHR.status === 401) {
                            window.location.reload();
                        }
                    }
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
        </style>

        @yield('js')
        <script type="text/javascript">
            $(document).ready(function () {
                $('#btnContentNop').click(function(){
                    var obj = $(this);
                    var str_id = '';
                    $('.check_one:checked').each(function(){
                        str_id += $(this).val() + ',';
                    });
                    if(str_id != ''){
                      $.ajax({
                        url : $('#route_booking_get_confirm_nop').val(),
                        type : 'GET',
                        data : {
                          str_id : str_id
                        },
                        success: function(data){
                          $('#loadConfirm').html(data);
                          $('#confirmNopModal').modal('show');
                        }
                      });
                    }

                 }); //btnContentNop
                $('#btnContentNopDT').click(function(){
                    var obj = $(this);
                    var str_id = '';
                    $('.check_one:checked').each(function(){
                        str_id += $(this).val() + ',';
                    });
                    if(str_id != ''){
                      $.ajax({
                        url : $('#route_booking_get_confirm_nop').val(),
                        type : 'GET',
                        data : {
                          str_id : str_id,
                          dt : 1
                        },
                        success: function(data){
                          $('#loadConfirm').html(data);
                          $('#confirmNopModal').modal('show');
                        }
                      });
                    }

                 }); //btnContentNop
                  $('#btnYcNop').click(function(){
                      var obj = $(this);
                      var str_id = '';
                      $('.check_one:checked').each(function(){
                          str_id += $(this).val() + ',';
                      });
                      var url = '';
                      if($('#dt').val() == 1){
                        url = $('#route_booking_get_content_nop_dt').val();
                      }else{
                        url = $('#route_booking_get_content_nop').val();
                      }
                      if(str_id != ''){
                        $.ajax({
                          url : url,
                          type : 'GET',
                          data : {
                            str_id : str_id,
                            content : $('#content_nop').val()
                          },
                          success: function(data){
                            $('#noi_dung_nop').html(data);
                            $('#btnYcNop').hide();
                          }
                        });
                      }

                   }); //btnYcNop
                $(".datepicker").datepicker({
                    dateFormat: "dd/mm/yy",
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '-100:+2'
                });
                // $(".datetimepicker").datetimepicker({
                //     sideBySide : true,
                //     format : "DD/MM/YYYY H:m",
                // });
                $(".daterange").daterangepicker({
                    // startDate: moment().startOf('month'),
                    // endDate: moment().endOf('month'),
                    linkedCalendars: false,
                    showDropdowns: true,
                    alwaysShowCalendars: true,
                    ranges: {
                        'Hôm nay': [moment(), moment()],
                        'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 ngày': [moment().subtract(6, 'days'), moment()],
                        '30 ngày': [moment().subtract(29, 'days'), moment()],
                        'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                        'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    locale: {
                        format:  "DD/MM/YYYY",
                        cancelLabel: 'Huỷ',
                        customRangeLabel: "Tuỳ chọn",
                        applyLabel: "Áp dụng",
                        daysOfWeek: [
                            "CN",
                            "T2",
                            "T3",
                            "T4",
                            "T5",
                            "T6",
                            "T7"
                        ],
                        monthNames: [
                            "Tháng 1",
                            "Tháng 2",
                            "Tháng 3",
                            "Tháng 4",
                            "Tháng 5",
                            "Tháng 6",
                            "Tháng 7",
                            "Tháng 8",
                            "Tháng 9",
                            "Tháng 10",
                            "Tháng 11",
                            "Tháng 12"
                        ]
                    }
                });

                $("#btnQuickSearch").click(function () {
                    if ($.trim($("#keyword").val()) != "") {
                        location.href = "{{ route('booking.fast-search')}}?keyword=" + $("#keyword").val();
                    }
                });
                $("#keyword").on("keydown", function (e) {
                    if (e.which == 13) {
                        if ($.trim($("#keyword").val()) != "") {
                            location.href = "{{ route('booking.fast-search')}}?keyword=" + $("#keyword").val();
                        }
                    }
                });
                $('input[name=city_default]').change(function(){
                    var obj = $(this);
                    $.ajax({
                        url  : "{{ route('set-city-id-default')}}",
                        type : 'GET',
                        data : {
                            city_id : obj.val()
                        },
                        success : function(){
                            window.location.reload();
                        }
                    });
                });
            });
        </script>
    </body>
</html>
