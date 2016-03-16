@extends('admin')

@section('content')

<link href="{{ asset('/css/user.css') }}" rel="stylesheet">
<link href="{{ asset('/themes/default/style.min.css') }}" rel="stylesheet">


<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->


<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header">{{trans('common.folder_layer_title')}}</h2>
        <input type="hidden" id="json-folder-data" data-meta="<?php echo $folders;?>" />
        {!! Form::token() !!}
        <div class="data  col-lg-6  col-md-6 admin-folder-list " style="padding-left: 0;">
        </div>

        <div class="col-md-8" style="padding-left: 0;">
            {!! Form::button(trans('common.folder_button_create'),array('class' => 'button-submit btn-create-folder','type' => 'button','data-action' => 'create')) !!}
            {{--{!! Form::button(trans('common.folder_button_terain_create'),array('class' => 'button-submit btn-create-folder','type' => 'button','data-action' => 'create-layer')) !!}--}}
            {!! Form::button(trans('common.folder_button_upload'),array('class' => 'button-submit btn-reset-form btn-upload-layer','type' => 'button')) !!}
            {{--{!! Form::button(trans('common.folder_button_edit'),array('class' => 'button-submit btn-edit-folder','type' => 'button','data-action' => 'edit')) !!}--}}
            {{--{!! Form::button(trans('common.folder_button_delete'),array('class' => 'button-submit btn-delete-folder','type' => 'button')) !!}--}}
        </div>
        <div id="event_result"></div>

    </div>
</div>

@endsection

@section('footer')
<script>
 var isAdmin={!! json_encode(session('user')->usergroup->auth_authorization)!!};
</script>
<script src="{{url('/js/jstree.js')}}"></script>
<script src="{{url('/js/tree/lib.js')}}"></script>
<script src="{{url('/js/modules/folder_list.js')}}"></script>

@endsection
