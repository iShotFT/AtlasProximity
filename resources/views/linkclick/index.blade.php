@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>Link Clicks</span>
                    </div>

                    <div class="card-body">

                        <table class="table">
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
                </div>
            </div>
        </div>
    </div>
@endsection