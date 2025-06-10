@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">← Back to Technical Dashboard</a>
        <h2 class="mb-4 text-primary">🔋 Battery Replacement Module</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('ControlCenter'))
            <a href="{{ route('battery.create') }}" class="btn btn-primary mb-3">+ Create New Battery Replacement</a>
        @endif

        <form method="GET" class="row mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
                <div>
                    <a href="{{ route('battery.export.excel') }}" class="btn btn-success me-2">
                        🧾 Export to Excel
                    </a>
                    <a href="{{ route('battery.export.csv') }}" class="btn btn-warning">
                        📄 Export to CSV
                    </a>
                </div>
                <form method="GET" class="d-flex align-items-end gap-2">
                    <div>
                        <label for="start_date">Start Date</label>
                        <input type="datetime-local" name="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>
                    <div>
                        <label for="end_date">End Date</label>
                        <input type="datetime-local" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <button type="submit" class="btn btn-info mt-2">🔍 Filter</button>
                </form>
            </div>
        </form>
        {{-- Available Jobs --}}
        <h4 class="text-primary">📋 Available Jobs</h4>
        @php
            $available = $jobs->where('status', 'Assigned');
            $inProgress = $jobs->where('status', 'Submitted');
        @endphp

        @if ($available->isEmpty())
            <p>No available jobs right now.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered" id="availableJobs">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Terminal ID</th>
                            <th>Status</th>
                            <th>Technician</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($available as $job)
                            <tr class="{{ session('highlight_id') == $job->id ? 'newly-added' : '' }}">
                                <td>{{ $job->id }}</td>
                                <td>{{ $job->terminal_id }}</td>
                                <td><span class="badge bg-warning">{{ $job->status }}</span></td>
                                <td>{{ $job->staff_id ?? '-' }}</td>
                                <td>{{ $job->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $job->creator->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('battery.attend', $job->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        Attend
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Submitted --}}
        <h4 class="text-primary mt-4">✅ Submitted</h4>
        @if ($inProgress->isEmpty())
            <p>No submitted battery replacements.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered" id="inProgressJobs">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Terminal ID</th>
                            <th>Status</th>
                            <th>Technician</th>
                            <th>Submitted Photo</th>
                            <th>Submitted</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inProgress as $job)
                            <tr>
                                <td>{{ $job->id }}</td>
                                <td>{{ $job->terminal_id }}</td>
                                <td><span class="badge bg-success">{{ $job->status }}</span></td>
                                <td>{{ $job->staff_id ?? '-' }}</td>
                                <td>
                                    @if ($job->photo)
                                        <a href="{{ asset('storage/' . $job->photo) }}" target="_blank">View</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $job->created_at->format('d/m/Y h:i A') }} </td>
                                <td>{{ $job->comment ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <style>
        .newly-added {
            animation: flashHighlight 2.5s ease-in-out;
            box-shadow: 0 0 10px 2px #00c851;
        }

        @keyframes flashHighlight {
            0% {
                background-color: #c8f7c5;
            }

            50% {
                background-color: #88f088;
            }

            100% {
                background-color: transparent;
            }
        }
    </style>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.5/sorting/datetime-moment.js"></script>

    <script>
        $(document).ready(function() {
            $.fn.dataTable.moment('DD/MM/YYYY HH:mm');

            $('#availableJobs').DataTable({
                order: [
                    [4, 'desc']
                ]
            });

            $('#inProgressJobs').DataTable({
                order: [
                    [5, 'desc']
                ]
            });

            setTimeout(() => {
                $('.newly-added').removeClass('newly-added');
            }, 3000);
        });

        // Optional: Refresh the page every 30s
        setInterval(() => location.reload(true), 30000);
    </script>
@endpush
