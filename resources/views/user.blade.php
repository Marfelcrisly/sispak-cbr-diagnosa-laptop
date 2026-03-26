@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Dashboard User</h3>
            <div class="page-subtitle">
                Ringkasan aktivitas diagnosa, status validasi, dan antrian retain milik kamu.
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="/diagnosa" class="btn btn-sm btn-primary">Mulai Diagnosa</a>
            <a href="/riwayat" class="btn btn-sm btn-outline-dark">Riwayat</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- CARD STATISTIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Total Diagnosa</div>
                    <div class="stat-value">{{ $stats['total_diagnosa'] ?? 0 }}</div>
                    <div class="stat-meta mt-2">
                        Jumlah seluruh diagnosa yang pernah kamu lakukan.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Valid</div>
                    <div class="stat-value text-success">{{ $stats['valid'] ?? 0 }}</div>
                    <div class="stat-meta mt-2">
                        Diagnosa yang memenuhi threshold sistem.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Perlu Review (Retain)</div>
                    <div class="stat-value text-warning">{{ $stats['perlu_review'] ?? 0 }}</div>

                    @if(isset($pendingCount))
                        <div class="stat-meta mt-2">
                            Pending saya: <b>{{ $pendingCount }}</b>
                            <a href="/user/pending" class="muted-link ms-2">Lihat</a>
                        </div>
                    @else
                        <div class="stat-meta mt-2">
                            <a href="/user/pending" class="muted-link">Lihat pending saya</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- DIAGNOSA TERBARU --}}
    <div class="card interactive-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">Diagnosa Terbaru</span>
            <span class="small text-muted">Riwayat hasil terbaru milik kamu</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:160px;">Tanggal</th>
                            <th style="width:140px;">Best Case</th>
                            <th>Kerusakan</th>
                            <th style="width:120px;">Similarity</th>
                            <th style="width:120px;">Threshold</th>
                            <th style="width:140px;">Status</th>
                            <th style="width:110px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($recent ?? []) as $r)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $r->case_code ?? '-' }}</td>
                                <td>{{ $r->damage_name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ number_format((float)$r->best_similarity, 2) }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ number_format((float)$r->threshold_used, 2) }}%
                                    </span>
                                </td>
                                <td>
                                    @if((int)($r->needs_review ?? 0) === 1)
                                        <span class="badge bg-warning text-dark">Perlu Review</span>
                                    @else
                                        <span class="badge bg-primary">Valid</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-dark" href="/riwayat/{{ $r->id }}">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada diagnosa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Tips: kalau status <b>Perlu Review</b>, cek menu <b>Pending Saya</b>.
            </div>
            <div class="d-flex gap-2">
                <a href="/user/pending" class="btn btn-sm btn-outline-dark">Pending Saya</a>
                <a href="/cases" class="btn btn-sm btn-outline-secondary">Data Case</a>
            </div>
        </div>
    </div>

</div>
@endsection