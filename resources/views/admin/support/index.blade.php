@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Support Requests</h1>

    {{-- Display success messages if any --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    {{-- Check if there are any support requests --}}
    @if($supportRequests->count())
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Message</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supportRequests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>
                    {{ $request->user ? $request->user->name : 'Guest' }}
                </td>
                <td>{{ $request->message }}</td>
                <td>
                    @if($request->is_read)
                    <span class="badge bg-success">Read</span>
                    @else
                    <span class="badge bg-warning text-dark">Unread</span>
                    @endif
                </td>
                <td>{{ $request->created_at->format('d M Y, H:i') }}</td>
                <td>
                    {{-- Example Action: Mark as read (if unread) --}}
                    @if(!$request->is_read)
                    <form action="{{ route('admin.support.markRead', $request->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm btn-primary">Mark as Read</button>
                    </form>
                    @endif
                    {{-- Example Action: Delete Request --}}
                    <form action="{{ route('admin.support.destroy', $request->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Are you sure you want to delete this request?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No support requests found.</p>
    @endif
</div>
@endsection