@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-3">Edit Kerusakan</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="/admin/damages/{{ $damage->id }}/update">
                @csrf

                <div class="mb-3">
                    <label>Kode</label>
                    <input type="text" name="code" class="form-control"
                           value="{{ $damage->code }}" required>
                </div>

                {{-- 🔥 KATEGORI --}}
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="category" class="form-select" required>
                        <option value="hardware" {{ $damage->category == 'hardware' ? 'selected' : '' }}>
                            Hardware
                        </option>
                        <option value="software" {{ $damage->category == 'software' ? 'selected' : '' }}>
                            Software
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ $damage->name }}" required>
                </div>

                <div class="mb-3">
                    <label>Solusi</label>
                    <textarea name="solution" class="form-control">{{ $damage->solution }}</textarea>
                </div>

                <button class="btn btn-primary">Update</button>
                <a href="/admin/damages" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

</div>
@endsection