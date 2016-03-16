@extends('popup')
@section('content')

    <div class="form-group1 " id="mergingColor">
        <div class="col-md-12">
            <br/>

            <h2 class="page-header">{{ trans('common.mergingcolor_title') }}</h2>

            {!! Form::open(array('route' => 'map.confirm','method' => 'post','class' => 'form-inline frm-validation','onsubmit="return false;"')) !!}
            <input id="fertilizerId" name="fertilizerId" type="hidden" value="{{$fertilizerId}}">
            <input id="isOneBarrel" name="isOneBarrel" type="hidden" value="{{$isOneBarrel}}">
            <input id="colorCode" name="colorCode" type="hidden" value="0,0,0">
            <!-- Begin column 1 -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <ul class="list-unstyled">
                                <li><label for="" class="control-label"></label>
                                </li>
                                <li>  <div id="editingcolor-box" onclick="changingcolor.colorPickColor(this);"
                                           class="editingcolor-box" style="background: #000">
                                    </div>
                                </li>
                            </ul>

                        </div>
                        @if($isOneBarrel)
                            <div class="form-group">
                                <ul class="list-unstyled">
                                    <li>
                                        <label for=""  class="control-label">{{trans('common.changingcolor_main_barrel')}}</label>
                                    </li>
                                    <li>
                                        {!! Form::text('main_barrel','', array('id' => 'main_barrel', 'maxlength'=>'4', 'class' => 'form-control smallint validate[required]  maxSize[4] ',
                                       'data-errormessage-value-missing' =>  trans("common.mergingcolor_message_mainsub_required")
                                   )) !!}
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="form-group">
                                <ul class="list-unstyled">
                                    <li>
                                        <label for=""  class="control-label">{{trans('common.changingcolor_main_barrel')}}</label>
                                    </li>
                                    <li>
                                        {!! Form::text('main_barrel','', array('id' => 'main_barrel', 'maxlength'=>'4', 'class' => 'form-control smallint validate[required]  maxSize[4] ',
                                       'data-errormessage-value-missing' =>  trans("common.mergingcolor_message_mainsub_required")
                                   )) !!}<span>{{trans('common.editingcolor_unit')}}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="form-group">
                                <ul class="list-unstyled">
                                    <li><label for=""
                                               class=" control-label">{{trans('common.changingcolor_sub_barrel')}}</label>
                                    </li>
                                    <li>
                                        {!! Form::text('sub_barrel','', array('id' => 'sub_barrel', 'maxlength'=>'4', 'class' => 'form-control smallint validate[required]  maxSize[4] ',
                                      'data-errormessage-value-missing' =>  trans("common.mergingcolor_message_mainsub_required")
                                  )) !!}<span>{{trans('common.editingcolor_unit')}}</span>
                                    </li>
                                </ul>
                            </div>
                        @endif



                    </div>
                </div>

            </div>
            <!-- End column 1 -->
            <div class="col-md-12 mergingcollor-button-group" onclick="">
                <div class="confirm-button-group">
                    <button type="button" class="button-submit button-ok"
                            onclick="changingcolor.openEditingColor('{{$fertilizerId}}');">{{trans('common.button_alert_ok')}}</button>
                    <button type="button" class="button-submit button-cancel"
                            onclick="changingcolor.openMergingColor();">{{trans('common.button_cancel')}}</button>

                </div>
            </div>
            {!! Form::close() !!}


        </div>
    </div>
<script>
    setTimeout(function(){
        $('.button-ok').focus();
    },0);
    controlTabC('button-ok','button-cancel');
</script>
@endsection



