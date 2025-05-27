@extends('layouts.app')

@section('content')
    <a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
        ← Back to Technical Dashboard
    </a>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <h1>Inspection Records</h1>

        <div class="mb-3">
            <form action="{{ route('technical-inspections') }}" method="GET" class="form-inline">
                <div class="form-group">
                    <label for="sort_field">Sort by:</label>
                    <select name="sort_field" id="sort_field" class="form-control ml-2 mr-2">
                        <option value="branch">Branch</option>
                        <option value="created_at">Date</option>
                        <option value="status">Status</option>
                    </select>
                </div>
                <div class="form-group ml-2">
                    <select name="sort_order" id="sort_order" class="form-control ml-2 mr-2">
                        <option value="asc">Ascending ↑</option>
                        <option value="desc">Descending ↓</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary ml-2">Apply</button>
            </form>
        </div>

        <a href="{{ route('inspections.create') }}" class="btn btn-primary mb-3">+ Add New Inspection</a>
        <form action="{{ route('technical-inspections') }}" method="GET" class="card p-4 mb-4 shadow-sm rounded bg-light">
            <h4 class="mb-3">Search Filters</h4>
            <div class="row gy-3">
                <div class="col-md-3">
                    <input type="text" name="terminal_id" class="form-control" placeholder="Search Terminal ID"
                        value="{{ request('terminal_id') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="zone" class="form-control" placeholder="Search Zone"
                        value="{{ request('zone') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="road" class="form-control" placeholder="Search Road"
                        value="{{ request('road') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="branch" class="form-control" placeholder="Search Branch"
                        value="{{ request('branch') }}">
                </div>

                <div class="col-md-3">
                    <input type="text" name="status" class="form-control" placeholder="Search Status"
                        value="{{ request('status') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="submitted_by" class="form-control" placeholder="Search Submitted By"
                        value="{{ request('submitted_by') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" placeholder="Start Date"
                        value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" placeholder="End Date"
                        value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="keypad_grade" class="form-control" placeholder="Search Keypad Grade"
                        value="{{ request('keypad_grade') }}">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('technical-inspections') }}" class="btn btn-outline-secondary ml-2">Reset</a>
            </div>
        </form>

        <a href="{{ route('inspections.export.csv', ['search' => request('search')]) }}"
            class="btn btn-success ml-2">Export CSV</a>
        <a href="{{ route('inspections.export.excel', ['search' => request('search')]) }}"
            class="btn btn-success ml-2">Export Excel</a>
        <a href="{{ route('technical-inspections') }}" class="btn btn-outline-secondary ml-2">
            🔄 Reload Table
        </a>

        {{-- Dynamic Alert Always Visible if Filters Applied --}}
        @if (request('start_date') ||
                request('end_date') ||
                request('terminal_id') ||
                request('zone') ||
                request('road') ||
                request('branch') ||
                request('status') ||
                request('submitted_by') ||
                request('keypad_grade'))
            <div class="alert alert-info">
                <strong>Filtered Results:</strong>

                @if (request('start_date') && request('end_date'))
                    Showing inspections from <strong>{{ request('start_date') }}</strong> to
                    <strong>{{ request('end_date') }}</strong>.
                @elseif (request('start_date'))
                    Showing inspections from <strong>{{ request('start_date') }}</strong> onwards.
                @elseif (request('end_date'))
                    Showing inspections up until <strong>{{ request('end_date') }}</strong>.
                @endif

                @if (request('terminal_id'))
                    <br>Terminal ID contains: <strong>{{ request('terminal_id') }}</strong>
                @endif
                @if (request('zone'))
                    <br>Zone contains: <strong>{{ request('zone') }}</strong>
                @endif
                @if (request('road'))
                    <br>Road contains: <strong>{{ request('road') }}</strong>
                @endif
                @if (request('branch'))
                    <br>Branch contains: <strong>{{ request('branch') }}</strong>
                @endif
                @if (request('status'))
                    <br>Status contains: <strong>{{ request('status') }}</strong>
                @endif
                @if (request('submitted_by'))
                    <br>Submitted By contains: <strong>{{ request('submitted_by') }}</strong>
                @endif
                @if (request('keypad_grade'))
                    <br>Keypad Grade contains: <strong>{{ request('keypad_grade') }}</strong>
                @endif

            </div>
        @endif

        {{-- Now Proceed to Table --}}
        @if ($inspections->isEmpty())
            <p style="color: red;">No inspections found.</p>
        @else
            <table id="inspectionTable" class="table table-bordered table-striped table-sm text-center align-middle">
                <thead class="thead-dark text-center">
                    <tr>
                        <th>Terminal ID</th>
                        <th>Zone</th>
                        <th>Road</th>
                        <th>Branch</th>
                        <th>Spare Parts</th>
                        <th>Status</th>
                        <th>Submitted By (User)</th>
                        <th>Created At</th>
                        <th>Keypad Grade</th>
                        @hasanyrole(['Admin', 'TechnicalLead'])
                            <th>Spotcheck</th>
                        @endhasanyrole
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inspections as $inspection)
                        <tr>
                            <td>{{ $inspection->terminal_id }}</td>
                            <td>{{ $inspection->zone }}</td>
                            <td>{{ $inspection->road }}</td>
                            <td>{{ $inspection->branch ?? '-' }}</td>
                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                @if (is_array($inspection->spare_parts))
                                    @php
                                        $sparePartsList = implode(', ', $inspection->spare_parts);
                                        $shortText =
                                            strlen($sparePartsList) > 50
                                                ? substr($sparePartsList, 0, 50) . '...'
                                                : $sparePartsList;
                                    @endphp
                                    <span id="shortText-{{ $inspection->id }}">
                                        {{ $shortText }}
                                        @if (strlen($sparePartsList) > 50)
                                            <a href="javascript:void(0);"
                                                onclick="document.getElementById('shortText-{{ $inspection->id }}').innerHTML='{{ $sparePartsList }}';">See
                                                More</a>
                                        @endif
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeColor = match (strtolower($inspection->status)) {
                                        'complete' => 'success',
                                        'almost' => 'warning',
                                        'failed' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}">
                                    {{ $inspection->status }}
                                </span>
                            </td>
                            <td>{{ $inspection->submitted_by }}</td>
                            <td>{{ $inspection->created_at->format('Y-m-d H:i:s') }}</td>

                            {{-- 🛠 Correct order below --}}
                            <td>{{ $inspection->keypad_grade ?? '-' }}</td> {{-- keypad grade first --}}
                            @hasanyrole(['Admin', 'TechnicalLead'])
                                <td>
                                    <form method="POST" action="{{ route('inspections.spotcheck', $inspection->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="checkbox" name="spotcheck_verified" value="Checked"
                                            onchange="this.form.submit()"
                                            {{ $inspection->spotcheck_verified ? 'checked' : '' }}>
                                    </form>
                                    @if ($inspection->spotcheck_verified_by)
                                        <small class="text-success">Verified by
                                            {{ $inspection->spotcheck_verified_by }}</small>
                                    @endif
                                </td>
                            @endhasanyrole
                            <td>
                                <a href="{{ route('inspections.show', $inspection->id) }}"
                                    class="btn btn-sm btn-primary">View Details</a>
                            </td> {{-- action after --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $inspections->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        let inspectionTable;
        let lastKnownInspectionId = {{ $inspections->first()->id ?? 0 }};

        function formatMediaList(media, type = 'image') {
            if (!Array.isArray(media)) {
                try {
                    media = JSON.parse(media || '[]');
                } catch (_) {
                    media = [];
                }
            }
            return media.map((item) => {
                if (type === 'image') {
                    return `<img src="/storage/${item}" width="60" class="me-1 mb-1">`;
                }
                return `<a href="/storage/${item}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-play-circle"></i> View</a>`;
            }).join('');
        }

        function fetchLatestInspections() {
            $.get('{{ route('technical-inspections') }}?json=1', function(data) {
                const inspections = data.inspections;
                if (!inspections || !inspections.length) return;

                console.log("Fetched inspection IDs:", inspections.map(i => i.id));
                console.log("Last known Inspection ID:", lastKnownInspectionId);

                if (inspections[0].id > lastKnownInspectionId) {
                    lastKnownInspectionId = inspections[0].id;
                    inspectionTable.clear();

                    inspections.forEach((insp) => {
                        const rowNode = inspectionTable.row.add([
                            insp.terminal_id,
                            insp.zone,
                            insp.road,
                            insp.branch ?? '-',
                            insp.spare_parts ?? '-',
                            insp.status,
                            insp.submitted_by ?? '-',
                            new Date(insp.created_at).toLocaleString(),
                            insp.keypad_grade ?? '-',
                            '<em class="text-secondary">Pending</em>',
                            `<a href="/technical/inspections/${insp.id}" class="btn btn-sm btn-primary">View Details</a>`
                        ]).node();

                        $(rowNode).addClass('flash-new');
                    });

                    // 👇 Force re-sorting by created_at (column index 7) descending
                    inspectionTable.order([7, 'desc']).draw();

                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                    toastr.success('New inspection received');

                    document.addEventListener('click', () => {
                        const ding = document.getElementById('ding-sound');
                        if (ding) ding.play().catch(err => console.warn('Still blocked:', err));
                    }, {
                        once: true
                    });
                }
            });
        }

        $(document).ready(function() {
            inspectionTable = $('#inspectionTable').DataTable({
                paging: false, // disable DataTables pagination
                searching: false, // disable the search box
                info: false, // remove "Showing 1 to X of Y entries"
                ordering: true, // keep sorting
                responsive: true
            });

            console.log("Auto-refresh started (Inspection)");
            setInterval(fetchLatestInspections, 5000);
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
