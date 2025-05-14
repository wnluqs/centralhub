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
            <a href="{{ route('complaints.export.excel', ['terminal_id' => request('terminal_id'), 'zone' => request('zone')]) }}"
                class="btn btn-success">Export to Excel</a>
            <a href="{{ route('complaints.export.csv', ['terminal_id' => request('terminal_id'), 'zone' => request('zone')]) }}"
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

        {{-- Available Jobs Table --}}
        <h4>🟢 Available Jobs (Status: New)</h4>
        @include('departments.technical.complaint.partials.table', [
            'complaints' => $available,
            'tableId' => 'availableTable',
        ])

        {{-- In Progress / Resolved Table --}}
        <h4 class="mt-5">🚲 In Progress / ✅ Resolved</h4>
        @include('departments.technical.complaint.partials.table', [
            'complaints' => $inProgress,
            'tableId' => 'progressTable',
        ])
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            ['availableTable', 'progressTable'].forEach(function (tableId) {
                const $table = $('#' + tableId);
                if ($table.length && $.fn.DataTable.isDataTable('#' + tableId)) {
                    $table.DataTable().clear().destroy(); // 👈 kill previous instance
                }
                if ($table.length) {
                    $table.DataTable({
                        order: [[10, 'desc']],
                        columnDefs: [{ targets: [10], visible: false }],
                        language: {
                            searchPlaceholder: tableId === 'availableTable'
                                ? "Search Available..."
                                : "Search In Progress / Resolved..."
                        }
                    });
                }
            });
        });
    </script>
@endpush

