<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Admin PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        .muted { color:#666; }
        .row { width: 100%; margin-bottom: 10px; }
        .box { border: 1px solid #ddd; padding: 10px; border-radius: 6px; }
        .title { font-size: 16px; font-weight: bold; margin: 0 0 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        th { background: #f3f3f3; text-align: left; }
        .badge { display:inline-block; padding: 2px 6px; border-radius: 10px; font-size: 11px; }
        .b-success { background:#d1fae5; }
        .b-warning { background:#fde68a; }
        .b-gray { background:#e5e7eb; }
        .right { text-align: right; }
        .small { font-size: 11px; }
        .mb8 { margin-bottom: 8px; }
        .mb12 { margin-bottom: 12px; }
        .mb16 { margin-bottom: 16px; }
        .hr { height:1px; background:#ddd; margin: 12px 0; }
        img { max-width: 100%; }
    </style>
</head>
<body>

    <div class="row">
        <div class="title">Dashboard Admin - Export PDF</div>
        <div class="muted small">
            Periode:
            <b>{{ \Carbon\Carbon::parse($from)->format('d-m-Y H:i') }}</b>
            s/d
            <b>{{ \Carbon\Carbon::parse($to)->format('d-m-Y H:i') }}</b>
            <span class="muted">| Range: {{ $range ?? '-' }}</span>
        </div>
    </div>

    <div class="row box mb12">
        <table>
            <tr>
                <th>Total Diagnosa</th>
                <th>Valid</th>
                <th>Perlu Review</th>
                <th>Pending</th>
                <th>Approved</th>
                <th>Rejected</th>
            </tr>
            <tr>
                <td class="right"><b>{{ $stats['total_diagnosa'] ?? 0 }}</b></td>
                <td class="right"><b>{{ $stats['valid'] ?? 0 }}</b></td>
                <td class="right"><b>{{ $stats['perlu_review'] ?? 0 }}</b></td>
                <td class="right"><b>{{ $stats['pending'] ?? 0 }}</b></td>
                <td class="right"><b>{{ $stats['approved'] ?? 0 }}</b></td>
                <td class="right"><b>{{ $stats['rejected'] ?? 0 }}</b></td>
            </tr>
        </table>

        <div class="hr"></div>

        <div class="mb8"><b>Ringkasan Persentase</b></div>
        <div class="small">
            Valid: <b>{{ $validPercent ?? 0 }}%</b> &nbsp; | &nbsp;
            Perlu Review: <b>{{ $reviewPercent ?? 0 }}%</b>
        </div>
    </div>

    {{-- CHART IMAGE (OPSIONAL) --}}
    @if(!empty($chartImage))
        <div class="row box mb12">
            <div class="mb8"><b>Grafik Trend (Valid vs Review)</b></div>
            <img src="{{ $chartImage }}" alt="Chart" />
            <div class="muted small">Sumber: chart dari halaman dashboard.</div>
        </div>
    @endif

    <div class="row box mb12">
        <div class="mb8"><b>Top Kerusakan (Top 7)</b></div>
        @if(empty($topDamages) || (is_countable($topDamages) && count($topDamages) === 0))
            <div class="muted">Tidak ada data.</div>
        @else
            <table>
                <tr>
                    <th>Kerusakan</th>
                    <th class="right">Jumlah</th>
                </tr>
                @foreach($topDamages as $d)
                    <tr>
                        <td>{{ $d->damage_name ?? '-' }}</td>
                        <td class="right">{{ $d->total ?? 0 }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="row box mb12">
        <div class="mb8"><b>Top Gejala (Top 10)</b></div>
        @if(empty($topSymptoms) || (is_countable($topSymptoms) && count($topSymptoms) === 0))
            <div class="muted">Tidak ada data.</div>
        @else
            <table>
                <tr>
                    <th>Gejala</th>
                    <th class="right">Jumlah</th>
                </tr>
                @foreach($topSymptoms as $s)
                    <tr>
                        <td><b>{{ $s['code'] ?? '-' }}</b> - {{ $s['name'] ?? '-' }}</td>
                        <td class="right">{{ $s['total'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="row box">
        <div class="mb8"><b>Diagnosa Terbaru (Max 15)</b></div>
        @if(empty($recent) || (is_countable($recent) && count($recent) === 0))
            <div class="muted">Tidak ada data.</div>
        @else
            <table>
                <tr>
                    <th>Tanggal</th>
                    <th>User</th>
                    <th>Best Case</th>
                    <th>Kerusakan</th>
                    <th class="right">Similarity</th>
                    <th class="right">Threshold</th>
                    <th>Status</th>
                </tr>
                @foreach($recent as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d-m-Y H:i') }}</td>
                        <td>{{ $r->user_name ?? '-' }}</td>
                        <td>{{ $r->case_code ?? '-' }}</td>
                        <td>{{ $r->damage_name ?? '-' }}</td>
                        <td class="right">{{ number_format((float)($r->best_similarity ?? 0), 2) }}%</td>
                        <td class="right">{{ number_format((float)($r->threshold_used ?? 0), 2) }}%</td>
                        <td>
                            @if((int)($r->needs_review ?? 0) === 1)
                                <span class="badge b-warning">Perlu Review</span>
                            @else
                                <span class="badge b-success">Valid</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>

</body>
</html>