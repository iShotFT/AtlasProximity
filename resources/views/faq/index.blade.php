@extends('adminlte::page')

@section('title', 'FAQ')

@section('content_header')
    <h1>Frequently Asked Questions</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Frequently Asked Questions</h3>
                        <div class="box-tools pull-right">
                            <a href="{{ route('faq.create') }}" class="btn btn-box-tool">
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
                                        <th scope="col">Question</th>
                                        <th scope="col">Answer</th>
                                        <th scope="col">Added</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($faqs as $faq)
                                        <tr>
                                            <th scope="row">{{ $faq->id }}</th>
                                            <td>{{ $faq->question }}</td>
                                            <td>{{ $faq->answer }}</td>
                                            <td>{{ $faq->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('faq.get.destroy', ['id' => $faq->id]) }}">X</a>
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