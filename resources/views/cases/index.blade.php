@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Data Case Base</h3>

        @php $role = auth()->user()->role ?? 'user'; @endphp
        @if($role === 'admin' || $role === 'teknisi')
            <a href="/admin/cases" class="btn btn-sm btn-primary">Kelola (Admin/Teknisi)</a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:130px;">Case Code</th>
                            <th>Kerusakan</th>
                            <th class="text-end" style="width:120px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cases as $c)
                            <tr>
                                <td class="fw-semibold">{{ $c->case_code }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $c->damage_name }}</div>
                                    <div class="text-muted small">{{ $c->damage_code }}</div>
                                </td>
                                <td class="text-end">
                                    <a href="/cases/{{ $c->id }}" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Belum ada data case.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection