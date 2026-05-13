@extends('layouts.app')

@section('title', ($pageTitle ?? 'Rekap Farmasi Pusat') . ' | Klink Report')

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
        min-width: 170px;
        flex: 0 0 170px;
        flex-direction: column;
        gap: 6px;
    }

    .field-wrap.is-date {
        min-width: 156px;
        flex-basis: 156px;
    }

    .field-wrap.is-compact {
        min-width: 122px;
        flex-basis: 122px;
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

    .report-wrap {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        min-width: var(--report-table-min-width, 1040px);
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

    .cell-code {
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
    }

    .cell-name {
        font-weight: 700;
        color: #111827;
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
        .field-wrap.is-date,
        .field-wrap.is-compact,
        .btn-filter {
            width: 100%;
            flex-basis: 100%;
        }
    }
</style>

@php
    $formatNumber = function ($value) {
        $numericValue = (float) $value;
        $decimals = abs($numericValue - round($numericValue)) < 0.00001 ? 0 : 2;

        return number_format($numericValue, $decimals, ',', '.');
    };

    $formatCurrency = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $tableMinWidth = $tableMinWidth ?? '1040px';
@endphp

<div class="central-shell" style="--report-table-min-width: {{ $tableMinWidth }};">
    <section class="central-card">
        <div class="central-copy">
            <p class="page-eyebrow">Pusat Laporan</p>
            <h1>{{ $pageTitle }}</h1>
            <p>{{ $pageDescription }}</p>
        </div>

        <form method="GET" class="filter-form">
            @if($showClinicFilter)
                <div class="field-wrap">
                    <label for="farmasi-clinic-id">Klinik Aktif</label>
                    <select id="farmasi-clinic-id" name="clinic_id">
                        @if($allowAllClinicOption ?? true)
                            <option value="all" @selected($selectedClinicFilter === 'all')>Semua Klinik</option>
                        @endif
                        @foreach($clinicOptions as $clinic)
                            <option value="{{ $clinic->id }}" @selected($selectedClinicFilter === (string) $clinic->id)>
                                {{ $clinic->nama_pendek ?: $clinic->nama_klinik }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($filterMode === 'stock')
                <div class="field-wrap is-compact">
                    <label for="farmasi-kd-bangsal">Kode Bangsal</label>
                    <input id="farmasi-kd-bangsal" type="text" name="kd_bangsal" value="{{ $selectedBangsalCode }}" maxlength="10">
                </div>
            @else
                <div class="field-wrap is-date">
                    <label for="farmasi-tanggal-awal">Tanggal Awal</label>
                    <input id="farmasi-tanggal-awal" type="date" name="tanggal_awal" value="{{ $selectedStartDate }}">
                </div>

                <div class="field-wrap is-date">
                    <label for="farmasi-tanggal-akhir">Tanggal Akhir</label>
                    <input id="farmasi-tanggal-akhir" type="date" name="tanggal_akhir" value="{{ $selectedEndDate }}">
                </div>
            @endif

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
        @foreach($summaryCards as $card)
            <article class="summary-card {{ !empty($card['accent']) ? 'is-strong' : '' }}">
                <span>{{ $card['label'] }}</span>
                <strong>{{ $card['value'] }}</strong>
                <p>{{ $card['description'] }}</p>
            </article>
        @endforeach
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
                <h2>{{ $tableTitle }}</h2>
                <p>{{ $tableDescription }}</p>
            </div>

            <div class="report-meta">
                <span class="meta-pill">{{ number_format($rows->count(), 0, ',', '.') }} baris</span>
                <span class="meta-pill">{{ number_format($successfulClinicCount, 0, ',', '.') }} koneksi</span>
                @if($filterMode === 'stock' && filled($selectedBangsalCode))
                    <span class="meta-pill">Bangsal {{ $selectedBangsalCode }}</span>
                @else
                    <span class="meta-pill">{{ $periodLabel }}</span>
                @endif
            </div>
        </div>

        <div class="report-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        @foreach($columns as $column)
                            <th class="{{ ($column['align'] ?? 'left') === 'right' ? 'text-right' : '' }}">
                                {{ $column['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @foreach($columns as $column)
                                @php
                                    $value = data_get($row, $column['key']);
                                    $type = $column['type'] ?? 'text';
                                    $alignClass = ($column['align'] ?? 'left') === 'right' ? 'text-right' : '';
                                    $cellClass = trim(($column['class'] ?? '') . ' ' . $alignClass);
                                @endphp
                                <td class="{{ $cellClass }}">
                                    @if($type === 'currency')
                                        {{ $formatCurrency($value) }}
                                    @elseif($type === 'number')
                                        {{ $formatNumber($value) }}
                                    @elseif($type === 'date')
                                        {{ filled($value) ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-' }}
                                    @else
                                        {{ filled($value) ? $value : '-' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="empty-state">
                                {{ $emptyMessage }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
