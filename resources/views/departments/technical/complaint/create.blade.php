@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('complaints.index') }}" class="btn btn-secondary mb-3">
        ← Back to Complaint List
    </a>

    <h2>Submit New Complaint</h2>

    <!-- Form for submitting a complaint -->
    <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Shared Fields -->
        <div class="form-group">
            <label for="terminal_id">Terminal ID</label>
            @if($terminals->isEmpty())
            <p style="color: red;">No terminals found. Please add terminals first.</p>
            @endif
            <select name="terminal_id" id="terminal_id" class="form-control" required>
                <option value="">Select Terminal</option>
                @foreach ($terminals as $terminal)
                <option value="{{ $terminal->id }}">{{ $terminal->id }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="zone">Zone</label>
            <select name="zone" id="zone" class="form-control" required>
                @foreach($zones as $zone)
                <option value="{{ $zone }}">{{ $zone }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="road">Road</label>
            <select name="road" id="road" class="form-control" required>
                @foreach($roads as $road)
                <option value="{{ $road }}">{{ $road }}</option>
                @endforeach
            </select>
        </div>

        <!-- Photo Upload -->
        <div class="form-group">
            <label for="photos">Upload Photos</label>
            <input type="file" name="photos" id="photos" class="form-control" accept="image/png, image/jpeg, image/jpg">
        </div>

        <div class="form-group">
            <label for="remarks">Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Complaint</button>
    </form>
</div>
@endsection