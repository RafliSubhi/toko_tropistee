<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            @if($logo && $logo->logo_path && Storage::disk('public')->exists($logo->logo_path))
                <img src="{{ asset('storage/app/public/' . $logo->logo_path) }}" alt="Logo" style="height: 30px; width: 30px; border-radius: 50%; object-fit: cover;" class="me-2">
            @endif
            <span class="fw-bold">{{ $settings['store_name'] ?? 'TropisTee' }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('menu') }}">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('kontak') }}">Kontak</a></li>
                @auth('pengunjung')
                    <li class="nav-item"><a class="nav-link" href="{{ route('pengunjung.cart.index') }}">Keranjang @if($cartCount > 0)<span class="badge bg-danger">{{ $cartCount }}</span>@endif</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('pengunjung.pesanan-saya.index') }}">Pesanan @if($activeOrderCount > 0)<span class="badge bg-primary">{{ $activeOrderCount }}</span>@endif</a></li>

                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown">
                        <a id="notificationDropdown" class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-mark-url="{{ route('pengunjung.notifications.mark-all-as-read') }}">
                            <i class="bi bi-bell"></i>
                            @if($unreadNotifications->isNotEmpty())
                            <span id="notification-count-badge" class="badge rounded-pill bg-danger" style="position: absolute; top: 10px; font-size: 0.6em; right: -5px;">
                                {{ $unreadNotifications->count() }}
                            </span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notificationDropdown" style="width: 350px;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Notifikasi</span>
                            </div>
                            <div id="notification-list" style="max-height: 400px; overflow-y: auto;">
                                @forelse($notifications as $notification)
                                    <div class="notification-item dropdown-item d-flex justify-content-between align-items-start px-3 py-2">
                                        <div class="flex-grow-1 pe-2">
                                            <a href="{{ $notification->link ?? '#' }}" class="text-decoration-none text-dark">
                                                <p class="mb-0 small">{!! $notification->message !!}</p>
                                            </a>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans(null, true) }} yang lalu</small>
                                        </div>
                                        <form action="{{ route('pengunjung.notifications.destroy', $notification) }}" method="POST" class="p-0 m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-close btn-sm" aria-label="Close"></button>
                                        </form>
                                    </div>
                                @empty
                                    <p id="no-notification-message" class="text-center text-muted small py-3 mb-0">Tidak ada notifikasi.</p>
                                @endforelse
                            </div>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::guard('pengunjung')->user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('pengunjung.profile.edit') }}">
                                Edit Profile
                            </a>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white me-2" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-outline-primary" href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

