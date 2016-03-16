
	<div class="row">
	    <div class="col-lg-12">
	        <h1 class="page-header">{{trans('common.user_registration_list_title')}}</h1>
	    </div>
	</div>
	<div class="row">
	    <div class="col-lg-12">
	    	{!! Form::open(array('route' => 'search-users','method' => 'post','name' => 'search-frm','class' => 'frm-validation-search')) !!} 
	        	<table>
	        		<tr>
	        			<td>
	        				{!! Form::button(trans('common.user_registration_search_label'),array('class' => 'btn-search-user button-submit','type' => 'button')) !!}
	        			</td>
	        			<td>
	        				<label>{{ trans('common.user_registration_search_label_username') }}</label>
	        				{!! Form::text('username','',array('maxlength' => 20,'id' => 'txtUserName','class' => 'custom-input validate[required,maxSize[20],custom[onlyLetterNumber]]','data-errormessage-range-overflow' =>  trans("common.login_username_max"),'data-errormessage-custom-error' =>  trans("common.login_username_alpha_num"),'data-errormessage-value-missing' =>  trans("common.login_username_required")  )) !!}
	        			</td>
	        			<td>
	        				<label>{{ trans('common.user_registration_search_label_usergroup') }}</label>
	        				{!! Form::select('user_group_id', $userGroups) !!}
	        			</td>
	        		</tr>
	        		
	        		<tr>
	        			<td></td>
	        			<td>
	        				<label>{{ trans('common.user_registration_search_label_lock_user') }}</label>
	        				{!! Form::checkbox('user_locked_flg',false,false) !!}
	        			</td>
	        			<td>
	        				<label>{{ trans('common.user_registration_search_label_unlock_user') }}</label>
	        				{!! Form::checkbox('user_locked_flg',false,true) !!}
	        			</td>
	        		</tr>
	        	</table>
			{!! Form::close() !!}
	    </div>
	</div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"></div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th colspan="2">{{trans('common.user_registration_head_username')}}</th>
                                <th>{{trans('common.user_registration_head_usercode')}}</th>
                                <th>{{trans('common.user_registration_head_usergroup')}}</th>
                                <th>{{trans('common.user_registration_head_userlock')}}</th>
                                <th>{{trans('common.user_registration_head_last_login')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            	@if(!empty($users))
                            		@foreach($users as $user)
			                            <tr>
			                           		<td width="2%">
			                           			<input type="checkbox" />
											</td>
			                           		<td>
			                           			<label>
			                           				{{ $user->username }}
			                           			</label>
			                           		</td>
			                           		<td>
			                           			<label>
			                           				{{ $user->code }}
			                           			</label>
			                           		</td>
			                           		<td><label>{{ $user->usergroup->group_name }}</label></td>
			                           		<td>{!! Form::checkbox('user_locked_flg', $user->user_locked_flg, $user->user_locked_flg) !!} </td>
			                           		<td><label>{{ date("d/d/Y H:i",strtotime($user->last_logout_time)) }}</label></td>
										</tr>
									@endforeach
								@endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">{!!$users->render()!!}</div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>

    </div>