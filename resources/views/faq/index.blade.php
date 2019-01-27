@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>Frequently Asked Questions</span>
                        <a href="{{ route('faq.create') }}" class="btn btn-success">New faq</a>
                    </div>

                    <div class="card-body">

                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Question</th>
                                <th scope="col">Answer</th>
                                <th scope="col">Added</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($faqs as $faq)
                                <tr>
                                    <th scope="row">{{ $faq->id }}</th>
                                    <td>{{ $faq->question }}</td>
                                    <td>{{ $faq->answer }}</td>
                                    <td>{{ $faq->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('faq.get.destroy', ['id' => $faq->id]) }}">X</a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $faqs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection