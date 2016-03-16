@extends('popup')

@section('content')
    <style>
        .fancybox-wrap {
            width: 70% !important;
            top: 40px !important;
            min-width:950px;
            min-height: 700px;
            height: 700px!important;
        }
        .fancybox-inner{
            height: 700px!important;
        }
        .fancybox-inner {
            overflow-x: hidden !important;
        }
        .custom-header-text{
            z-index: 0;
            opacity: 0.5;
            position: relative;
        }
        .fixed-panel-custom{
            min-height: 300px;
            overflow-y: scroll;
        }
        .fixed-panel-map{
            min-height: 500px;
            overflow-y: scroll;
        }
        .custom1{
            width: 30%;
        }
        .custom2{
            width: 20%;
        }
        #legendPopup{
          position: absolute;
          top: 270px;
          right: 22px;
          background-color: #fff;
        }
        .panel .panel-primary{
          min-height: 200px!important;
        }
        #typeDrawPopup{
          position: absolute;
          top: 103px;
          right: 16px;
            margin-top: 30px;
        }
    </style>
    <div id="dialog1"></div>
    <div class="row" style="height:75%;margin-left: 5px;margin-right: 0;">
        <div style="padding-right:10px;padding-left: 7px;">
            <div class="panel panel-primary" style="">
                <div class="panel-heading">
                    <h2 class="panel-title" style="font-size: 30px !important;">{{ trans('common.label_fertilization_out_prediction') }}</h2>
                </div>
                <div class="panel-body">
                    <a style="float: right" id="helpLink" href="javascript:void(0)" onclick="getHelp(4);">{{ trans("common.help_label") }}</a>
                    <form class="form-fertilization-predict">
                    <div class="form-group" style="float: left;">

                        <div class="col-md-4" style="float: left;">
                            <label class="control-label" for="textinput" style="">{{ trans('common.label_fertilization_predict_spread_width') }}</label>
                            <input id="spread-width" name="spread-width" type="text" style="margin-bottom: 10px" autofocus
                                   class="form-control spread-width input-md custom1 validate[required, custom[integer], max[100], min[1]]"
                                   data-prompt-position="bottomRight:0,-5"
                                   data-errormessage-value-missing="{{ trans('common.fertilization_predict_spread_width_required') }}"
                                   data-errormessage-custom-error="{{ trans('common.integer_number_validate') }}"
                                   data-errormessage-range-overflow="{{trans('common.fertilization_predict_spread_width_over_range_max')}}"
                                   data-errormessage-range-underflow="{{trans('common.fertilization_predict_over_range_min')}}">

                        </div>
                        <div class="col-md-6" style="float: left;">
                            <label class="control-label" for="textinput">{{ trans('common.label_fertilization_predict_main_tank') }}</label>
                            <input id="main-tank" name="main-tank" type="text" placeholder="" style="margin-bottom: 10px"
                                   class="form-control main-tank input-md custom2 validate[required, custom[integer], max[10000], min[100]]"
                                   data-prompt-position="bottomRight:0,-5"
                                   data-errormessage-value-missing="{{ trans('common.fertilization_predict_main_tank_required') }}"
                                   data-errormessage-custom-error="{{ trans('common.integer_number_validate') }}"
                                   data-errormessage-range-overflow="{{trans('common.fertilization_predict_main_tank_over_range_max')}}"
                                   data-errormessage-range-underflow="{{trans('common.fertilization_predict_main_tank_over_range_min')}}">
                        </div>

                        <div class="col-md-4" style="float: left;clear: left;margin-bottom: 30px;">
                            <label class="control-label" for="textinput">{{ trans('common.label_fertilization_predict_hojo_width') }}</label>
                            <input id="hojo-width" name="hojo-width"  type="text" style="margin-bottom: 10px"
                                   class="form-control hojo-width input-md custom1 validate[required, custom[integer], max[500], min[1]]"
                                   data-prompt-position="bottomRight:0,-5"
                                   data-errormessage-value-missing="{{ trans('common.fertilization_predict_hojo_width_required') }}"
                                   data-errormessage-custom-error="{{ trans('common.integer_number_validate') }}"
                                   data-errormessage-range-overflow="{{trans('common.fertilization_predict_hojo_width_over_range_max')}}"
                                   data-errormessage-range-underflow="{{trans('common.fertilization_predict_over_range_min')}}">

                        </div>
                        <!-- Hide it if it's one barrel -->
                        @if($fertilizingMachineType == 2)
                        <div class="col-md-6" style="float: left;">
                            <label class="control-label" style="" for="textinput">{{ trans('common.label_fertilization_predict_sub_tank') }}</label>
                            <input id="sub-tank" name="sub-tank" type="text" style="margin-bottom: 10px;margin-left: 13px;"
                                   class="form-control sub-tank input-md custom2 validate[required, custom[integer], max[1000], min[0]]"
                                   data-prompt-position="bottomRight:0,-5"
                                   data-errormessage-value-missing="{{ trans('common.fertilization_predict_sub_tank_required') }}"
                                   data-errormessage-custom-error="{{ trans('common.integer_number_validate') }}"
                                   data-errormessage-range-overflow="{{trans('common.fertilization_predict_sub_tank_over_range_max')}}"
                                   data-errormessage-range-underflow="{{trans('common.fertilization_predict_over_range_min')}}">
                        </div>
                        @endif
                    </div>
                    </form>
                    <hr style="margin-top: 60px;margin-bottom: 10px;clear: left;">
                    <div class="panel panel-primary" style="height: 75%;clear: left;">
                        <div id='typeDrawPopup' style="display:none; float:right;">
                            <button class="button-submit" value="LineString" name="type">{{ trans('common.predict_out_radio_draw_line') }}</button>
                            <button class="button-submit" value="Drag" name="type">{{ trans('common.predict_out_radio_drag') }}</button>
                            <button class="button-submit" value="Point" name="type">{{ trans('common.predict_out_radio_start_point') }}</button>


                    <button id="clearPopup" class="button-submit" style="margin-right: 40px;">{{trans('common.main_index_button_clean')}}</button>
                            <button class="button-submit btn-cancel-popup" style="float:right;margin: 8px;margin-top: 0;">{{ trans('common.usergroup_add_close_title') }}</button>
                            </span>
                            <select name="controlToolBar" id="controlToolBarPopup" multiple="multiple" style="float:right;display:none">
                                <option value="0">{{trans('common.option_scaleline')}}</option>
                                <option value="1">{{trans('common.option_zoom')}}</option>
                                <option value="4">{{trans('common.option_legend')}}</option>
                            </select>
                        </div>

                        <div class="panel-body "
                             style="width: 100%;padding-top: 0px;padding-left: 0px;padding-bottom: 0px;padding-right: 0px;">
                            <div id="mapPrediction" class="mapPrediction"></div>
                        </div>
                        <div>
                            <img id="legendPopup" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
    <script>
        controlTabC("spread-width","btn-out-predict");
        controlTabC("btn-cancel-popup","sub-tank");
        $(".numeric-only").keydown(function(event) {
            // Allow only backspace and delete
            if ( event.keyCode == 46 || event.keyCode == 8 ) {
                // let it happen, don't do anything
            }
            else {
                // Ensure that it is a number and stop the keypress
                if (event.keyCode < 48 || event.keyCode > 57 ) {
                    event.preventDefault();
                }
            }
        });
        $(function(){
            gisMap.loadMap({{$layer_id}},'layer_fertilizer',null,true);
            $('.btn-cancel-popup').click(function(event){
                event.preventDefault();
                $.fancybox.close(true);
            });

        });
    </script>
@endsection