@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Dashboard Admin</h3>
            <div class="page-subtitle">
                Monitoring statistik diagnosa, retain, dan evaluasi sistem secara ringkas.
            </div>
        </div>

        <form method="POST" action="/admin/export/pdf" id="exportPdfForm" class="d-inline">
            @csrf
            <input type="hidden" name="range" id="exportRange" value="{{ $range }}">
            <input type="hidden" name="from" id="exportFrom" value="{{ request('from') }}">
            <input type="hidden" name="to" id="exportTo" value="{{ request('to') }}">
            <input type="hidden" name="chart_image" id="chartImageInput">
            <button type="submit" class="btn btn-outline-primary btn-sm">Export PDF</button>
        </form>
    </div>

    <div class="soft-panel section-gap">
        <form method="GET" action="/admin" class="d-flex flex-wrap gap-2 align-items-start" id="filterForm">
            <div>
                <select name="range" class="form-select form-select-sm" style="width: 180px;" id="rangeSelect">
                    <option value="today"  {{ $range === 'today' ? 'selected' : '' }}>Hari ini</option>
                    <option value="7d"     {{ $range === '7d' ? 'selected' : '' }}>7 hari terakhir</option>
                    <option value="30d"    {{ $range === '30d' ? 'selected' : '' }}>30 hari terakhir</option>
                    <option value="custom" {{ $range === 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>

            <div>
                <input
                    type="date"
                    name="from"
                    class="form-control form-control-sm"
                    value="{{ request('from') ? request('from') : ($range === 'custom' ? \Carbon\Carbon::parse($from)->format('Y-m-d') : '') }}"
                    style="width: 170px;"
                    id="fromDate"
                >
                <div class="form-text small" id="fromHelp">Tanggal mulai</div>
            </div>

            <div>
                <input
                    type="date"
                    name="to"
                    class="form-control form-control-sm"
                    value="{{ request('to') ? request('to') : ($range === 'custom' ? \Carbon\Carbon::parse($to)->format('Y-m-d') : '') }}"
                    style="width: 170px;"
                    id="toDate"
                >
                <div class="form-text small" id="toHelp">Tanggal akhir</div>
            </div>

            <div>
                <button class="btn btn-sm btn-dark" type="submit">Terapkan</button>
            </div>
        </form>

        <div class="small mt-2" id="customInfo"></div>

        <div class="page-subtitle mt-3">
            Periode:
            <b>{{ \Carbon\Carbon::parse($from)->format('d-m-Y H:i') }}</b>
            s/d
            <b>{{ \Carbon\Carbon::parse($to)->format('d-m-Y H:i') }}</b>
        </div>
    </div>

    @php
        $validPercent = $stats['total_diagnosa'] > 0 ? round(($stats['valid'] / $stats['total_diagnosa']) * 100, 2) : 0;
        $reviewPercent = $stats['total_diagnosa'] > 0 ? round(($stats['perlu_review'] / $stats['total_diagnosa']) * 100, 2) : 0;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Total Diagnosa</div>
                    <div class="stat-value">{{ $stats['total_diagnosa'] }}</div>
                    <div class="stat-meta mt-2">Jumlah seluruh diagnosa pada periode terpilih.</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Valid</div>
                    <div class="stat-value text-success">{{ $stats['valid'] }}</div>
                    <div class="stat-meta mt-2">Hasil diagnosa yang memenuhi threshold.</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Perlu Review</div>
                    <div class="stat-value text-warning">{{ $stats['perlu_review'] }}</div>
                    <div class="stat-meta mt-2">Hasil diagnosa yang masuk proses retain.</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Pending Retain</div>
                    <div class="stat-value text-primary">{{ $stats['pending'] }}</div>
                    <div class="stat-meta mt-2">
                        <a href="/retain" class="muted-link">Lihat antrian</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Approved</div>
                    <div class="stat-value">{{ $stats['approved'] }}</div>
                    <div class="stat-meta mt-2">Jumlah case retain yang disetujui.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Rejected</div>
                    <div class="stat-value">{{ $stats['rejected'] }}</div>
                    <div class="stat-meta mt-2">Jumlah case retain yang ditolak.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="stat-label">Master Data</div>
                    <div class="stat-meta mt-2">
                        Case: <b>{{ $stats['total_case'] ?? '-' }}</b> |
                        Gejala: <b>{{ $stats['total_gejala'] ?? '-' }}</b> |
                        Kerusakan: <b>{{ $stats['total_kerusakan'] ?? '-' }}</b>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card interactive-card mb-4">
        <div class="card-header fw-bold">Ringkasan Persentase (Periode terpilih)</div>
        <div class="card-body">
            <div class="mb-2 d-flex justify-content-between">
                <span>Valid</span>
                <strong class="text-success">{{ $validPercent }}%</strong>
            </div>
            <div class="progress mb-3" style="height:18px;">
                <div class="progress-bar bg-success" style="width: {{ $validPercent }}%">
                    {{ $validPercent }}%
                </div>
            </div>

            <div class="mb-2 d-flex justify-content-between">
                <span>Perlu Review (Retain)</span>
                <strong class="text-warning">{{ $reviewPercent }}%</strong>
            </div>
            <div class="progress" style="height:18px;">
                <div class="progress-bar bg-warning text-dark" style="width: {{ $reviewPercent }}%">
                    {{ $reviewPercent }}%
                </div>
            </div>
        </div>
    </div>

    <div class="card interactive-card mb-4">
        <div class="card-header fw-bold">Trend Diagnosa per Hari (Valid vs Perlu Review)</div>
        <div class="card-body">
            <div class="chart-wrap">
                <canvas id="trendChart" height="90"></canvas>
            </div>
            <div class="small text-muted mt-2">Garis hijau: Valid — Garis kuning: Perlu Review.</div>
        </div>
    </div>

    <div class="card interactive-card">
        <div class="card-header fw-bold">Diagnosa Terbaru (Periode terpilih)</div>
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
                            <th>Threshold</th>
                            <th>Status</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent as $r)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $r->user_name ?? '-' }}</td>
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
                                    @if($r->needs_review)
                                        <span class="badge bg-warning text-dark">Perlu Review</span>
                                    @else
                                        <span class="badge bg-primary">Valid</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-dark" href="/riwayat/{{ $r->id }}">Lihat</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">
                                    Belum ada data pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const range = document.getElementById('rangeSelect');
    const from = document.getElementById('fromDate');
    const to = document.getElementById('toDate');
    const customInfo = document.getElementById('customInfo');

    const exportRange = document.getElementById('exportRange');
    const exportFrom  = document.getElementById('exportFrom');
    const exportTo    = document.getElementById('exportTo');

    function toggleDateHint() {
        const isCustom = range.value === 'custom';

        from.classList.toggle('border-primary', isCustom);
        to.classList.toggle('border-primary', isCustom);

        if (isCustom) {
            customInfo.className = 'small mt-2 text-primary';
            customInfo.textContent = 'Mode custom aktif. Silakan pilih tanggal mulai dan tanggal akhir.';
        } else {
            customInfo.className = 'small mt-2 text-muted';
            customInfo.textContent = 'Tanggal hanya dipakai jika memilih mode custom.';
        }

        exportRange.value = range.value;
        exportFrom.value = from.value;
        exportTo.value = to.value;
    }

    range.addEventListener('change', toggleDateHint);
    from.addEventListener('change', toggleDateHint);
    to.addEventListener('change', toggleDateHint);

    toggleDateHint();

    const trendLabels = @json($trendLabels ?? []);
    const trendValid  = @json($trendCountsValid ?? []);
    const trendReview = @json($trendCountsReview ?? []);

    let trendChartInstance = null;
    const canvas = document.getElementById('trendChart');

    if (canvas) {
        trendChartInstance = new Chart(canvas, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'Valid',
                        data: trendValid,
                        tension: 0.25,
                        fill: true,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.08)'
                    },
                    {
                        label: 'Perlu Review',
                        data: trendReview,
                        tension: 0.25,
                        fill: true,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.08)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    const exportForm = document.getElementById('exportPdfForm');
    const chartImageInput = document.getElementById('chartImageInput');

    if (exportForm) {
        exportForm.addEventListener('submit', function() {
            exportRange.value = range.value;
            exportFrom.value = from.value;
            exportTo.value = to.value;

            if (trendChartInstance && chartImageInput) {
                chartImageInput.value = trendChartInstance.toBase64Image();
            } else if (chartImageInput) {
                chartImageInput.value = '';
            }
        });
    }
});
</script>
@endsection