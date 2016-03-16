@extends('admin')

@section('content')
    <style>
        .ui-jqgrid-sortable {
            /*cursor: default !important;*/
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">{{trans('common.usergroup_main')}}</h2>
        </div>
    </div>
    {!! Form::open(array('route' => 'delete-group','method' => 'POST','class' => 'frm-validation-list-usergroup')) !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
                <div id="holdChecked" style="display: none;"></div>
            </div>
            <div class="row" style="margin: 10px 0 0 0">

                <button type="button" style ='margin-top: 5px;margin-bottom: 5px;'
                        class="btn-show-create-usergroup button-submit">
                    {{trans('common.usergroup_button_create')}}
                </button>

                <button type="button" style ='margin-top: 5px;margin-bottom: 5px;'
                       class="btn-show-edit-usergroup button-submit">
                    {{trans('common.usergroup_button_edit')}}
                </button>
                <button type="button" style ='margin-top: 5px;margin-bottom: 5px;'
                        class="btn-show-delete-usergroup button-submit">
                    {{trans('common.usergroup_button_delete')}}
                </button>

            </div>
        </div>
        </div>
        {!! Form::close() !!}
        @endsection
        @section('footer')
            <script src="{{url('/js/modules/usergroup_list.js')}}"></script>
            <script>


                controlTabC('navbar-brand','btn-show-delete-usergroup');
            controlTabC('btn-show-create-usergroup','help-link-nav');
            </script>
@endsection
