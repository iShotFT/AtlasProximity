@extends('adminlte::page')

@section('title', 'Updates')

@section('content_header')
    <h1>Updates</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Updates</h3>
                        <div class="box-tools pull-right">
                            <a href="{{ route('update.create') }}" class="btn btn-box-tool">
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
                                        <th scope="col">Full Version</th>
                                        <th scope="col">Changes</th>
                                        <th scope="col">Added</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($updates as $update)
                                        <tr>
                                            <th scope="row">{{ $update->id }}</th>
                                            <td>{{ $update->full_version }}</td>
                                            <td>{{ $update->changes }}</td>
                                            <td>{{ $update->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('update.get.destroy', ['id' => $update->id]) }}">X</a>
                                            </td>
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