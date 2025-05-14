@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('technical-complaints') }}" class="btn btn-secondary mb-3">
        ← Back to Technical Complaints
    </a>

    <h3 class="mb-4 text-primary">Attend Complaint for Terminal <strong>{{ $complaint->terminal_id }}</strong></h3>

    <form action="{{ route('complaints.attend.submit', $complaint->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Terminal Info (Read-only) --}}
        <div class="mb-3">
            <label class="form-label">Terminal ID</label>
            <input type="text" class="form-control" value="{{ $complaint->terminal_id }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Zone</label>
            <input type="text" class="form-control" value="{{ $complaint->zone }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Road</label>
            <input type="text" class="form-control" value="{{ $complaint->road }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" rows="2" readonly>{{ $complaint->remarks }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Types of Damages</label>
            @php $types = json_decode($complaint->types_of_damages, true); @endphp
            <input type="text" class="form-control" value="{{ $types && is_array($types) ? implode(', ', $types) : '-' }}" readonly>
        </div>

        {{-- Attendance Fields --}}
        <div class="mb-3">
            <label class="form-label">Comment / Action Taken</label>
            <textarea name="comment" class="form-control" rows="3" required placeholder="Describe the action taken..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Photo (Optional)</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success w-100">Submit Attendance</button>
    </form>
</div>
@endsection
