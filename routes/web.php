<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Operator\DashboardController;
use App\Http\Controllers\Operator\CatatanMeterController;
use App\Http\Controllers\Operator\ProfileController as OperatorProfileController;
use App\Http\Controllers\Teller\DashboardController as TellerDashboard;
use App\Http\Controllers\Teller\KasirController;
use App\Http\Controllers\Teller\ProfileController as TellerProfileController;
use App\Http\Controllers\Pelanggan\DashboardController as PelangganDashboard;
use App\Http\Controllers\Pelanggan\ProfileController;
use App\Http\Controllers\Pelanggan\TagihanController;
use App\Http\Controllers\Pelanggan\PengaduanController;
use App\Http\Controllers\Admin\LaporanController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Operator
Route::prefix('operator')->middleware(['auth', 'role:operator'])->name('operator.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('catatan-meter')->name('catatan-meter.')->group(function () {
        Route::get('/',          [CatatanMeterController::class, 'index'])->name('index');
        Route::get('/tambah',    [CatatanMeterController::class, 'create'])->name('create');
        Route::post('/',         [CatatanMeterController::class, 'store'])->name('store');
        Route::get('/{catatanMeter}', [CatatanMeterController::class, 'show'])->name('show');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',          [OperatorProfileController::class, 'index'])->name('index');
        Route::post('/password', [OperatorProfileController::class, 'updatePassword'])->name('password');
        Route::post('/logout',   [OperatorProfileController::class, 'logout'])->name('logout');
    });
});

// Teller
Route::prefix('teller')->middleware(['auth', 'role:teller'])->name('teller.')->group(function () {
    Route::get('/', [TellerDashboard::class, 'index'])->name('dashboard');

    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/',                    [KasirController::class, 'index'])->name('index');
        Route::post('/bayar',              [KasirController::class, 'bayar'])->name('bayar');
        Route::get('/nota/{pembayaran}',   [KasirController::class, 'nota'])->name('nota');
        Route::get('/riwayat',             [KasirController::class, 'riwayat'])->name('riwayat');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',                 [TellerProfileController::class, 'index'])->name('index');
        Route::post('/password',        [TellerProfileController::class, 'updatePassword'])->name('password');
        Route::post('/logout',          [TellerProfileController::class, 'logout'])->name('logout');
    });
});

// Pelanggan
Route::prefix('pelanggan')->middleware(['auth', 'role:pelanggan'])->name('pelanggan.')->group(function () {
    Route::get('/', [PelangganDashboard::class, 'index'])->name('dashboard');

    // Tagihan & QRIS
    Route::prefix('tagihan')->name('tagihan.')->group(function () {
        Route::get('/',                          [TagihanController::class, 'index'])->name('index');
        Route::get('/{tagihan}',                 [TagihanController::class, 'show'])->name('show');
        Route::get('/{tagihan}/qris',            [TagihanController::class, 'qris'])->name('qris');
        Route::post('/{tagihan}/upload-bukti',   [TagihanController::class, 'uploadBukti'])->name('upload-bukti');
    });

    // Riwayat pemakaian
    Route::get('/riwayat', [TagihanController::class, 'riwayat'])->name('riwayat');
    // nota riwayat pemakaian
    Route::get('/riwayat/{catatan}/nota', [TagihanController::class, 'notaRiwayat'])->name('riwayat.nota');

    // Pengaduan
    Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
        Route::get('/',             [PengaduanController::class, 'index'])->name('index');
        Route::get('/buat',         [PengaduanController::class, 'create'])->name('create');
        Route::post('/',            [PengaduanController::class, 'store'])->name('store');
        Route::get('/{pengaduan}',  [PengaduanController::class, 'show'])->name('show');
        Route::post('/{pengaduan}/balas', [PengaduanController::class, 'balas'])->name('balas');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',                 [ProfileController::class, 'index'])->name('index');
        Route::post('/password',        [ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/logout',          [ProfileController::class, 'logout'])->name('logout');
    });
});

// Admin → dihandle Filament, tapi fallback redirect kalau akses '/'
Route::get('/', function () {
    if (! auth()->check()) return redirect('/login');

    return redirect(match (auth()->user()->role) {
        'admin'     => '/admin',
        'operator'  => '/operator',
        'teller'    => '/teller',
        'pelanggan' => '/pelanggan',
        default     => '/login',
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/admin/laporan/pdf', [LaporanController::class, 'pdf'])
        ->name('admin.laporan.pdf');
});