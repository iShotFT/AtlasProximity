@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>Proximity Tracks</span>
                    </div>

                    <div class="card-body">

                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Guild ID</th>
                                <th scope="col">Channel ID</th>
                                <th scope="col">Coordinate</th>
                                <th scope="col">Created</th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($proximitytracks as $proximitytrack)
                                <tr>
                                    <th scope="row">{{ $proximitytrack->id }}</th>
                                    <td>{{ $proximitytrack->guild_id }}</td>
                                    <td>{{ $proximitytrack->channel_id }}</td>
                                    <td>{{ $proximitytrack->coordinate }}</td>
                                    <td>{{ $proximitytrack->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $proximitytracks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection