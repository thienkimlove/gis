@extends('dialog')
@section('content')
    <title>{{trans('common.greenmanure_title')}}</title>
    {!! Form::open(array('name' => 'greenmanure-frm','class'=>'frm-validation-greenmanure table-form-validation','files'=>true))!!}
    <div class="form-group">
        {!! Form::label('green_manure_label1', trans('common.greenmanure_table_label_1'), array('class' => 'change-label') ) !!}<br>
			<select id="selectgm1" onchange="creatingmap.getDataGM();" class="form-control change-select fertilizer-popup-select custom-input form-custom validate[required]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.greenmanure_require') }}">
                <option value="" disabled selected>{{ trans('common.greenmanure_option_gm') }}</option>
                <option value="1">{{ trans('common.greenmanure_select_gm1') }}</option>
					<option value="2">{{ trans('common.greenmanure_select_gm2') }}</option>
					<option value="3">{{ trans('common.greenmanure_select_gm3') }}</option>
					<option value="4">{{ trans('common.greenmanure_select_gm4') }}</option>
					<option value="5">{{ trans('common.greenmanure_select_gm5') }}</option>
					<option value="6">{{ trans('common.greenmanure_select_gm6') }}</option>
					<option value="7">{{ trans('common.greenmanure_select_gm7') }}</option>
			</select>
    </div>
    <div class="form-group">
        {!! Form::label('green_manure_label2', trans('common.greenmanure_table_label_2'), array('class' => 'change-label') ) !!}<br>
			<select id="selectgm2" onchange="creatingmap.getDataGM();" class="form-control change-select selectgm2 fertilizer-popup-select custom-input form-custom validate[required]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.crop_type_require') }}">
			        <option value="" disabled selected>{{ trans('common.greenmanure_option_ct') }}</option>
					<option value="1">{{ trans('common.greenmanure_select_ct1') }}</option>
					<option value="2">{{ trans('common.greenmanure_select_ct2') }}</option>
					<option value="3">{{ trans('common.greenmanure_select_ct3') }}</option>
			</select>
    </div>
    <div class="form-group">
        {!! Form::label('green_manure_label3', trans('common.greenmanure_table_label_3'), array('class' => 'change-label') ) !!}<br>
			<select id="selectgm3" onchange="creatingmap.getDataGM();" class="form-control change-select fertilizer-popup-select custom-input form-custom validate[required]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.soil_value_require') }}">
			        <option value="" disabled selected>{{ trans('common.byproduct_option_sv') }}</option>
					<option value="1">{{ trans('common.select_sv1') }}</option>
					<option value="2">{{ trans('common.select_sv2') }}</option>
					<option value="3">{{ trans('common.select_sv3') }}</option>
			</select>
    </div>
    <div class="form-group">
        {!! Form::label('green_manure_label4', trans('common.greenmanure_table_label_4'), array('class' => 'change-label') ) !!}<br>
	    <input id="kali-rate" type="text" disabled class="input-popup  change-select form-control validate[custom[integer], max[999]]" data-prompt-position="topRight:-200,20" data-errormessage-custom-error="{{ trans('common.float_value_require') }}" data-errormessage-range-overflow="{{trans('common.rate_over_range_max')}}">
    </div>

    <div class="form-group">
        <table class="table table-bordered">
            <tr>
                <td colspan="3" class="change-td-table change-background-td">{{trans('common.greenmanure_table_label_5')}}</td>
                <td rowspan="2" class="change-td-table change-background-td">{{trans('common.placeholder_4')}}</td>
                <td rowspan="2" class="change-td-table change-background-td">{{trans('common.placeholder_5')}}</td>
            </tr>
            <tr>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_1')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_2')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_3')}}</td>
            </tr>
            <tr>
                <td class="change-td-table">
                    <input type="text" class=" change-input-table" id="sub-greenmanure-nito" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class=" change-input-table" id="sub-greenmanure-photpho" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class=" change-input-table" id="sub-greenmanure-kali" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class=" change-input-table" id="gm-standard-dry" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class=" change-input-table" id="gm-standard-rate" disabled>
                </td>
            </tr>
        </table>
    </div>
    <div class="form-group" style="width: 58%;">
        <table class="table table-bordered">
            <tr>
                <td class="change-td-table change-background-td" colspan="3">{{trans('common.greenmanure_table_label_6')}}</td>
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
                           id="greenmanure-nito">
                </td>
                <td class="change-td-table">
                    <input type="text" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-errormessage-custom-error="{{trans('common.input_number_required')}}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}"
                           id="greenmanure-photpho">
                </td>
                <td class="change-td-table">
                    <input type="text" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-errormessage-custom-error="{{trans('common.input_number_required')}}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}"
                           id="greenmanure-kali">
                </td>
            </tr>
        </table>
    </div>

    <div class="form-group" style="float: right;">
		<button class="btn-greenmanure button-submit">{{trans('common.confirm_ok')}}</button>
        <button class="btn-cancel button-submit" type="button" onclick="closeDialog();">{{trans('common.no')}}</button>
    </div>
	<input id="greenmanure_message" type="hidden" value="{{ trans('common.greenmanure_fetilizer_require') }}" />
	<input id="gm_message" type="hidden" value="{{ trans('common.no_data_for_selection') }}" />
{!! Form::close()!!}
@endsection
@section('footer')
<script src="{{url('/js/modules/organicmatter_list.js')}}"></script>
@endsection
