@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Pengaturan Gambar QRIS</h1>
    <p class="mb-4">Unggah atau perbarui gambar QR Code yang akan ditampilkan pada halaman pembayaran.</p>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Unggah QRIS</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="qris_image" class="form-label">Unggah Gambar QRIS</label>
                    <input type="file" class="form-control" id="qris_image" name="qris_image">
                    @error('qris_image')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan Gambar</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Preview QRIS Saat Ini</h6>
        </div>
        <div class="card-body">
            <div class="text-center">
                @if($qris_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($qris_image_path))
                    <img src="{{ asset('storage/app/public/' . $qris_image_path) }}" alt="QRIS Code" class="img-fluid img-thumbnail" style="max-height: 250px;">
                @else
                    <p class="text-muted">Gambar QRIS belum diatur.</p>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
