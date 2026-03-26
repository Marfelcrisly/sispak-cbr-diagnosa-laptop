@extends('layouts.app')

@section('content')
<div class="container">

    <div class="top-actions section-gap">
        <div>
            <h3 class="page-title">Edit Case</h3>
            <div class="page-subtitle">
                Ubah kerusakan, catatan, dan bobot gejala sesuai kategori.
            </div>
        </div>

        <a href="/admin/cases" class="btn btn-sm btn-outline-secondary">← Kembali</a>
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
            <form method="POST" action="/admin/cases/{{ $case->id }}/update">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Case Code</label>
                        <input type="text"
                               name="case_code"
                               class="form-control"
                               value="{{ old('case_code', $case->case_code) }}"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kerusakan</label>
                        <select name="damage_id" id="damage_id" class="form-select" required>
                            <option value="">-- pilih kerusakan --</option>
                            @foreach($damages as $d)
                                <option value="{{ $d->id }}"
                                        data-category="{{ strtolower($d->category) }}"
                                        {{ (string) old('damage_id', $case->damage_id) === (string) $d->id ? 'selected' : '' }}>
                                    {{ $d->code }} - {{ $d->name }} ({{ ucfirst($d->category) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Catatan</label>
                        <input type="text"
                               name="note"
                               class="form-control"
                               value="{{ old('note', $case->note) }}">
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Bobot Gejala</h5>
                    <small class="text-muted">Gejala beda kategori akan disembunyikan otomatis.</small>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:120px;">Kode</th>
                                <th>Nama Gejala</th>
                                <th style="width:140px;">Kategori</th>
                                <th style="width:120px;">Bobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($symptoms as $s)
                                @php
                                    $currentWeight = old('symptoms.'.$s->id, $existing[$s->id] ?? 0);
                                @endphp
                                <tr class="symptom-row" data-category="{{ strtolower($s->category) }}">
                                    <td><span class="badge bg-secondary">{{ $s->code }}</span></td>
                                    <td>{{ $s->name }}</td>
                                    <td>
                                        @if(strtolower($s->category) === 'hardware')
                                            <span class="badge bg-primary">Hardware</span>
                                        @else
                                            <span class="badge bg-success">Software</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number"
                                               name="symptoms[{{ $s->id }}]"
                                               class="form-control symptom-input"
                                               min="0"
                                               max="10"
                                               value="{{ $currentWeight }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button class="btn btn-primary">Update</button>
                    <a href="/admin/cases" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const damageSelect = document.getElementById('damage_id');
    const rows = document.querySelectorAll('.symptom-row');

    function filterSymptoms() {
        const selected = damageSelect.options[damageSelect.selectedIndex];
        const category = selected ? (selected.dataset.category || '') : '';

        rows.forEach(row => {
            const rowCategory = row.dataset.category || '';
            const input = row.querySelector('.symptom-input');

            if (!category || rowCategory === category) {
                row.style.display = '';
                if (input) input.disabled = false;
            } else {
                row.style.display = 'none';
                if (input) {
                    input.disabled = true;
                    input.value = 0;
                }
            }
        });
    }

    damageSelect.addEventListener('change', filterSymptoms);
    filterSymptoms();
});
</script>
@endsection