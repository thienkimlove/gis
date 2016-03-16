@extends('popup')
@section('content')
<div class="popup-merging" id='popup_merging'>
    <div  class="col-md-12">
        <br/>
        <h2 class="page-header">{{trans('common.mergingcolor_popup_title')}}</h2>
        <label class="text-left"  style="margin-right: 14px;">{{trans('common.mergingcolor_popup_description_title')}}</label>
        <div class="panel panel-default">
            <div class="panel-body">
                <ul class="list-unstyled" id="listSelect">
                  @foreach ($colors as $key =>$value)
                        <li id="color_{{$key}}" class="color-items"  color="{{$key}}"  code-color="{{$value[1].','.$value[2]}}" style="margin-bottom: 3px;">
                            <div class="color-row">
                                <div class="color-box" style="background-color: {{'#'.$value[0]}};"></div>
                                @if($isOneBarrel)
                                <div class="color-name">{{$value[1].'kg/10a'}}</div>
                                @else
                                    <div class="color-name">{{trans('common.changingcolor_main_barrel').' '.$value[1].'kg/10a, '.trans('common.changingcolor_sub_barrel').' '.$value[2].'kg/10a'}}</div>
                                @endif
                            </div>
                        </li>

                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-12 button-group">
            <div class="pull-left">
                <button type="button" class="button button-submit button-merge"  onclick="changingcolor.mergeDataMapColor();">{{trans('common.mergingcolor_btn_other_fertilization')}}</button>
            </div>
            <div class="pull-right">
                <button type="button" class="button-submit" onclick="changingcolor.submitAreaColor();">{{trans('common.button_alert_ok')}}</button>
                <button type="button" class="button-submit btn-close-merging-map" onclick="closeMergingMap();return true;">{{trans('common.button_cancel')}}</button>
            </div>
        </div>
    </div>
</div>
<script src="{{url('/js/libs/jquery.multipleselectbox.js')}}"></script>
<script>
    setTimeout(function(){
       $('.button-merge').focus();
    },0);
    controlTabC('button-merge','btn-close-merging-map');
    $(document).ready(function() {
        $("#listSelect").multipleSelectBox({maxLimit : 1});
    });
</script>
@endsection