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

            {{-- Branch & Zone --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="branch" class="form-label">Branch</label>
                    <select name="branch" id="branch" class="form-control" required>
                        <option value="">-- Select Branch --</option>
                        <option value="Machang">Machang</option>
                        <option value="Kuantan">Kuantan</option>
                        <option value="Kuala Terengganu">Kuala Terengganu</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="zone" class="form-label">Zone</label>
                    <select name="zone_id" id="zone" class="form-control" required disabled>
                        <option value="">-- Select Zone --</option>
                    </select>
                </div>
            </div>

            {{-- Road --}}
            <div class="form-group mb-3">
                <label for="road">Road</label>
                <select name="road" id="road" class="form-control" required disabled>
                    <option value="">-- Select Road --</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="types_of_damages">Types of Damages</label>
                <select name="types_of_damages[]" id="types_of_damages" class="form-control select2" multiple>
                    <option value="Mesin Rosak">Mesin Rosak</option>
                    <option value="Coin Sangkut">Coin Sangkut</option>
                    <option value="Battery Low">Battery Low</option>
                    <option value="Paparan Skrin">Paparan Skrin</option>
                    <option value="Lain-lain">Lain-lain</option>
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
            $('#types_of_damages').select2({
                placeholder: 'Select types of damages',
                width: '100%'
            });
            $('#zone').prop('disabled', true);
            $('#road').prop('disabled', true);

            $('#branch').on('change', function() {
                const branch = $(this).val();
                $('#zone').empty().append('<option value="">-- Select Zone --</option>').prop('disabled',
                    true);
                $('#road').empty().append('<option value="">-- Select Road --</option>').prop('disabled',
                    true);

                if (branch !== '') {
                    $.get('/zones/' + branch, function(zones) {
                        if (zones.length > 0) {
                            $('#zone').prop('disabled', false);
                            zones.forEach(function(z) {
                                $('#zone').append('<option value="' + z.id + '">' + z.name +
                                    '</option>');
                            });
                        } else {
                            $('#zone').append('<option value="">No Zones Found</option>');
                        }
                    }).fail(function() {
                        alert('Failed to load zones from server.');
                    });
                }
            });

            $('#zone').on('change', function() {
                const zoneId = $(this).val();
                $('#road').empty().append('<option value="">-- Select Road --</option>').prop('disabled',
                    true);

                if (zoneId !== '') {
                    $.get('/roads/' + zoneId, function(roads) {
                        if (roads.length > 0) {
                            $('#road').prop('disabled', false);
                            roads.forEach(function(r) {
                                $('#road').append('<option value="' + r + '">' + r +
                                    '</option>');
                            });
                        } else {
                            $('#road').append('<option value="">No Roads Found</option>');
                        }
                    }).fail(function() {
                        alert('Failed to load roads from server.');
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#terminal_id').select2({
                placeholder: 'Search Terminal ID...',
                ajax: {
                    url: '{{ route('terminals.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
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
