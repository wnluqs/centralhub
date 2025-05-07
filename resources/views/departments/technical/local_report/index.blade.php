@extends('layouts.app')

@section('content')
    <a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
        ← Back to Technical Dashboard
    </a>

    <div class="container">
        <h2>Laporan Setempat (Local Reports)</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('technical-local_report.create') }}" class="btn btn-primary mb-3">+ Submit New Report</a>

        <table class="table table-bordered table-striped" id="localReportTable">
            <thead class="thead-dark text-center">
                <tr>
                    <th>#</th>
                    <th>Zone</th>
                    <th>Road</th>
                    <th>Public Complaints</th>
                    <th>Operations Complaints</th>
                    <th>Photos</th>
                    <th>Videos</th>
                    <th>Technician</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $report->zone }}</td>
                        <td>{{ $report->road }}</td>
                        <td>
                            <ul class="list-unstyled mb-0">
                                @foreach (json_decode($report->public_complaints ?? '{}', true) as $label => $value)
                                    <li>
                                        • {{ $label }}:
                                        @if (is_array($value))
                                            {{ $value['type'] ?? '-' }} : {{ $value['value'] ?? '0' }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </li>
                                @endforeach

                                @if ($report->public_others)
                                    <li><strong>Others:</strong> {{ $report->public_others }}</li>
                                @endif
                            </ul>
                        </td>

                        <td>
                            <ul class="list-unstyled mb-0">
                                @foreach (json_decode($report->operations_complaints ?? '{}', true) as $label => $value)
                                    <li>• {{ $label }}: {{ is_array($value) ? implode(' : ', $value) : $value }}
                                    </li>
                                @endforeach
                                @if ($report->operations_others)
                                    <li><strong>Others:</strong> {{ $report->operations_others }}</li>
                                @endif
                            </ul>
                        </td>

                        <td>
                            @foreach (json_decode($report->photos ?? '[]', true) as $photo)
                                <img src="{{ asset('storage/' . $photo) }}" width="60" class="me-1 mb-1">
                            @endforeach
                        </td>
                        <td>
                            @foreach (json_decode($report->videos ?? '[]', true) as $video)
                                <a href="{{ asset('storage/' . $video) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-play-circle"></i> View Video
                                </a>
                            @endforeach
                        </td>
                        <td>{{ $report->technician_name }}</td>
                        <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-danger">No reports available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <!-- Include jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" />

    <script>
        $(document).ready(function() {
            $('#localReportTable').DataTable({
                pageLength: 10,
                responsive: true
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        /* Make DataTables filter inputs readable */
        .dataTables_filter input,
        .dataTables_length select {
            color: rgb(204, 8, 8);
            background-color: white;
            border: 1px solid #ccc;
            padding: 4px 8px;
        }
    </style>
@endpush
