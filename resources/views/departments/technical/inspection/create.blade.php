@extends('layouts.app')

@section('content')
    <a href="{{ route('inspections.index') }}" class="btn btn-secondary mb-3">
        ← Back to Technical Inspections
    </a>

    <div class="container">
        <h2>Create Inspection</h2>

        <form action="{{ route('inspections.store') }}" method="POST" enctype="multipart/form-data">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @csrf
            <!-- Branch Selection -->
            <div class="form-group">
                <label for="branch">Branch</label>
                <select id="branch" name="branch" class="form-control" required>
                    <option value="">-- Select Branch --</option>
                    <option value="Kuantan">Kuantan</option>
                    <option value="Machang">Machang</option>
                    <option value="Kuala Terengganu">Kuala Terengganu</option>
                </select>
            </div>
            <!-- Terminal ID Selection -->
            <div class="form-group">
                <label for="terminal_id">Terminal ID</label>
                <select id="terminal_id" name="terminal_id" class="form-control" required>
                    <option value="">-- Select Terminal ID --</option>
                    {{-- Terminal IDs will be loaded dynamically --}}
                </select>
            </div>


            <div class="form-group">
                <label for="zone">Zone</label>
                <select name="zone" id="zone" class="form-control" required>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone }}">{{ $zone }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="road">Road</label>
                <select name="road" id="road" class="form-control" required>
                    @foreach ($roads as $road)
                        <option value="{{ $road }}">{{ $road }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Multi Spare Parts -->
            <div class="form-group">
                <label for="spare_parts">Spare Parts</label>
                <select name="spare_parts[]" id="spare_parts" class="form-control" multiple="multiple">
                    @foreach ($spareParts as $part)
                        <option value="{{ $part }}">{{ $part }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Dropdown -->
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Complete">Complete</option>
                    <option value="Failed">Failed</option>
                </select>
            </div>

            <div class="form-group">
                <label for="screen_condition">Screen Condition</label>
                <input type="text" name="screen_condition" class="form-control" placeholder="e.g. Good, Crack, Shattered">
            </div>
            <div class="form-group">
                <label for="keypad_condition">Keypad Condition</label>
                <input type="text" name="keypad_condition" class="form-control" placeholder="e.g. A, B, C">
            </div>
            <div class="form-group">
                <label for="sticker_condition">Sticker Condition</label>
                <input type="text" name="sticker_condition" class="form-control">
            </div>
            <div class="form-group">
                <label for="solar_condition">Solar Condition</label>
                <input type="text" name="solar_condition" class="form-control" placeholder="e.g. Good, Damaged, Broken">
            </div>
            <div class="form-group">
                <label for="environment_condition">Environment Condition</label>
                <input type="text" name="environment_condition" class="form-control">
            </div>

            <!-- Technician Name Dropdown -->
            <div class="form-group">
                <label for="technician_name">Technician Name</label>
                <select name="technician_name" id="technician_name" class="form-control" required>
                    <option value="">Select Technician</option>
                    @foreach ($technicians as $technician)
                        <option value="{{ $technician }}">{{ $technician }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Photo Upload -->
            <div class="form-group">
                <label for="photo_path">Upload Photo</label>
                <input type="file" name="photo_path[]" multiple class="form-control" accept="image/*">
            </div>

            <!-- Video Upload -->
            <div class="form-group">
                <label for="video_path">Upload Video</label>
                <input type="file" name="video_path" id="video_path" class="form-control" accept="video/*">
            </div>

            <!-- Video Grading -->
            <div class="form-group">
                <label for="keypad_grade">Keypad Grade</label>
                <select name="keypad_grade" id="keypad_grade" class="form-control">
                    <option value="">Select Grade</option>
                    <option value="A">Grade A - Excellent</option>
                    <option value="B">Grade B - Good</option>
                    <option value="C">Grade C - Damaged</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Create Inspection</button>
        </form>
    </div>
@endsection

@section('scripts')
    @section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Spare Parts Select2
            $('#spare_parts').select2({
                placeholder: "Select Spare Parts",
                allowClear: true,
                theme: 'bootstrap4',
                width: '100%'
            });

            // Initialize Terminal ID with Select2
            $('#terminal_id').select2({
                placeholder: "-- Select Terminal ID --",
                allowClear: true,
                theme: 'bootstrap4',
                width: '100%',
            });

            // Initially disable Terminal ID dropdown
            $('#terminal_id').prop('disabled', true);

            // Add Branch -> Terminal dynamic loading
            $('#branch').change(function() {
                var selectedBranch = $(this).val();

                // Clear and disable Terminal ID dropdown
                $('#terminal_id').html('<option value="">-- Fetching Terminals... --</option>').prop('disabled', false);

                if (selectedBranch) {
                    $.ajax({
                        url: "{{ route('terminals.byBranch') }}",
                        type: "GET",
                        data: { branch: selectedBranch },
                        success: function(response) {
                            $('#terminal_id').html('<option value="">-- Select Terminal ID --</option>');

                            if (response.length > 0) {
                                $.each(response, function(index, terminal) {
                                    $('#terminal_id').append(
                                        $('<option>', {
                                            value: terminal.id,
                                            text: terminal.id
                                        })
                                    );
                                });

                                $('#terminal_id').prop('disabled', false);

                                // Re-initialize select2 after AJAX loaded
                                $('#terminal_id').select2({
                                    placeholder: "-- Select Terminal ID --",
                                    allowClear: true,
                                    theme: 'bootstrap4',
                                    width: '100%'
                                });

                            } else {
                                $('#terminal_id').html('<option value="">-- No Terminals Found --</option>');
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                } else {
                    $('#terminal_id').html('<option value="">-- Select Terminal ID --</option>').prop('disabled', true);
                }
            });

        });
    </script>
@endsection

