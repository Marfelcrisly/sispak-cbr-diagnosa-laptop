@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Kelola Case Base</h3>
            <div class="page-subtitle">
                Kelola case berdasarkan kerusakan dan kategori hardware/software.
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="/admin/cases/create" class="btn btn-sm btn-primary">+ Tambah Case</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger py-2 mb-3">{{ session('error') }}</div>
    @endif

    <div class="soft-panel section-gap">
        <form method="GET" action="/admin/cases" class="d-flex flex-wrap gap-2 align-items-center">
            <input type="text"
                   name="q"
                   class="form-control"
                   placeholder="Cari case code / kerusakan / kategori..."
                   value="{{ $q ?? '' }}">
            <button class="btn btn-dark" type="submit">Cari</button>
            <a href="/admin/cases" class="btn btn-outline-secondary">Reset</a>
        </form>
    </div>

    <div class="card interactive-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">Daftar Case</span>
            <span class="small text-muted">
                Total data:
                <b>{{ method_exists($cases, 'total') ? $cases->total() : count($cases) }}</b>
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Case Code</th>
                            <th>Kerusakan</th>
                            <th style="width: 140px;">Kategori</th>
                            <th>Catatan</th>
                            <th class="text-end" style="width: 250px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cases as $c)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $c->case_code }}</span>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $c->damage_name }}</div>
                                    <div class="small text-muted">{{ $c->damage_code }}</div>
                                </td>

                                <td>
                                    @if(($c->damage_category ?? '') === 'hardware')
                                        <span class="badge bg-primary">Hardware</span>
                                    @else
                                        <span class="badge bg-success">Software</span>
                                    @endif
                                </td>

                                <td class="text-muted">{{ $c->note ?? '-' }}</td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="/cases/{{ $c->id }}" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                        <a href="/admin/cases/{{ $c->id }}/edit" class="btn btn-sm btn-outline-dark">Edit</a>

                                        <form method="POST"
                                              action="/admin/cases/{{ $c->id }}/delete"
                                              onsubmit="return confirm('Yakin hapus case ini?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada data case.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($cases, 'links'))
            <div class="card-footer d-flex justify-content-center">
                {{ $cases->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection