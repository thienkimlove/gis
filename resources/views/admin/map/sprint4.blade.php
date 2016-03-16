@extends('admin')

@section('content')

    <link href="{{ url('/css/changingcolor.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/creatingmap.css') }}" rel="stylesheet" type="text/css">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Demo setup bản đồ bón phân</h2>
        </div>
    </div>
    {!! Form::open( ['url' => '/submit-changing-user','method' => 'post', 'class'=>'form-horizontal changing-user-form','id'=>'test_form', 'name' => 'changing-user-form']) !!}
        <input type="hidden" name="mapInfoId" value="41103,41104,41822">

    {!! Form::close() !!}

        <button class="button-submit" onclick="creatingmap.openCreatingMap(Array('41822','41808','121212'),310,5,123);">Create map</button>

    <button class="button-submit" onclick="creatingmap.openMapViewer();">Open map viewer</button>

        <button class="button-submit" onclick="creatingmap.openCreatingMap(149);">Create map (default value)</button>
        <button class="button-submit" onclick="changingcolor.openChangingColor('1');">Change color</button>
        <button class="button-submit" onclick="changingcolor.openMergingColor('1');">Merge color</button>
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <ul>
                    @foreach ($mapLists as $map)
                        <li><button mapId="{{$map->id}}" mapName="{{$map->map_name}}" class="button show-feritymap">{{$map->map_name}}</button></li>
                    @endforeach
                </ul>
            </div>

            <div class="row">
                <div class="container">
                    <div style="width:800px; height: 100px; display: none" id="showMap">
                        <h2 id="map_name"></h2>
                        <input type="hidden" id="map_id" value="" />
                        <button class="button-submit user-choose-area">User Choose Area Button</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
	<script src="{{url('/js/modules/creatingmap.js')}}"></script>
    <script src="{{url('/js/modules/changingcolor.js')}}"></script>
    <script>
        $(function(){
            $('.show-feritymap').click(function(event){
                event.preventDefault();
                var mapId = $(this).attr('mapId');
                var mapName = $(this).attr('mapName');
                $('#map_name').html(mapName);
                $('#map_id').val(mapId);
                $('#showMap').show();
            });

            $('.user-choose-area').click(function(event){
                event.preventDefault();
                var choosenMap = $('#map_id').val();
                $.fancybox( [{
                            href : window.base_url + '/mapOption/' + choosenMap,
                            type : 'ajax',
                            helpers: {
                                overlay: { closeClick: false } //Disable click outside event
                            }
                        }], {
                            afterLoad: function(data){
                                try {
                                    var json = $.parseJSON(data.content);
                                    bootbox.dialog({
                                        message : json.message,
                                        title : Lang.get('common.error_title')
                                    });
                                    top.$.fancybox.close();
                                    return false;
                                } catch(err) {

                                }
                            },
                            afterClose : function(){

                            }
                        }
                );
            });
        });
    </script>
@endsection