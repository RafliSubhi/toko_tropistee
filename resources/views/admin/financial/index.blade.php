@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Finansial</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100" style="border-top: 4px solid #4e73df;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Saldo (Completed)</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-wallet2 fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-primary">Pesanan Selesai (Menunggu Konfirmasi "Done")</h6>
            <div>
                <a href="{{ route('admin.financial.history') }}" class="btn btn-info btn-sm">Lihat Riwayat Transaksi Done <i class="bi bi-arrow-right-circle ms-2"></i></a>
            </div>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <div class="mb-3">
                <form method="GET" action="{{ route('admin.financial.index') }}" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari berdasarkan email pelanggan..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal Selesai</th>
                            <th>Pelanggan</th>
                            <th>Total Harga</th>
                            <th class="text-center">Status Pembayaran</th>
                            <th class="text-center">Metode Pembayaran</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($completedOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->updated_at->format('d M Y, H:i') }}</td>
                                <td>{{ $order->name ?? 'N/A' }}</td>
                                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @php
                                        $paymentStatusClass = match($order->payment_status) {
                                            'paid' => 'bg-success',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $paymentStatusClass }}">{{ $order->indonesian_payment_status }}</span>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#showOrderModal{{ $order->id }}">
                                        <i class="bi bi-eye-fill"></i> Detail
                                    </button>
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning btn-sm me-2">
                                        <i class="bi bi-pencil-fill"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus pesanan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    </form>
                                     <form action="{{ route('admin.financial.done', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai transaksi ini sebagai DONE? Saldo akan bertambah.');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-circle-fill"></i> Done</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="mb-0 text-muted">Tidak ada transaksi yang perlu dikonfirmasi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($completedOrders->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $completedOrders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@foreach ($completedOrders as $order)
@php 
    $statusClass = 'bg-success'; // All are completed
    $paymentStatusClass = match($order->payment_status) {
        'paid' => 'bg-success',
        default => 'bg-secondary',
    };
@endphp
<!-- Show Order Modal -->
<div class="modal fade" id="showOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="showOrderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showOrderModalLabel{{ $order->id }}">Detail Pesanan #{{ $order->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <h6>Informasi Pelanggan</h6>
                        <p class="mb-1"><strong>Nama:</strong> {{ $order->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $order->email }}</p>
                        <p class="mb-1"><strong>No. Telepon:</strong> {{ $order->phone_number }}</p>
                        <p class="mb-0"><strong>Alamat:</strong> {{ $order->delivery_address }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Pesanan</h6>
                        <p class="mb-1"><strong>Metode Pembayaran:</strong> <span class="text-uppercase">{{ $order->payment_method }}</span></p>
                        <p class="mb-1"><strong>Status Pembayaran:</strong> <span class="badge {{ $paymentStatusClass }}">{{ $order->indonesian_payment_status }}</span></p>
                        <p class="mb-0"><strong>Status Pesanan:</strong> <span class="badge {{ $statusClass }}">{{ $order->indonesian_status }}</span></p>
                    </div>
                </div>
                <hr>
                <h6>Produk yang Dipesan</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-center">{{ $product->pivot->quantity }}</td>
                                    <td class="text-end">Rp {{ number_format($product->pivot->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($product->pivot->price * $product->pivot->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Subtotal Produk</td>
                                <td class="text-end fw-bold">Rp {{ number_format($order->total_price - $order->shipping_cost, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Ongkos Kirim</td>
                                <td class="text-end">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="3" class="text-end fw-bold">Total Keseluruhan</td>
                                <td class="text-end fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection