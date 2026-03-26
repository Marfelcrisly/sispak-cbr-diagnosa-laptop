@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Detail Retain #{{ $pending->id }}</h3>
            <div class="page-subtitle">
                Tinjau hasil diagnosa yang belum memenuhi threshold dan tentukan apakah akan dijadikan knowledge baru.
            </div>
        </div>

        <div>
            <a href="/retain" class="btn btn-sm btn-dark">Kembali</a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header fw-bold">Informasi Diagnosa</div>
                <div class="card-body">

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="stat-label">Tanggal</div>
                            <div class="fw-semibold">
                                {{ \Carbon\Carbon::parse($pending->created_at)->format('d-m-Y H:i') }}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">User</div>
                            <div class="fw-semibold">{{ $pending->user_name ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Best Match</div>
                            <div class="fw-semibold">{{ $pending->case_code ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Kerusakan</div>
                            <div class="fw-semibold">{{ $pending->damage_name ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Kategori</div>
                            <div>
                                @if(($pending->damage_category ?? '') === 'hardware')
                                    <span class="badge bg-primary">Hardware</span>
                                @elseif(($pending->damage_category ?? '') === 'software')
                                    <span class="badge bg-success">Software</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Similarity</div>
                            <span class="badge bg-warning text-dark">{{ number_format((float)$pending->best_similarity, 2) }}%</span>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Status</div>
                            <span class="badge bg-secondary">{{ $pending->status }}</span>
                        </div>
                    </div>

                    @if(!empty($pending->damage_solution))
                        <hr>
                        <div class="stat-label">Solusi (Best Match)</div>
                        <div class="soft-panel mt-1">{{ $pending->damage_solution }}</div>
                    @endif

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header fw-bold">Gejala Dipilih</div>
                <div class="card-body">
                    @if(count($selectedSymptoms) === 0)
                        <div class="text-muted">Tidak ada gejala.</div>
                    @else
                        <div class="row g-2">
                            @foreach($selectedSymptoms as $s)
                                <div class="col-12">
                                    <div class="soft-panel py-2 px-3">
                                        <span class="badge bg-secondary">{{ $s->code }}</span>
                                        <span class="ms-1">{{ $s->name }}</span>
                                        <span class="ms-2">
                                            @if(($s->category ?? '') === 'hardware')
                                                <span class="badge bg-primary">Hardware</span>
                                            @else
                                                <span class="badge bg-success">Software</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card interactive-card mb-3">
        <div class="card-header fw-bold">Top 3 Case Terdekat</div>
        <div class="card-body p-0">

            @if(count($topResults) === 0)
                <div class="p-3 text-muted">Top results kosong.</div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:60px">#</th>
                                <th>Case</th>
                                <th style="width:120px">Kategori</th>
                                <th style="width:140px">Similarity</th>
                                <th style="width:140px">Score</th>
                                <th>Gejala Cocok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topResults as $i => $r)
                                @php
                                    $caseCode = $r['detail']['case_code'] ?? ('ID '.$r['case_id']);
                                    $caseName = $r['detail']['name'] ?? '-';
                                    $solution = $r['detail']['solution'] ?? null;
                                    $resultCategory = strtolower((string)($r['detail']['category'] ?? ''));
                                    $sim = (float)($r['similarity'] ?? 0);
                                    $score = number_format((float)($r['matchWeight'] ?? 0), 2) . '/' . number_format((float)($r['totalWeight'] ?? 0), 2);
                                    $matchedDetails = $r['matchedDetails'] ?? [];
                                @endphp
                                <tr>
                                    <td><strong>#{{ $i + 1 }}</strong></td>

                                    <td>
                                        <div><strong>{{ $caseCode }}</strong> - {{ $caseName }}</div>
                                        @if($solution)
                                            <div class="small text-muted">{{ $solution }}</div>
                                        @endif
                                    </td>

                                    <td>
                                        @if($resultCategory === 'hardware')
                                            <span class="badge bg-primary">Hardware</span>
                                        @elseif($resultCategory === 'software')
                                            <span class="badge bg-success">Software</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-success">{{ number_format($sim, 2) }}%</span>
                                    </td>

                                    <td>{{ $score }}</td>

                                    <td>
                                        @if(count($matchedDetails) === 0)
                                            <span class="text-muted">-</span>
                                        @else
                                            <ul class="mb-0">
                                                @foreach($matchedDetails as $md)
                                                    <li>
                                                        <strong>{{ $md['code'] ?? '-' }}</strong> - {{ $md['name'] ?? '-' }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>

    @if($pending->status === 'pending')
        <div class="card interactive-card mb-3">
            <div class="card-header fw-bold">Aksi Teknisi</div>
            <div class="card-body">

                <form method="POST" action="/retain/{{ $pending->id }}/approve" class="mb-3">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Pilih Kerusakan (Case Baru)</label>
                        <select name="damage_id" class="form-select" required>
                            <option value="">-- pilih --</option>
                            @foreach($damages as $d)
                                <option value="{{ $d->id }}">
                                    {{ $d->code }} - {{ $d->name }} ({{ ucfirst($d->category) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Jika disetujui, sistem akan membuat case baru berdasarkan gejala user.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Review (opsional)</label>
                        <textarea name="review_note" class="form-control" rows="3"></textarea>
                    </div>

                    <button class="btn btn-success">
                        Approve → Buat Case Baru
                    </button>
                </form>

                <hr>

                <form method="POST" action="/retain/{{ $pending->id }}/reject">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan (opsional)</label>
                        <textarea name="review_note" class="form-control" rows="3"></textarea>
                    </div>

                    <button class="btn btn-danger">Reject</button>
                </form>

            </div>
        </div>
    @endif

</div>
@endsection