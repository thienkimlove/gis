@extends('login')
@section('content')
<link href="{{asset('css/viewhelp.css')}}" rel="stylesheet">
<form>
<input type="hidden" id="file" value="<?php echo $file ?>" /></form>
<div class="term-centered tou-parent-layout col-xs-12">
    <div id="holder" class="term-holder"></div>
</div>
@endsection
@section('footer')
<script type="text/javascript" src="{{url('js/libs/pdf/build/pdf.js')}}"></script>
<script type="text/javascript" src="{{url('js/libs/pdf/web/compatibility.js')}}"></script>
<script type="text/javascript" src="{{url('js/modules/viewhelp.js')}}"></script>
@endsection
