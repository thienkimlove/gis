@extends('admin')

@section('content')
    @include('admin.post.heading')
    <div class="row">
        <div class="col-lg-6">
            <h2>Add New Post</h2>
            {!! Form::model($post = new \Gis\Models\Entities\Post, ['route' => ['posts.store'], 'files' => true]) !!}
            @include('admin.post.form', ['submitText' => 'Add Post'])
            {!! Form::close() !!}
            @include('errors.list')

        </div>
    </div>
@stop