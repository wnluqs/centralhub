@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('ftlt.index') }}" class="btn btn-secondary mb-3">← Back to FTLT</a>
        <h2>Technician Check-In</h2>

        <form method="POST" action="{{ route('ftlt.store') }}" enctype="multipart/form-data">
            @csrf

            @php
                $locations = ['Kuala Terengganu', 'Kuantan', 'Machang'];
            @endphp


            <div class="form-group">
            </div> <!-- Fixed indentation and closing tag -->

            <div class="form-group">
                <label for="location">Location</label>
                <select name="location" class="form-control" required>
                    <option value="" disabled selected>Select Location</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc }}">{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="zone">Zone</label>
                <select name="zone" class="form-control" required>
                    <option value="" disabled selected>Select Zone</option>
                    <option>Ampang</option>
                    <option>Mount Kiara</option>
                    <option>KLCC</option>
                    <option>Chincatown</option>
                    <option>Bukit Bintang</option>
                    <option>Bangsar</option>
                </select>
            </div>
            @if (auth()->check())
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            @endif

            <input type="hidden" name="check_in_time" value="{{ now() }}">

            <div class="form-group">
                <label>Check-In Photo</label>
                <input type="file" name="checkin_photo" class="form-control" accept="image/*" required>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Enter notes (optional)"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Submit Check-In</button>
        </form>
    </div>
@endsection
