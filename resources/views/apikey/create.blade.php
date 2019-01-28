@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">API Keys</div>

                    <div class="card-body">
                        <form action="{{ route('apikey.store') }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" aria-describedby="titleHelp" placeholder="Title">
                                <small id="title" class="form-text text-muted">Give your API key a recognisable name</small>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection