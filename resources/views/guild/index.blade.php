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
                                <th scope="col">Name</th>
                                <th scope="col">Guild ID</th>
                                <th scope="col">Created</th>
                                <th scope="col">Updated</th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($guilds as $guild)
                                <tr>
                                    <th scope="row">{{ $guild->id }}</th>
                                    <td>{{ $guild->name }}</td>
                                    <td>{{ $guild->guild_id }}</td>
                                    <td>{{ $guild->created_at->diffForHumans() }}</td>
                                    <td>{{ $guild->updated_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $guilds->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection