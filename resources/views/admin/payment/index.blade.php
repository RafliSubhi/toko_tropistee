@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Konfirmasi Pembayaran</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal Pesan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td>{{ $order->payment_method }}</td>
                                <td><span class="badge bg-warning text-dark">{{ $order->payment_status }}</span></td>
                                <td class="d-flex gap-2">
                                    <form action="{{ route('admin.payment.update-status', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin menandai pembayaran ini LUNAS?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="paid">
                                        <button type="submit" class="btn btn-sm btn-success">Lunas</button>
                                    </form>
                                    <form action="{{ route('admin.payment.update-status', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin menandai pembayaran ini GAGAL?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="failed">
                                        <button type="submit" class="btn btn-sm btn-danger">Gagal</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada pembayaran yang perlu dikonfirmasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
