<div class="form-group">
    <label class="col-md-3">{{ trans('common.user_registration_label_username') }}</label>
    {!! Form::text('username', null, array(
              'maxlength' => 20,
              'id' => 'txtUserName',
              'class' => ' form-control custom-input validate[required,maxSize[20],custom[onlyLetterNumber]]',
              'data-errormessage-range-overflow' =>  trans("common.user_registration_username_max"),
              'data-errormessage-custom-error' =>  trans("common.user_registration_username_alpha_num"),
              'data-errormessage-value-missing' =>  trans("common.user_registration_username_max")
    )) !!}
</div>

 <div class="form-group">
	<label  class="col-md-3">{{ trans('common.user_registration_label_usercode') }}</label>
	@(if($user))
	{!! Form::text('user_code','',array(
	           'maxlength' => 10,
	           'id' => 'usercode',
	           'class' => 'form-control custom-input validate[required,custom[integer]]',
	           'data-errormessage-custom-error' =>  trans("common.user_registration_code_integer"),
	           'data-errormessage-value-missing' =>  trans("common.user_registration_code_required")  
	)) !!}
	@endif
</div>

<div class="form-group">
    <label  class="col-md-3">{{ trans('common.user_registration_label_password') }}</label>
    {!! Form::password('password',array(
             'maxlength' => 30,
             'id' => 'password',
             'class' => "form-control custom-input validate[required,maxSize[30],minSize[8],custom[onlyLetterNumber]]",
             'data-errormessage-range-underflow' =>  trans("common.user_registration_password_min"),
             'data-errormessage-range-overflow' =>  trans("common.user_registration_password_max"),
             'data-errormessage-custom-error' =>  trans("common.user_registration_password_alpha_num"),
             'data-errormessage-value-missing' =>  trans("common.user_registration_password_required")
    )) !!}
</div>
<div class="form-group">
    <label class="col-md-3">{{ trans('common.user_registration_label_email') }}</label>
    {!! Form::email('email', null, array(
            'maxlength' => 50,
            'id' => 'txtEmail',
            'class' => 'form-control custom-input validate[required,maxSize[50],custom[email]]',
            'data-errormessage-range-overflow' =>  trans("common.user_registration_email_max"),
            'data-errormessage-custom-error' =>  trans("common.user_registration_email_email"),
            'data-errormessage-value-missing' =>  trans("common.user_registration_email_required")
    )) !!}
</div>
@if ($userGroups)
<div class="form-group">
    <label class="col-md-3">{{ trans('common.user_registration_label_usergroup') }}</label>
    {!! Form::select('user_group_id', $userGroups, null, array(
             'id' => 'UserGroupId',
             'class' => 'form-control custom-input validate[required]',
             'data-errormessage-value-missing' =>  trans("common.user_registration_usergroup_required")
    )) !!}
</div>
@endif
<div class="form-group">
    {!! Form::checkbox('user_locked_flg', null, null) !!}
    <label class="col-md-3">{{ trans('common.user_edit_label_user_lock') }}</label>
</div>
<hr>
{!! Form::button(trans('common.user_registration_label_button_save'), array(
           'class' => 'btn-save-user button-submit',
           'type' => 'submit'
)) !!}
{!! Form::button(trans('common.user_registration_label_button_cancel'), array(
           'class' => 'button-submit btn-cancel-popup',
           'type' => 'button',
           'style'=>'margin:8px 0 0 0;'
)) !!}