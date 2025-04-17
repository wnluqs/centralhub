@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Inspection Details</h2>

    <p><strong>Terminal ID:</strong> {{ $inspection->terminal_id }}</p>
    <p><strong>Zone:</strong> {{ $inspection->zone }}</p>
    <p><strong>Road:</strong> {{ $inspection->road }}</p>
    <p><strong>Technician:</strong> {{ $inspection->technician_name }}</p>
    <p><strong>Status:</strong> <span class="badge badge-{{ strtolower($inspection->status) }}">{{ $inspection->status
            }}</span></p>

    <h4>Spare Parts Used</h4>
    <ul>
        <li>{{ $inspection->spare_part_1 }}</li>
        <li>{{ $inspection->spare_part_2 }}</li>
        <li>{{ $inspection->spare_part_3 }}</li>
    </ul>

    <h4>Inspection Photos</h4>
    @foreach(json_decode($inspection->photos, true) as $photo)
    <img src="{{ asset('storage/' . $photo) }}" class="img-fluid mb-3" width="300">
    @endforeach

    <a href="{{ route('inspections.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection