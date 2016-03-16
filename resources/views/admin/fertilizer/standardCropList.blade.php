@extends('popup')

@section('content')



  <script>
  
  </script>
  
  
<link href="{{ asset('/css/standardcrop.css') }}" rel="stylesheet">
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->
{!! Form::open(array('route' => 'delete-standard-crops','method' => 'post','class' => 'form-horizontal standard-crops-frm')) !!}
	<div class ="row">	
		<h2 class="page-header">{{$fertilizer->fertilization_standard_name}}</h2>
		<input type="hidden" id="hidden-standard-id" name ="hidden-standard-id" value="{{$fertilizer->id}}">
		<input type="hidden" id="standard_crop_ids" name ="standard_crop_ids">
	</div>
	<div class ="row">				
		<table id="jqGridInfo"></table>
		<div id="jqGridPagerInfo"></div>
			
	</div>
	<div class ="row">
        <hr>
	@if($fertilizer->created_by != 0 || session('user')->usergroup->auth_authorization)
	
	
		<button type="button" class=" btn-add-standard-crop  button-submit btn-form" autofocus>
        	{{trans('common.button_add')}}                
        </button>
        <button type="button" class="btn-edit-standard-crop button-submit btn-form">
            {{trans('common.button_edit')}}
        </button>
        <button type="button" class="btn-copy-standard-crop button-submit btn-form">
            {{trans('common.button_copy_standard_crop')}}
        </button>
        <button type="button" class="btn-delete-standard-crops button-submit btn-form" >
        	{{trans('common.button_delete')}}
        </button>
	@endif
	
	@if(session('user')->usergroup->auth_authorization)
        <button type="button" class="btn-detail-standard-crop button-submit btn-form" autofocus>
        	{{trans('common.button_detail')}}
        </button>
        
	@endif
	</div>
{!! Form::close() !!}

{!! Form::open(array('method' => 'post','class' => 'copy-standard-crop-frm')) !!}
	<input type="hidden" name ="standard-crop-ids" id ="standard-crop-ids" />
{!! Form::close() !!}
@endsection


@section('footer')
	<script src="{{url('/js/modules/standardcroplist.js')}}"></script>	
    <script>
    standardcroplist.loadGridData();
    setTimeout(function(){
       $('.btn-add-standard-crop').focus();
    },0);
    controlTabC('btn-add-standard-crop','btn-detail-standard-crop');
    </script>
@endsection