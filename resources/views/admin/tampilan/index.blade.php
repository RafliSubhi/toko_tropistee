@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Pengaturan Tampilan</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.tampilan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="tampilan-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="logo-tab" data-bs-toggle="tab" data-bs-target="#logo-section" type="button" role="tab" aria-controls="logo-section" aria-selected="true">Logo &amp; Favicon</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="beranda-tab" data-bs-toggle="tab" data-bs-target="#beranda-section" type="button" role="tab" aria-controls="beranda-section" aria-selected="false">Beranda</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="kontak-tab" data-bs-toggle="tab" data-bs-target="#kontak-section" type="button" role="tab" aria-controls="kontak-section" aria-selected="false">Kontak</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="tampilan-tabs-content">
                    <!-- Logo & Favicon Tab -->
                    <div class="tab-pane fade show active" id="logo-section" role="tabpanel" aria-labelledby="logo-tab">
                        <h5 class="mb-3">Pengaturan Logo & Favicon</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logo" class="form-label">Upload Logo</label>
                                    <input type="file" name="logo" id="logo" class="form-control" onchange="previewImage(event, 'logo-preview')">
                                    <small class="form-text text-muted">Rekomendasi ukuran: 200x50 pixel. Format: PNG, JPG, SVG.</small>
                                    <img id="logo-preview" src="{{ $logo && $logo->logo_path ? asset('storage/app/public/' . $logo->logo_path) : '#' }}" alt="Logo Preview" class="img-thumbnail mt-2" style="max-height: 50px; {{ !($logo && $logo->logo_path) ? 'display:none;' : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="favicon" class="form-label">Upload Favicon</label>
                                    <input type="file" name="favicon" id="favicon" class="form-control" onchange="previewImage(event, 'favicon-preview')">
                                    <small class="form-text text-muted">Ukuran: 32x32 atau 16x16 pixel. Format: PNG, ICO.</small>
                                    <img id="favicon-preview" src="{{ $logo && $logo->favicon_path ? asset('storage/app/public/' . $logo->favicon_path) : '#' }}" alt="Favicon Preview" class="img-thumbnail mt-2" style="max-height: 32px; {{ !($logo && $logo->favicon_path) ? 'display:none;' : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Beranda Tab -->
                    <div class="tab-pane fade" id="beranda-section" role="tabpanel" aria-labelledby="beranda-tab">
                        <h5 class="mb-3">Pengaturan Halaman Beranda</h5>
                        <div class="mb-3">
                            <label for="store_name" class="form-label">Nama Toko</label>
                            <input type="text" name="store_name" id="store_name" class="form-control" value="{{ $settings['store_name'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="welcome_greeting" class="form-label">Salam Selamat Datang</label>
                            <input type="text" name="welcome_greeting" id="welcome_greeting" class="form-control" value="{{ $settings['welcome_greeting'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="slogan" class="form-label">Slogan</label>
                            <input type="text" name="slogan" id="slogan" class="form-control" value="{{ $settings['slogan'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="business_description" class="form-label">Deskripsi Usaha</label>
                            <textarea name="business_description" id="business_description" class="form-control" rows="3">{{ $settings['business_description'] ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="vision" class="form-label">Visi</label>
                            <textarea name="vision" id="vision" class="form-control" rows="3">{{ $settings['vision'] ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="mission" class="form-label">Misi</label>
                            <textarea name="mission" id="mission" class="form-control" rows="3">{{ $settings['mission'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- Kontak Tab -->
                    <div class="tab-pane fade" id="kontak-section" role="tabpanel" aria-labelledby="kontak-tab">
                        <h5 class="mb-3">Pengaturan Halaman Kontak</h5>
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <input type="text" name="address" id="address" class="form-control" value="{{ $settings['address'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ $settings['email'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Nomor Telepon (WhatsApp)</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ $settings['phone_number'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="social_link" class="form-label">Link Media Sosial</label>
                            <input type="text" name="social_link" id="social_link" class="form-control" value="{{ $settings['social_link'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="google_maps_url" class="form-label">URL Google Maps</label>
                            <input type="text" name="google_maps_url" id="google_maps_url" class="form-control" value="{{ $settings['google_maps_url'] ?? '' }}">
                            <small class="form-text text-muted">Salin URL lengkap dari Google Maps (contoh: https://www.google.com/maps/embed?...)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
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