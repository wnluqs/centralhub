@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('inspections.index') }}" class="btn btn-secondary mb-3">
            ← Back to Technical Inspections
        </a>

        <h2 class="text-primary mb-4" style="font-size: 32px;">🛠️ Create Inspection</h2>

        {{-- Optional Step Indicator --}}
        <div class="progress mb-4" style="height: 25px;">
            <div class="progress-bar progress-bar-striped bg-info" style="width: 100%;">
                Step 1 of 1 - Inspection Form
            </div>
        </div>

        <form action="{{ route('inspections.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SECTION 1: Location Info --}}
            <h4 class="mt-4 mb-3 text-dark">📍 Location Info</h4>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="branch" class="form-label" style="font-size: 18px;">Branch</label>
                    <select id="branch" name="branch" class="form-control" style="font-size: 18px;" required>
                        <option value="">-- Select Branch --</option>
                        <option value="Kuantan">Kuantan</option>
                        <option value="Machang">Machang</option>
                        <option value="Kuala Terengganu">Kuala Terengganu</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="terminal_id" class="form-label" style="font-size: 18px;">Terminal ID</label>
                    <select id="terminal_id" name="terminal_id" class="form-control" style="font-size: 18px;" required>
                        <option value="">-- Select Terminal ID --</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="zone" class="form-label" style="font-size: 18px;">Zone</label>
                    <select name="zone" id="zone" class="form-control" style="font-size: 18px;" required>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone }}">{{ $zone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="road" class="form-label" style="font-size: 18px;">Road</label>
                    <select name="road" id="road" class="form-control" style="font-size: 18px;" required>
                        @foreach ($roads as $road)
                            <option value="{{ $road }}">{{ $road }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- SECTION 2: Inspection Details --}}
            <h4 class="mt-4 mb-3 text-dark">📋 Inspection Details</h4>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="spare_parts" class="form-label" style="font-size: 18px;">Spare Parts</label>
                    <select name="spare_parts[]" id="spare_parts" class="form-control" multiple="multiple" style="font-size: 18px;">
                        @foreach ($spareParts as $part)
                            <option value="{{ $part }}">{{ $part }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="status" class="form-label" style="font-size: 18px;">Status</label>
                    <select name="status" id="status" class="form-control" style="font-size: 18px;" required>
                        <option value="Complete">Complete</option>
                        <option value="Failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="screen" class="form-label" style="font-size: 18px;">Screen Condition</label>
                    <select name="screen" class="form-control" style="font-size: 18px;">
                        <option value="">-- Select --</option>
                        <option value="Good">Good</option>
                        <option value="Cracked">Cracked</option>
                        <option value="Shattered">Shattered</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="keypad" class="form-label" style="font-size: 18px;">Keypad Condition</label>
                    <select name="keypad" class="form-control" style="font-size: 18px;">
                        <option value="">-- Select --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="sticker" class="form-label" style="font-size: 18px;">Sticker Condition</label>
                    <select name="sticker" class="form-control" style="font-size: 18px;">
                        <option value="">-- Select --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="solar" class="form-label" style="font-size: 18px;">Solar Condition</label>
                    <select name="solar" class="form-control" style="font-size: 18px;">
                        <option value="">-- Select --</option>
                        <option value="Heavy Snow">Heavy Snow</option>
                        <option value="Partly Cloudly">Partly Cloudly</option>
                        <option value="Clear Summer Day">Clear Summer Day</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="environment" class="form-label" style="font-size: 18px;">Environment Condition</label>
                    <select name="environment" class="form-control" style="font-size: 18px;">
                        <option value="">-- Select --</option>
                        <option value="Clean">Clean</option>
                        <option value="Partially">Partially</option>
                        <option value="Dirty">Dirty</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="technician_name" class="form-label" style="font-size: 18px;">Technician Name</label>
                    <select name="technician_name" class="form-control" style="font-size: 18px;" required>
                        <option value="">-- Select Technician --</option>
                        @foreach ($technicians as $technician)
                            <option value="{{ $technician }}">{{ $technician }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- SECTION 3: Attachments --}}
            <h4 class="mt-4 mb-3 text-dark">📎 Attachments</h4>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="photo_path" class="form-label" style="font-size: 18px;">Upload Photo(s)</label>
                    <input type="file" name="photo_path[]" multiple class="form-control" accept="image/*" style="font-size: 18px;">
                </div>
                <div class="col-md-6 mb-4">
                    <label for="video_path" class="form-label" style="font-size: 18px;">Upload Video</label>
                    <input type="file" name="video_path" class="form-control" accept="video/*" style="font-size: 18px;">
                </div>
                <div class="col-md-6 mb-4">
                    <label for="keypad_grade" class="form-label" style="font-size: 18px;">Keypad Grade</label>
                    <select name="keypad_grade" class="form-control" style="font-size: 18px;">
                        <option value="">-- Select Grade --</option>
                        <option value="A">Grade A - Excellent</option>
                        <option value="B">Grade B - Good</option>
                        <option value="C">Grade C - Damaged</option>
                    </select>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">✅ Create Inspection</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#spare_parts').select2({
            placeholder: "Select Spare Parts",
            theme: 'bootstrap4',
            width: '100%'
        });

        $('#terminal_id').select2({
            placeholder: "-- Select Terminal ID --",
            theme: 'bootstrap4',
            width: '100%',
        });

        $('#terminal_id').prop('disabled', true);

        $('#branch').change(function () {
            var selectedBranch = $(this).val();

            $('#terminal_id').html('<option value="">-- Fetching Terminals... --</option>').prop('disabled', false);

            if (selectedBranch) {
                $.ajax({
                    url: "{{ route('terminals.byBranch') }}",
                    type: "GET",
                    data: { branch: selectedBranch },
                    success: function (response) {
                        $('#terminal_id').html('<option value="">-- Select Terminal ID --</option>');

                        if (response.length > 0) {
                            $.each(response, function (index, terminal) {
                                $('#terminal_id').append(
                                    $('<option>', {
                                        value: terminal.id,
                                        text: terminal.id
                                    })
                                );
                            });
                        } else {
                            $('#terminal_id').html('<option value="">-- No Terminals Found --</option>');
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                $('#terminal_id').html('<option value="">-- Select Terminal ID --</option>').prop('disabled', true);
            }
        });
    });
</script>
@endpush
