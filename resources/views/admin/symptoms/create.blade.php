@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Tambah Gejala</h3>
            <div class="page-subtitle">
                Sistem otomatis mengisi kode gejala berikutnya.
            </div>
        </div>

        <a href="/admin/symptoms" class="btn btn-sm btn-outline-secondary">← Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-bold mb-1">Terjadi kesalahan:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card interactive-card">
        <div class="card-body">

            <form method="POST" action="/admin/symptoms">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold">Kode Gejala</label>

                    <div class="input-group">
                        <span class="input-group-text bg-light">Auto</span>
                        <input type="text"
                               name="code"
                               class="form-control fw-bold text-primary"
                               value="{{ old('code', $nextCode) }}"
                               readonly>
                    </div>

                    <div class="form-text">
                        Kode otomatis berdasarkan data terakhir.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Nama Gejala</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           placeholder="Contoh: Laptop cepat panas"
                           value="{{ old('name') }}"
                           maxlength="255"
                           required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category" class="form-select" required>
                        <option value="">-- pilih kategori --</option>
                        <option value="hardware" {{ old('category') === 'hardware' ? 'selected' : '' }}>Hardware</option>
                        <option value="software" {{ old('category') === 'software' ? 'selected' : '' }}>Software</option>
                    </select>
                    <div class="form-text">
                        Pilih apakah gejala ini termasuk hardware atau software.
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary px-4">💾 Simpan</button>
                    <a href="/admin/symptoms" class="btn btn-outline-secondary">Batal</a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection