@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('battery.index') }}" class="btn btn-secondary mb-3">← Back to Battery Module</a>
    <h2>Attend Battery Job ({{ $job->terminal_id }})</h2>

    <form action="{{ route('battery.update', $job->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-3">
            <label>Upload Photo (Camera Only)</label>
            <input type="file" name="photo" accept="image/*" capture="environment" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Comment (Optional)</label>
            <textarea name="comment" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Submit Battery Job</button>
    </form>
</div>
@endsection
