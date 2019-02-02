@extends('adminlte::page')

@section('title', 'Commands')

@section('content_header')
    <h1>Commands</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Commands</h3>
                        {{--<div class="box-tools pull-right">--}}
                        {{--<a href="{{ route('faq.create') }}" class="btn btn-box-tool">--}}
                        {{--<i class="fa fa-plus"></i>--}}
                        {{--</a>--}}
                        {{--</div>--}}
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="table" style="width: 100%">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Command</th>
                                        <th scope="col">Arguments</th>
                                        <th scope="col">Guild ID</th>
                                        <th scope="col">Channel</th>
                                        <th scope="col">Created</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($commands as $command)
                                        <tr>
                                            <th scope="row">{{ $command->id }}</th>
                                            <td>{{ $command->user }}</td>
                                            <td>{{ $command->command }}</td>
                                            <td>{!! $command->arguments ?? '<i>none</i>' !!}</td>
                                            <td>{{ $command->guild_name }}</td>
                                            <td>{{ $command->channel_name }}</td>
                                            <td>{{ $command->created_at->diffForHumans() }}</td>
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