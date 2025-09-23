@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Pending Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100" style="border-top: 4px solid #f6c23e;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Pesanan Baru (Pending)</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $pendingOrders }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-inbox-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100" style="border-top: 4px solid #4e73df;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Semua Pesanan</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalOrders }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-receipt fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Visitors Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100" style="border-top: 4px solid #1cc88a;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Pengunjung Terdaftar</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalVisitors }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Admins Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100" style="border-top: 4px solid #36b9cc;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Total Admin</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalAdmins }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-rolodex fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Selamat Datang di Panel Admin</h6>
                </div>
                <div class="card-body">
                    <p>Ini adalah pusat kendali untuk toko online Anda. Dari sini, Anda dapat mengelola pesanan, produk, pelanggan, dan semua aspek lain dari toko Anda.</p>
                    <p>Gunakan menu di sebelah kiri untuk menavigasi ke berbagai bagian panel admin. Jika Anda menggunakan perangkat seluler, menu dapat diakses dengan mengetuk ikon di pojok kiri atas.</p>
                    <p class="mb-0">Jika Anda memiliki pertanyaan atau memerlukan bantuan, jangan ragu untuk merujuk ke dokumentasi atau menghubungi tim dukungan.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection