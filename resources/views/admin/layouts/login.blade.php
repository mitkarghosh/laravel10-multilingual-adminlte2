<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Administrator - @if($title){{$title}} @else {{ Helper::getAppName() }} @endif</title>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/site/favicon.ico') }}"/>
    <!-- Styles -->
    <link href="{{ asset('css/admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('css/admin/bower_components/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- Ionicons -->
    <link href="{{ asset('css/admin/bower_components/Ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <!-- Theme style -->
    <link href="{{ asset('css/admin/dist/css/AdminLTE.min.css') }}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ asset('css/admin/plugins/iCheck/square/blue.css') }}" rel="stylesheet">
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- Scripts -->
    <!-- jQuery 3 -->
    <script src="{{ asset('js/admin/bower_components/jquery/dist/jquery.min.js') }}"></script>
    
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('js/admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- iCheck -->
    <!-- <script src="{{ asset('js/admin/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
    $(function () {
        $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' /* optional */
        });
    });
    </script> -->

    <script src="{{ asset('js/admin/jquery.validate.min.js') }}"></script>

    <link href="{{ asset('css/admin/development_admin.css') }}" rel="stylesheet">

</head>
<body class="hold-transition login-page" style="background-image: url({{ asset('images/admin/bg_login.jpg') }});">
    <div class="login-box">
        <!-- <div class="logo-area">
            &nbsp;
        </div> -->
        <div class="login-form-content">
            <div class="login-logo">
                @php $logoImage=Helper::getSettingImage('logo');   @endphp
                <a><img style="max-height:100px" src="@if($logoImage){{$logoImage}}@else{{ asset('images/admin/logo.png') }}@endif" alt=""></a>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">
                @yield('content')            
            </div>
            <!-- /.login-box-body -->
        </div>
    </div>

    @php
	if (in_array(App::getLocale(), Helper::WEBITE_LANGUAGES)) {
		$jsLang = App::getLocale();
	@endphp
    <script src="{{ asset('js/admin/development_admin_'.$jsLang.'.js') }}"></script>
	@php
	}
	@endphp    
</body>
</html>