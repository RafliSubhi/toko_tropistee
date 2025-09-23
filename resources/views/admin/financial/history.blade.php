@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Riwayat Transaksi</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Export and Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h6 class="m-0 fw-bold text-primary">Filter & Export</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('admin.financial.history') }}" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari berdasarkan email pelanggan..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="d-flex">
                        <input type="date" id="export_date_input" name="export_date" class="form-control me-2">
                        <button type="button" id="export_button" class="btn btn-success w-100" disabled><i class="bi bi-file-earmark-excel-fill me-2"></i>Export Excel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Riwayat Transaksi (Done)</h6>
            
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal Pesan</th>
                            <th>Pelanggan</th>
                            <th>Total Harga</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($doneOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $order->name ?? 'N/A' }}</td>
                                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ $order->indonesian_status }}</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#showOrderModal{{ $order->id }}"><i class="bi bi-eye-fill"></i></button>
                                    <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editOrderModal{{ $order->id }}"><i class="bi bi-pencil-square"></i></button>
                                    <form action="{{ route('admin.financial.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Ini tidak bisa dikembalikan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="mb-0 text-muted">Tidak ada riwayat transaksi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($doneOrders->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $doneOrders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@foreach ($doneOrders as $order)
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
                        <p class="mb-1"><strong>Status Pembayaran:</strong> <span class="badge bg-success">Selesai</span></p>
                        <p class="mb-0"><strong>Status Pesanan:</strong> <span class="badge bg-success">{{ $order->indonesian_status }}</span></p>
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

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="editOrderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOrderModalLabel{{ $order->id }}">Edit Total Saldo Pesanan #{{ $order->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.financial.updateTotalPrice', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="total_price_{{ $order->id }}" class="form-label">Total Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="total_price" id="total_price_{{ $order->id }}" class="form-control" value="{{ $order->total_price }}" required>
                        </div>
                    </div>
                    <small class="text-muted">Perubahan ini hanya akan mengubah angka di riwayat, tidak akan memengaruhi total saldo keseluruhan yang sudah tercatat.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('export_date_input');
        const exportButton = document.getElementById('export_button');

        dateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            exportButton.disabled = true; // Disable button whenever date changes

            if (!selectedDate) {
                return;
            }

            // Check server if orders exist for the selected date
            fetch(`{{ route('admin.financial.index') }}/check-done-orders?date=${selectedDate}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        exportButton.disabled = false; // Enable if orders exist
                    } else {
                        alert('Tidak ada pesanan di tanggal tersebut.');
                    }
                })
                .catch(error => {
                    console.error('Error checking orders:', error);
                    alert('Terjadi kesalahan saat memeriksa data pesanan.');
                });
        });

        exportButton.addEventListener('click', function() {
            const selectedDate = dateInput.value;
            if (!selectedDate || this.disabled) {
                return;
            }
            
            // Proceed with export
            const exportUrl = `{{ route('admin.financial.export-history') }}?export_date=${selectedDate}`;
            window.location.href = exportUrl;
        });
    });
</script>
@endpush
