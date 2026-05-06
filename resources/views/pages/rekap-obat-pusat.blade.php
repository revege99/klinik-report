@extends('layouts.app')

@section('title', 'Rekap Obat Pusat | Klink Report')

@section('content')
<style>
    .central-shell {
        display: grid;
        gap: 18px;
    }

    .central-card,
    .central-summary,
    .central-table-card,
    .central-warning {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 26px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(16px);
    }

    .central-card {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
        gap: 14px 16px;
        padding: 18px 20px;
    }

    .page-eyebrow {
        margin: 0;
        color: #2563eb;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    .central-copy h1 {
        margin: 6px 0 0;
        color: #10233d;
        font-size: 1.32rem;
        line-height: 1.1;
    }

    .central-copy p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 0.79rem;
        line-height: 1.65;
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        justify-content: flex-end;
        gap: 9px;
    }

    .field-wrap {
        display: flex;
        min-width: 178px;
        flex: 0 0 178px;
        flex-direction: column;
        gap: 6px;
    }

    .field-wrap label {
        color: #334155;
        font-size: 0.69rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .field-wrap input,
    .field-wrap select {
        height: 40px;
        width: 100%;
        border: 1px solid #d7e1ef;
        border-radius: 13px;
        padding: 10px 12px;
        background: #f8fafc;
        color: #10233d;
        font-size: 0.79rem;
        transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
    }

    .field-wrap input:focus,
    .field-wrap select:focus {
        outline: none;
        border-color: #60a5fa;
        background: white;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.14);
    }

    .btn-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        height: 40px;
        padding: 0 14px;
        border: none;
        border-radius: 13px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.18);
        transition: transform 160ms ease, box-shadow 160ms ease;
    }

    .btn-filter:hover {
        transform: translateY(-1px);
    }

    .btn-filter svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 1.9;
    }

    .central-summary {
        display: grid;
        gap: 13px;
        padding: 18px 20px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .summary-card {
        border-radius: 20px;
        padding: 14px 15px;
        border: 1px solid rgba(148, 163, 184, 0.14);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.94), rgba(255, 255, 255, 0.96));
    }

    .summary-card span {
        display: block;
        color: #64748b;
        font-size: 0.61rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .summary-card strong {
        display: block;
        margin-top: 5px;
        color: #1e293b;
        font-size: 0.94rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .summary-card p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 0.75rem;
        line-height: 1.55;
    }

    .summary-card.is-strong {
        background: linear-gradient(180deg, rgba(236, 253, 245, 0.96), rgba(220, 252, 231, 0.9));
        border-color: rgba(34, 197, 94, 0.18);
    }

    .central-warning {
        padding: 16px 18px;
        border-color: rgba(245, 158, 11, 0.18);
        background: linear-gradient(180deg, rgba(255, 251, 235, 0.96), rgba(254, 243, 199, 0.82));
    }

    .central-warning h2 {
        margin: 0 0 8px;
        color: #92400e;
        font-size: 0.92rem;
    }

    .central-warning ul {
        margin: 0;
        padding-left: 18px;
        color: #9a3412;
        font-size: 0.78rem;
        line-height: 1.6;
    }

    .central-table-card {
        padding: 22px;
    }

    .report-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .report-head h2 {
        margin: 0;
        color: #1f2937;
        font-size: 0.98rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .report-head p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 0.75rem;
    }

    .report-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 8px 12px;
        background: rgba(15, 23, 42, 0.05);
        color: #334155;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .summary-table-wrap {
        overflow-x: auto;
    }

    .summary-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .summary-table th {
        padding: 11px 13px;
        border-bottom: 1px solid rgba(203, 213, 225, 0.9);
        color: #526277;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
        vertical-align: middle;
    }

    .summary-table td {
        padding: 12px 13px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        color: #1f2937;
        font-size: 0.8rem;
        vertical-align: middle;
    }

    .summary-table tfoot td {
        padding: 13px;
        border-top: 1px solid rgba(148, 163, 184, 0.26);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(241, 245, 249, 0.96));
        color: #0f172a;
        font-size: 0.79rem;
        font-weight: 700;
    }

    .summary-table tbody tr:hover {
        background: rgba(248, 250, 252, 0.88);
    }

    .summary-table tbody td.metric-cell {
        text-align: left;
        white-space: nowrap;
        padding-left: 18px;
    }

    .summary-table tbody td.action-cell {
        text-align: left;
        padding-left: 10px;
    }

    .clinic-main {
        font-weight: 700;
        color: #111827;
    }

    .clinic-sub {
        margin-top: 4px;
        color: #64748b;
        font-size: 0.74rem;
        line-height: 1.55;
    }

    .summary-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 12px;
        background: rgba(37, 99, 235, 0.08);
        color: #1d4ed8;
        font-size: 0.74rem;
        font-weight: 700;
        text-decoration: none;
        transition: background 160ms ease, color 160ms ease, transform 160ms ease;
    }

    .summary-link:hover {
        background: rgba(37, 99, 235, 0.14);
        transform: translateY(-1px);
    }

    .summary-link[type="button"] {
        border: none;
        cursor: pointer;
    }

    .report-wrap {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        min-width: 840px;
        border-collapse: collapse;
    }

    .report-table th {
        padding: 11px 13px;
        border-bottom: 1px solid rgba(203, 213, 225, 0.9);
        color: #526277;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
    }

    .report-table td {
        padding: 12px 13px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        color: #1f2937;
        font-size: 0.8rem;
        vertical-align: top;
    }

    .report-table tfoot td {
        padding: 13px;
        border-top: 1px solid rgba(148, 163, 184, 0.26);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(241, 245, 249, 0.96));
        color: #0f172a;
        font-size: 0.79rem;
        font-weight: 700;
    }

    .report-table tbody tr:hover {
        background: rgba(248, 250, 252, 0.88);
    }

    .cell-code {
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
    }

    .cell-name {
        font-weight: 700;
        color: #111827;
    }

    .cell-clinic {
        color: #64748b;
        font-size: 0.74rem;
        line-height: 1.55;
    }

    .text-right {
        text-align: right;
        white-space: nowrap;
    }

    .empty-state {
        padding: 28px 14px;
        color: #64748b;
        font-size: 0.82rem;
        text-align: center;
    }

    .obat-modal[hidden] {
        display: none;
    }

    .obat-modal {
        position: fixed;
        inset: 0;
        z-index: 80;
    }

    .obat-modal .modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.46);
        backdrop-filter: blur(6px);
    }

    .obat-modal .modal-dialog {
        position: relative;
        display: grid;
        grid-template-rows: auto minmax(0, 1fr) auto;
        gap: 0;
        width: min(1080px, calc(100vw - 32px));
        max-height: calc(100vh - 48px);
        margin: 24px auto;
        overflow: hidden;
        border-radius: 24px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 26px 60px rgba(15, 23, 42, 0.24);
    }

    .obat-modal .modal-header,
    .obat-modal .modal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 20px;
        background: rgba(248, 250, 252, 0.96);
    }

    .obat-modal .modal-header {
        border-bottom: 1px solid rgba(226, 232, 240, 0.9);
    }

    .obat-modal .modal-footer {
        border-top: 1px solid rgba(226, 232, 240, 0.9);
    }

    .obat-modal .modal-body {
        display: grid;
        grid-template-rows: auto minmax(0, 1fr);
        gap: 16px;
        min-height: 0;
        overflow: hidden;
        padding: 18px 20px 20px;
    }

    .obat-modal h3 {
        margin: 0;
        color: #13263f;
        font-size: 1rem;
        line-height: 1.2;
    }

    .obat-modal p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 0.76rem;
        line-height: 1.6;
    }

    .modal-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 12px;
        background: rgba(15, 23, 42, 0.06);
        color: #334155;
        font-size: 1.15rem;
        cursor: pointer;
        transition: background 160ms ease, transform 160ms ease;
    }

    .modal-close:hover {
        background: rgba(15, 23, 42, 0.1);
        transform: translateY(-1px);
    }

    .modal-summary {
        display: grid;
        gap: 12px;
        margin-bottom: 16px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .modal-summary-card {
        border-radius: 18px;
        padding: 13px 14px;
        border: 1px solid rgba(148, 163, 184, 0.14);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.94), rgba(255, 255, 255, 0.96));
    }

    .modal-summary-card span {
        display: block;
        color: #64748b;
        font-size: 0.61rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .modal-summary-card strong {
        display: block;
        margin-top: 5px;
        color: #1e293b;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .modal-empty {
        padding: 28px 14px;
        color: #64748b;
        font-size: 0.82rem;
        text-align: center;
    }

    .obat-modal .report-wrap {
        min-height: 0;
        max-height: 100%;
        overflow: auto;
    }

    .obat-modal .report-table {
        min-width: 760px;
        table-layout: fixed;
    }

    .obat-modal .report-table th,
    .obat-modal .report-table td {
        vertical-align: middle;
    }

    @media (max-width: 1100px) {
        .central-card {
            grid-template-columns: 1fr;
        }

        .filter-form {
            justify-content: stretch;
        }

        .central-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .central-summary {
            grid-template-columns: 1fr;
        }

        .field-wrap,
        .btn-filter {
            width: 100%;
            flex-basis: 100%;
        }

        .obat-modal .modal-dialog {
            width: calc(100vw - 20px);
            max-height: calc(100vh - 20px);
            margin: 10px auto;
        }

        .modal-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $clinicDetailSeed = $clinicDetailRows->mapWithKeys(function (array $clinicRow, string $clinicId) {
        return [
            (string) $clinicId => [
                'clinic_name' => $clinicRow['clinic_name'],
                'total_items' => (int) $clinicRow['total_items'],
                'total_jumlah' => (float) $clinicRow['total_jumlah'],
                'total_rupiah' => (float) $clinicRow['total_rupiah'],
                'rows' => collect($clinicRow['rows'] ?? [])->map(function (array $row) {
                    return [
                        'kode_brng' => $row['kode_brng'] ?? '-',
                        'nama_brng' => $row['nama_brng'] ?? 'Tanpa Nama Obat',
                        'total_jumlah' => (float) ($row['total_jumlah'] ?? 0),
                        'total_rupiah' => (float) ($row['total_rupiah'] ?? 0),
                    ];
                })->values()->all(),
            ],
        ];
    })->all();
@endphp

<div class="central-shell">
    <section class="central-card">
        <div class="central-copy">
            <p class="page-eyebrow">Pusat Laporan</p>
            <h1>Rekap Obat</h1>
            <p>Ringkasan obat dari database SIMRS klinik berdasarkan bulan aktif yang dipilih.</p>
        </div>

        <form method="GET" class="filter-form">
            @if($showClinicFilter && ! $isMasterView)
                <div class="field-wrap">
                    <label for="obat-clinic-id">Klinik Aktif</label>
                    <select id="obat-clinic-id" name="clinic_id">
                        <option value="all" @selected($selectedClinicFilter === 'all')>Semua Klinik</option>
                        @foreach($clinicOptions as $clinic)
                            <option value="{{ $clinic->id }}" @selected($selectedClinicFilter === (string) $clinic->id)>
                                {{ $clinic->nama_pendek ?: $clinic->nama_klinik }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="field-wrap">
                <label for="obat-bulan">Bulan</label>
                <input id="obat-bulan" type="month" name="bulan" value="{{ $selectedMonth }}">
            </div>

            <button type="submit" class="btn-filter">
                <svg viewBox="0 0 24 24">
                    <path d="M4 6h16"></path>
                    <path d="M7 12h10"></path>
                    <path d="M10 18h4"></path>
                </svg>
                Filter Data
            </button>
        </form>
    </section>

    <section class="central-summary">
        <article class="summary-card">
            <span>Periode Laporan</span>
            <strong>{{ $periodLabel }}</strong>
            <p>Perhitungan mengikuti tanggal registrasi pada bulan yang dipilih.</p>
        </article>
        <article class="summary-card">
            <span>Klinik Tampil</span>
            <strong>{{ $selectedClinicLabel }}</strong>
            <p>{{ $viewingAllClinics ? 'Agregasi pusat lintas klinik aktif.' : 'Data fokus pada klinik yang sedang dipilih.' }}</p>
        </article>
        <article class="summary-card">
            <span>Total Item Obat</span>
            <strong>{{ number_format($totalItems, 0, ',', '.') }}</strong>
            <p>Jumlah item obat unik yang masuk ke rekap bulan ini.</p>
        </article>
        <article class="summary-card is-strong">
            <span>Total Rupiah</span>
            <strong>Rp {{ number_format($totalRupiah, 0, ',', '.') }}</strong>
            <p>Total nilai farmasi dari {{ number_format($totalJumlah, 0, ',', '.') }} item obat.</p>
        </article>
    </section>

    @if($viewingAllClinics && $clinicSummaryRows->isNotEmpty())
        <section class="central-table-card">
            <div class="report-head">
                <div>
                    <h2>Ringkasan Total Obat Per Klinik</h2>
                    <p>Total harga obat setiap klinik ditampilkan langsung dari seluruh klinik yang berhasil dibaca pada periode ini.</p>
                </div>

                <div class="report-meta">
                    <span class="meta-pill">{{ number_format($clinicSummaryRows->count(), 0, ',', '.') }} klinik</span>
                </div>
            </div>

            <div class="summary-table-wrap">
                <table class="summary-table">
                    <colgroup>
                        <col style="width: 64px;">
                        <col style="width: 360px;">
                        <col style="width: 120px;">
                        <col style="width: 140px;">
                        <col style="width: 170px;">
                        <col style="width: 120px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Klinik</th>
                            <th class="text-right">Item Obat</th>
                            <th class="text-right">Total Jumlah</th>
                            <th class="text-right">Total Rupiah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clinicSummaryRows as $index => $clinicRow)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="clinic-main">{{ $clinicRow['clinic_name'] }}</div>
                                    <div class="clinic-sub">Ringkasan farmasi klinik pada {{ $periodLabel }}</div>
                                </td>
                                <td class="metric-cell">{{ number_format($clinicRow['total_items'], 0, ',', '.') }}</td>
                                <td class="metric-cell">{{ number_format($clinicRow['total_jumlah'], 0, ',', '.') }}</td>
                                <td class="metric-cell">Rp {{ number_format($clinicRow['total_rupiah'], 0, ',', '.') }}</td>
                                <td class="action-cell">
                                    <button
                                        type="button"
                                        class="summary-link"
                                        data-open-obat-detail
                                        data-clinic-id="{{ $clinicRow['clinic_id'] }}"
                                    >
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">Total Harga Obat Semua Klinik</td>
                            <td class="text-right">Rp {{ number_format($clinicSummaryRows->sum('total_rupiah'), 0, ',', '.') }}</td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    @endif

    @if($warnings->isNotEmpty())
        <section class="central-warning">
            <h2>Perlu Dicek</h2>
            <ul>
                @foreach($warnings as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @unless($viewingAllClinics)
        <section class="central-table-card">
            <div class="report-head">
                <div>
                    <h2>Detail Obat {{ $selectedClinicLabel }}</h2>
                    <p>{{ $successfulClinicCount > 0 ? $successfulClinicCount . ' koneksi klinik berhasil dibaca.' : 'Belum ada koneksi klinik yang berhasil dibaca.' }}</p>
                </div>
            </div>

            <div class="report-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode Barang</th>
                            <th>Nama Obat</th>
                            <th class="text-right">Total Jumlah</th>
                            <th class="text-right">Total Rupiah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="cell-code">{{ $row['kode_brng'] }}</td>
                                <td class="cell-name">{{ $row['nama_brng'] }}</td>
                                <td class="text-right">{{ number_format($row['total_jumlah'], 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($row['total_rupiah'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    Belum ada data obat yang tampil untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($rows->isNotEmpty())
                        <tfoot>
                            <tr>
                                <td colspan="4">Total Rupiah</td>
                                <td class="text-right">Rp {{ number_format($totalRupiah, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </section>
    @endunless
</div>

@if($viewingAllClinics)
    <div class="obat-modal" id="obatDetailModal" hidden>
        <div class="modal-overlay js-close-obat-modal"></div>

        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="obatDetailTitle" aria-describedby="obatDetailSubtitle">
            <div class="modal-header">
                <div>
                    <h3 id="obatDetailTitle">Detail Obat Klinik</h3>
                    <p id="obatDetailSubtitle">Rincian total obat per klinik pada periode yang dipilih.</p>
                </div>

                <button type="button" class="modal-close js-close-obat-modal" aria-label="Tutup modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="modal-summary">
                    <article class="modal-summary-card">
                        <span>Item Obat</span>
                        <strong id="obatDetailTotalItems">0</strong>
                    </article>
                    <article class="modal-summary-card">
                        <span>Total Jumlah</span>
                        <strong id="obatDetailTotalJumlah">0</strong>
                    </article>
                    <article class="modal-summary-card">
                        <span>Total Rupiah</span>
                        <strong id="obatDetailTotalRupiah">Rp 0</strong>
                    </article>
                </div>

                <div class="report-wrap">
                    <table class="report-table">
                        <colgroup>
                            <col style="width: 64px;">
                            <col style="width: 150px;">
                            <col>
                            <col style="width: 140px;">
                            <col style="width: 170px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Obat</th>
                                <th class="text-right">Total Jumlah</th>
                                <th class="text-right">Total Rupiah</th>
                            </tr>
                        </thead>
                        <tbody id="obatDetailTableBody">
                            <tr>
                                <td colspan="5" class="modal-empty">Pilih klinik dari ringkasan untuk melihat detail obatnya.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <span class="meta-pill">Periode {{ $periodLabel }}</span>
                <button type="button" class="summary-link js-close-obat-modal">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('obatDetailModal');
            const title = document.getElementById('obatDetailTitle');
            const subtitle = document.getElementById('obatDetailSubtitle');
            const totalItems = document.getElementById('obatDetailTotalItems');
            const totalJumlah = document.getElementById('obatDetailTotalJumlah');
            const totalRupiah = document.getElementById('obatDetailTotalRupiah');
            const tableBody = document.getElementById('obatDetailTableBody');
            const seed = @json($clinicDetailSeed);

            if (!modal || !title || !subtitle || !totalItems || !totalJumlah || !totalRupiah || !tableBody) {
                return;
            }

            const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));
            const formatCurrency = (value) => `Rp ${formatNumber(value)}`;

            const closeModal = () => {
                modal.hidden = true;
                document.body.style.overflow = '';
            };

            const openModal = (clinicId) => {
                const detail = seed[String(clinicId)];

                if (!detail) {
                    return;
                }

                title.textContent = `Detail Obat ${detail.clinic_name}`;
                subtitle.textContent = `Rincian obat klinik pada periode {{ $periodLabel }}.`;
                totalItems.textContent = formatNumber(detail.total_items);
                totalJumlah.textContent = formatNumber(detail.total_jumlah);
                totalRupiah.textContent = formatCurrency(detail.total_rupiah);

                if (!Array.isArray(detail.rows) || detail.rows.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="modal-empty">Belum ada detail obat untuk klinik ini.</td></tr>';
                } else {
                    tableBody.innerHTML = detail.rows.map((row, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td class="cell-code">${row.kode_brng ?? '-'}</td>
                            <td class="cell-name">${row.nama_brng ?? 'Tanpa Nama Obat'}</td>
                            <td class="text-right">${formatNumber(row.total_jumlah)}</td>
                            <td class="text-right">${formatCurrency(row.total_rupiah)}</td>
                        </tr>
                    `).join('');
                }

                modal.hidden = false;
                document.body.style.overflow = 'hidden';
            };

            document.querySelectorAll('[data-open-obat-detail]').forEach((button) => {
                button.addEventListener('click', () => {
                    openModal(button.dataset.clinicId);
                });
            });

            document.querySelectorAll('.js-close-obat-modal').forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.hidden) {
                    closeModal();
                }
            });
        })();
    </script>
@endif
@endsection
