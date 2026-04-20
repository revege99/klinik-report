<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Klink Report</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
            padding: 28px;
        }

        .login-stage {
            display: grid;
            grid-template-columns: minmax(320px, 1.08fr) minmax(380px, 0.92fr);
            gap: 22px;
            align-items: stretch;
        }

        .showcase,
        .auth-card {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 34px;
            background: var(--panel);
            box-shadow: 0 28px 60px rgba(15, 23, 42, 0.1);
            backdrop-filter: blur(18px);
        }

        .showcase {
            padding: 34px;
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
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }

        .showcase h1 {
            margin: 14px 0 12px;
            max-width: 12ch;
            font-size: clamp(2.4rem, 4vw, 4.1rem);
            line-height: 0.98;
        }

        .showcase p {
            max-width: 58ch;
            margin: 0;
            color: var(--muted);
            font-size: 0.96rem;
            line-height: 1.85;
        }

        .feature-grid {
            display: grid;
            gap: 14px;
            margin-top: 26px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .feature-card {
            padding: 16px 16px 14px;
            border-radius: 24px;
            border: 1px solid rgba(37, 99, 235, 0.12);
            background: rgba(255, 255, 255, 0.74);
        }

        .feature-card strong {
            display: block;
            color: var(--ink);
            font-size: 0.92rem;
        }

        .feature-card span {
            display: block;
            margin-top: 8px;
            color: var(--muted);
            font-size: 0.78rem;
            line-height: 1.7;
        }

        .showcase-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .showcase-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.76);
            border: 1px solid rgba(37, 99, 235, 0.12);
            color: #1f3a5f;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .auth-card {
            display: grid;
            align-content: center;
            padding: 34px;
        }

        .brand-row {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .brand-mark {
            display: inline-flex;
            height: 46px;
            width: 46px;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: radial-gradient(circle at 30% 30%, #a8ccff 0%, #5ba7ff 38%, #1d5ee0 100%);
            box-shadow: 0 16px 28px rgba(29, 94, 224, 0.22);
        }

        .brand-mark svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: white;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.9;
        }

        .brand-copy strong {
            display: block;
            color: var(--ink);
            font-size: 1.08rem;
            line-height: 1.2;
        }

        .brand-copy span {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 0.78rem;
        }

        .auth-card h2 {
            margin: 0;
            color: var(--ink);
            font-size: 1.75rem;
            line-height: 1.15;
        }

        .auth-card > p {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 0.86rem;
            line-height: 1.8;
        }

        .message {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 18px;
            font-size: 0.82rem;
            line-height: 1.7;
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
            gap: 16px;
            margin-top: 24px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .field-group label {
            color: #334155;
            font-size: 0.73rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .field-group input {
            height: 48px;
            width: 100%;
            border: 1px solid #d7e1ef;
            border-radius: 16px;
            padding: 12px 14px;
            background: #f8fafc;
            color: #10233d;
            font-size: 0.92rem;
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
            gap: 10px;
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .remember input {
            width: 16px;
            height: 16px;
            margin: 0;
        }

        .submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            height: 50px;
            border: none;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: white;
            font-size: 0.94rem;
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
            margin-top: 18px;
            color: #6b7280;
            font-size: 0.76rem;
            line-height: 1.7;
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
                <p class="eyebrow">Klink Report</p>
                <h1>Login yang rapi untuk kerja yang tenang.</h1>
                <p>
                    Masuk ke panel admin untuk memantau transaksi pasien, pengeluaran, dan laporan
                    dengan tampilan yang lebih terstruktur, ringan, dan profesional.
                </p>

                <div class="feature-grid">
                    <article class="feature-card">
                        <strong>Kontrol Operasional</strong>
                        <span>Lihat dashboard, input transaksi, dan kredit pengeluaran dalam satu alur kerja.</span>
                    </article>
                    <article class="feature-card">
                        <strong>Mapping Lebih Rapi</strong>
                        <span>Master layanan dan kategori pengeluaran tetap sinkron untuk kebutuhan laporan.</span>
                    </article>
                    <article class="feature-card">
                        <strong>Siap Rekap</strong>
                        <span>Data yang sudah diinput langsung bisa dibaca oleh rekap bulanan maupun tahunan.</span>
                    </article>
                    <article class="feature-card">
                        <strong>Jejak Pengguna</strong>
                        <span>Input transaksi dan pengeluaran bisa langsung dikaitkan dengan user yang login.</span>
                    </article>
                </div>

                <div class="showcase-footer">
                    <span class="showcase-chip">Dashboard profesional</span>
                    <span class="showcase-chip">Input lebih tertata</span>
                    <span class="showcase-chip">Laporan lebih siap audit</span>
                </div>
            </section>

            <section class="auth-card">
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
                        <strong>Panel Admin Klinik</strong>
                        <span>Masuk ke sistem laporan internal</span>
                    </div>
                </div>

                <h2>Selamat datang kembali</h2>
                <p>Masukkan username atau email, lalu lanjutkan dengan password untuk membuka panel kerja Anda.</p>

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

                    <button type="submit" class="submit-btn">Masuk ke Panel</button>
                </form>

                <p class="helper-text">
                    Halaman ini dirancang untuk penggunaan internal. Pastikan akun yang dipakai memang sudah diaktifkan oleh administrator sistem.
                </p>
            </section>
        </div>
    </div>
</body>
</html>
