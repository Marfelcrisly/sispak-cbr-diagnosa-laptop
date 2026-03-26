@extends('layouts.app')

@section('content')

<h3 class="mb-3">Edit Case</h3>

<form method="POST" action="/admin/cases/{{ $case->id }}/update">
@csrf

<div class="card mb-3">
    <div class="card-body">

        <div class="mb-3">
            <label>Case Code</label>
            <input type="text"
                   name="case_code"
                   value="{{ $case->case_code }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label>Kerusakan</label>
            <select name="damage_id" class="form-select" required>
                @foreach($damages as $d)
                    <option value="{{ $d->id }}"
                        {{ $case->damage_id == $d->id ? 'selected':'' }}>
                        {{ $d->code }} - {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>
</div>

<h5>Gejala & Bobot</h5>

<div class="card">
    <div class="card-body" style="max-height:400px; overflow:auto;">
        @foreach($symptoms as $s)
        <div class="row mb-2">
            <div class="col-md-6">
                {{ $s->code }} - {{ $s->name }}
            </div>
            <div class="col-md-3">
                <input type="number"
                       name="symptoms[{{ $s->id }}]"
                       class="form-control"
                       min="0"
                       max="5"
                       value="{{ $existing[$s->id] ?? 0 }}">
            </div>
        </div>
        @endforeach
    </div>
</div>

<button class="btn btn-success mt-3">Update Case</button>

</form>

@endsection