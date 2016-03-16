<div class="form-horizontal">
    <div class="form-group">
    <label class="col-md-12 ">{{ trans('common.helplink_label_page_address_url') }}</label>
        <div class="col-md-12">
        {!! Form::text('address', null, array(
            'maxlength'=>'200',
            'placeholder' => '*',
            'data-prompt-position' => 'topRight:-100',
            'class' => 'form-control txt-helplink input-md custom-input validate[required,custom[url],maxSize[300]]',
            'data-errormessage-value-missing' => trans("common.helplink_required_page_address_url_field"),
            'data-errormessage-range-overflow' => trans("common.helplink_check_max_field"),
            'data-errormessage-custom-error' => trans("common.helplink_check_valid_url")
        )) !!}
        </div>
    </div>
    <div class="form-group">
    <label class="col-md-12 ">{{ trans('common.helplink_help_destination') }}</label>
        <div class="col-md-12">
        {!! Form::text('help', null, array(
            'maxlength'=>'200',
            'placeholder' => '*',
            'data-prompt-position' => 'topRight:-100',
            'class' => 'form-control input-md custom-input validate[required,maxSize[200]]',
            'data-errormessage-range-overflow' => trans("common.helplink_check_max_field"),
            'data-errormessage-value-missing' => trans("common.helplink_required_help_destination_field")
        )) !!}
        </div>
    </div>
    <div class="form-group">
    <label class="col-md-12 ">{{ trans('common.helplink_popup_header') }}</label>
    <?php $popupArr=array(
        0=>"",
        1=>trans('common.help_popup_1'),
        2=>trans('common.help_popup_2'),
        3=>trans('common.help_popup_3'),
        4=>trans('common.help_popup_4'),
        );
    ?>
        <div class="col-md-12 ">
            {!! Form::select('popup_screen',$popupArr, null, array(
            'id' => 'popup-screen',
            'data-prompt-position' => 'topRight:-100',
            'class' => 'form-control custom-input validate[required]',
            'style' => 'border-radius: 4px;',
            'data-errormessage-value-missing' =>  trans("common.user_registration_usergroup_required")
            )) !!}
        </div>

    </div>
</div>
<hr>
<div class="form-group">
<div class="col-md-10">
{!! Form::button(trans('common.button_save'),array(
    'class' => 'button-submit btn-edit-help-link',
    'type' => 'submit'
    )) !!}
    {!! Form::button(trans('common.button_cancel'), array(
    'class' => 'button-submit btnCancelHelplink',
    'type' => 'button'
)) !!}
</div>
</div>