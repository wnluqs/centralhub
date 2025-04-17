@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Admin Approval for WFH Requests</h1>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($requests->isEmpty())
    <p>No WFH requests available.</p>
    @else
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Date</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Approval Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>{{ $request->user_id }}</td>
                <td>{{ $request->date }}</td>
                <td>{{ $request->reason }}</td>
                <td>{{ $request->status }}</td>
                <td>
                    @if($request->status === 'Pending')
                    <form action="{{ route('wfh.approve', $request) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                    </form>
                    <form action="{{ route('wfh.reject', $request) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="text" name="approval_notes" placeholder="Rejection Note" required>
                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                    @else
                    <em>No action available</em>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection