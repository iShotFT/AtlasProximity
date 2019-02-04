@extends('adminlte::page')

@section('title', 'Announcement | New')

@section('content_header')
    <h1>Frequently Asked Questions | New</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create new announcement</h3>
                        <div class="box-tools pull-right">
                            <a href="{{ route('announcement.create') }}" class="btn btn-box-tool">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <form action="{{ route('announcement.store') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" aria-describedby="titleHelp" placeholder="Title" value=":warning: :loudspeaker: **Announcement**">
                                        <small id="titleHelp" class="form-text text-muted">Title for the announcement</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="message">Message</label>
                                        <textarea name="message" id="message" aria-describedby="messageHelp" cols="30" rows="10" class="form-control"></textarea>
                                        <small id="messageHelp" class="form-text text-muted">What would you like to announce?</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="channels">Channels</label>
                                        <input type="text" class="form-control" id="channels" name="channels" aria-describedby="channelsHelp" placeholder="Channel IDs">
                                        <small id="channelsHelp" class="form-text text-muted">Seperated by comma space</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="mention">Mention</label>
                                        <input type="text" class="form-control" id="mention" name="mention" aria-describedby="mentionHelp" placeholder="User ID">
                                        <small id="mentionHelp" class="form-text text-muted">What user should we mention in the message? (Only works correctly when sending to one channel)</small>
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