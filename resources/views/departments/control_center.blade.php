@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Control Center Department</h1>
    <p style="color: beige; font-size: 20px">Welcome to the Control Center.</p>
    <p style="color: beige; font-size: 20px">
        The Control Center handles call center operations, public complaints,
        scheduling, and more.
    </p>

    <hr>

    <!-- Control Center Dashboard Icons -->
    <div class="row">
        <!-- Icon 1: Complaints -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('controlcenter-complaints') }}" class="dashboard-card">
                <img src="{{ asset('images/technical/complaint.png') }}" alt="Complaints Icon">
                <h5>Complaints</h5>
            </a>
        </div>

        <!-- Icon 2: Call Centre -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('technical-inspections') }}" class="dashboard-card">
                <img src="{{ asset('images/controlcenter/callcentre.png') }}" alt="Call Centre Icon">
                <h5>Call Centre</h5>
            </a>
        </div>

        <!-- Icon 3: Scheduling -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('technical-complaints') }}" class="dashboard-card">
                <img src="{{ asset('images/controlcenter/schedule.png') }}" alt="Scheduling Icon">
                <h5>Member & Patrol Scheduling</h5>
            </a>
        </div>

        <!-- Icon 4: Other Features (Patrol / Action Logs) -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('technical.audit') }}" class="dashboard-card">
                <img src="{{ asset('images/controlcenter/patrol.png') }}" alt="Action Icon">
                <h5>Buku Tindakan Segera</h5>
            </a>
        </div>
    </div>
</div>
@endsection