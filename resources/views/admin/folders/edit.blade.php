@extends('popup')

@section('content')

<link href="{{ asset('/css/user.css') }}" rel="stylesheet">
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->

	<div class="row">
		<div class="col-md-12">
				<h2 class="page-header">{{trans('common.folder_edit_form_title')}}</h2>
					{!! Form::open( ['route' => ['admin.folders.update',$folder->id],'method' => 'put', 'class'=>'form-horizontal export-map-from','id'=>'formID']) !!}
					    {!! Form::hidden('folderId',$folder->id) !!}
					    <div class="form-group">
							<label class="reset-label col-md-12" style="margin-top: -10px;">{{ trans('common.folder_create_label_group') }}</label>
							<div class="col-md-12">
								{!! Form::select('groupId[]',$userGroups,$groupSelected,array('multiple'=>'multiple','id' => 'group_id','class' => 'form-control select-box custom-input validate[required]','data-errormessage-value-missing' =>  trans("common.folder_create_group_required")  )) !!}
							</div>
						</div>
						
                        <hr>
							{!! Form::button(trans('common.folder_create_btn_save'),array('autofocus','class' => 'button-submit btn-save-edit','type' => 'button')) !!}
							{!! Form::button(trans('common.folder_create_btn_cancel'),array('onclick'=>'parent.$.fancybox.close();','class' => 'button-submit btn-reset-form','type' => 'button')) !!}

					{!! Form::close() !!}
		</div>
	</div>
	<div style="height: 200px"></div>

@endsection

@section('footer')
<script src="{{url('/js/modules/folder_form.js')}}"></script>
    <script>
        controlTabC('btn-save-edit','btn-reset-form');
    </script>
@endsection
