<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,height=device-height, initial-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Zukosha</title>

    <!-- Custom Fonts -->
    <link href="{{ url('/css/admin.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/common.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/jquery.fancybox.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ url('/css/select2.min.css')}}" rel="stylesheet" />
    <link href="{{ url('/css/jquery_validation/validationEngine.jquery.css')}}" rel="stylesheet" />
    @yield('header')

</head>

<body>
<div id="loading" class="centered">
    <div class="centered-icon">
    <img src="{{url('images/fancybox_loading.gif')}}" />
    </div>
</div>
@yield('content')

@include('admin.common.script')

@yield('footer')
</body>

</html>