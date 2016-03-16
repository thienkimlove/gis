@extends('popup')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">{{trans('common.usergroup_create_title')}}</h2>
            {!! Form::model($group = new \StdClass, ['route' => ['admin.groups.store'], 'class' => 'form-horizontal frm-validation-login']) !!}
            @include('admin.usergroup.form', [
                'submitText' => trans('common.usergroup_add_title'),
                'closeText' => trans('common.usergroup_add_close_title')
                ])
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('footer')
    <script src="{{url('/js/modules/usergroup_form.js')}}"></script>
    <script>
    controlTabC('txt-group-name','btn-cancel-popup');
    </script>
@endsection