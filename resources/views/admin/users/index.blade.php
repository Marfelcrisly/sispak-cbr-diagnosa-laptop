@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Kelola Users</h3>
    <a href="/admin/users/create" class="btn btn-primary btn-sm">+ Tambah User</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td><span class="badge bg-dark">{{ $u->role }}</span></td>
                            <td>{{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('d-m-Y H:i') : '-' }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="/admin/users/{{ $u->id }}/edit">
                                    Edit
                                </a>

                                <form method="POST" action="/admin/users/{{ $u->id }}/delete" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if($users->count() === 0)
                        <tr>
                            <td colspan="6" class="text-muted">Belum ada user.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection