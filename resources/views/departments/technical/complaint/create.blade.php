@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('complaints.index') }}" class="btn btn-secondary mb-3">
            ← Back to Complaint List
        </a>

        <h2>Submit New Complaint</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Error:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group mb-3">
                <label for="terminal_id">Terminal ID</label>
                <select class="form-control" id="terminal_id" name="terminal_id" style="width: 100%;">
                    <option value="">Select Terminal</option>
                    @foreach ($terminals as $terminal)
                        <option value="{{ $terminal->id }}">{{ $terminal->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="zone">Zone</label>
                <select name="zone" id="zone" class="form-control" required>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone }}">{{ $zone }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="road">Road</label>
                <select name="road" id="road" class="form-control" required>
                    @foreach ($roads as $road)
                        <option value="{{ $road }}">{{ $road }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="remarks">Complaint Details / Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="photos">Upload Photos (multiple allowed)</label>
                <input type="file" name="photos[]" multiple class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Submit Complaint</button>
        </form>
    </div>
@endsection

@push('scripts')
<!-- CSS & JS for Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#terminal_id').select2({
            placeholder: 'Search Terminal ID...',
            ajax: {
                url: '{{ route("terminals.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.id,
                                id: item.id
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });
</script>

<style>
    /* Fix dropdown text visibility */
    .select2-container--default .select2-results__option {
        color: black !important;
        background-color: white !important;
    }

    /* Fix search input text */
    .select2-container--default .select2-search--dropdown .select2-search__field {
        color: black !important;
    }

    /* Dropdown styling */
    .select2-container--default .select2-dropdown {
        background-color: white !important;
    }

    /* Fix selected value visibility */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: black !important;
    }
</style>

@endpush


