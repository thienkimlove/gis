@extends('popup')

@section('content')



  <script>
  
  </script>
  
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->
{!! Form::open(array('route' => 'submit-standard-crop-details','method' => 'post','class' => 'form-horizontal standard-crop-details-frm')) !!}
	
<div onclick="standardcropdetail.saveGrid();">
	<div class ="row">	
		<h2 class="page-header">{{trans('common.standardcropdetail_title')}}</h2>
		<input type="hidden" id="standard_crop_id" name ="standard_crop_id" value ="{{$standardCropId}}" />
		<input type="hidden" id="data" name ="data" />
		<input type="hidden" id="full_data" name ="full_data" />
	</div>
	<div class ="row">				
		<table class="smallint" id="jqGridDetail" onclick = "standardcropdetail.grid.isEditing = true;"></table>
		<div id="jqGridPagerInfo"></div>
			
	</div>
	<div class ="row">
    <hr>
		<button onclick='standardcropdetail.submitGrid();' type="button"class=" btn-save-standard-crop-detail  button-submit btn-form">
        	{{trans('common.button_save')}}                
        </button>
        <button  type="button" class="btn-cancel-popup button-submit btn-form">
            {{trans('common.button_cancel')}}
        </button>
        
	</div>
</div>
{!! Form::close() !!} 
@endsection


@section('footer')	
    <script>
        setTimeout(function(){
            $('.btn-save-standard-crop-detail').focus();
        },0);
        controlTabC('btn-save-standard-crop-detail','btn-cancel-popup');
        standardcropdetail.loadGridData();
    </script>
@endsection