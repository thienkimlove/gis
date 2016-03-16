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
            top: 300px;
            right: 16px;
            background-color:#fff;
        }
        #map-information{
            position: absolute;
            bottom: 8px;
            right: 30px;
            background-color:#fff;
            width: 60%;
            font-size: 12px;
        }
        .ol-unselectable{
            border: 0px !important;
        }
        #map{
            border: 0px !important;
            min-height: 400px;
            float: left;
            width:100%;
        }
        #typeDraw{
            padding-left:0;
            padding-right: 0;
            float:left;
            display: inline;
        }
        #panel-left {
            padding-right: 0;
        }
        #panel-right {
            padding-left: 8px;
        }
    </style>
    <input type="hidden" id="first-time-login" value={{$firstTimeLogin}}>
    @if(session('user')->usergroup->auth_authorization)
        <div class="col-xs-12" style="padding-left: 0px;">
        <form class="form-inline" role="form">
            <div class="form-group">
                <label >{{trans('common.choice_user_label')}} </label>
                <input type="text " class="form-control" style="width: 280px;"  placeholder="{{trans('common.choice_user_place_holder')}}" id = "search-box-main">
                <ul id="search_suggestion_holder_main" class="search_suggestion_holder" style="width: 280px;margin-left: 73px;">
                </ul>
            </div>
            <input type="hidden" id="user_id_main" value="" />
        </form>
        </div>
    @endif
    <div class="row" style="min-width:970px;">
        <div class="col-md-4 col-sm-4 col-xs-4" id="panel-left">
            <div class="panel panel-primary" >
                <div class="panel-heading">
                    <h3 class="panel-title">{{trans('common.layer_label')}}<a style="float:right" href="#">
                        <span id="show" class="glyphicon glyphicon-folder-open"   data-toggle="tooltip" data-placement="left" title="{{trans('common.show_all_tooltip')}}"></span>
                        </a>
                        <a style="float:right;margin-right: 20px;" href="#"><span id="hide" class="glyphicon glyphicon-folder-close" ></span>
                        </a></h3>
                </div>
                <div class="panel-body fixed-panel" id="panel-tree" style="padding-top: 0;">
                    <div class="col-xs-12">
                        <div class="data">
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-8 col-xs-8" id="panel-right">
            <div class="panel panel-primary" >
                <div class="panel-heading" style="padding-top: 6px;padding-bottom: 8px;padding-right: 0;padding-left: 0;height: 39px;">
                    <div class="col-xs-12" id="typeDraw" style="padding-right: 0;padding-left: 15px;background-color: #337AB7">
                    <span style="font-size: 16px;margin-right: 30px;">{{trans('common.map_label')}}</span>
                    <button class="btn btn-xs btn-success show-nito-map">{{ trans('common.create_fertilizer_btn_label') }}</button>
                    <button class="btn btn-xs btn-success user-map" id="user-map" type="button">{{ trans('common.user_map_btn_label') }}</button>
                    <button id="clear" class="btn btn-xs btn-success" style="display: none;">{{ trans('common.main_index_button_clean') }}</button>
                    <input id="polygonSelectionAreaForFertilizerCreation" name="" value='' style="display:none;">
                    </div>
                </div>
                <div class="panel-body" style="padding-top: 0px;padding-left: 0px;padding-bottom: 0px;padding-right: 0px;">
                    <div id="map"  class="map"></div>
                </div>
            </div>
            <div>
                <div id="legend"  ><img async src="" alt="legend" /></div>
                <span id="map-information">{{trans('common.main_index_map_information')}}</span>
            </div>
        </div>
    </div >
    <div class="modal fade" id="mapConfirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    </div>
@endsection
@section('footer')
    <script src="{{url('/js/jstree.js')}}"></script>
    <script src="{{url('/js/libs/ol3/ol-custom.js')}}"></script>
    <script src="{{url('/js/libs/colorpicker/js/bootstrap-colorpicker.js')}}"></script>
    <script>
        var gisObject = {
            config : {!! json_encode($config) !!},
            user_id_main : null,
            session_user_id : {!! session('user')->id !!},
            fertility_map_id : null,
            crop_id : null,
            is_fertilizer : false,
            layer_id : null,
            map_info_ids : [],
            mode_type : null,
            mode_selection_ids : [],
            mode_selection_info_ids : [],
            reloadMap :false,
            returnMap :false,
            guess_prediction : {} //only this object will using for process prediction.
        };
        var isGuest={!! json_encode(session('user')->usergroup->is_guest_group)!!};
        var isAdmin={!! json_encode(session('user')->usergroup->auth_authorization)!!};
    </script>
    <script src="{{url('/js/modules/drag.js')}}"></script>
    <script src="{{url('/js/tree/tree_admin.js')}}"></script>
    <script src="{{url('/js/modules/creatingmap.js')}}"></script>
    <script src="{{url('/js/modules/changingcolor.js')}}"></script>
    <script src="{{url('/js/modules/map.js')}}"></script>
    <script src="{{url('/js/modules/index.js')}}"></script>

    <script>

        $('body').click(function(event){
            var target = $( event.target );
            if(target.is('#search-box-main') && $('#search-box-main').val()!=''){
                $('#search_suggestion_holder_main').css('display','block');
            }else $('#search_suggestion_holder_main').css('display','none');
        });
        $( window ).resize(function(e) {
        if(e.target==window){
            $('#map').height($(window).height()*73/100);
            $('#panel-tree').height($(window).height()*73/100);
            $('#panel-left').removeAttr('style');
            $('#panel-right').removeAttr('style');
          }
        });
        applyAutocomplete(window.base_url+'/load-main-users','search-box-main','search_suggestion_holder_main','user_id_main');

    </script>
@endsection