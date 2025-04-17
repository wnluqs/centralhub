@extends('layouts.app')

@section('content')
<div id="content">

    <div class="overlay">
        <h2 class="text-black">Internal Site Project</h2>
        <p style="color: grey">Hello, welcome to Vista Summerose Internal Website.</p>
        <p style="color: grey">You can see here we have multiple departments in this organization.</p>
        <div class="department-icons">
            {{-- HR Icon: Only show if user can access HR department --}}
            <a href="{{ route('departments.hr') }}" class="icon-link">
                <img src="{{ asset('images/traffic.jpg') }}" alt="Traffic Management">
                <span>Traffic Management</span>
            </a>

            {{-- Accounting Icon --}}
            <a href="{{ route('departments.accounting') }}" class="icon-link">
                <img src="{{ asset('images/citycarpark.png') }}" alt="CityCarPark App">
                <span>CityCarPark App</span>
            </a>

            {{-- Operations Icon --}}
            <a href="{{ route('departments.operations') }}" class="icon-link">
                <img src="{{ asset('images/erp.jpg') }}" alt="ERP">
                <span>HandHeld Manager</span>
            </a>
        </div>
    </div>
</div>
@endsection