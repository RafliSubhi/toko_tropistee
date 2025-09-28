@extends('layouts.app')

@section('content')
<div class="container section">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Pembayaran Pesanan #{{ $order->id }}</h4>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    <div class="text-center mb-4">
                        <h5>Total Pembayaran:</h5>
                        <h2 class="fw-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h2>
                    </div>

                    @if($qrCodePath)
                        <h5 class="text-center fw-bold">{{ strtoupper($order->payment_method) }}</h5>
                        <p class="text-center text-muted">Silakan scan QR code di bawah ini untuk menyelesaikan pembayaran dengan {{ strtoupper($order->payment_method) }}.</p>
                        <div class="text-center p-3 border rounded">
                            <img src="{{ asset('storage/' . $qrCodePath) }}" alt="QR Code {{ strtoupper($order->payment_method) }}" class="img-fluid rounded" style="max-height: 300px;">
                        </div>

                        <hr class="my-4">

                        <div class="mt-3 text-center">
                            @if($order->payment_status == 'unpaid')
                                <p class="fw-bold">Setelah melakukan pembayaran, isi nama pengguna dan klik tombol di bawah ini:</p>
                                <form action="{{ route('pengunjung.pesanan-saya.confirm-payment', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah melakukan pembayaran? Admin akan memverifikasi bukti transfer Anda.')">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="nama_pengguna" class="form-label">Nama Pengguna</label>
                                        <input type="text" class="form-control" id="nama_pengguna" name="nama_pengguna" required>
                                    </div>
                                    <button type="submit" id="confirm-payment-btn" class="btn btn-primary btn-lg" disabled>Saya Sudah Bayar</button>
                                </form>
                            @else
                                <div class="alert alert-info">
                                    <h5 class="alert-heading">Menunggu Konfirmasi</h5>
                                    <p class="mb-0">Terima kasih! Konfirmasi pembayaran Anda telah kami terima dan sedang menunggu verifikasi oleh admin.</p>
                                </div>
                            @endif
                        </div>

                    @else
                        <div class="alert alert-warning text-center">
                            <h5 class="alert-heading">Metode Pembayaran Belum Dikonfigurasi</h5>
                            <p>Saat ini metode pembayaran {{ strtoupper($order->payment_method) }} belum diatur oleh admin. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                        </div>
                    @endif

                    <div class="mt-4 text-center">
                        <a href="{{ route('pengunjung.pesanan-saya.show', $order) }}" class="btn btn-link">Kembali ke Detail Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaPenggunaInput = document.getElementById('nama_pengguna');
        const confirmPaymentBtn = document.getElementById('confirm-payment-btn');

        if (namaPenggunaInput && confirmPaymentBtn) {
            namaPenggunaInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    confirmPaymentBtn.disabled = false;
                } else {
                    confirmPaymentBtn.disabled = true;
                }
            });
        }
    });
</script>
@endpush