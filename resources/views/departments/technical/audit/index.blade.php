@extends('layouts.app')
@section('content')
<a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
    ← Back to Technical Dashboard
</a>
<div class="container">
    <h2 class="mb-4">Activity Logs</h2>
    <p>View the COMPLAINTS activities that have been created or updated by the user.</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>
                    @php
                    $nextDirection = ($currentSort === 'user' && $currentDirection === 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a href="{{ route('technical.audit', ['sort' => 'user', 'direction' => $nextDirection]) }}">
                        User
                        @if ($currentSort === 'user')
                        <i class="bi bi-arrow-{{ $currentDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                        <i class="bi bi-arrow-down-up"></i>
                        @endif
                    </a>
                </th>
                <th>Event</th>
                <th>Model</th>
                <th>Description</th>
                <th>IP Address</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $log->user->name ?? 'System' }}</td>
                <td>{{ ucfirst($log->event) }}</td>
                <td>{{ $log->model }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y h:i A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
</div>
@endsection