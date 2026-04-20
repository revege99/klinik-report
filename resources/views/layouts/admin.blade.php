<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Klink Report')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:600,700|sora:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif

        <style>
            :root {
                --shell-bg: #eef3f9;
                --ink: #10233d;
                --muted: #667085;
                --panel: rgba(255, 255, 255, 0.92);
                --border: rgba(22, 44, 74, 0.08);
                --teal: #0f766e;
                --teal-soft: rgba(15, 118, 110, 0.12);
                --amber: #b45309;
                --amber-soft: rgba(217, 119, 6, 0.12);
                --slate-soft: rgba(15, 23, 42, 0.08);
                --sidebar: #0e1830;
                --sidebar-top: #16274b;
                --sidebar-border: rgba(148, 163, 184, 0.12);
                --sidebar-text: #e6eefc;
                --sidebar-muted: rgba(230, 238, 252, 0.64);
                --sidebar-active: #2d7ff9;
                --sidebar-active-strong: #1d66e5;
                --sidebar-active-glow: rgba(45, 127, 249, 0.28);
            }

            body {
                font-family: "Sora", sans-serif;
                color: var(--ink);
                background:
                    radial-gradient(circle at top left, rgba(45, 127, 249, 0.16), transparent 26%),
                    radial-gradient(circle at bottom right, rgba(15, 118, 110, 0.12), transparent 22%),
                    linear-gradient(180deg, #f8fbff 0%, #eef3f9 58%, #e8eef6 100%);
            }

            .font-display {
                font-family: "Fraunces", serif;
            }

            .shell-grid::before {
                content: "";
                position: fixed;
                inset: 0;
                pointer-events: none;
                background-image:
                    linear-gradient(rgba(16, 35, 61, 0.025) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(16, 35, 61, 0.025) 1px, transparent 1px);
                background-size: 38px 38px;
                mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.7), transparent 88%);
            }

            .panel {
                border: 1px solid var(--border);
                background: var(--panel);
                backdrop-filter: blur(18px);
                box-shadow: 0 22px 60px rgba(16, 35, 61, 0.07);
            }

            .app-sidebar {
                position: relative;
                overflow: hidden;
                border: 1px solid var(--sidebar-border);
                background: linear-gradient(180deg, var(--sidebar-top) 0%, var(--sidebar) 100%);
                color: var(--sidebar-text);
                box-shadow: 0 28px 70px rgba(13, 24, 48, 0.28);
            }

            .app-sidebar::before {
                content: "";
                position: absolute;
                inset: 0 0 auto 0;
                height: 180px;
                pointer-events: none;
                background:
                    radial-gradient(circle at top left, rgba(136, 180, 255, 0.3), transparent 48%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.04), transparent);
            }

            .sidebar-brand {
                display: flex;
                align-items: center;
                gap: 0.95rem;
                padding: 1rem;
                border-radius: 1.4rem;
                border: 1px solid rgba(255, 255, 255, 0.08);
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.04));
                backdrop-filter: blur(14px);
            }

            .sidebar-logo {
                display: inline-flex;
                height: 3.5rem;
                width: 3.5rem;
                flex-shrink: 0;
                align-items: center;
                justify-content: center;
                border-radius: 1.15rem;
                background: radial-gradient(circle at 30% 30%, #9fc6ff 0%, #58a3ff 35%, #215fdd 100%);
                box-shadow:
                    inset 0 0 0 1px rgba(255, 255, 255, 0.18),
                    0 16px 30px rgba(33, 95, 221, 0.3);
                color: white;
                font-size: 1.05rem;
                font-weight: 700;
                letter-spacing: 0.18em;
            }

            .sidebar-kicker,
            .sidebar-section-title,
            .sidebar-mini-label {
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.28em;
                text-transform: uppercase;
                color: var(--sidebar-muted);
            }

            .sidebar-brand h1 {
                font-size: 1.02rem;
                font-weight: 700;
                color: white;
            }

            .sidebar-small {
                margin-top: 0.2rem;
                font-size: 0.8rem;
                line-height: 1.55;
                color: var(--sidebar-muted);
            }

            .sidebar-menu {
                margin-top: 0.9rem;
                display: grid;
                gap: 0.5rem;
            }

            .nav-link {
                display: flex;
                align-items: center;
                gap: 0.9rem;
                padding: 0.9rem 1rem;
                border-radius: 1rem;
                border: 1px solid transparent;
                color: var(--sidebar-text);
                transition: 180ms ease;
            }

            .nav-link:hover {
                border-color: rgba(255, 255, 255, 0.06);
                background: rgba(255, 255, 255, 0.06);
                transform: translateX(4px);
            }

            .nav-link.is-active {
                border-color: rgba(188, 220, 255, 0.24);
                background: linear-gradient(135deg, var(--sidebar-active), var(--sidebar-active-strong));
                color: white;
                box-shadow: 0 18px 32px var(--sidebar-active-glow);
            }

            .nav-link-icon {
                display: inline-flex;
                height: 2.75rem;
                width: 2.75rem;
                flex-shrink: 0;
                align-items: center;
                justify-content: center;
                border-radius: 0.95rem;
                border: 1px solid rgba(255, 255, 255, 0.06);
                background: rgba(255, 255, 255, 0.07);
            }

            .nav-link-icon svg {
                height: 1.1rem;
                width: 1.1rem;
                fill: none;
                stroke: currentColor;
                stroke-linecap: round;
                stroke-linejoin: round;
                stroke-width: 1.8;
            }

            .nav-link.is-active .nav-link-icon {
                border-color: rgba(255, 255, 255, 0.12);
                background: rgba(255, 255, 255, 0.14);
            }

            .nav-link-text {
                display: block;
                font-size: 0.94rem;
                font-weight: 600;
                color: inherit;
            }

            .nav-link-subtitle {
                display: block;
                margin-top: 0.18rem;
                font-size: 0.74rem;
                line-height: 1.45;
                color: var(--sidebar-muted);
            }

            .nav-link.is-active .nav-link-subtitle {
                color: rgba(255, 255, 255, 0.86);
            }

            .sidebar-mini-card,
            .sidebar-footer {
                border-radius: 1.3rem;
                border: 1px solid rgba(255, 255, 255, 0.08);
                background: rgba(255, 255, 255, 0.05);
                padding: 1rem;
                backdrop-filter: blur(12px);
            }

            .sidebar-db-row {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                margin-top: 0.8rem;
                font-size: 0.82rem;
                color: var(--sidebar-muted);
            }

            .sidebar-db-row strong {
                color: white;
                font-weight: 600;
                text-align: right;
            }

            .sidebar-footer strong {
                display: block;
                margin-top: 0.55rem;
                font-size: 0.98rem;
                color: white;
            }

            .sidebar-footer span {
                display: block;
                margin-top: 0.4rem;
                font-size: 0.82rem;
                line-height: 1.6;
                color: var(--sidebar-muted);
            }

            .metric-glow {
                position: relative;
                overflow: hidden;
            }

            .metric-glow::after {
                content: "";
                position: absolute;
                inset: auto -30% -55% auto;
                width: 180px;
                height: 180px;
                border-radius: 999px;
                background: radial-gradient(circle, rgba(15, 118, 110, 0.2), transparent 70%);
                pointer-events: none;
            }

            .fade-up {
                animation: fadeUp 500ms ease both;
            }

            @keyframes fadeUp {
                from {
                    opacity: 0;
                    transform: translateY(14px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @media (max-width: 640px) {
                .nav-link-subtitle {
                    display: none;
                }

                .sidebar-brand {
                    padding: 0.9rem;
                }

                .sidebar-logo {
                    height: 3rem;
                    width: 3rem;
                    border-radius: 1rem;
                    font-size: 0.95rem;
                }
            }
        </style>
    </head>
    <body class="shell-grid min-h-screen">
        @php
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'dashboard', 'meta' => 'Ringkasan operasional'],
                ['label' => 'Input Data', 'route' => 'input-data', 'match' => 'input-data', 'icon' => 'clipboard', 'meta' => 'Form rekap pasien'],
                ['label' => 'Input Pengeluaran', 'route' => 'input-pengeluaran', 'match' => 'input-pengeluaran', 'icon' => 'wallet', 'meta' => 'Catat biaya klinik'],
                ['label' => 'Rekap Bulanan', 'route' => 'rekap-bulanan', 'match' => 'rekap-bulanan', 'icon' => 'calendar', 'meta' => 'Evaluasi per bulan'],
                ['label' => 'Rekap Tahunan', 'route' => 'rekap-tahunan', 'match' => 'rekap-tahunan', 'icon' => 'chart', 'meta' => 'Tren satu tahun'],
            ];
            $appDatabase = config('database.connections.'.config('database.default').'.database');
            $simrsDatabase = config('database.connections.simrs.database');
        @endphp

        <div class="mx-auto min-h-screen max-w-[1600px] px-4 py-4 sm:px-6 lg:px-8">
            <div class="grid min-h-[calc(100vh-2rem)] gap-4 xl:grid-cols-[290px_1fr]">
                <aside class="app-sidebar fade-up rounded-[2rem] p-5 xl:sticky xl:top-4 xl:h-[calc(100vh-2rem)]">
                    <div class="flex h-full flex-col">
                        <div class="sidebar-brand">
                            <div class="sidebar-logo">
                                KR
                            </div>
                            <div class="min-w-0">
                                <p class="sidebar-kicker">Klink Report</p>
                                <h1>Administrasi Klinik</h1>
                                <p class="sidebar-small">UI sidebar bergaya panel gelap dengan active menu biru seperti referensi.</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="sidebar-section-title">Menu Utama</p>
                            <nav class="sidebar-menu">
                                @foreach ($navItems as $item)
                                    <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['match']) ? 'is-active' : '' }}">
                                        <span class="nav-link-icon" aria-hidden="true">
                                            @switch($item['icon'])
                                                @case('dashboard')
                                                    <svg viewBox="0 0 24 24">
                                                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                                                        <rect x="14" y="3" width="7" height="5" rx="1.5"></rect>
                                                        <rect x="14" y="12" width="7" height="9" rx="1.5"></rect>
                                                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                                                    </svg>
                                                    @break
                                                @case('clipboard')
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M9 4h6"></path>
                                                        <path d="M10 2h4a1 1 0 0 1 1 1v2H9V3a1 1 0 0 1 1-1Z"></path>
                                                        <path d="M8 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2"></path>
                                                        <path d="M8 11h8"></path>
                                                        <path d="M8 15h5"></path>
                                                    </svg>
                                                    @break
                                                @case('wallet')
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M4 7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2"></path>
                                                        <path d="M4 7h14a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z"></path>
                                                        <path d="M16 12h4"></path>
                                                        <circle cx="16" cy="12" r="1"></circle>
                                                    </svg>
                                                    @break
                                                @case('calendar')
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M8 2v4"></path>
                                                        <path d="M16 2v4"></path>
                                                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                                        <path d="M3 10h18"></path>
                                                        <path d="M8 14h3"></path>
                                                        <path d="M13 14h3"></path>
                                                    </svg>
                                                    @break
                                                @default
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M4 19h16"></path>
                                                        <path d="M7 16V9"></path>
                                                        <path d="M12 16V5"></path>
                                                        <path d="M17 16v-4"></path>
                                                    </svg>
                                            @endswitch
                                        </span>
                                        <span class="min-w-0">
                                            <span class="nav-link-text">{{ $item['label'] }}</span>
                                            <span class="nav-link-subtitle">{{ $item['meta'] }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </nav>
                        </div>

                        <div class="sidebar-mini-card mt-6">
                            <p class="sidebar-mini-label">Database Aktif</p>
                            <div class="sidebar-db-row">
                                <span>Aplikasi</span>
                                <strong>{{ $appDatabase }}</strong>
                            </div>
                            <div class="sidebar-db-row">
                                <span>SIMRS</span>
                                <strong>{{ $simrsDatabase }}</strong>
                            </div>
                        </div>

                        <div class="sidebar-footer mt-auto">
                            <p class="sidebar-mini-label">Mode Sistem</p>
                            <strong>Single Clinic Local</strong>
                            <span>Menu tetap sama. Yang berubah hanya UI sidebar agar lebih dekat dengan referensi yang Anda kirim.</span>
                        </div>
                    </div>
                </aside>

                <main class="fade-up space-y-4">
                    <section class="panel rounded-[2rem] px-6 py-6 sm:px-8">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">@yield('eyebrow', 'Klink Report')</p>
                                <h2 class="font-display mt-3 text-3xl text-slate-950 sm:text-4xl">@yield('page-title')</h2>
                                <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600 sm:text-base">
                                    @yield('page-intro')
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @yield('header-actions')
                            </div>
                        </div>
                    </section>

                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
