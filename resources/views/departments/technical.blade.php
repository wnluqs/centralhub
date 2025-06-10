@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Technical Department</h1>
        <p style="color: rgb(57, 28, 223); font-size: 20px">Welcome to the Technical Side.</p>
        <p style="color: rgb(28, 98, 228); font-size: 20px">The team operation consist of multiple factions from on-site
            work, R&D and
            Stuff</p>
        <!-- If you want to keep the Export to Excel feature, leave this button -->
        {{-- <a href="{{ route('on_site_project.export') }}" class="btn btn-success mb-3">
        Export to Excel
    </a> --}}

        <hr>

        <!-- Technical Department Dashboard Icons -->
        <!-- 🧰 DAILY OPERATIONS -->
        <h4 class="mt-4 mb-3 text-primary">🧰 Daily Operations</h4>
        <div class="row">
            <div class="col-md-4 mb-4">
                <a href="{{ route('technical-report') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/summary.png') }}" alt="Summary Icon">
                    <h5>Report</h5>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('technical-inspections') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/inspect.png') }}" alt="Inspections Icon">
                    <h5>Inspections</h5>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('technical-complaints') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/complaint.png') }}" alt="Complaints Icon">
                    <h5>Complaints</h5>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('battery.index') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/battery.png') }}" alt="Battery Replacement Icon">
                    <h5>Battery Replacement</h5>
                </a>
            </div>
        </div>

        <!-- 🛠️ FIELD TOOLS -->
        <h4 class="mt-4 mb-3 text-primary">🛠️ Field Tools</h4>
        <div class="row">
            <div class="col-md-4 mb-4">
                <a href="{{ route('technical.terminal_parking') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/parking.png') }}" alt="Terminal Parking Icon">
                    <h5>Terminal Parking</h5>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('technical-local_report') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/local_report.png') }}" alt="Local Report Icon">
                    <h5>Laporan Setempat</h5>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('ftlt.index') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/ticket.png') }}" alt="First Ticket Last Ticket Icon">
                    <h5>First Ticket Last Ticket</h5>
                </a>
            </div>
        </div>

        <!-- 📊 MONITORING & LOGS -->
        <h4 class="mt-4 mb-3 text-primary">📊 Monitoring & Logs</h4>
        <div class="row">
            <div class="col-md-4 mb-4">
                <a href="{{ route('technical.audit') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/audit.png') }}" alt="Audit Icon">
                    <h5>Activity Logs</h5>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('technical.something_else') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/analysis.png') }}" alt="Analysis Icon">
                    <h5>Analysis & Statistics</h5>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('bts.index') }}" class="dashboard-card">
                    <img src="{{ asset('images/technical/book.png') }}" alt="Buku Tindakan Segera Icon">
                    <h5>Buku Tindakan Segera</h5>
                </a>
            </div>
        </div>
    </div>
@endsection
