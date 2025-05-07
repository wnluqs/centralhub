@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card p-4 shadow-lg rounded bg-white" style="max-width: 500px; width: 100%;">
        <a href="{{ route('ftlt.index') }}" class="btn btn-secondary mb-3">← Back to FTLT</a>
        <h4 class="mb-3 text-success">Technician Check-Out</h4>

        <!-- Inner soft grey box -->
        <div class="p-3 mb-3 rounded" style="background-color: #e9ecef; color: #212529;">
            <p><strong>Staff ID:</strong> {{ $ftlt->staff_id }}</p>
            <p><strong>Name:</strong> {{ $ftlt->user->name ?? '-' }}</p>
            <p><strong>Location:</strong> {{ $ftlt->location }}</p>
            <p><strong>Check-In Time:</strong> {{ $ftlt->check_in_time }}</p>
        </div>

        <form method="POST" action="{{ route('ftlt.checkout.submit', $ftlt->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group mb-3">
                <label class="text-dark">Check-Out Photo</label>
                <input type="file" name="checkout_photo" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Submit Check-Out</button>
        </form>
    </div>
</div>
@endsection
