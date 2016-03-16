@extends('admin')

@section('content')

<link href="{{ asset('/css/user.css') }}" rel="stylesheet">

<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<h4 class="panel-heading">{{ trans('common.changinguser_title') }}</h4>
				<div class="panel-body">
					@if (session('status'))
						<div class="alert alert-success">
							{{ session('status') }}
						</div>
					@endif
					{!! Form::open( ['url' => '/submit-changing-user','method' => 'post', 'autocomplete'=>'off','class'=>'form-horizontal changing-user-form validate[minSize[8],custom[onlyLetterNumber]]','id'=>'forget-password', 'name' => 'changing-user-form']) !!}
						
						<div class="form-group ">

							<label class="col-md-3" style="margin-top: 4px">{{ trans('common.changinguser_lable_username') }}</label>
							<div class="col-md-9">
								
								{!! Form::text('username',$user->username, array('id' => 'txtUserName','autocomplete'=>'off', 'maxlength'=>'20', 'class' => 'form-control validate[required,custom[onlyLetterNumber]]',
									
								 )) !!}
														
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-md-3" style="margin-top: 4px;">{{ trans('common.changinguser_lable_password') }}</label>
							<div class="col-md-9">
								{!! Form::password('password',array('id' => 'password','autocomplete'=>'off', 'maxlength'=>'30', 'class' => 'form-control onlyAlphaNumeric validate[funcCall[notEquals]]',
								  	'data-errormessage-range-underflow' =>  trans("common.changinguser_message_min_password"),
								  	'data-errormessage-custom-error' =>  trans("common.changinguser_message_password_invalid") 
								  )) !!}								  
								  
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-md-3" style="margin-top: 4px">{{ trans('common.changinguser_lable_confirm') }}</label>
							<div class="col-md-9">
								{!! Form::password('confirm_password',array('id' => 'confirm_password', 'maxlength'=>'30', 'class' => 'form-control onlyAlphaNumeric validate[equals[password]]',
									'data-errormessage-range-underflow' =>  trans("common.changinguser_message_min_password"),
									'data-errormessage-pattern-mismatch'=> trans("common.changinguser_password_comfirm_not_map") 
								 )) !!}
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3" style="margin-top: 4px;">{{ trans('common.changinguser_lable_email') }}</label>
							<div class="col-md-9">

							
								{!! Form::email('email',$user->email, array('id' => 'email', 'maxlength'=>'50', 'class' => 'form-control validate[required,custom[email]]',
                                'data-prompt-position' => 'topRight:-150,0',
								'data-errormessage-custom-error' =>  trans("common.changinguser_email_invalid")  )) !!}
								
														
							</div>
						</div>
												
                        <hr>
                        <div class="col-md-12" style="text-align: center;">
								{!! Form::button(trans('common.changinguser_button_change'), ['class' => 'button-submit btn-changing-user']) !!}
																
								<button id="btn-cancal" type="reset" onclick="changinguser.cleanForm();" class="button-submit btn-changing-cancel" style="margin:10px 0 0 0;">
									{{ trans('common.changinguser_button_cancel') }}
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
	<script src="{{url('/js/changinguser.js')}}"></script>	
	<script src="{{url('/js/common.js')}}"></script>	
    <script>

    window.username ="{{session('user')->username}}";
    window.email ="{{session('user')->email}}";
    window.changinguser_message_all_data_empty ="{{ trans('common.changinguser_message_all_data_empty') }}";
    window.changinguser_message_min_password ="{{ trans('common.changinguser_message_min_password') }}"; 
    
    window.changinguser_message_password_comfirm ="{{ trans('common.changinguser_message_password_comfirm') }}";
    window.changinguser_password_comfirm_not_map ="{{ trans('common.changinguser_password_comfirm_not_map') }}"; 


    controlTab('username','btn-cancal');
    </script>
@endsection
