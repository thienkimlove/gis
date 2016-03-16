@extends('popup')

@section('content')

<link href="{{ asset('/css/user.css') }}" rel="stylesheet">
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->

	<div class="row">
				<h2 class="page-header">{{trans('common.folder_terain_create_form_title')}}</h2>

					{!! Form::open( ['route' => 'admin.folders.storeLayer','method' => 'post', 'class'=>'form-horizontal export-map-from']) !!}
						
						<div class="form-group">
							<label class="col-md-3" style="margin-top: 4px">{{ trans('common.folder_create_label_name') }}</label>
							<div class="col-md-9">
								{!! Form::text('name','', array('id' => 'txt_layer_name','autofocus', 'maxlength'=>'100',
                                'class' => 'form-control text-layer-name validate[required,maxSize[100]]',
                                'data-prompt-position' => 'topRight:-100','autoPositionUpdate',
                                'data-errormessage-range-overflow' => trans("common.folder_terrain_create_name_max"),
                                'data-errormessage-value-missing' =>  trans("common.folder_terrain_create_name_required")
								 )) !!}  
							</div>
						</div>
						
					   <div class="form-group">
							<label class="col-md-3" style="margin-top: 4px">{{ trans('common.folder_terain_create_label_scale_type') }}</label>
							<div class="col-md-9">
								{!! Form::select('scaleType',$scaleTypes,null,array('id' => 'scaleType','class' => 'form-control custom-input validate[required]',
                                'data-prompt-position' => 'topRight:-100',
                                'data-errormessage-value-missing' =>  trans("common.folder_terrain_create_scale_type_required")  )) !!}
							</div>
						</div>
					    
                        <hr>
							{!! Form::button(trans('common.folder_create_btn_save'),array('class' => 'button-submit btn-save-edit','type' => 'button')) !!}
							{!! Form::button(trans('common.folder_create_btn_cancel'),array('onclick'=>'parent.$.fancybox.close();','class' => 'button-submit btn-reset-form','type' => 'button')) !!}

					{!! Form::close() !!}
	</div>

@endsection

@section('footer')
<script src="{{url('/js/modules/folder_form.js')}}"></script>
    <script>
        setTimeout(function() {
            $(".text-layer-name").focus();
        }, 0);
        controlTabC('text-layer-name','btn-reset-form');
    </script>
@endsection
