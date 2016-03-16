@extends('login')

@section('content')

<link href="{{ asset('/css/user.css') }}" rel="stylesheet">

<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->

		
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<h4 class="panel-heading">{{ trans('common.forgot_password') }}</h4>
				<div class="panel-body">
					{!! Form::open( ['url' => '/send-email','method' => 'post', 'class'=>'form-horizontal forget-password-form','id'=>'forget-password', 'name' => 'forget-password-form']) !!}
						<div class="form-group">
							<label class="col-md-3" style="margin-top: 4px;">{{ trans('common.forgetpassword_lable_email') }}</label>
							<div class="col-md-9">
								{!! Form::text('email','', array('id' => 'email','maxlength'=>'50', 'class' => 'form-control forgetpassword-email validate[required,custom[email]]',
                                'data-prompt-position' => 'topRight:-250',
                                'data-errormessage-custom-error' =>  trans("common.forgetpassword_email_invalid"),
								'data-errormessage-value-missing' =>  trans("common.forgetpassword_email_required")  )) !!}
							</div>
						</div>
												
                        <hr>
                        <div class="col-md-12" style="text-align: center;">
							{!! Form::button(trans('common.forgetpassword_button_sent'), ['class' => 'button-submit btn-forget-password']) !!}
                        </div>
						
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>		
@endsection

@section('footer')	
	<script src="{{url('/js/password.js')}}"></script>	
@endsection


