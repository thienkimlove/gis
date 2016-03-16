@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Welcome Zukosha.
                </div>
            </div>

        </div>
        <!-- /.col-lg-8 -->
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> Notifications Panel
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item">
                            <i class="fa fa-comment fa-fw"></i> Example Notifcationi
                                    <span class="pull-right text-muted small"><em>4 minutes ago</em>
                                    </span>
                        </a>
                    </div>
                    <!-- /.list-group -->
                </div>
                <!-- /.panel-body -->
            </div>

        </div>
        <!-- /.col-lg-4 -->
    </div>
@endsection