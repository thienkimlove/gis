@extends('popup')

@section('content')
<div class="row">
    <h2 class="page-header">{{trans('common.standardcropcopying_title_h')}}</h2>
    {!! Form::open(array('route' => 'save-copy-system-fertilizer','method' => 'post','name' => 'frm-copy-system-fertilizer','class' => 'form-horizontal frm-validation frm-copy-system-fertilizer')) !!}
    <input type="hidden" id="overWrite" name="overWrite" value=0 />
    <span class="col-xs-5 " style="padding-top: 0;">{{trans('common.system_standardcrop_title_copy')}}</span><br>
    <div class="form-group">
        <span class="col-xs-5 control-label" style="padding-top: 0;">{{trans('common.system_standardcrop_title')}}</span>
        <span class="col-xs-5 ">{{$fertilizer_get->fertilization_standard_name}}</span>
    </div>
    <div class="form-group">
            <span class="col-xs-5 control-label">{{ trans('common.system_standardcrop_crop') }}</span>
            <div class="col-xs-5">
                {!! Form::select('crops_id', $crops,null,array('class'=>'form-control crops_id input-md validate[required]',
                'data-prompt-position' => 'topRight:-100',
                'data-errormessage-value-missing' =>  trans('common.standardcropinfo_crop_required')
                )) !!}
            </div>
        </div>
    <span class="col-xs-5" style="padding-top: 0;">{{trans('common.system_standardcrop_title_copy')}}</span><br>
    <div class="form-group">
        <span class="col-xs-5 control-label">{{trans('common.user_standardcrop_title')}}</span>
        <div class="col-xs-5">
            {!! Form::select('fertilizer_id', $fertilizers,null,array('id'=>'fertilizer_id','class'=>'form-control input-md validate[required]',
            'data-prompt-position' => 'topRight:-200',
            'data-errormessage-value-missing' =>  trans('common.standardcropinfo_crop_required')
            )) !!}
        </div>
    </div>
    <input type="hidden" id="fertilizer_get" name="fertilizer_get" value={{$fertilizer_get->id}} />
    <div class="form-group ">
      <span class="col-xs-5 control-label " for="textinput">{{trans('common.system_standardcrop_title_N')}}</span>
      <div class="col-xs-2">
      <input id="inputN" name="inputN" type="text" maxLength='2' class="form-control smallint input-md validate[required,min[1],max[30]]"
             data-prompt-position="topRight"
             data-errormessage-value-missing = "{{trans('common.required')}}"
             data-errormessage-range-underflow="{{trans('common.fertilizer_copy_ranger_error_n')}}"
             data-errormessage-range-overflow="{{trans('common.fertilizer_copy_ranger_error_n')}}">
      </div>
      <span>{{trans('common.system_standardcrop_title_unit')}}</span>
    </div>

    <div class="form-group ">
      <span class="col-xs-5 control-label " for="textinput">{{trans('common.system_standardcrop_title_P')}}</span>
      <div class="col-xs-2">
      <input id="inputP" name="inputP" type="text" maxLength='2' class="form-control smallint input-md validate[required,min[1],max[60]]"
             data-prompt-position="topRight"
             data-errormessage-value-missing = "{{trans('common.required')}}"
             data-errormessage-range-underflow="{{trans('common.fertilizer_copy_ranger_error_p')}}"
      data-errormessage-range-overflow="{{trans('common.fertilizer_copy_ranger_error_p')}}">
      </div>
      <span>{{trans('common.system_standardcrop_title_unit')}}</span>
    </div>

    <div class="form-group ">
      <span class="col-xs-5 control-label " for="textinput">{{trans('common.system_standardcrop_title_K')}}</span>
      <div class="col-xs-2">
      <input id="inputK" name="inputK" type="text" maxLength='2' class="form-control smallint input-md validate[required,min[1],max[30]]"
             data-prompt-position="topRight"
             data-errormessage-value-missing = "{{trans('common.required')}}"
             data-errormessage-range-underflow="{{trans('common.fertilizer_copy_ranger_error_k')}}"
      data-errormessage-range-overflow="{{trans('common.fertilizer_copy_ranger_error_k')}}">
      </div>
      <span>{{trans('common.system_standardcrop_title_unit')}}</span>
    </div>
    {!!Form::close()!!}
    <hr>
    <div>
    <label class="fetilizer-label col-xs-5 col-xs-offset-2"></label>
    		{!! Form::button(trans('common.button_save'),array('class' => 'button-submit btn-copy-system-fertilizer','type' => 'button')) !!}
    		{!! Form::button(trans('common.button_cancel'),array('class' => ' button-submit btn-cancel-popup','type' => 'reset','style'=>'margin:8px 0 0 0;')) !!}

    </div>
</div>
@endsection
@section('footer')
	<script>
        setTimeout(function() {
            $(".crops_id").focus();
        }, 0);
        controlTabC('crops_id','btn-cancel-popup');
    $(".btn-copy-system-fertilizer").on("click",function(event){
        var form = $('.frm-copy-system-fertilizer');
        gisForm.clickSave(event,{
           formEle : form,
           callbackFunction : function(data){
                if(data.code === 1) {
                    fancyMessage(data.message, window.info_title,function(){
                    closeFancy();
                    fertilizer.refresh();
                    });
                }
                else if(data.code==409){
                    showConfirm(data.message, window.error_title,function(){
                        $('#overWrite').val(1);
                        gisForm.clickSave(event,{
                           formEle : form,
                           callbackFunction : function(data){
                               if(data.code === 1) {
                                   fancyMessage(data.message, window.info_title,function(){
                                   closeFancy();
                                   fertilizer.refresh();
                                   });
                               }
                           }
                        });
                    });
                }
           }
        });
    })
	</script>
@endsection