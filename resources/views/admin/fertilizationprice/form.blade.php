<div class="form-group">
    {!! Form::label('price', trans('common.fertilization_price_label_price'),['class' => 'col-md-12']) !!}
    <div class="col-md-12">
        {!! Form::text('price', null, [
        'maxlength' => 20,
        'data-prompt-position' => 'topRight:-100',
        'class' => 'form-control txt-group-name custom-input validate[required, maxSize[20], custom[integer], max[99999], min[1]]',
        'data-errormessage-value-missing' =>  trans("common.fertilization_unit_price_required"),
        'data-errormessage-range-overflow' =>  trans("common.fertilization_unit_price_max"),
        'data-errormessage-custom-error' => trans("common.fertilization_unit_price_integer_required"),
        'data-errormessage-range-overflow' => trans('common.over_range_unit_price_max'),
        'data-errormessage-range-underflow' => trans('common.over_range_unit_price_min')
        ]) !!}
    </div>

</div>

<div class="form-group">
    {!! Form::label('start-date', trans('common.fertilization_price_label_start_date'),['class' => 'col-md-12']) !!}
    <div class="col-md-12">
        {!! Form::text('start_date', null, [
        'data-prompt-position' => 'topRight:-100',
        'class' => 'form-control custom-input validate[required,custom[date]]',
        'data-errormessage-value-missing' =>  trans("common.fertilization_unit_price_start_date_required"),
        'data-errormessage-custom-error' =>  trans("common.fertilization_unit_price_date_required"),
        'id' => 'start-date'
        ])
        !!}
    </div>

</div>
<div class="form-group">
    {!! Form::label('end-date', trans('common.fertilization_price_label_end_date'),['class' => 'col-md-12']) !!}
    <div class="col-md-12">
        {!! Form::text('end_date', null, [
        'data-prompt-position' => 'topRight:-100',
        'class' => 'form-control custom-input validate[custom[date], future[#start-date]]',
        'data-errormessage-custom-error' =>  trans("common.fertilization_unit_price_date_required"),
        'data-errormessage-type-mismatch' => trans("common.fertilization_unit_price_end_date_condition"),
        'id' => 'end-date'
        ])
        !!}
    </div>

</div>
<hr>
{!! Form::button($submitText, array(
'class' => 'btn-save-price button-submit',
'type' => 'submit'
)) !!}
{!! Form::button($closeText, array(
'class' => 'button-submit btn-cancel-popup',
'type' => 'button'
)) !!}

