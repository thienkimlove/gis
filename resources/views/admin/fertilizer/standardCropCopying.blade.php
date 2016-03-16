@extends('popup')

@section('content')
<link href="{{ asset('/css/standard-crop-info.css') }}" rel="stylesheet">
<!-- Source//https://github.com/posabsolute/jQuery-Validation-Engine -->
{!! Form::open(array('route' => 'submit-standard-crop-copying','method' => 'post','class' => 'form-horizontal standard-crop-copying-frm')) !!}
	<div class ="row">	
		<h2 class="page-header">{{trans('common.standardcropcopying_title')}}</h2>
		<input type="hidden" name="standard_crop_id" value = "{{$standardCrop->id}}" />
	</div>
	<div class="row">	
		<!-- 	Begin column 2 -->
        <div class="col-md-12">
			<div class="form-group ">
				<label class="standard-crop-label col-md-3 col-md-offset-1">{{ trans('common.standardcropcopying_crop') }}</label>
				<div class="col-md-8">
					{!! Form::text('crops_name',$standardCrop->crop->crops_name, array('id' => 'crops_name', 'maxlength'=>'20','readonly'=>'true', 'class' => 'form-control  '
						
					 )) !!}														
				</div>
			</div>
			<div class="form-group">
				<label class="standard-crop-label col-md-3 col-md-offset-1">{{ trans('common.standardcropcopying_to_standard') }}</label>
				<div class="col-md-8">
                {!! Form::select('destination_standard_id',$listFertilizer,null,array(
                        'id' => 'destination_standard_id',
                        'class' => 'form-control validate[required]',
                    'data-errormessage-value-missing' =>  trans('common.required')
                    )) !!}

				</div>
			</div>
			
		</div>		
		<!-- 	End column 2 -->
	</div><hr>
	<div style="float: right;">
		{!! Form::button(trans('common.button_execute'),array('class' => 'standard_crop_copying_save_button button-submit','type' => 'button')) !!}
		{!! Form::button(trans('common.button_cancel'),array('class' => ' button-submit btn-cancel-popup','type' => 'button','style'=>'margin:8px 0 0 0;')) !!}
			
	</div>
{!! Form::close() !!}
@endsection


@section('footer')
	<script src="{{url('/js/modules/standardcropinfo.js')}}"></script>	
    <script>
        controlTabC('search-box-fertilizer','btn-cancel-popup');
    </script>
@endsection