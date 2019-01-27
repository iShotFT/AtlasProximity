@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
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
                                <th scope="col">Created At</th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($updates as $update)
                                <tr>
                                    <th scope="row">{{ $update->id }}</th>
                                    <td>{{ $update->full_version }}</td>
                                    <td>{{ $update->changes }}</td>
                                    <td>{{ $update->created_at }}</td>
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