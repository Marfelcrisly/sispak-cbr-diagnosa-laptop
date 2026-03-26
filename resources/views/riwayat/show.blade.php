@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Detail Riwayat Diagnosa</h3>
            <div class="page-subtitle">
                Lihat hasil diagnosa + kategori hardware/software
            </div>
        </div>

        <a href="/riwayat" class="btn btn-sm btn-dark">Kembali</a>
    </div>

    {{-- INFO --}}
    <div class="card dashboard-card mb-3">
        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">
                    <div class="stat-label">Tanggal</div>
                    <div>{{ \Carbon\Carbon::parse($history->created_at)->format('d-m-Y H:i') }}</div>
                </div>

                <div class="col-md-4">
                    <div class="stat-label">User</div>
                    <div>{{ $history->user_name ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="stat-label">Hasil</div>
                    <div>{{ $history->case_code }} - {{ $history->damage_name }}</div>
                </div>

                {{-- 🔥 KATEGORI --}}
                <div class="col-md-4">
                    <div class="stat-label">Kategori</div>
                    @if($history->damage_category === 'hardware')
                        <span class="badge bg-primary">Hardware</span>
                    @else
                        <span class="badge bg-success">Software</span>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="stat-label">Similarity</div>
                    <span class="badge bg-success">
                        {{ number_format((float)$history->best_similarity, 2) }}%
                    </span>
                </div>

                <div class="col-md-4">
                    <div class="stat-label">Status</div>
                    @if($history->needs_review)
                        <span class="badge bg-warning text-dark">Perlu Review</span>
                    @else
                        <span class="badge bg-primary">Valid</span>
                    @endif
                </div>

            </div>

            <hr>

            <div>
                <div class="stat-label">Solusi</div>
                <div class="soft-panel mt-1">
                    {{ $history->damage_solution ?? '-' }}
                </div>
            </div>

        </div>
    </div>

    {{-- GEJALA --}}
    <div class="card dashboard-card mb-3">
        <div class="card-header fw-bold">Gejala</div>
        <div class="card-body">

            @forelse($selectedSymptoms as $sym)
                <div class="mb-2">
                    <span class="badge bg-secondary">{{ $sym->code }}</span>
                    {{ $sym->name }}

                    {{-- 🔥 kategori gejala --}}
                    @if($sym->category === 'hardware')
                        <span class="badge bg-primary ms-2">Hardware</span>
                    @else
                        <span class="badge bg-success ms-2">Software</span>
                    @endif
                </div>
            @empty
                <div class="text-muted">Tidak ada gejala</div>
            @endforelse

        </div>
    </div>

    {{-- TOP 3 --}}
    <div class="card interactive-card">
        <div class="card-header fw-bold">Top 3 Similarity</div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Case</th>
                        <th>Kategori</th>
                        <th>Similarity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topResults as $i => $r)
                        <tr>
                            <td>{{ $i + 1 }}</td>

                            <td>
                                {{ $r['detail']['case_code'] }} - {{ $r['detail']['name'] }}
                            </td>

                            <td>
                                @if(($r['detail']['category'] ?? '') === 'hardware')
                                    <span class="badge bg-primary">Hardware</span>
                                @else
                                    <span class="badge bg-success">Software</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-success">
                                    {{ number_format($r['similarity'], 2) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection