@extends('layouts.app')

@section('title', 'Input Klaim BPJS | Klink Report')

@section('content')
<style>
    .klaim-shell {
        display: grid;
        gap: 18px;
    }

    .klaim-shell > * {
        min-width: 0;
    }

    .hero-card,
    .message-card,
    .compose-card,
    .preview-card,
    .table-card,
    .warning-card {
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
        font-size: 1.42rem;
        line-height: 1.1;
    }

    .hero-copy p {
        margin: 7px 0 0;
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.7;
    }

    .hero-filter-form {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: flex-end;
        gap: 10px;
    }

    .field-wrap {
        display: flex;
        min-width: 170px;
        flex: 0 1 190px;
        flex-direction: column;
        gap: 6px;
    }

    .field-wrap.is-wide {
        flex-basis: 240px;
    }

    .field-wrap label,
    .form-group label,
    .table-head p,
    .hint-label {
        color: #64748b;
        font-size: 0.66rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .field-wrap input,
    .field-wrap select,
    .form-group input,
    .form-group textarea {
        width: 100%;
        height: 40px;
        border: 1px solid #d7e1ef;
        border-radius: 14px;
        padding: 9px 12px;
        background: #f8fafc;
        color: #10233d;
        font-size: 0.81rem;
        transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
    }

    .form-group textarea {
        height: auto;
        min-height: 108px;
        resize: vertical;
        padding-block: 12px;
    }

    .field-wrap input:focus,
    .field-wrap select:focus,
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #60a5fa;
        background: white;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.14);
    }

    .message-card,
    .compose-card,
    .preview-card,
    .table-card,
    .warning-card {
        padding: 24px;
    }

    .message-card.success {
        border-color: rgba(34, 197, 94, 0.2);
        background: linear-gradient(180deg, rgba(240, 253, 244, 0.96), rgba(236, 253, 245, 0.92));
        color: #166534;
    }

    .message-card.error {
        border-color: rgba(239, 68, 68, 0.18);
        background: linear-gradient(180deg, rgba(254, 242, 242, 0.96), rgba(254, 226, 226, 0.92));
        color: #991b1b;
    }

    .message-card p {
        margin: 0;
        font-weight: 700;
    }

    .message-card ul {
        margin: 12px 0 0;
        padding-left: 18px;
    }

    .warning-card {
        border-color: rgba(245, 158, 11, 0.2);
        background: linear-gradient(180deg, rgba(255, 251, 235, 0.96), rgba(254, 243, 199, 0.88));
    }

    .warning-card h2 {
        margin: 0 0 8px;
        color: #92400e;
        font-size: 0.96rem;
    }

    .warning-card p,
    .warning-card li {
        color: #9a3412;
        font-size: 0.79rem;
        line-height: 1.65;
    }

    .warning-card ul {
        margin: 8px 0 0;
        padding-left: 18px;
    }

    .content-grid {
        display: grid;
        gap: 18px;
        grid-template-columns: minmax(340px, 430px) minmax(0, 1fr);
    }

    .section-head,
    .table-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .section-head h2,
    .table-head h2 {
        margin: 0;
        color: #13263f;
        font-size: 1rem;
        line-height: 1.2;
    }

    .section-head p,
    .table-head span {
        display: block;
        margin: 4px 0 0;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.7;
    }

    .panel-chip,
    .status-chip,
    .soft-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 0.67rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .panel-chip {
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
    }

    .status-chip.is-ready {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }

    .status-chip.is-warning {
        background: rgba(245, 158, 11, 0.14);
        color: #b45309;
    }

    .status-chip.is-danger {
        background: rgba(239, 68, 68, 0.12);
        color: #b91c1c;
    }

    .soft-chip {
        background: rgba(15, 23, 42, 0.05);
        color: #334155;
    }

    .claim-form {
        display: grid;
        gap: 16px;
    }

    .form-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group.is-full {
        grid-column: 1 / -1;
    }

    .readonly-display {
        background: linear-gradient(180deg, #f8fbff, #eef4ff) !important;
        color: #1d4ed8 !important;
        font-weight: 700;
        cursor: default;
    }

    .helper-note {
        padding: 14px 16px;
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(239, 246, 255, 0.96), rgba(224, 242, 254, 0.9));
        border: 1px solid rgba(56, 189, 248, 0.14);
        color: #1e3a5f;
        font-size: 0.78rem;
        line-height: 1.72;
    }

    .helper-note code {
        padding: 2px 6px;
        border-radius: 8px;
        background: rgba(15, 23, 42, 0.08);
        color: #0f172a;
        font-size: 0.74rem;
        font-weight: 700;
    }

    .form-actions,
    .row-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        border-radius: 16px;
        padding: 12px 18px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
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

    .btn-muted {
        background: #f1f5f9;
        color: #334155;
    }

    .btn-ghost {
        padding: 10px 14px;
        border-radius: 14px;
        background: #f8fafc;
        color: #1d4ed8;
        border: 1px solid rgba(37, 99, 235, 0.12);
        box-shadow: none;
    }

    .btn-danger {
        padding: 10px 14px;
        border-radius: 14px;
        background: rgba(220, 38, 38, 0.08);
        color: #b91c1c;
        border: 1px solid rgba(220, 38, 38, 0.16);
        box-shadow: none;
    }

    .btn[disabled] {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .summary-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-bottom: 18px;
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
        font-size: 0.96rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .summary-card p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 0.74rem;
        line-height: 1.55;
    }

    .summary-card.is-emerald {
        background: linear-gradient(180deg, rgba(236, 253, 245, 0.96), rgba(220, 252, 231, 0.9));
        border-color: rgba(16, 185, 129, 0.18);
    }

    .summary-card.is-amber {
        background: linear-gradient(180deg, rgba(255, 251, 235, 0.96), rgba(254, 243, 199, 0.88));
        border-color: rgba(245, 158, 11, 0.18);
    }

    .summary-card.is-rose {
        background: linear-gradient(180deg, rgba(255, 241, 242, 0.96), rgba(255, 228, 230, 0.88));
        border-color: rgba(244, 63, 94, 0.18);
    }

    .summary-card.is-sky {
        background: linear-gradient(180deg, rgba(240, 249, 255, 0.96), rgba(224, 242, 254, 0.88));
        border-color: rgba(56, 189, 248, 0.16);
    }

    .summary-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }

    .report-wrap {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
    }

    .report-table th,
    .report-table td {
        padding: 12px 12px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        vertical-align: top;
    }

    .report-table th {
        color: #526277;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
    }

    .report-table td {
        color: #1f2937;
        font-size: 0.8rem;
        line-height: 1.6;
    }

    .report-table tfoot td {
        border-top: 1px solid rgba(148, 163, 184, 0.22);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(241, 245, 249, 0.96));
        font-weight: 700;
    }

    .code-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 8px;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.12);
        color: #1d4ed8;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .row-title {
        color: #13263f;
        font-size: 0.83rem;
        font-weight: 700;
    }

    .row-subtitle {
        margin-top: 4px;
        color: #64748b;
        font-size: 0.74rem;
        line-height: 1.6;
    }

    .table-card .empty-state,
    .compose-card .empty-state,
    .preview-card .empty-state {
        padding: 20px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(241, 245, 249, 0.96));
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.75;
        text-align: center;
    }

    .inline-action-form {
        margin: 0;
    }

    .allocation-state {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .allocation-state.is-ready {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }

    .allocation-state.is-muted {
        background: rgba(148, 163, 184, 0.14);
        color: #475569;
    }

    .allocation-state.is-rose {
        background: rgba(244, 63, 94, 0.12);
        color: #be123c;
    }

    @media (max-width: 1180px) {
        .hero-card,
        .content-grid {
            grid-template-columns: 1fr;
        }

        .hero-filter-form {
            justify-content: flex-start;
        }
    }

    @media (max-width: 760px) {
        .hero-card,
        .message-card,
        .compose-card,
        .preview-card,
        .table-card,
        .warning-card {
            border-radius: 24px;
            padding: 18px;
        }

        .field-wrap,
        .field-wrap.is-wide,
        .summary-grid,
        .form-grid {
            min-width: 0;
            grid-template-columns: 1fr;
        }

        .field-wrap {
            flex-basis: 100%;
        }

        .summary-grid {
            display: grid;
        }

        .form-group.is-full {
            grid-column: span 1;
        }

        .section-head,
        .table-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

@php
    $selectedMonthDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
    $periodLabel = $selectedMonthDate->locale('id')->translatedFormat('F Y');
    $previewRows = collect($preview['alokasi_rows'] ?? []);
    $hasExistingClaim = filled($existingClaim);
    $canCreateClaim = filled($selectedClinicId) && ! $hasExistingClaim;
    $canUpdateExistingClaim = filled($selectedClinicId) && $hasExistingClaim && $canUpdateClaim;
    $formLockedBecauseExisting = filled($selectedClinicId) && $hasExistingClaim && ! $canUpdateClaim;
    $basisPajakTotal = (float) $previewRows
        ->where('basis_pajak_obat', true)
        ->sum('nominal_alokasi');
    $selisihArah = $preview['selisih_arah'] ?? null;
    $selisihLabel = $selisihArah === 'debet'
        ? 'Masuk Debet'
        : ($selisihArah === 'kredit' ? 'Masuk Kredit' : 'Balance');
    $komponenSelisih = $preview['komponen_selisih'] ?? null;
    $claimFormAction = $canUpdateExistingClaim
        ? route('input-klaim-bpjs.update', $existingClaim)
        : route('input-klaim-bpjs.store');
    $claimFormButtonLabel = $canUpdateExistingClaim ? 'Simpan Perubahan' : 'Simpan Klaim BPJS';
    $allocationSeed = $previewRows->map(function ($row) {
        return [
            'basis_nominal' => (float) ($row['basis_nominal'] ?? 0),
            'persentase' => (float) ($row['persentase'] ?? 0),
            'basis_pajak_obat' => (bool) ($row['basis_pajak_obat'] ?? false),
        ];
    })->values()->all();
    $bpjsSummarySeed = [
        'total_versi_klinik' => (float) ($preview['total_versi_klinik'] ?? 0),
        'total_komponen_acuan' => (float) ($preview['total_komponen_acuan'] ?? 0),
        'jumlah_komponen_acuan' => (int) ($preview['jumlah_komponen_acuan'] ?? 0),
    ];
@endphp

<div class="klaim-shell">
    <section class="hero-card">
        <div class="hero-copy">
            <p class="page-eyebrow">Keuangan Klinik</p>
            <h1>Input Klaim BPJS</h1>
            <p>Simpan satu klaim BPJS per bulan per klinik, lalu pantau selisih, komponen acuan, dan alokasi nominalnya secara langsung.</p>
        </div>

        <form method="GET" action="{{ route('input-klaim-bpjs') }}" class="hero-filter-form">
            <div class="field-wrap">
                <label for="bpjs-bulan">Periode</label>
                <input id="bpjs-bulan" type="month" name="bulan" value="{{ $selectedMonth }}">
            </div>

            @if ($showClinicFilter)
                <div class="field-wrap is-wide">
                    <label for="bpjs-clinic-filter">Klinik Aktif</label>
                    <select id="bpjs-clinic-filter" name="clinic_id">
                        <option value="" @selected($selectedClinicFilter === '')>Pilih Klinik</option>
                        <option value="all" @selected($selectedClinicFilter === 'all')>Semua Klinik</option>
                        @foreach ($clinicOptions as $clinicOption)
                            <option value="{{ $clinicOption->id }}" @selected((string) $clinicOption->id === $selectedClinicFilter)>
                                {{ $clinicOption->kode_klinik }} · {{ $clinicOption->nama_klinik }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit" class="btn btn-primary filter-submit">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 5h16"></path>
                    <path d="M7 12h10"></path>
                    <path d="M10 19h4"></path>
                </svg>
                Filter Data
            </button>
        </form>
    </section>

    @if (session('success'))
        <section class="message-card success">
            <p>{{ session('success') }}</p>
        </section>
    @endif

    @if (session('error'))
        <section class="message-card error">
            <p>{{ session('error') }}</p>
        </section>
    @endif

    @if ($errors->any())
        <section class="message-card error">
            <p>Masih ada data klaim yang perlu diperbaiki.</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if (! $komponenSelisih)
        <section class="warning-card">
            <h2>Komponen penyesuaian klaim belum diatur</h2>
            <p>
                Sistem belum menemukan komponen transaksi yang ditandai sebagai <strong>Penyesuaian Klaim BPJS</strong>.
                Anda tetap bisa menyimpan klaim, tetapi baris sistem untuk selisih belum punya kode dinamis.
            </p>
            <ul>
                <li>Buka menu <strong>Kode Yayasan → Komponen Transaksi</strong>.</li>
                <li>Pilih satu komponen lalu set <strong>Peran Sistem = Penyesuaian Klaim BPJS</strong>.</li>
            </ul>
        </section>
    @endif

    <div class="content-grid">
        <section class="compose-card">
            <div class="section-head">
                <div>
                    <h2>{{ $hasExistingClaim ? 'Kelola Klaim BPJS' : 'Input Klaim Bulanan' }}</h2>
                    <p>Satu klinik hanya boleh memiliki satu data klaim BPJS untuk setiap periode bulanan.</p>
                </div>
                <span class="panel-chip">{{ $hasExistingClaim ? 'Mode Tersimpan' : 'Mode Baru' }}</span>
            </div>

            @if ($viewingAllClinics)
                <div class="empty-state">
                    Mode <strong>Semua Klinik</strong> hanya menampilkan daftar data. Pilih satu klinik agar form klaim bulanan bisa dipakai.
                </div>
            @elseif (! $selectedClinicId)
                <div class="empty-state">
                    Pilih klinik aktif terlebih dahulu untuk mulai menghitung dan menyimpan klaim BPJS bulanan.
                </div>
            @else
                <form method="POST" action="{{ $claimFormAction }}" class="claim-form" id="bpjsClaimForm">
                    @csrf
                    @if ($canUpdateExistingClaim)
                        @method('PUT')
                    @endif

                    <input type="hidden" name="clinic_profile_id" value="{{ $selectedClinicId }}">
                    <input type="hidden" name="bulan" value="{{ $selectedMonth }}">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="claim-clinic-label">Klinik</label>
                            <input id="claim-clinic-label" type="text" value="{{ $selectedClinicLabel }}" class="readonly-display" readonly>
                        </div>

                        <div class="form-group">
                            <label for="claim-period-label">Periode</label>
                            <input id="claim-period-label" type="text" value="{{ $periodLabel }}" class="readonly-display" readonly>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_terima">Tanggal Terima</label>
                            <input id="tanggal_terima" type="date" name="tanggal_terima" value="{{ $selectedClaimDate }}">
                        </div>

                        <div class="form-group">
                            <label for="total_klaim">Total Klaim BPJS</label>
                            <input
                                id="total_klaim"
                                type="text"
                                name="total_klaim"
                                class="js-currency-input"
                                inputmode="decimal"
                                autocomplete="off"
                                value="{{ number_format((float) $selectedClaimAmount, 0, ',', '.') }}"
                            >
                        </div>

                        <div class="form-group">
                            <label for="claim-total-klinik">Total Versi Klinik BPJS</label>
                            <input
                                id="claim-total-klinik"
                                type="text"
                                value="Rp {{ number_format((float) ($preview['total_versi_klinik'] ?? 0), 0, ',', '.') }}"
                                class="readonly-display js-live-klinik-total"
                                readonly
                            >
                        </div>

                        <div class="form-group">
                            <label for="claim-selisih-label">Selisih Klaim</label>
                            <input
                                id="claim-selisih-label"
                                type="text"
                                value="Rp {{ number_format((float) abs($preview['selisih_nominal'] ?? 0), 0, ',', '.') }} · {{ $selisihLabel }}"
                                class="readonly-display js-live-selisih-label"
                                readonly
                            >
                        </div>

                        <div class="form-group is-full">
                            <label for="keterangan">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" rows="4" placeholder="Tambahkan catatan penerimaan klaim, koreksi, atau informasi penting lain.">{{ $selectedNote }}</textarea>
                        </div>
                    </div>

                    @if ($formLockedBecauseExisting)
                        <div class="helper-note">
                            Data klaim untuk periode ini sudah tersimpan. Karena akun ini tidak punya akses update, silakan hapus data lama terlebih dahulu jika ingin input ulang.
                        </div>
                    @else
                        <div class="helper-note">
                            Nilai <code>selisih</code> dihitung otomatis dari <code>total klaim BPJS - total versi klinik BPJS</code>.
                            Kode komponen sistem yang dipakai saat ini:
                            <code>{{ $komponenSelisih?->kode_komponen ?: 'Belum Diatur' }}</code>.
                        </div>
                    @endif

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            @if (! $canCreateClaim && ! $canUpdateExistingClaim) disabled @endif
                        >
                            {{ $claimFormButtonLabel }}
                        </button>

                        @if ($hasExistingClaim)
                            <a href="{{ route('input-klaim-bpjs', ['bulan' => $selectedMonth, 'clinic_id' => $selectedClinicId]) }}" class="btn btn-muted">
                                Muat Ulang Data
                            </a>
                        @endif
                    </div>
                </form>
            @endif
        </section>

        <section class="preview-card">
            <div class="section-head">
                <div>
                    <h2>Selisih, Persentase, dan Alokasi</h2>
                    <p>Persentase dibentuk dinamis dari total komponen transaksi BPJS yang ikut alokasi pada periode {{ $periodLabel }}.</p>
                </div>
                <span class="status-chip {{ ($preview['jumlah_komponen_acuan'] ?? 0) > 0 ? 'is-ready' : 'is-warning' }}">
                    {{ ($preview['jumlah_komponen_acuan'] ?? 0) > 0 ? 'Acuan Siap' : 'Belum Ada Acuan' }}
                </span>
            </div>

            <div class="summary-grid">
                <article class="summary-card">
                    <span>Total Klaim BPJS</span>
                    <strong class="js-live-total-klaim">Rp {{ number_format((float) ($preview['total_klaim'] ?? 0), 0, ',', '.') }}</strong>
                    <p>Nilai penerimaan bulanan yang akan disimpan untuk klinik aktif.</p>
                </article>

                <article class="summary-card is-emerald">
                    <span>Total Versi Klinik</span>
                    <strong class="js-live-versi-klinik">Rp {{ number_format((float) ($preview['total_versi_klinik'] ?? 0), 0, ',', '.') }}</strong>
                    <p>Total transaksi pasien BPJS versi klinik untuk periode yang sama.</p>
                </article>

                <article class="summary-card {{ ($preview['selisih_nominal'] ?? 0) >= 0 ? 'is-amber' : 'is-rose' }}">
                    <span>Selisih Klaim</span>
                    <strong class="js-live-selisih">Rp {{ number_format((float) abs($preview['selisih_nominal'] ?? 0), 0, ',', '.') }}</strong>
                    <p class="js-live-selisih-direction">{{ $selisihLabel }}</p>
                </article>

                <article class="summary-card is-sky">
                    <span>Selisih Persen</span>
                    <strong class="js-live-selisih-persen">{{ number_format((float) ($preview['selisih_persen'] ?? 0), 2, ',', '.') }}%</strong>
                    <p>Persentase selisih terhadap total versi klinik BPJS.</p>
                </article>

                <article class="summary-card">
                    <span>Komponen Acuan</span>
                    <strong class="js-live-komponen-count">{{ number_format((int) ($preview['jumlah_komponen_acuan'] ?? 0), 0, ',', '.') }} Komponen</strong>
                    <p>Total basis transaksi yang dipakai untuk membentuk persentase alokasi.</p>
                </article>

                <article class="summary-card is-sky">
                    <span>Alokasi Basis Pajak Obat</span>
                    <strong class="js-live-pajak-obat">Rp {{ number_format($basisPajakTotal, 0, ',', '.') }}</strong>
                    <p>Total alokasi dari komponen yang ditandai sebagai basis pajak obat.</p>
                </article>
            </div>

            <div class="summary-meta">
                <span class="soft-chip">
                    Komponen Selisih:
                    {{ $komponenSelisih?->kode_komponen ?: '-' }}{{ $komponenSelisih ? ' · ' . $komponenSelisih->nama_komponen : ' Belum Diatur' }}
                </span>
                <span class="soft-chip">
                    Total Basis Acuan:
                    <strong class="js-live-total-acuan">Rp {{ number_format((float) ($preview['total_komponen_acuan'] ?? 0), 0, ',', '.') }}</strong>
                </span>
            </div>

            @if ($previewRows->isEmpty())
                <div class="empty-state">
                    Belum ada komponen transaksi BPJS yang bisa dijadikan acuan alokasi untuk periode ini.
                </div>
            @else
                <div class="report-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Komponen</th>
                                <th>Basis Klinik</th>
                                <th>Persentase</th>
                                <th>Alokasi Klaim</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($previewRows as $index => $row)
                                <tr data-allocation-row data-row-index="{{ $index }}">
                                    <td>
                                        <span class="code-badge">{{ $row['kode_komponen'] }}</span>
                                    </td>
                                    <td>
                                        <div class="row-title">{{ $row['nama_komponen'] }}</div>
                                        <div class="row-subtitle">
                                            {{ !empty($row['is_acuan']) ? 'Dipakai dalam basis persentase bulan ini.' : 'Belum punya nilai basis pada periode ini.' }}
                                        </div>
                                    </td>
                                    <td class="js-alloc-basis">Rp {{ number_format((float) $row['basis_nominal'], 0, ',', '.') }}</td>
                                    <td class="js-alloc-percent">{{ number_format((float) $row['persentase'], 2, ',', '.') }}%</td>
                                    <td class="js-alloc-nominal">Rp {{ number_format((float) $row['nominal_alokasi'], 0, ',', '.') }}</td>
                                    <td>
                                        <div class="row-actions">
                                            <span class="allocation-state {{ !empty($row['is_acuan']) ? 'is-ready' : 'is-muted' }}">
                                                {{ !empty($row['is_acuan']) ? 'Acuan Aktif' : 'Nilai Nol' }}
                                            </span>
                                            @if (!empty($row['basis_pajak_obat']))
                                                <span class="allocation-state is-rose">Basis Pajak Obat</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td class="js-total-basis">Rp {{ number_format((float) ($preview['total_komponen_acuan'] ?? 0), 0, ',', '.') }}</td>
                                <td>100,00%</td>
                                <td class="js-total-alokasi">Rp {{ number_format((float) ($preview['total_klaim'] ?? 0), 0, ',', '.') }}</td>
                                <td>{{ number_format((int) ($preview['jumlah_komponen_acuan'] ?? 0), 0, ',', '.') }} Komponen</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </section>
    </div>

    <section class="table-card">
        <div class="table-head">
            <div>
                <h2>Data Klaim Tersimpan</h2>
                <span>Daftar klaim BPJS bulanan yang sudah masuk untuk periode {{ $periodLabel }}.</span>
            </div>
            <span class="panel-chip">{{ number_format($claims->count(), 0, ',', '.') }} Data</span>
        </div>

        @if ($claims->isEmpty())
            <div class="empty-state">
                Belum ada data klaim BPJS yang tersimpan untuk periode {{ $periodLabel }}.
            </div>
        @else
            <div class="report-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Klinik</th>
                            <th>Periode</th>
                            <th>Total Klaim</th>
                            <th>Versi Klinik</th>
                            <th>Selisih</th>
                            <th>Persen</th>
                            <th>Komponen Acuan</th>
                            <th>Komponen Selisih</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($claims as $claim)
                            <tr>
                                <td>
                                    <div class="row-title">{{ $claim->clinicProfile?->nama_pendek ?: $claim->clinicProfile?->nama_klinik ?: '-' }}</div>
                                    <div class="row-subtitle">{{ $claim->clinicProfile?->kode_klinik ?: '-' }}</div>
                                </td>
                                <td>
                                    <div class="row-title">{{ \Carbon\Carbon::create($claim->tahun, $claim->bulan, 1)->locale('id')->translatedFormat('F Y') }}</div>
                                    <div class="row-subtitle">
                                        Tgl terima {{ optional($claim->tanggal_terima)->format('d/m/Y') ?: '-' }}
                                    </div>
                                </td>
                                <td>Rp {{ number_format((float) $claim->total_klaim, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format((float) $claim->total_versi_klinik, 0, ',', '.') }}</td>
                                <td>
                                    <div class="row-title">
                                        Rp {{ number_format((float) abs($claim->selisih_nominal), 0, ',', '.') }}
                                    </div>
                                    <div class="row-subtitle">
                                        {{ $claim->selisih_arah === 'debet' ? 'Masuk Debet' : ($claim->selisih_arah === 'kredit' ? 'Masuk Kredit' : 'Balance') }}
                                    </div>
                                </td>
                                <td>{{ number_format((float) $claim->selisih_persen, 2, ',', '.') }}%</td>
                                <td>
                                    <div class="row-title">{{ number_format((int) $claim->jumlah_komponen_acuan, 0, ',', '.') }} komponen</div>
                                    <div class="row-subtitle">
                                        Basis Rp {{ number_format((float) $claim->total_komponen_acuan, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="row-title">
                                        {{ $claim->masterKomponenSelisih?->kode_komponen ?: '-' }}
                                        @if ($claim->masterKomponenSelisih)
                                            · {{ $claim->masterKomponenSelisih->nama_komponen }}
                                        @endif
                                    </div>
                                    <div class="row-subtitle">
                                        {{ optional($claim->updated_at)->timezone(config('app.timezone'))->format('d/m/Y H:i') ?: '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="row-title">{{ $claim->user?->name ?: '-' }}</div>
                                    <div class="row-subtitle">
                                        {{ $claim->user?->pegawaiProfile?->jabatan ?: 'Petugas' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <a
                                            href="{{ route('input-klaim-bpjs', ['bulan' => \Carbon\Carbon::create($claim->tahun, $claim->bulan, 1)->format('Y-m'), 'clinic_id' => $claim->clinic_profile_id]) }}"
                                            class="btn btn-ghost"
                                        >
                                            {{ $canUpdateClaim ? 'Kelola' : 'Lihat' }}
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('input-klaim-bpjs.destroy', $claim) }}"
                                            class="inline-action-form"
                                            data-confirm-delete
                                            data-confirm-title="Hapus data klaim BPJS?"
                                            data-confirm-message="Data klaim bulanan ini akan dihapus permanen dari aplikasi."
                                            data-confirm-button="Ya, hapus klaim"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_clinic_filter" value="{{ $selectedClinicFilter }}">
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

<script>
    const bpjsAllocationSeed = @json($allocationSeed);
    const bpjsSummarySeed = @json($bpjsSummarySeed);

    const claimForm = document.getElementById('bpjsClaimForm');
    const totalClaimInput = document.getElementById('total_klaim');
    const currencyInputs = Array.from(document.querySelectorAll('.js-currency-input'));
    const allocationRows = Array.from(document.querySelectorAll('[data-allocation-row]'));

    function splitCurrencyParts(value) {
        const cleaned = String(value ?? '').replace(/[^\d.,]/g, '').trim();

        if (!cleaned) {
            return { intPart: '', fracPart: '' };
        }

        const lastComma = cleaned.lastIndexOf(',');
        const lastDot = cleaned.lastIndexOf('.');
        const candidates = [lastComma, lastDot].filter((index) => index >= 0).sort((a, b) => b - a);

        let separatorIndex = -1;

        for (const index of candidates) {
            const intCandidate = cleaned.slice(0, index).replace(/[^\d]/g, '');
            const fracCandidate = cleaned.slice(index + 1).replace(/[^\d]/g, '');

            if (intCandidate.length > 0 && fracCandidate.length > 0 && fracCandidate.length <= 2) {
                separatorIndex = index;
                break;
            }
        }

        if (separatorIndex >= 0) {
            return {
                intPart: cleaned.slice(0, separatorIndex).replace(/[^\d]/g, ''),
                fracPart: cleaned.slice(separatorIndex + 1).replace(/[^\d]/g, '').slice(0, 2),
            };
        }

        return {
            intPart: cleaned.replace(/[^\d]/g, ''),
            fracPart: '',
        };
    }

    function formatCurrencyDisplay(value) {
        const { intPart, fracPart } = splitCurrencyParts(value);

        if (!intPart && !fracPart) {
            return '';
        }

        const normalizedInt = (intPart || '0').replace(/^0+(?=\d)/, '') || '0';
        const formattedInt = normalizedInt.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        return fracPart ? `${formattedInt},${fracPart}` : formattedInt;
    }

    function currencyValueToSubmitString(value) {
        const { intPart, fracPart } = splitCurrencyParts(value);

        if (!intPart && !fracPart) {
            return '';
        }

        return `${intPart || '0'}${fracPart ? `.${fracPart}` : ''}`;
    }

    function parseCurrencyValue(value) {
        const submitReady = currencyValueToSubmitString(value);

        return submitReady ? Number(submitReady) : 0;
    }

    function formatRupiah(value) {
        const number = Number(value || 0);
        return `Rp ${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(number)}`;
    }

    function formatPercent(value) {
        const number = Number(value || 0);
        return `${new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(number)}%`;
    }

    function updateClaimPreview() {
        const totalKlaim = parseCurrencyValue(totalClaimInput?.value);
        const totalVersiKlinik = Number(bpjsSummarySeed.total_versi_klinik || 0);
        const totalAcuan = Number(bpjsSummarySeed.total_komponen_acuan || 0);
        const jumlahAcuan = Number(bpjsSummarySeed.jumlah_komponen_acuan || 0);
        const selisihNominal = totalKlaim - totalVersiKlinik;
        const selisihPersen = totalVersiKlinik > 0 ? (selisihNominal / totalVersiKlinik) * 100 : 0;
        const selisihDirection = Math.abs(selisihNominal) < 0.00001
            ? 'Balance'
            : (selisihNominal > 0 ? 'Masuk Debet' : 'Masuk Kredit');

        let pajakObatTotal = 0;

        allocationRows.forEach((row, index) => {
            const seed = bpjsAllocationSeed[index] || {};
            const nominalAlokasi = (Number(seed.persentase || 0) / 100) * totalKlaim;

            const nominalCell = row.querySelector('.js-alloc-nominal');
            if (nominalCell) {
                nominalCell.textContent = formatRupiah(nominalAlokasi);
            }

            if (seed.basis_pajak_obat) {
                pajakObatTotal += nominalAlokasi;
            }
        });

        document.querySelectorAll('.js-live-total-klaim').forEach((el) => {
            el.textContent = formatRupiah(totalKlaim);
        });
        document.querySelectorAll('.js-live-versi-klinik').forEach((el) => {
            el.textContent = formatRupiah(totalVersiKlinik);
        });
        document.querySelectorAll('.js-live-selisih').forEach((el) => {
            el.textContent = formatRupiah(Math.abs(selisihNominal));
        });
        document.querySelectorAll('.js-live-selisih-direction').forEach((el) => {
            el.textContent = selisihDirection;
        });
        document.querySelectorAll('.js-live-selisih-persen').forEach((el) => {
            el.textContent = formatPercent(selisihPersen);
        });
        document.querySelectorAll('.js-live-komponen-count').forEach((el) => {
            el.textContent = `${new Intl.NumberFormat('id-ID').format(jumlahAcuan)} Komponen`;
        });
        document.querySelectorAll('.js-live-pajak-obat').forEach((el) => {
            el.textContent = formatRupiah(pajakObatTotal);
        });
        document.querySelectorAll('.js-live-total-acuan').forEach((el) => {
            el.textContent = formatRupiah(totalAcuan);
        });
        document.querySelectorAll('.js-live-klinik-total').forEach((el) => {
            el.value = formatRupiah(totalVersiKlinik);
        });
        document.querySelectorAll('.js-live-selisih-label').forEach((el) => {
            el.value = `${formatRupiah(Math.abs(selisihNominal))} · ${selisihDirection}`;
        });
        document.querySelectorAll('.js-total-basis').forEach((el) => {
            el.textContent = formatRupiah(totalAcuan);
        });
        document.querySelectorAll('.js-total-alokasi').forEach((el) => {
            el.textContent = formatRupiah(totalKlaim);
        });
    }

    function prepareCurrencyInputsForSubmit(scope) {
        if (!scope) {
            return;
        }

        scope.querySelectorAll('.js-currency-input').forEach((field) => {
            field.dataset.displayValue = field.value;
            field.value = currencyValueToSubmitString(field.value);
        });
    }

    function restoreCurrencyInputs(scope) {
        if (!scope) {
            return;
        }

        scope.querySelectorAll('.js-currency-input').forEach((field) => {
            if (Object.prototype.hasOwnProperty.call(field.dataset, 'displayValue')) {
                field.value = field.dataset.displayValue;
                delete field.dataset.displayValue;
            }
        });
    }

    currencyInputs.forEach((input) => {
        input.addEventListener('input', () => {
            input.value = formatCurrencyDisplay(input.value);
            updateClaimPreview();
        });

        input.addEventListener('blur', () => {
            input.value = formatCurrencyDisplay(input.value);
            updateClaimPreview();
        });

        if (input.value) {
            input.value = formatCurrencyDisplay(input.value);
        }
    });

    if (claimForm) {
        claimForm.addEventListener('submit', () => {
            prepareCurrencyInputsForSubmit(claimForm);
            window.setTimeout(() => restoreCurrencyInputs(claimForm), 0);
        });
    }

    updateClaimPreview();
</script>
@endsection
