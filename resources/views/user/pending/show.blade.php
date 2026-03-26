@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-0">Detail Pending Saya</h3>
            <div class="text-muted small">ID Pending: #{{ $pending->id ?? '-' }}</div>
        </div>

        <div class="d-flex gap-2">
            <a href="/user/pending" class="btn btn-sm btn-outline-dark">← Kembali</a>
            @if(!empty($pending->history_id))
                <a href="/riwayat/{{ $pending->history_id }}" class="btn btn-sm btn-outline-secondary">Riwayat</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- STATUS --}}
    @php
        $status = $pending->status ?? 'pending';
        $badge = match($status) {
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-warning text-dark',
        };
    @endphp

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <div class="text-muted">Status</div>
                    <div class="fs-5 fw-bold">
                        <span class="badge {{ $badge }}">
                            {{ strtoupper($status) }}
                        </span>
                    </div>
                </div>

                <div>
                    <div class="text-muted">Tanggal</div>
                    <div class="fw-semibold">
                        {{ !empty($pending->created_at) ? \Carbon\Carbon::parse($pending->created_at)->format('d-m-Y H:i') : '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-muted">Best Similarity</div>
                    <div class="fw-semibold">
                        {{ number_format((float)($pending->best_similarity ?? 0), 2) }}%
                    </div>
                </div>
            </div>

            @if(!empty($pending->review_note))
                <hr>
                <div class="fw-semibold mb-1">Catatan Verifikator</div>
                <div class="text-muted">{{ $pending->review_note }}</div>
            @endif
        </div>
    </div>

    {{-- GEJALA DIPILIH --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">Gejala yang Dipilih</div>
        <div class="card-body">
            @if(isset($selectedSymptoms) && $selectedSymptoms->count())
                <ul class="mb-0">
                    @foreach($selectedSymptoms as $g)
                        <li><b>{{ $g->code }}</b> - {{ $g->name }}</li>
                    @endforeach
                </ul>
            @else
                <div class="text-muted">Tidak ada gejala.</div>
            @endif
        </div>
    </div>

    {{-- TOP RESULTS --}}
    <div class="card shadow-sm">
        <div class="card-header fw-bold">Hasil Similarity (Top 3)</div>
        <div class="card-body">

            @if(!empty($topResults) && is_array($topResults))
                @foreach($topResults as $i => $r)
                    @php
                        $rank = $i + 1;
                        $similarity = (float)($r['similarity'] ?? 0);
                        $detail = $r['detail'] ?? null;

                        // sebagian data top_results kamu bisa berupa array, bukan object
                        $caseCode = $detail['case_code'] ?? ($detail->case_code ?? '-');
                        $damageName = $detail['name'] ?? ($detail->name ?? '-');
                        $solution = $detail['solution'] ?? ($detail->solution ?? null);

                        $score = (float)($r['score'] ?? 0);
                        $total = (float)($r['total'] ?? 0);

                        $matchedDetails = $r['matchedDetails'] ?? [];
                        $isCollection = $matchedDetails instanceof \Illuminate\Support\Collection;
                    @endphp

                    <div class="border rounded mb-3 overflow-hidden">
                        <div class="px-3 py-2 fw-semibold {{ $rank === 1 ? 'bg-success text-white' : 'bg-dark text-white' }}">
                            Peringkat {{ $rank }}
                        </div>

                        <div class="p-3">
                            <div class="mb-2">
                                <div class="fw-semibold">Kode Case: {{ $caseCode }}</div>
                                <div><b>Kerusakan:</b> {{ $damageName }}</div>
                            </div>

                            <div class="mb-2">
                                <div class="progress" style="height: 18px;">
                                    <div class="progress-bar {{ $rank === 1 ? 'bg-success' : 'bg-secondary' }}"
                                         style="width: {{ $similarity }}%">
                                        {{ number_format($similarity, 2) }}%
                                    </div>
                                </div>
                                <div class="small text-muted mt-1">
                                    Nilai: {{ number_format($score, 0) }} / {{ number_format($total, 0) }}
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="fw-semibold">Gejala yang Cocok:</div>

                                @if($isCollection && $matchedDetails->count())
                                    <ul class="mb-0">
                                        @foreach($matchedDetails as $m)
                                            <li><b>{{ $m->code }}</b> - {{ $m->name }}</li>
                                        @endforeach
                                    </ul>
                                @elseif(is_array($matchedDetails) && count($matchedDetails))
                                    <ul class="mb-0">
                                        @foreach($matchedDetails as $m)
                                            @php
                                                $mCode = $m['code'] ?? ($m->code ?? '-');
                                                $mName = $m['name'] ?? ($m->name ?? '-');
                                            @endphp
                                            <li><b>{{ $mCode }}</b> - {{ $mName }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-muted">Tidak ada gejala yang cocok.</div>
                                @endif
                            </div>

                            @if(!empty($solution))
                                <hr>
                                <div class="fw-semibold mb-1">Solusi:</div>
                                <div class="text-muted">{{ $solution }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-muted">Top result belum tersedia.</div>
            @endif

        </div>
    </div>

</div>
@endsection