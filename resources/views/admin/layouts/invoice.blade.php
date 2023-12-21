<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@if($title){{$title}} @else {{ Helper::getAppName() }} @endif</title>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/site/favicon.ICO') }}"/>
    <!-- Styles -->
    <link href="{{ asset('css/admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('css/admin/bower_components/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- Theme style -->
    <link href="{{ asset('css/admin/dist/css/AdminLTE.min.css') }}" rel="stylesheet">
    <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('css/admin/dist/css/skins/_all-skins.min.css') }}">
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">


    <link href="{{ asset('css/admin/development_admin.css') }}" rel="stylesheet">
    
</head>

<body class="hold-transition skin-blue sidebar-mini" onload="window.print();">
    <div class="">        
        @yield('content')
    </div>
    <!-- ./wrapper -->
    
</body>
</html>