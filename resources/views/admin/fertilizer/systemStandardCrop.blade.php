@extends('popup')

@section('content')
    <style>
        .ui-helper-reset {
            font-size: 14px!important;
        }
        .fancybox-wrap{
            height:100%!important;
            width:80% !important;
        }
        .ui-tabs .ui-tabs-nav li.ui-tabs-active {
            background: #60ABF8 !important;
        }
    </style>
    {!! Form::open(array('route' => 'submit-system-standard-crop-details','method' => 'post','class' => 'form-horizontal system-standard-crop-details-frm')) !!}
    <div class="row">
        <h2 class="page-header">{{$fertilizer->fertilization_standard_name}}</h2>
        <input type="hidden" id="hidden-standard-id" name ="hidden-standard-id" value="{{$fertilizer->id}}">
        <input type="hidden" id="del-p" name ="del-p">
        <input type="hidden" id="del-p-arr" name ="del-p-arr">
        <input type="hidden" id="del-k" name ="del-k">
        <input type="hidden" id="del-k-arr" name ="del-k-arr">
        <input type="hidden" id="dataChangeN" name ="dataChangeN" />
        <input type="hidden" id="dataChangeP" name ="dataChangeP" />
        <input type="hidden" id="dataChangeK" name ="dataChangeK" />
        <div class="form-group">
            <div style="float:left; width:100%;">
                <label class="standard-crop-label col-md-1" style="margin-top: 4px;" for="textinput">{{ trans('common.standardcrop_crop') }}</label>
                {!! Form::select('crops_id', $crops,array('class'=>'form-control select-standard-crop validate[required]',
                    'style'=>'font-size:12px;',
                    'data-errormessage-value-missing' =>  trans('common.standardcropinfo_crop_required')
                )) !!}
                @if(session('user')->usergroup->auth_authorization)
                <button type="button" id="btnClearCrop" onclick="confirmClearCrop();" class="btnClearCrop button-submit btn-form" >
                    {{trans('common.fertilizer_system_clear_button')}}
                </button>
                @endif
            </div>

        </div>

        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">{{ trans('common.system_standard_crop_nito_label') }}</a></li>
                <li><a href="#tabs-2">{{ trans('common.system_standard_crop_photpho_label') }}</a></li>
                <li><a href="#tabs-3">{{ trans('common.system_standard_crop_kali_label') }}</a></li>
            </ul>
            <div id="tabs-1" style="padding-left:0px;padding-right:0px">
                <table id="list1" class="onlyDecimal4_2"></table>
                <div id="pager1"></div>
            </div>
            <div id="tabs-2" style="padding-left:0px;padding-right:0px">
                <table id="list2"></table>
                <div id="pager2"></div>
            </div>
            <div id="tabs-3" style="padding-left:0px;padding-right:0px">
                <table id="list3"></table>
                <div id="pager3"></div>
            </div>
        </div>
    </div>
    @if($user->usergroup->auth_authorization)
        <div class ="row">
            <hr>
            <button type="button" class="btn-save-system-standard-crop button-submit btn-form">
                {{trans('common.button_save')}}
            </button>
            <button type="button" onclick="parent.$.fancybox.close();" class="btn-cancel-system-standard-crop button-submit btn-form" >
                {{trans('common.button_cancel')}}
            </button>
        </div>
    @endif
    {!! Form::close() !!}
@endsection
@section('footer')
    <script>
        setTimeout(function(){
            $('[name=crops_id]').focus();
        },0);
        controlTabC('btnClearCrop','btn-cancel-system-standard-crop');
        controlTabC('btn-save-system-standard-crop','btnClearCrop');
        var isAdmin={!! json_encode($user->usergroup->auth_authorization)!!};
        controlTabC('select-standard-crop','btn-cancel-system-standard-crop');
        controlTabC('btn-save-system-standard-crop','select-standard-crop');
    </script>
    <script src="{{url('/js/modules/system_standard_crop.js')}}"></script>

@endsection