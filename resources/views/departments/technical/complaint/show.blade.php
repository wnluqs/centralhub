@extends('layouts.app')

@section('content')
<a href="{{ route('technical-complaints') }}" class="btn btn-secondary mb-3">
    ← Back to Technical Complaint
</a>
<div class="container">
    <h2>Complaint Detail</h2>
    <p><strong>Description:</strong> {{ $complaint->description }}</p>
    <p><strong>Location:</strong> {{ $complaint->location }}</p>
    <p><strong>Status:</strong> {{ $complaint->status }}</p>
    <p>
        @if($complaint->photo_path)
        <img src="{{ asset('storage/' . $complaint->photo_path) }}" width="100">
        @endif
    </p>

    <!-- List Inspections for this complaint -->
    <h3 style="background-color: lightblue;">Inspections</h3>
    <a href="{{ route('inspections.create', ['complaint_id' => $complaint->id]) }}" class="btn btn-warning">
        Create Inspection
    </a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Notes</th>
                <th>Inspected At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($complaint->inspections as $inspection)
            <tr>
                <td>{{ $inspection->notes }}</td>
                <td>{{ $inspection->inspected_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection