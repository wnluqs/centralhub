@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('battery.index') }}" class="btn btn-secondary mb-3">← Back to Battery Jobs</a>
        <h2 class="mb-4 text-primary">Assign Battery Replacement Jobs</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('battery.store') }}" method="POST">
            @csrf

            <div class="form-group mb-3">
                <label for="terminal_ids">Select Terminals (max 80)</label>
                <select name="terminal_ids[]" id="terminal_ids" class="form-control" multiple required>
                    @foreach ($terminals as $terminal)
                        <option value="{{ $terminal->id }}">{{ $terminal->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="comment">Comment (optional)</label>
                <input type="text" class="form-control" name="comment" placeholder="Remarks for this batch (optional)">
            </div>

            <button type="submit" class="btn btn-success">✔ Assign Terminals</button>
        </form>
    </div>
@endsection

@push('scripts')
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#terminal_ids').select2({
                placeholder: 'Select terminals...',
                allowClear: true,
                width: '100%',
                maximumSelectionLength: 80
            });
        });
    </script>
@endpush
