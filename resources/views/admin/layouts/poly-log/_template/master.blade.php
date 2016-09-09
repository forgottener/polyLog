<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LogViewer</title>
    <meta name="description" content="LogViewer">
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/font-awesome/4.6.3/css/font-awesome.min.css") }}">
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css") }}">
    @include('admin.layouts.poly-log._template.style')
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.3/html5shiv-printshiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    @include('admin.layouts.poly-log._template.navigation')

    <div class="container-fluid">
        @yield('content')
    </div>

    @include('admin.layouts.poly-log._template.footer')

    <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}"></script>
    <script src="//cdn.bootcss.com/moment.js/2.14.1/moment-with-locales.min.js"></script>
    <script src="{{ asset("/bower_components/AdminLTE/plugins/chartjs/Chart.min.js") }}"></script>
    <script src="{{ asset("/bower_components/AdminLTE/plugins/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js") }}"></script>
    <script>
        Chart.defaults.global.responsive      = true;
        Chart.defaults.global.scaleFontFamily = "'Source Sans Pro'";
        Chart.defaults.global.animationEasing = "easeOutQuart";
    </script>
    @yield('scripts')
</body>
</html>
