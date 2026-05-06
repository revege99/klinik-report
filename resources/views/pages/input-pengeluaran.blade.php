@extends('layouts.app')

@section('title', 'Input Pengeluaran | Klink Report')

@section('content')
<style>
    .pengeluaran-shell {
        display: grid;
        gap: 18px;
    }

    .hero-card,
    .table-card,
    .message-card {
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

    .hero-card.is-master {
        align-items: center;
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

    .hero-tools {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        min-width: 0;
        flex-wrap: wrap;
    }

    .hero-summary {
        display: flex;
        min-width: 220px;
        flex: 0 0 auto;
    }

    .hero-stat {
        width: 100%;
        border-radius: 18px;
        padding: 10px 12px;
        background: linear-gradient(180deg, #f8fbff, #eef4ff);
        border: 1px solid rgba(59, 130, 246, 0.12);
    }

    .hero-stat span {
        display: block;
        color: #64748b;
        font-size: 0.61rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .hero-stat strong {
        display: block;
        margin-top: 5px;
        color: #1e293b;
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .hero-filter-form {
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .hero-filter-field {
        display: flex;
        min-width: 150px;
        flex: 0 1 170px;
        flex-direction: column;
        gap: 5px;
    }

    .hero-filter-field.is-wide {
        flex-basis: 190px;
    }

    .hero-filter-field label {
        color: #64748b;
        font-size: 0.63rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .hero-filter-field input,
    .hero-filter-field select {
        height: 40px;
        border: 1px solid #d7e1ef;
        border-radius: 14px;
        padding: 9px 12px;
        background: #f8fafc;
        color: #10233d;
        font-size: 0.8rem;
    }

    .hero-filter-actions {
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }

    .hero-filter-button {
        min-height: 40px;
        border-radius: 14px;
        padding: 10px 13px;
        font-size: 0.74rem;
        letter-spacing: 0.03em;
    }

    .table-card,
    .message-card {
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

    .form-group label,
    .modal-group label {
        color: #334155;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .form-group input,
    .form-group select,
    .form-group textarea,
    .modal-group input,
    .modal-group select,
    .modal-group textarea {
        width: 100%;
        border: 1px solid #d7e1ef;
        border-radius: 16px;
        padding: 12px 14px;
        background: #f8fafc;
        color: #10233d;
        font-size: 0.9rem;
        transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus,
    .modal-group input:focus,
    .modal-group select:focus,
    .modal-group textarea:focus {
        outline: none;
        border-color: #60a5fa;
        background: white;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.16);
    }

    .readonly-display {
        background: linear-gradient(180deg, #f8fbff, #eef4ff) !important;
        color: #1d4ed8 !important;
        font-weight: 700;
        cursor: default;
    }

    .modal-actions {
        display: flex;
        flex-wrap: wrap;
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
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
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

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 14px 30px rgba(239, 68, 68, 0.18);
    }

    .tabs-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .tabs-nav {
        display: inline-flex;
        flex-wrap: wrap;
        max-width: 100%;
        align-items: center;
        gap: 8px;
        padding: 6px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(241, 245, 249, 0.96));
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
    }

    .tab-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: none;
        border-radius: 14px;
        padding: 11px 16px;
        background: transparent;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        cursor: pointer;
        transition: background 160ms ease, color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
    }

    .tab-button:hover {
        color: #1e293b;
        transform: translateY(-1px);
    }

    .tab-button.is-active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.18);
    }

    .tab-button-count {
        display: inline-flex;
        min-width: 24px;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 4px 8px;
        background: rgba(148, 163, 184, 0.16);
        color: inherit;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1;
    }

    .tab-button.is-active .tab-button-count {
        background: rgba(255, 255, 255, 0.16);
        color: white;
    }

    .tabs-toolbar-forms {
        display: flex;
        min-width: 0;
        flex: 1 1 440px;
        justify-content: flex-end;
        padding-right: 4px;
    }

    .tab-filter-form[hidden],
    .pengeluaran-modal[hidden],
    .tab-panel[hidden] {
        display: none;
    }

    .tab-filter-form {
        display: flex;
        width: min(100%, 520px);
        min-width: 0;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .tab-filter-field {
        display: flex;
        min-width: 140px;
        flex: 0 0 150px;
        flex-direction: column;
        gap: 5px;
    }

    .tab-filter-field.is-wide {
        flex-basis: 180px;
    }

    .tab-filter-field label {
        color: #64748b;
        font-size: 0.63rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .tab-filter-field input,
    .tab-filter-field select {
        height: 40px;
        border: 1px solid #d7e1ef;
        border-radius: 14px;
        padding: 9px 12px;
        background: #f8fafc;
        color: #10233d;
        font-size: 0.8rem;
    }

    .tab-filter-actions {
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }

    .tab-filter-button {
        min-height: 40px;
        border-radius: 14px;
        padding: 10px 13px;
        font-size: 0.74rem;
        letter-spacing: 0.03em;
    }

    .table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .table-head h2 {
        margin: 0;
        color: #1f2937;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .table-head p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 0.78rem;
    }

    .form-shell {
        display: grid;
        gap: 16px;
    }

    .form-card {
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 24px;
        padding: 18px;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.92), rgba(255, 255, 255, 0.98));
    }

    .form-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.span-2 {
        grid-column: span 2;
    }

    .form-group.span-4 {
        grid-column: span 4;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
    }

    .data-table th {
        padding: 12px 14px;
        border-bottom: 1px solid #dbe3ef;
        text-align: left;
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .data-table td {
        padding: 14px;
        border-bottom: 1px solid #edf2f7;
        color: #334155;
        font-size: 0.84rem;
        vertical-align: top;
    }

    .row-title {
        color: #172033;
        font-size: 0.86rem;
        font-weight: 700;
    }

    .row-subtitle {
        margin-top: 4px;
        color: #64748b;
        font-size: 0.76rem;
        line-height: 1.55;
    }

    .amount-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 8px 12px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        color: #1d4ed8;
        font-size: 0.77rem;
        font-weight: 800;
    }

    .action-stack {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 8px;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 14px;
        padding: 0;
        cursor: pointer;
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.12);
        transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease, color 160ms ease;
    }

    .action-btn svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 1.9;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow:
            inset 0 0 0 1px rgba(148, 163, 184, 0.16),
            0 12px 20px rgba(15, 23, 42, 0.08);
    }

    .action-btn.edit {
        background: linear-gradient(180deg, #ecfeff, #cffafe);
        color: #0f766e;
    }

    .action-btn.delete {
        background: linear-gradient(180deg, #fff1f2, #ffe4e6);
        color: #be123c;
    }

    .empty-state {
        padding: 34px 18px;
        border: 1px dashed #cbd5e1;
        border-radius: 22px;
        text-align: center;
        color: #64748b;
        line-height: 1.8;
    }

    .inline-form {
        display: inline-flex;
        margin: 0;
    }

    .pengeluaran-modal {
        position: fixed;
        inset: 0;
        z-index: 80;
    }

    .modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.56);
        backdrop-filter: blur(4px);
    }

    .modal-dialog {
        position: relative;
        z-index: 1;
        width: min(760px, calc(100vw - 32px));
        max-height: calc(100vh - 32px);
        margin: 16px auto;
        overflow: hidden;
        border-radius: 28px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: white;
        box-shadow: 0 40px 90px rgba(15, 23, 42, 0.26);
    }

    .modal-header,
    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 24px;
        border-bottom: 1px solid #e5edf6;
    }

    .modal-footer {
        justify-content: flex-end;
        border-top: 1px solid #e5edf6;
        border-bottom: none;
    }

    .modal-header h3 {
        margin: 0;
        color: #0f172a;
        font-size: 1.04rem;
        font-weight: 700;
    }

    .modal-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 0.78rem;
    }

    .modal-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border: none;
        border-radius: 14px;
        background: #f1f5f9;
        color: #0f172a;
        font-size: 1.3rem;
        cursor: pointer;
    }

    .modal-body {
        max-height: calc(100vh - 210px);
        overflow: auto;
        padding: 22px 24px 28px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }

    .modal-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .modal-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .modal-group.span-2 {
        grid-column: span 2;
    }

    @media (max-width: 1200px) {
        .hero-card {
            grid-template-columns: 1fr;
            align-items: start;
        }

        .hero-tools {
            justify-content: flex-start;
        }

        .form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .form-group.span-4 {
            grid-column: span 2;
        }
    }

    @media (max-width: 768px) {
        .hero-card,
        .table-card,
        .message-card {
            border-radius: 22px;
            padding: 18px;
        }

        .hero-card {
            grid-template-columns: 1fr;
            align-items: stretch;
        }

        .tabs-nav {
            display: flex;
            width: 100%;
        }

        .tab-button {
            justify-content: center;
            flex: 1 1 100%;
        }

        .tabs-toolbar-forms {
            width: 100%;
            flex-basis: 100%;
            justify-content: stretch;
        }

        .hero-filter-form {
            width: 100%;
            justify-content: flex-start;
        }

        .hero-filter-field,
        .hero-filter-field.is-wide {
            min-width: 0;
            flex: 1 1 180px;
        }

        .hero-summary {
            width: 100%;
        }

        .tab-filter-form {
            width: 100%;
            min-width: 0;
            justify-content: flex-start;
        }

        .tab-filter-field,
        .tab-filter-field.is-wide {
            min-width: 0;
            flex: 1 1 180px;
        }

        .hero-summary,
        .form-grid,
        .modal-grid {
            grid-template-columns: 1fr;
        }

        .form-group.span-2,
        .form-group.span-4,
        .modal-group.span-2 {
            grid-column: span 1;
        }

        .modal-dialog {
            width: min(100vw - 20px, 760px);
            margin: 10px auto;
            border-radius: 22px;
        }

        .modal-header,
        .modal-footer,
        .modal-body {
            padding-left: 16px;
            padding-right: 16px;
        }

        .table-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

@php
    $selectedMonthDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
    $formattedMonth = $selectedMonthDate->locale('id')->translatedFormat('F Y');
    $defaultExpenseDate = old('tanggal', $selectedMonth === now()->format('Y-m')
        ? now()->format('Y-m-d')
        : $selectedMonthDate->copy()->startOfMonth()->format('Y-m-d'));
    $canEditOperationalData = auth()->user()?->canEditOperationalData();
    $isMasterPengeluaranView = auth()->user()?->isMaster();
@endphp

<div class="pengeluaran-shell">
    <section class="hero-card {{ $isMasterPengeluaranView ? 'is-master' : '' }}">
        <div class="hero-copy">
            <p class="page-eyebrow">Operasional Klinik</p>
            <h1>Input Pengeluaran</h1>
        </div>

        <div class="hero-tools">
            <div class="hero-summary">
                <article class="hero-stat">
                    <span>Total Pengeluaran</span>
                    <strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong>
                </article>
            </div>

            @if ($showClinicFilter)
            <form method="GET" action="{{ route('input-pengeluaran') }}" class="hero-filter-form" id="expenseHeroFilterForm">
                <input type="hidden" name="active_tab" id="expense-hero-active-tab" value="{{ $preferredTab ?: 'panel-input-pengeluaran' }}">
                <input type="hidden" name="bulan" value="{{ $selectedMonth }}">
                <input type="hidden" name="kategori" value="{{ $selectedCategory }}">

                    <div class="hero-filter-field is-wide">
                        <label for="hero-pengeluaran-clinic-id">Klinik Aktif</label>
                        <select id="hero-pengeluaran-clinic-id" name="clinic_id">
                            <option value="all" @selected($selectedClinicFilter === 'all')>Semua Klinik</option>
                            @foreach ($clinicOptions as $clinicOption)
                                <option value="{{ $clinicOption->id }}" @selected((string) $clinicOption->id === $selectedClinicFilter)>
                                    {{ $clinicOption->kode_klinik }} · {{ $clinicOption->nama_klinik }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="hero-filter-actions">
                        <button type="submit" class="btn btn-primary hero-filter-button filter-submit">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 6h16"></path>
                                <path d="M7 12h10"></path>
                                <path d="M10 18h4"></path>
                            </svg>
                            Filter Data
                        </button>
                    </div>
            </form>
            @endif
        </div>
    </section>

    @if (session('success'))
        <section class="message-card success">
            <p>{{ session('success') }}</p>
        </section>
    @endif

    @if ($errors->any())
        <section class="message-card error">
            <p>Masih ada data pengeluaran yang perlu diperbaiki.</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="table-card">
        <div class="tabs-toolbar">
            <div class="tabs-nav" role="tablist" aria-label="Tab input pengeluaran">
                <button
                    type="button"
                    class="tab-button is-active"
                    role="tab"
                    id="tab-input-pengeluaran"
                    aria-selected="true"
                    aria-controls="panel-input-pengeluaran"
                    data-tab-target="panel-input-pengeluaran"
                >
                    Input Pengeluaran
                    <span class="tab-button-count">{{ $categoryOptions->count() }}</span>
                </button>

                <button
                    type="button"
                    class="tab-button"
                    role="tab"
                    id="tab-data-pengeluaran"
                    aria-selected="false"
                    aria-controls="panel-data-pengeluaran"
                    data-tab-target="panel-data-pengeluaran"
                >
                    Data Pengeluaran
                    <span class="tab-button-count">{{ $expenses->count() }}</span>
                </button>
            </div>

            <div class="tabs-toolbar-forms">
                <form
                    method="GET"
                    action="{{ route('input-pengeluaran') }}"
                    class="tab-filter-form"
                    data-tab-filter="panel-data-pengeluaran"
                    hidden
                >
                    <input type="hidden" name="clinic_id" value="{{ $selectedClinicFilter }}">
                    <input type="hidden" name="active_tab" value="panel-data-pengeluaran">

                    <div class="tab-filter-field">
                        <label for="data-bulan-pengeluaran">Bulan</label>
                        <input id="data-bulan-pengeluaran" type="month" name="bulan" value="{{ $selectedMonth }}">
                    </div>

                    <div class="tab-filter-field is-wide">
                        <label for="kategori">Kategori</label>
                        <select id="kategori" name="kategori">
                            <option value="">Semua Kategori</option>
                            @foreach ($categoryOptions as $category)
                                <option value="{{ $category->nama_kategori }}" @selected($selectedCategory === $category->nama_kategori)>
                                    {{ $category->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($showClinicFilter)
                        <div class="tab-filter-field is-wide">
                            <label for="pengeluaran-clinic-id">Klinik</label>
                            <select id="pengeluaran-clinic-id" name="clinic_id">
                                @foreach ($clinicOptions as $clinicOption)
                                    <option value="{{ $clinicOption->id }}" @selected((int) $selectedClinicId === (int) $clinicOption->id)>
                                        {{ $clinicOption->kode_klinik }} · {{ $clinicOption->nama_klinik }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="tab-filter-actions">
                        <button type="submit" class="btn btn-primary tab-filter-button filter-submit">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 6h16"></path>
                                <path d="M7 12h10"></path>
                                <path d="M10 18h4"></path>
                            </svg>
                            Filter Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-panel" id="panel-input-pengeluaran" role="tabpanel" aria-labelledby="tab-input-pengeluaran">
            <div class="table-head">
                <div>
                    <h2>Form Pengeluaran {{ $formattedMonth }}</h2>
                    <p>Catat pengeluaran klinik ke database lokal agar rekap bulanan bisa langsung dibandingkan dengan pendapatan.</p>
                </div>
            </div>

            <div class="form-shell">
                <div class="form-card">
                    @if ($showClinicFilter && $viewingAllClinics)
                        <div class="empty-state">
                            Pilih satu klinik pada filter header terlebih dahulu agar pengeluaran baru tersimpan ke klinik yang tepat.
                        </div>
                    @else
                    <form method="POST" action="{{ route('input-pengeluaran.store') }}" id="createPengeluaranForm">
                        @csrf
                        @if ($showClinicFilter)
                            <input type="hidden" name="clinic_profile_id" value="{{ $selectedClinicId }}">
                        @endif

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="create-tanggal">Tanggal</label>
                                <input id="create-tanggal" type="date" name="tanggal" value="{{ $defaultExpenseDate }}">
                            </div>

                            <div class="form-group">
                                <label for="create-kategori">Kategori Pengeluaran</label>
                                <select id="create-kategori" name="master_kategori_pengeluaran_id">
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categoryOptions as $category)
                                        <option value="{{ $category->id }}" @selected((string) old('master_kategori_pengeluaran_id') === (string) $category->id)>
                                            {{ $category->kode_kategori }} · {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="create-jumlah_rp">Jumlah Rp.</label>
                                <input id="create-jumlah_rp" type="text" inputmode="decimal" autocomplete="off" class="js-currency-input" name="jumlah_rp" value="{{ old('jumlah_rp', 0) }}">
                            </div>

                            <div class="form-group">
                                <label>User Login</label>
                                <input type="text" value="{{ $loggedInAdminName }}" readonly class="readonly-display">
                            </div>

                            @if ($showClinicFilter)
                                <div class="form-group span-4">
                                    <label>Klinik Tujuan</label>
                                    <input type="text" value="{{ $selectedClinicLabel }}" readonly class="readonly-display">
                                </div>
                            @endif

                            <div class="form-group span-4">
                                <label for="create-deskripsi">Deskripsi</label>
                                <input id="create-deskripsi" type="text" name="deskripsi" value="{{ old('deskripsi') }}" placeholder="Contoh: Pembelian ATK, service alat, honor kebersihan">
                            </div>

                            <div class="form-group span-4">
                                <label for="create-keterangan">Keterangan</label>
                                <textarea id="create-keterangan" name="keterangan" rows="4" placeholder="Catatan tambahan bila diperlukan">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>

                        <div class="table-head" style="margin-top: 18px; margin-bottom: 0; justify-content: flex-end;">
                            <div class="modal-actions">
                                <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="tab-panel" id="panel-data-pengeluaran" role="tabpanel" aria-labelledby="tab-data-pengeluaran" hidden>
            <div class="table-head">
                <div>
                    <h2>
                        Data Pengeluaran {{ $formattedMonth }}
                        @if (filled($selectedCategory))
                            · {{ $selectedCategory }}
                        @endif
                    </h2>
                    <p>Daftar pengeluaran lokal yang siap masuk ke kredit pada rekap bulanan.</p>
                </div>
            </div>

            @if ($expenses->isEmpty())
                <div class="empty-state">
                    Belum ada pengeluaran untuk periode {{ $formattedMonth }}.
                </div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                @if ($viewingAllClinics)
                                    <th>Klinik</th>
                                @endif
                                <th>Petugas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td>
                                        <div class="row-title">{{ optional($expense->tanggal)->format('d/m/Y') ?: '-' }}</div>
                                        <div class="row-subtitle">
                                            {{ $expense->tanggal ? $expense->tanggal->locale('id')->translatedFormat('l') : '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row-title">{{ $expense->kategori_pengeluaran ?: 'Tanpa Kategori' }}</div>
                                        <div class="row-subtitle">
                                            {{ optional($expense->masterKategoriPengeluaran)->kode_kategori ?: 'KX' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row-title">{{ $expense->deskripsi }}</div>
                                        <div class="row-subtitle">
                                            {{ $expense->keterangan ?: 'Tanpa keterangan tambahan' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="amount-pill">Rp {{ number_format((float) $expense->jumlah_rp, 0, ',', '.') }}</span>
                                    </td>
                                    @if ($viewingAllClinics)
                                        <td>
                                            <div class="row-title">{{ $expense->clinicProfile?->nama_pendek ?: $expense->clinicProfile?->nama_klinik ?: '-' }}</div>
                                            <div class="row-subtitle">
                                                {{ $expense->clinicProfile?->kode_klinik ?: '-' }}
                                            </div>
                                        </td>
                                    @endif
                                    <td>
                                        <div class="row-title">{{ $expense->petugas_admin ?: '-' }}</div>
                                        <div class="row-subtitle">
                                            ID {{ $expense->id }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-stack">
                                            @if ($canEditOperationalData)
                                                <button
                                                    type="button"
                                                    class="action-btn edit js-open-expense-modal"
                                                    data-expense-id="{{ $expense->id }}"
                                                    title="Edit pengeluaran"
                                                    aria-label="Edit pengeluaran"
                                                >
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M4 20h4l10-10-4-4L4 16v4Z"></path>
                                                        <path d="M12.5 7.5l4 4"></path>
                                                    </svg>
                                                </button>
                                            @endif

                                            <form
                                                method="POST"
                                                action="{{ route('input-pengeluaran.destroy', $expense) }}"
                                                class="inline-form"
                                                data-confirm-delete
                                                data-confirm-title="Hapus data pengeluaran?"
                                                data-confirm-message="Data pengeluaran lokal ini akan dihapus permanen dari aplikasi."
                                                data-confirm-button="Ya, hapus pengeluaran"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="action-btn delete"
                                                    title="Hapus pengeluaran"
                                                    aria-label="Hapus pengeluaran"
                                                >
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M4 7h16"></path>
                                                        <path d="M9 7V4h6v3"></path>
                                                        <path d="M6 7l1 13h10l1-13"></path>
                                                        <path d="M10 11v5"></path>
                                                        <path d="M14 11v5"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
</div>

<div class="pengeluaran-modal" id="pengeluaranModal" hidden>
    <div class="modal-overlay js-close-expense-modal"></div>

    <div class="modal-dialog">
        <div class="modal-header">
            <div>
                <h3 id="expenseModalTitle">Edit Pengeluaran</h3>
                <p id="expenseModalSubtitle">Ubah data pengeluaran lokal.</p>
            </div>

            <button type="button" class="modal-close js-close-expense-modal" aria-label="Tutup modal">&times;</button>
        </div>

        <form
            id="pengeluaranForm"
            method="POST"
            action="{{ route('input-pengeluaran.store') }}"
            data-store-url="{{ route('input-pengeluaran.store') }}"
            data-update-url="{{ url('/input-pengeluaran/__ID__') }}"
        >
            @csrf
            <input type="hidden" name="_method" id="expense-field-method" value="POST">
            <input type="hidden" name="_modal_mode" id="expense-field-meta-mode" value="edit">
            <input type="hidden" name="_pengeluaran_id" id="expense-field-meta-id">
            <input type="hidden" name="clinic_profile_id" id="expense-field-clinic_profile_id" value="{{ $selectedClinicId }}">

            <div class="modal-body">
                <div class="modal-grid">
                    <div class="modal-group">
                        <label for="expense-field-tanggal">Tanggal</label>
                        <input id="expense-field-tanggal" type="date" name="tanggal">
                    </div>

                    <div class="modal-group">
                        <label for="expense-field-master_kategori_pengeluaran_id">Kategori Pengeluaran</label>
                        <select id="expense-field-master_kategori_pengeluaran_id" name="master_kategori_pengeluaran_id">
                            <option value="">Pilih kategori</option>
                            @foreach ($categoryOptions as $category)
                                <option value="{{ $category->id }}">{{ $category->kode_kategori }} · {{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-group span-2">
                        <label for="expense-field-deskripsi">Deskripsi</label>
                        <input id="expense-field-deskripsi" type="text" name="deskripsi">
                    </div>

                    <div class="modal-group">
                        <label for="expense-field-jumlah_rp">Jumlah Rp.</label>
                        <input id="expense-field-jumlah_rp" type="text" inputmode="decimal" autocomplete="off" class="js-currency-input" name="jumlah_rp">
                    </div>

                    <div class="modal-group">
                        <label>User Login</label>
                        <input type="text" value="{{ $loggedInAdminName }}" readonly class="readonly-display">
                    </div>

                    @if ($showClinicFilter)
                        <div class="modal-group span-2">
                            <label>Klinik Tujuan</label>
                            <input type="text" id="expense-field-clinic_label" value="{{ $selectedClinicLabel }}" readonly class="readonly-display">
                        </div>
                    @endif

                    <div class="modal-group span-2">
                        <label for="expense-field-keterangan">Keterangan</label>
                        <textarea id="expense-field-keterangan" name="keterangan" rows="4"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="modal-actions">
                    <button type="button" class="btn btn-muted js-close-expense-modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const expenseMap = @json($expenseData);
    const oldFormState = @json(session()->getOldInput());
    const hasValidationErrors = @json($errors->any());
    const preferredTab = @json($preferredTab);

    const modal = document.getElementById('pengeluaranModal');
    const form = document.getElementById('pengeluaranForm');
    const createForm = document.getElementById('createPengeluaranForm');
    const methodField = document.getElementById('expense-field-method');
    const modalModeField = document.getElementById('expense-field-meta-mode');
    const modalExpenseField = document.getElementById('expense-field-meta-id');
    const modalTitle = document.getElementById('expenseModalTitle');
    const modalSubtitle = document.getElementById('expenseModalSubtitle');
    const heroActiveTabField = document.getElementById('expense-hero-active-tab');
    const tabButtons = Array.from(document.querySelectorAll('[data-tab-target]'));
    const tabPanels = Array.from(document.querySelectorAll('.tab-panel'));
    const tabFilterForms = Array.from(document.querySelectorAll('[data-tab-filter]'));
    const tabStorageKey = 'input-pengeluaran.active-tab';
    const legacyClinicFilterField = document.getElementById('pengeluaran-clinic-id')?.closest('.tab-filter-field');

    if (legacyClinicFilterField) {
        legacyClinicFilterField.remove();
    }

    const trackedFields = [
        'clinic_profile_id',
        'clinic_label',
        'master_kategori_pengeluaran_id',
        'tanggal',
        'deskripsi',
        'jumlah_rp',
        'keterangan',
    ];

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

    function setCurrencyFieldDisplay(field, value) {
        if (!field) {
            return;
        }

        field.value = formatCurrencyDisplay(value);
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

    function activateTab(targetId) {
        tabButtons.forEach((button) => {
            const isActive = button.dataset.tabTarget === targetId;

            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        tabPanels.forEach((panel) => {
            panel.hidden = panel.id !== targetId;
        });

        tabFilterForms.forEach((filterForm) => {
            filterForm.hidden = filterForm.dataset.tabFilter !== targetId;
        });

        if (heroActiveTabField) {
            heroActiveTabField.value = targetId;
        }

        window.localStorage.setItem(tabStorageKey, targetId);
    }

    function setFieldValue(name, value) {
        const field = document.getElementById(`expense-field-${name}`);

        if (!field) {
            return;
        }

        if (field.classList.contains('js-currency-input')) {
            setCurrencyFieldDisplay(field, value);
            return;
        }

        field.value = value ?? '';
    }

    function closeModal() {
        modal.hidden = true;
        document.body.style.overflow = '';
    }

    function hydrateForm(payload, expenseId) {
        trackedFields.forEach((fieldName) => setFieldValue(fieldName, payload[fieldName]));

        form.action = form.dataset.updateUrl.replace('__ID__', expenseId);
        methodField.value = 'PUT';
        modalModeField.value = 'edit';
        modalExpenseField.value = expenseId;
        modalTitle.textContent = 'Edit Pengeluaran';
        modalSubtitle.textContent = 'Ubah data pengeluaran lokal.';
    }

    function openModal(expenseId, overrideData = null) {
        const payload = overrideData || expenseMap[expenseId] || {};

        hydrateForm(payload, expenseId || payload.id || '');
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    document.querySelectorAll('.js-open-expense-modal').forEach((button) => {
        button.addEventListener('click', () => {
            openModal(button.dataset.expenseId);
        });
    });

    document.querySelectorAll('.js-close-expense-modal').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => activateTab(button.dataset.tabTarget));
    });

    const fallbackTab = preferredTab || window.localStorage.getItem(tabStorageKey) || 'panel-input-pengeluaran';
    const initialTab = hasValidationErrors && oldFormState._modal_mode === 'edit'
        ? 'panel-data-pengeluaran'
        : hasValidationErrors
            ? 'panel-input-pengeluaran'
            : fallbackTab;

    activateTab(initialTab);

    if (hasValidationErrors && oldFormState._modal_mode === 'edit' && oldFormState._pengeluaran_id) {
        openModal(oldFormState._pengeluaran_id, oldFormState);
    }

    document.querySelectorAll('.js-currency-input').forEach((input) => {
        input.addEventListener('input', () => {
            input.value = formatCurrencyDisplay(input.value);
        });

        input.addEventListener('blur', () => {
            input.value = formatCurrencyDisplay(input.value);
        });

        if (input.value) {
            input.value = formatCurrencyDisplay(input.value);
        }
    });

    if (createForm) {
        createForm.addEventListener('submit', () => {
            prepareCurrencyInputsForSubmit(createForm);
            window.setTimeout(() => restoreCurrencyInputs(createForm), 0);
        });
    }

    form.addEventListener('submit', () => {
        prepareCurrencyInputsForSubmit(form);
        window.setTimeout(() => restoreCurrencyInputs(form), 0);
    });
</script>
@endsection
