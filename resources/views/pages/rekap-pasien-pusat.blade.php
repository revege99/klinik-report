@extends('layouts.app')

@section('title', 'Rekap Pasien Pusat | Klink Report')

@section('content')
<style>
    .patient-shell {
        display: grid;
        gap: 18px;
    }

    .patient-card,
    .patient-summary,
    .patient-table-card {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 26px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(16px);
    }

    .patient-card {
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

    .patient-copy h1 {
        margin: 6px 0 0;
        color: #10233d;
        font-size: 1.32rem;
        line-height: 1.1;
    }

    .patient-copy p {
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

    .patient-summary {
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
        background: linear-gradient(180deg, rgba(239, 246, 255, 0.96), rgba(219, 234, 254, 0.92));
        border-color: rgba(59, 130, 246, 0.18);
    }

    .patient-table-card {
        padding: 22px;
    }

    .report-head {
        display: flex;
        align-items: center;
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

    .sync-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 8px 12px;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    .report-wrap {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        min-width: 940px;
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

    .report-table tbody tr:hover {
        background: rgba(248, 250, 252, 0.88);
    }

    .cell-main {
        font-weight: 700;
        color: #111827;
    }

    .cell-sub {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 0.72rem;
        line-height: 1.5;
    }

    .empty-state {
        padding: 28px 14px;
        color: #64748b;
        font-size: 0.82rem;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .patient-card {
            grid-template-columns: 1fr;
        }

        .filter-form {
            justify-content: stretch;
        }

        .patient-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .patient-summary {
            grid-template-columns: 1fr;
        }

        .field-wrap,
        .btn-filter {
            width: 100%;
            flex-basis: 100%;
        }
    }
</style>

@php
    $displayTimezone = config('app.display_timezone', config('app.timezone'));
@endphp

<div class="patient-shell">
    <section class="patient-card">
        <div class="patient-copy">
            <p class="page-eyebrow">Pusat Laporan</p>
            <h1>Rekap Pasien</h1>
            <p>Snapshot pasien hasil update rekap dari masing-masing klinik sesuai bulan yang dipilih.</p>
        </div>

        <form method="GET" class="filter-form">
            @if($showClinicFilter)
                <div class="field-wrap">
                    <label for="rekap-pasien-clinic-id">Klinik Aktif</label>
                    <select id="rekap-pasien-clinic-id" name="clinic_id">
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
                <label for="rekap-pasien-bulan">Bulan</label>
                <input id="rekap-pasien-bulan" type="month" name="bulan" value="{{ $selectedMonth }}">
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

    <section class="patient-summary">
        <article class="summary-card">
            <span>Periode Laporan</span>
            <strong>{{ $periodLabel }}</strong>
            <p>Rekap lokal pasien yang sudah pernah di-update dari transaksi harian.</p>
        </article>
        <article class="summary-card">
            <span>Klinik Tampil</span>
            <strong>{{ $selectedClinicLabel }}</strong>
            <p>{{ $viewingAllClinics ? 'Menampilkan akumulasi snapshot rekap pasien lintas klinik.' : 'Data fokus pada satu klinik yang dipilih.' }}</p>
        </article>
        <article class="summary-card">
            <span>Total Baris</span>
            <strong>{{ number_format($totalRows, 0, ',', '.') }}</strong>
            <p>Total kunjungan pasien yang masuk ke snapshot bulan aktif.</p>
        </article>
        <article class="summary-card is-strong">
            <span>Pasien Unik</span>
            <strong>{{ number_format($uniquePatients, 0, ',', '.') }}</strong>
            <p>{{ number_format($activeLayananCount, 0, ',', '.') }} layanan aktif terpakai di snapshot bulan ini.</p>
        </article>
    </section>

    <section class="patient-table-card">
        <div class="report-head">
            <div>
                <h2>Data Rekap Pasien</h2>
                <p>Data berasal dari tabel pusat `rekap_pasien` yang sudah tersimpan per klinik.</p>
            </div>

            @if($lastRekapUpdate)
                <span class="sync-pill">
                    Update terakhir
                    {{ $lastRekapUpdate->synced_at?->timezone($displayTimezone)->translatedFormat('d M Y · H:i') }}
                </span>
            @endif
        </div>

        <div class="report-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Rawat</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Layanan</th>
                        @if($viewingAllClinics)
                            <th>Klinik</th>
                        @endif
                        <th>Sinkron</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ optional($row->tanggal)->translatedFormat('d/m/Y') }}</td>
                            <td>
                                <span class="cell-main">{{ $row->no_rawat }}</span>
                            </td>
                            <td>{{ $row->no_rm ?: '-' }}</td>
                            <td>
                                <span class="cell-main">{{ $row->nama_pasien ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="cell-main">{{ $row->masterLayanan?->nama_layanan ?: ($row->layanan_medis ?: '-') }}</span>
                                <span class="cell-sub">{{ $row->masterLayanan?->kode_layanan ?: ($row->layanan_medis ?: '-') }}</span>
                            </td>
                            @if($viewingAllClinics)
                                <td>{{ $row->clinicProfile?->nama_pendek ?: $row->clinicProfile?->nama_klinik ?: '-' }}</td>
                            @endif
                            <td>
                                <span class="cell-main">
                                    {{ $row->synced_at?->timezone($displayTimezone)->translatedFormat('d M Y') ?: '-' }}
                                </span>
                                <span class="cell-sub">
                                    {{ $row->synced_at?->timezone($displayTimezone)->translatedFormat('H:i') ?: 'Belum sinkron' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $viewingAllClinics ? 7 : 6 }}" class="empty-state">
                                Belum ada data rekap pasien untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
