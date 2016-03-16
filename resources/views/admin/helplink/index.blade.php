@extends('admin') @section('content')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">{{trans('common.helplink_title_page')}}</h2>
	</div>
</div>

{!! Form::open(array(
      'route' => 'delete-helplink',
      'method' => 'post',
      'class' => 'frm-validation-list-helplink'
)) !!}

<table id="jqGrid"></table>
<div id="jqGridPager"></div>
<div id="holdChecked" style="display: none;"></div>


<div style="margin: 10px 0 0 0">


	{!! Form::button(trans('common.usergroup_button_create'),array(
       'class' => 'btn-show-create-helplink button-submit',
       'type' => 'button',
       'style'=> 'margin-top: 5px;margin-bottom: 5px;'
    )) !!}
	
	{!! Form::button(trans('common.usergroup_button_edit'),array(
         'class' => 'btn-show-edit-helplink button-submit',
         'type' => 'button',
         'style'=> 'margin-top: 5px;margin-bottom: 5px;'
    )) !!}
	
	{!! Form::button(trans('common.usergroup_button_delete'),array(
        'class' => 'btn-show-delete-helplink button-submit',
        'type' => 'button',
        'style'=> 'margin-top: 5px;margin-bottom: 5px;'
    )) !!}
</div>
{!! Form::close() !!}
@endsection @section('footer')
<script src="{{url('/js/modules/helplink_list.js')}}"></script>
<script>
    controlTabC('navbar-brand','btn-show-delete-helplink');
    controlTabC('btn-show-create-helplink','help-link-nav');
</script>
@endsection