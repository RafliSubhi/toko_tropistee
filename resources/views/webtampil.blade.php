@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section id="beranda" class="py-5 text-center bg-light">
        <div class="container">
            @if($logo && $logo->logo_path && Storage::disk('public')->exists($logo->logo_path))
                <img src="{{ asset('storage/' . $logo->logo_path) }}" alt="Logo Toko" class="mb-4 rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
            @endif
            <h1 class="display-4 fw-bold">{{ $settings['welcome_greeting'] ?? 'Selamat Datang di TropisTee' }}</h1>
            <p class="lead text-muted">{{ $settings['slogan'] ?? 'Slogan toko Anda akan tampil di sini.' }}</p>
            <a href="{{ route('menu') }}" class="btn btn-primary btn-lg mt-3 shadow">Lihat Menu</a>
            <a href="{{ route('kontak') }}" class="btn btn-outline-secondary btn-lg mt-3">Hubungi Kami</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Tentang Kami</h2>
                <p class="text-muted">Kenali lebih jauh tentang kami.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center">
                            <h3 class="card-title h4 fw-bold">Deskripsi Usaha</h3>
                            <p class="card-text text-muted">{{ $settings['business_description'] ?? 'Deskripsi singkat mengenai usaha TropisTee.' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center">
                            <h3 class="card-title h4 fw-bold">Visi</h3>
                            <p class="card-text text-muted">{{ $settings['vision'] ?? 'Visi perusahaan Anda akan tampil di sini.' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center">
                            <h3 class="card-title h4 fw-bold">Misi</h3>
                            <p class="card-text text-muted">{{ $settings['mission'] ?? 'Misi perusahaan Anda akan tampil di sini.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="tim" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Tim Kami</h2>
                <p class="text-muted">Orang-orang di balik layar.</p>
            </div>
            <div class="row g-4">
                @forelse ($teamMembers as $member)
                    <div class="col-lg-3 col-md-6">
                        <div class="card team-card h-100 shadow-sm border-0 text-center" data-bs-toggle="modal" data-bs-target="#teamMemberModal{{ $member->id }}">
                            <img src="{{ asset('storage/' . $member->image_path) }}" class="card-img-top" alt="Foto {{ $member->name }}" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{ $member->name }}</h5>
                                <p class="card-text text-primary">{{ $member->position }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col">
                        <p class="text-center text-muted">Data tim belum tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Team Member Modals -->
    @foreach ($teamMembers as $member)
    <div class="modal fade" id="teamMemberModal{{ $member->id }}" tabindex="-1" aria-labelledby="teamMemberModalLabel{{ $member->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="teamMemberModalLabel{{ $member->id }}">Detail Anggota Tim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . $member->image_path) }}" class="rounded-circle shadow-sm mb-3" alt="Foto {{ $member->name }}" style="width: 150px; height: 150px; object-fit: cover;">
                        <h3 class="fw-bold">{{ $member->name }}</h3>
                        <p class="text-primary fs-5">{{ $member->position }}</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex">
                            <i class="bi bi-envelope-fill me-3 fs-5"></i>
                            <span>{{ $member->email ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex">
                            <i class="bi bi-geo-alt-fill me-3 fs-5"></i>
                            <span>{{ $member->alamat ?? '-' }}</span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endsection

@push('styles')
<style>
    .team-card {
        transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
    }
    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        cursor: pointer;
    }
</style>
@endpush