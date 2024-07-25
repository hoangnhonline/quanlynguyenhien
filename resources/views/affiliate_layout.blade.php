<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>Plan To Travel</title>
        <meta property="og:type" content="website" />
        <meta property="og:image" content="images/logo-plan-to-travel.png" />
        <meta name="robots" content="noindex" />
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300&display=swap" rel="stylesheet" />
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{ asset('admin/bootstrap/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('https://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css') }}" />
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css?v=2.1') }}" />
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ asset('css/ionicons.min.css?v=2.1') }}" />
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset('admin/plugins/iCheck/flat/blue.css?v=2.0') }}" />
        <link rel="stylesheet" href="{{ asset('admin/plugins/colorpicker/bootstrap-colorpicker.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin/dist/css/select2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin/dist/css/sweetalert2.min.css') }}" />
        <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}" />
    </head>
    <body>
        @yield('content')

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
        <script src="{{ asset('admin/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('admin/dist/js/ajax-upload.js') }}"></script>
        <script src="{{ asset('admin/dist/js/form.js') }}"></script>
        <script src="{{ asset('admin/dist/js/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('admin/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('admin/dist/js/es6-promise.min.js') }}"></script>
        <script src="{{ asset('js/moment.min.js') }}"></script>

        <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        @yield('js')
    </body>
</html>
