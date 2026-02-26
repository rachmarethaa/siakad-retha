<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenProfileController;
use App\Http\Controllers\MahasiswaProfileController;
use App\Http\Controllers\StaffPresensiController;
use App\Http\Controllers\Auth\MultiRoleLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('index');
})->name('index');

// Public: lihat jadwal mengajar
Route::get('/jadwal', [\App\Http\Controllers\JadwalMengajarController::class, 'publicIndex'])->name('jadwal.index');

// Debug route to inspect session and auth state (only when APP_DEBUG=true)
Route::get('/debug-session', function (Request $request) {
    if (!config('app.debug')) {
        abort(404);
    }

    return response()->json([
        'session_id' => $request->session()->getId(),
        'session_driver' => config('session.driver'),
        'csrf_token' => csrf_token(),
        'cookies' => [],
        'auth' => [
            'web' => Auth::guard('web')->check(),
            'staff' => Auth::guard('staff')->check(),
            'dosen' => Auth::guard('dosen')->check(),
            'mahasiswa' => Auth::guard('mahasiswa')->check(),
        ],
    ]);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| MULTI-ROLE LOGIN & LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/login/authenticate', [MultiRoleLoginController::class, 'authenticate'])
    ->name('login.authenticate');

// TIKET MAHASISWA
Route::middleware('auth:mahasiswa')->group(function () {
    Route::get('/tiket', [\App\Http\Controllers\Tiket\MahasiswaTiketController::class, 'index'])->name('tiket.mahasiswa.index');
    Route::get('/tiket/create', [\App\Http\Controllers\Tiket\MahasiswaTiketController::class, 'create'])->name('tiket.mahasiswa.create');
    Route::post('/tiket', [\App\Http\Controllers\Tiket\MahasiswaTiketController::class, 'store'])->name('tiket.mahasiswa.store');
    Route::get('/tiket/{id}', [\App\Http\Controllers\Tiket\MahasiswaTiketController::class, 'show'])->name('tiket.mahasiswa.show');


    Route::get('/tiket/{id}', [\App\Http\Controllers\Tiket\MahasiswaTiketController::class, 'show'])->name('tiket.mahasiswa.show');
});

// STAFF tiket management
Route::middleware('auth:staff')->prefix('staff')->group(function () {
    Route::get('/tiket', [\App\Http\Controllers\Tiket\StaffTiketController::class, 'index'])->name('staff.tiket.index');
    Route::get('/tiket/create', [\App\Http\Controllers\Tiket\StaffTiketController::class, 'create'])->name('staff.tiket.create');
    Route::post('/tiket', [\App\Http\Controllers\Tiket\StaffTiketController::class, 'store'])->name('staff.tiket.store');
    Route::get('/tiket/{id}', [\App\Http\Controllers\Tiket\StaffTiketController::class, 'show'])->name('staff.tiket.show');
    Route::delete('/tiket/{id}', [\App\Http\Controllers\Tiket\StaffTiketController::class, 'destroy'])->name('staff.tiket.destroy');
    Route::post('/tiket/{id}/forward', [\App\Http\Controllers\Tiket\StaffTiketController::class, 'forwardToDosen'])->name('staff.tiket.forward');
    Route::post('/tiket/{id}/assign', [\App\Http\Controllers\Tiket\StaffTiketController::class, 'assign'])->name('staff.tiket.assign');
    Route::post('/tiket/{id}/respond', [\App\Http\Controllers\Tiket\StaffTiketController::class, 'respond'])->name('staff.tiket.respond');
    Route::post('/tiket/{id}/send-to-mahasiswa', [\App\Http\Controllers\Tiket\StaffTiketController::class, 'sendToMahasiswa'])->name('staff.tiket.send_to_mahasiswa');
});

// Dosen read-only view
Route::middleware('auth:dosen')->group(function () {
    Route::get('/dosen/tiket', [\App\Http\Controllers\Tiket\DosenTiketController::class, 'index'])->name('dosen.tiket.index');
    Route::get('/dosen/tiket/{id}', [\App\Http\Controllers\Tiket\DosenTiketController::class, 'show'])->name('dosen.tiket.show');
    Route::post('/dosen/tiket/{id}/respond', [\App\Http\Controllers\Tiket\DosenTiketController::class, 'respond'])->name('dosen.tiket.respond');
});

Route::post('/logout/all', [MultiRoleLoginController::class, 'logout'])
    ->name('logout.all');

/*
|--------------------------------------------------------------------------
| STAFF ROUTES (Authenticated with staff guard)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:staff')->group(function () {
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.dashboard');
    Route::post('/staff/logout', [StaffController::class, 'logout'])->name('staff.logout');
    
    // Staff: Manage Mahasiswa
    Route::get('/staff/mahasiswa', [\App\Http\Controllers\Staff\MahasiswaManagementController::class, 'index'])->name('staff.mahasiswa.index');
    Route::get('/staff/mahasiswa/create', [\App\Http\Controllers\Staff\MahasiswaManagementController::class, 'create'])->name('staff.mahasiswa.create');
    Route::post('/staff/mahasiswa', [\App\Http\Controllers\Staff\MahasiswaManagementController::class, 'store'])->name('staff.mahasiswa.store');
    Route::get('/staff/mahasiswa/{id}/edit', [\App\Http\Controllers\Staff\MahasiswaManagementController::class, 'edit'])->name('staff.mahasiswa.edit');
    Route::put('/staff/mahasiswa/{id}', [\App\Http\Controllers\Staff\MahasiswaManagementController::class, 'update'])->name('staff.mahasiswa.update');
    Route::delete('/staff/mahasiswa/{id}', [\App\Http\Controllers\Staff\MahasiswaManagementController::class, 'destroy'])->name('staff.mahasiswa.destroy');
    Route::post('/staff/mahasiswa/{id}/reset-password', [\App\Http\Controllers\Staff\MahasiswaManagementController::class, 'resetPassword'])->name('staff.mahasiswa.resetPassword');

    // Staff: Manage Dosen
    Route::get('/staff/dosen', [\App\Http\Controllers\Staff\DosenManagementController::class, 'index'])->name('staff.dosen.index');
    Route::get('/staff/dosen/create', [\App\Http\Controllers\Staff\DosenManagementController::class, 'create'])->name('staff.dosen.create');
    Route::post('/staff/dosen', [\App\Http\Controllers\Staff\DosenManagementController::class, 'store'])->name('staff.dosen.store');
    Route::get('/staff/dosen/{id}/edit', [\App\Http\Controllers\Staff\DosenManagementController::class, 'edit'])->name('staff.dosen.edit');
    Route::put('/staff/dosen/{id}', [\App\Http\Controllers\Staff\DosenManagementController::class, 'update'])->name('staff.dosen.update');
    Route::delete('/staff/dosen/{id}', [\App\Http\Controllers\Staff\DosenManagementController::class, 'destroy'])->name('staff.dosen.destroy');
    Route::post('/staff/dosen/{id}/reset-password', [\App\Http\Controllers\Staff\DosenManagementController::class, 'resetPassword'])->name('staff.dosen.resetPassword');
    // Staff: Manage Jadwal Mengajar
    Route::get('/staff/jadwal', [\App\Http\Controllers\JadwalMengajarController::class, 'index'])->name('staff.jadwal.index');
    Route::get('/staff/jadwal/create', [\App\Http\Controllers\JadwalMengajarController::class, 'create'])->name('staff.jadwal.create');
    Route::post('/staff/jadwal', [\App\Http\Controllers\JadwalMengajarController::class, 'store'])->name('staff.jadwal.store');
    Route::get('/staff/jadwal/{id}/edit', [\App\Http\Controllers\JadwalMengajarController::class, 'edit'])->name('staff.jadwal.edit');
    Route::put('/staff/jadwal/{id}', [\App\Http\Controllers\JadwalMengajarController::class, 'update'])->name('staff.jadwal.update');
    Route::delete('/staff/jadwal/{id}', [\App\Http\Controllers\JadwalMengajarController::class, 'destroy'])->name('staff.jadwal.destroy');
    
    // Staff: Input Presensi (oleh staff untuk kelas apa pun)
    Route::get('/staff/presensi', [StaffPresensiController::class, 'index'])->name('staff.presensi.index');
    Route::get('/staff/presensi/{jadwal_id}/create', [StaffPresensiController::class, 'create'])->name('staff.presensi.create');
    Route::post('/staff/presensi/{jadwal_id}', [StaffPresensiController::class, 'store'])->name('staff.presensi.store');
    
    // Staff: Laporan Presensi
    Route::get('/staff/laporan', [\App\Http\Controllers\PresensiReportController::class, 'staffDashboard'])->name('staff.laporan.dashboard');
    Route::get('/staff/laporan/dosen/{dosen_id}', [\App\Http\Controllers\PresensiReportController::class, 'staffDosenReport'])->name('staff.laporan.dosen');
    Route::get('/staff/laporan/mahasiswa', [\App\Http\Controllers\PresensiReportController::class, 'staffStudentReport'])->name('staff.laporan.mahasiswa');
    Route::get('/staff/laporan/export', [\App\Http\Controllers\PresensiReportController::class, 'staffExportExcel'])->name('staff.laporan.export');
});

/*
|--------------------------------------------------------------------------
| DOSEN ROUTES (Authenticated with dosen guard)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:dosen')->group(function () {
    Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.dashboard');
    
    // Profile Routes
    Route::get('/dosen/profile/edit', [DosenProfileController::class, 'edit'])->name('dosen.profile.edit');
    Route::put('/dosen/profile/update', [DosenProfileController::class, 'update'])->name('dosen.profile.update');
    
    // Dosen: lihat jadwal mengajar
    Route::get('/dosen/jadwal', [\App\Http\Controllers\JadwalMengajarController::class, 'dosenIndex'])->name('dosen.jadwal.index');
    Route::get('/dosen/jadwal/{id}', [\App\Http\Controllers\JadwalMengajarController::class, 'show'])->name('dosen.jadwal.show');
    
    // Dosen: Input Nilai
    Route::get('/dosen/nilai', [\App\Http\Controllers\NilaiController::class, 'index'])->name('dosen.nilai.index');
    Route::get('/dosen/nilai/{jadwal_id}/create', [\App\Http\Controllers\NilaiController::class, 'create'])->name('dosen.nilai.create');
    Route::post('/dosen/nilai/{jadwal_id}', [\App\Http\Controllers\NilaiController::class, 'store'])->name('dosen.nilai.store');
    Route::get('/dosen/nilai/{jadwal_id}', [\App\Http\Controllers\NilaiController::class, 'show'])->name('dosen.nilai.show');
    Route::get('/dosen/nilai/{jadwal_id}/{mahasiswa_id}/edit', [\App\Http\Controllers\NilaiController::class, 'edit'])->name('dosen.nilai.edit');
    Route::put('/dosen/nilai/{jadwal_id}/{mahasiswa_id}', [\App\Http\Controllers\NilaiController::class, 'update'])->name('dosen.nilai.update');
    
    // Dosen: Input Presensi
    Route::get('/dosen/presensi', [\App\Http\Controllers\DosenPresensiController::class, 'index'])->name('dosen.presensi.index');
    Route::get('/dosen/presensi/{jadwal_id}/create', [\App\Http\Controllers\DosenPresensiController::class, 'create'])->name('dosen.presensi.create');
    Route::post('/dosen/presensi/{jadwal_id}', [\App\Http\Controllers\DosenPresensiController::class, 'store'])->name('dosen.presensi.store');
    Route::get('/dosen/presensi/{jadwal_id}', [\App\Http\Controllers\DosenPresensiController::class, 'show'])->name('dosen.presensi.show');
    Route::get('/dosen/presensi/{jadwal_id}/{tanggal}/edit', [\App\Http\Controllers\DosenPresensiController::class, 'edit'])->name('dosen.presensi.edit');
    Route::put('/dosen/presensi/{jadwal_id}/{tanggal}', [\App\Http\Controllers\DosenPresensiController::class, 'update'])->name('dosen.presensi.update');
    Route::delete('/dosen/presensi/{jadwal_id}/{presensi_id}', [\App\Http\Controllers\DosenPresensiController::class, 'destroy'])->name('dosen.presensi.destroy');
    Route::get('/dosen/presensi/{jadwal_id}/summary', [\App\Http\Controllers\DosenPresensiController::class, 'getSummaryByMahasiswa'])->name('dosen.presensi.summary');
    Route::get('/dosen/presensi/{jadwal_id}/export', [\App\Http\Controllers\DosenPresensiController::class, 'exportReport'])->name('dosen.presensi.export');
    
    // Dosen: Laporan Presensi
    Route::get('/dosen/laporan', [\App\Http\Controllers\PresensiReportController::class, 'dosenDashboard'])->name('dosen.laporan.dashboard');
    Route::get('/dosen/laporan/kelas/{jadwal_id}', [\App\Http\Controllers\PresensiReportController::class, 'dosenClassReport'])->name('dosen.laporan.kelas');
    Route::get('/dosen/laporan/kelas/{jadwal_id}/excel', [\App\Http\Controllers\PresensiReportController::class, 'dosenExportExcel'])->name('dosen.laporan.excel');
    Route::get('/dosen/laporan/kelas/{jadwal_id}/pdf', [\App\Http\Controllers\PresensiReportController::class, 'dosenExportPdf'])->name('dosen.laporan.pdf');
});

/*
|--------------------------------------------------------------------------
| MAHASISWA ROUTES (Authenticated with mahasiswa guard)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:mahasiswa')->group(function () {
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.dashboard');
    
    // Profile Routes
    Route::get('/mahasiswa/profile/edit', [MahasiswaProfileController::class, 'edit'])->name('mahasiswa.profile.edit');
    Route::put('/mahasiswa/profile/update', [MahasiswaProfileController::class, 'update'])->name('mahasiswa.profile.update');
    
    // Mahasiswa: lihat jadwal kuliah
    Route::get('/mahasiswa/jadwal', [\App\Http\Controllers\JadwalMengajarController::class, 'mahasiswaIndex'])->name('mahasiswa.jadwal.index');
    
    // Mahasiswa: lihat nilai dan transkip
    Route::get('/mahasiswa/nilai', [\App\Http\Controllers\NilaiMahasiswaController::class, 'index'])->name('mahasiswa.nilai.index');
    Route::get('/mahasiswa/transkip', [\App\Http\Controllers\NilaiMahasiswaController::class, 'transkip'])->name('mahasiswa.transkip');
    
    // Mahasiswa: Lihat Presensi
    Route::get('/mahasiswa/presensi', [\App\Http\Controllers\MahasiswaPresensiController::class, 'index'])->name('mahasiswa.presensi.index');
    Route::get('/mahasiswa/presensi/{jadwal_id}', [\App\Http\Controllers\MahasiswaPresensiController::class, 'show'])->name('mahasiswa.presensi.show');
    Route::get('/mahasiswa/presensi/stats/summary', [\App\Http\Controllers\MahasiswaPresensiController::class, 'getStats'])->name('mahasiswa.presensi.stats');
    Route::get('/mahasiswa/presensi/today/list', [\App\Http\Controllers\MahasiswaPresensiController::class, 'getTodayPresensi'])->name('mahasiswa.presensi.today');
    Route::get('/mahasiswa/presensi/export/report', [\App\Http\Controllers\MahasiswaPresensiController::class, 'exportReport'])->name('mahasiswa.presensi.export');
    Route::post('/mahasiswa/presensi/check-in', [\App\Http\Controllers\MahasiswaPresensiController::class, 'checkIn'])->name('mahasiswa.presensi.checkin');
    Route::post('/mahasiswa/presensi/{presensi_id}/check-out', [\App\Http\Controllers\MahasiswaPresensiController::class, 'checkOut'])->name('mahasiswa.presensi.checkout');
    
    // Mahasiswa: KRS (Kartu Rencana Studi)
    Route::get('/mahasiswa/krs', [\App\Http\Controllers\KRSController::class, 'index'])->name('mahasiswa.krs.index');
    Route::get('/mahasiswa/krs/create', [\App\Http\Controllers\KRSController::class, 'create'])->name('mahasiswa.krs.create');
    Route::post('/mahasiswa/krs', [\App\Http\Controllers\KRSController::class, 'store'])->name('mahasiswa.krs.store');
    Route::get('/mahasiswa/krs/{id}', [\App\Http\Controllers\KRSController::class, 'show'])->name('mahasiswa.krs.show');
    Route::delete('/mahasiswa/krs/{id}', [\App\Http\Controllers\KRSController::class, 'destroy'])->name('mahasiswa.krs.destroy');
});

/*
|--------------------------------------------------------------------------
| MIDDLEWARE FOR FUTURE ROUTES
|--------------------------------------------------------------------------
*/
// Uncomment dan tambahkan route berikutnya di sini jika diperlukan

