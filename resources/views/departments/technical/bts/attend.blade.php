@extends('layouts.app')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card p-4 shadow-lg rounded bg-white" style="max-width: 600px; width: 100%;">
            <a href="{{ route('bts.index') }}" class="btn btn-secondary mb-3">← Back to BTS</a>
            <h4 class="mb-3 text-primary">Attend BTS Alert for Terminal <strong>{{ $bts->terminal_id }}</strong></h4>

            <form method="POST" action="{{ route('bts.updateAttend', $bts->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group mb-3">
                    <label>Location</label>
                    <input type="text" class="form-control" value="{{ $bts->location }}" readonly>
                </div>

                <div class="form-group mb-3">
                    <label>Event Date</label>
                    <input type="text" class="form-control"
                        value="{{ \Carbon\Carbon::parse($bts->event_date)->format('d/m/Y H:i') }}" readonly>
                </div>

                <div class="form-group mb-3">
                    <label>Event Code - Name</label>
                    <input type="text" class="form-control" value="{{ $bts->event_code_name }}" readonly>
                </div>

                <div class="form-group mb-3">
                    <label>Comment / Action Taken</label>
                    <textarea name="comment" class="form-control" rows="3" required>{{ old('comment', $bts->comment) }}</textarea>
                </div>

                <div class="form-group mb-3">
                    <label>Parts Request</label>
                    <select name="parts_request" class="form-control" required>
                        <option value="" disabled selected>Select Parts Request</option>
                        <option>Printer</option>
                        <option>Door</option>
                        <option>Battery</option>
                        <option>Compact Screen</option>
                        <option>Solar Panel</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Upload Photo (Optional)</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>

                <div class="form-group mb-3">
                    <label>Terminal Status</label>
                    <select name="terminal_status" class="form-control" required>
                        <option value="">-- Select --</option>
                        <option value="Okay">Okay</option>
                        <option value="Off">Off</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100">Submit Attendance</button>
            </form>
        </div>
    </div>
@endsection
