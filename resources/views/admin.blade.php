<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <meta http-equiv="Expires" content="0" />
   <meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0" />
   <meta http-equiv="Cache-Control" content="no-store" />
   <meta http-equiv="Cache-Control" content="no-cache" />
   <meta http-equiv="Pragma" content="no-cache" />
    <meta name="viewport" content="width=device-width, initial-scale=1,minimal-ui">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="" />
    <title>Zukosha</title>


    <!-- Custom Fonts -->

    <link href="{{ url('/css/common.css') }}" rel="stylesheet" type="text/css">  
    <link href="{{ url('/css/admin.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/select2.min.css')}}" rel="stylesheet" />
    <link href="{{ url('/css/common.css')}}" rel="stylesheet" />
    <link href="{{ url('/css/jquery_validation/validationEngine.jquery.css')}}" rel="stylesheet" />
	<link href="{{ url('/css/jquery.fancybox.css')}}" rel="stylesheet" />
    <link href="{{ asset('/js/jqgrid/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('/js/jqgrid/ui.jqgrid.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/search-auto.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/fertilizer.css') }}" rel="stylesheet">

    
</head>

<body >
    <div>@include('admin.common.nav')</div>
    <div id="loading" class="centered">
        <div class="centered-icon">
        <img src="{{url('images/fancybox_loading.gif')}}" />
        </div>
    </div>
	<input type="hidden" id="fancy-list" value="1111" />
	<input type="hidden" id="fancy-level1" />
	<input type="hidden" id="fancy-level2" />
	<input type="hidden" id="fancy-level3" />
	<input type="hidden" id="fancy-level4" />
    <div id="wrapper">
    <div class="container-fluid"  style="background-color: #fff;padding-bottom:50px;min-width:970px;">
        @include('flash::message')
        @yield('content')
    </div>
    <footer  id="footer" class="footer">
    @include('admin.common.footer')
    @include('admin.common.script')
    @yield('footer')
    </footer>
    </div>
</body>


</html>
