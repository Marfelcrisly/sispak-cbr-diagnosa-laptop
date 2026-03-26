<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sispak CBR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --bg-1:#f5f7fb;
            --bg-2:#eef2f9;
            --card:#ffffffcc;
            --line:#e8edf5;
            --text:#172033;
            --muted:#6b7280;
            --shadow:0 10px 30px rgba(15, 23, 42, .08);
            --shadow-hover:0 18px 45px rgba(15, 23, 42, .14);
            --radius:20px;
        }

        html, body {
            min-height: 100%;
            background:
                radial-gradient(circle at top left, rgba(37,99,235,.08), transparent 28%),
                radial-gradient(circle at top right, rgba(16,185,129,.07), transparent 24%),
                linear-gradient(180deg, var(--bg-1), var(--bg-2));
            color: var(--text);
        }

        body {
            font-family: "Segoe UI", Inter, system-ui, -apple-system, sans-serif;
        }

        .navbar-modern {
            background: rgba(15, 23, 42, .92) !important;
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255,255,255,.08);
            box-shadow: 0 8px 26px rgba(2, 6, 23, .18);
        }

        .navbar-brand {
            letter-spacing: .2px;
            font-weight: 800 !important;
        }

        .navbar .nav-link {
            position: relative;
            color: rgba(255,255,255,.78) !important;
            transition: .25s ease;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: #fff !important;
            transform: translateY(-1px);
        }

        .navbar .nav-link::after {
            content: "";
            position: absolute;
            left: .5rem;
            right: .5rem;
            bottom: .2rem;
            height: 2px;
            background: linear-gradient(90deg, #60a5fa, #34d399);
            border-radius: 999px;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform .25s ease;
        }

        .navbar .nav-link:hover::after,
        .navbar .nav-link.active::after {
            transform: scaleX(1);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -.03em;
            margin-bottom: 0;
        }

        .page-subtitle {
            color: var(--muted);
            margin-top: .4rem;
        }

        .card {
            background: var(--card);
            backdrop-filter: blur(10px);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-header {
            background: rgba(255,255,255,.65) !important;
            border-bottom: 1px solid var(--line) !important;
            font-weight: 700;
        }

        .dashboard-card,
        .interactive-card {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
        }

        .dashboard-card::before,
        .interactive-card::before {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            left: var(--mx, 50%);
            top: var(--my, 50%);
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, rgba(96,165,250,.20), rgba(52,211,153,.14) 35%, transparent 70%);
            opacity: 0;
            transition: opacity .22s ease;
            pointer-events: none;
            z-index: 0;
        }

        .dashboard-card:hover,
        .interactive-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(37,99,235,.18);
        }

        .dashboard-card:hover::before,
        .interactive-card:hover::before {
            opacity: 1;
        }

        .dashboard-card > *,
        .interactive-card > * {
            position: relative;
            z-index: 1;
        }

        .stat-label {
            color: var(--muted);
            font-size: .95rem;
            margin-bottom: .35rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.03em;
        }

        .stat-meta {
            font-size: .9rem;
            color: var(--muted);
        }

        .btn {
            border-radius: 14px;
            transition: .25s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            box-shadow: 0 10px 24px rgba(37,99,235,.25);
        }

        .btn-dark {
            background: linear-gradient(135deg, #111827, #1f2937);
            border: none;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-size: .9rem;
            color: #475569;
            background: #f8fafc !important;
            border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }

        .table tbody td {
            vertical-align: middle;
            border-color: #edf2f7;
        }

        .table tbody tr:hover {
            background: rgba(37,99,235,.03);
        }

        .badge {
            border-radius: 999px;
            padding: .5em .7em;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .form-control,
        .form-select {
            border-radius: 14px;
            border-color: #dbe3ef;
            box-shadow: none !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #93c5fd;
        }

        .alert {
            border-radius: 16px;
            box-shadow: var(--shadow);
        }

        .section-gap {
            margin-bottom: 1.5rem;
        }

        .top-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
            align-items: center;
            justify-content: space-between;
        }

        .soft-panel {
            background: rgba(255,255,255,.55);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 1rem;
        }

        .chart-wrap {
            position: relative;
            min-height: 280px;
        }

        .muted-link {
            text-decoration: none;
            color: #2563eb;
        }

        .muted-link:hover {
            color: #1d4ed8;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.6rem;
            }

            .stat-value {
                font-size: 1.7rem;
            }

            .top-actions {
                align-items: stretch;
            }
        }
    </style>
</head>

<body>
    @php
        $role = auth()->check() ? (auth()->user()->role ?? 'user') : 'guest';
    @endphp

    <nav class="navbar navbar-expand-lg navbar-dark navbar-modern sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">Sispak CBR</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('diagnosa*') ? 'active' : '' }}" href="/diagnosa">Diagnosa</a>
                    </li>

                    @if($role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin') ? 'active' : '' }}" href="/admin">Dashboard</a>
                        </li>
                    @elseif($role === 'teknisi')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('teknisi') ? 'active' : '' }}" href="/teknisi">Dashboard</a>
                        </li>
                    @elseif($role === 'user')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('user') ? 'active' : '' }}" href="/user">Dashboard</a>
                        </li>
                    @endif

                    @if($role === 'admin' || $role === 'teknisi')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/cases*') ? 'active' : '' }}" href="/admin/cases">Kelola Case</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/symptoms*') ? 'active' : '' }}" href="/admin/symptoms">Data Gejala</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/damages*') ? 'active' : '' }}" href="/admin/damages">Data Kerusakan</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('riwayat*') ? 'active' : '' }}" href="/riwayat">Riwayat</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('retain*') ? 'active' : '' }}" href="/retain">Retain</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('settings/cbr*') ? 'active' : '' }}" href="/settings/cbr">Setting CBR</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('evaluasi*') ? 'active' : '' }}" href="/evaluasi">Evaluasi</a>
                        </li>
                    @elseif($role === 'user')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('cases*') ? 'active' : '' }}" href="/cases">Data Case</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('riwayat*') ? 'active' : '' }}" href="/riwayat">Riwayat</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('user/pending*') ? 'active' : '' }}" href="/user/pending">Pending Saya</a>
                        </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @if(auth()->check())
                        <span class="text-white small">
                            {{ auth()->user()->name }} ({{ auth()->user()->role }})
                        </span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    @else
                        <a href="/login" class="btn btn-outline-light btn-sm">Login</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.dashboard-card, .interactive-card').forEach((card) => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    card.style.setProperty('--mx', `${x}px`);
                    card.style.setProperty('--my', `${y}px`);
                });
            });
        });
    </script>
</body>

</html>