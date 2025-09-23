<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings['store_name'] ?? 'Toko TropisTee' }}</title>
    @if($logo && $logo->favicon_path && Storage::disk('public')->exists($logo->favicon_path))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $logo->favicon_path) }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; }
        .section {
            padding: 60px 0;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 40px 0;
        }
    </style>
    @stack('styles')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&v=weekly" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    @include('layouts.partials.navbar')

    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo and Store Name -->
                <div class="col-md-3 text-center text-md-start mb-3 mb-md-0 d-flex align-items-center justify-content-center justify-content-md-start">
                    @if($logo && $logo->logo_path && Storage::disk('public')->exists($logo->logo_path))
                        <img src="{{ asset('storage/' . $logo->logo_path) }}" alt="Logo Footer" style="height: 50px; width: 50px; border-radius: 50%; object-fit: cover;">
                    @endif
                    <span class="ms-2 fw-bold">{{ $settings['store_name'] ?? 'TropisTee' }}</span>
                </div>

                <!-- Kontak & Social Media -->
                <div class="col-md-6 text-center text-md-start">
                    <h5>Kontak</h5>
                    <p class="mb-1">
                        <strong>Alamat:</strong>
                        <span class="text-white">{{ $settings['address'] ?? '[Alamat belum diatur]' }}</span>
                    </p>
                    <p class="mb-1">
                        <strong>Email:</strong>
                        <a href="mailto:{{ $settings['email'] ?? '' }}" class="text-white text-decoration-none">{{ $settings['email'] ?? '[email@anda.com]' }}</a>
                    </p>
                    <p class="mb-1">
                        <strong>Telepon:</strong>
                        <a href="https://wa.me/{{ $settings['phone_number'] ?? '' }}" target="_blank" class="text-white text-decoration-none">{{ $settings['phone_number'] ?? '[Nomor belum diatur]' }}</a>
                    </p>
                    @if(isset($settings['social_link']))
                        <p class="mb-0">
                            <strong>Media Sosial:</strong>
                            <a href="{{ $settings['social_link'] }}" target="_blank" class="text-white text-decoration-none">Kunjungi Kami</a>
                        </p>
                    @endif
                </div>

                <!-- Copyright -->
                <div class="col-md-3 text-center text-md-end mt-3 mt-md-0">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ $settings['store_name'] ?? 'TropisTee' }}.<br>All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    </body>
</html>
