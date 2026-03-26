@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-1">Diagnosa Kerusakan Laptop</h3>
            <div class="text-muted small">
                Pilih kategori dan gejala yang kamu alami. Ketik di kolom search lalu klik saran yang muncul agar cepat.
            </div>
        </div>
        <div class="text-muted small">
            <span class="badge bg-dark" id="selectedCount">0</span> gejala dipilih
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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

    @php
        $currentCategory = strtolower(trim((string)($category ?? old('category', ''))));

        $normalized = collect($symptoms)->map(function($s){
            $cat = strtolower(trim((string)($s->category ?? 'hardware')));
            $s->cat_norm = $cat;
            return $s;
        });

        $grouped = $normalized->groupBy('cat_norm');
        $categories = ['hardware', 'software'];
    @endphp

    <form method="POST" action="/diagnosa" id="diagnosaForm">
        @csrf

        <input type="hidden" name="category" id="categoryHidden" value="{{ old('category', $currentCategory) }}">

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5 position-relative">

                        <input type="text"
                               class="form-control"
                               id="q"
                               placeholder="Cari gejala (contoh: wifi, boot, layar, baterai, keyboard...)"
                               autocomplete="off">

                        <div id="suggestBox"
                             class="list-group position-absolute w-100 shadow-sm"
                             style="z-index: 1050; display:none; max-height: 260px; overflow:auto; margin-top: 6px;">
                        </div>

                        <div class="form-text">
                            Tips: ketik kata bebas (depan/tengah/belakang), lalu klik saran untuk langsung memilih.
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select class="form-select" id="cat">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $c)
                                <option value="{{ $c }}" {{ $c === $currentCategory ? 'selected' : '' }}>
                                    {{ ucfirst($c) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Pilih Hardware atau Software agar hasil diagnosa sesuai kategori.
                        </div>
                    </div>

                    <div class="col-md-4 d-flex gap-2 justify-content-md-end">
                        <button type="button" class="btn btn-outline-secondary" id="btnReset">
                            Reset Pilihan
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Proses Diagnosa
                        </button>
                    </div>
                </div>

                <div class="mt-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <div class="small text-muted mb-1">Gejala Terpilih:</div>
                        <div id="selectedChips" class="d-flex flex-wrap gap-2"></div>
                        <div id="selectedEmpty" class="text-muted small">Belum ada gejala dipilih.</div>
                    </div>

                    <div>
                        @if($currentCategory === 'hardware')
                            <span class="badge bg-primary">Kategori aktif: Hardware</span>
                        @elseif($currentCategory === 'software')
                            <span class="badge bg-success">Kategori aktif: Software</span>
                        @else
                            <span class="badge bg-secondary">Kategori aktif: Belum dipilih</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion shadow-sm" id="symptomsAcc">
            @php $accIndex = 0; @endphp

            @foreach($grouped as $catName => $items)
                @php
                    $accIndex++;
                    $headingId = "heading".$accIndex;
                    $collapseId = "collapse".$accIndex;
                @endphp

                <div class="accordion-item symptom-category" data-cat="{{ strtolower($catName) }}" data-collapse-id="{{ $collapseId }}">
                    <h2 class="accordion-header" id="{{ $headingId }}">
                        <button class="accordion-button {{ $accIndex === 1 ? '' : 'collapsed' }}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#{{ $collapseId }}"
                                aria-expanded="{{ $accIndex === 1 ? 'true' : 'false' }}"
                                aria-controls="{{ $collapseId }}">
                            <div class="d-flex flex-wrap align-items-center gap-2 w-100">
                                <span class="fw-semibold">{{ ucfirst($catName) }}</span>
                                <span class="badge bg-secondary">{{ count($items) }}</span>
                                <span class="ms-auto small text-muted d-none d-md-inline">Klik untuk buka/tutup</span>
                            </div>
                        </button>
                    </h2>

                    <div id="{{ $collapseId }}"
                         class="accordion-collapse collapse {{ $accIndex === 1 ? 'show' : '' }}"
                         aria-labelledby="{{ $headingId }}"
                         data-bs-parent="#symptomsAcc">
                        <div class="accordion-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($items as $s)
                                    @php
                                        $label = trim($s->code.' - '.$s->name);
                                        $desc  = trim((string)($s->description ?? ''));
                                        $catN  = strtolower($s->cat_norm ?? $catName);
                                        $searchText = strtolower($label.' '.$desc.' '.$catN);
                                    @endphp

                                    <label class="list-group-item d-flex gap-3 align-items-start symptom-item"
                                           data-id="{{ $s->id }}"
                                           data-code="{{ $s->code }}"
                                           data-name="{{ $s->name }}"
                                           data-text="{{ $searchText }}"
                                           data-cat="{{ $catN }}">
                                        <input class="form-check-input mt-1 symptom-check"
                                               type="checkbox"
                                               name="symptoms[]"
                                               value="{{ $s->id }}"
                                               data-code="{{ $s->code }}"
                                               data-name="{{ $s->name }}"
                                               {{ in_array($s->id, old('symptoms', [])) ? 'checked' : '' }}>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">
                                                <span class="sym-code">{{ $s->code }}</span>
                                                <span class="text-muted">-</span>
                                                <span class="sym-name">{{ $s->name }}</span>
                                            </div>
                                            @if($desc !== '')
                                                <div class="small text-muted mt-1">{{ $desc }}</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </form>

</div>

<script>
(function(){
    const q = document.getElementById('q');
    const cat = document.getElementById('cat');
    const btnReset = document.getElementById('btnReset');
    const suggestBox = document.getElementById('suggestBox');
    const selectedCount = document.getElementById('selectedCount');
    const selectedChips = document.getElementById('selectedChips');
    const selectedEmpty = document.getElementById('selectedEmpty');
    const categoryHidden = document.getElementById('categoryHidden');

    const items = Array.from(document.querySelectorAll('.symptom-item'));
    const checks = Array.from(document.querySelectorAll('.symptom-check'));
    const categories = Array.from(document.querySelectorAll('.symptom-category'));

    function escapeHtml(str){
        return (str || '').replace(/[&<>"']/g, m => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[m]));
    }

    function syncHiddenCategory(){
        const val = (cat.value || '').trim().toLowerCase();
        categoryHidden.value = val;
    }

    function updateSelectedUI(){
        const selected = checks.filter(c => c.checked).map(c => ({
            id: c.value,
            code: c.dataset.code,
            name: c.dataset.name
        }));

        selectedCount.textContent = selected.length;
        selectedChips.innerHTML = '';

        if (selected.length === 0){
            selectedEmpty.style.display = '';
            return;
        }

        selectedEmpty.style.display = 'none';

        selected.forEach(s => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'btn btn-sm btn-outline-dark';
            chip.innerHTML = `${escapeHtml(s.code)} <span class="text-muted">-</span> ${escapeHtml(s.name)} <span class="ms-1">✕</span>`;
            chip.addEventListener('click', () => {
                const target = checks.find(c => c.value === s.id);
                if (target){
                    target.checked = false;
                    updateSelectedUI();
                }
            });
            selectedChips.appendChild(chip);
        });
    }

    function applyFilter(){
        const term = (q.value || '').trim().toLowerCase();
        const catVal = (cat.value || '').trim().toLowerCase();

        items.forEach(el => {
            const text = (el.dataset.text || '').toLowerCase();
            const itemCat = (el.dataset.cat || '').toLowerCase();
            const matchTerm = term === '' ? true : text.includes(term);
            const matchCat = catVal === '' ? true : itemCat === catVal;
            el.style.display = (matchTerm && matchCat) ? '' : 'none';
        });

        categories.forEach(box => {
            const catName = (box.dataset.cat || '').toLowerCase();
            const visibleInside = items.some(it => (it.dataset.cat || '').toLowerCase() === catName && it.style.display !== 'none');
            box.style.display = visibleInside ? '' : 'none';
        });
    }

    function closeSuggest(){
        suggestBox.style.display = 'none';
        suggestBox.innerHTML = '';
    }

    function openCategoryOfItem(itemEl){
        const itemCat = (itemEl.dataset.cat || '').toLowerCase();
        const catBox = categories.find(c => (c.dataset.cat || '').toLowerCase() === itemCat);
        if (!catBox) return;

        const collapseId = catBox.dataset.collapseId;
        const collapseEl = document.getElementById(collapseId);
        if (!collapseEl) return;

        if (window.bootstrap && bootstrap.Collapse){
            const bs = bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
            bs.show();
        } else {
            collapseEl.classList.add('show');
        }
    }

    function selectSymptomById(id){
        const checkbox = checks.find(c => c.value === String(id));
        const itemEl = items.find(i => i.dataset.id === String(id));

        if (checkbox){
            checkbox.checked = true;
        }

        updateSelectedUI();

        if (itemEl){
            openCategoryOfItem(itemEl);
            itemEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            itemEl.classList.add('border', 'border-primary');
            setTimeout(() => itemEl.classList.remove('border','border-primary'), 700);
        }
    }

    function buildSuggest(){
        const term = (q.value || '').trim().toLowerCase();
        const catVal = (cat.value || '').trim().toLowerCase();

        if (term.length < 2){
            closeSuggest();
            return;
        }

        const matched = items
            .filter(el => {
                const text = (el.dataset.text || '').toLowerCase();
                const itemCat = (el.dataset.cat || '').toLowerCase();
                const matchTerm = text.includes(term);
                const matchCat = catVal === '' ? true : itemCat === catVal;
                return matchTerm && matchCat;
            })
            .slice(0, 10);

        if (matched.length === 0){
            suggestBox.innerHTML = `<div class="list-group-item text-muted small">Tidak ada yang cocok.</div>`;
            suggestBox.style.display = '';
            return;
        }

        suggestBox.innerHTML = '';
        matched.forEach(el => {
            const id = el.dataset.id;
            const code = el.dataset.code || '';
            const name = el.dataset.name || '';
            const itemCat = el.dataset.cat || '';

            const a = document.createElement('button');
            a.type = 'button';
            a.className = 'list-group-item list-group-item-action';
            a.innerHTML = `
                <div class="d-flex justify-content-between gap-2">
                    <div>
                        <b>${escapeHtml(code)}</b> - ${escapeHtml(name)}
                        <div class="small text-muted">${escapeHtml(itemCat)}</div>
                    </div>
                    <div class="small text-muted">Klik pilih</div>
                </div>
            `;
            a.addEventListener('click', () => {
                selectSymptomById(id);
                q.value = '';
                closeSuggest();
                applyFilter();
            });
            suggestBox.appendChild(a);
        });

        suggestBox.style.display = '';
    }

    q.addEventListener('input', () => {
        applyFilter();
        buildSuggest();
    });

    q.addEventListener('keydown', (e) => {
        if (e.key === 'Enter'){
            const first = suggestBox.querySelector('button.list-group-item-action');
            if (first){
                e.preventDefault();
                first.click();
            }
        } else if (e.key === 'Escape'){
            closeSuggest();
        }
    });

    document.addEventListener('click', (e) => {
        if (!suggestBox.contains(e.target) && e.target !== q){
            closeSuggest();
        }
    });

    cat.addEventListener('change', () => {
        syncHiddenCategory();
        applyFilter();
        buildSuggest();
    });

    checks.forEach(c => c.addEventListener('change', updateSelectedUI));

    btnReset.addEventListener('click', () => {
        checks.forEach(c => c.checked = false);
        updateSelectedUI();
    });

    syncHiddenCategory();
    applyFilter();
    updateSelectedUI();
})();
</script>
@endsection