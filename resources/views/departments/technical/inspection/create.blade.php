@extends('layouts.app')

@section('content')
<a href="{{ route('inspections.index') }}" class="btn btn-secondary mb-3">
    ← Back to Technical Inspections
</a>

<div class="container">
    <h2>Create Inspection</h2>

    <form action="{{ route('inspections.store') }}" method="POST" enctype="multipart/form-data">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @csrf

        <!-- Terminal Selection (Foreign Key) -->
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

        <!-- Spare Parts Dropdowns -->
        <div class="form-group">
            <label for="spare_part_1">Spare Part 1</label>
            <select name="spare_part_1" id="spare_part_1" class="form-control">
                <option value="">Select Spare Part</option>
                @foreach($spareParts as $part)
                <option value="{{ $part }}">{{ $part }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="spare_part_2">Spare Part 2</label>
            <select name="spare_part_2" id="spare_part_2" class="form-control">
                <option value="">Select Spare Part</option>
                @foreach($spareParts as $part)
                <option value="{{ $part }}">{{ $part }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="spare_part_3">Spare Part 3</label>
            <select name="spare_part_3" id="spare_part_3" class="form-control">
                <option value="">Select Spare Part</option>
                @foreach($spareParts as $part)
                <option value="{{ $part }}">{{ $part }}</option>
                @endforeach
            </select>
        </div>

        <!-- Status Dropdown -->
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="Complete">Complete</option>
                <option value="Failed">Failed</option>
                <option value="Almost">Almost</option>
            </select>
        </div>

        <!-- Technician Name Dropdown -->
        <div class="form-group">
            <label for="technician_name">Technician Name</label>
            <select name="technician_name" id="technician_name" class="form-control" required>
                <option value="">Select Technician</option>
                @foreach($technicians as $technician)
                <option value="{{ $technician }}">{{ $technician }}</option>
                @endforeach
            </select>
        </div>

        <!-- Photo Upload -->
        <div class="form-group">
            <label for="photos">Upload Photos/Videos</label>
            <input type="file" name="photos" id="photos" class="form-control"
                accept="image/png, image/jpeg, image/jpg, video/mp4, video/mov, video/avi">
            <small class="form-text text-muted">You can upload images or videos. Max size: 2MB.</small>
        </div>

        <button type="submit" class="btn btn-success">Create Inspection</button>
    </form>

</div>
@endsection