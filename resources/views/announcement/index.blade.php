@extends('adminlte::page')

@section('title', 'Announcement')

@section('content_header')
    <h1>Announcement</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Announcement</h3>
                        <div class="box-tools pull-right">
                            <a href="{{ route('announcement.create') }}" class="btn btn-box-tool">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="table" style="width: 100%">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Message</th>
                                        <th scope="col">Channels</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($announcements as $announcement)
                                        <tr>
                                            <th scope="row">{{ $announcement->id }}</th>
                                            <td>{{ $announcement->title }}</td>
                                            <td>{{ $announcement->message }}</td>
                                            <td>{{ count($announcement->channels) }}</td>
                                            <td>{{ $announcement->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>


                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- ./box-body -->
                </div>

                {{--<div class="card">--}}
                {{--<div class="card-header d-flex justify-content-between">--}}
                {{--<span>API keys</span>--}}
                {{--</div>--}}

                {{--<div class="card-body">--}}
                {{--</div>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                'order': [[0, 'desc']],
                'pageLength': 100,
            });
        });
    </script>
@stop