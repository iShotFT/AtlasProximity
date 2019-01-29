@extends('adminlte::page')

@section('title', 'Updates | New')

@section('content_header')
    <h1>Updates | New</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create new update</h3>
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
                                <form action="{{ route('update.store') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="form-group">
                                        <label for="version">Version</label>
                                        <input type="number" class="form-control" id="version" name="version" aria-describedby="versionHelp" placeholder="Version" value="{{ $previous_update ? $previous_update->version : '' }}">
                                        <small id="versionHelp" class="form-text text-muted">Previous version: {{ $previous_update ? $previous_update->version : 'none' }}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="major">Major</label>
                                        <input type="number" class="form-control" id="major" name="major" aria-describedby="majorHelp" placeholder="Major" value="{{ $previous_update ? $previous_update->major : '' }}">
                                        <small id="majorHelp" class="form-text text-muted">Previous major: {{ $previous_update ? $previous_update->major : 'none' }}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="minor">Minor</label>
                                        <input type="number" class="form-control" id="minor" name="minor" aria-describedby="minorHelp" placeholder="Minor" value="{{ $previous_update ? $previous_update->minor + 1 : '' }}">
                                        <small id="minorHelp" class="form-text text-muted">Previous minor: {{ $previous_update ? $previous_update->minor : 'none' }}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="changes">Changes</label>
                                        <textarea name="changes" id="changes" aria-describedby="changesHelp" cols="30" rows="10" class="form-control">
__New command(s):__
*none*

__General change(s):__
*none*

__Extra(s):__
*none*</textarea>
                                        <small id="changesHelp" class="form-text text-muted">What has changed since last version?</small>
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