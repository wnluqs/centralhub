{{-- resources/views/departments/technical/local_report/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('technical-local_report') }}" class="btn btn-secondary mb-3">← Back to Local Report</a>
        <h2 class="text-primary mb-4 text-center fw-bold">Hantar Laporan Setempat</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Error:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('technical-local_report.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Branch & Zone --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="branch" class="form-label">Branch</label>
                    <select name="branch" id="branch" class="form-control" required>
                        <option value="">-- Pilih Branch --</option>
                        <option value="Machang">Machang</option>
                        <option value="Kuantan">Kuantan</option>
                        <option value="Kuala Terengganu">Kuala Terengganu</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="zone" class="form-label">Zon</label>
                    <select name="zone" id="zone" class="form-control select2" required disabled>
                        <option value="">-- Pilih Zon --</option>
                    </select>
                </div>
            </div>

            {{-- Road --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="road" class="form-label">Road</label>
                    <select name="road" id="road" class="form-control" required>
                        <option value="">-- Pilih Road --</option>
                        @foreach ($roads as $road)
                            <option value="{{ $road->name }}">{{ $road->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Public Complaints --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">Aduan Awam</div>
                <div class="card-body bg-white rounded shadow-sm">
                    @php
                        $publicOptions = [
                            'Garis Petak Parking Pudar',
                            'Halangan Dalam Petak',
                            'Isu tumbuhan',
                            'Peniaga/Penjaja Dalam Petak',
                            'Jalan tidak rata',
                            'Petak Parking Diturap',
                        ];
                    @endphp

                    <div class="row g-3">
                        {{-- Halangan Dalam Petak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Halangan Dalam Petak</label>
                            <select name="public_complaints[Halangan Dalam Petak][type]" class="form-control mb-2">
                                <option value="">-- Pilih Jenis Halangan --</option>
                                <option value="RORO Bin">RORO Bin</option>
                                <option value="Kren">Kren</option>
                                <option value="Tong Sampah">Trash Can</option>
                                <option value="Peraih/Peniaga Pasar">Peraih/Peniaga Pasar</option>
                                <option value="Kontraktor Binaan">Kontraktor Binaan</option>
                                <option value="Kanopi Sewaan">Kanopi Sewaan</option>
                                <option value="Pembuangan/Pengumpulan Sampah">Pembuangan/Pengumpulan Sampah</option>
                                <option value="Wujudnya Ramp Laluan Masuk ke Premis Oleh Pekedai">Wujudnya Ramp Laluan Masuk
                                    ke Premis Oleh Pekedai</option>
                                <option value="Pencuci Kertea">Car Wash</option>
                                <option value="Kereta Usang/Tersadai/Bengkel">Kereta Usang/Tersadai</option>
                            </select>
                            <input type="number" name="public_complaints[Halangan Dalam Petak][value]" class="form-control"
                                placeholder="Nilai (0–100)" min="0" max="100">
                        </div>

                        {{-- Isu Tambahan: Petak Parking Diturap --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Petak Parking Diturap (0–1000)</label>
                            <input type="hidden" name="public_complaints[Petak Parking Diturap]" value="0">
                            <input type="number" name="public_complaints[Petak Parking Diturap]" class="form-control"
                                min="0" max="1000">
                        </div>

                        {{-- Peniaga/Penjaja Dalam Petak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Peniaga/Penjaja Dalam Petak (0–100)</label>
                            <input type="hidden" name="public_complaints[Peniaga/Penjaja Dalam Petak]" value="0">
                            <input type="number" name="public_complaints[Peniaga/Penjaja Dalam Petak]" class="form-control"
                                min="0" max="100">
                        </div>

                        {{-- Jalan Tidak Rata/Tar Rosak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jalan Tidak Rata/Tar Rosak (0–500)</label>
                            <input type="hidden" name="public_complaints[Jalan Tidak Rata/Tar Rosak]" value="0">
                            <input type="number" name="public_complaints[Jalan Tidak Rata/Tar Rosak]" class="form-control"
                                min="0" max="500">
                        </div>

                        {{-- Garisan Petak Parking Pudar --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Garisan Petak Parking Pudar (0–500)</label>
                            <input type="hidden" name="public_complaints[Garisan Petak Parking Pudar]" value="0">
                            <input type="number" name="public_complaints[Garisan Petak Parking Pudar]" class="form-control"
                                min="0" max="500">
                        </div>

                        {{-- Dan lain-lain --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Dan lain-lain…</label>
                            <input type="text" name="public_others" class="form-control"
                                placeholder="Masukkan aduan lain-lain">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Operations Complaints --}}
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">Aduan Harta Operasi</div>
                <div class="card-body bg-white rounded shadow-sm">
                    <div class="row g-3">
                        {{-- Meter Rosak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Meter Rosak</label>
                            <input type="text" name="operations_complaints[Meter Rosak]" class="form-control"
                                placeholder="Butiran untuk Meter Rosak">
                        </div>

                        {{-- Tiang Halangan Rosak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tiang Halangan Rosak</label>
                            <input type="text" name="operations_complaints[Tiang Halangan Rosak]" class="form-control"
                                placeholder="Butiran untuk Tiang Halangan Rosak">
                        </div>

                        {{-- Papan Tanda P Sign Rosak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Papan Tanda P Sign Rosak (0–100)</label>
                            <input type="number" name="operations_complaints[Papan Tanda P Sign Rosak]"
                                class="form-control" placeholder="Nilai untuk Papan Tanda P Sign Rosak" min="0"
                                max="100">
                        </div>

                        {{-- Cat Island Pudar --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cat Island Pudar (0–100)</label>
                            <input type="number" name="operations_complaints[Cat Island Pudar]" class="form-control"
                                placeholder="Nilai untuk Cat Island Pudar" min="0" max="100">
                        </div>

                        {{-- Papan Tanda Arah Rosak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Papan Tanda Arah Rosak (0–100)</label>
                            <input type="number" name="operations_complaints[Papan Tanda Arah Rosak]"
                                class="form-control" placeholder="Nilai untuk Papan Tanda Arah Rosak" min="0"
                                max="100">
                        </div>

                        {{-- Papan Tanda Maklumat Rosak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Papan Tanda Maklumat Rosak (0–100)</label>
                            <input type="number" name="operations_complaints[Papan Tanda Maklumat Rosak]"
                                class="form-control" placeholder="Nilai untuk Papan Tanda Maklumat Rosak" min="0"
                                max="100">
                        </div>

                        {{-- Nombor Petak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombor Petak</label>
                            <input type="text" name="operations_parking_number" class="form-control"
                                placeholder="Masukkan nombor petak">
                        </div>

                        {{-- Bollard Rosak --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Bollard Rosak (Nyatakan Jumlah)</label>
                            <input type="text" name="operations_complaints[Bollard Rosak]" class="form-control"
                                placeholder="Contoh: 2 unit, 3 unit...">
                        </div>

                        {{-- Dan lain-lain --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Dan lain-lain…</label>
                            <input type="text" name="operations_others" class="form-control"
                                placeholder="Masukkan aduan lain-lain">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Media Upload + Location --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="photos" class="form-label">Muat Naik Foto</label>
                    <input type="file" name="photos[]" multiple class="form-control" accept="image/*">

                    {{-- Landmark --}}
                    <label for="landmark" class="form-label mt-3">Landmark</label>
                    <input type="text" name="landmark" id="landmark" class="form-control"
                        placeholder="Contoh: Landmark berhampiran...">
                </div>

                <div class="col-md-6">
                    <label for="videos" class="form-label">Muat Naik Video</label>
                    <input type="file" name="videos[]" multiple class="form-control" accept="video/*">

                    {{-- Latitude --}}
                    <label for="latitude" class="form-label mt-3">Latitude</label>
                    <input type="text" name="latitude" id="latitude" class="form-control"
                        placeholder="Contoh: 3.123456">

                    {{-- Longitude --}}
                    <label for="longitude" class="form-label mt-3">Longitude</label>
                    <input type="text" name="longitude" id="longitude" class="form-control"
                        placeholder="Contoh: 101.654321">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    {{-- Nama Juruteknik --}}
                    <label for="technician_name" class="form-label">Nama Juruteknik</label>
                    <input type="text" name="technician_name" class="form-control" value="{{ Auth::user()->name }}"
                        readonly>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100">Hantar Laporan Setempat</button>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .form-label,
        .form-control {
            font-family: Arial, sans-serif !important;
            font-size: 18px !important;
        }

        /* Ensure all label texts are black, especially outside cards */
        label.form-label {
            color: #000 !important;
        }

        .card-header.bg-info {
            background-color: #003366 !important;
            /* dark blue */
        }

        .card-header.bg-warning {
            background-color: #6c757d !important;
            /* grey */
            color: white !important;
            font-size: 18px !important;
        }

        .card-body {
            background-color: #fff !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Disable dropdowns initially
            $('#zone').prop('disabled', true);
            $('#road').prop('disabled', true);

            // When Branch changes
            $('#branch').on('change', function() {
                const branch = $(this).val();
                $('#zone').empty().append('<option value="">-- Pilih Zon --</option>').prop('disabled',
                    true);
                $('#road').empty().append('<option value="">-- Pilih Road --</option>').prop('disabled',
                    true);

                if (branch !== '') {
                    $.get('/zones/' + branch, function(zones) {
                        if (zones.length > 0) {
                            $('#zone').prop('disabled', false);
                            zones.forEach(function(z) {
                                $('#zone').append('<option value="' + z.id + '">' + z.name + '</option>');
                            });
                        } else {
                            $('#zone').append('<option value="">Tiada Zon Ditemui</option>');
                        }
                    }).fail(function() {
                        alert('Gagal memuat zon dari server.');
                    });
                }
            });

            // When Zone changes
            $('#zone').on('change', function() {
                const zone = $(this).val();
                $('#road').empty().append('<option value="">-- Pilih Road --</option>').prop('disabled',
                    true);

                if (zone !== '') {
                    $.get('/roads/' + zone, function(roads) {
                        if (roads.length > 0) {
                            $('#road').prop('disabled', false);
                            roads.forEach(function(r) {
                                $('#road').append('<option value="' + r + '">' + r +
                                    '</option>');
                            });
                        } else {
                            $('#road').append('<option value="">Tiada Jalan Ditemui</option>');
                        }
                    }).fail(function() {
                        alert('Gagal memuat jalan dari server.');
                    });
                }
            });
        });
    </script>
    <script>
        window.onload = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                    document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                }, function(error) {
                    console.warn("Geolocation not allowed or failed: ", error.message);
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        };
    </script>
@endpush
