@extends('layouts.app')

@section('content')
<a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
    ← Back to Technical Dashboard
</a>

<div class="container">
    <h2 class="mb-4">Complaints</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('complaints.create') }}" class="btn btn-primary">+ New Complaint</a>
        <a href="{{ route('complaints.export.excel', ['terminal_id'=>request('terminal_id'),'zone'=>request('zone')]) }}"
           class="btn btn-success">Export to Excel</a>
        <a href="{{ route('complaints.export.csv', ['terminal_id'=>request('terminal_id'),'zone'=>request('zone')]) }}"
           class="btn btn-warning">Export to CSV</a>
    </div>

    {{-- Date Filter --}}
    <form method="GET" class="row g-2 mb-4 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('complaints.index') }}" class="btn btn-secondary w-100">Reset</a>
        </div>
    </form>

    {{-- Unified Complaints Table --}}
    <div class="table-responsive">
        <table id="complaintsTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Terminal ID</th>
                    <th>Zone</th>
                    <th>Road</th>
                    <th>Remarks</th>
                    <th>Assigned To</th>
                    <th>Types of Damages</th>
                    <th>Attended At</th>
                    <th>Fixed At</th>
                    <th>Status</th>
                    <th>Photos</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($complaints as $c)
                <tr>
                    <td>{{ $c->terminal_id }}</td>
                    <td>{{ $c->zone }}</td>
                    <td>{{ ucfirst($c->road) }}</td>
                    <td>{{ $c->remarks }}</td>
                    <td>{{ $c->technician->name ?? '-' }}</td>
                    <td>
                        @php $types = json_decode($c->types_of_damages, true); @endphp
                        {{ $types && is_array($types) ? implode(', ', $types) : '-' }}
                    </td>
                    <td>{{ $c->attended_at ? \Carbon\Carbon::parse($c->attended_at)->format('Y-m-d H:i:s') : '-' }}</td>
                    <td>{{ $c->fixed_at ? \Carbon\Carbon::parse($c->fixed_at)->format('Y-m-d H:i:s') : '-' }}</td>
                    <td>
                        <span class="badge bg-{{
                            $c->status === 'Resolved' ? 'success' :
                            ($c->status === 'In Progress' ? 'warning text-dark' : 'secondary')
                        }}">
                            {{ $c->status }}
                        </span>
                    </td>
                    <td>
                        @if($c->photos)
                            @foreach(json_decode($c->photos,true) as $p)
                                <a href="{{ asset('storage/'.$p) }}" target="_blank">View</a><br>
                            @endforeach
                        @else - @endif
                    </td>
                    <td>
                        @if($c->status === 'New')
                            <a href="{{ route('complaints.assign', $c->id) }}" class="btn btn-sm btn-warning">Assign</a>
                        @elseif($c->status === 'In Progress')
                            <a href="{{ route('complaints.markFixed', $c->id) }}" class="btn btn-sm btn-success">Mark as Fixed</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center">No complaints available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
    $(function(){
        $('#complaintsTable').DataTable({
            order: [[7,'desc']],
            language: {
                searchPlaceholder: "Search Complaints..."
            }
        });
    });
    </script>
@endpush
