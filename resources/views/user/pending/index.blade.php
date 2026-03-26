@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-0">Pending Saya</h3>
            <div class="text-muted small">Daftar diagnosa yang masuk antrian Retain</div>
        </div>

        <div class="d-flex gap-2">
            <a href="/diagnosa" class="btn btn-sm btn-primary">+ Diagnosa</a>
            <a href="/user" class="btn btn-sm btn-outline-dark">Dashboard</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:90px;">ID</th>
                            <th style="width:170px;">Tanggal</th>
                            <th style="width:140px;">Best Similarity</th>
                            <th style="width:130px;">Status</th>
                            <th>Best Case / Kerusakan</th>
                            <th style="width:120px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($items ?? []) as $p)

                            @php
                                $status = $p->status ?? 'pending';
                                $badge = match($status) {
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-warning text-dark',
                                };
                            @endphp

                            <tr>
                                <td class="fw-semibold">#{{ $p->id }}</td>

                                <td>
                                    {{ !empty($p->created_at) ? \Carbon\Carbon::parse($p->created_at)->format('d-m-Y H:i') : '-' }}
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ number_format((float)($p->best_similarity ?? 0), 2) }}%
                                    </span>
                                </td>

                                <td>
                                    <span class="badge {{ $badge }}">
                                        {{ strtoupper($status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $p->case_code ?? '-' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $p->damage_name ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <a href="/user/pending/{{ $p->id }}" class="btn btn-sm btn-outline-dark">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada pending.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(isset($items) && method_exists($items, 'links'))
            <div class="card-footer">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection