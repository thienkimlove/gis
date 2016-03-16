@extends('popup')

@section('content')
<link href="{{ asset('/css/standard-crop-info.css') }}" rel="stylesheet">
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->
{!! Form::open(array('route' => 'submit-standard-crop','method' => 'post','class' => 'form-horizontal standard-crop-info-frm')) !!}
	<div class ="row">	
		<h2 class="page-header">{{trans('common.standardcropinfo_title')}}</h2>
		<input type="hidden" name="fertilizer_standard_definition_id" value = "{{$fertilizer->id}}" />
		<input type="hidden" name="id" value = "{{$standardCrop->id}}" />
	</div>
	<div class="row">	
		<!-- 	Begin column 2 -->
        <div class="col-md-10 form-header-column">	        	
			<div class="form-group ">
				<label class="standard-crop-label col-md-4 col-md-offset-1">{{ trans('common.fertilizer_info_standard') }}</label>							
				<div class="col-md-6">								
					{!! Form::text('fertilization_standard_name',$fertilizer->fertilization_standard_name, array('id' => 'fertilization_standard_name', 'maxlength'=>'20','readonly'=>'true', 'class' => 'form-control  ',
						
					 )) !!}														
				</div>
			</div>
			
			<div class="form-group">
				<label class="standard-crop-label col-md-4 col-md-offset-1">{{ trans('common.standardcrop_crop') }}</label>
				<div class="col-md-6">
					{!! Form::select('crops_id', $crops,$standardCrop->crops_id,array('id' => 'search-select-usergroup','autofocus','class'=>'form-control select-user-group col-md-6 validate[required]',
						'data-errormessage-value-missing' =>  trans('common.standardcropinfo_crop_required')
					)) !!}							  
				</div>
			</div>
			
			<div class="form-group">
				<label class="standard-crop-label col-md-4 col-md-offset-1">{{ trans('common.standardcrop_n') }}</label>
				<div class="col-md-3">				
					{!! Form::text('fertilization_standard_amount_n',$standardCrop->fertilization_standard_amount_n, array('id' => 'fertilization_standard_amount_n', 'maxlength'=>'4', 'class' => 'form-control onlyNumeric validate[required]',
                    'data-prompt-position' => 'topRight:20,0',
                    'data-errormessage-value-missing' =>  trans('common.standardcropinfo_n_required')
					  )) !!}											
				</div>
			</div>
			<div class="form-group">
				<label class="standard-crop-label col-md-4 col-md-offset-1">{{ trans('common.standardcrop_p') }}</label>
				<div class="col-md-3">				
					{!! Form::text('fertilization_standard_amount_p',$standardCrop->fertilization_standard_amount_p, array('id' => 'fertilization_standard_amount_p', 'maxlength'=>'4', 'class' => 'form-control onlyNumeric validate[required]',
                    'data-prompt-position' => 'topRight:20,0',
                    'data-errormessage-value-missing' =>  trans('common.standardcropinfo_p_required')
					  )) !!}											
				</div>
			</div>
			<div class="form-group">
				<label class="standard-crop-label col-md-4 col-md-offset-1">{{ trans('common.standardcrop_k') }}</label>
				<div class="col-md-3">				
					{!! Form::text('fertilization_standard_amount_k',$standardCrop->fertilization_standard_amount_k, array('id' => 'fertilization_standard_amount_k', 'maxlength'=>'4', 'class' => 'form-control onlyNumeric validate[required]',
                    'data-prompt-position' => 'topRight:20,0',
                    'data-errormessage-value-missing' =>  trans('common.standardcropinfo_k_required')
					  )) !!}											
				</div>
			</div>
		</div>		
		<!-- 	End column 2 -->
		<!-- 	Begin column 3 -->
        <div class="col-md-10 form-header-column">	        	
			<div class="form-group ">
				<label class="standard-crop-label col-md-4 col-md-offset-1">{{ trans('common.standardcrop_remarks') }}</label>							
				<div class="col-md-6">								
					{!! Form::text('remarks',$standardCrop->remarks, array('id' => 'remarks', 'maxlength'=>'300', 'class' => 'form-control'
					 )) !!}														
				</div>
			</div>
			
			<div class="form-group">
				<label class="standard-crop-label col-md-4 col-md-offset-1">{{ trans('common.standardcrop_not_avarible') }}</label>
				<div class="col-md-6">	
					{!! Form::checkbox('not_available', null,$standardCrop->not_available) !!}							  
					  
				</div>
			</div>

		</div>		
		<!-- 	End column 3 -->
	</div>
	<div class ="row">
        <hr style="margin-top: 16px;margin-bottom: 14px;">
        {!! Form::button(trans('common.button_save'),array('class' => ' btn-save-standard-crop button-submit','type' => 'button')) !!}
		{!! Form::button(trans('common.button_cancel'),array('class' => ' button-submit btn-cancel-popup','type' => 'button','style'=>'margin:8px 0 0 0;')) !!}
			
	</div>
{!! Form::close() !!}
@endsection


@section('footer')
	<script src="{{url('/js/modules/standardcropinfo.js')}}"></script>	
    <script>
        setTimeout(function(){
            $('.select-user-group').focus();
        },0);
            controlTabC('select-user-group','btn-cancel-popup');
    </script>
@endsection