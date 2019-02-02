@extends('layouts.landing')

@section('content')
    <div class="d-flex justify-content-center align-items-center flex-column">
        <h1>Coming soon</h1>
        <a href="{{ url('/docs') }}" class="btn btn-success">Get the bot installed using our documentation page</a>
        <a href="https://discord.gg/KMHkqtb" class="btn btn-info">Join our Discord for info & updates</a>
    </div>
@stop