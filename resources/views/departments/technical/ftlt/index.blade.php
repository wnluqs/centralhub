@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">← Back to Technical Dashboard</a>
        <h2 class="mb-3">First Ticket, Last Ticket (FTLT)</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('ftlt.create') }}" class="btn btn-primary mb-4">+ Technician Check-In</a>
        @role('Admin||TechnicalLead')
            <!-- Filter/Search -->
            <form method="GET" class="row g-2 align-items-end mb-4">

                <!-- Search Staff ID -->
                <div class="col-md-4">
                    <label for="staff_id" class="form-label">Staff ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="staff_id" id="staff_id" class="form-control" placeholder="e.g. V007"
                            value="{{ request('staff_id') }}">
                    </div>
                </div>

                <!-- Filter by Zone -->
                <div class="col-md-3">
                    <label for="zone" class="form-label">Zone</label>
                    <select name="zone" id="zone" class="form-select">
                        <option value="">All Zones</option>
                        @foreach (['Kuala Terengganu', 'Kuantan', 'Machang'] as $z)
                            <option value="{{ $z }}" {{ request('zone') == $z ? 'selected' : '' }}>
                                {{ $z }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Time -->
                <div class="col-md-2">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                        value="{{ request('start_time') }}">
                </div>

                <!-- End Time -->
                <div class="col-md-2">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" value="{{ request('end_time') }}">
                </div>

                <!-- Buttons -->
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-funnel-fill"></i></button>
                    <a href="{{ route('ftlt.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        @endrole
        <!-- Technician Table -->
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Zone</th>
                        <th>Location</th>
                        <th>Check-In Time</th>
                        <th>Check-Out Time</th>
                        <th>Check-In Photo</th>
                        <th>Check-Out Photo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ftlts as $index => $ftlt)
                        <tr>
                            <td>{{ ($ftlts->currentPage() - 1) * $ftlts->perPage() + $index + 1 }}</td>
                            <td>{{ $ftlt->staff_id }}</td>
                            <td>{{ $ftlt->user->name ?? '-' }}</td>
                            <td>{{ $ftlt->zone ?? '-' }}</td>
                            <td>{{ $ftlt->location }}</td>
                            <td>{{ $ftlt->check_in_time }}</td>
                            <td>{{ $ftlt->check_out_time ?? '-' }}</td>
                            <td>
                                @if ($ftlt->checkin_photo)
                                    <a href="{{ asset('storage/' . $ftlt->checkin_photo) }}" target="_blank"
                                        class="btn btn-outline-light btn-sm">View</a>
                                @endif
                            </td>
                            <td>
                                @if ($ftlt->check_out_time)
                                    <a href="{{ asset('storage/' . $ftlt->checkout_photo) }}" target="_blank"
                                        class="btn btn-outline-light btn-sm">View</a>
                                @else
                                    @if (auth()->user()->hasRole('Admin') || $ftlt->staff_id === auth()->user()->staff_id)
                                        <a href="{{ route('ftlt.checkout', $ftlt->id) }}"
                                            class="btn btn-warning btn-sm">Check-Out</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $ftlts->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
