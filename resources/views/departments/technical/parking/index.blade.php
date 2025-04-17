@extends('layouts.app')

@section('content')

<div class="container">
    <a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
        ← Back to Technical Dashboard
    </a>

    <h2>Terminal Parking System</h2>
    <p>Here you can view all the terminals in the parking system. This is to provided the current status of the Terminal
        Boxes </p>

    <form action="{{ route('technical.terminal_parking') }}" method="GET" class="mb-3">
        <div class="row g-2 align-items-center">
            <!-- Terminal Number Input -->
            <div class="col-md-3">
                <label class="form-label fw-bold">Terminal Number</label>
                <input type="text" name="terminal_number" class="form-control" placeholder="e.g., TN-01"
                    value="{{ request('terminal_number') }}">
            </div>

            <!-- Location Input -->
            <div class="col-md-3">
                <label class="form-label fw-bold">Location</label>
                <input type="text" name="location" class="form-control" placeholder="e.g., Bukit Bintang Parking"
                    value="{{ request('location') }}">
            </div>

            <!-- Zone Code Input -->
            <div class="col-md-3">
                <label class="form-label fw-bold">Zone Code</label>
                <input type="text" name="zone_code" class="form-control" placeholder="e.g., UP-0001"
                    value="{{ request('zone_code') }}">
            </div>

            <!-- Filter Button -->
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('technical.terminal_parking.export.csv', request()->only('search', 'terminal_number', 'location', 'zone_code')) }}"
                    class="btn btn-success me-2">Export CSV</a>
                <a href="{{ route('technical.terminal_parking.export.excel', request()->only('search', 'terminal_number', 'location', 'zone_code')) }}"
                    class="btn btn-primary">Export Excel</a>
            </div>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Terminal Number</th>
                <th>Status</th>
                <th>Zone Code</th>
                <th>Last Communication</th>
                <th>Battery Health</th>
                <th>Location</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($terminals as $terminal)
            <tr>
                <td>{{ $terminal->number }}</td>
                <td>{{ $terminal->status }}</td>
                <td>{{ $terminal->zone_code }}</td>
                <td>{{ $terminal->last_communication }}</td>
                <td class="
                    @if($terminal->battery_health == 'Full')
                        battery-full
                    @elseif($terminal->battery_health == 'Half')
                        battery-half
                    @elseif($terminal->battery_health == 'Depleted')
                        battery-depleted
                    @endif
                ">
                    {{ $terminal->battery_health }}
                </td>
                <td>{{ $terminal->location }}</td>
                <td>{{ $terminal->latitude }}</td>
                <td>{{ $terminal->longitude }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div id="map" style="height: 400px;"></div>
</div>

<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: { lat: 3.1390, lng: 101.6869 }
        });

        var terminals = @json($terminals);

        terminals.forEach(function(terminal) {
            new google.maps.Marker({
                position: { lat: parseFloat(terminal.latitude), lng: parseFloat(terminal.longitude) },
                map: map,
                title: terminal.terminal_number
            });
        });
    }
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAHrTNapLSbPaD2ViANNf_ptGGvVxVf6Rs&callback=initMap">
</script>
@endsection