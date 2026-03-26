@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-3">Tambah Kerusakan</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="/admin/damages">
                @csrf

                <div class="mb-3">
                    <label>Kode</label>
                    <input type="text" name="code" class="form-control" required>
                </div>

                {{-- 🔥 KATEGORI --}}
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="category" class="form-select" required>
                        <option value="">- pilih -</option>
                        <option value="hardware">Hardware</option>
                        <option value="software">Software</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Solusi</label>
                    <textarea name="solution" class="form-control"></textarea>
                </div>

                <button class="btn btn-primary">Simpan</button>
                <a href="/admin/damages" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

</div>
@endsection