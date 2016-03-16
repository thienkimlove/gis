@extends('popup')

@section('content')
<link href="{{ asset('/css/specifyuser.css') }}" rel="stylesheet">
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->
{!! Form::open(array('route' => 'submit-specify-user','method' => 'post','class' => 'form-horizontal specify-user-frm')) !!}
	<div class ="row">	
		<h2 class="page-header">{{trans('common.specifyuser_title')}}</h2>
		<input type="hidden" id="hidden-select-info" name ="hidden-select-info" value ={{$standardUserCodes}}>
		<input type="hidden" id="selected_ids" name ="selected_ids">
	</div>
	<div class="row">
		<!-- 	Begin column 2 -->
        <div class="col-md-12 form-header-column">	        	
			<div class="form-group ">
				
				{!! Form::hidden('fertilizer-id',$fertilizerId, array('id' => 'fertilizer-id')) !!}
				<label class="specifyuser-label col-md-2 col-md-offset-1a">{{ trans('common.specifyuser_user_code') }}</label>							
				<div class="col-md-6">								
					{!! Form::text('specifyuser_user_code','', array('id' => 'specifyuser_user_code', 'maxlength'=>'18', 'class' => 'specifyuser_user_code form-control onlyNumeric ')) !!}
				</div>
			</div>
			
			<div class="form-group">
				<label class="specifyuser-label col-md-2 col-md-offset-1a">{{ trans('common.specifyuser_user_name') }}</label>
				<div class="col-md-6">					
					{!! Form::text('specifyuser_user_name','', array('id' => 'specifyuser_user_name', 'maxlength'=>'20', 'class' => 'form-control')) !!}									  
					  
				</div>
			</div>
			<div class="form-group">
				<label class="specifyuser-label col-md-2 col-md-offset-1a">{{ trans('common.authorization_lable_user_group') }}</label>
				<div class="col-md-6 specify-column">				
					
				         {!! Form::select('specifyuser_group_id', $userGroups,null,array('id' => 'specifyuser_group_id','class'=>'form-control col-md-6')) !!}
				</div>				  
					  
			</div>
			<div class="form-button">
				<div class="col-md-12">					
					<button class="button-spectify button-submit" onclick="specifyuser.refresh();" type="button">{{trans("common.button_search")}}</button>
				</div>
			</div>
			
		</div>		
		<!-- 	End column 2 -->
	</div>
	<div class ="row">				
		<table id="jqGridInfo"></table>
		<div id="jqGridPagerInfo"></div>
			
	</div>

    <div class ="row">
        <hr style="margin-bottom: 10px;">
        {!! Form::button(trans('common.button_save'),array('onclick'=>'specifyuser.saveSpecifyUser();','class' => ' btn-save-specify-user button-submit','type' => 'button')) !!}
		{!! Form::button(trans('common.button_cancel'),array('class' => ' button-submit btn-cancel-popup','type' => 'button','style'=>'margin:8px 0 0 0;')) !!}
			
	</div>
{!! Form::close() !!}
@endsection


@section('footer')
	
    <script>
        setTimeout(function() {
            $(".specifyuser_user_code").focus();
        }, 0);
        controlTabC('specifyuser_user_code','btn-cancel-popup');
        controlTabC('btn-save-specify-user','button-spectify');
    	specifyuser.loadGridData();

        window.standardUserCodes ="{{$standardUserCodes}}";
    </script>
@endsection