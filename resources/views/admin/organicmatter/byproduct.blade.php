@extends('dialog')
@section('content')

    <title>{{trans('common.byproduct_title')}}</title>
    {{--<input id="bp_hidden" type="hidden" value="{{ $jsondata }}" />--}}
{!! Form::open(array('name' => 'byproduct-frm','class'=>'frm-validation-byproduct table-form-validation','files'=>true))!!}
<div class="table-form-validation">
	<div class="form-group">
		{!! Form::label('by_product_label1',trans('common.byproduct_table_label_1'), array('class' => 'change-label')) !!}<br>
		<select id="select1" onchange="creatingmap.getData();" class="form-control change-select fertilizer-popup-select custom-input form-custom validate[required]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.byproduct_require') }}">
			<option value="" disabled selected>{{ trans('common.byproduct_option_bp') }}</option>
			<option value="1">{{ trans('common.byproduct_select_bp1') }}</option>
			<option value="2">{{ trans('common.byproduct_select_bp2') }}</option>
			<option value="3">{{ trans('common.byproduct_select_bp3') }}</option>
			<option value="4">{{ trans('common.byproduct_select_bp4') }}</option>
			<option value="5">{{ trans('common.byproduct_select_bp5') }}</option>
			<option value="6">{{ trans('common.byproduct_select_bp6') }}</option>
			<option value="7">{{ trans('common.byproduct_select_bp7') }}</option>
		</select>
	</div>
	<div class="form-group">
		{!! Form::label('by_product_label2',trans('common.byproduct_table_label_2'), array('class' => 'change-label')) !!} <br>
		<select id="select2" onchange="creatingmap.getData();" class="form-control change-select select2 fertilizer-popup-select custom-input form-custom validate[required]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.process_method_require') }}">
			<option value="" disabled selected>{{ trans('common.byproduct_option_pm') }}</option>
			<option value="1">{{ trans('common.byproduct_select_pm1') }}</option>
			<option value="2">{{ trans('common.byproduct_select_pm2') }}</option>
			<option value="3" disabled>{{ trans('common.byproduct_select_pm3') }}</option>
		</select>
	</div>
	<div class="form-group">
		{!! Form::label('by_product_label3',trans('common.byproduct_table_label_3'), array('class' => 'change-label')) !!} <br>
		<select id="select3" onchange="creatingmap.getData();" class="form-control change-select fertilizer-popup-select custom-input form-custom validate[required]" data-prompt-position="topRight:-200,20" data-errormessage-value-missing="{{ trans('common.soil_value_require') }}">
			<option value="" disabled selected>{{ trans('common.byproduct_option_sv') }}</option>
			<option value="1">{{ trans('common.select_sv1') }}</option>
			<option value="2">{{ trans('common.select_sv2') }}</option>
			<option value="3">{{ trans('common.select_sv3') }}</option>
		</select>
	</div>

    <div class="form-group">
        <table class="table table-bordered">
            <tr>
                <td colspan="3" class="change-td-table change-background-td">{{trans('common.byproduct_table_label_4')}}</td>
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
                    <input type="text" class=" change-input-table" id="sub-byproduct-nito" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class=" change-input-table" id="sub-byproduct-photpho" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class=" change-input-table" id="sub-byproduct-kali" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class=" change-input-table" id="standard-dry" disabled>
                </td>
                <td class="change-td-table">
                    <input type="text" class=" change-input-table" id="standard-rate" disabled>
                </td>
            </tr>
        </table>
    </div>
    <div class="form-group" style="width: 58%;">
        <table class="table table-bordered">
            <tr>
                <td class="change-td-table change-background-td" colspan="3">{{trans('common.byproduct_table_label_5')}}</td>
            </tr>
            <tr>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_1')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_2')}}</td>
                <td class="change-td-table change-background-td">{{trans('common.placeholder_3')}}</td>
            </tr>
            <tr>
                <td class="change-td-table">
                    <input type="text" class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-prompt-position="topRight:0,22"
                           data-errormessage-custom-error="{{trans('common.input_number_required')}}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}" id="byproduct-nito">
                </td>
                <td class="change-td-table">
                    <input type="text"class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-prompt-position="topRight:0,22"
                           data-errormessage-custom-error="{{trans('common.input_number_required')}}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}"
                           id="byproduct-photpho">
                </td>
                <td class="change-td-table">
                    <input type="text"class="change-input-table onlyDecimal6_1 validate[custom[number], max[999.9], min[-999.9]]"
                           data-prompt-position="topRight:0,22"
                           data-errormessage-custom-error="{{trans('common.input_number_required')}}"
                           data-errormessage-range-overflow="{{trans('common.over_range_max')}}"
                           data-errormessage-range-underflow="{{trans('common.over_range_min')}}"
                           id="byproduct-kali">
                </td>
            </tr>
        </table>
    </div>

	<div class="form-group" style="float: right;">
		<button class="btn-byproduct button-submit" type="button">{{trans('common.confirm_ok')}}</button>
        <button class="btn-cancel button-submit" type="button" onclick="closeDialog();">{{trans('common.no')}}</button>
    </div>
	<input id="bp_message" type="hidden" value="{{ trans('common.no_data_for_selection') }}" />
</div>
{!! Form::close()!!}
@endsection @section('footer')
<script src="{{url('/js/modules/organicmatter_list.js')}}"></script>
@endsection
