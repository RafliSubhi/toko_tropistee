@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Pengaturan Pembayaran</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">

                <!-- Payment Settings -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">Pengaturan Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Upload gambar QR Code untuk metode pembayaran yang Anda sediakan. Pelanggan akan melihat QR Code ini saat melakukan pembayaran.</p>
                        <hr>

                        <!-- QRIS Section -->
                        <h6 class="mb-3 fw-bold">QRIS</h6>
                        <div class="mb-3">
                            <label for="qris_image" class="form-label">Upload Gambar QRIS</label>
                            <input type="file" class="form-control" id="qris_image" name="qris_image">
                            @if(isset($settings['qris_image']) && $settings['qris_image'])
                                <div class="mt-2 border p-2 rounded d-inline-block">
                                    <img src="{{ asset('storage/' . $settings['qris_image']) }}" alt="QRIS Preview" style="max-height: 150px;">
                                </div>
                            @endif
                        </div>
                        <hr>

                        <!-- DANA Section -->
                        <h6 class="mb-3 fw-bold">DANA</h6>
                        <div class="mb-3">
                            <label for="dana_qr_code" class="form-label">Upload QR Code DANA</label>
                            <input type="file" class="form-control" id="dana_qr_code" name="dana_qr_code">
                            @if(isset($settings['dana_qr_code']) && $settings['dana_qr_code'])
                                <div class="mt-2 border p-2 rounded d-inline-block">
                                    <img src="{{ asset('storage/' . $settings['dana_qr_code']) }}" alt="DANA QR Preview" style="max-height: 150px;">
                                </div>
                            @endif
                        </div>
                        <hr>

                        <!-- GoPay Section -->
                        <h6 class="mb-3 fw-bold">GoPay</h6>
                        <div class="mb-3">
                            <label for="gopay_qr_code" class="form-label">Upload QR Code GoPay</label>
                            <input type="file" class="form-control" id="gopay_qr_code" name="gopay_qr_code">
                            @if(isset($settings['gopay_qr_code']) && $settings['gopay_qr_code'])
                                <div class="mt-2 border p-2 rounded d-inline-block">
                                    <img src="{{ asset('storage/' . $settings['gopay_qr_code']) }}" alt="GoPay QR Preview" style="max-height: 150px;">
                                </div>
                            @endif
                        </div>
                        <hr>

                        <!-- OVO Section -->
                        <h6 class="mb-3 fw-bold">OVO</h6>
                        <div class="mb-3">
                            <label for="ovo_qr_code" class="form-label">Upload QR Code OVO</label>
                            <input type="file" class="form-control" id="ovo_qr_code" name="ovo_qr_code">
                            @if(isset($settings['ovo_qr_code']) && $settings['ovo_qr_code'])
                                <div class="mt-2 border p-2 rounded d-inline-block">
                                    <img src="{{ asset('storage/' . $settings['ovo_qr_code']) }}" alt="OVO QR Preview" style="max-height: 150px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">Simpan Perubahan</h6>
                    </div>
                    <div class="card-body">
                        <p>Klik tombol di bawah untuk menyimpan semua perubahan yang telah Anda buat.</p>
                        <button type="submit" class="btn btn-primary w-100">Simpan Pengaturan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(event, previewId) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush