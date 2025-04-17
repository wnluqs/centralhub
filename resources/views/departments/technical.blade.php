@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Technical Department</h1>
    <p style="color: beige; font-size: 20px">Welcome to the Technical Side.</p>
    <p style="color: beige; font-size: 20px">The team operation consist of multiple factions from on-site work, R&D and
        Stuff</p>
    <!-- If you want to keep the Export to Excel feature, leave this button -->
    {{-- <a href="{{ route('on_site_project.export') }}" class="btn btn-success mb-3">
        Export to Excel
    </a> --}}

    <hr>

    <!-- Technical Department Dashboard Icons -->
    <div class="row">
        <!-- Icon 1: Summary Report -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('technical-summary') }}" class="dashboard-card">
                <img src="{{ asset('images/technical/summary.png') }}" alt="Summary Icon">
                <h5>Report</h5>
            </a>
        </div>

        <!-- Icon 2: Inspections -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('technical-inspections') }}" class="dashboard-card">
                <img src="{{ asset('images/technical/inspect.png') }}" alt="Inspections Icon">
                <h5>Inspections</h5>
            </a>
        </div>

        <!-- Icon 3: Complaints -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('technical-complaints') }}" class="dashboard-card">
                <img src="{{ asset('images/technical/complaint.png') }}" alt="Complaints Icon">
                <h5>Complaints</h5>
            </a>
        </div>

        <!-- Icon 4: Terminal Parking -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('technical.terminal_parking') }}" class="dashboard-card">
                <img src="{{ asset('images/technical/parking.png') }}" alt="Terminal Parking Icon">
                <h5>Terminal Parking</h5>
            </a>
        </div>

        <!-- Icon 5: Audit Trail -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('technical.audit') }}" class="dashboard-card">
                <img src="{{ asset('images/technical/audit.png') }}" alt="Audit Icon">
                <h5>Activity Logs</h5>
            </a>
        </div>

        <!-- Icon 6: Extra Feature -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('technical.something_else') }}" class="dashboard-card">
                <img src="{{ asset('images/technical/analysis.png') }}" alt="Analysis Icon">
                <h5>Analysis & Statistics</h5>
            </a>
        </div>
    </div>
</div>
@endsection