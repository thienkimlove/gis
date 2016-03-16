@extends('admin')

@section('content')

    <h2 class="page-header"> {{trans('common.import_layer_map_to_destination_title')}}</h2>
    {!! Form::open(['class'=>'form-horizontal frm-validation col-lg-offset-4','method'=>'post'])!!}
    @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif
    <div class="form-group">
        {!! Form::label('folder_id', trans('common.lbl_import_layer_map_to_destination_layer_map') ) !!}
            <span class="custom-dropdown custom-dropdown--white">
                <select class="custom-dropdown-select custom-dropdown-select-white validate[required]"
                        id="fertility_id" name="fertility_id">
                    <option value="" selected="selected">{{ trans('common.select_import_layer_map_to_destination_map_name') }}</option>
                        @foreach($fertility_map as $fp)
                            <option value="{{ $fp->id }}" >{{ $fp->map_name }}</option>
                        @endforeach
                </select>
            </span>
    </div>
    <div class="form-group">
        {!! Form::label('folder_id', trans('common.lbl_import_layer_map_to_destination_layer_folder') ) !!}
            <span class="custom-dropdown custom-dropdown--white">
                <select class="custom-dropdown-select custom-dropdown-select-white validate[required]"
                        id="folder_id" name="folder_id">
                    <option value="" selected="selected">{{ trans('common.select_import_layer_map_to_destination_folder_name') }}</option>
                    @foreach($folder as $f)
                        <option value="{{ $f->id }}" >{{ $f->name }}</option>
                    @endforeach
                </select>
            </span>
    </div>
    <div class="form-group">
        {!! Form::label('folder_id', trans('common.lbl_import_layer_map_to_destination_layer_name'),
        ['class'=>'col-md-3','style'=>'padding:0']) !!}
        {!! Form::text('layer_name', null, ['class' => 'validate[required] col-md-5', 'readonly' ,'id' => 'layer_name']) !!}
    </div>
    <div class="form-group">
        {!! Form::submit('Save',array('class' => 'button-submit')) !!}
        {!! Form::reset('Cancel',array('class' => 'button-submit')) !!}
    </div>

    {!! Form::close()!!}

@stop
@section('footer')
    <script src="{{url('/js/moment.min.js')}}"></script>
    <script src="{{url('/js/modules/import_map_to_folder.js')}}"></script>
@endsection
