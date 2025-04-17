@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Work From Home Request</h2>

    <!-- If the current user is an Admin, show a link to the Admin Dashboard -->
    {{-- @if(auth()->check() && auth()->user()->role === 'Admin') --}}
    <div class="row">
        <div class="col-md-4 mb-4">
            <a href="{{ route('wfh.admin') }}" class="dashboard-card">
                <img src="{{ asset('images/hr/admin.png') }}" alt="Admin Icon" style="width: 100px;">
                <h5>Admin WFH Dashboard</h5>
            </a>
        </div>
    </div>
    {{-- @endif --}}

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('wfh.store') }}" method="POST" class="mb-4 card card-body">
        @csrf
        <div class="mb-3">
            <label style="color: green">Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label style="color: green">Reason</label>
            <textarea name="reason" rows="3" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit WFH Request</button>
    </form>

    <h4>My WFH Requests</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Approval Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>{{ $request->date }}</td>
                <td>{{ $request->reason }}</td>
                <td>
                    <span
                        class="badge bg-{{ $request->status == 'Approved' ? 'success' : ($request->status == 'Rejected' ? 'danger' : 'secondary') }}">
                        {{ $request->status }}
                    </span>
                </td>
                <td>{{ $request->approval_notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection