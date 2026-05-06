@extends('layouts.app')

@section('title', 'Rekap Bulanan | Klink Report')

@section('content')
<style>
    .rekap-shell {
        display: grid;
        gap: 18px;
    }

    .hero-card,
    .summary-grid,
    .table-card {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(16px);
    }

    .hero-card {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
        gap: 14px 16px;
        padding: 18px 20px;
    }

    .page-eyebrow {
        margin: 0;
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.2em;
        text-transform: uppercase;
    }

    .hero-copy h1 {
        margin: 6px 0 0;
        color: #10233d;
        font-size: 1.45rem;
        line-height: 1.1;
    }

    .hero-copy p {
        margin: 7px 0 0;
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.7;
    }

    .hero-stats {
        display: grid;
        min-width: 420px;
        gap: 8px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .hero-stat {
        border-radius: 18px;
        padding: 10px 12px;
        background: linear-gradient(180deg, #f8fbff, #eef4ff);
        border: 1px solid rgba(59, 130, 246, 0.12);
    }

    .hero-stat span,
    .summary-card span {
        display: block;
        color: #64748b;
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .hero-stat strong,
    .summary-card strong {
        display: block;
        margin-top: 5px;
        color: #1e293b;
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1.15;
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        gap: 10px;
        justify-content: flex-end;
    }

    .field-wrap {
        display: flex;
        min-width: 180px;
        flex: 0 0 180px;
        flex-direction: column;
        gap: 6px;
    }

    .field-wrap label {
        color: #334155;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .field-wrap input,
    .field-wrap select {
        height: 42px;
        width: 100%;
        border: 1px solid #d7e1ef;
        border-radius: 14px;
        padding: 10px 12px;
        background: #f8fafc;
        color: #10233d;
        font-size: 0.82rem;
        transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
    }

    .field-wrap input:focus,
    .field-wrap select:focus {
        outline: none;
        border-color: #60a5fa;
        background: white;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.16);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        border-radius: 14px;
        padding: 10px 14px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease;
    }

    .btn,
    .btn:link,
    .btn:visited {
        text-decoration: none;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.2);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        color: #10233d;
        border: 1px solid rgba(148, 163, 184, 0.3);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .summary-grid {
        display: grid;
        gap: 14px;
        padding: 20px;
        grid-template-columns: repeat(6, minmax(0, 1fr));
    }

    .summary-card {
        border-radius: 22px;
        padding: 16px;
        border: 1px solid rgba(148, 163, 184, 0.14);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.94), rgba(255, 255, 255, 0.96));
    }

    .summary-card.balance-good {
        background: linear-gradient(180deg, rgba(236, 253, 245, 0.96), rgba(220, 252, 231, 0.92));
        border-color: rgba(34, 197, 94, 0.18);
    }

    .summary-card.balance-warn {
        background: linear-gradient(180deg, rgba(255, 247, 237, 0.96), rgba(254, 215, 170, 0.2));
        border-color: rgba(245, 158, 11, 0.18);
    }

    .summary-card.is-info {
        background: linear-gradient(180deg, rgba(240, 249, 255, 0.96), rgba(224, 242, 254, 0.9));
        border-color: rgba(56, 189, 248, 0.16);
    }

    .summary-card p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.6;
    }

    .table-card {
        padding: 24px;
    }

    .report-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .report-head h2 {
        margin: 0;
        color: #1f2937;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .report-head p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 0.78rem;
    }

    .report-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .balance-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 9px 14px;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .balance-pill.good {
        background: rgba(34, 197, 94, 0.14);
        color: #166534;
    }

    .balance-pill.warn {
        background: rgba(245, 158, 11, 0.14);
        color: #b45309;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 9px 14px;
        background: rgba(15, 23, 42, 0.05);
        color: #334155;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .penjamin-row {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .penjamin-tabs {
        display: inline-flex;
        width: fit-content;
        max-width: 100%;
        flex-wrap: wrap;
        gap: 8px;
        padding: 6px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(241, 245, 249, 0.96));
    }

    .penjamin-download {
        flex: 0 0 auto;
    }

    .penjamin-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 16px;
        border-radius: 14px;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: background 160ms ease, color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
    }

    .penjamin-tab:hover {
        color: #1e293b;
        transform: translateY(-1px);
    }

    .penjamin-tab.is-active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.18);
    }

    .report-wrap {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .report-table th {
        padding: 12px 14px;
        border: 1px solid #b8d18d;
        background: #dbe7b6;
        color: #223017;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .report-table td {
        padding: 11px 14px;
        border: 1px solid #c9dba6;
        background: rgba(245, 248, 234, 0.82);
        color: #2b3442;
        font-size: 0.82rem;
        vertical-align: top;
    }

    .report-table th.is-date,
    .report-table td.is-date,
    .report-table th.is-number,
    .report-table td.is-number {
        white-space: nowrap;
    }

    .report-table td.is-number,
    .report-table th.is-number {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .report-table tr.section-row td {
        background: #eef5d9;
        color: #31411c;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .report-table tr.subtotal-row td,
    .report-table tr.total-row td {
        font-weight: 800;
    }

    .report-table tr.subtotal-row td {
        background: #f2f7e4;
    }

    .report-table tr.total-row td {
        background: #e5efcb;
        color: #223017;
    }

    .report-table tr.balance-row td {
        background: #eef6ff;
        border-color: #cbdcf7;
        color: #1d4ed8;
    }

    @media (max-width: 1200px) {
        .hero-card {
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
        }

        .summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .hero-card,
        .summary-grid,
        .table-card {
            border-radius: 22px;
            padding: 18px;
        }

        .hero-card {
            grid-template-columns: 1fr;
        }

        .filter-form {
            justify-content: flex-start;
        }

        .field-wrap {
            min-width: 0;
            flex: 1 1 220px;
        }

        .hero-stats,
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .report-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .report-meta {
            justify-content: flex-start;
        }

        .penjamin-row {
            align-items: stretch;
        }

        .penjamin-download {
            align-self: flex-end;
        }
    }

    @media (max-width: 560px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $formattedMonth = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->locale('id')->translatedFormat('F Y');
    $bpjsClaimCode = $bpjsClaimSummary['komponen_selisih']?->kode_komponen ?: 'BPJS';
    $bpjsClaimName = $bpjsClaimSummary['komponen_selisih']?->nama_komponen ?: 'Klaim BPJS Bulanan';
    $bpjsClaimTotal = (float) ($bpjsClaimSummary['total_klaim'] ?? 0);
    $bpjsClaimDebit = (float) ($bpjsClaimSummary['debet'] ?? 0);
    $bpjsClaimKredit = (float) ($bpjsClaimSummary['kredit'] ?? 0);
    $bpjsClaimCount = (int) ($bpjsClaimSummary['count'] ?? 0);
    $bpjsClaimSelisih = (float) ($bpjsClaimSummary['selisih_nominal'] ?? 0);
    $bpjsClaimSelisihArah = $bpjsClaimSummary['selisih_arah'] ?? null;
    $bpjsClaimSelisihLabel = $bpjsClaimSelisihArah === 'debet'
        ? 'Surplus di Debet'
        : ($bpjsClaimSelisihArah === 'kredit' ? 'Selisih di Kredit' : 'Tanpa Selisih');
    $bpjsClaimDetailLabel = $bpjsClaimCount > 0
        ? $bpjsClaimName . ' · ' . $bpjsClaimCount . ' data klaim · '
            . number_format((int) ($bpjsClaimSummary['jumlah_komponen_acuan'] ?? 0), 0, ',', '.')
            . ' komponen acuan'
        : $bpjsClaimName;
    $tabQueryBase = array_filter([
        'bulan' => $selectedMonth,
        'clinic_id' => $selectedClinicFilter !== '' ? $selectedClinicFilter : null,
    ], fn ($value) => filled($value));
    $pdfDownloadUrl = route('rekap-bulanan.pdf', array_merge($tabQueryBase, [
        'penjamin' => $selectedPenjaminMode,
    ]));
    $formatNominal = function ($value) {
        $value = (float) $value;

        if (abs($value) < 0.01) {
            return '-';
        }

        if ($value < 0) {
            return '-Rp ' . number_format(abs($value), 0, ',', '.');
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    };
@endphp

<div class="rekap-shell">
    <section class="hero-card">
        <div class="hero-copy">
            <p class="page-eyebrow">Laporan Bulanan</p>
            <h1>Rekap Bulanan</h1>
            <p>Rekap ini memisahkan versi klinik, klaim BPJS bulanan, dan total setelah klaim agar pembacaan layanan serta detail transaksi tetap rapi dan balance.</p>
        </div>

        <form method="GET" action="{{ route('rekap-bulanan') }}" class="filter-form">
            <input type="hidden" name="penjamin" value="{{ $selectedPenjaminMode }}">
            <div class="field-wrap">
                <label for="bulan">Filter Bulan</label>
                <input id="bulan" type="month" name="bulan" value="{{ $selectedMonth }}">
            </div>

            @if ($showClinicFilter)
                <div class="field-wrap">
                    <label for="rekap-bulan-clinic-id">Klinik</label>
                    <select id="rekap-bulan-clinic-id" name="clinic_id">
                        <option value="all" @selected($selectedClinicFilter === 'all')>Semua Klinik</option>
                        @foreach ($clinicOptions as $clinicOption)
                            <option value="{{ $clinicOption->id }}" @selected($selectedClinicFilter === (string) $clinicOption->id)>
                                {{ $clinicOption->kode_klinik }} · {{ $clinicOption->nama_klinik }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit" class="btn btn-primary filter-submit">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6h16"></path>
                    <path d="M7 12h10"></path>
                    <path d="M10 18h4"></path>
                </svg>
                Filter Data
            </button>
        </form>

        <div class="penjamin-row">
            <div class="penjamin-tabs" role="tablist" aria-label="Filter penjamin laporan bulanan">
                @foreach ([
                    'all' => 'Semua Penjamin',
                    'umum' => 'Umum',
                    'bpjs' => 'BPJS',
                ] as $tabValue => $tabLabel)
                    <a
                        href="{{ route('rekap-bulanan', array_merge($tabQueryBase, ['penjamin' => $tabValue])) }}"
                        class="penjamin-tab {{ $selectedPenjaminMode === $tabValue ? 'is-active' : '' }}"
                        role="tab"
                        aria-selected="{{ $selectedPenjaminMode === $tabValue ? 'true' : 'false' }}"
                    >
                        {{ $tabLabel }}
                    </a>
                @endforeach
            </div>

            <a href="{{ $pdfDownloadUrl }}" class="btn btn-secondary penjamin-download">
                Download PDF
            </a>
        </div>
    </section>

    <section class="summary-grid">
        <article class="summary-card is-info">
            <span>Mode Penjamin</span>
            <strong>{{ $selectedPenjaminLabel }}</strong>
            <p>Periode aktif {{ $formattedMonth }} {{ $viewingAllClinics ? '· semua klinik' : '· ' . $selectedClinicLabel }}.</p>
        </article>

        <article class="summary-card">
            <span>Versi Klinik</span>
            <strong>{{ $formatNominal($totalDebitLayananVersiKlinik) }}</strong>
            <p>Total pendapatan layanan berdasarkan transaksi versi klinik untuk mode penjamin yang dipilih.</p>
        </article>

        <article class="summary-card">
            <span>Klaim BPJS</span>
            <strong>{{ $formatNominal($bpjsClaimTotal) }}</strong>
            <p>{{ $selectedPenjaminMode === 'umum' ? 'Mode Umum tidak memakai klaim BPJS.' : ($bpjsClaimCount > 0 ? $bpjsClaimCount . ' data klaim bulanan terbaca.' : 'Belum ada klaim BPJS bulanan untuk periode ini.') }}</p>
        </article>

        <article class="summary-card">
            <span>Setelah Klaim</span>
            <strong>{{ $formatNominal($totalDebitLayanan) }}</strong>
            <p>Total layanan setelah klaim BPJS ditambahkan ke sisi {{ $bpjsClaimDebit > 0 ? 'debet' : ($bpjsClaimKredit > 0 ? 'kredit' : 'yang sesuai') }}.</p>
        </article>

        <article class="summary-card {{ $isBalanced ? 'balance-good' : 'balance-warn' }}">
            <span>Saldo Bulanan</span>
            <strong>{{ $formatNominal($saldoAkhir) }}</strong>
            <p>Selisih antara total debet detail transaksi setelah klaim dengan seluruh kredit detail dan pengeluaran.</p>
        </article>

        <article class="summary-card {{ abs($bpjsClaimSelisih) < 0.01 ? 'is-info' : ($bpjsClaimSelisih > 0 ? 'balance-good' : 'balance-warn') }}">
            <span>Selisih Klaim</span>
            <strong>{{ $formatNominal($bpjsClaimSelisih) }}</strong>
            <p>{{ $selectedPenjaminMode === 'umum' ? 'Tidak ada selisih klaim pada mode umum.' : $bpjsClaimSelisihLabel }}</p>
        </article>
    </section>

    <section class="table-card">
        <div class="report-head">
            <div>
                <h2>Rekap Periode {{ $periodLabel }} · {{ $selectedPenjaminLabel }}</h2>
                <p>Field transaksi mengikuti master komponen dan administrasi yang sama dengan form input. Untuk BPJS, laporan memisahkan subtotal versi klinik dan total setelah klaim bulanan.</p>
            </div>

            <div class="report-meta">
                @if ($selectedPenjaminMode !== 'umum')
                    <span class="meta-pill">
                        Klaim BPJS: {{ $bpjsClaimCount }} data
                    </span>
                @endif
                <span class="balance-pill {{ $isBalanced ? 'good' : 'warn' }}">
                    {{ $isBalanced ? 'Balance Sesuai' : 'Balance Belum Sama' }}
                </span>
            </div>
        </div>

        <div class="report-wrap">
            <table class="report-table">
                <colgroup>
                    <col style="width: 180px;">
                    <col style="width: 260px;">
                    <col style="width: 100px;">
                    <col style="width: 170px;">
                    <col style="width: 170px;">
                </colgroup>
                <thead>
                    <tr>
                        <th class="is-date">Tanggal</th>
                        <th>Keterangan</th>
                        <th>Kode</th>
                        <th class="is-number">Debet</th>
                        <th class="is-number">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="section-row">
                        <td colspan="5">A. Pendapatan Berdasarkan Layanan</td>
                    </tr>
                    @foreach ($layananRows as $row)
                        <tr>
                            <td class="is-date">{{ $periodLabel }}</td>
                            <td>{{ $row['keterangan'] }}</td>
                            <td>{{ $row['kode'] }}</td>
                            <td class="is-number">{{ $formatNominal($row['debet']) }}</td>
                            <td class="is-number">{{ $formatNominal($row['kredit']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="3">Subtotal Versi Klinik</td>
                        <td class="is-number">{{ $formatNominal($totalDebitLayananVersiKlinik) }}</td>
                        <td class="is-number">{{ $formatNominal($totalKreditLayananVersiKlinik) }}</td>
                    </tr>
                    @if ($hasBpjsClaimRows && ! $bpjsClaimMergedIntoLayanan)
                        <tr class="subtotal-row">
                            <td class="is-date">{{ $periodLabel }}</td>
                            <td>{{ $bpjsClaimDetailLabel }}</td>
                            <td>{{ $bpjsClaimCode }}</td>
                            <td class="is-number">{{ $formatNominal($bpjsClaimDebit) }}</td>
                            <td class="is-number">{{ $formatNominal($bpjsClaimKredit) }}</td>
                        </tr>
                    @endif
                    @if ($hasBpjsClaimRows)
                        <tr class="total-row">
                            <td colspan="3">Total Setelah Klaim BPJS</td>
                            <td class="is-number">{{ $formatNominal($totalDebitLayanan) }}</td>
                            <td class="is-number">{{ $formatNominal($totalKreditLayanan) }}</td>
                        </tr>
                        <tr class="balance-row">
                            <td class="is-date">{{ $periodLabel }}</td>
                            <td>Selisih Klaim vs Versi Klinik</td>
                            <td>INFO</td>
                            <td class="is-number">{{ $bpjsClaimSelisih > 0 ? $formatNominal($bpjsClaimSelisih) : '-' }}</td>
                            <td class="is-number">{{ $bpjsClaimSelisih < 0 ? $formatNominal(abs($bpjsClaimSelisih)) : '-' }}</td>
                        </tr>
                    @else
                        <tr class="total-row">
                            <td colspan="3">Total Pendapatan Berdasarkan Layanan</td>
                            <td class="is-number">{{ $formatNominal($totalDebitLayanan) }}</td>
                            <td class="is-number">{{ $formatNominal($totalKreditLayanan) }}</td>
                        </tr>
                    @endif

                    <tr class="section-row">
                        <td colspan="5">B. Detail Transaksi</td>
                    </tr>
                    @foreach ($komponenRows as $row)
                        <tr>
                            <td class="is-date">{{ $periodLabel }}</td>
                            <td>{{ $row['keterangan'] }}</td>
                            <td>{{ $row['kode'] }}</td>
                            <td class="is-number">{{ $formatNominal($row['debet']) }}</td>
                            <td class="is-number">{{ $formatNominal($row['kredit']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="3">Subtotal Detail Versi Klinik</td>
                        <td class="is-number">{{ $formatNominal($totalDebitKomponenVersiKlinik) }}</td>
                        <td class="is-number">{{ $formatNominal($totalKreditKomponenVersiKlinik) }}</td>
                    </tr>
                    @if ($hasBpjsClaimRows)
                        <tr class="total-row">
                            <td colspan="3">Total Detail Setelah Klaim BPJS</td>
                            <td class="is-number">{{ $formatNominal($totalDebitKomponen) }}</td>
                            <td class="is-number">{{ $formatNominal($totalKreditKomponen) }}</td>
                        </tr>
                        <tr class="balance-row">
                            <td class="is-date">{{ $periodLabel }}</td>
                            <td>Selisih Klaim vs Versi Klinik</td>
                            <td>INFO</td>
                            <td class="is-number">{{ $bpjsClaimSelisih > 0 ? $formatNominal($bpjsClaimSelisih) : '-' }}</td>
                            <td class="is-number">{{ $bpjsClaimSelisih < 0 ? $formatNominal(abs($bpjsClaimSelisih)) : '-' }}</td>
                        </tr>
                    @else
                        <tr class="total-row">
                            <td colspan="3">Total Detail Transaksi</td>
                            <td class="is-number">{{ $formatNominal($totalDebitKomponen) }}</td>
                            <td class="is-number">{{ $formatNominal($totalKreditKomponen) }}</td>
                        </tr>
                    @endif

                    <tr class="section-row">
                        <td colspan="5">C. Pengeluaran</td>
                    </tr>
                    @foreach ($pengeluaranRows as $row)
                        <tr>
                            <td class="is-date">{{ $periodLabel }}</td>
                            <td>{{ $row['keterangan'] }}</td>
                            <td>{{ $row['kode'] }}</td>
                            <td class="is-number">-</td>
                            <td class="is-number">{{ $formatNominal($row['kredit']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="3">Subtotal Pengeluaran</td>
                        <td class="is-number">-</td>
                        <td class="is-number">{{ $formatNominal($totalKreditPengeluaran) }}</td>
                    </tr>

                    <tr class="total-row">
                        <td colspan="3">Total Debet Layanan Setelah Klaim</td>
                        <td class="is-number">{{ $formatNominal($totalDebitLayanan) }}</td>
                        <td class="is-number">-</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total Kredit Layanan Setelah Klaim</td>
                        <td class="is-number">-</td>
                        <td class="is-number">{{ $formatNominal($totalKreditLayanan) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total Debet Detail Setelah Klaim</td>
                        <td class="is-number">{{ $formatNominal($totalDebitKomponen) }}</td>
                        <td class="is-number">-</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total Kredit Detail Setelah Klaim</td>
                        <td class="is-number">-</td>
                        <td class="is-number">{{ $formatNominal($totalKreditKomponen) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total Kredit Pengeluaran</td>
                        <td class="is-number">-</td>
                        <td class="is-number">{{ $formatNominal($totalKreditPengeluaran) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total Kredit Keseluruhan</td>
                        <td class="is-number">-</td>
                        <td class="is-number">{{ $formatNominal($totalKredit) }}</td>
                    </tr>
                    <tr class="balance-row total-row">
                        <td colspan="3">Saldo Bulanan</td>
                        <td class="is-number">{{ $formatNominal($saldoAkhir) }}</td>
                        <td class="is-number">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
