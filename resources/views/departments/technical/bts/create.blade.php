@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card p-4 shadow-lg rounded bg-white" style="max-width: 600px; width: 100%;">
        <a href="{{ route('bts.index') }}" class="btn btn-secondary mb-3">← Back to BTS</a>
        <h4 class="mb-3 text-primary fw-bold">New BTS Alert Entry</h4>

        <form method="POST" action="{{ route('bts.store') }}">
            @csrf

            <div class="form-group mb-3">
                <label for="terminal_id" class="fw-bold">Terminal ID</label>
                <select name="terminal_id" id="terminal_id" class="form-control" required></select>
            </div>

            <div class="form-group mb-3">
                <label for="status" class="fw-bold">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Warning">Warning</option>
                    <option value="Error">Error</option>
                    <option value="Normal">Normal</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="location" class="fw-bold">Location</label>
                <input type="text" name="location" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="event_date" class="fw-bold">Event Date</label>
                <input type="datetime-local" name="event_date" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="event_code_name" class="fw-bold">Event Code - Name</label>
                <input type="text" name="event_code_name" class="form-control" placeholder="e.g., Paper near end, Door opened" required>
            </div>

            <div class="form-group mb-4">
                <label for="comment" class="fw-bold">Comment</label>
                <textarea name="comment" rows="3" class="form-control" placeholder="Optional comment..."></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100 fw-bold">Submit Alert</button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 Styling -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        background-color: white;
        color: black;
        height: 40px;
        font-size: 14px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        display: flex;
        align-items: center;
    }

    .select2-dropdown {
        background-color: white;
        color: black;
        font-size: 14px;
    }

    .select2-results__option {
        padding: 6px 12px;
    }

    .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff;
        color: white;
    }

    .select2-selection__rendered {
        padding-left: 10px !important;
    }

    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@push('scripts')
<!-- Select2 Script -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#terminal_id').select2({
            placeholder: 'Search Terminal ID...',
            ajax: {
                url: '{{ route("bts.searchTerminals") }}',
                dataType: 'json',
                delay: 200,
                processResults: function (data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.id
                        }))
                    };
                },
                cache: true
            }
        });
    });
</script>
@endpush
