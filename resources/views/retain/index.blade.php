@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Antrian Retain (Pending Cases)</h3>

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
                            <th style="width:80px">ID</th>
                            <th style="width:170px">Tanggal</th>
                            <th>User</th>
                            <th>Best Case</th>
                            <th>Kerusakan</th>
                            <th style="width:120px">Similarity</th>
                            <th style="width:120px">Status</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $it)
                            <tr>
                                <td>#{{ $it->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($it->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $it->user_name ?? '-' }}</td>
                                <td>{{ $it->case_code ?? '-' }}</td>
                                <td>{{ $it->damage_name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ number_format((float)$it->best_similarity, 2) }}%
                                    </span>
                                </td>
                                <td>
                                    @if($it->status === 'pending')
                                        <span class="badge bg-secondary">pending</span>
                                    @elseif($it->status === 'approved')
                                        <span class="badge bg-success">approved</span>
                                    @else
                                        <span class="badge bg-danger">rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="/retain/{{ $it->id }}" class="btn btn-sm btn-dark">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada antrian retain.
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