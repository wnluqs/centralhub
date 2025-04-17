@extends('layouts.app')

@section('content')
<a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
    ← Back to Technical Dashboard
</a>

<div class="container">
    <h1>Inspection Records</h1>

    <a href="{{ route('inspections.create') }}" class="btn btn-primary mb-3">+ Add New Inspection</a>
    <form action="{{ route('technical-inspections') }}" method="GET" class="mb-3">
        <input type="text" name="search" placeholder="Terminal ID" value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Filter</button>
        <!-- Export Buttons -->
        <a href="{{ route('inspections.export.csv', ['search' => request('search')]) }}"
            class="btn btn-success ml-2">Export
            CSV</a>
        <a href="{{ route('inspections.export.excel', ['search' => request('search')]) }}"
            class="btn btn-success ml-2">Export Excel</a>
    </form>

    @if($inspections->isEmpty())
    <p style="color: red;">No inspections found.</p>
    @else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Terminal ID</th>
                <th>Zone</th>
                <th>Road</th>
                <th>Spare Part 1</th>
                <th>Spare Part 2</th>
                <th>Spare Part 3</th>
                <th>Status</th>
                <th>Technician</th>
                <th>Created At</th>
                <th>Photos/Videos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspections as $inspection)
            <tr>
                <td>{{ $inspection->terminal_id }}</td>
                <td>{{ $inspection->zone }}</td>
                <td>{{ $inspection->road }}</td>
                <td>{{ $inspection->spare_part_1 }}</td>
                <td>{{ $inspection->spare_part_2 }}</td>
                <td>{{ $inspection->spare_part_3 }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($inspection->status) }}">
                        {{ $inspection->status }}
                    </span>
                </td>
                <td>{{ $inspection->technician_name }}</td>
                <td>{{ $inspection->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                    @if($inspection->photos)
                    @php
                    // Get file extension to determine if it's an image or video
                    $extension = pathinfo($inspection->photos, PATHINFO_EXTENSION);
                    $extension = strtolower($extension); // normalize case
                    @endphp

                    @if(in_array($extension, ['jpg','jpeg','png']))
                    <!-- It's an image -->
                    <img src="{{ asset('storage/' . $inspection->photos) }}" width="100" alt="Inspection Photo">
                    @elseif(in_array($extension, ['mp4','mov','avi']))
                    <!-- It's a video -->
                    <video width="150" height="auto" controls>
                        <source src="{{ asset('storage/' . $inspection->photos) }}" type="video/{{ $extension }}">
                        Your browser does not support the video tag.
                    </video>
                    @else
                    <!-- Unrecognized format or none -->
                    <span style="color: gray;">File format not supported</span>
                    @endif

                    @else
                    <span style="color: gray;">No photo/video</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
</div>
@endsection