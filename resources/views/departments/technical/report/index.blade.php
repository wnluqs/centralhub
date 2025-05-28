@extends('layouts.app')

@section('content')
    <a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
        ← Back to Technical Dashboard
    </a>

    <div class="container">
        <h2 class="text-primary">Reports List</h2>
        <h3 class="text-secondary">Total Reports: {{ $reports->count() }}</h3>

        <div class="mb-3">
            <a href="{{ route('report.export.csv', ['type' => request('type')]) }}" class="btn btn-success">
                Export CSV
            </a>
            <a href="{{ route('report.export.excel', ['type' => request('type')]) }}" class="btn btn-warning">
                Export Excel
            </a>
        </div>

        {{-- Filters: Type + Date Range --}}
        <form method="GET" action="{{ route('report.index') }}" class="row g-2 mb-3 align-items-end">
            <div class="col-md-3">
                <label for="type" class="form-label">Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="BTS" {{ request('type') === 'BTS' ? 'selected' : '' }}>BTS</option>
                    <option value="Complaint" {{ request('type') === 'Complaint' ? 'selected' : '' }}>Complaint</option>
                    <option value="Local" {{ request('type') == 'Local' ? 'selected' : '' }}>Local</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="minDate" class="form-label">Start Date</label>
                <input type="text" id="minDate" name="minDate" class="form-control" placeholder="dd/mm/yyyy">
            </div>
            <div class="col-md-3">
                <label for="maxDate" class="form-label">End Date</label>
                <input type="text" id="maxDate" name="maxDate" class="form-control" placeholder="dd/mm/yyyy">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table id="reportsTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Type</th>
                        <th>Terminal ID</th>
                        <th>Location</th>
                        <th>Event Date</th>
                        <th>Types of Damages</th>
                        <th>Event Code - Name</th>
                        <th>Comment</th>
                        <th>Attended At</th>
                        <th>Fixed At</th>
                        <th>Parts Request</th>
                        <th>Technician</th>
                        <th>Photo</th>
                        <th>Terminal Status</th>
                        @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('TechnicalLead'))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $r)
                        <tr>
                            <td>{{ $r->type ?? '-' }}</td>
                            <td>{{ $r->terminal_id ?? '-' }}</td>
                            <td>{{ $r->location ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->event_date)->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @php
                                    $decoded = is_string($r->types_of_damages)
                                        ? json_decode($r->types_of_damages, true)
                                        : (is_array($r->types_of_damages)
                                            ? $r->types_of_damages
                                            : []);
                                @endphp

                                <ul class="list-unstyled text-start m-0 p-0">
                                    @if (!empty($decoded))
                                        @foreach ($decoded as $label => $value)
                                            <li>
                                                • {{ $label }}:
                                                @if (is_array($value))
                                                    {{ $value['type'] ?? '-' }} ({{ $value['value'] ?? '0' }})
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </li>
                                        @endforeach
                                    @else
                                        <li>-</li>
                                    @endif
                                </ul>
                            </td>
                            <td>{{ $r->event_code_name ?? '-' }}</td>
                            <td>{{ $r->comment ?? '-' }}</td>
                            <td>{{ isset($r->attended_at) ? \Carbon\Carbon::parse($r->attended_at)->format('Y-m-d H:i:s') : '-' }}
                            </td>
                            <td>{{ isset($r->fixed_at) ? \Carbon\Carbon::parse($r->fixed_at)->format('Y-m-d H:i:s') : '-' }}
                            </td>
                            <td>{{ $r->parts_request ?? '-' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $r->technician_name ?? '-' }}</span>
                            </td>
                            <td>
                                @if (!empty($r->photo))
                                    <a href="{{ asset('storage/' . $r->photo) }}" target="_blank"
                                        class="btn btn-sm btn-info">View</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $r->terminal_status === 'Resolved' ? 'success' : 'secondary' }}">
                                    {{ $r->terminal_status ?? '-' }}
                                </span>
                            </td>
                            @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('TechnicalLead'))
                                <td>
                                    {{-- Optional: Add actions for BTS only, or just display "-" --}}
                                    @if ($r->type === 'BTS')
                                        {{-- Example: <a href="{{ route('bts.attend', $r->id) }}" class="btn btn-sm btn-warning">Attend</a> --}}
                                        <span class="badge bg-warning">BTS</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <!-- jQuery UI CSS for Datepicker -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endpush

@push('scripts')
    <!-- jQuery UI + DataTables JS -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        // Custom DataTables date-range filter
        $.fn.dataTable.ext.search.push(function(settings, data, index) {
            const min = $('#minDate').datepicker("getDate");
            const max = $('#maxDate').datepicker("getDate");

            // Column 3 = Event Date (formatted YYYY-MM-DD)
            let dateStr = data[3].split(' ')[0];
            const parts = dateStr.split('-'); // ['2025', '05', '27']
            const eventDate = new Date(parts[0], parts[1] - 1, parts[2]); // JS months are 0-based

            if ((!min && !max) ||
                (!min && eventDate <= max) ||
                (min <= eventDate && !max) ||
                (min <= eventDate && eventDate <= max)) {
                return true;
            }
            return false;
        });

        $(function() {
            // Initialize Datepickers
            $('#minDate, #maxDate').datepicker({
                dateFormat: 'yy-mm-dd' // Matches Y-m-d format from Laravel
            });

            // Initialize DataTable
            const table = $('#reportsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                order: [
                    [3, 'desc']
                ], // Order by Event Date
                columnDefs: [{
                        targets: [10, 11, 12],
                        orderable: false
                    } // Disable sorting on Technician, Photo, Terminal Status
                ],
                language: {
                    search: "Search table:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries"
                }
            });

            // Redraw table on date filter change
            $('#minDate, #maxDate').on('change', () => table.draw());
        });
    </script>
@endpush

@push('styles')
<style>
    td ul {
        padding-left: 1rem;
        margin: 0;
        text-align: left;
    }
</style>
@endpush
