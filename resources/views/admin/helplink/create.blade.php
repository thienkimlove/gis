@extends('popup')

@section('content')

    {!! Form::model($helplink = new \StdClass, array(
       'url' => 'helplink',
       'method' => 'post',
       'class' => 'form-horizontal frm-validation form-edit-helplink'
    )) !!}

    <div class="row">
        <div class="col-md-12">
            <h2 class="page-header">{{trans('common.helplink_create_list_title')}}</h2>
            @include('admin.helplink.form')
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('footer')
<script src="{{url('/js/modules/helplink_form.js')}}"></script>
<script>
    controlTabC('txt-helplink','btnCancelHelplink');
</script>
@endsection