@extends('admin')

@section('content')
    <div class="row" xmlns="http://www.w3.org/1999/html">
        <div class="col-lg-12">
            <h2 class="page-header">{{trans('common.downloadmanagement_title')}}</h2>
        </div>
    </div>
    {!! Form::open(array('route' => 'admin.downloadmanagement.export','method' => 'get','class' => 'frm-validation form-download form-horizontal')) !!}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="col-md-3 form-group">
                <label for="userName"
                       class="col-md-6">{{ trans("common.form_downloadmanagement_user_code_lbl") }}</label>

                <div class="col-md-4"><input type="text" class="form-control validate[custom[integer]]" id="useCode" name="useCode"
                                             data-errormessage-custom-error="{{ trans('common.integer_number_validate') }}"
                                             placeholder="" maxlength="10"></div>

            </div>
            <div class="col-md-4 form-group">
                <label for="userCode"
                       class="col-md-5">{{ trans("common.form_downloadmanagement_user_name_lbl") }}</label>

                <div class="col-sm-6 col-md-5"><input type="text" class="form-control" id="userName" name="userName"
                                             placeholder="" maxlength="20"></div>

            </div>
            <div class="col-md-3 form-group">
                <label for="userCode"
                       class="col-md-5" >{{ trans("common.form_downloadmanagement_user_group_lbl") }}</label>

                <div class="col-sm-6 col-md-5">
                    <select class="form-control"  id="userGroup" name="userGroup">
                        <option value=""></option>
                        @foreach($groups as $group)
                            <option value="{{$group->group_name}}">{{$group->group_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 form-group">
                <input type="button" value="{{ trans("common.button_search") }}" class="button-submit" id="btn-search-download">
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">

            <div class="col-md-3 form-group">
                <label for="userCode"
                       class="col-md-6">{{ trans("common.form_downloadmanagement_download_id_lbl") }}</label>

                <div class="col-md-4"><input type="text" class="form-control" id="downloadId" name="downloadId"
                                             placeholder="" maxlength="12"></div>

            </div>
            <div class="col-md-4 form-group date-field-form">
                <label for="userCode"
                       class="col-md-6">{{ trans("common.form_downloadmanagement_download_date_lbl") }}</label>

                <div class="col-md-6"><input type="text" class="form-control fix-column validate[past[#downloadDateEnd]]"
                                             data-errormessage-type-mismatch="{{ trans('common.download_management_start_date_condition') }}"
                                             id="downloadDateStart"
                                             name="downloadDateStart">
                    <span class="character" style="margin-top: 7px;margin-left:2px;">~</span>
                    <input type="text" class="form-control  fix-column validate[future[#downloadDateStart]]"
                           data-errormessage-type-mismatch="{{ trans('common.download_management_end_date_condition') }}"
                           id="downloadDateEnd" name="downloadDateEnd"
                           placeholder=""></div>

            </div>
            <div class="col-md-3 form-group payment-field-form">
                <label for="userCode"
                       class="col-md-4 col-lg-5 col-sm-4">{{ trans("common.form_downloadmanagement_paymenet_state_lbl") }}</label>

                <div class=" col-md-6 col-lg-5 col-sm-4">
                    <select class="form-control" id="paymentState" name="paymentState">
                        <option value="1,0">{{trans("common.form_downloadmanagement_all_lbl")}}</option>
                        <option value="1">{{trans("common.form_downloadmanagement_paid_lbl")}}</option>
                        <option value="0">{{trans("common.form_downloadmanagement_unpaid_lbl")}}</option>
                    </select>
                </div>

            </div>
            <div class="col-md-2 form-group">
                <input type="reset" value="{{ trans("common.button_reset") }}" class="button-submit" id="btn-reset-download">

            </div>
        </div>

    </div>
    <br/>
    {!! Form::close() !!}

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
                <div id="holdChecked" style="display: none;"></div>
            </div>
        </div>
    </div>
    <div class="row" style="margin: 10px 0 0 0">

        <button type="button" style='margin-top: 5px;margin-bottom: 5px;'
                class="button-submit" id="btn-show-edit-download">
            {{trans('common.usergroup_button_edit')}}
        </button>
        <button type="button" style='margin-top: 5px;margin-bottom: 5px;'
        class=" button-submit" id="btn-show-export-csv">
        {{trans('common.form_downloadmanagement_btn_csv_lbl')}}
        </button>

    </div>

@endsection
@section('footer')
    <script src="{{url('/js/modules/download.management.js')}}" charset="UTF-8"></script>
@endsection
