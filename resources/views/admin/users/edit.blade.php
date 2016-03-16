@extends('popup')
@section('content')
    {!! Form::open(array('route' => ['admin.users.update',$user->id],'method' => 'put','class' => 'form-horizontal frm-validation-login')) !!}
    {!! Form::hidden('id', $user->id, array('id' => 'id')) !!}
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-header">{{trans('common.user_edit_list_title')}}</h2>

                <div class="form-group">
                    <label class="col-md-12">{{ trans('common.user_registration_label_username') }}</label>

                    <div class="col-md-12">
                        {!! Form::text('username',$user->username,array('maxlength' => 20,'id' => 'txtUserName','data-prompt-position' => 'topRight:-200','class' => 'form-control custom-input validate[required,maxSize[20],custom[onlyLetterNumber]]','data-errormessage-range-overflow' =>  trans("common.user_registration_username_max"),'data-errormessage-custom-error' =>  trans("common.user_registration_username_alpha_num"),'data-errormessage-value-missing' =>  trans("common.user_registration_username_max")  )) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-12">{{ trans('common.user_registration_label_usercode') }} :</label>

                    <div class="col-md-12">
                        {!! Form::text('usercode',$user->user_code,array('id' => 'usercode','class' => 'form-control custom-input','readonly'=> true )) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-12">{{ trans('common.user_registration_label_password') }}</label>

                    <div class="col-md-12">
                        {!! Form::password('password',array('maxlength' => 30,'minlength' => 8,'id' => 'password','data-prompt-position' => 'topRight:-200','class' => "form-control custom-input validate[maxSize[30],minSize[8],custom[onlyLetterNumber]],funcCall[notEquals]",'data-errormessage-range-underflow' =>  trans("common.user_registration_password_min"),'data-errormessage-range-overflow' =>  trans("common.user_registration_password_max"),'data-errormessage-custom-error' =>  trans("common.user_registration_password_alpha_num")  )) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-12">{{ trans('common.user_registration_label_email') }}</label>

                    <div class="col-md-12">
                        {!! Form::email('email',$user->email,array('maxlength' => 50,'id' => 'txtEmail','data-prompt-position' => 'topRight:-200','class' => 'form-control custom-input validate[required,maxSize[50],custom[email]]','data-errormessage-range-overflow' =>  trans("common.user_registration_email_max"),'data-errormessage-custom-error' =>  trans("common.user_registration_email_email"),'data-errormessage-value-missing' =>  trans("common.user_registration_email_required")  )) !!}
                    </div>
                </div>
                @if($userGroups)
                    <div class="form-group">
                        <label class="col-md-12">{{ trans('common.user_registration_label_usergroup') }}</label>

                        <div class="col-md-12">
                            {!! Form::select('user_group_id',$userGroups,$user->user_group_id,array('id' => 'UserGroupId','class' => 'form-control custom-input' )) !!}
                        </div>
                    </div>
                @endif

                <div class="form-group">

                    <label class="col-md-3">{{ trans('common.user_edit_label_user_lock') }}</label>
                    {!! Form::checkbox('user_locked_flg', true, $user->user_locked_flg) !!}
                </div>

                <div class="form-group">
                    <label class="col-md-12">{{ trans('common.user_registration_label_system_number') }}</label>

                    <div class="col-md-12">
                        {!! Form::select('system_number',$systemNumbers,$user->system_number,array('id' => 'system-number','class' => 'form-control custom-input' )) !!}
                    </div>
                </div>

                <hr>
                {!! Form::button(trans('common.user_registration_label_button_save'),array('class' => 'btn-save-user button-submit','type' => 'submit')) !!}
                {!! Form::button(trans('common.user_registration_label_button_cancel'),array('id' => 'btn-cancel','class' => 'button-submit btn-cancel-popup','type' => 'button')) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('footer')
    <script src="{{url('/js/modules/user_edit.js')}}"></script>
    <script>
        controlTab('txtUserName', 'btn-cancel');
    </script>
@endsection