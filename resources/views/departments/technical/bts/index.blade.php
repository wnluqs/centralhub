@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">← Back to Technical Dashboard</a>
        <h2 class="mb-4 text-light">Buku Tindakan Segera (BTS)</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('bts.create') }}" class="btn btn-primary mb-3">+ New BTS Entry</a>

        {{-- Available Jobs --}}
        <h4 class="text-light">Available Jobs</h4>
        @if ($available->isEmpty())
            <p class="text-light">No available jobs right now.</p>
        @else
            @include('components.bts_table', [
                'data' => $available,
                'mode' => 'available',
                'tableId' => 'availableJobs',
            ])
        @endif

        {{-- In Progress --}}
        <h4 class="text-light">In Progress</h4>
        @if ($inProgress->isEmpty())
            <p class="text-light">No jobs in progress.</p>
        @else
            @include('components.bts_table', [
                'data' => $inProgress,
                'mode' => 'in_progress',
                'tableId' => 'inProgressJobs',
            ])
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
            // ✅ Register Moment.js for both tables
            $.fn.dataTable.moment('DD/MM/YYYY HH:mm'); // Use correct format!

            // ✅ Available Jobs Table (Event Date = index 3)
            $('#availableJobs').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "order": [
                    [3, 'desc']
                ],
                "info": true,
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "search": "Quick Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "zeroRecords": "No matching records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "No entries available",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                }
            });

            // ✅ In Progress Table (Created At = index 7)
            $('#inProgressJobs').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "order": [
                    [7, 'desc']
                ],
                "info": true,
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "search": "Quick Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "zeroRecords": "No matching records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "No entries available",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                }
            });
        });

        setTimeout(() => {
            document.querySelectorAll('.newly-added').forEach(el => {
                el.classList.remove('newly-added');
            });
        }, 3000);
    </script>
@endpush
