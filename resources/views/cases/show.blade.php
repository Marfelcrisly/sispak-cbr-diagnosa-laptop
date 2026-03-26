@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Detail Case</h3>
            <div class="page-subtitle">
                Informasi lengkap case base dan gejala yang terhubung.
            </div>
        </div>

        <a href="/cases" class="btn btn-sm btn-outline-secondary">← Kembali</a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header fw-bold">Informasi Case</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="stat-label">Case Code</div>
                            <div class="fw-semibold">{{ $case->case_code ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Kode Kerusakan</div>
                            <div class="fw-semibold">{{ $case->damage_code ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Kerusakan</div>
                            <div class="fw-semibold">{{ $case->damage_name ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Kategori</div>
                            <div>
                                @if(($case->damage_category ?? '') === 'hardware')
                                    <span class="badge bg-primary">Hardware</span>
                                @else
                                    <span class="badge bg-success">Software</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="stat-label">Catatan</div>
                        <div class="soft-panel">{{ $case->note ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="stat-label">Solusi</div>
                        <div class="soft-panel">{{ $case->solution ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header fw-bold">Gejala Terkait</div>
                <div class="card-body">
                    @if($symptoms->count())
                        <div class="d-grid gap-2">
                            @foreach($symptoms as $s)
                                <div class="soft-panel py-2 px-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-secondary">{{ $s->code }}</span>
                                            <span class="ms-1">{{ $s->name }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(($s->category ?? '') === 'hardware')
                                                <span class="badge bg-primary">Hardware</span>
                                            @else
                                                <span class="badge bg-success">Software</span>
                                            @endif
                                            <span class="badge bg-dark">Bobot: {{ $s->weight }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">Belum ada gejala terkait.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection