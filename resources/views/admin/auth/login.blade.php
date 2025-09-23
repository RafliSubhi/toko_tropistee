<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ $settings['store_name'] ?? 'Toko TropisTee' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #e9ecef;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 1rem;
        }
    </style>
</head>
<body>
    <div class="card login-card shadow-lg">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                @if($logo && $logo->logo_path && Illuminate\Support\Facades\Storage::disk('public')->exists($logo->logo_path))
                    <img src="{{ asset('storage/' . $logo->logo_path) }}" alt="Logo Toko" class="mb-3 rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                @endif
                <h3 class="card-title fw-bold">Admin Panel Login</h3>
                <p class="text-muted">Silakan login untuk melanjutkan</p>
            </div>
            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger small" role="alert">
                        Email atau password yang Anda masukkan salah.
                    </div>
                @endif

                <div class="form-floating mb-3">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
                    <label for="email">{{ __('Email Address') }}</label>
                </div>

                <div class="form-floating mb-3">
                    <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" placeholder="Password">
                    <label for="password">{{ __('Password') }}</label>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>