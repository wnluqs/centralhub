@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
            ← Back to Technical Dashboard
        </a>

        <h2>Terminal Parking System</h2>
        <p>View the current status and locations of all terminal boxes in the parking system.</p>

        <!-- Filter Form -->
        <form action="{{ route('technical.terminal_parking') }}" method="GET" class="mb-3">
            <div class="row mt-3">
                <div class="col-md-12 text-end">
                    <a href="{{ route('technical.terminal_parking.export.csv', request()->only('search', 'terminal_number', 'location', 'zone_code')) }}"
                        class="btn btn-success me-2">Export CSV</a>
                    <a href="{{ route('technical.terminal_parking.export.excel', request()->only('search', 'terminal_number', 'location', 'zone_code')) }}"
                        class="btn btn-primary">Export Excel</a>
                </div>
            </div>
        </form>

        <!-- Data Table -->
        <table id="terminalTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Terminal Number</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($terminals as $terminal)
                    <tr>
                        <td>{{ $terminal->terminal?->id ?? ($terminal->terminal_id ?? 'N/A') }}</td>
                        <td>{{ $terminal->branch ?? '-' }}</td>
                        <td>{{ $terminal->status ?? '-' }}</td>
                        <td>{{ $terminal->location ?? 'No Location' }}</td>
                        <td>{{ $terminal->latitude ?? '0.0000000' }}</td>
                        <td>{{ $terminal->longitude ?? '0.0000000' }}</td>
                        <td>
                            <a href="{{ route('terminal_parking.edit_location', $terminal->id) }}"
                                class="btn btn-sm btn-warning">Edit Location</a>
                        </td>
                    </tr>
                @endforeach

                <!-- ✅ Add demo rows inside the same tbody -->
                <tr>
                    <td>TEST001</td>
                    <td>HQ</td>
                    <td>Active</td>
                    <td>Kuala Lumpur</td>
                    <td>3.1390</td>
                    <td>101.6869</td>
                    <td><button class="btn btn-sm btn-secondary" disabled>Edit</button></td>
                </tr>
                <tr>
                    <td>TEST002</td>
                    <td>HQ</td>
                    <td>Inactive</td>
                    <td>Kuala Lumpur</td>
                    <td>3.2320</td>
                    <td>101.7000</td>
                    <td><button class="btn btn-sm btn-secondary" disabled>Edit</button></td>
                </tr>
            </tbody>
        </table>

        <!-- Google Map -->
        <div id="map" style="height: 400px;"></div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#terminalTable').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ]
            });
        });

        // ✅ Global initMap function
        window.initMap = function() {
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8,
                center: {
                    lat: 3.1390,
                    lng: 101.6869
                }
            });

            const terminals = @json($terminals);

            terminals.push({
                terminal: {
                    id: 'TEST001'
                },
                latitude: '3.1390',
                longitude: '101.6869',
                location: 'Kuala Lumpur',
                status: 'Active',
                branch: 'HQ'
            }, {
                terminal: {
                    id: 'TEST002'
                },
                latitude: '3.2320',
                longitude: '101.7000',
                location: 'Kuala Lumpur',
                status: 'Inactive',
                branch: 'HQ'
            });

            const infoWindow = new google.maps.InfoWindow();
            const bounds = new google.maps.LatLngBounds();

            terminals.forEach(t => {
                const lat = parseFloat(t.latitude);
                const lng = parseFloat(t.longitude);

                if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                    const marker = new google.maps.Marker({
                        position: {
                            lat,
                            lng
                        },
                        map: map,
                        title: t.terminal?.id ?? 'Unknown'
                    });

                    bounds.extend(marker.getPosition());

                    marker.addListener('click', () => {
                        infoWindow.setContent(`
                            <div style="font-size:14px;">
                                <strong>${t.terminal?.id ?? 'N/A'}</strong><br>
                                <b>Branch:</b> ${t.branch ?? 'N/A'}<br>
                                <b>Status:</b> ${t.status ?? 'Unknown'}
                            </div>
                        `);
                        infoWindow.open(map, marker);
                    });
                }
            });

            map.fitBounds(bounds);
        };
    </script>

    <!-- ✅ Script must come AFTER defining initMap -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAHrTNapLSbPaD2ViANNf_ptGGvVxVf6Rs&callback=initMap"></script>
@endpush
