@extends('admin')
@section('content')
    @include('admin.post.heading')
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
                                <th>#</th>
                                <th>Title</th>
                                <th>Desc</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($posts as $post)
                                <tr>
                                    <td>{{$post->id}}</td>
                                    <td><a href="{{url('admin/posts/'.$post->id)}}">{{$post->title}}</a></td>
                                    <td>{{$post->desc}}</td>
                                    <td>{{($post->status) ? 'Yes' : 'No'}}</td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="{{url('posts/'.$post->id.'/edit')}}" type="button">Edit</a>&nbsp;
                                        <br>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['posts.destroy', $post->id]]) !!}
                                        <button type="submit" class="btn btn-danger btn-mini">Delete</button>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">{!!$posts->render()!!}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="{{url('posts/create')}}">Create</a>
                        </div>
                    </div>


                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>

    </div>
@endsection