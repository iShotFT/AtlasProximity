@extends('adminlte::page')

@section('title', 'Link Clicks')

@section('content_header')
    <h1>Link Clicks</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Link Clicks</h3>
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
                                        <th scope="col">Useragent</th>
                                        <th scope="col">Source</th>
                                        <th scope="col">Created</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($linkclicks as $linkclick)
                                        <tr>
                                            <th scope="row">{{ $linkclick->id }}</th>
                                            <td>{{ $linkclick->ip }}</td>
                                            <td>{{ $linkclick->useragent }}</td>
                                            <td>{{ $linkclick->source ?? 'none' }}</td>
                                            <td>{{ $linkclick->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>

                                {{ $linkclicks->links() }}
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