@extends('popup')

@section('content')
<style>
    .dropdown-menu.pull-right{
        right: 0;
    }
</style>
<link href="{{ asset('/css/user.css') }}" rel="stylesheet">
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->

	<div class="row">
		<div class="col-md-12">
				<h2 class="page-header">{{trans('common.folder_create_form_title')}}</h2>
					{!! Form::open( ['route' => 'admin.folders.store','method' => 'post', 'class'=>'form-horizontal export-map-from',
					'id'=>'formID'
					]) !!}
						<div class="form-group">
							<label class="col-md-12">{{ trans('common.folder_create_label_name') }}</label>
							<div class="col-md-12">
								{!! Form::text('name','', array('id' => 'txt_folder_name','autofocus','data-prompt-position' => 'topRight:-200', 'maxlength'=>'100', 'class' => 'txt_folder_name form-control validate[required,maxSize[100]]','data-errormessage-range-overflow' => trans("common.folder_create_name_max"),'data-errormessage-value-missing' =>  trans("common.folder_create_name_required") ,'data-errormessage-custom-error' =>  trans("common.folder_create_name_alpha")
								 )) !!}  
							</div>
						</div>
						
					   <div class="form-group">
							<label class="col-md-12">{{ trans('common.folder_create_label_type') }}</label>
							<div class="col-md-12">
								{!! Form::select('folderType',$folderTypes,null,array('id' => 'folderType','data-prompt-position' => 'topRight:-200','class' => 'form-control custom-input validate[required]','style' => 'text-align:center;','data-errormessage-value-missing' =>  trans("common.folder_create_type_required")  )) !!}
							</div>
						</div>
					    
					    <div class="form-group">
							<label class="col-md-12">{{ trans('common.folder_create_label_group') }}</label>
							<div class="col-md-12">
								{!! Form::select('groupId[]',$userGroups,null,array('multiple'=>'multiple','id' => 'group_id','data-prompt-position' => 'topRight:-200','class' => 'form-control custom-input validate[required]','data-errormessage-value-missing' =>  trans("common.folder_create_group_required")  )) !!}
							</div>
						</div>
						
                        <hr>
                {!! Form::button(trans('common.folder_create_btn_save'),array('class' => 'button-submit btn-save-edit','type' => 'button')) !!}
                {!! Form::button(trans('common.folder_create_btn_cancel'),array('onclick'=>'parent.$.fancybox.close();','class' => 'button-submit btn-reset-form','type' => 'button')) !!}


					{!! Form::close() !!}
		</div>
	</div>
	<div style="height: 180px;"></div>

@endsection

@section('footer')
<script src="{{url('/js/modules/folder_form.js')}}"></script>
    <script>
        setTimeout(function() {
            $(".txt_folder_name").focus();
        }, 0);
        controlTabC('txt_folder_name','btn-reset-form');
    </script>
@endsection
