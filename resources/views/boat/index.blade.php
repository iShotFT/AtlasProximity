@extends('adminlte::page')

@section('title', 'Boats')

@section('content_header')
    <h1>Boats</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Boats</h3>
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
                                        <th scope="col">From</th>
                                        <th scope="col">To</th>
                                        <th scope="col">Players</th>
                                        <th scope="col">Guild ID</th>
                                        <th scope="col">Channel ID</th>
                                        <th scope="col">Created</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($boats as $boat)
                                        <tr>
                                            <th scope="row">{{ $boat->id }}</th>
                                            <td>{{ $boat->from }}</td>
                                            <td>{{ $boat->coordinate }}</td>
                                            <td>{{ $boat->count }}</td>
                                            <td>{{ $boat->guild_name }}</td>
                                            <td>{{ $boat->channel_id }}</td>
                                            <td>{{ $boat->created_at->diffForHumans() }}</td>
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
            });
        });
    </script>
@stop