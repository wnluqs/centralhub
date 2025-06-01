@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('inspections.index') }}" class="btn btn-secondary mb-3">
            ← Back to Inspections
        </a>

        <h1>Inspection Details</h1>

        <table class="table table-bordered">
            <tr>
                <th>Terminal ID</th>
                <td>{{ $inspection->terminal_id }}</td>
            </tr>
            <tr>
                <th>Zone</th>
                <td>{{ $inspection->zone }}</td>
            </tr>
            <tr>
                <th>Road</th>
                <td>{{ $inspection->road }}</td>
            </tr>
            <tr>
                <th>Branch</th>
                <td>{{ $inspection->branch }}</td>
            </tr>
            <tr>
                <th>Spare Parts</th>
                <td>{{ implode(', ', (array) $inspection->spare_parts) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $inspection->status }}</td>
            </tr>
            <tr>
                <th>Submitted_By</th>
                <td>{{ $inspection->submitted_by }}</td>
            </tr>
            <tr>
                <th>Screen Condition</th>
                <td>{{ $inspection->screen }}</td>
            </tr>
            <tr>
                <th>Keypad Condition</th>
                <td>{{ $inspection->keypad }}</td>
            </tr>
            <tr>
                <th>Sticker Condition</th>
                <td>{{ $inspection->sticker }}</td>
            </tr>
            <tr>
                <th>Solar Condition</th>
                <td>{{ $inspection->solar }}</td>
            </tr>
            <tr>
                <th>Environment Condition</th>
                <td>{{ $inspection->environment }}</td>
            </tr>

            <tr>
                <th>Photo</th>
                <td style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @if ($inspection->photo_path)
                        @php
                            $photos = json_decode($inspection->photo_path, true);
                        @endphp
                        @if (is_array($photos))
                            @foreach ($photos as $photoUrl)
                                <img src="{{ $photoUrl }}" width="200" />
                            @endforeach
                        @else
                            <a href="{{ asset('storage/' . $inspection->photo_path) }}" data-lightbox="inspection-gallery">
                                <img src="{{ asset('storage/' . $inspection->photo_path) }}" alt="Photo"
                                    class="img-thumbnail" style="width: 100px; height: auto;">
                            </a>
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Video</th>
                <td>
                    @if ($inspection->video_path)
                        <video controls style="max-width: 400px;">
                            <source src="{{ $inspection->video_path }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <p>No Video Uploaded</p>
                    @endif
                </td>
            </tr>
        </table>
    </div>
@endsection

{{-- Lightbox2 Assets Only for This Page --}}
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
@endpush
