<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sispak CBR</title>

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <style>
        :root{
            --bg1:#eef4ff;
            --bg2:#f6fbff;
            --primary:#2563eb;
            --primary-dark:#1d4ed8;
            --text:#172033;
            --muted:#6b7280;
            --line:#dbe6f4;
            --card:rgba(255,255,255,.72);
            --shadow:0 20px 60px rgba(15, 23, 42, .12);
            --radius:28px;
        }

        *{ box-sizing:border-box; }

        html, body{
            margin:0;
            min-height:100%;
            font-family:"Figtree", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left, rgba(37,99,235,.16), transparent 30%),
                radial-gradient(circle at bottom right, rgba(16,185,129,.12), transparent 26%),
                linear-gradient(135deg, var(--bg1), var(--bg2));
        }

        body{
            position:relative;
            overflow-x:hidden;
        }

        .bg-orb{
            position:absolute;
            border-radius:50%;
            filter:blur(18px);
            opacity:.45;
            pointer-events:none;
        }

        .orb-1{
            width:240px;
            height:240px;
            top:70px;
            left:80px;
            background:rgba(37,99,235,.18);
        }

        .orb-2{
            width:180px;
            height:180px;
            bottom:80px;
            right:90px;
            background:rgba(16,185,129,.16);
        }

        .login-shell{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:32px 18px;
            position:relative;
            z-index:1;
        }

        .login-wrap{
            width:100%;
            max-width:1100px;
            display:grid;
            grid-template-columns: 1.05fr .95fr;
            background:var(--card);
            backdrop-filter:blur(18px);
            border:1px solid rgba(255,255,255,.55);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            overflow:hidden;
        }

        .login-brand{
            position:relative;
            padding:48px 42px;
            background:
                linear-gradient(155deg, rgba(37,99,235,.95), rgba(29,78,216,.86)),
                linear-gradient(180deg, #2563eb, #1d4ed8);
            color:#fff;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            min-height:620px;
        }

        .login-brand::before{
            content:"";
            position:absolute;
            inset:auto -60px -80px auto;
            width:260px;
            height:260px;
            border-radius:50%;
            background:rgba(255,255,255,.08);
        }

        .login-brand::after{
            content:"";
            position:absolute;
            top:-40px;
            left:-40px;
            width:180px;
            height:180px;
            border-radius:50%;
            background:rgba(255,255,255,.06);
        }

        .brand-top,
        .brand-bottom{
            position:relative;
            z-index:1;
        }

        .brand-badge{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:10px 16px;
            border:1px solid rgba(255,255,255,.18);
            border-radius:999px;
            background:rgba(255,255,255,.1);
            font-size:13px;
            font-weight:600;
            letter-spacing:.02em;
            backdrop-filter:blur(10px);
        }

        .brand-title{
            margin:20px 0 10px;
            font-size:42px;
            line-height:1.08;
            font-weight:800;
            letter-spacing:-.04em;
        }

        .brand-subtitle{
            max-width:420px;
            font-size:16px;
            line-height:1.7;
            color:rgba(255,255,255,.88);
        }

        .feature-list{
            display:grid;
            gap:14px;
            margin-top:28px;
        }

        .feature-item{
            display:flex;
            gap:12px;
            align-items:flex-start;
            padding:14px 16px;
            border:1px solid rgba(255,255,255,.12);
            border-radius:18px;
            background:rgba(255,255,255,.08);
        }

        .feature-icon{
            width:38px;
            height:38px;
            flex:0 0 38px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:rgba(255,255,255,.14);
            font-size:18px;
        }

        .feature-text strong{
            display:block;
            font-size:14px;
            margin-bottom:4px;
        }

        .feature-text span{
            font-size:13px;
            color:rgba(255,255,255,.82);
            line-height:1.5;
        }

        .brand-footer{
            margin-top:28px;
            color:rgba(255,255,255,.78);
            font-size:13px;
        }

        .login-panel{
            padding:46px 42px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:rgba(255,255,255,.48);
        }

        .login-card{
            width:100%;
            max-width:410px;
        }

        .login-logo{
            display:flex;
            align-items:center;
            gap:12px;
            margin-bottom:26px;
        }

        .logo-mark{
            width:48px;
            height:48px;
            border-radius:16px;
            background:linear-gradient(135deg, var(--primary), var(--primary-dark));
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:800;
            box-shadow:0 14px 34px rgba(37,99,235,.28);
        }

        .logo-text{
            font-size:15px;
            color:var(--muted);
        }

        .logo-text strong{
            display:block;
            font-size:22px;
            color:var(--text);
            line-height:1.2;
            margin-bottom:2px;
        }

        .login-heading{
            margin-bottom:24px;
        }

        .login-heading h1{
            margin:0 0 8px;
            font-size:32px;
            line-height:1.15;
            font-weight:800;
            letter-spacing:-.03em;
        }

        .login-heading p{
            margin:0;
            color:var(--muted);
            line-height:1.7;
        }

        .form-group{
            margin-bottom:18px;
        }

        .form-label{
            display:block;
            font-size:14px;
            font-weight:700;
            margin-bottom:8px;
            color:#334155;
        }

        .form-input{
            width:100%;
            height:54px;
            border-radius:18px;
            border:1px solid var(--line);
            background:rgba(255,255,255,.88);
            padding:0 18px;
            font-size:15px;
            color:var(--text);
            outline:none;
            transition:.2s ease;
        }

        .form-input:focus{
            border-color:#93c5fd;
            box-shadow:0 0 0 4px rgba(59,130,246,.12);
            background:#fff;
        }

        .helper-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            margin:4px 0 22px;
            flex-wrap:wrap;
        }

        .remember-wrap{
            display:flex;
            align-items:center;
            gap:10px;
            color:#475569;
            font-size:14px;
        }

        .remember-wrap input{
            width:16px;
            height:16px;
        }

        .link{
            color:var(--primary);
            text-decoration:none;
            font-size:14px;
            font-weight:600;
        }

        .link:hover{
            color:var(--primary-dark);
        }

        .btn-login{
            width:100%;
            height:54px;
            border:none;
            border-radius:18px;
            background:linear-gradient(135deg, var(--primary), var(--primary-dark));
            color:#fff;
            font-size:16px;
            font-weight:800;
            letter-spacing:.02em;
            cursor:pointer;
            transition:.2s ease;
            box-shadow:0 16px 34px rgba(37,99,235,.28);
        }

        .btn-login:hover{
            transform:translateY(-1px);
            box-shadow:0 18px 38px rgba(37,99,235,.34);
        }

        .status,
        .error-box{
            border-radius:18px;
            padding:14px 16px;
            margin-bottom:18px;
            font-size:14px;
            line-height:1.6;
        }

        .status{
            background:#ecfdf5;
            border:1px solid #bbf7d0;
            color:#166534;
        }

        .error-box{
            background:#fef2f2;
            border:1px solid #fecaca;
            color:#b91c1c;
        }

        .error-list{
            margin:8px 0 0 18px;
            padding:0;
        }

        .page-note{
            text-align:center;
            margin-top:22px;
            color:#64748b;
            font-size:13px;
        }

        @media (max-width: 980px){
            .login-wrap{
                grid-template-columns:1fr;
            }

            .login-brand{
                min-height:auto;
                padding:34px 28px;
            }

            .login-panel{
                padding:34px 24px 30px;
            }

            .brand-title{
                font-size:34px;
            }
        }

        @media (max-width: 576px){
            .login-shell{
                padding:18px 12px;
            }

            .login-brand,
            .login-panel{
                padding:26px 20px;
            }

            .login-heading h1{
                font-size:28px;
            }

            .brand-title{
                font-size:28px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>

    <div class="login-shell">
        <div class="login-wrap">

            <div class="login-brand">
                <div class="brand-top">
                    <div class="brand-badge">
                        <span>●</span>
                        Sistem Pakar Case-Based Reasoning
                    </div>

                    <h2 class="brand-title">
                        Diagnosa Kerusakan Laptop<br>
                        Hardware & Software
                    </h2>

                    <p class="brand-subtitle">
                        Platform diagnosis berbasis CBR untuk membantu pengguna mengenali
                        gejala kerusakan laptop secara lebih cepat, terstruktur, dan mudah dipahami.
                    </p>

                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon">⚙️</div>
                            <div class="feature-text">
                                <strong>Diagnosa Lebih Cepat</strong>
                                <span>Temukan kemiripan kasus dari gejala yang dipilih pengguna.</span>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">🧠</div>
                            <div class="feature-text">
                                <strong>Metode CBR</strong>
                                <span>Memanfaatkan pengalaman kasus lama sebagai dasar rekomendasi solusi.</span>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">📊</div>
                            <div class="feature-text">
                                <strong>Data Tersusun Rapi</strong>
                                <span>Mengelola gejala, kerusakan, evaluasi, retain, dan riwayat diagnosa.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="brand-bottom">
                    <div class="brand-footer">
                        Sispak CBR • Skripsi Sistem Pakar Diagnosa Kerusakan Laptop
                    </div>
                </div>
            </div>

            <div class="login-panel">
                <div class="login-card">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </div>
</body>
</html>