@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('controlcenter-complaints') }}" class="btn btn-secondary mb-3">
            ← Back to Complaints List
        </a>

        <h2>Assign Complaint to Technician</h2>

        <form method="POST" action="{{ route('complaints.assign.update', $complaint->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label for="terminal_id">Terminal ID</label>
                <input type="text" class="form-control" value="{{ $complaint->terminal_id }}" disabled>
            </div>

            <div class="form-group mb-3">
                <label for="zone">Zone</label>
                <input type="text" class="form-control" value="{{ $complaint->zone }}" disabled>
            </div>

            <div class="form-group mb-3">
                <label for="road">Road</label>
                <input type="text" class="form-control" value="{{ $complaint->road }}" disabled>
            </div>

            <div class="form-group mb-3">
                <label for="technician_id">Assign to Technician</label>
                <select name="technician_id" id="technician_id" class="form-control" required>
                    <option value="">-- Select Technician --</option>
                    @foreach ($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }} ({{ $tech->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="types_of_damages">Types of Damages</label>
                <select name="types_of_damages[]" id="types_of_damages" class="form-control" multiple required>
                    <option value="Mesin Rosak">Mesin Rosak</option>
                    <option value="Coin Sangkut">Coin Sangkut</option>
                    <option value="Battery Low">Battery Low</option>
                    <option value="Paparan Skrin">Paparan Skrin</option>
                    <option value="Lain-lain">Lain-lain</option>
                </select>
                <small class="text-muted">Hold Ctrl (or Cmd) to select multiple</small>
            </div>

            <button type="submit" class="btn btn-success">Assign Complaint</button>
        </form>
    </div>
@endsection
