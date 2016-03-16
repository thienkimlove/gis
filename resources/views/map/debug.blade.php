@extends('admin')


@section('content')
    <link href="{{ asset('/themes/default/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/ol.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/bootstrap-multiselect.css')}}" type="text/css"/>
    <link href="{{ url('/css/tooltip.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/creatingmap.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/changingcolor.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/js/libs/colorpicker/css/bootstrap-colorpicker.min.css') }}" rel="stylesheet" type="text/css">

    <style>
        .ol-scale-line, .ol-scale-line:not([ie8andbelow]) {
            background: none;
            padding: 5px;
        }
        .ol-scale-line-inner{
            color: #000;
            border: 2px solid #000;
            border-top: none;
            font-weight: bold;
            font-size:13px;
        }
        #ratio-scale{
            position:absolute;
            font-size:13px;
            bottom:5px;
            color:#000;
            right:-62%;
            font-weight:bold
        }
        .ol-full-screen{
            top:0 !important;
        }
        .ol-mouse-position{
            top:0px !important
        }
        .ol-rotate{
            top:34px  !important;
        }
        a[tabindex="0"]{
            background-color:#fff !important;
        }
        #legend{
            position: absolute;
            top: 350px;
            right: 16px;
        }
        .ol-unselectable{
            border: 0px !important;
            /*width: 132% !important;*/
            /*height: 400px!important;*/
        }
        #map{
            border: 0px !important;
            /*width: 970px !important;*/
            height: 500px!important;
        }
        #typeDraw{
            height: 25px !important;
        }
        .multiselect{
            width: 120px;
        }

    </style>
    @if(session('user')->usergroup->auth_authorization)
        <form class="col-sm-12">
            <div class="form-group form-inline col-sm-6">
                <label for="choiceUser">{{trans('common.choice_user_label')}} </label>
                <input type="text " class="form-control col-sm-offset-1"  placeholder="{{trans('common.choice_user_place_holder')}}" id = "search-box-main">
                <ul id="search_suggestion_holder_main" class="search_suggestion_holder">
                </ul>
                <input type="hidden" id="user_id_main" value="" />
            </div>
        </form>
    @endif
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12" style="padding-right:10px">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">{{trans('common.layer_label')}}<a style="float:right" href="#">
                            <span id="show" class="glyphicon glyphicon-list"   data-toggle="tooltip" data-placement="left" title="{{trans('common.show_all_tooltip')}}"></span>
                        </a>
                        <a style="float:right;margin-right: 20px;" href="#"><span id="hide" class="glyphicon glyphicon-refresh" ></span>
                        </a></h3>
                </div>
                <div class="panel-body fixed-panel">
                    <div class="col-sm-12">
                        <div class="data">
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-12 col-xs-12" style="padding-left:0px">
            <div class="panel panel-primary">
                <div class="panel-heading" style="padding-top: 8px;padding-bottom: 8px;">
                    <span style="margin-right:20px;">{{trans('common.map_label')}}</span>
                    <button class="btn btn-xs btn-success show-nito-map">{{ trans('common.create_fertilizer_btn_label') }}</button>
                    <button class="btn btn-xs btn-success change-color" id="change_color" type="button">{{ trans('common.change_color_fertilizer_btn_label') }}</button>

                    <button class="btn btn-xs btn-success user-map" id="user-map" type="button">{{ trans('common.user_map_btn_label') }}</button>

                    <button class="btn btn-xs btn-success layer1" id="layer1" type="button">Layer1</button>

                    <div id='typeDraw'  style="display:none; float:right;">
                        <input type="radio" class="type type-poligon"  name="type" value='Polygon'><span style="padding-right: 5px">Polygon</span>
                        <input type="radio" class="type type-box" name="type" value='Box'><span style="padding-right: 5px">Box</span>
                        <span id="clear" class="glyphicon glyphicon-erase" ><span style="padding-right: 5px">Clean</span></span>
                        <select name="controlToolBar" id="controlToolBar" multiple="multiple" style="float:right">
                            <option value="0">{{trans('common.option_scaleline')}}</option>
                            <option value="1">{{trans('common.option_zoom')}}</option>
                            <option value="4">{{trans('common.option_legend')}}</option>
                        </select>

                    </div>
                </div>
                <div class="panel-body fixed-panel" style="padding-top: 0px;padding-left: 0px;padding-bottom: 0px;padding-right: 0px;">
                    <div id="map"  class="map"></div>
                </div>
            </div>
            <div>
                <div id="legend" ><img src="" /></div>
            </div>
        </div>
    </div >
@endsection
@section('footer')
    <script src="{{url('/js/jstree.js')}}"></script>
    <script src="{{url('/js/libs/ol3/ol-custom.js')}}"></script>
    <script src="{{url('/js/libs/colorpicker/js/bootstrap-colorpicker.js')}}"></script>
    <script>
        var gisObject = {
            config : {!! json_encode($config) !!},
            user_id_main : null,
            fertility_map_id : null,
            crop_id : null,
            is_fertilizer : false,
            layer_id : null,
            map_info_ids : [],
            mode_type : null,
            mode_selection_id : null,
            mode_selection_info_ids : []
        };

    </script>
    <script src="{{url('/js/tree/tree_admin.js')}}"></script>
    <script src="{{url('/js/modules/creatingmap.js')}}"></script>
    <script src="{{url('/js/modules/changingcolor.js')}}"></script>
    <script src="{{url('/js/modules/map.js')}}"></script>
    <script src="{{url('/js/modules/index.js')}}"></script>

    <script>
        applyAutocomplete(window.base_url+'/load-main-users','search-box-main','search_suggestion_holder_main','user_id_main');
    </script>

    <script>
        $(function(){
            $('.layer1').click(function(){
               gisMap.debugMap({!! json_encode($layer1) !!}, {!! json_encode($layer2) !!}, {!! json_encode($layer3) !!});
            });
        });
    </script>
@endsection