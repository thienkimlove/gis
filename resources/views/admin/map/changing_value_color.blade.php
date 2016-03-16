@extends('popup')
@section('content')
    <style>
        .fancybox-opened {
            width: 35% !important;
            min-width: 400px;
            height: 500px !important;
        }

        #page-wrapper {
            min-height: 320px !important;
        }

        #search_suggestion_holder_main {
            display: none;
        }
        .page-header{
            margin: 10px 0 10px;
        }
        .button-submit{
            margin: 0;
        }

    </style>
    <div class="form-group1 open-changing-color">
        {!! Form::open(array('route' => 'submit-changing-color','method' => 'post','class' => 'changing-color-frm frm-validation','onsubmit="return false;"')) !!}
        <div id="data-input" class="col-md-12">
            <div class="row">
                <h2 class="page-header" style="margin-left: 14px;margin-right: 14px;">{{ trans('common.changingcolor_title') }}</h2>
                <input type="hidden" id="CurrentColors" name="CurrentColors" value="{{$colors->id}}">
                <input type="hidden" id="IsOnebarrel" name="IsOnebarrel" value="{{$isOneBarrel}}">
                <input type="hidden" id="colorCode" name="ColorCode" value="{{$rgb}}">
                <input type="hidden" id="layerID" name="layerID" value="{{$layerID}}">
                <input type="hidden" id="hidden_update_colors" name="update_colors">
            </div>

            <!-- Begin column 0 -->
            <div class="panel panel-default">
                <div class="panel-body">

                        <div class="form-group col-md-2">
                            <ul class="list-unstyled">
                                <li><label for="" class="control-label"></label>
                                </li>
                                <li>  <div id="editingcolor-box" onclick="changingcolor.colorPickColor(this);"
                                           class="editingcolor-box" style="background:rgb({{$rgb}})">
                                    </div>
                                </li>
                            </ul>

                        </div>
                        @if($isOneBarrel)
                            <div class="form-group col-md-4">
                                <ul class="list-unstyled">
                                    <li>
                                        <label for="" class="control-label">{{trans('common.changingcolor_main_barrel')}}</label>
                                    </li>
                                    <li>
                                        {!! Form::text('main_barrel',round($colors->main_fertilizer), array('id' => 'main_barrel', 'maxlength'=>'4', 'class' => 'form-control smallint validate[required]  maxSize[4] ',
                                       'data-errormessage-value-missing' =>  trans("common.mergingcolor_message_mainsub_required")
                                   )) !!}
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="form-group  col-md-4">
                                <ul class="list-unstyled">
                                    <li>
                                        <label for=""  class="control-label">{{trans('common.changingcolor_main_barrel')}}</label>
                                    </li>
                                    <li>
                                        {!! Form::text('main_barrel',round($colors->main_fertilizer), array('id' => 'main_barrel', 'maxlength'=>'4', 'class' => 'form-control smallint validate[required]  maxSize[4] ',
                                       'data-errormessage-value-missing' =>  trans("common.mergingcolor_message_mainsub_required")
                                   )) !!}<span>{{trans('common.editingcolor_unit')}}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="form-group  col-md-4">
                                <ul class="list-unstyled">
                                    <li><label for=""
                                               class=" control-label">{{trans('common.changingcolor_sub_barrel')}}</label>
                                    </li>
                                    <li>
                                        {!! Form::text('sub_barrel',round($colors->sub_fertilizer), array('id' => 'sub_barrel', 'maxlength'=>'4', 'class' => 'form-control smallint validate[required]  maxSize[4] ',
                                      'data-errormessage-value-missing' =>  trans("common.mergingcolor_message_mainsub_required")
                                  )) !!}<span>{{trans('common.editingcolor_unit')}}</span>
                                    </li>
                                </ul>
                            </div>
                        @endif

                </div>
            </div>

        </div>
        <div class="col-md-12" onclick="">
            <div class="confirm-button-group text-center">
                <hr style="clear: left;">
                <button type="button" class="button-submit" id="submit-changing-color"
                              onclick="changingcolor.submitChangingColor()">{{trans('common.button_alert_ok')}}</button>
                <button type="button" class="button-submit"
                        onclick="changingcolor.openChangingColor();">{{trans('common.button_cancel')}}</button>
            </div>
        </div>
        {!! Form::close() !!}

    </div>
@endsection



