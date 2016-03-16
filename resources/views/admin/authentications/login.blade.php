@extends('login')
@section('content')
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->
{!! Form::open(array('route' => 'do-login','method' => 'post','name' => 'login-frm','class' => 'form-horizontal frm-validation-login')) !!}
    <div class="container">
    <div class="col-md-5 col-sm-7 col-xs-12 login-centered" >
        <div class="login-title">{{ trans('common.login_form_title') }}</div>
        <div class="login-field" >
        	<table>
	        	<colgroup>
	        		<col class="login-col1" width="40%"><col>
	        		<col class="login-col2" width="*"><col>
	        	</colgroup>
        		<tr>
        			<td><label>{{ trans('common.login_label_username') }}</label></td>
        			<td>
        				{!! Form::text('username','',array('maxlength' => $validateData['maxLengthUsername'],'id' => 'txtUserName','class' => 'form-control custom-input validate[required,maxSize[20]]','data-errormessage-range-overflow' =>  trans("common.login_username_max"),'data-errormessage-custom-error' =>  trans("common.login_username_alpha_num"),'data-errormessage-value-missing' =>  trans("common.login_username_required")  )) !!}

        			</td>
        		</tr>
        		<tr>
        			<td><label>{{ trans('common.login_label_password') }}</label></td>
        			<td>
        				{!! Form::password('password',array('maxlength' => $validateData['maxLengthPassword'],'id' => 'password','class' => "form-control custom-input validate[required,maxSize[30],minSize[8],custom[onlyLetterNumber]]",'data-errormessage-range-underflow' =>  trans("common.user_registration_password_min"),'data-errormessage-range-overflow' =>  trans("common.login_password_max"),'data-errormessage-custom-error' =>  trans("common.login_password_alpha_num"),'data-errormessage-value-missing' =>  trans("common.login_password_required")  )) !!}
        			</td>
        		</tr>
        		<tr>
        			<td></td>
        			<td><a href="{{ url('forget-password') }}">{{ trans('common.login_label_forgot_link') }}</a></td>
        		</tr>
        		
        	</table>
        </div>

        <div class="login-btn col-md-12 col-xs-12" >
            	{!! HTML::link( route('login-guest') , trans('common.login_label_button_guest'), array('class' => 'login-btnGuest alert white-button','data-toggle'=>'tooltip','data-placement'=>"top",'style'=>'margin-bottom: 5px;')) !!}
        	{!! Form::button(trans('common.login_label_button_submit'),array('class' => 'login-btnLogin button-submit','type' => 'button')) !!}
        	
        </div>
        <div ><span class="guest-button-note ">{{ trans('common.login_login_guest_intro') }}</span></div>
    </div>
    </div>
{!! Form::close() !!}
@endsection

@section('footer')
<script src="{{url('/js/modules/login.js')}}"></script>
@endsection

