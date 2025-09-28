@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Manajemen Anggota Tim</h1>
    <p class="mb-4">Kelola semua anggota tim yang akan ditampilkan di halaman utama.</p>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Daftar Anggota Tim</h6>
                <a href="{{ route('admin.team-members.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Anggota
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <div class="mb-4">
                <form action="{{ route('admin.team-members.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Cari nama anggota..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Posisi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teamMembers as $member)
                            <tr>
                                <td>{{ $loop->iteration + $teamMembers->firstItem() - 1 }}</td>
                                <td>
                                    @if($member->image_path)
                                        <img src="{{ asset('storage/' . $member->image_path) }}" alt="{{ $member->name }}" class="img-thumbnail" width="80">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->position }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.team-members.edit', $member->id) }}" class="btn btn-warning btn-sm me-2"><i class="bi bi-pencil-square"></i> Edit</a>
                                    <form action="{{ route('admin.team-members.destroy', $member->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="mb-0 text-muted">Tidak ada anggota tim yang ditemukan.</p>
                                    @if(request('search'))
                                        <small>Coba kata kunci lain atau <a href="{{ route('admin.team-members.index') }}">tampilkan semua anggota</a>.</small>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($teamMembers->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $teamMembers->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
