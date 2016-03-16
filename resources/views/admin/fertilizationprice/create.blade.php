@extends('popup')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">{{trans('common.fertilization_price_create_title')}}</h2>
            {!! Form::model($price = new \StdClass, ['route' => ['admin.fertilizationprice.store'], 'class' => 'form-horizontal frm-validation-price']) !!}
            @include('admin.fertilizationprice.form', [
                'submitText' => trans('common.button_save'),
                'closeText' => trans('common.button_cancel')
                ])
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('footer')
    <script src="{{url('/js/modules/unit_price.js')}}"></script>
    <script>
        controlTabC('txt-group-name','btn-cancel-popup');
    </script>
@endsection