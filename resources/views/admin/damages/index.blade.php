@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Data Kerusakan</h3>
            <div class="page-subtitle">
                Kelola daftar kerusakan laptop beserta kategori & solusi.
            </div>
        </div>

        <a href="/admin/damages/create" class="btn btn-sm btn-primary">
            + Tambah Kerusakan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- SEARCH --}}
    <div class="soft-panel section-gap">
        <form method="GET" action="/admin/damages" class="d-flex gap-2">
            <input type="text" name="q" class="form-control"
                   placeholder="Cari kode / nama / solusi..."
                   value="{{ $q ?? '' }}">
            <button class="btn btn-dark">Cari</button>
            <a href="/admin/damages" class="btn btn-outline-secondary">Reset</a>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="card interactive-card">
        <div class="card-header d-flex justify-content-between">
            <span class="fw-bold">Daftar Kerusakan</span>
            <span class="small text-muted">
                Total:
                <b>{{ method_exists($damages, 'total') ? $damages->total() : count($damages) }}</b>
            </span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Kategori</th> {{-- 🔥 --}}
                        <th>Nama</th>
                        <th>Solusi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($damages as $d)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $d->code }}</span></td>

                        <td>
                            @if($d->category === 'hardware')
                                <span class="badge bg-primary">Hardware</span>
                            @else
                                <span class="badge bg-success">Software</span>
                            @endif
                        </td>

                        <td>{{ $d->name }}</td>

                        <td class="text-muted">
                            {{ \Illuminate\Support\Str::limit($d->solution ?? '-', 80) }}
                        </td>

                        <td class="text-end">
                            <a href="/admin/damages/{{ $d->id }}/edit" class="btn btn-sm btn-outline-dark">Edit</a>

                            <form method="POST"
                                  action="/admin/damages/{{ $d->id }}/delete"
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin hapus?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada data.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($damages, 'links'))
        <div class="card-footer text-center">
            {{ $damages->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection