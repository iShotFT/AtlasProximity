@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>Updates</span>
                        <a href="{{ route('update.create') }}" class="btn btn-success">New update</a>
                    </div>

                    <div class="card-body">

                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Full Version</th>
                                <th scope="col">Changes</th>
                                <th scope="col">Added</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($updates as $update)
                                <tr>
                                    <th scope="row">{{ $update->id }}</th>
                                    <td>{{ $update->full_version }}</td>
                                    <td>{{ $update->changes }}</td>
                                    <td>{{ $update->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('update.get.destroy', ['id' => $update->id]) }}">X</a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $updates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection