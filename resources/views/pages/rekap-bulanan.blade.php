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

    .report-wrap {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        min-width: 880px;
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
    }

    .report-table td.is-number {
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
    }
</style>

@php
    $formattedMonth = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->locale('id')->translatedFormat('F Y');
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
        </div>

        <form method="GET" action="{{ route('rekap-bulanan') }}" class="filter-form">
            <div class="field-wrap">
                <label for="bulan">Filter Bulan</label>
                <input id="bulan" type="month" name="bulan" value="{{ $selectedMonth }}">
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
                <strong>{{ $formattedMonth }}</strong>
            </article>
            <article class="hero-stat">
                <span>Debet Komponen</span>
                <strong>{{ $formatNominal($totalDebitKomponen) }}</strong>
            </article>
            <article class="hero-stat">
                <span>Kredit Pengeluaran</span>
                <strong>{{ $formatNominal($totalKredit) }}</strong>
            </article>
        </div>
    </section>

    <section class="summary-grid">
        <article class="summary-card">
            <span>Debet Layanan</span>
            <strong>{{ $formatNominal($totalDebitLayanan) }}</strong>
            <p>Total pendapatan yang dibaca dari kelompok layanan klinik.</p>
        </article>

        <article class="summary-card">
            <span>Debet Komponen</span>
            <strong>{{ $formatNominal($totalDebitKomponen) }}</strong>
            <p>Total pendapatan yang dibaca dari komponen uang daftar, periksa, obat, dan seterusnya.</p>
        </article>

        <article class="summary-card">
            <span>Kredit Pengeluaran</span>
            <strong>{{ $formatNominal($totalKredit) }}</strong>
            <p>Total pengeluaran lokal yang masuk ke sisi kredit.</p>
        </article>

        <article class="summary-card">
            <span>Saldo Bulanan</span>
            <strong>{{ $formatNominal($saldoAkhir) }}</strong>
            <p>Selisih antara debet komponen dengan seluruh kredit pengeluaran.</p>
        </article>

        <article class="summary-card {{ $isBalanced ? 'balance-good' : 'balance-warn' }}">
            <span>Status Balance</span>
            <strong>{{ $isBalanced ? 'Sesuai' : 'Perlu Cek' }}</strong>
            <p>Debet layanan dan debet komponen {{ $isBalanced ? 'sudah cocok' : 'belum sama' }} untuk periode ini.</p>
        </article>
    </section>

    <section class="table-card">
        <div class="report-head">
            <div>
                <h2>Rekap Periode {{ $periodLabel }}</h2>
                <p>Dua blok debet ditampilkan berdampingan secara logika untuk membuktikan laporan tetap balance sebelum dikurangi pengeluaran.</p>
            </div>

            <span class="balance-pill {{ $isBalanced ? 'good' : 'warn' }}">
                {{ $isBalanced ? 'Balance Sesuai' : 'Balance Belum Sama' }}
            </span>
        </div>

        <div class="report-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Kod</th>
                        <th>Debet</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="section-row">
                        <td colspan="5">A. Pendapatan Berdasarkan Layanan</td>
                    </tr>
                    @foreach ($layananRows as $row)
                        <tr>
                            <td>{{ $periodLabel }}</td>
                            <td>{{ $row['keterangan'] }}</td>
                            <td>{{ $row['kode'] }}</td>
                            <td class="is-number">{{ $formatNominal($row['debet']) }}</td>
                            <td class="is-number">-</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="3">Subtotal Pendapatan Berdasarkan Layanan</td>
                        <td class="is-number">{{ $formatNominal($totalDebitLayanan) }}</td>
                        <td class="is-number">-</td>
                    </tr>

                    <tr class="section-row">
                        <td colspan="5">B. Pendapatan Berdasarkan Field Transaksi</td>
                    </tr>
                    @foreach ($komponenRows as $row)
                        <tr>
                            <td>{{ $periodLabel }}</td>
                            <td>{{ $row['keterangan'] }}</td>
                            <td>{{ $row['kode'] }}</td>
                            <td class="is-number">{{ $formatNominal($row['debet']) }}</td>
                            <td class="is-number">-</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="3">Subtotal Pendapatan Berdasarkan Field Transaksi</td>
                        <td class="is-number">{{ $formatNominal($totalDebitKomponen) }}</td>
                        <td class="is-number">-</td>
                    </tr>

                    <tr class="section-row">
                        <td colspan="5">C. Pengeluaran</td>
                    </tr>
                    @foreach ($pengeluaranRows as $row)
                        <tr>
                            <td>{{ $periodLabel }}</td>
                            <td>{{ $row['keterangan'] }}</td>
                            <td>{{ $row['kode'] }}</td>
                            <td class="is-number">-</td>
                            <td class="is-number">{{ $formatNominal($row['kredit']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="3">Subtotal Pengeluaran</td>
                        <td class="is-number">-</td>
                        <td class="is-number">{{ $formatNominal($totalKredit) }}</td>
                    </tr>

                    <tr class="total-row">
                        <td colspan="3">Total Debet Layanan</td>
                        <td class="is-number">{{ $formatNominal($totalDebitLayanan) }}</td>
                        <td class="is-number">-</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total Debet Komponen</td>
                        <td class="is-number">{{ $formatNominal($totalDebitKomponen) }}</td>
                        <td class="is-number">-</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total Kredit Pengeluaran</td>
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
