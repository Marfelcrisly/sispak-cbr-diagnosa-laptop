@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Detail Evaluasi</h3>
            <div class="page-subtitle">
                Tinjau hasil diagnosa sistem, gejala yang dipilih, top similarity, dan simpan validasi pakar.
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="/riwayat/{{ $history->id }}" class="btn btn-sm btn-outline-secondary">Lihat di Riwayat</a>
            <a href="/evaluasi" class="btn btn-sm btn-dark">Kembali</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <div class="fw-bold mb-2">Validasi gagal:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
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
                                {{ \Carbon\Carbon::parse($history->created_at)->format('d-m-Y H:i') }}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">User</div>
                            <div class="fw-semibold">{{ $history->user_name ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Best Case</div>
                            <div class="fw-semibold">{{ $history->case_code ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Similarity</div>
                            <div>
                                <span class="badge bg-success">
                                    {{ number_format((float)($history->best_similarity ?? 0), 2) }}%
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Threshold Dipakai</div>
                            <div>
                                <span class="badge bg-secondary">
                                    {{ number_format((float)($history->threshold_used ?? 0), 2) }}%
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="stat-label">Status</div>
                            <div>
                                @if((int)($history->needs_review ?? 0) === 1)
                                    <span class="badge bg-warning text-dark">Perlu Review (Retain)</span>
                                @else
                                    <span class="badge bg-primary">Valid</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header fw-bold">Prediksi Sistem</div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="stat-label">Kerusakan</div>
                        <div class="fw-semibold">{{ $history->predicted_damage_name ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="stat-label">Kategori</div>
                        @if(($history->predicted_damage_category ?? '') === 'hardware')
                            <span class="badge bg-primary">Hardware</span>
                        @elseif(($history->predicted_damage_category ?? '') === 'software')
                            <span class="badge bg-success">Software</span>
                        @else
                            <span class="badge bg-secondary">-</span>
                        @endif
                    </div>

                    <div>
                        <div class="stat-label mb-2">Solusi (jika ada)</div>
                        <div class="soft-panel" style="white-space: pre-wrap;">
                            {{ $history->predicted_solution ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card interactive-card mb-3">
        <div class="card-header fw-bold">Gejala Dipilih</div>
        <div class="card-body">
            @if($selectedSymptoms->count() === 0)
                <div class="text-muted">Tidak ada gejala.</div>
            @else
                <div class="row g-2">
                    @foreach($selectedSymptoms as $s)
                        <div class="col-md-6">
                            <div class="soft-panel py-2 px-3 h-100">
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

    <div class="card interactive-card mb-3">
        <div class="card-header fw-bold">Top 3 Hasil Similarity</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 90px;">Rank</th>
                            <th>Case</th>
                            <th style="width:120px;">Kategori</th>
                            <th>Kerusakan</th>
                            <th style="width: 140px;">Similarity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topResults as $i => $r)
                            @php
                                $resultCategory = strtolower((string)($r['detail']['category'] ?? ''));
                            @endphp
                            <tr>
                                <td><span class="fw-semibold">#{{ $i + 1 }}</span></td>
                                <td>{{ $r['detail']['case_code'] ?? '-' }}</td>
                                <td>
                                    @if($resultCategory === 'hardware')
                                        <span class="badge bg-primary">Hardware</span>
                                    @elseif($resultCategory === 'software')
                                        <span class="badge bg-success">Software</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>{{ $r['detail']['name'] ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ number_format((float)($r['similarity'] ?? 0), 2) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card interactive-card">
        <div class="card-header fw-bold">Validasi Pakar</div>
        <div class="card-body">
            <form method="POST" action="/evaluasi/{{ $history->id }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Hasil Diagnosa Menurut Pakar</label>
                    <select name="expert_damage_id" class="form-select" required>
                        <option value="">- pilih kerusakan -</option>
                        @foreach($damages as $d)
                            <option value="{{ $d->id }}"
                                {{ (int)old('expert_damage_id', $history->expert_damage_id ?? 0) === (int)$d->id ? 'selected' : '' }}>
                                {{ $d->code }} - {{ $d->name }} ({{ ucfirst($d->category) }})
                            </option>
                        @endforeach
                    </select>

                    @if(!empty($history->expert_damage_id))
                        <div class="form-text">
                            Sudah divalidasi pada:
                            <b>{{ $history->validated_at ? \Carbon\Carbon::parse($history->validated_at)->format('d-m-Y H:i') : '-' }}</b>
                        </div>
                    @else
                        <div class="form-text">
                            Pilih hasil diagnosa menurut pakar untuk membandingkan dengan prediksi sistem.
                        </div>
                    @endif
                </div>

                @if(!empty($history->expert_damage_name))
                    <div class="mb-3">
                        <div class="stat-label">Validasi Pakar Saat Ini</div>
                        <div class="fw-semibold">{{ $history->expert_damage_name }}</div>
                        <div class="mt-1">
                            @if(($history->expert_damage_category ?? '') === 'hardware')
                                <span class="badge bg-primary">Hardware</span>
                            @elseif(($history->expert_damage_category ?? '') === 'software')
                                <span class="badge bg-success">Software</span>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Catatan Pakar (opsional)</label>
                    <textarea
                        name="note"
                        rows="4"
                        class="form-control"
                        placeholder="Contoh: gejala lebih cocok mengarah ke kerusakan tertentu karena ..."
                    >{{ old('note', $history->validation_note ?? '') }}</textarea>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary">Simpan Validasi</button>
                    <a href="/evaluasi" class="btn btn-outline-secondary">Kembali ke Daftar</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection