@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('technical-summary') }}" class="btn btn-secondary mb-3">
        ← Back to Technical Summary
    </a>
    <div class="container">
        <h2>Edit Report</h2>
        <form action="{{ route('summary.update', $report->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Terminal ID</label>
                <select name="terminal_id" class="form-control">
                    @foreach ($terminals as $terminal)
                    <option value="{{ $terminal->id }}" {{ $report->terminal_id == $terminal->id ? 'selected' : '' }}>
                        {{ $terminal->id }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Spare Part 1</label>
                <select name="spare_part_1" class="form-control">
                    <option value="">-- Select Spare Part 1 --</option>
                    @foreach ($spareParts as $part)
                    <option value="{{ $part }}" {{ $report->spare_part_1 == $part ? 'selected' : '' }}>
                        {{ $part }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Spare Part 2</label>
                <select name="spare_part_2" class="form-control">
                    <option value="">-- Select Spare Part 2 --</option>
                    @foreach ($spareParts as $part)
                    <option value="{{ $part }}" {{ $report->spare_part_2 == $part ? 'selected' : '' }}>
                        {{ $part }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Spare Part 3</label>
                <select name="spare_part_3" class="form-control">
                    <option value="">-- Select Spare Part 3 --</option>
                    @foreach ($spareParts as $part)
                    <option value="{{ $part }}" {{ $report->spare_part_3 == $part ? 'selected' : '' }}>
                        {{ $part }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Complete" {{ $report->status == 'Complete' ? 'selected' : '' }}>Complete</option>
                    <option value="Failed" {{ $report->status == 'Failed' ? 'selected' : '' }}>Failed</option>
                    <option value="Almost" {{ $report->status == 'Almost' ? 'selected' : '' }}>Almost</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update Report</button>
        </form>
    </div>
    @endsection