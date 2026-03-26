@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Riwayat Diagnosa</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:170px">Tanggal</th>
                            <th>User</th>
                            <th style="width:120px">Case</th>
                            <th>Kerusakan</th>
                            <th style="width:130px">Kemiripan</th>
                            <th style="width:120px">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $h)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($h->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $h->user_name ?? '-' }}</td>
                                <td>{{ $h->case_code ?? '-' }}</td>
                                <td>{{ $h->damage_name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ number_format((float)$h->best_similarity, 2) }}%
                                    </span>

                                    {{-- kalau kamu sudah punya kolom needs_review --}}
                                    @if(isset($h->needs_review) && $h->needs_review)
                                        <span class="badge bg-warning text-dark ms-1">Review</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="/riwayat/{{ $h->id }}" class="btn btn-secondary btn-sm">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada riwayat diagnosa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection