<div class="form-group">
    {!! Form::label('group_name', trans('common.usergroup_label_name'), ['class' => 'col-md-12']) !!}
    <div class="col-md-12">
        {!! Form::text('group_name', null, [
        'maxlength' => 20,
        'data-prompt-position' => 'topRight:-200,13',
        'class' => 'form-control txt-group-name custom-input validate[required, maxSize[20]]',
        'data-errormessage-value-missing' =>  trans("common.usergroup_group_name_required"),
        'data-errormessage-range-overflow' =>  trans("common.usergroup_group_name_max"),
        ]) !!}
    </div>
</div>

<div class="form-group">
     {!! Form::label('description', trans('common.usergroup_label_desc'), ['class' => 'col-md-12']) !!}
    <div class="col-md-12">
     {!! Form::text('description', null, [
         'maxlength' => 30,
        'data-prompt-position' => 'topRight:-200,0',
         'class' => 'form-control custom-input validate[required,maxSize[30]]',
         'data-errormessage-value-missing' =>  trans("common.usergroup_description_required"),
         'data-errormessage-range-overflow' =>  trans("common.usergroup_description_max"),
    ]) !!}
        </div>
</div>
<div class="form-group">
    {!! Form::label('is_guest_group', trans('common.usergroup_label_is_guest_group'), ['class' => 'col-md-3']) !!}
    @if (isset($group->is_guest_group) && $group->is_guest_group)
        {!! Form::checkbox('is_guest_group', null, null,  array('disabled' => true)) !!}
    @else
        {!! Form::checkbox('is_guest_group', null, null) !!}
    @endif
</div>
<hr style="margin-top: 16px;">
         {!! Form::button($submitText, array(
             'class' => 'btn-save-usergroup button-submit',
             'type' => 'submit'
         )) !!}
         {!! Form::button($closeText, array(
             'class' => 'button-submit btn-cancel-popup',
             'type' => 'button'
     )) !!}

