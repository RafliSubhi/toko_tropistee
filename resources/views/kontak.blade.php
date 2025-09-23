@extends('layouts.app')

@section('content')
<div class="container section">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="text-center mb-5">
                <h2>Hubungi Kami</h2>
                <p class="lead">Temukan kami dan jangan ragu untuk menghubungi melalui detail di bawah ini.</p>
            </div>

            <div class="card shadow-lg border-0">
                <!-- Google Maps Embed -->
                @if(!empty($settings['google_maps_url']))
                    <div class="map-responsive">
                        <iframe
                            src="{{ $settings['google_maps_url'] }}"
                            width="600" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                @endif

                <div class="card-body p-lg-5 p-4">
                    <h4 class="card-title text-center mb-4">Informasi Kontak</h4>
                    <div class="list-group list-group-flush">
                        <!-- Address -->
                        <div class="list-group-item d-flex align-items-start py-3 px-0">
                            <i class="bi bi-geo-alt-fill fs-4 text-primary me-4"></i>
                            <div>
                                <h6 class="mb-1">Alamat</h6>
                                <p class="mb-0 text-muted">{{ $settings['address'] ?? '[Alamat belum diatur]' }}</p>
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="list-group-item d-flex align-items-start py-3 px-0">
                            <i class="bi bi-envelope-fill fs-4 text-primary me-4"></i>
                            <div>
                                <h6 class="mb-1">Email</h6>
                                <a href="mailto:{{ $settings['email'] ?? '#' }}" class="text-decoration-none text-dark">{{ $settings['email'] ?? '[Email belum diatur]' }}</a>
                            </div>
                        </div>
                        <!-- Phone -->
                        <div class="list-group-item d-flex align-items-start py-3 px-0">
                            <i class="bi bi-telephone-fill fs-4 text-primary me-4"></i>
                            <div>
                                <h6 class="mb-1">Telepon / WhatsApp</h6>
                                <a href="https://wa.me/{{ str_replace([' ', '-', '+'], '', $settings['phone_number'] ?? '#') }}" target="_blank" class="text-decoration-none text-dark">{{ $settings['phone_number'] ?? '[Nomor belum diatur]' }}</a>
                            </div>
                        </div>
                        <!-- Social Media -->
                        @if(!empty($settings['social_link']))
                            <div class="list-group-item d-flex align-items-start py-3 px-0">
                                <i class="bi bi-people-fill fs-4 text-primary me-4"></i>
                                <div>
                                    <h6 class="mb-1">Media Sosial</h6>
                                    <a href="{{ $settings['social_link'] }}" target="_blank" class="text-decoration-none text-dark">Kunjungi Profil Kami</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .section {
        padding: 60px 0;
    }
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .map-responsive {
        overflow: hidden;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
        position: relative;
        height: 0;
    }
    .map-responsive iframe {
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        position: absolute;
    }
</style>
@endpush