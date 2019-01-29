@extends('adminlte::page')

@section('title', 'FAQ | New')

@section('content_header')
    <h1>Frequently Asked Questions | New</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create new frequently asked question</h3>
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
                                <form action="{{ route('faq.store') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="form-group">
                                        <label for="question">Question</label>
                                        <input type="text" class="form-control" id="question" name="question" aria-describedby="questionHelp" placeholder="Question">
                                        <small id="question" class="form-text text-muted">What is often asked?</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="answer">Answer</label>
                                        <textarea name="answer" id="answer" aria-describedby="answerHelp" cols="30" rows="10" class="form-control"></textarea>
                                        <small id="answerHelp" class="form-text text-muted">What is the answer to the often asked question?</small>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">Confirm</button>
                                    </div>
                                </form>
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