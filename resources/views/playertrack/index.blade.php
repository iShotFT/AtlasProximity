@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>Player Tracks</span>
                    </div>

                    <div class="card-body">

                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Guild ID</th>
                                <th scope="col">Channel ID</th>
                                <th scope="col">Player</th>
                                <th scope="col">Last Coordinate</th>
                                <th scope="col">Last Direction</th>
                                <th scope="col">Status</th>
                                <th scope="col">Expires at</th>
                                <th scope="col">Created</th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($playertracks as $playertrack)
                                <tr>
                                    <th scope="row">{{ $playertrack->id }}</th>
                                    <td>{{ $playertrack->guild_id }}</td>
                                    <td>{{ $playertrack->channel_id }}</td>
                                    <td>{{ $playertrack->player }}</td>
                                    <td>{{ $playertrack->last_coordinate }}</td>
                                    <td>{{ $playertrack->last_direction }}</td>
                                    <td>{{ $playertrack->last_status }}</td>
                                    <td>{{ $playertrack->until->diffForHumans() }}</td>
                                    <td>{{ $playertrack->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $playertracks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection