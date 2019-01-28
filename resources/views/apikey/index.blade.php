@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>API keys</span>
                        <a href="{{ route('apikey.create') }}" class="btn btn-success">New key</a>
                    </div>

                    <div class="card-body">

                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Title</th>
                                <th scope="col">Key</th>
                                <th scope="col">Created</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($api_keys as $api_key)
                                <tr>
                                    <th scope="row">{{ $api_key->id }}</th>
                                    <td>{{ $api_key->title }}</td>
                                    <td>{{ $api_key->key }}</td>
                                    <td>{{ $api_key->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('apikey.get.destroy', ['id' => $api_key->id]) }}">X</a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $api_keys->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection