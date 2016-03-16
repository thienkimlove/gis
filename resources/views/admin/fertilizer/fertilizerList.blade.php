@extends('admin')

@section('content')
<style>
.color-box {
  border: 1px solid;
  height: 20px;
  width: 50px;
  float: left;
  margin-right: 15px;
}
.ui-jqgrid-sortable{
    cursor: default !important;
}
.ui-state-default{
    color: #ffffff !important;
}
</style>

<link href="{{ asset('/css/fertilizer.css') }}" rel="stylesheet">

<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->

    <div class="row">
        <div class="col-sm-12">
            <h2 class="page-header">{{trans('common.fertilizer_title')}}</h2>            
			<input type="hidden" id="pager-standard-crop-list">
			
            <input type="hidden" id="hidden-select" name ="hidden-select">
        </div>
    </div>
    {!! Form::open(array('route' => 'delete-fertilizers','method' => 'POST','class' => 'frm-validation-list-usergroup fertilizer-form')) !!}
    <div class="row">
        <div class="col-sm-3">
            <label class="color-box " style="background-color: #edf"> </label><span>{{trans('common.fertilizer_standard_system')}}</span>
            </div>
            <div class=" col-sm-3">
            <label class="color-box" style="background-color: #ffffff"> </label><span>{{trans('common.fertilizer_standard_normal')}}</span>
        </div>
        <div class="col-sm-12">
            <div class="table-responsive">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
                <div id="holdChecked" style="display: none;"></div>
            </div>
            <div class="row" style="margin: 10px 0 0 0">

          
					{!! Form::button(trans('common.button_add'),array('class' => 'fancybox-list-btn fancybox.ajax button-submit','href' => route('fertilizer-info'),'type' => 'button')) !!}

                <button type="button"class="btn-edit-fertilizer button-submit btn-form">
                    {{trans('common.button_edit')}}
                </button>
                <button type="button" class="btn-copy-fertilizer button-submit btn-form">
                    {{trans('common.button_copy_standard_crop')}}
                </button>
                <button type="button" class="btn-delete-fertilizer button-submit btn-form" >
                    {{trans('common.button_delete')}}
                </button>
                
				@if(session('user')->usergroup->auth_authorization)
                <button type="button" onclick="specifyUserForFertilization();" class="btn-specify-user button-submit btn-form"  >
                    {{trans('common.button_specify_user')}}
                </button>                
				@endif
				                
                <input name ="fertilizer_ids" id="fertilizer_ids" type="hidden">
            </div>
        </div>
        </div>
        {!! Form::close() !!}	
        {!! Form::open(array('route' => 'copy-fertilizer','method' => 'POST','class' => 'copy-fetilizer-frm')) !!}
        	<input type="hidden" name ="hidden_fertilizer_id" id="hidden_fertilizer_id">
        {!! Form::close() !!}	
@endsection

@section('footer')
	<script src="{{url('/js/fertilizer.js')}}"></script>
	<script src="{{url('/js/modules/specifyuser.js')}}"></script>	
	<script src="{{url('/js/modules/standardcropdetail.js')}}"></script>	
    <script>
        window.auth_authorization = "{{session('user')->usergroup->auth_authorization}}";
        fertilizer.loadGridData();
        resizeGrid('jqGrid','jqGridInfo','jqGridDetail');
    </script>
@endsection
