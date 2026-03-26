@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Dashboard Teknisi</h3>
            <div class="page-subtitle">
                Fokus pada antrian retain, hasil review, dan aktivitas diagnosa terbaru yang perlu ditangani.
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="/retain" class="btn btn-sm btn-primary">Buka Antrian</a>
        </div>
    </div>

    {{-- CARD STATISTIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Pending Retain</div>
                    <div class="stat-value text-primary">{{ $stats['pending'] }}</div>
                    <div class="stat-meta mt-2">
                        <a href="/retain" class="muted-link">Buka antrian retain</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Approved</div>
                    <div class="stat-value">{{ $stats['approved'] }}</div>
                    <div class="stat-meta mt-2">
                        Case retain yang disetujui menjadi knowledge baru.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Rejected</div>
                    <div class="stat-value">{{ $stats['rejected'] }}</div>
                    <div class="stat-meta mt-2">
                        Case retain yang ditolak setelah review teknisi.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Total Diagnosa</div>
                    <div class="stat-value">{{ $stats['total_diagnosa'] }}</div>
                    <div class="stat-meta mt-2">
                        Ringkasan total diagnosa yang tercatat di sistem.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PENDING TERBARU --}}
    <div class="card interactive-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">Pending Terbaru</span>
            <span class="small text-muted">Daftar kasus yang menunggu tindakan teknisi</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Best Case</th>
                            <th>Kerusakan</th>
                            <th>Similarity</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingLatest as $p)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $p->user_name ?? '-' }}</td>
                                <td>{{ $p->case_code ?? '-' }}</td>
                                <td>{{ $p->damage_name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ number_format((float)$p->best_similarity, 2) }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $p->status }}</span>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-dark" href="/retain/{{ $p->id }}">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    Belum ada pending.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Prioritaskan review pada kasus dengan similarity rendah dan status masih pending.
            </div>
            <div>
                <a href="/retain" class="btn btn-sm btn-outline-primary">Lihat Semua Retain</a>
            </div>
        </div>
    </div>

</div>
@endsection