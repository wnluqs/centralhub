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
                    <input type="text" name="technician_name" class="form-control" placeholder="Search Technician"
                        value="{{ request('technician_name') }}">
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

        {{-- Dynamic Alert Always Visible if Filters Applied --}}
        @if (request('start_date') ||
                request('end_date') ||
                request('terminal_id') ||
                request('zone') ||
                request('road') ||
                request('branch') ||
                request('status') ||
                request('technician_name') ||
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
                @if (request('technician_name'))
                    <br>Technician Name contains: <strong>{{ request('technician_name') }}</strong>
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
            <table class="table table-bordered table-striped table-sm text-center align-middle">
                {{-- Your table headers and foreach --}}
            </table>
        @endif

        @if ($inspections->isEmpty())
            <p style="color: red;">No inspections found.</p>
        @else
            <table class="table table-bordered table-striped table-sm text-center align-middle">
                <thead class="thead-dark text-center">
                    <tr>
                        <th>Terminal ID</th>
                        <th>Zone</th>
                        <th>Road</th>
                        <th>Branch</th>
                        <th>Spare Parts</th>
                        <th>Status</th>
                        <th>Technician</th>
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
                            <td>{{ $inspection->technician_name }}</td>
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
