@extends('adminlte::page')

@section('title', 'Players')

@section('content_header')
    <h1>Players</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Players</h3>
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
                                        <th scope="col">IP</th>
                                        <th scope="col">Port</th>
                                        <th scope="col">Player</th>
                                        <th scope="col">Region</th>
                                        <th scope="col">Gamemode</th>
                                        <th scope="col">Coordinate</th>
                                        <th scope="col">Created</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($playerpings as $playerping)
                                        <tr>
                                            <th scope="row">{{ $playerping->id }}</th>
                                            <td>{{ $playerping->ip }}</td>
                                            <td>{{ $playerping->port }}</td>
                                            <td>{{ $playerping->player }}</td>
                                            <td>{{ $playerping->region }}</td>
                                            <td>{{ $playerping->gamemode }}</td>
                                            <td>{{ $playerping->coordinates }}</td>
                                            <td>{{ $playerping->created_at->diffForHumans() }}</td>
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