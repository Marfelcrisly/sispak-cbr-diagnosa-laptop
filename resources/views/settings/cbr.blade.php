@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Setting CBR</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            <form method="POST" action="/settings/cbr">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Threshold Similarity</label>
                    <input type="number"
                           step="0.01"
                           min="0"
                           max="100"
                           class="form-control"
                           name="similarity_threshold"
                           value="{{ number_format((float)$threshold, 2, '.', '') }}"
                           required>
                </div>

                <button class="btn btn-primary">Simpan</button>
            </form>

            <hr>

            <h5>Riwayat (10 terakhir)</h5>
            <ul>
                @foreach($history as $h)
                    <li>
                        {{ number_format((float)$h->similarity_threshold, 2) }}%
                        - {{ $h->updater_name ?? '-' }}
                    </li>
                @endforeach
            </ul>

        </div>
    </div>
</div>
@endsection