@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-0">Detail Antrian Retain</h3>
            <div class="text-muted small">Khusus untuk melihat antrian milik Anda (read-only).</div>
        </div>

        <div class="d-flex gap-2">
            <a href="/riwayat" class="btn btn-sm btn-outline-secondary">← Kembali ke Riwayat</a>
            @if(!empty($pending->history_id))
                <a href="/riwayat/{{ $pending->history_id }}" class="btn btn-sm btn-outline-dark">Lihat Riwayat Ini</a>
            @endif
        </div>
    </div>

    {{-- STATUS --}}
    @php
        $badge = 'bg-secondary';
        if (($pending->status ?? '') === 'pending') $badge = 'bg-warning text-dark';
        if (($pending->status ?? '') === 'approved') $badge = 'bg-success';
        if (($pending->status ?? '') === 'rejected') $badge = 'bg-danger';
    @endphp

    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap justify-content-between gap-3">
            <div>
                <div class="text-muted small">Status</div>
                <div class="fs-5 fw-semibold">
                    <span class="badge {{ $badge }}">{{ $statusLabel ?? strtoupper($pending->status ?? '-') }}</span>
                </div>
            </div>

            <div>
                <div class="text-muted small">Tanggal dibuat</div>
                <div class="fw-semibold">
                    {{ \Carbon\Carbon::parse($pending->created_at)->format('d-m-Y H:i') }}
                </div>
            </div>

            <div>
                <div class="text-muted small">Best Case</div>
                <div class="fw-semibold">{{ $pending->case_code ?? '-' }}</div>
                <div class="text-muted small">{{ $pending->damage_name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-muted small">Similarity</div>
                <div class="fw-semibold">{{ number_format((float)($pending->best_similarity ?? 0), 2) }}%</div>
            </div>
        </div>
    </div>

    {{-- CATATAN REVIEW TEKNISI (kalau ada) --}}
    @if(!empty($pending->review_note) || !empty($pending->reviewed_at))
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Catatan Review</div>
            <div class="card-body">
                @if(!empty($pending->reviewed_at))
                    <div class="text-muted small mb-2">
                        Direview pada: {{ \Carbon\Carbon::parse($pending->reviewed_at)->format('d-m-Y H:i') }}
                    </div>
                @endif
                <div>{{ $pending->review_note ?? '-' }}</div>
            </div>
        </div>
    @endif

    {{-- GEJALA YANG DIPILIH --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header fw-semibold">Gejala yang Dipilih</div>
        <div class="card-body">
            @if(isset($selectedSymptoms) && $selectedSymptoms->count())
                <ul class="mb-0">
                    @foreach($selectedSymptoms as $s)
                        <li><b>{{ $s->code }}</b> - {{ $s->name }}</li>
                    @endforeach
                </ul>
            @else
                <div class="text-muted">Tidak ada data gejala.</div>
            @endif
        </div>
    </div>

    {{-- TOP 3 HASIL (dari JSON top_results) --}}
    <div class="card shadow-sm">
        <div class="card-header fw-semibold">Hasil Diagnosa (Top 3) saat diajukan</div>
        <div class="card-body">

            @if(is_array($topResults) && count($topResults))
                @foreach($topResults as $i => $r)
                    @php
                        $rank = $i + 1;
                        $detail = $r['detail'] ?? null;

                        // beberapa project nyimpen detail sebagai array, jadi kita handle dua-duanya
                        $caseCode = is_array($detail) ? ($detail['case_code'] ?? '-') : ($detail->case_code ?? '-');
                        $damageName = is_array($detail) ? ($detail['name'] ?? '-') : ($detail->name ?? '-');
                        $solution = is_array($detail) ? ($detail['solution'] ?? null) : ($detail->solution ?? null);

                        $similarity = (float)($r['similarity'] ?? 0);
                        $score = (float)($r['score'] ?? 0);
                        $total = (float)($r['total'] ?? 0);

                        $matchedDetails = $r['matchedDetails'] ?? [];
                    @endphp

                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div class="fw-semibold">Peringkat {{ $rank }}</div>
                            <span class="badge bg-dark">{{ number_format($similarity, 2) }}%</span>
                        </div>

                        <div class="mb-2">
                            <div><b>Kode Case:</b> {{ $caseCode }}</div>
                            <div><b>Kerusakan:</b> {{ $damageName }}</div>
                            <div class="text-muted small">Nilai: {{ number_format($score, 0) }} / {{ number_format($total, 0) }}</div>
                        </div>

                        <div class="progress mb-2" style="height: 16px;">
                            <div class="progress-bar" style="width: {{ $similarity }}%"></div>
                        </div>

                        <div class="mt-3">
                            <div class="fw-semibold">Gejala yang Cocok:</div>

                            @if($matchedDetails instanceof \Illuminate\Support\Collection)
                                @if($matchedDetails->count())
                                    <ul class="mb-0">
                                        @foreach($matchedDetails as $m)
                                            <li><b>{{ $m->code }}</b> - {{ $m->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-muted">Tidak ada gejala cocok.</div>
                                @endif

                            @elseif(is_array($matchedDetails) && count($matchedDetails))
                                <ul class="mb-0">
                                    @foreach($matchedDetails as $m)
                                        @php
                                            $mCode = is_array($m) ? ($m['code'] ?? '-') : ($m->code ?? '-');
                                            $mName = is_array($m) ? ($m['name'] ?? '-') : ($m->name ?? '-');
                                        @endphp
                                        <li><b>{{ $mCode }}</b> - {{ $mName }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-muted">Tidak ada gejala cocok.</div>
                            @endif
                        </div>

                        @if(!empty($solution))
                            <hr>
                            <div class="fw-semibold mb-1">Solusi:</div>
                            <div class="text-muted">{{ $solution }}</div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-muted">Tidak ada data top results.</div>
            @endif

        </div>
    </div>

</div>
@endsection