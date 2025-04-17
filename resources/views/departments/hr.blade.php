@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">HR Department Dashboard</h1>
    <p class="mb-4" style="font-size: 18px; color: #555;">
        Manage your HR related activities from Work From Home requests to Annual Leave and Attendance records.
        Click an icon below to proceed.
    </p>

    <!-- Inline styles for dashboard cards -->
    <style>
        .dashboard-card {
            display: block;
            text-align: center;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            margin: 10px 0;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            text-decoration: none;
        }

        .dashboard-card img {
            max-width: 100px;
            margin-bottom: 10px;
        }

        .dashboard-card h5 {
            font-size: 1.2rem;
            margin-top: 0;
            color: #333;
        }
    </style>

    <div class="row">
        <!-- Icon 1: Work From Home -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('wfh.index') }}" class="dashboard-card">
                <img src="{{ asset('images/hr/wfh.png') }}" alt="Work From Home Icon">
                <h5>Work From Home</h5>
            </a>
        </div>

        <!-- Icon 2: Annual Leave (Placeholder) -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('hr.al') }}" class="dashboard-card">
                <img src="{{ asset('images/hr/annual_leave.png') }}" alt="Annual Leave Icon">
                <h5>Annual Leave</h5>
            </a>
        </div>

        <!-- Icon 3: Attendance (Placeholder) -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('hr.attendance') }}" class="dashboard-card">
                <img src="{{ asset('images/hr/attendance.png') }}" alt="Attendance Icon">
                <h5>Attendance</h5>
            </a>
        </div>
    </div>
</div>
@endsection