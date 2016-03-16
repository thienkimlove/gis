<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Zukosha</title>

    <!-- Custom Fonts -->
    <link href="{{ url('/css/common.css') }}" rel="stylesheet"   type="text/css">
    <link href="{{ url('/css/admin.css') }}" rel="stylesheet"   type="text/css">
    <link href="{{ url('/css/select2.min.css')}}" rel="stylesheet"  type="text/css" />
    <link href="{{ url('/css/jquery_validation/validationEngine.jquery.css')}}" rel="stylesheet"  type="text/css" />
    <link href="{{ url('/css/jquery.fancybox.css')}}" rel="stylesheet"  type="text/css" />
    <link href="{{ asset('/js/jqgrid/jquery-ui.css') }}" rel="stylesheet"  type="text/css">
    <link href="{{ asset('/js/jqgrid/ui.jqgrid.css') }}" rel="stylesheet"  type="text/css">
    <link href="{{ asset('/css/search-auto.css') }}" rel="stylesheet"  type="text/css">
    <!-- Style for all popup -->
    <link href="{{ asset('/css/popup-style.css') }}" rel="stylesheet"  type="text/css" type="text/css">
    <style>
        #page-wrapper{
            min-height: 430px !important;
            overflow: auto;
        }
    </style>
</head>

<body>

<div id="wrapper">

    <div id="page-wrapper">
        @include('flash::message')
        @yield('content')
    </div>


</div>

@include('admin.common.script')

@yield('footer')

</body>

</html>
