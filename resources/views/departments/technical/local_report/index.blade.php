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
                    <th>Landmark</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Public Complaints</th>
                    <th>Operations Complaints</th>
                    <th>Photos</th>
                    <th>Videos</th>
                    <th>Technician</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $index => $report)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $report->zone }}</td>
                        <td>{{ $report->road }}</td>
                        <td>{{ $report->landmark ?? '-' }}</td>
                        <td>{{ $report->latitude ?? '-' }}</td>
                        <td>{{ $report->longitude ?? '-' }}</td>
                        <td>
                            @php
                                $public = is_array($report->public_complaints)
                                    ? $report->public_complaints
                                    : json_decode($report->public_complaints ?? '{}', true);
                            @endphp
                            <ul class="list-unstyled mb-0">
                                @foreach ($public as $label => $value)
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
                            @php
                                $ops = is_array($report->operations_complaints)
                                    ? $report->operations_complaints
                                    : json_decode($report->operations_complaints ?? '{}', true);
                            @endphp
                            <ul class="list-unstyled mb-0">
                                @foreach ($ops as $label => $value)
                                    <li>• {{ $label }}: {{ is_array($value) ? implode(' : ', $value) : $value }}
                                    </li>
                                @endforeach
                                @if ($report->operations_others)
                                    <li><strong>Others:</strong> {{ $report->operations_others }}</li>
                                @endif
                            </ul>
                        </td>
                        <td>
                            @php
                                $photos = is_array($report->photos)
                                    ? $report->photos
                                    : json_decode($report->photos ?? '[]', true);
                            @endphp

                            @foreach ($photos as $photo)
                                @if (is_string($photo))
                                    <img src="{{ asset('storage/' . $photo) }}" width="60" class="me-1 mb-1">
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @php
                                $videos = is_array($report->videos)
                                    ? $report->videos
                                    : json_decode($report->videos ?? '[]', true);
                            @endphp

                            @foreach ($videos as $video)
                                @if (is_string($video))
                                    <a href="{{ asset('storage/' . $video) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-play-circle"></i> View Video
                                    </a>
                                @endif
                            @endforeach
                        </td>
                        <td>{{ $report->technician_name }}</td>
                        <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Sound -->
    <audio id="ding-sound" src="{{ asset('sounds/ding.wav') }}" preload="auto"></audio>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        let table;
        let lastKnownId = {{ $reports->first()->id ?? 0 }};

        function formatComplaints(jsonStr, others) {
            let out = "<ul class='list-unstyled mb-0'>";
            let obj = {};
            try {
                obj = typeof jsonStr === 'string' ? JSON.parse(jsonStr) : jsonStr;
            } catch (e) {}

            for (const key in obj) {
                const value = obj[key];
                out +=
                    `<li>• ${key}: ${(typeof value === 'object' && value !== null) ? Object.values(value).join(" : ") : value}</li>`;
            }

            if (others) out += `<li><strong>Others:</strong> ${others}</li>`;
            out += "</ul>";
            return out;
        }

        function fetchLatestReports() {
            $.get('{{ route('technical-local_report') }}?json=1', function(data) {
                const reports = data.reports;
                if (!reports || !reports.length) return;

                console.log("Fetched report IDs:", reports.map(r => r.id));
                console.log("Last known ID:", lastKnownId);

                if (reports[0].id > lastKnownId) {
                    lastKnownId = reports[0].id;
                    table.clear().draw();

                    setTimeout(() => {
                        reports.forEach((report, index) => {
                            const photos = typeof report.photos === 'string' ? JSON.parse(report
                                .photos || '[]') : (report.photos || []);
                            const videos = typeof report.videos === 'string' ? JSON.parse(report
                                .videos || '[]') : (report.videos || []);
                            const pubComplaints = typeof report.public_complaints === 'string' ?
                                JSON.parse(report.public_complaints || '{}') : report
                                .public_complaints;
                            const opsComplaints = typeof report.operations_complaints === 'string' ?
                                JSON.parse(report.operations_complaints || '{}') : report
                                .operations_complaints;

                            const rowNode = table.row.add([
                                index + 1,
                                report.zone,
                                report.road,
                                report.landmark ?? '-',
                                report.latitude ?? '-',
                                report.longitude ?? '-',
                                formatComplaints(pubComplaints, report.public_others),
                                formatComplaints(opsComplaints, report.operations_others),
                                photos.map(photo =>
                                    `<img src="/storage/${photo}" width="60" class="me-1 mb-1">`
                                ).join(''),
                                videos.map(video =>
                                    `<a href="/storage/${video}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-play-circle"></i> View Video</a>`
                                ).join(''),
                                report.technician_name,
                                new Date(report.created_at).toLocaleString()
                            ]).draw(false).node();

                            $(rowNode).addClass('flash-new');
                        });

                        $('html, body').animate({
                            scrollTop: 0
                        }, 'fast');
                        toastr.success('New report received');
                        document.addEventListener('click', () => {
                            const ding = document.getElementById('ding-sound');
                            if (ding) ding.play().catch(err => console.warn('Still blocked:', err));
                        }, {
                            once: true
                        });
                    }, 100);
                }
            });
        }

        $(document).ready(function() {
            table = $('#localReportTable').DataTable({
                pageLength: 10,
                responsive: true
            });

            console.log("Auto-refresh started");
            setInterval(fetchLatestReports, 5000);
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <style>
        .dataTables_filter input,
        .dataTables_length select {
            color: #000;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 4px 8px;
        }

        .flash-new {
            animation: flashGreen 4s ease-in-out;
        }

        @keyframes flashGreen {
            0% {
                background-color: #28a745;
            }

            50% {
                background-color: #d4edda;
            }

            100% {
                background-color: transparent;
            }
        }
    </style>
@endpush
