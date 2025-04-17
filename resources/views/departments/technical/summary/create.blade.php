@extends('layouts.app')

@section('content')
<a href="{{ route('technical-summary') }}" class="btn btn-secondary mb-3">
    ← Back to Technical Summary
</a>
<div class="container">
    <h2>Add New Report</h2>
    <form action="{{ route('summary.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Terminal ID</label>

            @if($terminals->isEmpty())
            <p style="color: red; font-weight: bold;">No terminals found. Please add terminals first.</p>
            @endif

            <select name="terminal_id" class="form-control" required>
                <option value="">Select Terminal</option>
                @foreach ($terminals as $terminal)
                <option value="{{ $terminal->id }}">{{ $terminal->id }}</option>
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

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="Complete">Complete</option>
                <option value="Failed">Failed</option>
                <option value="Almost">Almost</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Submit Report</button>
    </form>
</div>
@endsection