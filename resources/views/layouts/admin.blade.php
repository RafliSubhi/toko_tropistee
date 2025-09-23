<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - {{ $settings['store_name'] ?? 'Toko TropisTee' }}</title>
    @if(isset($logo) && $logo->favicon_path && Illuminate\Support\Facades\Storage::disk('public')->exists($logo->favicon_path))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $logo->favicon_path) }}">
    @endif
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .main-container {
            display: flex;
            flex: 1;
            overflow: hidden; /* Prevent body scroll */
        }
        .sidebar {
            width: 280px;
            background-color: #343a40;
            color: white;
            flex-shrink: 0;
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }
        .sidebar .nav-pills {
            overflow-y: auto; /* Enable scrolling for the nav items */
            flex-grow: 1;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 10px 20px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        .sidebar .nav-link .bi {
            margin-right: 10px;
        }
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #495057;
            flex-shrink: 0;
        }
        .nav-item-header {
            color: white !important; /* Change header text color */
        }
        .content {
            flex-grow: 1;
            padding: 30px;
            background-color: #f8f9fa;
            overflow-y: auto; /* Allow content to scroll */
        }
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            max-height: 70vh; /* Set a max height for vertical scroll */
            overflow-y: auto;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }
        .sidebar-overlay.active {
            display: block;
        }

        /* Responsive styles for mobile */
        @media (max-width: 991.98px) {
            .main-container {
                overflow-x: hidden;
            }
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 1050;
                transform: translateX(-100%);
                height: 100vh; /* Ensure sidebar takes full height */
            }
            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column p-3">
            <div class="sidebar-header text-center">
                <a href="{{ route('admin.dashboard') }}" class="fs-4 text-white text-decoration-none">
                    Admin TropisTee
                </a>
            </div>
            <hr>
            @php
                $unreadNotifications = \App\Models\Notification::where('is_read', false)->latest()->take(5)->get();
                $unreadCount = $unreadNotifications->count();
                $pendingCancellationsCount = \App\Models\CancellationRequest::where('status', 'pending')->count();
            @endphp
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                @if(in_array(Auth::guard('admin')->user()->role, ['utama', 'pesanan', 'produksi', 'distribusi', 'finansial']))
                <hr>
                <li class="nav-item-header px-3 text-muted small" style="font-size: 0.8rem; text-transform: uppercase;">Manajemen Pesanan</li>
                <li class="nav-item">
                    <a href="{{ route('admin.cancellations.index') }}" class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.cancellations.*') ? 'active' : '' }}">
                        <span><i class="bi bi-x-circle"></i> Konfirmasi Pembatalan</span>
                        @if($pendingCancellationsCount > 0)
                            <span class="badge bg-danger rounded-pill">{{ $pendingCancellationsCount }}</span>
                        @endif
                    </a>
                </li>
                @endif

                @if(in_array(Auth::guard('admin')->user()->role, ['utama', 'pesanan']))
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-inbox-fill"></i> Pesanan Masuk
                    </a>
                </li>
                @endif

                @if(in_array(Auth::guard('admin')->user()->role, ['utama', 'produksi']))
                <li class="nav-item">
                    <a href="{{ route('admin.production.index') }}" class="nav-link {{ request()->routeIs('admin.production.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Produksi
                    </a>
                </li>
                @endif

                @if(in_array(Auth::guard('admin')->user()->role, ['utama', 'distribusi']))
                <li class="nav-item">
                    <a href="{{ route('admin.distribution.index') }}" class="nav-link {{ request()->routeIs('admin.distribution.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Distribusi
                    </a>
                </li>
                @endif

                @if(in_array(Auth::guard('admin')->user()->role, ['utama', 'finansial']))
                <li class="nav-item">
                    <a href="{{ route('admin.financial.index') }}" class="nav-link {{ request()->routeIs('admin.financial.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i> Finansial
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.saldo') }}" class="nav-link {{ request()->routeIs('admin.settings.saldo') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin"></i> Pengaturan Saldo
                    </a>
                </li>
                @endif

                @if(Auth::guard('admin')->user()->role == 'utama')
                <hr>
                <li class="nav-item-header px-3 text-muted small" style="font-size: 0.8rem; text-transform: uppercase;">Manajemen Konten</li>

                <li class="nav-item">
                    <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="bi bi-cup-straw"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.team-members.index') }}" class="nav-link {{ request()->routeIs('admin.team-members.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Team Members
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.pengunjung.index') }}" class="nav-link {{ request()->routeIs('admin.pengunjung.*') ? 'active' : '' }}">
                        <i class="bi bi-person-check"></i> Pengunjung
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.tampilan.index') }}" class="nav-link {{ request()->routeIs('admin.tampilan.*') ? 'active' : '' }}">
                        <i class="bi bi-palette-fill"></i> Tampilan
                    </a>
                </li>

                <hr>

                <li class="nav-item-header px-3 text-muted small" style="font-size: 0.8rem; text-transform: uppercase;">Pengaturan</li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-credit-card"></i> Pengaturan Pembayaran
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-rolodex"></i> Pengaturan Admin
                    </a>
                </li>
                @endif
                <hr>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </a>
                </li>
            </ul>
            <hr>
        </div>

        <!-- Page Content -->
        <div class="d-flex flex-column" style="flex-grow: 1;">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button class="btn btn-link d-md-none rounded-circle mr-3" id="sidebar-toggle">
                    <i class="bi bi-list"></i>
                </button>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminNotificationToggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                             <i class="bi bi-bell-fill position-relative">
                                @if($unreadCount > 0)
                                <span id="adminNotificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.75em;">
                                    {{ $unreadCount }}
                                </span>
                                @endif
                            </i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="adminNotificationToggle">
                            @forelse($unreadNotifications as $notification)
                                <li><a class="dropdown-item" href="{{ $notification->link ?? '#' }}">{{ $notification->message }}</a></li>
                            @empty
                                <li><span class="dropdown-item-text">Tidak ada notifikasi baru.</span></li>
                            @endforelse
                        </ul>
                    </li>
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::guard('admin')->user()->name ?? 'Admin' }} ({{ Auth::guard('admin')->user()->role }})</span>
                            <i class="bi bi-person-circle fs-4"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                    Sign out
                                </a>
                                <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            
            // Create and append overlay
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            overlay.addEventListener('click', function () {
                toggleSidebar();
            });

            // --- Notification script (unchanged) ---
            const notificationToggle = document.getElementById('adminNotificationToggle');
            if (notificationToggle) {
                let notificationsMarked = false;
                notificationToggle.addEventListener('click', function() {
                    const badge = document.getElementById('adminNotificationBadge');
                    if (badge && !notificationsMarked) {
                        notificationsMarked = true; // Prevent multiple calls
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        fetch('{{ route("admin.notifications.markAllAsRead") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                badge.style.display = 'none';
                            }
                        }).catch(error => {
                            console.error('Error marking notifications as read:', error);
                            notificationsMarked = false; // Allow retry on error
                        });
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
