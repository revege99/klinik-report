@extends('layouts.app')

@section('title', 'Rekap Tahunan | Klink Report')

@section('content')
<style>
    :root {
        --annual-sticky-no-width: 52px;
        --annual-sticky-keterangan-width: 176px;
        --annual-sticky-code-width: 74px;
    }

    .annual-shell {
        display: grid;
        gap: 18px;
    }

    .annual-shell > * {
        min-width: 0;
    }

    .hero-card,
    .summary-grid,
    .table-card {
        min-width: 0;
        max-width: 100%;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(16px);
    }

    .hero-card {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
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

    .hero-stats {
        display: grid;
        min-width: 340px;
        gap: 8px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .hero-stat,
    .summary-card {
        border-radius: 18px;
        padding: 12px 14px;
        border: 1px solid rgba(59, 130, 246, 0.12);
        background: linear-gradient(180deg, #f8fbff, #eef4ff);
    }

    .hero-stat span,
    .summary-card span {
        display: block;
        color: #64748b;
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .hero-stat strong,
    .summary-card strong {
        display: block;
        margin-top: 6px;
        color: #1e293b;
        font-size: 0.92rem;
        font-weight: 600;
        line-height: 1.15;
    }

    .summary-card p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 0.75rem;
        line-height: 1.6;
    }

    .summary-card.is-saldo {
        background: linear-gradient(180deg, rgba(236, 253, 245, 0.96), rgba(220, 252, 231, 0.92));
        border-color: rgba(34, 197, 94, 0.18);
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

    .field-wrap input {
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

    .field-wrap input:focus {
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

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.2);
    }

    .summary-grid {
        display: grid;
        gap: 14px;
        padding: 20px;
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .table-card {
        padding: 24px;
        overflow: hidden;
    }

    .report-head {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 16px;
        margin-bottom: 18px;
        min-width: 0;
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
        font-size: 0.75rem;
    }

    .scroll-rail {
        margin-bottom: 14px;
        padding: 8px 10px;
        border: 1px solid rgba(78, 167, 198, 0.16);
        border-radius: 16px;
        background: linear-gradient(180deg, rgba(248, 251, 255, 0.92), rgba(237, 247, 251, 0.96));
        max-width: 100%;
    }

    .scroll-rail-label {
        display: block;
        margin-bottom: 6px;
        color: #55667d;
        font-size: 0.66rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .scroll-rail-track {
        overflow-x: auto;
        overflow-y: hidden;
        height: 18px;
        scrollbar-width: thin;
        scrollbar-color: rgba(78, 167, 198, 0.92) rgba(207, 226, 237, 0.8);
    }

    .scroll-rail-track::-webkit-scrollbar {
        height: 14px;
    }

    .scroll-rail-track::-webkit-scrollbar-track {
        border-radius: 999px;
        background: rgba(207, 226, 237, 0.88);
    }

    .scroll-rail-track::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: linear-gradient(90deg, #4ea7c6, #3e97b7);
    }

    .scroll-rail-inner {
        height: 1px;
    }

    .report-wrap {
        width: 100%;
        max-width: 100%;
        overflow-x: scroll;
        padding-bottom: 8px;
        border-top: 1px solid rgba(148, 163, 184, 0.14);
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        scrollbar-width: thin;
        scrollbar-color: rgba(78, 167, 198, 0.82) rgba(226, 232, 240, 0.6);
    }

    .report-wrap::-webkit-scrollbar {
        height: 12px;
    }

    .report-wrap::-webkit-scrollbar-track {
        border-radius: 999px;
        background: rgba(226, 232, 240, 0.7);
    }

    .report-wrap::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: linear-gradient(90deg, #4ea7c6, #3e97b7);
    }

    .annual-table {
        width: 100%;
        min-width: 3200px;
        border-collapse: collapse;
    }

    .annual-table th,
    .annual-table td {
        border: 1px solid #d5e7ef;
    }

    .annual-table thead th {
        background: linear-gradient(180deg, #4ea7c6, #3e97b7);
        color: #f8fcff;
        text-align: center;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .annual-table thead tr:first-child th {
        padding: 12px 10px;
    }

    .annual-table thead tr:last-child th {
        padding: 8px 10px;
    }

    .annual-table td {
        padding: 8px 10px;
        background: #f2f8fb;
        color: #20313c;
        font-size: 0.77rem;
    }

    .annual-table td.is-center {
        text-align: center;
    }

    .annual-table td.is-number {
        text-align: right;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .annual-table td.keterangan-cell {
        min-width: var(--annual-sticky-keterangan-width);
        width: var(--annual-sticky-keterangan-width);
        max-width: var(--annual-sticky-keterangan-width);
        font-weight: 600;
    }

    .annual-table td.code-cell {
        min-width: var(--annual-sticky-code-width);
        width: var(--annual-sticky-code-width);
        max-width: var(--annual-sticky-code-width);
        text-align: center;
        font-weight: 600;
    }

    .annual-table td.no-cell {
        min-width: var(--annual-sticky-no-width);
        width: var(--annual-sticky-no-width);
        max-width: var(--annual-sticky-no-width);
        text-align: center;
        font-weight: 600;
    }

    .truncate-text {
        display: inline-block;
        max-width: 156px;
        overflow: hidden;
        color: inherit;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: bottom;
    }

    .annual-table :is(th, td).sticky-no,
    .annual-table :is(th, td).sticky-keterangan,
    .annual-table :is(th, td).sticky-code,
    .annual-table td.sticky-summary {
        position: sticky;
    }

    .annual-table :is(th, td).sticky-no {
        left: 0;
        z-index: 4;
    }

    .annual-table :is(th, td).sticky-keterangan {
        left: var(--annual-sticky-no-width);
        z-index: 4;
    }

    .annual-table :is(th, td).sticky-code {
        left: calc(var(--annual-sticky-no-width) + var(--annual-sticky-keterangan-width));
        z-index: 4;
        box-shadow: 10px 0 18px -16px rgba(15, 23, 42, 0.38);
    }

    .annual-table thead th.sticky-no,
    .annual-table thead th.sticky-keterangan,
    .annual-table thead th.sticky-code {
        z-index: 8;
        background: linear-gradient(180deg, #4ea7c6, #3e97b7);
        color: #f8fcff;
    }

    .annual-table tbody td.sticky-no,
    .annual-table tbody td.sticky-keterangan,
    .annual-table tbody td.sticky-code {
        background: #f2f8fb;
        color: #20313c;
    }

    .annual-table td.sticky-summary {
        left: 0;
        z-index: 5;
        min-width: calc(var(--annual-sticky-no-width) + var(--annual-sticky-keterangan-width) + var(--annual-sticky-code-width));
        box-shadow: 10px 0 18px -16px rgba(15, 23, 42, 0.38);
        background: inherit;
    }

    .annual-table tr.section-row td {
        background: #ffffff;
        color: #0f172a;
        font-size: 0.84rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .annual-table tr.subtotal-row td {
        background: #4ea7c6;
        color: #ffffff;
        font-weight: 700;
    }

    .annual-table tr.balance-row td {
        background: #e8f7ef;
        color: #166534;
        font-weight: 700;
    }

    .annual-table tr.balance-row.is-negative td {
        background: #fff7ed;
        color: #c2410c;
    }

    @media (max-width: 1200px) {
        .hero-card {
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
        }

        .hero-stats {
            grid-column: 1 / -1;
            min-width: 0;
        }

        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
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
            grid-template-columns: 1fr;
        }

        .report-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .scroll-rail {
            padding: 8px;
        }
    }
</style>

@php
    $formatNominal = function ($value) {
        $value = (float) $value;

        if (abs($value) < 0.01) {
            return '-';
        }

        if ($value < 0) {
            return '-Rp ' . number_format(abs($value), 0, ',', '.');
        }

        return number_format($value, 0, ',', '.');
    };

    $formatRupiah = function ($value) use ($formatNominal) {
        $value = (float) $value;

        if (abs($value) < 0.01) {
            return 'Rp 0';
        }

        if ($value < 0) {
            return '-Rp ' . $formatNominal(abs($value));
        }

        return 'Rp ' . $formatNominal($value);
    };
@endphp

<div class="annual-shell">
    <section class="hero-card">
        <div class="hero-copy">
            <p class="page-eyebrow">Laporan Tahunan</p>
            <h1>Rekap Tahunan</h1>
        </div>

        <form method="GET" action="{{ route('rekap-tahunan') }}" class="filter-form">
            <div class="field-wrap">
                <label for="tahun">Filter Tahun</label>
                <input id="tahun" type="number" name="tahun" min="2000" max="2100" value="{{ $selectedYear }}">
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

        <div class="hero-stats">
            <article class="hero-stat">
                <span>Periode</span>
                <strong>{{ $selectedYear }}</strong>
            </article>
            <article class="hero-stat">
                <span>Total Penerimaan</span>
                <strong>{{ $formatRupiah($annualDebitTotal) }}</strong>
            </article>
            <article class="hero-stat">
                <span>Total Pengeluaran</span>
                <strong>{{ $formatRupiah($annualKreditTotal) }}</strong>
            </article>
        </div>
    </section>

    <section class="summary-grid">
        <article class="summary-card">
            <span>Penerimaan Tahunan</span>
            <strong>{{ $formatRupiah($annualDebitTotal) }}</strong>
            <p>Total debet dari transaksi pasien sepanjang tahun {{ $selectedYear }}.</p>
        </article>

        <article class="summary-card">
            <span>Pengeluaran Tahunan</span>
            <strong>{{ $formatRupiah($annualKreditTotal) }}</strong>
            <p>Total kredit dari input pengeluaran lokal pada tahun yang sama.</p>
        </article>

        <article class="summary-card is-saldo">
            <span>Saldo Bersih</span>
            <strong>{{ $formatRupiah($annualSaldo) }}</strong>
            <p>Selisih penerimaan dengan pengeluaran tahunan.</p>
        </article>

        <article class="summary-card">
            <span>Kode Penerimaan</span>
            <strong>{{ $penerimaanRows->count() }}</strong>
            <p>Jumlah baris layanan yang dipakai untuk penerimaan tahunan.</p>
        </article>

        <article class="summary-card">
            <span>Kategori Kredit</span>
            <strong>{{ $pengeluaranRows->count() }}</strong>
            <p>Jumlah baris kategori pengeluaran yang masuk ke sisi kredit.</p>
        </article>
    </section>

    <section class="table-card">
        <div class="report-head">
            <div>
                <h2>Rekap Penerimaan dan Pengeluaran Tahun {{ $selectedYear }}</h2>
                <p>Tabel dibuat lebar agar struktur per bulan tetap jelas dan bisa discroll horizontal seperti format laporan Excel.</p>
            </div>
        </div>

        <div class="scroll-rail" id="annualScrollRail">
            <span class="scroll-rail-label">Scrollbar tabel kiri kanan</span>
            <div class="scroll-rail-track" id="annualTopScroller" aria-label="Scrollbar horizontal tabel rekap tahunan">
                <div class="scroll-rail-inner" id="annualTopScrollerInner"></div>
            </div>
        </div>

        <div class="report-wrap" id="annualReportWrap">
            <table class="annual-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="sticky-no">No.</th>
                        <th rowspan="2" class="sticky-keterangan">Keterangan</th>
                        <th rowspan="2" class="sticky-code">Kode</th>
                        @foreach ($monthHeaders as $month)
                            <th colspan="2">{{ strtoupper($month['label']) }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($monthHeaders as $month)
                            <th>Debet</th>
                            <th>Kredit</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="section-row">
                        <td colspan="{{ 3 + ($monthHeaders->count() * 2) }}">Penerimaan</td>
                    </tr>

                    @foreach ($penerimaanRows as $index => $row)
                        <tr>
                            <td class="no-cell sticky-no">{{ $index + 1 }}</td>
                            <td class="keterangan-cell sticky-keterangan">
                                <span class="truncate-text" title="{{ $row['keterangan'] }}">{{ $row['keterangan'] }}</span>
                            </td>
                            <td class="code-cell sticky-code">{{ $row['kode'] }}</td>
                            @foreach ($monthHeaders as $month)
                                <td class="is-number">{{ $formatNominal(data_get($row, 'months.' . $month['number'] . '.debet', 0)) }}</td>
                                <td class="is-number">-</td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr class="subtotal-row">
                        <td colspan="3" class="sticky-summary">Jumlah Berdasarkan Jenis Penerimaan</td>
                        @foreach ($monthHeaders as $month)
                            <td class="is-number">{{ $formatNominal($penerimaanMonthlyTotals[$month['number']] ?? 0) }}</td>
                            <td class="is-number">-</td>
                        @endforeach
                    </tr>

                    <tr class="section-row">
                        <td colspan="{{ 3 + ($monthHeaders->count() * 2) }}">Biaya Pengeluaran</td>
                    </tr>

                    @foreach ($pengeluaranRows as $index => $row)
                        <tr>
                            <td class="no-cell sticky-no">{{ $index + 1 }}</td>
                            <td class="keterangan-cell sticky-keterangan">
                                <span class="truncate-text" title="{{ $row['keterangan'] }}">{{ $row['keterangan'] }}</span>
                            </td>
                            <td class="code-cell sticky-code">{{ $row['kode'] }}</td>
                            @foreach ($monthHeaders as $month)
                                <td class="is-number">-</td>
                                <td class="is-number">{{ $formatNominal(data_get($row, 'months.' . $month['number'] . '.kredit', 0)) }}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr class="subtotal-row">
                        <td colspan="3" class="sticky-summary">Jumlah Pengeluaran</td>
                        @foreach ($monthHeaders as $month)
                            <td class="is-number">-</td>
                            <td class="is-number">{{ $formatNominal($pengeluaranMonthlyTotals[$month['number']] ?? 0) }}</td>
                        @endforeach
                    </tr>

                    @php
                        $hasNegativeSaldo = collect($saldoMonthlyTotals)->contains(fn ($value) => (float) $value < 0);
                    @endphp
                    <tr class="balance-row {{ $hasNegativeSaldo ? 'is-negative' : '' }}">
                        <td colspan="3" class="sticky-summary">Saldo Bersih per Bulan</td>
                        @foreach ($monthHeaders as $month)
                            @php
                                $saldo = (float) ($saldoMonthlyTotals[$month['number']] ?? 0);
                            @endphp
                            <td class="is-number">{{ $saldo >= 0 ? $formatNominal($saldo) : '-' }}</td>
                            <td class="is-number">{{ $saldo < 0 ? $formatNominal(abs($saldo)) : '-' }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
    (() => {
        const wrap = document.getElementById('annualReportWrap');
        const rail = document.getElementById('annualScrollRail');
        const topScroller = document.getElementById('annualTopScroller');
        const topScrollerInner = document.getElementById('annualTopScrollerInner');

        if (!wrap || !topScroller || !topScrollerInner) {
            return;
        }

        let syncLock = false;

        const syncScrollerWidth = () => {
            topScrollerInner.style.width = `${wrap.scrollWidth}px`;
        };

        const updateScrollState = () => {
            const maxScroll = Math.max(wrap.scrollWidth - wrap.clientWidth, 0);
            const canScroll = maxScroll > 8;

            if (rail) {
                rail.style.display = canScroll ? 'block' : 'none';
            }
        };

        const syncFromWrap = () => {
            if (syncLock) {
                return;
            }

            syncLock = true;
            topScroller.scrollLeft = wrap.scrollLeft;
            syncLock = false;
        };

        const syncFromTopScroller = () => {
            if (!topScroller || syncLock) {
                return;
            }

            syncLock = true;
            wrap.scrollLeft = topScroller.scrollLeft;
            syncLock = false;
            updateScrollState();
        };

        wrap.addEventListener('scroll', () => {
            syncFromWrap();
            updateScrollState();
        }, { passive: true });

        topScroller.addEventListener('scroll', syncFromTopScroller, { passive: true });
        window.addEventListener('resize', () => {
            syncScrollerWidth();
            syncFromWrap();
            updateScrollState();
        });

        if (window.ResizeObserver) {
            const resizeObserver = new ResizeObserver(() => {
                syncScrollerWidth();
                syncFromWrap();
                updateScrollState();
            });

            resizeObserver.observe(wrap);
            const table = wrap.querySelector('table');
            if (table) {
                resizeObserver.observe(table);
            }
        }

        syncScrollerWidth();
        syncFromWrap();
        updateScrollState();
    })();
</script>
@endsection
