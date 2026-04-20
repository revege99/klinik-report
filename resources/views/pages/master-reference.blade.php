@extends('layouts.app')

@section('title', $pageTitle . ' | Klink Report')

@section('content')
<style>
    .master-shell {
        display: grid;
        gap: 18px;
    }

    .master-shell > * {
        min-width: 0;
    }

    .hero-card,
    .message-card,
    .compose-card,
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
        gap: 16px 18px;
        padding: 20px 22px;
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
        margin: 7px 0 0;
        color: #10233d;
        font-size: 1.5rem;
        line-height: 1.1;
    }

    .hero-copy p {
        margin: 10px 0 0;
        max-width: 68ch;
        color: #64748b;
        font-size: 0.84rem;
        line-height: 1.8;
    }

    .hero-stats {
        display: grid;
        min-width: 440px;
        gap: 8px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .hero-stat {
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
        font-size: 0.96rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .message-card,
    .compose-card,
    .table-card {
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

    .content-grid {
        display: grid;
        gap: 18px;
        grid-template-columns: minmax(320px, 420px) minmax(0, 1fr);
    }

    .section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .section-head h2 {
        margin: 0;
        color: #13263f;
        font-size: 1rem;
        line-height: 1.2;
    }

    .section-head p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.7;
    }

    .panel-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .master-form {
        display: grid;
        gap: 14px;
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

    .form-group label {
        color: #334155;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .form-group input,
    .search-box input {
        width: 100%;
        border: 1px solid #d7e1ef;
        border-radius: 16px;
        padding: 12px 14px;
        background: #f8fafc;
        color: #10233d;
        font-size: 0.88rem;
        transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
    }

    .form-group input:focus,
    .search-box input:focus {
        outline: none;
        border-color: #60a5fa;
        background: white;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.16);
    }

    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 18px;
        border: 1px solid #d7e1ef;
        background: #f8fafc;
    }

    .toggle-wrap input {
        width: 18px;
        height: 18px;
        margin: 0;
    }

    .toggle-copy strong {
        display: block;
        color: #13263f;
        font-size: 0.84rem;
        line-height: 1.2;
    }

    .toggle-copy span {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 0.74rem;
        line-height: 1.55;
    }

    .helper-note {
        padding: 14px 16px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(239, 246, 255, 0.96), rgba(224, 242, 254, 0.9));
        border: 1px solid rgba(56, 189, 248, 0.14);
        color: #1e3a5f;
        font-size: 0.78rem;
        line-height: 1.75;
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
    .search-box {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .search-box {
        justify-content: flex-end;
    }

    .search-box input {
        min-width: 240px;
        height: 42px;
        border-radius: 14px;
        padding: 10px 12px;
        font-size: 0.82rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        border-radius: 16px;
        padding: 12px 18px;
        font-size: 0.88rem;
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

    .table-wrap {
        overflow-x: auto;
    }

    .master-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
    }

    .master-table th,
    .master-table td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.88);
        vertical-align: top;
    }

    .master-table th {
        color: #64748b;
        font-size: 0.69rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
    }

    .master-table td {
        color: #1f2937;
        font-size: 0.82rem;
        line-height: 1.6;
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

    .name-cell strong {
        display: block;
        color: #13263f;
        font-size: 0.86rem;
        line-height: 1.4;
    }

    .name-cell span {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 0.74rem;
    }

    .mapping-stack {
        display: grid;
        gap: 6px;
    }

    .mapping-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.05);
        color: #334155;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .mapping-muted {
        color: #94a3b8;
        font-size: 0.74rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .status-pill.is-active {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }

    .status-pill.is-inactive {
        background: rgba(148, 163, 184, 0.16);
        color: #475569;
    }

    .usage-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(249, 115, 22, 0.1);
        color: #c2410c;
        font-size: 0.7rem;
        font-weight: 800;
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

    @media (max-width: 1120px) {
        .hero-card,
        .content-grid {
            grid-template-columns: 1fr;
        }

        .hero-stats {
            min-width: 0;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 680px) {
        .hero-card,
        .message-card,
        .compose-card,
        .table-card {
            border-radius: 24px;
            padding: 18px;
        }

        .hero-stats,
        .form-grid {
            grid-template-columns: 1fr;
        }

        .search-box {
            justify-content: stretch;
        }

        .search-box input {
            min-width: 0;
            width: 100%;
        }
    }
</style>

@php
    $isEditing = filled($editingItem);
    $formAction = $isEditing
        ? route($routeUpdate, $editingItem)
        : route($routeStore);
@endphp

<div class="master-shell">
    <section class="hero-card">
        <div class="hero-copy">
            <p class="page-eyebrow">{{ $pageEyebrow }}</p>
            <h1>{{ $pageTitle }}</h1>
            <p>{{ $pageDescription }}</p>
        </div>

        <div class="hero-stats">
            <article class="hero-stat">
                <span>{{ $variant === 'layanan' ? 'Total Kode' : 'Total Kategori' }}</span>
                <strong>{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</strong>
            </article>
            <article class="hero-stat">
                <span>Data Aktif</span>
                <strong>{{ number_format($stats['active'] ?? 0, 0, ',', '.') }}</strong>
            </article>
            <article class="hero-stat">
                <span>{{ $variant === 'layanan' ? 'Mapping SIMRS' : 'Kode Siap Pakai' }}</span>
                <strong>{{ number_format($stats['mapped'] ?? 0, 0, ',', '.') }}</strong>
            </article>
            <article class="hero-stat">
                <span>{{ $variant === 'layanan' ? 'Dipakai Transaksi' : 'Dipakai Pengeluaran' }}</span>
                <strong>{{ number_format($stats['used'] ?? 0, 0, ',', '.') }}</strong>
            </article>
        </div>
    </section>

    @if (session('success'))
        <section class="message-card success">
            <p>{{ session('success') }}</p>
        </section>
    @endif

    @if ($errors->any())
        <section class="message-card error">
            <p>Masih ada data yang perlu diperbaiki sebelum disimpan.</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <div class="content-grid">
        <section class="compose-card">
            <div class="section-head">
                <div>
                    <h2>{{ $isEditing ? 'Edit ' . $pageTitle : 'Input ' . $pageTitle }}</h2>
                    <p>
                        {{ $variant === 'layanan'
                            ? 'Gunakan kode yang rapi lalu hubungkan dengan kd_poli SIMRS agar mapping transaksi lebih stabil.'
                            : 'Kategori ini akan dipakai pada input pengeluaran dan otomatis terbaca di laporan kredit.' }}
                    </p>
                </div>
                <span class="panel-chip">{{ $isEditing ? 'Mode Edit' : 'Mode Baru' }}</span>
            </div>

            <form method="POST" action="{{ $formAction }}" class="master-form">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <input type="hidden" name="is_active" value="0">

                <div class="form-grid">
                    @if ($variant === 'layanan')
                        <div class="form-group">
                            <label for="kode_layanan">Kode Layanan</label>
                            <input
                                id="kode_layanan"
                                type="text"
                                name="kode_layanan"
                                value="{{ old('kode_layanan', $editingItem?->kode_layanan) }}"
                                placeholder="Contoh: K1"
                            >
                        </div>

                        <div class="form-group">
                            <label for="nama_layanan">Nama Layanan</label>
                            <input
                                id="nama_layanan"
                                type="text"
                                name="nama_layanan"
                                value="{{ old('nama_layanan', $editingItem?->nama_layanan) }}"
                                placeholder="Contoh: Klinik Umum"
                            >
                        </div>

                        <div class="form-group">
                            <label for="simrs_kd_poli">SIMRS kd_poli</label>
                            <input
                                id="simrs_kd_poli"
                                type="text"
                                name="simrs_kd_poli"
                                value="{{ old('simrs_kd_poli', $editingItem?->simrs_kd_poli) }}"
                                placeholder="Contoh: umum"
                            >
                        </div>

                        <div class="form-group">
                            <label for="simrs_nm_poli">SIMRS Nama Poli</label>
                            <input
                                id="simrs_nm_poli"
                                type="text"
                                name="simrs_nm_poli"
                                value="{{ old('simrs_nm_poli', $editingItem?->simrs_nm_poli) }}"
                                placeholder="Contoh: Poliklinik Umum"
                            >
                        </div>
                    @else
                        <div class="form-group">
                            <label for="kode_kategori">Kode Pengeluaran</label>
                            <input
                                id="kode_kategori"
                                type="text"
                                name="kode_kategori"
                                value="{{ old('kode_kategori', $editingItem?->kode_kategori) }}"
                                placeholder="Contoh: E11"
                            >
                        </div>

                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori</label>
                            <input
                                id="nama_kategori"
                                type="text"
                                name="nama_kategori"
                                value="{{ old('nama_kategori', $editingItem?->nama_kategori) }}"
                                placeholder="Contoh: Obat-obatan"
                            >
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="urutan_laporan">Urutan Laporan</label>
                        <input
                            id="urutan_laporan"
                            type="number"
                            min="0"
                            name="urutan_laporan"
                            value="{{ old('urutan_laporan', $editingItem?->urutan_laporan ?? 0) }}"
                            placeholder="0"
                        >
                    </div>

                    <div class="form-group is-full">
                        <label>Status</label>
                        <label class="toggle-wrap">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $editingItem?->is_active ?? true) ? 'checked' : '' }}
                            >
                            <span class="toggle-copy">
                                <strong>Aktifkan data master ini</strong>
                                <span>Data aktif akan tampil sebagai referensi utama pada modul transaksi dan laporan.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="helper-note">
                    @if ($variant === 'layanan')
                        <code>simrs_kd_poli</code> dipakai sebagai kunci mapping saat menarik data dari
                        <code>reg_periksa.kd_poli</code>. Jadi isi bagian ini sesuai kode poli asli dari SIMRS.
                    @else
                        Kode kategori ini akan dipakai untuk rekap kredit bulanan dan tahunan. Urutan laporan menentukan posisi tampil di tabel laporan.
                    @endif
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Data' }}
                    </button>

                    @if ($isEditing)
                        <a href="{{ route($routeIndex, ['q' => $search ?: null]) }}" class="btn btn-muted">Batal Edit</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="table-card">
            <div class="section-head">
                <div>
                    <h2>Data {{ $pageTitle }}</h2>
                    <p>
                        {{ $variant === 'layanan'
                            ? 'Daftar master layanan yang dipakai untuk mapping transaksi pasien dan laporan debet.'
                            : 'Daftar master kategori pengeluaran yang dipakai untuk input kredit dan rekap pengeluaran.' }}
                    </p>
                </div>

                <form method="GET" action="{{ route($routeIndex) }}" class="search-box">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode atau nama...">
                    <button type="submit" class="btn btn-ghost">Cari</button>
                </form>
            </div>

            @if ($records->isEmpty())
                <div class="empty-state">
                    Data belum ada untuk filter ini. Tambahkan data baru lewat panel form di sebelah kiri.
                </div>
            @else
                <div class="table-wrap">
                    <table class="master-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>{{ $variant === 'layanan' ? 'Layanan' : 'Kategori' }}</th>
                                @if ($variant === 'layanan')
                                    <th>Mapping SIMRS</th>
                                @endif
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Dipakai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $record)
                                <tr>
                                    <td>
                                        <span class="code-badge">{{ $record['code'] }}</span>
                                    </td>
                                    <td class="name-cell">
                                        <strong>{{ $record['name'] }}</strong>
                                        <span>{{ $variant === 'layanan' ? 'Kode layanan laporan' : 'Kategori kredit pengeluaran' }}</span>
                                    </td>
                                    @if ($variant === 'layanan')
                                        <td>
                                            <div class="mapping-stack">
                                                @if (filled($record['mapping_key']))
                                                    <span class="mapping-pill">kd_poli: {{ $record['mapping_key'] }}</span>
                                                @endif
                                                @if (filled($record['mapping_label']))
                                                    <span class="mapping-pill">{{ $record['mapping_label'] }}</span>
                                                @endif
                                                @if (! filled($record['mapping_key']) && ! filled($record['mapping_label']))
                                                    <span class="mapping-muted">Belum ada mapping SIMRS</span>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                    <td>{{ number_format($record['sort_order'], 0, ',', '.') }}</td>
                                    <td>
                                        <span class="status-pill {{ $record['is_active'] ? 'is-active' : 'is-inactive' }}">
                                            {{ $record['is_active'] ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="usage-chip">{{ number_format($record['usage_count'], 0, ',', '.') }} data</span>
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route($routeIndex, ['edit' => $record['id'], 'q' => $search ?: null]) }}"
                                            class="btn btn-ghost"
                                        >
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
