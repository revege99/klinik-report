@extends('layouts.app')

@section('title', 'Rekap Penyakit Pusat | Klink Report')

@section('content')
<style>
    .central-shell {
        display: grid;
        gap: 18px;
    }

    .central-shell > * {
        min-width: 0;
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

    .summary-card.is-strong {
        background: linear-gradient(180deg, rgba(239, 246, 255, 0.96), rgba(219, 234, 254, 0.92));
        border-color: rgba(59, 130, 246, 0.18);
    }

    .summary-card.is-success {
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
        padding: 18px 18px 16px;
    }

    .report-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .report-head h2 {
        margin: 0;
        color: #1f2937;
        font-size: 0.98rem;
        font-weight: 700;
        letter-spacing: 0.03em;
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

    .report-wrap {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .report-table th {
        padding: 9px 13px 10px;
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
        padding: 10px 13px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        color: #1f2937;
        font-size: 0.8rem;
        vertical-align: middle;
    }

    .report-table tbody tr:hover {
        background: rgba(248, 250, 252, 0.88);
    }

    .cell-main {
        display: block;
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

    .text-right {
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .empty-state {
        padding: 28px 14px;
        color: #64748b;
        font-size: 0.82rem;
        text-align: center;
    }

    .report-table th:nth-child(3),
    .report-table td:nth-child(3),
    .report-table th:last-child,
    .report-table td:last-child {
        white-space: normal;
        word-break: break-word;
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
    }
</style>

@php
    $formatClinicNames = function (array $clinicNames) {
        $joinedNames = collect($clinicNames)
            ->filter()
            ->implode(', ');

        return \Illuminate\Support\Str::limit($joinedNames, 84);
    };
@endphp

<div class="central-shell">
    <section class="central-card">
        <div class="central-copy">
            <p class="page-eyebrow">Pusat Laporan</p>
            <h1>Rekap Penyakit</h1>
        </div>

        <form method="GET" class="filter-form">
            @if($showClinicFilter && ! $isMasterView)
                <div class="field-wrap">
                    <label for="penyakit-clinic-id">Klinik Aktif</label>
                    <select id="penyakit-clinic-id" name="clinic_id">
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
                <label for="penyakit-kelompok-usia">Kelompok Usia</label>
                <select id="penyakit-kelompok-usia" name="kelompok_usia">
                    <option value="all" @selected($selectedAgeFilter === 'all')>Semua Usia</option>
                    <option value="anak" @selected($selectedAgeFilter === 'anak')>Anak-anak</option>
                    <option value="dewasa" @selected($selectedAgeFilter === 'dewasa')>Dewasa</option>
                </select>
            </div>

            <div class="field-wrap">
                <label for="penyakit-bulan">Bulan</label>
                <input id="penyakit-bulan" type="month" name="bulan" value="{{ $selectedMonth }}">
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
        </article>
        <article class="summary-card">
            <span>Kelompok Usia</span>
            <strong>{{ $selectedAgeLabel }}</strong>
        </article>
        <article class="summary-card is-strong">
            <span>Total ICD</span>
            <strong>{{ number_format($totalDiseases, 0, ',', '.') }}</strong>
        </article>
        <article class="summary-card">
            <span>Total Kasus</span>
            <strong>{{ number_format($totalCases, 0, ',', '.') }}</strong>
        </article>
        <article class="summary-card">
            <span>Laki-laki</span>
            <strong>{{ number_format($totalMale, 0, ',', '.') }}</strong>
        </article>
        <article class="summary-card">
            <span>Perempuan</span>
            <strong>{{ number_format($totalFemale, 0, ',', '.') }}</strong>
        </article>
        <article class="summary-card is-success">
            <span>Anak-anak</span>
            <strong>{{ number_format($totalChildren, 0, ',', '.') }}</strong>
        </article>
        <article class="summary-card">
            <span>Dewasa</span>
            <strong>{{ number_format($totalAdults, 0, ',', '.') }}</strong>
        </article>
    </section>

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

    <section class="central-table-card">
        <div class="report-head">
            <div>
                <h2>ICD Terbanyak Sesuai Filter</h2>
            </div>

            <div class="report-meta">
                <span class="meta-pill">{{ number_format($rows->count(), 0, ',', '.') }} baris</span>
                <span class="meta-pill">{{ $selectedAgeLabel }}</span>
                <span class="meta-pill">{{ $selectedClinicLabel }}</span>
                @if($viewingAllClinics)
                    <span class="meta-pill">Lintas Klinik</span>
                @endif
                @if($successfulClinicCount > 0)
                    <span class="meta-pill">{{ number_format($successfulClinicCount, 0, ',', '.') }} koneksi</span>
                @endif
            </div>
        </div>

        <div class="report-wrap">
            <table class="report-table">
                <colgroup>
                    <col style="width: 56px;">
                    <col style="width: 86px;">
                    <col>
                    <col style="width: 106px;">
                    <col style="width: 106px;">
                    <col style="width: 106px;">
                    <col style="width: 106px;">
                    <col style="width: 106px;">
                    @if($viewingAllClinics)
                        <col style="width: 180px;">
                    @endif
                </colgroup>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ICD</th>
                        <th>Nama Penyakit</th>
                        <th class="text-right">Total Kasus</th>
                        <th class="text-right">Laki-laki</th>
                        <th class="text-right">Perempuan</th>
                        <th class="text-right">Anak-anak</th>
                        <th class="text-right">Dewasa</th>
                        @if($viewingAllClinics)
                            <th>Klinik</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="cell-main">{{ $row['icd'] ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="cell-main">{{ $row['nama_penyakit'] ?? '-' }}</span>
                            </td>
                            <td class="text-right">{{ number_format((int) ($row['total_kasus'] ?? 0), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format((int) ($row['total_laki_laki'] ?? 0), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format((int) ($row['total_perempuan'] ?? 0), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format((int) ($row['total_anak'] ?? 0), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format((int) ($row['total_dewasa'] ?? 0), 0, ',', '.') }}</td>
                            @if($viewingAllClinics)
                                <td>
                                    <span class="cell-main">{{ number_format((int) ($row['clinic_count'] ?? 0), 0, ',', '.') }} klinik</span>
                                    <span class="cell-sub">{{ $formatClinicNames($row['clinic_names'] ?? []) }}</span>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $viewingAllClinics ? 9 : 8 }}" class="empty-state">
                                Belum ada data penyakit yang tampil untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
