@extends('popup')
@section('content')

    <div class="form-group1 changingcolor">
        <div class="col-md-12">
            <br/>

            <h3 class="text-center panel-title">{{ trans('common.mergingcolor_title') }}</h3>

            {!! Form::open(array('route' => 'map.confirm','method' => 'post','class' => 'form-horizontal creating-map-frm')) !!}
            <input id="fertilizerId" name="fertilizerId" type="hidden" value="{{$fertilizerId}}">
            <input id="isOneBarrel" name="isOneBarrel" type="hidden" value="{{$isOneBarrel}}">
            <!-- Begin column 1 -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-12">
                        <ul id="color_list_merging" class="color-area list-unstyled " style="height: 150px">
                            @foreach ($colors as $key =>$value)

                                <li id="color_{{$key}}" class="color-items" color="{{$key}}"
                                    value="{{$value[1].','.$value[2]}}">
                                    <div class="color-row">
                                        <div class="color-box" style="background-color: {{'#'.$value[0]}};"></div>
                                        @if($isOneBarrel)
                                            <div class="color-name">{{$value[1]}}</div>
                                        @else
                                            <div class="color-name">{{trans('common.changingcolor_main_barrel').' '.$value[1].'kg/10a, '.trans('common.changingcolor_sub_barrel').' '.$value[2].'kg/10a'}}</div>
                                        @endif
                                    </div>
                                </li>

                            @endforeach
                        </ul>

                    </div>
                </div>

            </div>
            <!-- End column 1 -->
            <div class="col-md-12 mergingcollor-button-group" onclick="">
                <div class="confirm-button-group">
                    <button type="button" class="button-submit"
                            onclick="changingcolor.openEditingColor('{{$fertilizerId}}');">{{trans('common.button_alert_ok')}}</button>
                    <button type="button" class="button-submit"
                            onclick="changingcolor.openMergingColor();">{{trans('common.button_cancel')}}</button>

                </div>
            </div>
            {!! Form::close() !!}


        </div>
    </div>
    <script src="{{url('/js/libs/jquery.multipleselectbox.js')}}"></script>
    <script>
        $(document).ready(function () {
            $("#color_list_merging").multipleSelectBox();
        });

    </script>
@endsection



