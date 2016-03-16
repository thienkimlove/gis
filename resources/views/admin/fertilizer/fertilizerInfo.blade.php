@extends('popup')

@section('content')
    <style>
        .custom-textarea{
            width: 80%;
        }

    </style>
<link href="{{ asset('/css/fertilizer.css') }}" rel="stylesheet">
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->
<input type="hidden" name="created_by" value="{{$fertilizer->created_by}}">
{!! Form::open(array('route' => 'submit-fertilizer','method' => 'post','name' => 'frm-fertilizer-info','class' => 'form-horizontal frm-validation frm-fertilizer-info')) !!}
    <input type="hidden" id="agreedSave" name="agreedSave" val=''>
	<h2 class="page-header">{{trans('common.fertilizer_info_title')}}</h2>
			<div class="form-group ">
				<label class="fetilizer-label col-md-12">{{ trans('common.fertilizer_info_standard') }}</label>
				<div class="col-md-12">
					<input type="hidden" name="id" value="{{$fertilizer->id}}" >					
					{!! Form::text('fertilization_standard_name',$fertilizer->fertilization_standard_name, array('id' => 'fertilization_standard_name','autofocus','data-prompt-position' => 'topRight:-200', 'maxlength'=>'500', 'class' => 'fertilization_standard_name form-control custom-input validate[required] ',
						'data-errormessage-value-missing' =>  trans("common.fertilizer_info_fertilization_standard_name_required")
					 )) !!}														
				</div>
			</div>
			
			<div class="form-group">
				<label class="fetilizer-label col-md-12">{{ trans('common.fertilizer_info_range') }}</label>
				<div class="col-md-12">
					{!! Form::textarea('range_of_application',$fertilizer->range_of_application, array('id' => 'range_of_application','rows'=>'3',  'maxlength'=>'500', 'class' => 'form-control custom-input'
						
					 )) !!}									  
					  
				</div>
			</div>
			
			<div class="form-group">
				<label class="fetilizer-label col-md-12">{{ trans('common.fertilizer_info_note') }}</label>
				<div class="col-md-12">
					{!! Form::textarea('notes',$fertilizer->notes, array('id' => 'notes','rows'=>'3', 'maxlength'=>'300', 'class' => 'form-control custom-input'
					 )) !!}									  
					  
				</div>
			</div>
			
			<div class="form-group">
				<label class="fetilizer-label col-md-12">{{ trans('common.fertilizer_info_reference') }}</label>
				<div class="col-md-12">
					{!! Form::textarea('remarks',$fertilizer->remarks, array('id' => 'remarks','rows'=>'3', 'maxlength'=>'300', 'class' => 'form-control custom-input',
					  )) !!}
											
				</div>
			</div>
			      	

			<div class="form-group">
				<label class="fetilizer-label col-md-3">{{ trans('common.fertilizer_info_not_avarible') }}</label>
				<div class="col-sm-1">
					{!! Form::checkbox('not_available', null,$fertilizer->not_available) !!}
				</div>
				@if(session('user')->usergroup->auth_authorization)
				<label class="fetilizer-label col-md-3">{{ trans('common.fertilizer_info_initial_display') }}</label>
				<div class="col-sm-1">
					{!! Form::checkbox('initial_display', null,$fertilizer->initial_display) !!}
				</div>
			</div>
			<div class="form-group">
			@if(session('user')->usergroup->auth_authorization && $fertilizer->created_by!=2)
				<label class="fetilizer-label col-md-3">{{ trans('common.fertilizer_info_public') }}</label>
				<div class="col-sm-1">
					{!! Form::checkbox('public', null,$fertilizer->created_by==0) !!}
				</div>
			@endif
			    <div id="basic" style="display: none;">
                <label class="fetilizer-label col-md-3">{{ trans('common.fertilizer_info_basis_of_calculation') }}</label>
                <div class="col-sm-1">
                    {!! Form::checkbox('basis_of_calculation', null,$fertilizer->basis_of_calculation) !!}
                </div>
	        </div>
	        @endif
		<!-- 	End column 2 -->
		<!-- 	Begin column 3 -->
		<!-- 	End column 3 -->
	</div>
    <hr>
        <button type="button" class="btn-save-fertilizer button-submit">{{trans('common.user_registration_label_button_save')}}</button>
        <button type="button" class="btn-cancel-popup button-submit">{{trans('common.user_registration_label_button_cancel')}}</button>
	{!! Form::close() !!}


@endsection


@section('footer')
	<script src="{{url('/js/modules/fertilizerinfo.js')}}"></script>	
    <script>
        setTimeout(function() {
            $("#fertilization_standard_name").focus();
        }, 0);
        controlTabC('fertilization_standard_name','btn-cancel-popup');
    $(document).ready(function(){
        if($("input[name='id']").val()!='')
        $( "input[name='public']" ).attr('disabled',1);
        if($("input[name='created_by']").val()=='0')
            $('#basic').show();
    });
    </script>
@endsection