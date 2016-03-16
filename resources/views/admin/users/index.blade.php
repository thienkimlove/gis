@extends('admin')

@section('content')
    <style>
        .ui-jqgrid-sortable {
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">{{trans('common.user_registration_list_title')}}</h2>
        </div>
    </div>
    {!! Form::open(array('class' => 'form-horizontal frm-search')) !!}
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group col-xs-2">
                <label>{{ trans('common.user_registration_search_label_usercode') }}</label>
                {!! Form::text('code','',array('maxlength' => 9,'id' => 'search-txt-usercode','class' => 'form-control custom-input validate[custom[integer]]','data-errormessage-custom-error' =>  trans("common.user_registration_code_integer"))) !!}
            </div>
            <div class="form-group col-xs-2">
                            <label>{{ trans('common.user_registration_search_label_username') }}</label>
                            {!! Form::text('username','',array('id' => 'search-txt-username','class' => 'form-control custom-input')) !!}
                        </div>
            <div class="form-group col-xs-4">
                <label>{{ trans('common.user_registration_search_label_email') }}</label>
                {!! Form::text('email','',array('id' => 'search-txt-email','class' => 'form-control custom-input')) !!}
            </div>
            <div class="form-group col-xs-2">
                <label>{{ trans('common.user_registration_search_label_usergroup') }}</label>
                {!! Form::select('user_group_id', $userGroups, null, array(
                         'id' => 'search-select-usergroup',
                         'class'=>'form-control'
                )) !!}
            </div>
            <div class="form-group col-xs-2" style="margin-left: 0px;padding-left:0px;">
                <label>{{ trans('common.user_registration_search_label_lock_user') }}</label>
                {!! Form::select('user_locked_flg', $optionLocks,null,array(
                         'id' => 'search-select-userlock',
                         'class'=>'form-control'
                )) !!}
            </div>
            <div class="form-group ">
                {!! Form::button(trans('common.user_registration_search_label'),array(
                     'class' => 'btn-search-user button-submit',
                     'type' => 'button',
                     'style'=>'margin-left: 15px;margin-top: 23px;'
                )) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    {!! Form::open(array('route' => 'delete-user','method' => 'POST','class' => 'frm-validation-list-user')) !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
                <div id="holdChecked" style="display: none;"></div>
            </div>
            <div class="row" style="margin: 10px 0 0 0">

                <button type="button" style ='margin-top: 5px;margin-bottom: 5px;'
                        class="btn-show-create-user button-submit">
                    {{trans('common.user_list_label_button_create')}}
                </button>

                <button type="button" style ='margin-top: 5px;margin-bottom: 5px;'
                        class="btn-show-edit-user button-submit">
                    {{trans('common.user_list_label_button_edit')}}
                </button>
                <button type="button" style ='margin-top: 5px;margin-bottom: 5px;'
                        class="btn-delete-user button-submit">
                    {{trans('common.user_list_label_button_delete')}}
                </button>

            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@section('footer')
    <script src="{{url('/js/modules/user_list.js')}}"></script>
    <script>
    controlTabC('navbar-brand','btn-delete-user');
    controlTabC('btn-show-create-user','btn-search-user');
    </script>
@endsection
