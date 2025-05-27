@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Assign Complaint</h2>

    <form action="{{ route('technical.complaints.assign.update', $complaint->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="user_id">Assign to Technician:</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="">-- Select Technician --</option>
                @foreach($technicians as $technician)
                    <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Assign</button>
    </form>
</div>
@endsection
