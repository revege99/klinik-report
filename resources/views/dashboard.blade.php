@extends('layouts.app')

@section('title', 'Dashboard | Klink Report')

@section('content')
<style>
    .dashboard-shell {
        display: grid;
        gap: 20px;
    }

    .dashboard-shell > * {
        min-width: 0;
    }

    .hero-panel,
    .metric-card,
    .mini-card,
    .dashboard-panel {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.88);
        box-shadow: 0 24px 54px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(18px);
    }

    .hero-panel {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.9fr);
        gap: 20px;
        padding: 24px;
        background:
            radial-gradient(circle at top right, rgba(37, 99, 235, 0.16), transparent 28%),
            radial-gradient(circle at bottom left, rgba(20, 184, 166, 0.14), transparent 26%),
            linear-gradient(145deg, rgba(255, 255, 255, 0.94), rgba(242, 248, 255, 0.9));
    }

    .hero-panel::before {
        content: "";
        position: absolute;
        inset: auto -40px -52px auto;
        width: 220px;
        height: 220px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.18), transparent 68%);
        pointer-events: none;
    }

    .hero-copy {
        position: relative;
        z-index: 1;
    }

    .eyebrow {
        margin: 0;
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.2em;
        text-transform: uppercase;
    }

    .hero-copy h1 {
        margin: 8px 0 10px;
        color: #12233b;
        font-size: clamp(1.85rem, 3vw, 2.7rem);
        line-height: 1.05;
    }

    .hero-copy p {
        max-width: 62ch;
        margin: 0;
        color: #5b6b82;
        font-size: 0.94rem;
        line-height: 1.8;
    }

    .hero-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .hero-filter {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 10px;
        margin-top: 18px;
    }

    .hero-filter-field {
        display: flex;
        min-width: 220px;
        flex-direction: column;
        gap: 6px;
    }

    .hero-filter-field label {
        color: #334155;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .hero-filter-field select {
        min-height: 44px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 16px;
        padding: 10px 14px;
        background: rgba(255, 255, 255, 0.84);
        color: #12233b;
    }

    .hero-greeting {
        margin-top: 18px;
        padding: 16px 18px;
        border-radius: 24px;
        border: 1px solid rgba(37, 99, 235, 0.14);
        background: linear-gradient(135deg, rgba(239, 246, 255, 0.96), rgba(224, 242, 254, 0.92));
        box-shadow: 0 16px 32px rgba(37, 99, 235, 0.08);
    }

    .hero-greeting strong {
        display: block;
        color: #13335a;
        font-size: 0.96rem;
        line-height: 1.45;
    }

    .hero-greeting p {
        max-width: none;
        margin: 8px 0 0;
        color: #55708d;
        font-size: 0.82rem;
        line-height: 1.8;
    }

    .hero-greeting span {
        display: inline-flex;
        align-items: center;
        margin-top: 10px;
        padding: 7px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.72);
        color: #1d4ed8;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        border: 1px solid rgba(37, 99, 235, 0.12);
        background: rgba(255, 255, 255, 0.72);
        color: #1f3b64;
        font-size: 0.78rem;
        font-weight: 700;
        box-shadow: 0 12px 28px rgba(37, 99, 235, 0.08);
    }

    .hero-side {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 14px;
    }

    .focus-card {
        padding: 18px 18px 16px;
        border-radius: 24px;
        border: 1px solid rgba(37, 99, 235, 0.12);
        background: linear-gradient(180deg, rgba(249, 252, 255, 0.96), rgba(234, 244, 255, 0.92));
    }

    .focus-card.is-cash {
        border-color: rgba(16, 185, 129, 0.14);
        background: linear-gradient(180deg, rgba(245, 255, 251, 0.98), rgba(228, 250, 242, 0.94));
    }

    .focus-card span {
        display: block;
        color: #5f738c;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .focus-card strong {
        display: block;
        margin-top: 8px;
        color: #12233b;
        font-size: 1.55rem;
        line-height: 1.05;
    }

    .focus-card p,
    .focus-card small {
        display: block;
        margin: 8px 0 0;
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.65;
    }

    .focus-bar {
        position: relative;
        height: 10px;
        margin-top: 12px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(191, 219, 254, 0.54);
    }

    .focus-bar span {
        position: absolute;
        inset: 0 auto 0 0;
        width: var(--progress, 0%);
        max-width: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #2563eb, #38bdf8);
    }

    .focus-card.is-cash .focus-bar {
        background: rgba(167, 243, 208, 0.56);
    }

    .focus-card.is-cash .focus-bar span {
        background: linear-gradient(90deg, #10b981, #34d399);
    }

    .metrics-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .metric-card {
        padding: 20px;
    }

    .metric-card::after {
        content: "";
        position: absolute;
        top: -36px;
        right: -18px;
        width: 110px;
        height: 110px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.16), transparent 70%);
        pointer-events: none;
    }

    .metric-card.is-expense::after {
        background: radial-gradient(circle, rgba(244, 114, 182, 0.16), transparent 70%);
    }

    .metric-card.is-balance::after {
        background: radial-gradient(circle, rgba(16, 185, 129, 0.16), transparent 70%);
    }

    .metric-card.is-volume::after {
        background: radial-gradient(circle, rgba(245, 158, 11, 0.18), transparent 70%);
    }

    .metric-card span {
        position: relative;
        z-index: 1;
        display: block;
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .metric-card strong {
        position: relative;
        z-index: 1;
        display: block;
        margin-top: 10px;
        color: #13263f;
        font-size: 1.4rem;
        line-height: 1.1;
    }

    .metric-card p {
        position: relative;
        z-index: 1;
        margin: 8px 0 0;
        color: #66768d;
        font-size: 0.8rem;
        line-height: 1.7;
    }

    .mini-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .mini-card {
        padding: 16px 18px;
    }

    .mini-card span {
        display: block;
        color: #66768d;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .mini-card strong {
        display: block;
        margin-top: 6px;
        color: #12233b;
        font-size: 1.04rem;
        line-height: 1.15;
    }

    .mini-card p {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.65;
    }

    .board-grid,
    .activity-grid {
        display: grid;
        gap: 18px;
        grid-template-columns: minmax(0, 1.35fr) minmax(340px, 0.95fr);
    }

    .activity-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-panel {
        padding: 20px;
    }

    .panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .panel-head h2,
    .panel-head h3 {
        margin: 0;
        color: #17263d;
        font-size: 1.02rem;
        line-height: 1.2;
    }

    .panel-head p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.65;
    }

    .panel-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .trend-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
    }

    .legend-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #5f738c;
        font-size: 0.73rem;
        font-weight: 700;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
    }

    .legend-dot.is-revenue {
        background: linear-gradient(180deg, #2563eb, #38bdf8);
    }

    .legend-dot.is-expense {
        background: linear-gradient(180deg, #f97316, #fb7185);
    }

    .trend-chart {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 12px;
        min-height: 280px;
        align-items: end;
    }

    .trend-item {
        display: grid;
        gap: 10px;
    }

    .trend-bars {
        display: flex;
        align-items: end;
        justify-content: center;
        gap: 8px;
        min-height: 188px;
        padding: 0 4px;
    }

    .trend-bar {
        width: 22px;
        min-height: 10px;
        border-radius: 14px 14px 6px 6px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
    }

    .trend-bar.is-revenue {
        background: linear-gradient(180deg, #60a5fa, #2563eb);
        box-shadow: 0 16px 28px rgba(37, 99, 235, 0.18);
    }

    .trend-bar.is-expense {
        background: linear-gradient(180deg, #fb923c, #f43f5e);
        box-shadow: 0 16px 28px rgba(244, 63, 94, 0.16);
    }

    .trend-caption {
        text-align: center;
    }

    .trend-caption strong {
        display: block;
        color: #16283f;
        font-size: 0.78rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .trend-caption span {
        display: block;
        margin-top: 4px;
        color: #73839a;
        font-size: 0.72rem;
    }

    .trend-net {
        margin-top: 8px;
        text-align: center;
        color: #0f766e;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .trend-net.is-negative {
        color: #c2410c;
    }

    .service-stack,
    .mix-stack,
    .quick-stack,
    .activity-list {
        display: grid;
        gap: 12px;
    }

    .service-card {
        padding: 14px 14px 12px;
        border-radius: 20px;
        border: 1px solid rgba(148, 163, 184, 0.14);
        background: linear-gradient(180deg, rgba(249, 252, 255, 0.96), rgba(244, 248, 253, 0.92));
    }

    .service-card.is-blue { --service-start: #2563eb; --service-end: #38bdf8; }
    .service-card.is-emerald { --service-start: #059669; --service-end: #34d399; }
    .service-card.is-amber { --service-start: #d97706; --service-end: #fbbf24; }
    .service-card.is-sky { --service-start: #0284c7; --service-end: #38bdf8; }
    .service-card.is-violet { --service-start: #7c3aed; --service-end: #a78bfa; }
    .service-card.is-slate { --service-start: #334155; --service-end: #94a3b8; }

    .service-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .service-code {
        display: inline-flex;
        align-items: center;
        padding: 5px 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.72);
        color: #23406b;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .service-head strong {
        display: block;
        margin-top: 8px;
        color: #17263d;
        font-size: 0.92rem;
        line-height: 1.35;
    }

    .service-amount {
        color: #11253c;
        font-size: 0.84rem;
        font-weight: 800;
        text-align: right;
        white-space: nowrap;
    }

    .service-bar {
        position: relative;
        height: 10px;
        margin-top: 12px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(226, 232, 240, 0.92);
    }

    .service-bar span {
        position: absolute;
        inset: 0 auto 0 0;
        width: var(--progress, 0%);
        max-width: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--service-start), var(--service-end));
    }

    .service-note {
        margin-top: 8px;
        color: #64748b;
        font-size: 0.75rem;
    }

    .mix-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(248, 251, 255, 0.94), rgba(241, 245, 249, 0.96));
        border: 1px solid rgba(148, 163, 184, 0.12);
    }

    .mix-card strong {
        display: block;
        color: #17263d;
        font-size: 0.88rem;
        line-height: 1.3;
    }

    .mix-card span {
        display: block;
        margin-top: 4px;
        color: #66768d;
        font-size: 0.74rem;
    }

    .mix-value {
        text-align: right;
        white-space: nowrap;
    }

    .mix-value strong {
        color: #0f766e;
        font-size: 0.85rem;
    }

    .mix-value span {
        color: #64748b;
    }

    .quick-action {
        display: block;
        padding: 16px 16px 14px;
        border-radius: 22px;
        text-decoration: none;
        border: 1px solid rgba(148, 163, 184, 0.14);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(244, 248, 252, 0.92));
        box-shadow: 0 16px 28px rgba(15, 23, 42, 0.05);
        transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
    }

    .quick-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.08);
    }

    .quick-action strong {
        display: block;
        color: #11253c;
        font-size: 0.9rem;
    }

    .quick-action span {
        display: block;
        margin-top: 8px;
        color: #64748b;
        font-size: 0.76rem;
        line-height: 1.65;
    }

    .quick-action.is-blue:hover { border-color: rgba(37, 99, 235, 0.24); }
    .quick-action.is-emerald:hover { border-color: rgba(16, 185, 129, 0.24); }
    .quick-action.is-amber:hover { border-color: rgba(245, 158, 11, 0.24); }
    .quick-action.is-violet:hover { border-color: rgba(124, 58, 237, 0.24); }

    .activity-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid rgba(226, 232, 240, 0.74);
    }

    .activity-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .activity-item strong {
        display: block;
        color: #17263d;
        font-size: 0.88rem;
        line-height: 1.35;
    }

    .activity-item span {
        display: block;
        margin-top: 5px;
        color: #66768d;
        font-size: 0.74rem;
        line-height: 1.6;
    }

    .activity-amount {
        color: #0f766e;
        font-size: 0.84rem;
        font-weight: 800;
        white-space: nowrap;
        text-align: right;
    }

    .activity-amount.is-out {
        color: #c2410c;
    }

    .empty-state {
        padding: 18px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(241, 245, 249, 0.98));
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.75;
        text-align: center;
    }

    @media (max-width: 1280px) {
        .metrics-grid,
        .mini-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .board-grid,
        .activity-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 980px) {
        .hero-panel {
            grid-template-columns: 1fr;
        }

        .hero-side {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 768px) {
        .hero-panel,
        .metric-card,
        .mini-card,
        .dashboard-panel {
            border-radius: 24px;
        }

        .hero-panel,
        .dashboard-panel {
            padding: 18px;
        }

        .metrics-grid,
        .mini-grid,
        .hero-side {
            grid-template-columns: 1fr;
        }

        .trend-chart {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
</style>

@php
    $formatCurrency = function ($value) {
        $value = (float) $value;

        if (abs($value) < 0.01) {
            return 'Rp 0';
        }

        if ($value < 0) {
            return '-Rp ' . number_format(abs($value), 0, ',', '.');
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    };

    $masterNeutralMode = $showClinicFilter && ! $viewingAllClinics && blank($sharedClinicProfile);
    $clinicBrandTitle = $masterNeutralMode
        ? 'Dashboard Pusat'
        : ($sharedClinicProfile?->nama_pendek ?: $sharedClinicProfile?->nama_klinik ?: config('app.name', 'Klink Report'));
    $clinicBrandName = $masterNeutralMode
        ? 'Multi Klinik'
        : ($sharedClinicProfile?->nama_klinik ?: 'Klinik');
    $dashboardHeroTitle = ($viewingAllClinics || $masterNeutralMode)
        ? 'Pusat Kendali Operasional Multi Klinik'
        : 'Pusat Kendali Operasional ' . $clinicBrandName;
    $clinicBrandDescription = $masterNeutralMode
        ? 'Mulai dari dashboard pusat terlebih dahulu, lalu pilih klinik saat Anda ingin memfokuskan transaksi, pengeluaran, dan laporan pada unit tertentu.'
        : ($sharedClinicProfile?->deskripsi_singkat
            ?: 'Pantau penerimaan, pengeluaran, kualitas mapping layanan, dan aktivitas input harian dalam satu tampilan yang rapi, ringkas, dan siap dipakai untuk operasional klinik.');
@endphp

<div class="dashboard-shell">
    <section class="hero-panel">
        <div class="hero-copy">
            <p class="eyebrow">{{ $clinicBrandTitle }}</p>
            <h1>{{ $dashboardHeroTitle }}</h1>
            <p>{{ $clinicBrandDescription }}</p>

            @if ($showClinicFilter)
                <form method="GET" action="{{ route('dashboard') }}" class="hero-filter">
                    <div class="hero-filter-field">
                        <label for="dashboard-clinic-id">Filter Klinik</label>
                        <select id="dashboard-clinic-id" name="clinic_id">
                            <option value="" @selected($selectedClinicFilter === '')>Pilih Klinik</option>
                            <option value="all" @selected($selectedClinicFilter === 'all')>Semua Klinik</option>
                            @foreach ($clinicOptions as $clinicOption)
                                <option value="{{ $clinicOption->id }}" @selected($selectedClinicFilter === (string) $clinicOption->id)>
                                    {{ $clinicOption->kode_klinik }} · {{ $clinicOption->nama_klinik }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary filter-submit">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 6h16"></path>
                            <path d="M7 12h10"></path>
                            <path d="M10 18h4"></path>
                        </svg>
                        Filter Data
                    </button>
                </form>
            @endif

            <div class="hero-greeting">
                <strong>{{ $dashboardGreeting['title'] }}</strong>
                <p>{{ $dashboardGreeting['body'] }}</p>
                <span>{{ $dashboardGreeting['role'] }}</span>
            </div>

            <div class="hero-tags">
                @if (! $masterNeutralMode)
                    <span class="hero-tag">{{ $selectedClinicLabel }}</span>
                @endif
                <span class="hero-tag">{{ $todayLabel }}</span>
                <span class="hero-tag">{{ $monthLabel }}</span>
                <span class="hero-tag">{{ $monthlyTransactionCount }} transaksi pasien bulan ini</span>
            </div>
        </div>

        <div class="hero-side">
            <article class="focus-card" style="--progress: {{ max($mappedPercentage, 6) }}%">
                <span>Kesehatan Mapping</span>
                <strong>{{ number_format($mappedPercentage, 0, ',', '.') }}%</strong>
                <p>Transaksi bulan ini sudah terhubung ke master layanan dan siap dipakai untuk rekap.</p>
                <div class="focus-bar"><span></span></div>
                <small>{{ $unmappedTransactionCount }} transaksi masih perlu dicek mapping layanannya.</small>
            </article>

            <article class="focus-card is-cash" style="--progress: {{ $monthlyRevenue > 0 ? max(min(($monthlyNet / $monthlyRevenue) * 100, 100), 6) : 6 }}%">
                <span>Saldo Bulan Berjalan</span>
                <strong>{{ $formatCurrency($monthlyNet) }}</strong>
                <p>Selisih penerimaan dan pengeluaran untuk periode {{ $currentMonthName }}.</p>
                <div class="focus-bar"><span></span></div>
                <small>{{ $formatCurrency($monthlyExpenseTotal) }} sudah keluar sebagai kredit bulan ini.</small>
            </article>
        </div>
    </section>

    <section class="metrics-grid">
        <article class="metric-card is-revenue">
            <span>Penerimaan Bulan Ini</span>
            <strong>{{ $formatCurrency($monthlyRevenue) }}</strong>
            <p>Total debet transaksi pasien lokal untuk periode {{ $currentMonthName }}.</p>
        </article>

        <article class="metric-card is-expense">
            <span>Pengeluaran Bulan Ini</span>
            <strong>{{ $formatCurrency($monthlyExpenseTotal) }}</strong>
            <p>Total kredit yang sudah diinput dari menu pengeluaran.</p>
        </article>

        <article class="metric-card is-balance">
            <span>Saldo Berjalan</span>
            <strong>{{ $formatCurrency($monthlyNet) }}</strong>
            <p>Nilai bersih yang siap dibawa ke ringkasan bulanan dan tahunan.</p>
        </article>

        <article class="metric-card is-volume">
            <span>Volume Transaksi</span>
            <strong>{{ number_format($monthlyTransactionCount, 0, ',', '.') }} Data</strong>
            <p>Jumlah baris transaksi pasien yang tersimpan untuk bulan aktif.</p>
        </article>
    </section>

    <section class="mini-grid">
        <article class="mini-card">
            <span>Hari Ini</span>
            <strong>{{ $formatCurrency($todayRevenue) }}</strong>
            <p>{{ $todayTransactionCount }} transaksi pasien tercatat pada {{ $todayLabel }}.</p>
        </article>

        <article class="mini-card">
            <span>Pengeluaran Hari Ini</span>
            <strong>{{ $formatCurrency($todayExpenseTotal) }}</strong>
            <p>{{ $todayExpenseCount }} input pengeluaran masuk untuk hari yang sama.</p>
        </article>

        <article class="mini-card">
            <span>Rata-rata Transaksi</span>
            <strong>{{ $formatCurrency($averageTransaction) }}</strong>
            <p>Nilai rata-rata per transaksi pasien pada bulan berjalan.</p>
        </article>

        <article class="mini-card">
            <span>Master Aktif</span>
            <strong>{{ $activeServiceCount }} layanan / {{ $activeExpenseCategoryCount }} kategori</strong>
            <p>Struktur master yang dipakai oleh transaksi pasien dan pengeluaran.</p>
        </article>
    </section>

    <div class="board-grid">
        <section class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h2>Tren 6 Bulan Terakhir</h2>
                    <p>Perbandingan debet transaksi pasien dan kredit pengeluaran agar kondisi kas lebih cepat terbaca.</p>
                </div>
                <span class="panel-badge">Ringkas & Visual</span>
            </div>

            <div class="trend-legend">
                <span class="legend-chip"><i class="legend-dot is-revenue"></i>Penerimaan</span>
                <span class="legend-chip"><i class="legend-dot is-expense"></i>Pengeluaran</span>
            </div>

            <div class="trend-chart">
                @foreach ($monthlyTrend as $item)
                    <article class="trend-item">
                        <div class="trend-bars">
                            <span class="trend-bar is-revenue" style="height: {{ max($item['revenue_ratio'], 8) }}%"></span>
                            <span class="trend-bar is-expense" style="height: {{ max($item['expense_ratio'], 8) }}%"></span>
                        </div>
                        <div class="trend-caption">
                            <strong>{{ $item['label'] }}</strong>
                            <span>{{ $formatCurrency($item['revenue']) }}</span>
                        </div>
                        <div class="trend-net {{ $item['net'] < 0 ? 'is-negative' : '' }}">
                            {{ $formatCurrency($item['net']) }}
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Top Layanan Bulan Ini</h3>
                    <p>Layanan dengan kontribusi penerimaan paling besar dari transaksi pasien lokal.</p>
                </div>
                <span class="panel-badge">Top {{ min($topLayanan->count(), 6) }}</span>
            </div>

            <div class="service-stack">
                @forelse ($topLayanan as $service)
                    <article class="service-card is-{{ $service['tone'] }}" style="--progress: {{ max($service['share'], 6) }}%">
                        <div class="service-head">
                            <div>
                                <span class="service-code">{{ $service['kode'] }}</span>
                                <strong>{{ $service['nama'] }}</strong>
                            </div>
                            <div class="service-amount">{{ $formatCurrency($service['amount']) }}</div>
                        </div>
                        <div class="service-bar"><span></span></div>
                        <div class="service-note">{{ number_format($service['share'], 1, ',', '.') }}% dari total penerimaan bulan ini.</div>
                    </article>
                @empty
                    <div class="empty-state">Belum ada penerimaan pada bulan berjalan, jadi daftar layanan unggulan masih kosong.</div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="board-grid">
        <section class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Komposisi Penjamin</h3>
                    <p>Distribusi penjamin pasien dari data transaksi lokal pada bulan aktif.</p>
                </div>
                <span class="panel-badge">{{ $penjaminCount }} kelompok</span>
            </div>

            <div class="mix-stack">
                @forelse ($penjaminMix as $item)
                    <article class="mix-card">
                        <div>
                            <strong>{{ $item['label'] }}</strong>
                            <span>{{ number_format($item['count'], 0, ',', '.') }} transaksi</span>
                        </div>
                        <div class="mix-value">
                            <strong>{{ $formatCurrency($item['amount']) }}</strong>
                            <span>{{ number_format($item['share'], 1, ',', '.') }}% dari debet bulan ini</span>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">Belum ada data penjamin pada transaksi bulan ini.</div>
                @endforelse
            </div>
        </section>

        <section class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Akses Cepat</h3>
                    <p>Langsung lompat ke modul yang paling sering dipakai saat input dan pengecekan laporan.</p>
                </div>
                <span class="panel-badge">Workflow</span>
            </div>

            <div class="quick-stack">
                @foreach ($quickActions as $action)
                    <a href="{{ $action['route'] }}" class="quick-action is-{{ $action['tone'] }}">
                        <strong>{{ $action['label'] }}</strong>
                        <span>{{ $action['description'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>

    <div class="activity-grid">
        <section class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Transaksi Pasien Terbaru</h3>
                    <p>Input lokal terakhir yang bisa langsung dipakai untuk pengecekan cepat.</p>
                </div>
                <span class="panel-badge">Debet</span>
            </div>

            <div class="activity-list">
                @forelse ($recentTransactions as $transaction)
                    <article class="activity-item">
                        <div>
                            <strong>{{ $transaction->nama_pasien ?: 'Pasien tanpa nama' }}</strong>
                            <span>
                                {{ $transaction->masterLayanan?->nama_layanan ?: ($transaction->layanan_medis ?: 'Belum Dimapping') }}
                                • {{ $transaction->tanggal?->locale('id')->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div class="activity-amount">{{ $formatCurrency($transaction->jumlah_rp) }}</div>
                    </article>
                @empty
                    <div class="empty-state">Belum ada transaksi pasien lokal yang tersimpan.</div>
                @endforelse
            </div>
        </section>

        <section class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Pengeluaran Terbaru</h3>
                    <p>Riwayat kredit terakhir untuk membantu membaca arus keluar secara cepat.</p>
                </div>
                <span class="panel-badge">Kredit</span>
            </div>

            <div class="activity-list">
                @forelse ($recentExpenses as $expense)
                    <article class="activity-item">
                        <div>
                            <strong>{{ $expense->deskripsi ?: 'Pengeluaran tanpa deskripsi' }}</strong>
                            <span>
                                {{ $expense->masterKategoriPengeluaran?->nama_kategori ?: ($expense->kategori_pengeluaran ?: 'Kategori belum dipilih') }}
                                • {{ $expense->tanggal?->locale('id')->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div class="activity-amount is-out">{{ $formatCurrency($expense->jumlah_rp) }}</div>
                    </article>
                @empty
                    <div class="empty-state">Belum ada data pengeluaran lokal yang tersimpan.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
