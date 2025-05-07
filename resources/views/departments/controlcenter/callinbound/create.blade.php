@extends('layouts.app')

@section('content')
<a href="{{ route('departments.controlcenter') }}" class="btn btn-secondary mb-3">
    ← Back to Control Center Dashboard
</a>

<div class="container">
    <h2 class="text-primary">Add New Call Inbound</h2>

    <form method="POST" action="{{ route('controlcenter.callinbound.store') }}">
        @csrf

        <div class="mb-3">
            <label for="caller_name" class="form-label">Caller Name</label>
            <input type="text" name="caller_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label for="call_time" class="form-label">Call Time</label>
            <input type="datetime-local" name="call_time" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" name="category" class="form-control">
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label for="department_referred" class="form-label">Department Referred</label>
            <input type="text" name="department_referred" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Submit</button>
    </form>
</div>
@endsection
