@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Reassign Complaint: {{ $complaint->terminal_id }}</h2>

        <form action="{{ route('complaints.reassign.update', $complaint->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="technician_id" class="form-label">Select Technician</label>
                <select name="technician_id" id="technician_id" class="form-control" required>
                    <option value="">-- Choose Technician --</option>
                    @foreach ($technicians as $technician)
                        <option value="{{ $technician->id }}" {{ $complaint->assigned_to == $technician->id ? 'selected' : '' }}>
                            {{ $technician->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-danger">Reassign</button>
            <a href="{{ route('technical-complaints') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
