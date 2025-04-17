@extends('layouts.app')

@section('content')
<a href="{{ route('departments.controlcenter') }}" class="btn btn-secondary mb-3">
    ← Back to Control Center Dashboard
</a>

<div class="container">
    <h2>Complaints</h2>

    <a href="{{ route('complaints.create') }}" class="btn btn-primary mb-3">+ New Complaint</a>
    <a href="{{ route('complaints.export.excel', [
    'terminal_id' => request('terminal_id'),
    'zone' => request('zone')
]) }}" class="btn btn-success mb-3">Export to Excel</a>

    <a href="{{ route('complaints.export.csv', [
    'terminal_id' => request('terminal_id'),
    'zone' => request('zone')
]) }}" class="btn btn-warning mb-3">Export to CSV</a>
    <!-- Filter form -->
    <div class="mb-3">
        <form action="{{ route('technical-complaints') }}" method="GET" class="mb-3">
            <input type="text" name="terminal_id" placeholder="Terminal ID" value="{{ request('terminal_id') }}">
            <input type="text" name="zone" placeholder="Zone" value="{{ request('zone') }}">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Terminal ID</th>
                <th>Zone</th>
                <th>Road</th>
                <th>Photos</th>
                <th>Remarks</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @if ($complaints->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No complaints available.</td>
            </tr>
            @else
            @foreach ($complaints as $complaint)
            <tr>
                <td>{{ $complaint->terminal_id }}</td>
                <td>{{ $complaint->zone }}</td>
                <td>{{ ucfirst($complaint->road) }}</td>
                <td>
                    @if($complaint->photos)
                    <img src="{{ asset('storage/' . $complaint->photos) }}" width="80">
                    @else
                    No Photo
                    @endif
                </td>
                <td>{{ $complaint->remarks }}</td>
                <td>{{ $complaint->created_at->format('Y-m-d H:i:s') }}</td> <!-- Format timestamp -->
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection