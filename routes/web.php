<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PengajuanKoreksiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\PublicVerificationController;
use App\Http\Controllers\SiakadController;
use App\Http\Controllers\SiakadMataKuliahController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\TranskripController;
use App\Http\Controllers\WisudawanController;
use Illuminate\Support\Facades\Route;

// Public routes - Directly redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/verifikasi/{kode}', [PublicVerificationController::class, 'verify'])->name('verifikasi.transkrip');

// Authenticated shared
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Super Admin Routes (Monitoring / Supervisi - Read Only Data & Manajemen Admin Fakultas)
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/transkrip/{transkrip}', [SuperAdminController::class, 'showTranskrip'])->name('transkrip.show');
    Route::post('/transkrip/{transkrip}/ratify', [SuperAdminController::class, 'ratifyTranskrip'])->name('transkrip.ratify');
    Route::get('/transkrip/{transkrip}/pdf', [TranskripController::class, 'downloadPdf'])->name('transkrip.pdf');

    // Manajemen Admin Fakultas (Super Admin creates & approves)
    Route::post('/admin-fakultas', [SuperAdminController::class, 'storeAdminFakultas'])->name('admin-fakultas.store');
    Route::post('/requests/{req}/approve', [SuperAdminController::class, 'approveRequest'])->name('requests.approve');
    Route::post('/requests/{req}/reject', [SuperAdminController::class, 'rejectRequest'])->name('requests.reject');
    Route::patch('/admin-fakultas/{user}/toggle-status', [SuperAdminController::class, 'toggleStatus'])->name('admin-fakultas.toggle');
    Route::delete('/admin-fakultas/{user}', [SuperAdminController::class, 'destroyAdmin'])->name('admin-fakultas.destroy');

    // Aturan Bobot Nilai & SIAKAD Bridge Integration
    Route::put('/bobot-nilai/{bobot}', [SuperAdminController::class, 'updateBobotNilai'])->name('bobot-nilai.update');
    Route::post('/siakad/sync', [SuperAdminController::class, 'testSiakadSync'])->name('siakad.sync');

    // Read-only & Supervision Directory
    Route::get('/mahasiswa', [SuperAdminController::class, 'mahasiswa'])->name('mahasiswa.index');
    Route::get('/wisudawan', [SuperAdminController::class, 'wisudawan'])->name('wisudawan.index');
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    // Input & Edit Nilai Manual Seluruh Fakultas
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilai/create', [NilaiController::class, 'create'])->name('nilai.create');
    Route::post('/nilai', [NilaiController::class, 'store'])->name('nilai.store');
    Route::put('/nilai/{nilai}', [NilaiController::class, 'update'])->name('nilai.update');
});

// Admin Fakultas & Super Admin Routes (Operasional Akademik & Transkrip)
Route::middleware(['auth', 'role:admin,admin_fakultas,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Master Data
    Route::post('/fakultas/sync-api', [FakultasController::class, 'syncApi'])->name('fakultas.sync-api');
    Route::resource('fakultas', FakultasController::class)->parameters(['fakultas' => 'fakultas'])->except(['create', 'show', 'edit']);
    Route::post('/program-studi/sync-api', [ProgramStudiController::class, 'syncApi'])->name('program-studi.sync-api');
    Route::resource('program-studi', ProgramStudiController::class)->except(['create', 'show', 'edit']);
    Route::resource('mata-kuliah', SiakadMataKuliahController::class)->except(['create', 'show', 'edit']);
    Route::resource('mahasiswa', MahasiswaController::class);

    // SIAKAD Module
    Route::get('/siakad/mahasiswa', [SiakadController::class, 'mahasiswa'])->name('siakad.mahasiswa');
    Route::post('/siakad/mahasiswa/import', [SiakadController::class, 'importMahasiswa'])->name('siakad.mahasiswa.import');
    Route::get('/siakad/mahasiswa/template', [SiakadController::class, 'downloadMahasiswaTemplate'])->name('siakad.mahasiswa.template');

    Route::get('/siakad/nilai', [SiakadController::class, 'nilai'])->name('siakad.nilai');
    Route::post('/siakad/nilai/import', [SiakadController::class, 'importNilai'])->name('siakad.nilai.import');
    Route::get('/siakad/nilai/template', [SiakadController::class, 'downloadNilaiTemplate'])->name('siakad.nilai.template');

    // Pengelolaan Nilai Akademik (Manual, Massal, Edit & Conflict Detection)
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilai/create', [NilaiController::class, 'create'])->name('nilai.create');
    Route::post('/nilai', [NilaiController::class, 'store'])->name('nilai.store');
    Route::get('/nilai/mass', [NilaiController::class, 'massCreate'])->name('nilai.mass');
    Route::post('/nilai/mass', [NilaiController::class, 'massStore'])->name('nilai.mass.store');
    Route::put('/nilai/{nilai}', [NilaiController::class, 'update'])->name('nilai.update');

    // Pengelolaan Pengajuan Koreksi Nilai / Data
    Route::get('/koreksi', [PengajuanKoreksiController::class, 'index'])->name('koreksi.index');
    Route::post('/koreksi/{koreksi}/approve', [PengajuanKoreksiController::class, 'approve'])->name('koreksi.approve');
    Route::post('/koreksi/{koreksi}/reject', [PengajuanKoreksiController::class, 'reject'])->name('koreksi.reject');

    // Wisudawan Module
    Route::post('/wisudawan/sync-ewisuda', [WisudawanController::class, 'syncEwisuda'])->name('wisudawan.sync-ewisuda');
    Route::post('/wisudawan/import', [WisudawanController::class, 'import'])->name('wisudawan.import');
    Route::get('/wisudawan/template', [WisudawanController::class, 'downloadTemplate'])->name('wisudawan.template');
    Route::resource('wisudawan', WisudawanController::class);

    // Transkrip Module (Operasional: Sinkronisasi, Validasi, Detail, Cetak PDF)
    Route::get('/transkrip', [TranskripController::class, 'index'])->name('transkrip.index');
    Route::post('/transkrip/sync', [TranskripController::class, 'sync'])->name('transkrip.sync');
    Route::get('/transkrip/{transkrip}', [TranskripController::class, 'show'])->name('transkrip.show');
    Route::post('/transkrip/{transkrip}/validate', [TranskripController::class, 'validateTranskrip'])->name('transkrip.validate');
    Route::get('/transkrip/{transkrip}/pdf', [TranskripController::class, 'downloadPdf'])->name('transkrip.pdf');

    // Audit Log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
});

// Mahasiswa Routes
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'mahasiswa'])->name('dashboard');
    Route::get('/transkrip/{transkrip}/pdf', [TranskripController::class, 'downloadPdf'])->name('transkrip.pdf');
    Route::post('/koreksi', [PengajuanKoreksiController::class, 'store'])->name('koreksi.store');
});

require __DIR__.'/auth.php';
