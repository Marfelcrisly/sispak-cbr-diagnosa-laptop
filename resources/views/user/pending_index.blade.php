@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h3 class="mb-0">Antrian Retain Saya</h3>
        <a href="/diagnosa" class="btn btn-sm btn-outline-dark">+ Diagnosa Baru</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:170px;">Tanggal</th>
                            <th style="width:120px;">Status</th>
                            <th>Prediksi</th>
                            <th style="width:120px;">Similarity</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $p)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d-m-Y H:i') }}</td>
                                <td>
                                    @if($p->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($p->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($p->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $p->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $p->damage_name ?? '-' }}</div>
                                    <div class="text-muted small">Best Case: {{ $p->case_code ?? '-' }}</div>
                                    @if(!empty($p->review_note))
                                        <div class="text-muted small mt-1">
                                            <b>Catatan:</b> {{ $p->review_note }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-dark">
                                        {{ number_format((float)$p->best_similarity, 2) }}%
                                    </span>
                                </td>
                                <td>
                                    <a href="/user/pending/{{ $p->id }}" class="btn btn-sm btn-outline-primary">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada antrian retain.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($items, 'links'))
            <div class="card-footer">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection