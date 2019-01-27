@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Updates</div>

                    <div class="card-body">
                        <form action="{{ route('faq.store') }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="question">Question</label>
                                <input type="text" class="form-control" id="question" name="question" aria-describedby="questionHelp" placeholder="Question">
                                <small id="question" class="form-text text-muted">What is often asked?</small>
                            </div>
                            <div class="form-group">
                                <label for="answer">Answer</label>
                                <textarea name="answer" id="answer" aria-describedby="answerHelp" cols="30" rows="10" class="form-control"></textarea>
                                <small id="answerHelp" class="form-text text-muted">What is the answer to the often asked question?</small>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection