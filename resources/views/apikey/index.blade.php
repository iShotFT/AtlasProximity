@extends('adminlte::page')

@section('title', 'API Keys')

@section('content_header')
    <h1>API Keys</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">API Keys</h3>
                        <div class="box-tools pull-right">
                            <a href="{{ route('apikey.create') }}" class="btn btn-box-tool">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="table" style="width: 100%;">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Key</th>
                                        <th scope="col">Created</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($api_keys as $api_key)
                                        <tr>
                                            <th scope="row">{{ $api_key->id }}</th>
                                            <td>{{ $api_key->title }}</td>
                                            <td>{{ $api_key->key }}</td>
                                            <td>{{ $api_key->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('apikey.get.destroy', ['id' => $api_key->id]) }}">X</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>

                                {{ $api_keys->links() }}
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
{{--@extends('layouts.app')--}}