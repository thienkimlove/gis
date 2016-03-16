@extends('popup')
@section('content')
    <style>
        .fancybox-opened {
            width: 30% !important;
            min-width: 300px;
            height: 500px !important;
        }

        #page-wrapper {
            min-height: 200px !important;
        }

        #search_suggestion_holder_main {
            display: none;
        }

        .colorpicker-visible {
            z-index: 9999;
        }
    </style>
    <div class="form-group1 editingcolor" onclick="changingcolor.hideListColors();">

        {!! Form::open(array('route' => 'submit-editing-color','method' => 'post','class' => 'form-horizontal editing-color-frm')) !!}
        <div id="data-input" class="col-md-12">
            <div class="row">
                <br/>
                <h4 class="changingcolor-header text-center">{{ trans('common.editingcolor_title') }}</h4>
                <input id="fertilizerId" name="fertilizerId" type="hidden" value="{{$fertilizerId}}">
                <input id="colorIds" name="colorIds" type="hidden" value="{{$colorIds}}">
                <input id="colorSelectIds" name="colorSelectIds" type="hidden" value="{{$mapInfoIds}}">
                <input type="hidden" id="hidden_is_one_barrel" name="is_one_barrel" value="{{$isOneBarrel}}">
                <input id="color_code" name="color_code" type="hidden"
                       value="{{$selectedColor->r.','.$selectedColor->g.','.$selectedColor->b}}">
                <input type="hidden" id="hidden_update_colors" name="update_colors">

            </div>
            <!-- Begin column 1 -->
            <div class="col-md-12 form-header-column">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group ">

                            <label class="specifyuser-label col-md-4">{{ trans('common.changingcolor_main_barrel') }}</label>

                            <div class="col-md-4" style="padding-right: 0; margin-right: 14px;">
                                {!! Form::text('main_barrel',$selectedColor->main_fertilizer, array('id' => 'specifyuser_user_code', 'maxlength'=>'4', 'class' => 'form-control smallint validate[required]  maxSize[4] ',
                                    'data-errormessage-value-missing' =>  trans("common.fertilizer_info_fertilization_standard_name_required")
                                )) !!}
                            </div>
                            <label class="">{{ trans('common.editingcolor_unit') }}</label>
                        </div>

                        <div class="form-group">


                            @if($isOneBarrel)
                                {!! Form::hidden('sub_barrel','', array('disabled','id' => 'specifyuser_user_name_disable', 'maxlength'=>'20', 'class' => 'form-control smallint',
                                       )) !!}
                            @else
                                <label class="specifyuser-label col-md-4">{{ trans('common.changingcolor_sub_barrel') }}</label>
                                <div class="col-md-4" style="padding-right: 0; margin-right: 14px;">
                                    {!! Form::text('sub_barrel',$selectedColor->sub_fertilizer, array('id' => 'specifyuser_user_name', 'maxlength'=>'4', 'class' => 'form-control smallint validate[required]  maxSize[4] ',
                                 'data-errormessage-value-missing' =>  trans("common.fertilizer_info_fertilization_standard_name_required")
                                    )) !!}
                                </div>
                                <label class="">{{ trans('common.editingcolor_unit') }}</label>

                            @endif

                        </div>

                        <div class="form-group">
                            <label class="specifyuser-label col-md-4">{{ trans('common.editingcolor_color') }}</label>

                            <div class="col-md-4" style="padding-right: 0; margin-right: 14px;">
                                <div id="editingcolor-box" onclick="changingcolor.colorPickColor(this);"
                                     class="editingcolor-box" color="01"
                                     style="background-color: {{'rgb('.$selectedColor->r.', '.$selectedColor->g.', '.$selectedColor->b.');'}}"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- 	End column 1 -->
        </div>

        {!! Form::close() !!}
        <div class="col-md-12 editingcollor-button-group">
            <div class="confirm-button-group">
                <span><button type="button" class="button-submit"
                              onclick="changingcolor.submitEditingColor();">{{trans('common.button_alert_ok')}}</button></span>
                <button type="button" class="button-submit"
                        onclick="closeFancy();">{{trans('common.button_cancel')}}</button>
            </div>
        </div>
    </div>

@endsection



