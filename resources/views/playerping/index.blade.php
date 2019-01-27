@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>Player Pings</span>
                    </div>

                    <div class="card-body">

                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">IP</th>
                                <th scope="col">Port</th>
                                <th scope="col">Player</th>
                                <th scope="col">Region</th>
                                <th scope="col">Gamemode</th>
                                <th scope="col">Coordinate</th>
                                <th scope="col">Created</th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($playerpings as $playerping)
                                <tr>
                                    <th scope="row">{{ $playerping->id }}</th>
                                    <td>{{ $playerping->ip }}</td>
                                    <td>{{ $playerping->port }}</td>
                                    <td>{{ $playerping->player }}</td>
                                    <td>{{ $playerping->region }}</td>
                                    <td>{{ $playerping->gamemode }}</td>
                                    <td>{{ $playerping->coordinates }}</td>
                                    <td>{{ $playerping->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $playerpings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection