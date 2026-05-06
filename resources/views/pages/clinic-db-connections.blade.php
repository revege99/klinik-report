@extends('layouts.app')

@section('title', 'Koneksi DB Klinik | Klink Report')

@section('content')
<style>
    .db-shell {
        display: grid;
        gap: 18px;
    }

    .db-shell > * {
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
        max-width: 72ch;
        color: #64748b;
        font-size: 0.84rem;
        line-height: 1.8;
    }

    .hero-stats {
        display: grid;
        min-width: 500px;
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
        line-height: 1.2;
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
        grid-template-columns: minmax(360px, 440px) minmax(0, 1fr);
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

    .db-form {
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
    .form-group select,
    .form-group textarea,
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

    .form-group textarea {
        min-height: 92px;
        resize: vertical;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus,
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
        display: grid;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(239, 246, 255, 0.96), rgba(224, 242, 254, 0.9));
        border: 1px solid rgba(56, 189, 248, 0.14);
        color: #1e3a5f;
        font-size: 0.78rem;
        line-height: 1.75;
    }

    .helper-note strong {
        color: #10233d;
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

    .row-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .inline-action-form {
        margin: 0;
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

    .btn-danger {
        padding: 10px 14px;
        border-radius: 14px;
        background: rgba(220, 38, 38, 0.08);
        color: #b91c1c;
        border: 1px solid rgba(220, 38, 38, 0.16);
        box-shadow: none;
    }

    .btn-danger:hover {
        background: rgba(220, 38, 38, 0.12);
    }

    .table-wrap {
        overflow-x: auto;
    }

    .db-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .db-table th,
    .db-table td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.88);
        vertical-align: top;
    }

    .db-table th {
        color: #64748b;
        font-size: 0.69rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
    }

    .db-table td {
        color: #1f2937;
        font-size: 0.82rem;
        line-height: 1.6;
    }

    .name-cell strong,
    .meta-stack strong {
        display: block;
        color: #13263f;
        font-size: 0.86rem;
        line-height: 1.4;
    }

    .name-cell span,
    .meta-stack span {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 0.74rem;
    }

    .pill {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.05);
        color: #334155;
        font-size: 0.72rem;
        font-weight: 700;
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

    .empty-state {
        padding: 18px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(241, 245, 249, 0.98));
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.75;
        text-align: center;
    }

    @media (max-width: 1160px) {
        .hero-card,
        .content-grid {
            grid-template-columns: 1fr;
        }

        .hero-stats {
            min-width: 0;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
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
        ? route('koneksi-db-klinik.update', $editingItem)
        : route('koneksi-db-klinik.store');
    $listingQuery = array_filter([
        'q' => $search ?: null,
    ], fn ($value) => filled($value));
@endphp

<div class="db-shell">
    <section class="hero-card">
        <div class="hero-copy">
            <p class="page-eyebrow">Master Infrastruktur</p>
            <h1>Koneksi DB Klinik</h1>
            <p>
                Isi koneksi database sumber per klinik untuk membaca data SIMRS lewat jaringan lokal atau ZeroTier.
                Koneksi ini dipakai modul transaksi pasien saat menarik data kunjungan dari klinik yang sedang login.
            </p>
        </div>

        <div class="hero-stats">
            <article class="hero-stat">
                <span>Total Koneksi</span>
                <strong>{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</strong>
            </article>
            <article class="hero-stat">
                <span>Koneksi Aktif</span>
                <strong>{{ number_format($stats['active'] ?? 0, 0, ',', '.') }}</strong>
            </article>
            <article class="hero-stat">
                <span>Host ZeroTier</span>
                <strong>{{ number_format($stats['zero_tier'] ?? 0, 0, ',', '.') }}</strong>
            </article>
            <article class="hero-stat">
                <span>App DB Pusat</span>
                <strong>{{ $appDbSummary['mode'] }} · {{ $appDbSummary['host'] }}</strong>
            </article>
        </div>
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
            <p>Masih ada data koneksi yang perlu diperbaiki.</p>
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
                    <h2>{{ $isEditing ? 'Edit Koneksi Klinik' : 'Input Koneksi Klinik' }}</h2>
                    <p>Set satu sumber SIMRS per klinik. Host bisa diisi IP lokal server klinik, sedangkan ZeroTier diisi jika VPS membaca lewat jaringan private.</p>
                </div>
                <span class="panel-chip">{{ $isEditing ? 'Mode Edit' : 'Mode Baru' }}</span>
            </div>

            <form method="POST" action="{{ $formAction }}" class="db-form">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <input type="hidden" name="is_active" value="0">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="clinic_profile_id">Klinik</label>
                        <select id="clinic_profile_id" name="clinic_profile_id">
                            <option value="">Pilih klinik</option>
                            @foreach ($clinicOptions as $clinicOption)
                                <option
                                    value="{{ $clinicOption->id }}"
                                    @selected((int) old('clinic_profile_id', $editingItem?->clinic_profile_id) === (int) $clinicOption->id)
                                >
                                    {{ $clinicOption->kode_klinik }} · {{ $clinicOption->nama_klinik }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="driver">Driver</label>
                        <select id="driver" name="driver">
                            <option value="mariadb" @selected(old('driver', $editingItem?->driver ?? 'mariadb') === 'mariadb')>MariaDB</option>
                            <option value="mysql" @selected(old('driver', $editingItem?->driver ?? 'mariadb') === 'mysql')>MySQL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="server_name">Nama Server Klinik</label>
                        <input
                            id="server_name"
                            type="text"
                            name="server_name"
                            value="{{ old('server_name', $editingItem?->server_name) }}"
                            placeholder="Contoh: Server Klinik Siborongborong"
                        >
                    </div>

                    <div class="form-group">
                        <label for="host">Host Lokal / LAN</label>
                        <input
                            id="host"
                            type="text"
                            name="host"
                            value="{{ old('host', $editingItem?->host) }}"
                            placeholder="Contoh: 192.168.1.10"
                        >
                    </div>

                    <div class="form-group">
                        <label for="zero_tier_ip">IP ZeroTier</label>
                        <input
                            id="zero_tier_ip"
                            type="text"
                            name="zero_tier_ip"
                            value="{{ old('zero_tier_ip', $editingItem?->zero_tier_ip) }}"
                            placeholder="Contoh: 172.28.10.5"
                        >
                    </div>

                    <div class="form-group">
                        <label for="port">Port</label>
                        <input
                            id="port"
                            type="number"
                            min="1"
                            max="65535"
                            name="port"
                            value="{{ old('port', $editingItem?->port ?? 3306) }}"
                            placeholder="3306"
                        >
                    </div>

                    <div class="form-group">
                        <label for="database">Nama Database</label>
                        <input
                            id="database"
                            type="text"
                            name="database"
                            value="{{ old('database', $editingItem?->database) }}"
                            placeholder="Contoh: sik_klinik_a"
                        >
                    </div>

                    <div class="form-group">
                        <label for="username">Username DB</label>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username', $editingItem?->username) }}"
                            placeholder="root"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password DB</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            value=""
                            placeholder="{{ $isEditing ? 'Kosongkan jika tidak diubah' : 'Isi jika memakai password' }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="charset">Charset</label>
                        <input
                            id="charset"
                            type="text"
                            name="charset"
                            value="{{ old('charset', $editingItem?->charset ?? 'utf8mb4') }}"
                            placeholder="utf8mb4"
                        >
                    </div>

                    <div class="form-group">
                        <label for="collation">Collation</label>
                        <input
                            id="collation"
                            type="text"
                            name="collation"
                            value="{{ old('collation', $editingItem?->collation ?? 'utf8mb4_unicode_ci') }}"
                            placeholder="utf8mb4_unicode_ci"
                        >
                    </div>

                    <div class="form-group is-full">
                        <label for="notes">Catatan</label>
                        <textarea
                            id="notes"
                            name="notes"
                            placeholder="Catat misalnya lokasi server, nama PC, atau kebutuhan akses khusus."
                        >{{ old('notes', $editingItem?->notes) }}</textarea>
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
                                <strong>Aktifkan koneksi ini</strong>
                                <span>Koneksi aktif akan dipakai aplikasi saat membaca sumber SIMRS klinik tersebut.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="helper-note">
                    <div>
                        <strong>App DB pusat sekarang sudah siap diarahkan ke komputer Anda via ZeroTier.</strong>
                        Mode aktif saat ini: <code>{{ $appDbSummary['mode'] }}</code>,
                        host: <code>{{ $appDbSummary['host'] }}</code>,
                        database: <code>{{ $appDbSummary['database'] }}</code>.
                    </div>
                    <div>
                        Saat deploy ke VPS, cukup isi env berikut:
                        <code>APP_DB_CONNECTION</code>,
                        <code>APP_DB_ZERO_TIER_IP</code>,
                        <code>APP_DB_PORT</code>,
                        <code>APP_DB_DATABASE</code>,
                        <code>APP_DB_USERNAME</code>,
                        <code>APP_DB_PASSWORD</code>.
                    </div>
                    <div>
                        Untuk koneksi klinik, aplikasi akan mencoba <code>zero_tier_ip</code> lebih dulu.
                        Jika kosong, sistem fallback ke <code>host</code>.
                    </div>
                    <div>
                        Jika tes koneksi gagal dengan <code>auth_gssapi_client</code>, berarti user DB di server klinik memakai plugin autentikasi GSSAPI/Kerberos.
                        Solusinya buat user khusus aplikasi dengan autentikasi password biasa, lalu pakai user itu di form koneksi ini.
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Koneksi' }}
                    </button>

                    @if ($isEditing)
                        <form method="POST" action="{{ route('koneksi-db-klinik.test', $editingItem) }}" class="inline-action-form">
                            @csrf
                            <button type="submit" class="btn btn-ghost">Tes Koneksi</button>
                        </form>
                        <a href="{{ route('koneksi-db-klinik', $listingQuery) }}" class="btn btn-muted">Batal Edit</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="table-card">
            <div class="section-head">
                <div>
                    <h2>Daftar Koneksi Klinik</h2>
                    <p>Sumber ini dipakai tab transaksi pasien untuk membaca data rawat dari server klinik masing-masing.</p>
                </div>

                <form method="GET" action="{{ route('koneksi-db-klinik') }}" class="search-box">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari klinik, host, database, atau ZeroTier...">
                    <button type="submit" class="btn btn-ghost">Cari</button>
                </form>
            </div>

            @if ($records->isEmpty())
                <div class="empty-state">
                    Belum ada koneksi klinik yang tersimpan. Tambahkan dulu dari form di sebelah kiri.
                </div>
            @else
                <div class="table-wrap">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Klinik</th>
                                <th>Server</th>
                                <th>Host</th>
                                <th>ZeroTier</th>
                                <th>Database</th>
                                <th>Status</th>
                                <th>Verifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $record)
                                <tr>
                                    <td class="name-cell">
                                        <strong>{{ $record->clinicProfile?->nama_pendek ?: $record->clinicProfile?->nama_klinik ?: '-' }}</strong>
                                        <span>{{ $record->clinicProfile?->kode_klinik ?: '-' }}</span>
                                    </td>
                                    <td class="meta-stack">
                                        <strong>{{ $record->server_name ?: 'Server klinik' }}</strong>
                                        <span>Driver: {{ strtoupper($record->driver) }}</span>
                                    </td>
                                    <td class="meta-stack">
                                        <strong>{{ $record->host }}</strong>
                                        <span>Port: {{ $record->port }}</span>
                                    </td>
                                    <td>
                                        @if (filled($record->zero_tier_ip))
                                            <span class="pill">{{ $record->zero_tier_ip }}</span>
                                        @else
                                            <span class="meta-stack">
                                                <span>Belum diisi</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="meta-stack">
                                        <strong>{{ $record->database }}</strong>
                                        <span>User: {{ $record->username }}</span>
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $record->is_active ? 'is-active' : 'is-inactive' }}">
                                            {{ $record->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="meta-stack">
                                        <strong>{{ $record->last_verified_at?->format('d/m/Y H:i') ?: 'Belum dicek' }}</strong>
                                        <span>{{ $record->notes ?: 'Tanpa catatan' }}</span>
                                    </td>
                                    <td>
                                        <div class="row-actions">
                                            <form method="POST" action="{{ route('koneksi-db-klinik.test', $record) }}" class="inline-action-form">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost">Tes</button>
                                            </form>

                                            <a
                                                href="{{ route('koneksi-db-klinik', array_merge($listingQuery, ['edit' => $record->id])) }}"
                                                class="btn btn-ghost"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('koneksi-db-klinik.destroy', $record) }}"
                                                class="inline-action-form"
                                                data-confirm-delete
                                                data-confirm-title="Hapus koneksi DB klinik?"
                                                data-confirm-message="Konfigurasi koneksi yang dihapus harus diisi ulang jika nanti masih dibutuhkan."
                                                data-confirm-button="Ya, hapus koneksi"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="q" value="{{ $search }}">
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
</div>
@endsection
