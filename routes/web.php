<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportUiController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [ReportUiController::class, 'dashboard'])->name('dashboard');
    Route::get('/transaksi-pasien', [ReportUiController::class, 'transaksiPasien'])->name('transaksi-pasien');
    Route::post('/transaksi-pasien/sync-rekap', [ReportUiController::class, 'syncRekapPasien'])->name('transaksi-pasien.sync-rekap');
    Route::post('/transaksi-pasien', [ReportUiController::class, 'storeTransaksiPasien'])->name('transaksi-pasien.store');
    Route::delete('/transaksi-pasien/{transaksiPasien}', [ReportUiController::class, 'destroyTransaksiPasien'])->name('transaksi-pasien.destroy');
    Route::get('/input-pengeluaran', [ReportUiController::class, 'inputPengeluaran'])->name('input-pengeluaran');
    Route::post('/input-pengeluaran', [ReportUiController::class, 'storePengeluaran'])->name('input-pengeluaran.store');
    Route::delete('/input-pengeluaran/{pengeluaran}', [ReportUiController::class, 'destroyPengeluaran'])->name('input-pengeluaran.destroy');
    Route::get('/input-klaim-bpjs', [ReportUiController::class, 'inputKlaimBpjs'])->name('input-klaim-bpjs');
    Route::post('/input-klaim-bpjs', [ReportUiController::class, 'storeKlaimBpjs'])->name('input-klaim-bpjs.store');
    Route::delete('/input-klaim-bpjs/{bpjsKlaimBulanan}', [ReportUiController::class, 'destroyKlaimBpjs'])->name('input-klaim-bpjs.destroy');
    Route::get('/rekap-obat-pusat', [ReportUiController::class, 'rekapObatPusat'])->name('rekap-obat-pusat');
    Route::get('/rekap-stok-obat-pusat', [ReportUiController::class, 'rekapStokObatPusat'])->name('rekap-stok-obat-pusat');
    Route::get('/rekap-retur-obat-pusat', [ReportUiController::class, 'rekapReturObatPusat'])->name('rekap-retur-obat-pusat');
    Route::get('/rekap-pembelian-obat-pusat', [ReportUiController::class, 'rekapPembelianObatPusat'])->name('rekap-pembelian-obat-pusat');
    Route::get('/rekap-pasien-pusat', [ReportUiController::class, 'rekapPasienPusat'])->name('rekap-pasien-pusat');
    Route::get('/rekap-penyakit-pusat', [ReportUiController::class, 'rekapPenyakitPusat'])->name('rekap-penyakit-pusat');

    Route::middleware('admin_or_master')->group(function () {
        Route::put('/transaksi-pasien/{transaksiPasien}', [ReportUiController::class, 'updateTransaksiPasien'])->name('transaksi-pasien.update');
        Route::get('/profile-klinik', [ReportUiController::class, 'profileKlinik'])->name('profile-klinik');
        Route::post('/profile-klinik', [ReportUiController::class, 'saveProfileKlinik'])->name('profile-klinik.save');
        Route::put('/input-pengeluaran/{pengeluaran}', [ReportUiController::class, 'updatePengeluaran'])->name('input-pengeluaran.update');
        Route::put('/input-klaim-bpjs/{bpjsKlaimBulanan}', [ReportUiController::class, 'updateKlaimBpjs'])->name('input-klaim-bpjs.update');
        Route::get('/rekap-bulanan', [ReportUiController::class, 'rekapBulanan'])->name('rekap-bulanan');
        Route::get('/rekap-bulanan/pdf', [ReportUiController::class, 'downloadRekapBulananPdf'])->name('rekap-bulanan.pdf');
        Route::get('/rekap-tahunan', [ReportUiController::class, 'rekapTahunan'])->name('rekap-tahunan');
    });

    Route::middleware('master')->group(function () {
        Route::get('/kode-layanan', [ReportUiController::class, 'kodeLayanan'])->name('kode-layanan');
        Route::post('/kode-layanan', [ReportUiController::class, 'storeKodeLayanan'])->name('kode-layanan.store');
        Route::put('/kode-layanan/{masterLayanan}', [ReportUiController::class, 'updateKodeLayanan'])->name('kode-layanan.update');
        Route::delete('/kode-layanan/{masterLayanan}', [ReportUiController::class, 'destroyKodeLayanan'])->name('kode-layanan.destroy');
        Route::get('/kode-komponen-transaksi', [ReportUiController::class, 'kodeKomponenTransaksi'])->name('kode-komponen-transaksi');
        Route::post('/kode-komponen-transaksi', [ReportUiController::class, 'storeKodeKomponenTransaksi'])->name('kode-komponen-transaksi.store');
        Route::put('/kode-komponen-transaksi/{masterKomponenTransaksi}', [ReportUiController::class, 'updateKodeKomponenTransaksi'])->name('kode-komponen-transaksi.update');
        Route::delete('/kode-komponen-transaksi/{masterKomponenTransaksi}', [ReportUiController::class, 'destroyKodeKomponenTransaksi'])->name('kode-komponen-transaksi.destroy');
        Route::get('/kode-administrasi-pasien', [ReportUiController::class, 'kodeAdministrasiPasien'])->name('kode-administrasi-pasien');
        Route::post('/kode-administrasi-pasien', [ReportUiController::class, 'storeKodeAdministrasiPasien'])->name('kode-administrasi-pasien.store');
        Route::put('/kode-administrasi-pasien/{masterAdministrasiPasien}', [ReportUiController::class, 'updateKodeAdministrasiPasien'])->name('kode-administrasi-pasien.update');
        Route::delete('/kode-administrasi-pasien/{masterAdministrasiPasien}', [ReportUiController::class, 'destroyKodeAdministrasiPasien'])->name('kode-administrasi-pasien.destroy');
        Route::get('/kode-pengeluaran', [ReportUiController::class, 'kodePengeluaran'])->name('kode-pengeluaran');
        Route::post('/kode-pengeluaran', [ReportUiController::class, 'storeKodePengeluaran'])->name('kode-pengeluaran.store');
        Route::put('/kode-pengeluaran/{masterKategoriPengeluaran}', [ReportUiController::class, 'updateKodePengeluaran'])->name('kode-pengeluaran.update');
        Route::delete('/kode-pengeluaran/{masterKategoriPengeluaran}', [ReportUiController::class, 'destroyKodePengeluaran'])->name('kode-pengeluaran.destroy');
        Route::get('/koneksi-db-klinik', [ReportUiController::class, 'koneksiDbKlinik'])->name('koneksi-db-klinik');
        Route::post('/koneksi-db-klinik', [ReportUiController::class, 'storeKoneksiDbKlinik'])->name('koneksi-db-klinik.store');
        Route::put('/koneksi-db-klinik/{clinicDatabaseConnection}', [ReportUiController::class, 'updateKoneksiDbKlinik'])->name('koneksi-db-klinik.update');
        Route::delete('/koneksi-db-klinik/{clinicDatabaseConnection}', [ReportUiController::class, 'destroyKoneksiDbKlinik'])->name('koneksi-db-klinik.destroy');
        Route::post('/koneksi-db-klinik/{clinicDatabaseConnection}/test', [ReportUiController::class, 'testKoneksiDbKlinik'])->name('koneksi-db-klinik.test');
        Route::get('/manajemen-user', [UserManagementController::class, 'index'])->name('manajemen-user');
        Route::post('/manajemen-user', [UserManagementController::class, 'store'])->name('manajemen-user.store');
        Route::put('/manajemen-user/{user}', [UserManagementController::class, 'update'])->name('manajemen-user.update');
    });
});
