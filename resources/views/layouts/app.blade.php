<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings['store_name'] ?? 'Toko TropisTee' }}</title>
    @if($logo && $logo->favicon_path && Storage::disk('public')->exists($logo->favicon_path))
        <link rel="icon" type="image/png" href="{{ asset('storage/app/public/' . $logo->favicon_path) }}">
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
        .auth-bg {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            border-radius: 15px;
            overflow: hidden;
        }
        .auth-card .auth-image {
            background: url('https://images.unsplash.com/photo-1554224311-5e0f76390d95?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center;
            background-size: cover;
        }
        .btn-primary.text-white:hover {
            color: #fff !important;
        }
        .btn-primary.text-white {
            color: #000 !important;
        }
        .btn-outline-primary:hover {
            color: #fff !important;
        }
    </style>
    @stack('styles')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&v=weekly" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</head>
<body>

    @include('layouts.partials.navbar')

    @yield('content')

    @include('layouts.partials.footer')

    </body>
</html>
