@extends('dialog')
@section('content')
    <title>{{trans('common.compost_title')}}</title>
    {!! Form::open(array('name' => 'compost-frm','class'=>'frm-validation-compost table-form-validation','files'=>true))!!}
    <div class="form-group">
        {!! Form::label('compost_label1',trans('common.compost_table_label_1'), array('class' => 'change-label') ) !!}<br>
            <select id="selectcp1" onchange="creatingmap.getDataCP();creatingmap.getDataCP3();" class="form-control change-select fertilizer-popup-select custom-input form-custom validate[required]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.compost_require') }}">
                    <option value="" disabled selected>{{ trans('common.compost_option_c') }}</option>
                    <option value="1">{{ trans('common.compost_select_c1') }}</option>
                    <option value="2">{{ trans('common.compost_select_c2') }}</option>
                    <option value="3">{{ trans('common.compost_select_c3') }}</option>
                    <option value="4">{{ trans('common.compost_select_c4') }}</option>
            </select>
    </div>
    <div class="form-group">
        {!! Form::label('compost_label2',trans('common.compost_table_label_2'), array('class' => 'change-label') ) !!}<br>
            <select id="selectcp3" onchange="creatingmap.getDataCP2();" class="form-control change-select fertilizer-popup-select custom-input form-custom validate[required]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.compost_component_require') }}" data-errormessage-range-overflow="{{trans('common.dry_matter_over_range_max')}}">
                    <option value="" disabled selected>{{ trans('common.compost_option_cc') }}</option>
                    <option value="1">{{ trans('common.compost_select_cc1') }}</option>
                    <option value="2">{{ trans('common.compost_select_cc2') }}</option>
            </select>

    </div>
    <div class="form-group" style="text-align: center;">
        {!! Form::label('compost_label2',trans('common.placeholder_6'), array('class' => 'change-label') ) !!}
        <input id="dry-matter" type="text" class="input-popup onlyDecimaPositive_1 form-control validate[custom[float], max[99.9]]"
               style="width: 30%;"
               data-prompt-position="topRight:-100,20"
               data-errormessage-custom-error="{{ trans('common.integer_value_require') }}"
               data-errormessage-range-overflow="{{trans('common.dry_matter_max')}}" >
        <span>%</span>
    </div>
    <div class="form-group" style="">
        <table class="table table-bordered">
            <tr>
                <td class="change-td-table change-background-td" colspan="3">{{trans('common.compost_table_new_add')}}</td>
            </tr>
            <tr>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_1')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_2')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_3')}}</td>
            </tr>
            <tr>
                <td class="change-td-table">
                    <input id="seibun-nito" type="text" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-prompt-position="topRight:-100,15"
                           data-errormessage-custom-error="{{ trans('common.integer_value_require') }}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}">
                </td>
                <td class="change-td-table">
                    <input id="seibun-photpho" type="text" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-prompt-position="topRight:-100,15"
                           data-errormessage-custom-error="{{ trans('common.integer_value_require') }}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}">
                </td>
                <td class="change-td-table">
                    <input id="seibun-kali" type="text"  class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-prompt-position="topRight:-100,0"
                           data-errormessage-custom-error="{{ trans('common.integer_value_require') }}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}">
                </td>
            </tr>
        </table>
    </div>
    <div class="form-group">
         {!! Form::label('compost_label3',trans('common.compost_table_label_3'), array('class' => 'change-label') ) !!}<br>
            <select id="selectcp2" onchange="creatingmap.getDataCP3();" class="form-control change-select fertilizer-popup-select custom-input form-custom validate[required]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.application_time_require') }}">
                    <option value="" disabled selected>{{ trans('common.compost_option_time') }}</option>
                    <option value="1">{{ trans('common.compost_select_time1') }}</option>
                    <option value="2">{{ trans('common.compost_select_time2') }}</option>
                    <option value="3" disabled>{{ trans('common.byproduct_select_pm3') }}</option>
            </select>
    </div>
    <div class="form-group">
        {!! Form::label('compost_label4',trans('common.compost_table_label_4'), array('class' => 'change-label') ) !!}<br>
        <input id="compost-input" type="text" class="input-popup onlyDecimal6_1 change-select form-control validate[required,custom[integer], max[999]]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.compost_input_require') }}" data-errormessage-custom-error="{{ trans('common.integer_value_require') }}" data-errormessage-range-overflow="{{trans('common.rate_over_range_max')}}">
    </div>
    <div class="form-group" style="">
        <table class="table table-bordered">
            <tr>
                <td class="change-td-table change-background-td" colspan="3">{{trans('common.compost_table_label_5')}}</td>
            </tr>
            <tr>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_1')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_2')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_3')}}</td>
            </tr>
            <tr>
                <td class="change-td-table">
                    <input type="text" onchange="creatingmap.npkRecommend();" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]" data-errormessage-custom-error="{{trans('common.input_number_required')}}" data-errormessage-range-overflow="{{trans('common.over_range_max')}}" data-errormessage-range-underflow="{{trans('common.over_range_min')}}" id="sub-compost-nito">
                </td>
                <td class="change-td-table">
                    <input type="text" onchange="creatingmap.npkRecommend();" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]" data-errormessage-custom-error="{{trans('common.input_number_required')}}" data-errormessage-range-overflow="{{trans('common.over_range_max')}}" data-errormessage-range-underflow="{{trans('common.over_range_min')}}" id="sub-compost-photpho">
                </td>
                <td class="change-td-table">
                    <input type="text" onchange="creatingmap.npkRecommend();" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]" data-errormessage-custom-error="{{trans('common.input_number_required')}}" data-errormessage-range-overflow="{{trans('common.over_range_max')}}" data-errormessage-range-underflow="{{trans('common.over_range_min')}}" id="sub-compost-kali">
                </td>
            </tr>
        </table>
    </div>
    <div class="form-group" style="">
        <table class="table table-bordered">
            <tr>
                <td class="change-td-table change-background-td" colspan="3">{{trans('common.compost_table_label_6')}}</td>
            </tr>
            <tr>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_1')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_2')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_3')}}</td>
            </tr>
            <tr>
                <td class="change-td-table">
                    <input type="text" class="change-input-table" id="compost-nito" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class="change-input-table" id="compost-photpho" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class="change-input-table" id="compost-kali" disabled>
                </td>
            </tr>
        </table>
    </div>
    <div class="form-group" style="">
        <table class="table table-bordered">
            <tr>
                <td class="change-td-table change-background-td" colspan="3">{{trans('common.compost_table_label_7')}}</td>
            </tr>
            <tr>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_1')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_2')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_3')}}</td>
            </tr>
            <tr>
                <td class="change-td-table">
                    <input type="text" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-errormessage-custom-error="{{trans('common.input_number_required')}}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}"
                           id="compost-user-nito">
                </td>
                <td class="change-td-table">
                    <input type="text" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-errormessage-custom-error="{{trans('common.input_number_required')}}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}"
                           id="compost-user-photpho">
                </td>
                <td class="change-td-table">
                    <input type="text" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-errormessage-custom-error="{{trans('common.input_number_required')}}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}"
                           id="compost-user-kali">
                </td>
            </tr>
        </table>
    </div>
    <div class="form-group" style="float: right;">
		<button class="btn-compost button-submit">{{trans('common.confirm_ok')}}</button>
        <button class="btn-cancel button-submit" type="button" onclick="closeDialog();">{{trans('common.no')}}</button>
    </div>
	<input id="compost_message1" type="hidden" value="{{ trans('common.dry_matter_require') }}" />
	<input id="compost_message2" type="hidden" value="{{ trans('common.integer_value_require') }}" />
{!! Form::close()!!}
@endsection @section('footer')
<script src="{{url('/js/modules/organicmatter_list.js')}}"></script>
@endsection
