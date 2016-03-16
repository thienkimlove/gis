<?php
/**
 * User: smagic39
 * Date: 6/9/15
 * Time: 10:15 AM
 */
?>
@extends('admin')

@section('content')
    <h2 class="page-header">{{trans('common.upload_layer_title')}}</h2>
    {!! Form::open( array( 'route' => 'upload.layer.destroy' , 'id' => 'frm-upload-layer' , 'name' => 'frm-upload-layer' )) !!}
            <table id="jqGrid"></table>
            <div id="jqGridPager"></div>

    <hr>
    <div class="container">
        <div class="row">
            <button class="btn-delete-usermap button-submit " type="button">Delete</button>
            <button class="fancybox-list-btn fancybox.iframe   button-submit" href="{{ route('import.data') }}" type="button">Add</button>
        </div>
    </div>
    <!--MESSAGE-->
    {!! Form::hidden('user_delete_list_data',null,array('id' => 'user_delete_list_data')) !!}
    {!! Form::close()  !!}
@stop


@section('footer')
    <script src="{{url('/js/modules/upload-layer.js')}}"></script>
    <script>
        var status = '{{ Session::get('status') }}';
        if(status.length){
            parent.$.fancybox.close();
            window.location.reload(true);
            parent.location.href = window.location.href;
        }
    </script>
@endsection