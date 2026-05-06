<!DOCTYPE html>
<html lang="id">
@php
    $loginBrandName = data_get($sharedAppSettings ?? [], 'login_brand_name') ?: 'Yayasan Karya Luhur Jaya';
    $loginBrandCaption = data_get($sharedAppSettings ?? [], 'login_brand_caption') ?: 'Sistem laporan operasional yayasan.';
    $loginWelcomeMessage = data_get($sharedAppSettings ?? [], 'login_welcome_message') ?: 'Bekerja dengan cinta akan menghasilkan keajaiban.';
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | {{ $loginBrandName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html {
            font-size: 14px;
        }

        :root {
            --ink: #13253f;
            --muted: #5f7188;
            --line: rgba(148, 163, 184, 0.18);
            --panel: rgba(255, 255, 255, 0.92);
            --accent: #2563eb;
            --accent-strong: #1d4ed8;
            --success: #059669;
            --danger: #dc2626;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Quicksand", "Trebuchet MS", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.22), transparent 28%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.18), transparent 22%),
                linear-gradient(140deg, #eef5ff 0%, #f7fbff 48%, #edf7f6 100%);
        }

        .login-shell {
            display: grid;
            min-height: 100vh;
            padding: 24px;
        }

        .login-stage {
            display: grid;
            grid-template-columns: minmax(320px, 1.04fr) minmax(350px, 0.88fr);
            gap: 18px;
            align-items: stretch;
        }

        .showcase,
        .auth-card {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 32px;
            background: var(--panel);
            box-shadow: 0 28px 60px rgba(15, 23, 42, 0.1);
            backdrop-filter: blur(18px);
        }

        .showcase {
            padding: 28px;
            background:
                radial-gradient(circle at 85% 14%, rgba(56, 189, 248, 0.18), transparent 16%),
                radial-gradient(circle at 15% 86%, rgba(37, 99, 235, 0.16), transparent 20%),
                linear-gradient(145deg, rgba(255, 255, 255, 0.94), rgba(237, 247, 255, 0.92));
        }

        .showcase::before {
            content: "";
            position: absolute;
            inset: auto -70px -90px auto;
            width: 260px;
            height: 260px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.12), transparent 70%);
            pointer-events: none;
        }

        .eyebrow {
            margin: 0;
            color: var(--accent);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .showcase-lead {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 9px 13px;
            border-radius: 999px;
            border: 1px solid rgba(37, 99, 235, 0.12);
            background: rgba(255, 255, 255, 0.78);
            color: #1f3a5f;
            font-size: 0.73rem;
            font-weight: 700;
            box-shadow: 0 14px 26px rgba(37, 99, 235, 0.08);
        }

        .showcase-lead::before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: linear-gradient(180deg, #2563eb, #38bdf8);
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.08);
        }

        .showcase h1 {
            margin: 16px 0 10px;
            max-width: 15ch;
            font-size: clamp(1.72rem, 3vw, 2.65rem);
            line-height: 1.08;
        }

        .showcase p {
            max-width: 58ch;
            margin: 0;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.78;
        }

        .feature-grid {
            display: grid;
            gap: 12px;
            margin-top: 22px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .feature-card {
            padding: 14px 14px 13px;
            border-radius: 22px;
            border: 1px solid rgba(37, 99, 235, 0.12);
            background: rgba(255, 255, 255, 0.74);
        }

        .feature-card strong {
            display: block;
            color: var(--ink);
            font-size: 0.86rem;
        }

        .feature-card span {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 0.75rem;
            line-height: 1.65;
        }

        .showcase-note {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 20px;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: linear-gradient(180deg, rgba(248, 251, 255, 0.94), rgba(241, 245, 249, 0.92));
        }

        .showcase-note strong {
            display: block;
            color: var(--ink);
            font-size: 0.84rem;
            line-height: 1.4;
        }

        .showcase-note span {
            display: block;
            margin-top: 5px;
            color: var(--muted);
            font-size: 0.74rem;
            line-height: 1.62;
        }

        .showcase-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-top: 20px;
        }

        .showcase-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 13px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.76);
            border: 1px solid rgba(37, 99, 235, 0.12);
            color: #1f3a5f;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .auth-card {
            display: grid;
            align-content: center;
            padding: 28px;
        }

        .auth-inner {
            width: min(100%, 358px);
            margin-left: auto;
            margin-right: auto;
        }

        .brand-row {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .brand-mark {
            display: inline-flex;
            height: 42px;
            width: 42px;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: radial-gradient(circle at 30% 30%, #a8ccff 0%, #5ba7ff 38%, #1d5ee0 100%);
            box-shadow: 0 16px 28px rgba(29, 94, 224, 0.22);
        }

        .brand-mark svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: white;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.9;
        }

        .brand-copy strong {
            display: block;
            color: var(--ink);
            font-size: 0.96rem;
            line-height: 1.2;
        }

        .brand-copy span {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 0.72rem;
        }

        .auth-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 2px 0 14px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.08);
            color: #1d4ed8;
            font-size: 0.66rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .auth-card h2 {
            margin: 0;
            color: var(--ink);
            font-size: 1.28rem;
            line-height: 1.16;
        }

        .auth-card > p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.8rem;
            line-height: 1.7;
        }

        .auth-intro {
            margin: 0;
            color: var(--muted);
            font-size: 0.78rem;
            line-height: 1.72;
        }

        .message {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 16px;
            font-size: 0.76rem;
            line-height: 1.62;
        }

        .message.success {
            background: rgba(220, 252, 231, 0.86);
            color: #166534;
            border: 1px solid rgba(34, 197, 94, 0.18);
        }

        .message.error {
            background: rgba(254, 226, 226, 0.88);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.18);
        }

        .auth-form {
            display: grid;
            gap: 14px;
            margin-top: 18px;
            padding: 16px;
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 251, 255, 0.94));
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field-group label {
            color: #334155;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .field-group input {
            height: 44px;
            width: 100%;
            border: 1px solid #d7e1ef;
            border-radius: 15px;
            padding: 11px 13px;
            background: #f8fafc;
            color: #10233d;
            font-size: 0.84rem;
            transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
        }

        .field-group input:focus {
            outline: none;
            border-color: #60a5fa;
            background: white;
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.16);
        }

        .field-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 2px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .remember input {
            width: 14px;
            height: 14px;
            margin: 0;
        }

        .submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 46px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: white;
            font-size: 0.84rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 18px 34px rgba(37, 99, 235, 0.22);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 20px 38px rgba(37, 99, 235, 0.28);
        }

        .helper-text {
            margin-top: 16px;
            color: #6b7280;
            font-size: 0.7rem;
            line-height: 1.62;
        }

        @media (max-width: 1080px) {
            .login-stage {
                grid-template-columns: 1fr;
            }

            .feature-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 720px) {
            .login-shell {
                padding: 16px;
            }

            .showcase,
            .auth-card {
                border-radius: 28px;
                padding: 22px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .auth-inner {
                width: 100%;
            }

            .field-foot {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <div class="login-stage">
            <section class="showcase">
                <p class="eyebrow">{{ $loginBrandName }}</p>
                <span class="showcase-lead">Akses internal yang aman dan rapi</span>
                <h1>{{ $loginWelcomeMessage }}</h1>
                <p>
                    Masuk ke {{ $loginBrandName }} untuk memantau transaksi pasien, pengeluaran, dan laporan
                    harian dalam satu alur kerja yang tenang, ringkas, dan profesional.
                </p>

                <div class="feature-grid">
                    <article class="feature-card">
                        <strong>Operasional Harian</strong>
                        <span>Dashboard, transaksi, dan pengeluaran tertata dalam satu ruang kerja.</span>
                    </article>
                    <article class="feature-card">
                        <strong>Data Lebih Konsisten</strong>
                        <span>Master layanan dan kategori tetap sinkron untuk kebutuhan laporan.</span>
                    </article>
                    <article class="feature-card">
                        <strong>Siap Rekap</strong>
                        <span>Data yang masuk langsung siap dibaca pada rekap bulanan dan tahunan.</span>
                    </article>
                </div>

                <div class="showcase-note">
                    <strong>{{ $loginBrandCaption }}</strong>
                    <span>Dirancang untuk penggunaan internal, dengan pengalaman login yang lebih tenang, lebih matang, dan terasa siap produksi.</span>
                </div>

                <div class="showcase-footer">
                    <span class="showcase-chip">Akses internal</span>
                    <span class="showcase-chip">Input lebih tertata</span>
                    <span class="showcase-chip">Laporan lebih siap dibaca</span>
                </div>
            </section>

            <section class="auth-card">
                <div class="auth-inner">
                    <div class="brand-row">
                        <div class="brand-mark" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M8.5 5.5a3.5 3.5 0 0 1 5 0l4 4a3.5 3.5 0 1 1-5 5l-4-4a3.5 3.5 0 0 1 0-5Z"></path>
                                <path d="M10 10l4 4"></path>
                                <path d="M19 5v4"></path>
                                <path d="M17 7h4"></path>
                            </svg>
                        </div>
                        <div class="brand-copy">
                            <strong>{{ $loginBrandName }}</strong>
                            <span>{{ $loginBrandCaption }}</span>
                        </div>
                    </div>

                    <span class="auth-kicker">Login Internal</span>
                    <h2>Masuk ke Ruang Kerja</h2>
                    <p class="auth-intro">Gunakan akun aktif Anda untuk melanjutkan pekerjaan operasional dan membuka akses ke sistem laporan klinik.</p>

                    @if (session('status'))
                        <div class="message success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="message error">{{ $errors->first('login') ?: $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.attempt') }}" class="auth-form">
                        @csrf

                        <div class="field-group">
                            <label for="login">Username atau Email</label>
                            <input
                                id="login"
                                type="text"
                                name="login"
                                value="{{ old('login') }}"
                                placeholder="Masukkan username atau email"
                                autocomplete="username"
                                required
                            >
                        </div>

                        <div class="field-group">
                            <label for="password">Password</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <div class="field-foot">
                            <label class="remember">
                                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                <span>Ingat sesi login saya</span>
                            </label>
                        </div>

                        <button type="submit" class="submit-btn">Masuk ke Sistem</button>
                    </form>

                    <p class="helper-text">
                        Halaman ini ditujukan untuk penggunaan internal. Pastikan akun yang dipakai sudah aktif dan sesuai dengan peran kerja Anda.
                    </p>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
