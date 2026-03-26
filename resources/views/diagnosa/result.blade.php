@extends('layouts.app')

@section('content')
<div class="container">

    @php
        $role = auth()->check() ? (auth()->user()->role ?? 'user') : 'guest';
        $isStaff = in_array($role, ['admin','teknisi']);

        $needsReview = (bool)($retainInfo['needs_review'] ?? false);
        $threshold   = (float)($retainInfo['threshold'] ?? 0);

        $pendingId = $retainInfo['pending_case_id'] ?? null;
        $historyId = $retainInfo['history_id'] ?? null;
        $categoryBadge = strtolower((string)($category ?? ''));
    @endphp

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Hasil Diagnosa</h3>
            <div class="page-subtitle">
                Berikut 3 case terdekat berdasarkan perhitungan similarity metode CBR.
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="/diagnosa?category={{ $categoryBadge }}" class="btn btn-sm btn-outline-secondary">Diagnosa Lagi</a>
            @if($historyId)
                <a href="/riwayat/{{ $historyId }}" class="btn btn-sm btn-dark">Lihat Riwayat</a>
            @endif
        </div>
    </div>

    @if(!empty($categoryBadge))
        <div class="soft-panel section-gap">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <div class="fw-semibold">Kategori Diagnosa</div>
                    <div class="text-muted">Perhitungan hanya memakai gejala, case, dan kerusakan pada kategori ini.</div>
                </div>

                @if($categoryBadge === 'hardware')
                    <span class="badge bg-primary">Hardware</span>
                @elseif($categoryBadge === 'software')
                    <span class="badge bg-success">Software</span>
                @endif
            </div>
        </div>
    @endif

    @if($needsReview)
        <div class="alert alert-warning border-0 shadow-sm">
            <div class="fw-semibold mb-1">
                Similarity di bawah threshold ({{ number_format($threshold, 2) }}%).
                Hasil ini masuk <b>Antrian Retain</b> untuk diverifikasi teknisi.
            </div>

            <div class="d-flex flex-wrap gap-2 mt-2">
                @if($isStaff)
                    @if($pendingId)
                        <a href="/retain/{{ $pendingId }}" class="btn btn-sm btn-dark">Lihat Pending Ini</a>
                    @endif
                    <a href="/retain" class="btn btn-sm btn-outline-dark">Lihat Semua Antrian</a>
                @else
                    @if($pendingId)
                        <a href="/user/pending/{{ $pendingId }}" class="btn btn-sm btn-dark">Lihat Antrian Saya</a>
                        <a href="/user/pending" class="btn btn-sm btn-outline-dark">Semua Pending Saya</a>
                    @elseif($historyId)
                        <a href="/riwayat/{{ $historyId }}" class="btn btn-sm btn-dark">Lihat Riwayat Diagnosa</a>
                    @endif
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-success border-0 shadow-sm">
            <div class="fw-semibold">
                Similarity memenuhi threshold ({{ number_format($threshold, 2) }}%).
                Hasil diagnosa dinyatakan <b>Valid</b>.
            </div>

            @if($historyId)
                <div class="mt-2">
                    <a href="/riwayat/{{ $historyId }}" class="btn btn-sm btn-outline-success">Lihat di Riwayat</a>
                </div>
            @endif
        </div>
    @endif

    <div class="card interactive-card mb-3">
        <div class="card-header fw-bold">Gejala yang Dipilih</div>
        <div class="card-body">
            @if(isset($selectedDetails) && count($selectedDetails))
                <div class="row g-2">
                    @foreach($selectedDetails as $g)
                        <div class="col-md-6">
                            <div class="soft-panel py-2 px-3 h-100">
                                <span class="badge bg-secondary">{{ $g->code }}</span>
                                <span class="ms-1">{{ $g->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted">Tidak ada gejala dipilih.</div>
            @endif
        </div>
    </div>

    @if(isset($topResults) && count($topResults))
        @foreach($topResults as $i => $r)
            @php
                $rank = $i + 1;
                $detail = $r['detail'] ?? [];
                $similarity = (float)($r['similarity'] ?? 0);
                $matchWeight = (float)($r['matchWeight'] ?? 0);
                $totalWeight = (float)($r['totalWeight'] ?? 0);
                $matchedDetails = $r['matchedDetails'] ?? [];
                $resultCategory = strtolower((string)($detail['category'] ?? ''));
            @endphp

            @if(!empty($categoryBadge) && $resultCategory !== '' && $resultCategory !== $categoryBadge)
                @continue
            @endif

            <div class="card interactive-card mb-3">
                <div class="card-header fw-bold {{ $rank === 1 ? 'bg-success text-white' : 'bg-dark text-white' }}">
                    Peringkat {{ $rank }}
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <div class="stat-label">Kode Case</div>
                                <div class="fw-semibold">{{ $detail['case_code'] ?? '-' }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="stat-label">Kerusakan</div>
                                <div class="fw-semibold">{{ $detail['name'] ?? '-' }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="stat-label">Kategori</div>
                                @if($resultCategory === 'hardware')
                                    <span class="badge bg-primary">Hardware</span>
                                @elseif($resultCategory === 'software')
                                    <span class="badge bg-success">Software</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <div class="stat-label d-flex justify-content-between">
                                    <span>Similarity</span>
                                    <strong>{{ number_format($similarity, 2) }}%</strong>
                                </div>
                                <div class="progress" style="height: 18px;">
                                    <div class="progress-bar {{ $rank === 1 ? 'bg-success' : 'bg-secondary' }}"
                                         style="width: {{ $similarity }}%">
                                        {{ number_format($similarity, 2) }}%
                                    </div>
                                </div>
                                <div class="small text-muted mt-1">
                                    Nilai: {{ number_format($matchWeight, 2) }} / {{ number_format($totalWeight, 2) }}
                                </div>
                            </div>

                            @if(!empty($detail['solution']))
                                <div>
                                    <div class="stat-label mb-2">Solusi</div>
                                    <div class="soft-panel">
                                        {{ $detail['solution'] }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-5">
                            <div class="stat-label mb-2">Gejala yang Cocok</div>

                            @if(!empty($matchedDetails))
                                <div class="d-grid gap-2">
                                    @foreach($matchedDetails as $m)
                                        <div class="soft-panel py-2 px-3">
                                            <span class="badge bg-secondary">{{ $m['code'] ?? '-' }}</span>
                                            <span class="ms-1">{{ $m['name'] ?? '-' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-muted">Tidak ada gejala yang cocok.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-secondary">Tidak ada hasil.</div>
    @endif

</div>
@endsection