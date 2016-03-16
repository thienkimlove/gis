@extends('popup')

@section('content')

<link href="{{ asset('/css/user.css') }}" rel="stylesheet">
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->

<div class="container-fluid">
	<div class="row">
		<div class="col-md-11 col-xs-12">
				<h3 class="page-header">{{trans('common.folder_terain_edit_form_title')}}</h3>
				<div class="modal-body">
					{!! Form::open( ['route' => ['admin.folders.updateLayer',$folder->id],'method' => 'post', 'class'=>'form-horizontal export-map-from']) !!}
						{!! Form::hidden('folderId', $folder->id) !!}
						<div class="form-group">
							<label class="col-md-12">{{ trans('common.folder_create_label_name') }}</label>
							<div class="col-md-12">
								{!! Form::text('name',$folder->name, array('id' => 'txt_layer_name', 'maxlength'=>'100', 'class' => 'form-control validate[required,maxSize[100]]','data-errormessage-range-overflow' => trans("common.folder_terrain_create_name_max"),'data-errormessage-value-missing' =>  trans("common.folder_terrain_create_name_required") 
								 )) !!}  
							</div>
						</div>

                        <hr>
                        <div class="form-group"></div>
                        <div class="col-md-12">
							{!! Form::button(trans('common.folder_create_btn_save'),array('class' => 'button-submit btn-save-edit','type' => 'button')) !!}
							{!! Form::button(trans('common.folder_create_btn_cancel'),array('onclick'=>'location.href=window.base_url;','class' => 'button-submit btn-reset-form','type' => 'button')) !!}
                        </div>
						
					{!! Form::close() !!}
				</div>
		</div>
	</div>
</div>
				
@endsection

@section('footer')
<script src="{{url('/js/modules/folder_form.js')}}"></script>
@endsection
