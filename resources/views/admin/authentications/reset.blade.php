@extends('login')

@section('content')
<link href="{{ asset('/css/user.css') }}" rel="stylesheet">


<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<h4 class="panel-heading">{{ trans('common.resetpassword_title') }}</h4>
				<div class="panel-body">

					{!! Form::open( ['url' => '/submit-reset-password','method' => 'post', 'class'=>'form-horizontal frm-resetpassword reset-password-form','id'=>'reset-password', 'name' => '']) !!}
												
						<div class="form-group">
							<label class="col-md-3" style="margin-top: 5px;">{{ trans('common.resetpassword_lable_password') }}</label>
							<div class="col-md-9">
								<input type="hidden" value="{{$data['username']}}" class="form-control" name="username">
								<input type="hidden" value="{{$data['guid']}}" class="form-control" name="guid">
							
							
								{!! Form::password('password',array('id' => 'password','maxlength'=>'30', 'class' => 'form-control onlyAlphaNumeric validate[required,minSize[8],custom[onlyLetterNumber]] ',
                                'data-prompt-position' => 'topRight:-250',
                                'data-errormessage-value-missing' =>  trans('common.resetpassword_password_required'),
									'data-errormessage-range-underflow' =>  trans("common.resetpassword_message_min_password"),
								  	'data-errormessage-custom-error' =>  trans("common.changinguser_message_password_invalid") 
								)) !!}
																  
								  
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-md-3" style="margin-top: 5px;">{{ trans('common.resetpassword_lable_confirmpassword') }}</label>
							<div class="col-md-9">
								{!! Form::password('password_confirmation',array('id' => 'password_confirmation','maxlength'=>'30', 'class' => 'form-control onlyAlphaNumeric validate[equals[password]]',
                                'data-prompt-position' => 'topRight:-250',
									'data-errormessage-pattern-mismatch'=> trans("common.resetpassword_password_comfirm_not_map")
								)) !!}
								
								
							</div>
						</div>
												
                        <hr>
                        <div class="col-md-12" style="text-align: center;">
								
								<button type="button" onclick="" class="button-submit btn-reset-password" >
									{{ trans('common.resetpassword_button_reset') }}
								</button>
								<button type="button" class="button-submit btn_cancel" onclick='location.href=window.base_url;'>
									{{ trans('common.resetpassword_button_cancel') }}
								</button>		
                        </div>
						
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('footer')
	<script src="{{url('/js/modules/resetpassword.js')}}"></script>
	<script type="text/javascript"> 
	window.resetpassword_confirm ="{{ trans('common.resetpassword_confirm') }}";
	window.resetpassword_password_comfirm_not_map ="{{ trans('common.resetpassword_password_comfirm_not_map') }}";
		
	</script>
@endsection
