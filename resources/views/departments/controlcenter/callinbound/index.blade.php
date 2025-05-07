@extends('layouts.app')

@section('content')
    <a href="{{ route('departments.controlcenter') }}" class="btn btn-secondary mb-3">
        ← Back to Control Center Dashboard
    </a>

    <div class="container">
        <h2 class="text-primary">Call Inbound Records</h2>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('controlcenter.callinbound.export') }}" class="btn btn-warning">Export to Excel</a>
                <a href="{{ route('controlcenter.callinbound.create') }}" class="btn btn-success">Add New Call</a>
            </div>
        </div>

        <div class="table-responsive">
            <form method="GET" class="row g-2 mb-4 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
                <div class="col-md-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('controlcenter.callinbound.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Caller Name</th>
                        <th>Phone</th>
                        <th>Call Time</th>
                        <th>Category</th>
                        <th>Notes</th>
                        <th>Department Referred</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calls as $call)
                        <tr>
                            <td>{{ $call->id }}</td>
                            <td>{{ $call->caller_name }}</td>
                            <td>{{ $call->phone ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($call->call_time)->format('d/m/Y H:i') }}</td>
                            <td>{{ $call->category ?? '-' }}</td>
                            <td>{{ $call->notes ?? '-' }}</td>
                            <td>{{ $call->department_referred ?? '-' }}</td>
                            <td>
                                <a href="{{ route('controlcenter.callinbound.edit', $call->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('controlcenter.callinbound.destroy', $call->id) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this call record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                order: [
                    [3, 'desc']
                ], // Call Time column is 4th (index 3)
                columnDefs: [{
                        targets: -1,
                        orderable: false
                    } // Disable sorting on Actions
                ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                }
            });
        });
    </script>
@endpush
