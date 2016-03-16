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
    <div class="form-group1 changingcolor">
        {!! Form::open(array('route' => 'submit-changing-color','method' => 'post','class' => 'changing-color-frm frm-validation')) !!}
        <div id="data-input" class="col-md-12">
            <div class="row">
                <h2 class="text-left page-header" style="margin-left: 14px;margin-right: 14px;">{{ trans('common.changingcolor_title') }}</h2>
                <p class="text-left"  style="margin-left: 14px;margin-right: 14px;">{{ trans('common.changingcolor_fertilizer_amount') }}</p>

                <input type="hidden" id="hidden_list_colors" name="list_colors">
                <input type="hidden" id="hidden_current_colors" name="current_colors">
                <input type="hidden" id="hidden_update_colors" name="update_colors">
                <input type="hidden" id="hidden_is_one_barrel" name="is_one_barrel" value="{{$isOneBarrel}}">
            </div>

            <!-- Begin column 1 -->
            <div class="col-md-14">
                <fieldset class="col-md-12 panel panel-group-color"  style="padding-left: 0;">
                    <div id="colorList" class="color-area">
                        <ol class="col-md-12 col-lg-12">
                            @foreach ($colors as $key =>$value)

                                <li id="color_{{$key}}" ondblclick="changingcolor.openValueChangingColor();" color="{{$key}}" subvalue='{{$value[1].','.$value[2]}}'>
                                    <div class="color-row">
                                        <div class="color-box" style="background-color: {{'#'.$value[0]}};"></div>
                                        @if(!$isOneBarrel)
                                        <div class="color-name">{{trans('common.changingcolor_main_barrel').' '.$value[1].'kg/10a,'.trans('common.changingcolor_sub_barrel').' '.$value[2].'kg/10a'}} </div>
                                        @else
                                            <div class="color-name">{{$value[1].'kg/10a'}}</div>
                                        @endif
                                    </div>
                                </li>

                            @endforeach
                        </ol>

                    </div>

                    <div class="panel-body">
                    </div>
                </fieldset>
            </div>
            <!-- End column 1 -->

        </div>
        <div class="col-md-12" onclick="">
            <div class="confirm-button-group">
                <hr style="clear: left;">
                <span><button type="button" class="button-submit" id="submit-changing-color"
                              onclick="changingcolor.openValueChangingColor()">{{trans('common.button_alert_ok')}}</button></span>
                <button type="button" class="button-submit"
                        onclick="closeMergingMap();">{{trans('common.button_cancel')}}</button>
            </div>
        </div>
        {!! Form::close() !!}

    </div>
@endsection



