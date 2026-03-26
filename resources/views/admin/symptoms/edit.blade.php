@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Edit Gejala</h3>
            <div class="page-subtitle">
                Ubah data gejala, termasuk nama dan kategorinya.
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

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card interactive-card">
        <div class="card-body">
            <form method="POST" action="/admin/symptoms/{{ $symptom->id }}/update">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Gejala</label>
                    <input type="text"
                           name="code"
                           class="form-control"
                           value="{{ old('code', $symptom->code) }}"
                           maxlength="20"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Gejala</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $symptom->name) }}"
                           maxlength="255"
                           required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category" class="form-select" required>
                        <option value="hardware" {{ old('category', $symptom->category) === 'hardware' ? 'selected' : '' }}>Hardware</option>
                        <option value="software" {{ old('category', $symptom->category) === 'software' ? 'selected' : '' }}>Software</option>
                    </select>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary">Update</button>
                    <a href="/admin/symptoms" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>

            <hr>

            <form method="POST"
                  action="/admin/symptoms/{{ $symptom->id }}/delete"
                  onsubmit="return confirm('Yakin hapus gejala ini?')">
                @csrf
                <button class="btn btn-outline-danger">Hapus</button>
            </form>
        </div>
    </div>

</div>
@endsection