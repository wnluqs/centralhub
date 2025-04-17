@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Back to Main Dashboard (Already in your example) -->
    <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
        ← Back to Main Dashboard
    </a>

    <h1>Need Help? Contact Support!</h1>

    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif

    <!-- The Support Form -->
    <form action="{{ route('support.submit') }}" method="POST" class="mt-4">
        @csrf
        <div class="form-group">
            <label for="message">Your Message:</label>
            <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>

    {{-- Optional: Only show the "View Support Requests" button if the current user is an Admin.
    You might use:
    @if(Auth::check() && Auth::user()->hasRole('Admin'))
    or
    @can('view support requests')
    or
    @if(Auth::user()->role == 'Admin')
    --}}
    @hasrole('Admin')
    <hr />
    <a href="{{ route('admin.support.index') }}" class="btn btn-warning">
        View All Support Requests (Admin)
    </a>
    @endhasrole
</div>
@endsection