@extends('admin')

@section('content')
  @include('admin.post.heading')
  <div class="row">
      <div class="col-lg-6">
          <h2>Edit "{{ $post->title }}"</h2>
          {!! Form::model($post, ['method' => 'PATCH', 'route' => ['posts.update', $post->id]]) !!}
              @include('admin.post.form', ['submitText' => 'Edit'])
          {!! Form::close() !!}
          @include('errors.list')

      </div>
  </div>
@stop