@extends('popup')
@section('content')
{!! Form::open(array('route' => 'fertilizer.validate.specification','method' => 'post','name' => 'specification-frm','class' => 'ewewewe form-horizontal frm-validation-create-map')) !!}
<div class="modal-body">
    <div class="col-md-12">
     <h2 class="page-header">{{ trans('common.create_fertilizer_caption') }}</h2>
    <div class="form-group">
    <label class="col-md-6 col-lg-5 col-lg-offset-1">{{ trans('common.create_fertilizer_select_map') }}</label>
        <div class="col-md-6 col-lg-6">
        {!! Form::select('layer_id', $maps, null, array(
                 'id' => 'layer-id',
                 'class' => 'form-control validate[required] layer-id mode_selection',
            'data-prompt-position' => 'topRight:-150',
                 'data-errormessage-value-missing' =>  trans("common.create_fertilizer_map_required")
        )) !!}
        </div>
    </div>
    <div class="form-group">
    <label class="col-md-6 col-lg-5 col-lg-offset-1">{{ trans('common.create_fertilizer_select_crop') }}</label>
        <div class=" col-md-6 col-lg-6">
        {!! Form::select('crop_id', $crops, null, array(
                 'id' => 'crop-id',
                 'class' => 'form-control crop_id validate[required] mode_selection',
            'data-prompt-position' => 'topRight:-150',
                 'data-errormessage-value-missing' =>  trans("common.create_fertilizer_map_crop_required")
        )) !!}
        </div>
    </div>
    <div class="form-group">
    <label class="col-md-6 col-lg-5 col-lg-offset-1">{{ trans('common.create_fertilizer_select_mode') }}</label>
        <div class="col-md-6 col-lg-6">
        {!! Form::select('mode_type', $modes, null, array(
                 'id' => 'mode_type',
                 'class' => 'form-control mode_type validate[required] mode_selection',
            'data-prompt-position' => 'topRight:-150',
                 'data-errormessage-value-missing' =>  trans("common.create_fertilizer_map_mode_required")
        )) !!}
        </div>
    </div>
    <hr>
        <div class="pull-right">
            {!! Form::button(
                          trans('common.confirm_ok'),
                          array(
                              'class' => 'btn-create-map button-submit',
                              'type' => 'button'
                          )
                      )
                  !!}
            {!! Form::button(
                    trans('common.create_fertilizer_btn_cancel'),
                    array(
                        'id' => 'btn-cancel',
                        'class' => 'button-submit cancel-specification-map',
                        'type' => 'button',
                        'style'=>'margin:8px 0 0 0;'
                    )
                )
            !!}
        </div>

    </div>
</div>
    {!! Form::close() !!}

@endsection
<script>
    setTimeout(function(){
        $('.layer-id').focus();
    },0);
    controlTabC('layer-id','cancel-specification-map');
        $(document).ready(function(){
            $('#layer-id').children('option').each(function(index,val){
                var currentVal = $(val).val();
                var arrayList = currentVal.split('_');
                if(arrayList.length > 0){
                    if(arrayList[1] == gisMap.layerID){
                        $('#layer-id').val(currentVal);
                        return true;
                    }
                }
            });

        });
</script>
