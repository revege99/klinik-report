@extends('layouts.app')

@section('title', 'Manajemen User | Klink Report')

@section('content')
<style>
    .user-shell {
        display: grid;
        gap: 18px;
    }

    .user-shell > * {
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
        max-width: 70ch;
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
        grid-template-columns: minmax(340px, 460px) minmax(0, 1fr);
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

    .user-form {
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
        padding: 14px 16px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(239, 246, 255, 0.96), rgba(224, 242, 254, 0.9));
        border: 1px solid rgba(56, 189, 248, 0.14);
        color: #1e3a5f;
        font-size: 0.78rem;
        line-height: 1.75;
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

    .user-table {
        width: 100%;
        min-width: 860px;
        border-collapse: collapse;
    }

    .user-table th,
    .user-table td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.88);
        vertical-align: top;
    }

    .user-table th {
        color: #64748b;
        font-size: 0.69rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
    }

    .user-table td {
        color: #1f2937;
        font-size: 0.82rem;
        line-height: 1.6;
    }

    .user-badge {
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

    .user-name strong {
        display: block;
        color: #13263f;
        font-size: 0.86rem;
        line-height: 1.4;
    }

    .user-name span,
    .meta-text {
        display: block;
        margin-top: 3px;
        color: #64748b;
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

    .role-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(79, 70, 229, 0.1);
        color: #4338ca;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
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
    $isEditing = filled($editingUser);
    $editingProfile = $editingUser?->pegawaiProfile;
@endphp

<div class="user-shell">
    <section class="hero-card">
        <div class="hero-copy">
            <p class="page-eyebrow">Administrasi Akses</p>
            <h1>Manajemen User & Pegawai</h1>
            <p>
                Tambahkan akun baru dari UI, atur role admin atau staff, lalu lengkapi identitas pegawai
                agar operator pada transaksi dan pengeluaran selalu tercatat lebih rapi.
            </p>
        </div>

        <div class="hero-stats">
            <article class="hero-stat">
                <span>Total User</span>
                <strong>{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</strong>
            </article>
            <article class="hero-stat">
                <span>Admin</span>
                <strong>{{ number_format($stats['admin'] ?? 0, 0, ',', '.') }}</strong>
            </article>
            <article class="hero-stat">
                <span>User Aktif</span>
                <strong>{{ number_format($stats['active'] ?? 0, 0, ',', '.') }}</strong>
            </article>
            <article class="hero-stat">
                <span>Profil Pegawai</span>
                <strong>{{ number_format($stats['pegawai'] ?? 0, 0, ',', '.') }}</strong>
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
            <p>Masih ada data user yang perlu diperbaiki.</p>
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
                    <h2>{{ $isEditing ? 'Edit User' : 'Tambah User Baru' }}</h2>
                    <p>Isi akun login dan profil pegawai sekaligus dalam satu form.</p>
                </div>
                <span class="panel-chip">{{ $isEditing ? 'Mode Edit' : 'Mode Baru' }}</span>
            </div>

            <form
                method="POST"
                action="{{ $isEditing ? route('manajemen-user.update', $editingUser) : route('manajemen-user.store') }}"
                class="user-form"
            >
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <input type="hidden" name="is_active" value="0">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $editingUser?->name) }}" placeholder="Nama user">
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username', $editingUser?->username) }}" placeholder="contoh: kasir01">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $editingUser?->email) }}" placeholder="contoh@domain.com">
                    </div>

                    <div class="form-group">
                        <label for="password">{{ $isEditing ? 'Password Baru' : 'Password' }}</label>
                        <input id="password" type="password" name="password" placeholder="{{ $isEditing ? 'Kosongkan bila tidak diubah' : 'Minimal 8 karakter' }}">
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role">
                            <option value="staff" @selected(old('role', $editingUser?->role ?? 'staff') === 'staff')>Staff</option>
                            <option value="admin" @selected(old('role', $editingUser?->role) === 'admin')>Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nip">NIP / ID Pegawai</label>
                        <input id="nip" type="text" name="nip" value="{{ old('nip', $editingProfile?->nip) }}" placeholder="Opsional">
                    </div>

                    <div class="form-group">
                        <label for="jabatan">Jabatan</label>
                        <input id="jabatan" type="text" name="jabatan" value="{{ old('jabatan', $editingProfile?->jabatan) }}" placeholder="Contoh: Administrator Sistem">
                    </div>

                    <div class="form-group">
                        <label for="unit_kerja">Unit Kerja</label>
                        <input id="unit_kerja" type="text" name="unit_kerja" value="{{ old('unit_kerja', $editingProfile?->unit_kerja) }}" placeholder="Contoh: Yayasan / Klinik">
                    </div>

                    <div class="form-group">
                        <label for="phone_number">No. Telepon</label>
                        <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number', $editingProfile?->phone_number) }}" placeholder="Opsional">
                    </div>

                    <div class="form-group is-full">
                        <label for="bio">Catatan Pegawai</label>
                        <textarea id="bio" name="bio" rows="3" placeholder="Catatan singkat bila diperlukan">{{ old('bio', $editingProfile?->bio) }}</textarea>
                    </div>

                    <div class="form-group is-full">
                        <label>Status</label>
                        <label class="toggle-wrap">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $editingUser?->is_active ?? true) ? 'checked' : '' }}>
                            <span class="toggle-copy">
                                <strong>Aktifkan akun user ini</strong>
                                <span>User nonaktif tidak bisa login ke panel admin.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="helper-note">
                    Hanya user dengan <strong>role admin</strong> yang bisa membuka menu ini. Untuk edit user lama, pilih tombol
                    <strong>Edit</strong> pada tabel di samping.
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">{{ $isEditing ? 'Simpan Perubahan' : 'Simpan User' }}</button>

                    @if ($isEditing)
                        <a href="{{ route('manajemen-user', ['q' => $search ?: null]) }}" class="btn btn-muted">Batal Edit</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="table-card">
            <div class="section-head">
                <div>
                    <h2>Daftar User</h2>
                    <p>Semua akun yang bisa masuk ke sistem beserta informasi pegawai dan status aksesnya.</p>
                </div>

                <form method="GET" action="{{ route('manajemen-user') }}" class="search-box">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, username, jabatan...">
                    <button type="submit" class="btn btn-ghost">Cari</button>
                </form>
            </div>

            @if ($users->isEmpty())
                <div class="empty-state">
                    Belum ada user untuk filter ini. Tambahkan akun baru lewat panel form di sebelah kiri.
                </div>
            @else
                <div class="table-wrap">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Akses</th>
                                <th>Pegawai</th>
                                <th>Login Terakhir</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="user-name">
                                        <strong>{{ $user->name }}</strong>
                                        <span>{{ $user->username }} • {{ $user->email }}</span>
                                    </td>
                                    <td>
                                        <span class="role-pill">{{ strtoupper($user->role ?: 'staff') }}</span>
                                    </td>
                                    <td>
                                        <div class="meta-text">
                                            {{ $user->pegawaiProfile?->jabatan ?: 'Jabatan belum diisi' }}
                                        </div>
                                        <div class="meta-text">
                                            {{ $user->pegawaiProfile?->unit_kerja ?: 'Unit kerja belum diisi' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="meta-text">
                                            {{ $user->last_login_at?->locale('id')->translatedFormat('d M Y H:i') ?: 'Belum pernah login' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $user->is_active ? 'is-active' : 'is-inactive' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('manajemen-user', ['edit' => $user->id, 'q' => $search ?: null]) }}" class="btn btn-ghost">
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
