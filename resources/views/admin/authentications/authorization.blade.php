 @extends('admin')

@section('content')

<link href="{{ asset('/css/authorization.css') }}" rel="stylesheet">
<style>
    .ui-jqgrid-sortable{
        cursor: default !important;
    }
    .ui-state-default{
        color: #ffffff !important;
    }
</style>
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->

<div class="row">

    <div class="col-lg-12 ">
        <h2 class="page-header" style="padding-bottom: 16px">{{trans('common.authorization_title')}}</h2>
        <div class="panel panel-default">
	    <div class="form-group panel-body">
		<div class="col-md-4">
		<label >{{trans('common.authorization_lable_user_group')}}</label><br>
	         {!! Form::select('user_group_id', $userGroups,null,array('id' => 'search-select-usergroup','onchange'=>'authorization.loadUserGroup(this.value);','class'=>'form-control col-md-6')) !!}
		</div>

		{!! Form::button(trans('common.authorization_button_save'), ['class' => ' button-submit btn-authorization-save custom123','style'=>'margin:24px 0 0 0;' ]) !!}
	    {!! Form::button(trans('common.authorization_button_cancel'), ['class' => ' button-submit btn-forget-password custom123','onclick'=>'location.href=window.base_url;']) !!}
	    </div>
	    
	    </div>
	</div>
	
    <div class="col-md-12 ">
	    <table id="jqGrid"></table>
	    <div id="jqGridPager"></div>
	</div>
		
	{!! Form::open( ['url' => '/submit-authorization','method' => 'post', 'class'=>'form-horizontal frm-validation authorization-form','id'=>'authorization-form', 'name' => 'forget-password-form']) !!}
		<input type="hidden" name ="group_id" id="group_id"/>
         @foreach ($permissions as $permission)
		<input id="hidden_{{$permission}}"
               name = "{{$permission}}"
               value="false"
               type="hidden"
        />
         @endforeach

	{!! Form::close() !!}
			
</div>
				
@endsection

@section('footer')	
	<script src="{{url('/js/authorization.js')}}"></script>	
    <script>
		$( document ).ready(function() {
			authorization.loadGridData();
		});
    
        window.changinguser_password_comfirm_not_map ="{{ trans('common.changinguser_password_comfirm_not_map') }}"; 
    </script>
    <script>
    controlTabC('navbar-brand','btn-forget-password');
</script>
@endsection
