<div class="p-3 bg-white" style="width: 280px;">
    <a href="/" class="d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom">
        <span class="fs-5 fw-semibold">Toko TropisTee</span>
    </a>
    <ul class="list-unstyled ps-0">
        <li class="mb-1">
            <a href="{{ route('dashboard') }}" class="btn btn-toggle align-items-center rounded collapsed">
                Dashboard
            </a>
        </li>
        <li class="mb-1">
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#settings-collapse" aria-expanded="false">
                Pengaturan Web
            </button>
            <div class="collapse" id="settings-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="{{ route('admin.settings.index') }}" class="link-dark rounded">Pengaturan Umum</a></li>
                </ul>
            </div>
        </li>
        <li class="mb-1">
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#content-collapse" aria-expanded="false">
                Manajemen Konten
            </button>
            <div class="collapse" id="content-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="{{ route('admin.products.index') }}" class="link-dark rounded">Produk</a></li>
                    <li><a href="{{ route('admin.team-members.index') }}" class="link-dark rounded">Anggota Tim</a></li>
                </ul>
            </div>
        </li>
        <li class="border-top my-3"></li>
        <li class="mb-1">
            <a href="/" target="_blank" class="btn btn-toggle align-items-center rounded collapsed">
                Lihat Website
            </a>
        </li>
    </ul>
</div>
