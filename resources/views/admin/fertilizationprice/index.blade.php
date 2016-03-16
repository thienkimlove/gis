@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">{{trans('common.fertilization_price_main')}}</h2>
        </div>
    </div>
    {!! Form::open(array('route' => 'fertilization-price/delete-fertilization','method' => 'POST','class' => 'frm-validation-list-price')) !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
                <div id="holdChecked" style="display: none;"></div>
            </div>
            <div class="row" style="margin: 10px 0 0 0">

                <button type="button" style ='margin-top: 5px;margin-bottom: 5px;'
                        class="btn-show-create-price button-submit">
                    {{trans('common.button_add')}}
                </button>

                <button type="button" style ='margin-top: 5px;margin-bottom: 5px;'
                        class="btn-show-edit-price button-submit">
                    {{trans('common.button_edit')}}
                </button>
                <button type="button" style ='margin-top: 5px;margin-bottom: 5px;'
                        class="btn-show-delete-price button-submit">
                    {{trans('common.button_delete')}}
                </button>

            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@section('footer')
    <script src="{{url('/js/modules/fertilization_price_list.js')}}"></script>
@endsection
