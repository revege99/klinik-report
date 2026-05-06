@extends('layouts.app')

@section('title', 'Profile Klinik | Klink Report')

@section('content')
<style>
    .clinic-shell {
        display: grid;
        gap: 20px;
    }

    .clinic-hero,
    .clinic-card,
    .profile-preview,
    .profile-note {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 24px 54px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(18px);
    }

    .clinic-hero {
        padding: 24px 26px;
        background:
            radial-gradient(circle at top right, rgba(37, 99, 235, 0.16), transparent 30%),
            radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.12), transparent 28%),
            linear-gradient(145deg, rgba(255, 255, 255, 0.96), rgba(241, 247, 255, 0.92));
    }

    .clinic-eyebrow {
        margin: 0;
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.2em;
        text-transform: uppercase;
    }

    .clinic-hero h1 {
        margin: 10px 0 12px;
        color: #10233c;
        font-size: clamp(1.8rem, 3vw, 2.6rem);
        line-height: 1.08;
    }

    .clinic-hero p {
        max-width: 70ch;
        margin: 0;
        color: #5f7088;
        font-size: 0.92rem;
        line-height: 1.8;
    }

    .clinic-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .clinic-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        border: 1px solid rgba(37, 99, 235, 0.12);
        background: rgba(255, 255, 255, 0.72);
        color: #1f3b64;
        font-size: 0.77rem;
        font-weight: 700;
    }

    .clinic-grid {
        display: grid;
        gap: 20px;
        grid-template-columns: minmax(0, 1.3fr) minmax(320px, 0.82fr);
        align-items: start;
    }

    .clinic-card,
    .profile-preview,
    .profile-note {
        padding: 22px;
    }

    .card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .card-head h2,
    .preview-head h2 {
        margin: 0;
        color: #15263d;
        font-size: 1.06rem;
        line-height: 1.2;
    }

    .card-head p,
    .preview-head p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.7;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 7px 11px;
        border-radius: 999px;
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .success-banner {
        margin-bottom: 18px;
        padding: 14px 16px;
        border: 1px solid rgba(16, 185, 129, 0.18);
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(236, 253, 245, 0.96), rgba(220, 252, 231, 0.92));
        color: #047857;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .profile-form {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.span-2 {
        grid-column: span 2;
    }

    .form-group label {
        color: #21334d;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 16px;
        padding: 13px 14px;
        background: rgba(255, 255, 255, 0.92);
        color: #0f172a;
        font-size: 0.88rem;
        outline: none;
        transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
    }

    .form-group textarea {
        min-height: 108px;
        resize: vertical;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: rgba(37, 99, 235, 0.36);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
    }

    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.24);
        background: rgba(248, 250, 252, 0.92);
    }

    .toggle-wrap input[type="checkbox"] {
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

    .form-hint,
    .form-error {
        font-size: 0.74rem;
        line-height: 1.6;
    }

    .form-hint {
        color: #64748b;
    }

    .form-error {
        color: #c2410c;
        font-weight: 700;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 6px;
        grid-column: span 2;
    }

    .form-actions p {
        margin: 0;
        color: #64748b;
        font-size: 0.78rem;
        line-height: 1.7;
    }

    .save-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 20px;
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, #2563eb, #38bdf8);
        color: #ffffff;
        font-size: 0.86rem;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 18px 36px rgba(37, 99, 235, 0.2);
        transition: transform 180ms ease, box-shadow 180ms ease;
    }

    .save-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 22px 40px rgba(37, 99, 235, 0.26);
    }

    .logo-upload-card {
        display: grid;
        gap: 14px;
        grid-template-columns: 96px minmax(0, 1fr);
        align-items: center;
        padding: 16px;
        border: 1px dashed rgba(15, 118, 110, 0.28);
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(240, 253, 250, 0.96), rgba(248, 250, 252, 0.98));
    }

    .logo-upload-preview {
        display: flex;
        height: 96px;
        width: 96px;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 24px;
        background: linear-gradient(145deg, #f8fafc, #dbeafe);
        border: 1px solid rgba(148, 163, 184, 0.24);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
    }

    .logo-upload-preview img {
        max-width: 82%;
        max-height: 82%;
        object-fit: contain;
    }

    .logo-upload-fallback {
        color: #0f766e;
        font-size: 1.2rem;
        font-weight: 800;
        letter-spacing: 0.08em;
    }

    .logo-upload-copy {
        min-width: 0;
    }

    .logo-upload-copy input[type="file"] {
        padding: 10px 12px;
        background: #ffffff;
    }

    .logo-upload-copy p {
        margin: 0 0 10px;
        color: #475569;
        font-size: 0.78rem;
        line-height: 1.7;
    }

    .profile-stack {
        display: grid;
        gap: 20px;
    }

    .profile-preview {
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, 0.16), transparent 28%),
            linear-gradient(180deg, rgba(248, 251, 255, 0.98), rgba(238, 245, 255, 0.92));
    }

    .preview-brand {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 16px;
        padding: 18px;
        border-radius: 24px;
        background: linear-gradient(180deg, #18305f, #101c39);
        box-shadow: 0 24px 42px rgba(15, 23, 42, 0.18);
    }

    .preview-mark {
        display: inline-flex;
        height: 52px;
        width: 52px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: radial-gradient(circle at 30% 30%, #b8d7ff 0%, #67adff 40%, #2563eb 100%);
        color: #ffffff;
        font-size: 1rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        box-shadow: 0 18px 34px rgba(37, 99, 235, 0.26);
    }

    .preview-mark.is-logo {
        overflow: hidden;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.16);
    }

    .preview-mark.is-logo img {
        max-width: 84%;
        max-height: 84%;
        object-fit: contain;
    }

    .preview-copy {
        min-width: 0;
    }

    .preview-copy span {
        display: block;
        color: rgba(214, 226, 245, 0.76);
        font-size: 0.66rem;
        font-weight: 800;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    .preview-copy strong {
        display: block;
        margin-top: 6px;
        color: #f8fbff;
        font-size: 1.06rem;
        line-height: 1.3;
    }

    .preview-copy p {
        margin: 6px 0 0;
        color: rgba(219, 231, 246, 0.76);
        font-size: 0.76rem;
        line-height: 1.65;
    }

    .preview-contact {
        display: grid;
        gap: 10px;
        margin-top: 18px;
    }

    .preview-item {
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(148, 163, 184, 0.14);
    }

    .preview-item span {
        display: block;
        color: #64748b;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .preview-item strong,
    .preview-item p {
        display: block;
        margin-top: 7px;
        color: #12233b;
        font-size: 0.84rem;
        line-height: 1.7;
    }

    .profile-note ul {
        margin: 16px 0 0;
        padding-left: 18px;
        color: #5f7088;
        font-size: 0.8rem;
        line-height: 1.8;
    }

    .profile-note li + li {
        margin-top: 6px;
    }

    @media (max-width: 1080px) {
        .clinic-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .clinic-hero,
        .clinic-card,
        .profile-preview,
        .profile-note {
            border-radius: 24px;
            padding: 18px;
        }

        .profile-form {
            grid-template-columns: 1fr;
        }

        .form-group.span-2,
        .form-actions {
            grid-column: span 1;
        }

        .form-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

@php
    $currentUser = auth()->user();
    $canManageCode = $currentUser?->isMaster();
    $profileName = old('nama_klinik', $clinicProfile?->nama_klinik ?: 'Klink Report');
    $profileShortName = old('nama_pendek', $clinicProfile?->nama_pendek ?: $profileName);
    $profileTagline = old('tagline', $clinicProfile?->tagline ?: 'Laporan operasional klinik yang rapi dan terkendali.');
    $profileCode = old('kode_klinik', $clinicProfile?->kode_klinik ?: '');
    $profileLogoDataUri = $clinicLogoPreviewDataUri ?? null;
    $previewInitials = collect(preg_split('/\s+/', trim((string) $profileShortName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<div class="clinic-shell">
    <section class="clinic-hero">
        <p class="clinic-eyebrow">Identitas Klinik</p>
        <h1>Profile Klinik yang Siap Tampil Lebih Profesional</h1>
        <p>
            Lengkapi data inti klinik sekali saja, lalu nama, tagline, dan informasi pentingnya akan otomatis
            terasa konsisten di sidebar dan dashboard tanpa mengganggu desain utama aplikasi.
        </p>

        <div class="clinic-meta">
            <span class="clinic-chip">{{ $profileCode ?: 'Kode klinik belum diatur' }}</span>
            <span class="clinic-chip">{{ $profileShortName ?: 'Nama singkat klinik' }}</span>
            <span class="clinic-chip">{{ old('telepon', $clinicProfile?->telepon ?: 'Kontak klinik') }}</span>
            <span class="clinic-chip">{{ old('jam_pelayanan', $clinicProfile?->jam_pelayanan ?: 'Jam pelayanan klinik') }}</span>
        </div>
    </section>

    <div class="clinic-grid">
        <section class="clinic-card">
            <div class="card-head">
                <div>
                    <h2>Data Umum Klinik</h2>
                    <p>
                        {{ $canManageCode ? 'Master bisa memilih klinik aktif, membuat klinik baru, dan mengatur kode klinik dari halaman ini.' : 'Form ini dipakai untuk identitas utama klinik Anda dan tampilan dashboard.' }}
                    </p>
                </div>

                <span class="status-pill">
                    {{ $clinicProfile ? ($clinicProfile->is_active ? 'Aktif' : 'Nonaktif') : 'Siap Disimpan' }}
                </span>
            </div>

            @if ($showClinicFilter)
                <div class="card-head" style="margin-top: -4px;">
                    <form method="GET" action="{{ route('profile-klinik') }}" class="profile-form" style="grid-template-columns: minmax(0, 1fr) auto auto;">
                        <div class="form-group">
                            <label for="clinic_id">Klinik Aktif</label>
                            <select id="clinic_id" name="clinic_id">
                                @foreach ($clinicOptions as $clinicOption)
                                    <option value="{{ $clinicOption->id }}" @selected((int) $selectedClinicId === (int) $clinicOption->id)>
                                        {{ $clinicOption->kode_klinik }} · {{ $clinicOption->nama_klinik }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" style="justify-content: flex-end;">
                            <label>&nbsp;</label>
                            <button type="submit" class="save-button" style="min-width: 140px;">Tampilkan Klinik</button>
                        </div>

                        <div class="form-group" style="justify-content: flex-end;">
                            <label>&nbsp;</label>
                            <a href="{{ route('profile-klinik', ['mode' => 'create']) }}" class="save-button" style="text-decoration: none; min-width: 130px; background: linear-gradient(135deg, #0f766e, #0ea5a4);">
                                Klinik Baru
                            </a>
                        </div>
                    </form>
                </div>
            @endif

            @if (session('success'))
                <div class="success-banner">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('profile-klinik.save') }}" class="profile-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="profile_mode" value="{{ $isCreateMode ? 'create' : 'edit' }}">
                <input type="hidden" name="profile_id" value="{{ $clinicProfile?->id }}">

                <div class="form-group">
                    <label for="kode_klinik">Kode Klinik</label>
                    <input
                        id="kode_klinik"
                        type="text"
                        name="kode_klinik"
                        value="{{ $profileCode }}"
                        placeholder="Contoh: KLN001"
                        @if (! $canManageCode) readonly @endif
                    >
                    <div class="form-hint">
                        {{ $canManageCode ? 'Kode unik ini dipakai untuk scoping semua data klinik di aplikasi.' : 'Kode klinik dikelola oleh user master.' }}
                    </div>
                    @error('kode_klinik')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group {{ $canManageCode ? '' : 'span-2' }}">
                    <label for="nama_klinik">Nama Klinik</label>
                    <input id="nama_klinik" type="text" name="nama_klinik" value="{{ old('nama_klinik', $clinicProfile?->nama_klinik) }}" placeholder="Contoh: Klinik Kasih Ibu Sehat">
                    @error('nama_klinik')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nama_pendek">Nama Pendek</label>
                    <input id="nama_pendek" type="text" name="nama_pendek" value="{{ old('nama_pendek', $clinicProfile?->nama_pendek) }}" placeholder="Contoh: Klinik KIS">
                    <div class="form-hint">Dipakai untuk judul singkat di sidebar.</div>
                    @error('nama_pendek')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group span-2">
                    <label for="logo_file">Logo Klinik</label>
                    <div class="logo-upload-card">
                        <div class="logo-upload-preview" id="logo-upload-preview">
                            @if ($profileLogoDataUri)
                                <img src="{{ $profileLogoDataUri }}" alt="Logo klinik" id="logo-upload-image">
                            @else
                                <span class="logo-upload-fallback" id="logo-upload-fallback">{{ $previewInitials ?: 'KR' }}</span>
                                <img src="" alt="Logo klinik" id="logo-upload-image" hidden>
                            @endif
                        </div>

                        <div class="logo-upload-copy">
                            <p>Upload logo klinik agar tampil lebih profesional di profil dan otomatis masuk ke header PDF laporan.</p>
                            <input id="logo_file" type="file" name="logo_file" accept=".png,.jpg,.jpeg">
                            <div class="form-hint">Format yang didukung: PNG atau JPG. Maksimal 2 MB.</div>
                        </div>
                    </div>
                    @error('logo_file')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tagline">Tagline</label>
                    <input id="tagline" type="text" name="tagline" value="{{ old('tagline', $clinicProfile?->tagline) }}" placeholder="Contoh: Pelayanan hangat, laporan tetap rapi">
                    @error('tagline')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group span-2">
                    <label for="alamat">Alamat Klinik</label>
                    <textarea id="alamat" name="alamat" placeholder="Masukkan alamat lengkap klinik">{{ old('alamat', $clinicProfile?->alamat) }}</textarea>
                    @error('alamat')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kecamatan">Kecamatan</label>
                    <input id="kecamatan" type="text" name="kecamatan" value="{{ old('kecamatan', $clinicProfile?->kecamatan) }}" placeholder="Contoh: Siborongborong">
                    @error('kecamatan')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kota">Kota / Kabupaten</label>
                    <input id="kota" type="text" name="kota" value="{{ old('kota', $clinicProfile?->kota) }}" placeholder="Contoh: Medan">
                    @error('kota')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="provinsi">Provinsi</label>
                    <input id="provinsi" type="text" name="provinsi" value="{{ old('provinsi', $clinicProfile?->provinsi) }}" placeholder="Contoh: Sumatera Utara">
                    @error('provinsi')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kode_pos">Kode Pos</label>
                    <input id="kode_pos" type="text" name="kode_pos" value="{{ old('kode_pos', $clinicProfile?->kode_pos) }}" placeholder="Contoh: 20154">
                    @error('kode_pos')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="telepon">Telepon / WhatsApp</label>
                    <input id="telepon" type="text" name="telepon" value="{{ old('telepon', $clinicProfile?->telepon) }}" placeholder="Contoh: 0812-3456-7890">
                    @error('telepon')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Klinik</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $clinicProfile?->email) }}" placeholder="Contoh: admin@klinikanda.id">
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="website">Website / Link Profil</label>
                    <input id="website" type="text" name="website" value="{{ old('website', $clinicProfile?->website) }}" placeholder="Contoh: https://klinikanda.id">
                    @error('website')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="penanggung_jawab">Penanggung Jawab</label>
                    <input id="penanggung_jawab" type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $clinicProfile?->penanggung_jawab) }}" placeholder="Contoh: dr. Maria Simanjuntak">
                    @error('penanggung_jawab')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="jam_pelayanan">Jam Pelayanan</label>
                    <input id="jam_pelayanan" type="text" name="jam_pelayanan" value="{{ old('jam_pelayanan', $clinicProfile?->jam_pelayanan) }}" placeholder="Contoh: Senin - Sabtu · 08.00 - 21.00">
                    @error('jam_pelayanan')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                @if ($canManageCode)
                    <div class="form-group">
                        <label>Status Klinik</label>
                        <label class="toggle-wrap">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $clinicProfile?->is_active ?? true) ? 'checked' : '' }}>
                            <span class="toggle-copy">
                                <strong>Klinik aktif</strong>
                                <span>Klinik nonaktif tidak akan muncul di pilihan input dan laporan.</span>
                            </span>
                        </label>
                    </div>
                @endif

                <div class="form-group span-2">
                    <label for="deskripsi_singkat">Deskripsi Singkat</label>
                    <textarea id="deskripsi_singkat" name="deskripsi_singkat" placeholder="Ceritakan singkat karakter pelayanan klinik atau fungsi dashboard ini.">{{ old('deskripsi_singkat', $clinicProfile?->deskripsi_singkat) }}</textarea>
                    <div class="form-hint">Akan dipakai sebagai copy pendukung di area dashboard.</div>
                    @error('deskripsi_singkat')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <p>
                        {{ $isCreateMode ? 'Klinik baru yang disimpan akan langsung siap dipakai untuk user, transaksi, dan laporan.' : 'Perubahan yang disimpan akan langsung dipakai sebagai identitas utama klinik di tampilan aplikasi.' }}
                    </p>
                    <button type="submit" class="save-button">{{ $isCreateMode ? 'Simpan Klinik Baru' : 'Simpan Profile Klinik' }}</button>
                </div>
            </form>
        </section>

        <div class="profile-stack">
            <aside class="profile-preview">
                <div class="preview-head">
                    <h2>Preview Branding</h2>
                    <p>Tampilan ini menggambarkan nuansa identitas klinik saat dipakai pada sidebar dan dashboard.</p>
                </div>

                <div class="preview-brand">
                    <div class="preview-mark {{ $profileLogoDataUri ? 'is-logo' : '' }}" id="preview-brand-mark">
                        @if ($profileLogoDataUri)
                            <img src="{{ $profileLogoDataUri }}" alt="Logo klinik" id="preview-brand-image">
                        @else
                            <span id="preview-brand-fallback">{{ $previewInitials ?: 'KR' }}</span>
                            <img src="" alt="Logo klinik" id="preview-brand-image" hidden>
                        @endif
                    </div>
                    <div class="preview-copy">
                        <span>Identitas Klinik</span>
                        <strong>{{ $profileShortName ?: $profileName }}</strong>
                        <p>{{ \Illuminate\Support\Str::limit($profileTagline ?: 'Tagline klinik akan muncul di sini.', 70) }}</p>
                    </div>
                </div>

                <div class="preview-contact">
                    <div class="preview-item">
                        <span>Nama Lengkap</span>
                        <strong>{{ $profileName }}</strong>
                    </div>

                    <div class="preview-item">
                        <span>Kontak Utama</span>
                        <p>
                            {{ old('telepon', $clinicProfile?->telepon ?: 'Belum diisi') }}<br>
                            {{ old('email', $clinicProfile?->email ?: 'Email belum diisi') }}
                        </p>
                    </div>

                    <div class="preview-item">
                        <span>Alamat Singkat</span>
                        <p>
                            {{ old('alamat', $clinicProfile?->alamat ?: 'Alamat klinik akan tampil di sini.') }}
                            @if (filled(old('kecamatan', $clinicProfile?->kecamatan)))
                                <br>Kec. {{ old('kecamatan', $clinicProfile?->kecamatan) }}
                            @endif
                            @if (filled(old('kota', $clinicProfile?->kota)))
                                <br>{{ old('kota', $clinicProfile?->kota) }}
                            @endif
                        </p>
                    </div>
                </div>
            </aside>

            <aside class="profile-note">
                <div class="preview-head">
                    <h2>Dipakai di Mana?</h2>
                    <p>Begitu profil klinik tersimpan, identitas ini akan ikut hidup di area penting aplikasi.</p>
                </div>

                <ul>
                    <li>Nama pendek klinik akan menggantikan tulisan header sidebar.</li>
                    <li>Tagline dan deskripsi singkat dipakai untuk memperhalus kesan dashboard.</li>
                    <li>Kontak dan lokasi siap dipakai untuk identitas laporan internal klinik.</li>
                </ul>
            </aside>
        </div>
    </div>
</div>

<script>
    (() => {
        const input = document.getElementById('logo_file');
        const uploadImage = document.getElementById('logo-upload-image');
        const uploadFallback = document.getElementById('logo-upload-fallback');
        const previewMark = document.getElementById('preview-brand-mark');
        const previewImage = document.getElementById('preview-brand-image');
        const previewFallback = document.getElementById('preview-brand-fallback');

        if (! input || ! uploadImage || ! previewImage || ! previewMark) {
            return;
        }

        input.addEventListener('change', (event) => {
            const file = event.target.files?.[0];

            if (! file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = () => {
                const result = typeof reader.result === 'string' ? reader.result : '';

                if (result === '') {
                    return;
                }

                uploadImage.src = result;
                uploadImage.hidden = false;
                previewImage.src = result;
                previewImage.hidden = false;
                previewMark.classList.add('is-logo');

                if (uploadFallback) {
                    uploadFallback.hidden = true;
                }

                if (previewFallback) {
                    previewFallback.hidden = true;
                }
            };

            reader.readAsDataURL(file);
        });
    })();
</script>
@endsection
