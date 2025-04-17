@extends('layouts.app')

@section('content')
<div class="container text-center" style="padding:50px;">
    <h1 class="display-4">Oops! Looks like you walk into a locked website.</h1>
    <p class="lead">
        You don’t have the required access to view this department.
        Please ask the admin for further support or follow the steps below.
    </p>

    <!-- Funny GIF (can randomize if you wish) -->
    <img src="https://media3.giphy.com/media/8abAbOrQ9rvLG/giphy.gif" alt="Access Denied"
        style="max-width: 400px; margin: 20px auto; display:block;" />

    <div class="alert alert-warning mt-4" role="alert">
        <strong>Need access?</strong> Here are some ways to get help:
    </div>
    <ul class="list-unstyled">
        <li><i class="bi bi-envelope-fill"></i> Please Email to luqskywalker@force.com</li>
        <li>
            <button type="button" onclick="window.location.href='{{ route('support.form') }}'">
                Chat with Support
            </button>
        </li>
        {{-- <li><i class="bi bi-file-earmark-person-fill"></i> <a href="{{ route('request.access') }}">Request
                Access</a> --}}
        </li>
    </ul>

    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-primary me-2">Back to Dashboard</a>
        {{-- <a href="{{ route('help.faq') }}" class="btn btn-outline-secondary me-2">Visit FAQ</a> --}}
        {{-- <a href="{{ route('support.form') }}" class="btn btn-outline-warning">Contact Support</a> --}}
    </div>
</div>
@endsection