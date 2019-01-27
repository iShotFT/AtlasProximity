@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>Pings</span>
                    </div>

                    <div class="card-body">

                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">IP</th>
                                <th scope="col">Port</th>
                                <th scope="col">Region</th>
                                <th scope="col">Gamemode</th>
                                <th scope="col">Coordinate</th>
                                <th scope="col">Online</th>
                                <th scope="col">Players</th>
                                <th scope="col">Created</th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($pings as $ping)
                                <tr>
                                    <th scope="row">{{ $ping->id }}</th>
                                    <td>{{ $ping->ip }}</td>
                                    <td>{{ $ping->port }}</td>
                                    <td>{{ $ping->region }}</td>
                                    <td>{{ $ping->gamemode }}</td>
                                    <td>{{ $ping->coordinates }}</td>
                                    <td>{{ $ping->online }}</td>
                                    <td>{{ $ping->players }}</td>
                                    <td>{{ $ping->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $pings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection