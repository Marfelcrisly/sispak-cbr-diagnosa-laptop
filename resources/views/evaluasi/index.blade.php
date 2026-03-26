@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Evaluasi / Validasi Pakar</h3>
            <div class="page-subtitle">
                Bandingkan hasil prediksi sistem dengan validasi pakar pada periode terpilih.
            </div>
        </div>

        <form method="GET" action="/evaluasi" class="d-flex flex-wrap gap-2 align-items-start" id="filterForm">
            <div>
                <select name="range" class="form-select form-select-sm" style="width: 180px;" id="rangeSelect">
                    <option value="today"  {{ ($range ?? '30d') === 'today' ? 'selected' : '' }}>Hari ini</option>
                    <option value="7d"     {{ ($range ?? '30d') === '7d' ? 'selected' : '' }}>7 hari terakhir</option>
                    <option value="30d"    {{ ($range ?? '30d') === '30d' ? 'selected' : '' }}>30 hari terakhir</option>
                    <option value="custom" {{ ($range ?? '30d') === 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>

            <div>
                <input
                    type="date"
                    name="from"
                    class="form-control form-control-sm"
                    value="{{ request('from') ? request('from') : (($range ?? '') === 'custom' && !empty($from) ? \Carbon\Carbon::parse($from)->format('Y-m-d') : '') }}"
                    style="width: 170px;"
                    id="fromDate"
                >
                <div class="form-text small">Tanggal mulai</div>
            </div>

            <div>
                <input
                    type="date"
                    name="to"
                    class="form-control form-control-sm"
                    value="{{ request('to') ? request('to') : (($range ?? '') === 'custom' && !empty($to) ? \Carbon\Carbon::parse($to)->format('Y-m-d') : '') }}"
                    style="width: 170px;"
                    id="toDate"
                >
                <div class="form-text small">Tanggal akhir</div>
            </div>

            <div>
                <button class="btn btn-sm btn-dark" type="submit">Terapkan</button>
            </div>
        </form>
    </div>

    <div class="soft-panel section-gap">
        <div class="small" id="customInfo"></div>

        <div class="page-subtitle mt-2">
            @if(!empty($from) && !empty($to))
                Periode:
                <b>{{ \Carbon\Carbon::parse($from)->format('d-m-Y H:i') }}</b>
                s/d
                <b>{{ \Carbon\Carbon::parse($to)->format('d-m-Y H:i') }}</b>
            @else
                Periode: <b>-</b>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Total Validasi</div>
                    <div class="stat-value">{{ $totalValidated ?? 0 }}</div>
                    <div class="stat-meta mt-2">Jumlah diagnosis yang sudah divalidasi pakar.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Benar (Prediksi = Pakar)</div>
                    <div class="stat-value text-success">{{ $totalCorrect ?? 0 }}</div>
                    <div class="stat-meta mt-2">Jumlah prediksi sistem yang sesuai dengan validasi pakar.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Akurasi</div>
                    <div class="stat-value text-primary">{{ $accuracy ?? 0 }}%</div>
                    <div class="stat-meta mt-2">Persentase akurasi berdasarkan data tervalidasi.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card interactive-card mb-4">
        <div class="card-header fw-bold">Ringkasan Prediksi vs Validasi Pakar (Top)</div>
        <div class="card-body p-0">
            @if(empty($matrix) || (is_countable($matrix) && count($matrix) === 0))
                <div class="p-3 text-muted">Belum ada data validasi pada periode ini.</div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Prediksi Sistem</th>
                                <th>Validasi Pakar</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($matrix as $m)
                                <tr>
                                    <td>{{ $m->predicted ?? '-' }}</td>
                                    <td>{{ $m->expert ?? '-' }}</td>
                                    <td class="text-end">{{ $m->total ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card interactive-card">
        <div class="card-header fw-bold">Daftar Diagnosa (Periode terpilih)</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Best Case</th>
                            <th>Prediksi Kerusakan</th>
                            <th>Similarity</th>
                            <th>Threshold</th>
                            <th>Status Retain</th>
                            <th>Validasi Pakar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $h)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($h->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $h->user_name ?? '-' }}</td>
                                <td>{{ $h->case_code ?? '-' }}</td>
                                <td>{{ $h->predicted_damage_name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ number_format((float)($h->best_similarity ?? 0), 2) }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ number_format((float)($h->threshold_used ?? 0), 2) }}%
                                    </span>
                                </td>
                                <td>
                                    @if((int)($h->needs_review ?? 0) === 1)
                                        <span class="badge bg-warning text-dark">Perlu Review</span>
                                    @else
                                        <span class="badge bg-primary">Valid</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($h->expert_damage_id))
                                        <span class="badge bg-success">Sudah</span>
                                        <div class="small text-muted mt-1">
                                            {{ $h->expert_damage_name ?? '-' }}
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">Belum</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-dark" href="/evaluasi/{{ $h->id }}">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">
                                    Belum ada data pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($histories, 'links'))
            <div class="card-footer">
                {{ $histories->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const range = document.getElementById('rangeSelect');
    const from = document.getElementById('fromDate');
    const to = document.getElementById('toDate');
    const customInfo = document.getElementById('customInfo');

    function toggleDateHint() {
        const isCustom = range.value === 'custom';

        from.classList.toggle('border-primary', isCustom);
        to.classList.toggle('border-primary', isCustom);

        if (isCustom) {
            customInfo.className = 'small text-primary';
            customInfo.textContent = 'Mode custom aktif. Silakan pilih tanggal mulai dan tanggal akhir.';
        } else {
            customInfo.className = 'small text-muted';
            customInfo.textContent = 'Tanggal hanya dipakai jika memilih mode custom.';
        }
    }

    range.addEventListener('change', toggleDateHint);
    from.addEventListener('change', toggleDateHint);
    to.addEventListener('change', toggleDateHint);

    toggleDateHint();
});
</script>
@endsection