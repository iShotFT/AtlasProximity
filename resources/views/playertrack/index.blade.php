@extends('adminlte::page')

@section('title', 'Tracks')

@section('content_header')
    <h1>Tracks</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Tracks</h3>
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
                                        <th scope="col">Guild ID</th>
                                        <th scope="col">Channel ID</th>
                                        <th scope="col">Player</th>
                                        <th scope="col">Last Coordinate</th>
                                        <th scope="col">Last Direction</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Expires at</th>
                                        <th scope="col">Created</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($playertracks as $playertrack)
                                        <tr>
                                            <th scope="row">{{ $playertrack->id }}</th>
                                            <td>{{ $playertrack->guild_id }}</td>
                                            <td>{{ $playertrack->channel_id }}</td>
                                            <td>{{ $playertrack->player }}</td>
                                            <td>{{ $playertrack->last_coordinate }}</td>
                                            <td>{{ $playertrack->last_direction }}</td>
                                            <td>{{ $playertrack->last_status }}</td>
                                            <td>{{ $playertrack->until->diffForHumans() }}</td>
                                            <td>{{ $playertrack->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>

                                {{ $playertracks->links() }}
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
            $('#table').DataTable();
        });
    </script>
@stop