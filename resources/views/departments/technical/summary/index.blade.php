@extends('layouts.app')

@section('content')
<a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
    ← Back to Technical Dashboard
</a>

<div class="container">
    <h2>Reports List</h2>
    <a href="{{ route('summary.create') }}" class="btn btn-primary mb-3">Add New Report</a>
    <a href="{{ route('summary.export.csv', [
        'terminal' => request('terminal'),
        'spare_part' => request('spare_part')
    ]) }}" class="btn btn-success mb-3">Export CSV</a>
    <a href="{{ route('summary.export.excel', [
        'terminal' => request('terminal'),
        'spare_part' => request('spare_part')
    ]) }}" class="btn btn-warning mb-3">Export Excel</a>
    <form method="GET" action="{{ route('summary.index') }}" class="mb-3">
        <input type="text" name="terminal" placeholder="Terminal ID" value="{{ request('terminal') }}">
        <input type="text" name="spare_part" placeholder="Spare Part" value="{{ request('spare_part') }}">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Terminal ID</th>
                <th>Created At</th>
                <th>Spare Part 1</th>
                <th>Spare Part 2</th>
                <th>Spare Part 3</th>
                <th>Status</th>
                {{-- Show "Actions" column only if the authenticated user is an Admin or a TechnicalLead --}}
                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('TechnicalLead'))
                <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
            <tr>
                <td>{{ $report->terminal->id }}</td>
                <td>{{ $report->created_at }}</td>
                <td>{{ $report->spare_part_1 }}</td>
                <td>{{ $report->spare_part_2 }}</td>
                <td>{{ $report->spare_part_3 }}</td>
                <td>{{ $report->status }}</td>
                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('TechnicalLead'))
                <td>
                    <a href="{{ route('summary.edit', $report->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('summary.destroy', $report->id) }}" method="POST" style="display:inline;"
                        onsubmit="return confirm('Are you sure you want to delete this row data?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection