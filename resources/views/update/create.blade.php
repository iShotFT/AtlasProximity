@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Updates</div>

                    <div class="card-body">
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
                                <textarea name="changes" id="changes" aria-describedby="changesHelp" cols="30" rows="10" class="form-control"></textarea>
                                <small id="changesHelp" class="form-text text-muted">What has changed since last version?</small>
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