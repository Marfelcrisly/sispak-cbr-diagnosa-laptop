@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Data Gejala</h3>
            <div class="page-subtitle">
                Kelola daftar gejala beserta kategorinya.
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="/admin/symptoms/create" class="btn btn-sm btn-primary">+ Tambah Gejala</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger py-2 mb-3">{{ session('error') }}</div>
    @endif

    <div class="soft-panel section-gap">
        <form method="GET" action="/admin/symptoms" class="d-flex flex-wrap gap-2 align-items-center">
            <input
                type="text"
                name="q"
                class="form-control"
                placeholder="Cari kode / nama / kategori gejala..."
                value="{{ $q ?? '' }}"
            >
            <button class="btn btn-dark" type="submit">Cari</button>
            <a href="/admin/symptoms" class="btn btn-outline-secondary">Reset</a>
        </form>
    </div>

    <div class="card interactive-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">Daftar Gejala</span>
            <span class="small text-muted">
                Total data:
                <b>{{ method_exists($symptoms, 'total') ? $symptoms->total() : count($symptoms) }}</b>
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:120px;">Kode</th>
                            <th>Nama Gejala</th>
                            <th style="width:140px;">Kategori</th>
                            <th class="text-end" style="width:220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($symptoms as $s)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $s->code }}</span>
                                </td>

                                <td>{{ $s->name }}</td>

                                <td>
                                    @if(($s->category ?? '') === 'hardware')
                                        <span class="badge bg-primary">Hardware</span>
                                    @else
                                        <span class="badge bg-success">Software</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="/admin/symptoms/{{ $s->id }}/edit" class="btn btn-sm btn-outline-dark">
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="/admin/symptoms/{{ $s->id }}/delete"
                                              onsubmit="return confirm('Yakin hapus gejala ini?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada data gejala.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($symptoms, 'links'))
            <div class="card-footer d-flex justify-content-center">
                {{ $symptoms->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection