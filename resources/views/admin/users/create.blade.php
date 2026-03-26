@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Tambah User</h3>
    <a href="/admin/users" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="/admin/users">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
                <div class="form-text">Minimal 6 karakter.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="user" {{ old('role')=='user'?'selected':'' }}>user</option>
                    <option value="teknisi" {{ old('role')=='teknisi'?'selected':'' }}>teknisi</option>
                    <option value="admin" {{ old('role')=='admin'?'selected':'' }}>admin</option>
                </select>
            </div>

            <button class="btn btn-success" type="submit">Simpan</button>
        </form>
    </div>
</div>

@endsection