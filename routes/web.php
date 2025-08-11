<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\PilihJadwalController;
use App\Http\Controllers\MasukkanJadwalController;
use App\Http\Controllers\PesanController;
use App\Http\Controllers\ProfilController;
use App\Models\JadwalBimbingan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

// Route untuk guest (belum login)
Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::get('/datausulanbimbingan', function () {
    return view('bimbingan.admin.datausulanbimbingan');
});

Route::middleware(['auth:mahasiswa,dosen,admin'])->group(function () {
    Route::prefix('pesan')->group(function () {

        Route::get('/dashboardkonsultasi', function () {
            if (auth()->guard('mahasiswa')->check()) {
                return app(PesanController::class)->indexMahasiswa();
            } else {
                return app(PesanController::class)->indexDosen();
            }
        })->name('pesan.dashboardkonsultasi');

        Route::get('/getMahasiswaByAngkatan', [PesanController::class, 'getMahasiswaByAngkatan'])->name('pesan.getMahasiswaByAngkatan');
        Route::get('/create', [PesanController::class, 'create'])->name('pesan.create');
        Route::post('/store', [PesanController::class, 'store'])->name('pesan.store');

        // Route lainnya untuk pesan
        Route::get('/{id}', [PesanController::class, 'show'])->name('pesan.show');
        Route::patch('/{id}/status', [PesanController::class, 'updateStatus'])->name('pesan.updateStatus');
        Route::post('/request-notification', [PesanController::class, 'requestNotification'])->name('pesan.requestNotification');
        Route::get('/filterAktif', [PesanController::class, 'filterAktif'])->name('pesan.filterAktif');
        Route::get('/filterSelesai', [PesanController::class, 'filterSelesai'])->name('pesan.filterSelesai');
        Route::get('/getDosen', [PesanController::class, 'getDosen'])->name('pesan.getDosen');
        Route::post('/reply/{id}', [PesanController::class, 'storeReply'])->name('pesan.reply');
        Route::post('/end/{id}', [PesanController::class, 'endChat'])->name('pesan.end');
        Route::get('/attachment/{id}', [PesanController::class, 'downloadAttachment'])->name('pesan.attachment');
    });

    Route::controller(ProfilController::class)->group(function () {
        Route::get('/profil', 'show')->name('profile.show');
        Route::put('/profil/update', 'update')->name('profile.update');
        Route::delete('/profil/remove', 'remove')->name('profile.remove');
    });
});

// Route untuk mahasiswa
Route::middleware(['auth:mahasiswa', 'checkRole:mahasiswa'])->group(function () {
    // Route view biasa

    Route::controller(MahasiswaController::class)->group(function () {
        Route::get('/usulanbimbingan', 'index')->name('mahasiswa.usulanbimbingan');
        Route::post('/usulanbimbingan/selesai/{id}', 'selesaiBimbingan')->name('mahasiswa.selesaibimbingan');
        Route::get('/aksiInformasi/{id}', 'getDetailBimbingan')->name('mahasiswa.aksiInformasi');
        Route::get('/detaildaftar/{nip}', 'getDetailDaftar')->name('mahasiswa.detaildaftar');
    });

    // Bimbingan routes
    Route::controller(PilihJadwalController::class)->prefix('pilihjadwal')->group(function () {
        Route::get('/', 'index')->name('pilihjadwal.index');
        Route::post('/store', 'store')->name('pilihjadwal.store');
        Route::get('/available', 'getAvailableJadwal')->name('pilihjadwal.available');
        Route::get('/check', 'checkAvailability')->name('pilihjadwal.check');
        Route::post('/create-event/{usulanId}', 'createGoogleCalendarEvent')->name('pilihjadwal.create-event');

        Route::get('/dosen/{nip}/jenis-bimbingan', 'getJenisBimbingan')->name('pilihjadwal.getJenisBimbingan');
        Route::post('/cancel/{id}', 'cancelBooking')->name('pilihjadwal.cancel');
    });

    Route::controller(GoogleCalendarController::class)->prefix('mahasiswa')->group(function () {
        Route::get('/google/connect', 'connect')->name('mahasiswa.google.connect');
        Route::get('/google/callback', 'callback')->name('mahasiswa.google.callback');
    });

    Route::get('/alumni', [MahasiswaController::class, 'get_alumni_view'])->name('mahasiswa.alumni');
    Route::post('/alumni-step1', [MahasiswaController::class, 'biodata_step_store'])->name('mahasiswa.alumni-step1');
    Route::post('/alumni-step2', [MahasiswaController::class, 'kuisioner_wajib_step_store'])->name('mahasiswa.alumni-step2');
    Route::post('/alumni-step3', [MahasiswaController::class, 'kuisioner_lainnya_step_store'])->name('mahasiswa.alumni-step3');
    Route::post('/update-profil', [MahasiswaController::class, 'input_profil'])->name('mahasiswa.update-profil');
    Route::post('/generate-cv', [MahasiswaController::class, 'generateCV'])->name('alumni.generate-cv');
    Route::get('/download-cv', [MahasiswaController::class, 'downloadCV'])->name('alumni.download-cv');
});

// Route untuk dosen
Route::middleware(['auth:dosen', 'checkRole:dosen'])->group(function () {
    // Route view biasa

    Route::controller(DosenController::class)->group(function () {
        Route::get('/persetujuan', 'index')->name('dosen.persetujuan');
        Route::get('/terimausulanbimbingan/{id}', 'getDetailBimbingan')->name('dosen.detailbimbingan');
        Route::post('/terimausulanbimbingan/terima/{id}', 'terima')->name('dosen.detailbimbingan.terima');
        Route::post('/terimausulanbimbingan/tolak/{id}', 'tolak')->name('dosen.detailbimbingan.tolak');
        Route::post('/persetujuan/terima/{id}', 'terima')->name('dosen.persetujuan.terima');
        Route::post('/persetujuan/tolak/{id}', 'tolak')->name('dosen.persetujuan.tolak');
        Route::post('/persetujuan/selesai/{id}', [DosenController::class, 'selesaikan'])->name('dosen.persetujuan.selesai');
        Route::get('/dosen/detail/{nip}', [DosenController::class, 'dosenDetail'])->name('dosen.detail');
        Route::get('/dosen/riwayat-detail/{nip}', [DosenController::class, 'riwayatDosenDetail'])->name('dosen.riwayat.detail');
        Route::get('/persetujuan/related-schedules/{id}', [DosenController::class, 'getRelatedSchedules'])
            ->name('persetujuan.related-schedules');
        Route::post('/persetujuan/batal/{id}', [DosenController::class, 'batalkanPersetujuan'])
            ->name('persetujuan.batal')
            ->middleware(['auth']);
    });

    // Jadwal routes
    Route::controller(MasukkanJadwalController::class)->prefix('masukkanjadwal')->group(function () {
        Route::get('/', 'index')->name('dosen.jadwal.index');
        Route::post('/store', 'store')->name('dosen.jadwal.store');
        Route::delete('/{eventId}', 'destroy')->name('dosen.jadwal.destroy');
        Route::post('/debug-store', [MasukkanJadwalController::class, 'debugStore']);
    });

    Route::controller(GoogleCalendarController::class)->prefix('dosen')->group(function () {
        Route::get('/google/connect', 'connect')->name('dosen.google.connect');
        Route::get('/google/events', 'getEvents')->name('dosen.google.events');
        Route::get('/google/callback', 'callback')->name('dosen.google.callback');
        Route::get('/dosen/google/events', [MasukkanJadwalController::class, 'getEvents'])
            ->name('dosen.google.events')
            ->middleware(['auth:dosen']);
    });

    Route::get('/dosen/alumni', [DosenController::class, 'get_dosen_alumni_view'])->name('dosen.alumni');
    Route::post('/dosen/alumni/download', [DosenController::class, 'downloadData'])->name('dosen.alumni.download');
});

Route::middleware(['auth:admin', 'checkRole:admin'])->group(function () {
    Route::get('/admin/alumni', [AdminController::class, 'get_admin_alumni_view'])->name('admin.alumni');
    Route::post('/admin/alumni/download', [AdminController::class, 'downloadData'])->name('admin.alumni.download');
});

// Route debugging
Route::get('/debug-jadwal', function () {
    $jadwals = DB::table('jadwal_bimbingans')
        ->whereNotNull('jenis_bimbingan')
        ->get();
    return $jadwals;
});

Route::get('/debug-jadwal-jenis', function () {
    $jadwals = DB::table('jadwal_bimbingans')
        ->select('id', 'event_id', 'nip', 'jenis_bimbingan', 'waktu_mulai')
        ->get();

    return $jadwals;
});

Route::get('/debug-jadwal-detail', function () {
    return DB::table('jadwal_bimbingans')
        ->select('id', 'nip', 'jenis_bimbingan', 'has_kuota_limit')
        ->get();
});

Route::get('/debug-struktur-tabel', function () {
    $columns = DB::getSchemaBuilder()->getColumnListing('jadwal_bimbingans');
    return [
        'columns' => $columns,
        'has_jenis_bimbingan' => in_array('jenis_bimbingan', $columns),
        'has_has_kuota_limit' => in_array('has_kuota_limit', $columns)
    ];
});
Route::get('/debug-jenis-bimbingan', function () {
    $dosenList = DB::table('dosens')
        ->select('nip', 'nama')
        ->get();

    $jenisBimbinganPerDosen = [];
    foreach ($dosenList as $dosen) {
        $jadwalDenganJenis = DB::table('jadwal_bimbingans')
            ->where('nip', $dosen->nip)
            ->whereNotNull('jenis_bimbingan')
            ->where('jenis_bimbingan', '!=', '')
            ->distinct()
            ->pluck('jenis_bimbingan')
            ->toArray();

        $jenisBimbinganPerDosen[$dosen->nip] = $jadwalDenganJenis;
    }

    return $jenisBimbinganPerDosen;
});

// Tambahkan ini di route yang aman (misal di route debug atau buat yang baru)
Route::get('/fix-jadwal-jenis-bimbingan/{nip}/{jenis}', function ($nip, $jenis) {
    $updatedCount = DB::table('jadwal_bimbingans')
        ->where('nip', $nip)
        ->whereNull('jenis_bimbingan')
        ->update(['jenis_bimbingan' => $jenis]);

    return "Updated {$updatedCount} jadwal for NIP {$nip} with jenis_bimbingan '{$jenis}'";
});

Route::get('/fix-jadwal/{id}/{jenis}', function ($id, $jenis) {
    $jadwal = JadwalBimbingan::find($id);
    if ($jadwal) {
        $jadwal->jenis_bimbingan = $jenis;
        $jadwal->save();
        return "Jadwal ID: $id diupdate ke jenis bimbingan: $jenis";
    }
    return "Jadwal tidak ditemukan";
});

Route::get('/fix-jenis-bimbingan', function () {
    // Update jadwal ke jenis bimbingan "skripsi" untuk dosen tertentu
    $updated = DB::table('jadwal_bimbingans')
        ->where('nip', '198501012015041025')
        ->where(function ($query) {
            $query->whereNull('jenis_bimbingan')
                ->orWhere('jenis_bimbingan', '');
        })
        ->update(['jenis_bimbingan' => 'skripsi']);

    // Tampilkan jadwal setelah update
    $jadwals = DB::table('jadwal_bimbingans')
        ->where('nip', '198501012015041025')
        ->select('id', 'nip', 'jenis_bimbingan', 'waktu_mulai')
        ->get();

    return [
        'updated_count' => $updated,
        'jadwals' => $jadwals
    ];
});

Route::get('/debug-jadwal-dosen/{nip}', function ($nip) {
    $jadwals = DB::table('jadwal_bimbingans')
        ->where('nip', $nip)
        ->select('id', 'event_id', 'jenis_bimbingan', 'waktu_mulai', 'waktu_selesai')
        ->orderBy('id', 'desc')
        ->get();

    return [
        'count' => $jadwals->count(),
        'data' => $jadwals
    ];
});

Route::get('/debug-schema', function () {
    $columns = DB::getSchemaBuilder()->getColumnListing('jadwal_bimbingans');
    $columnTypes = [];

    foreach ($columns as $column) {
        $columnTypes[$column] = DB::connection()->getDoctrineColumn('jadwal_bimbingans', $column)->getType()->getName();
    }

    return $columnTypes;
});

Route::get('/update-jadwal/{id}/{jenis}', function ($id, $jenis) {
    $jadwal = JadwalBimbingan::find($id);
    if ($jadwal) {
        $jadwal->jenis_bimbingan = $jenis;
        $jadwal->save();
        return "Jadwal ID: $id diupdate ke jenis bimbingan: $jenis";
    }
    return "Jadwal tidak ditemukan";
});

Route::get('/update-dosen-jadwal/{nip}/{jenis}', function ($nip, $jenis) {
    // Validasi jenis bimbingan
    $validJenis = ['skripsi', 'kp', 'akademik', 'konsultasi', 'mbkm', 'lainnya'];
    if (!in_array($jenis, $validJenis)) {
        return "Jenis bimbingan tidak valid. Pilih dari: " . implode(', ', $validJenis);
    }

    // Update semua jadwal null untuk dosen ini
    $updated = DB::table('jadwal_bimbingans')
        ->where('nip', $nip)
        ->whereNull('jenis_bimbingan')
        ->update(['jenis_bimbingan' => $jenis]);

    return "Updated $updated jadwal untuk dosen $nip ke '$jenis'";
});

Route::get('/run-update-jadwal', function () {
    Artisan::call('jadwal:update-status');
    return "Status jadwal berhasil diperbarui";
});

// Di routes/web.php
Route::get('/jadwal/{id}/status', function ($id) {
    // PERBAIKAN: Pastikan id yang diterima adalah numerik
    if (!is_numeric($id)) {
        // Coba cari jadwal berdasarkan event_id jika id bukan numerik
        $jadwal = \App\Models\JadwalBimbingan::where('event_id', $id)->first();

        if (!$jadwal) {
            return response()->json(['error' => 'Jadwal tidak ditemukan'], 404);
        }

        // Gunakan ID numerik untuk update selanjutnya
        $id = $jadwal->id;
    } else {
        $jadwal = \App\Models\JadwalBimbingan::find($id);

        if (!$jadwal) {
            return response()->json(['error' => 'Jadwal tidak ditemukan'], 404);
        }
    }

    // Hitung jumlah pendaftar aktual dari database
    $pendaftarCount = DB::table('usulan_bimbingans')
        ->where('event_id', $jadwal->event_id)
        ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
        ->count();

    // PERBAIKAN: Jika status penuh, pastikan jumlah pendaftar = kapasitas
    if ($jadwal->status === 'penuh') {
        $pendaftarCount = $jadwal->kapasitas;
    }

    // Update jumlah pendaftar di database menggunakan ID numerik
    DB::table('jadwal_bimbingans')
        ->where('id', $id)
        ->update(['jumlah_pendaftar' => $pendaftarCount]);

    // Tentukan status berdasarkan kondisi
    $status = $jadwal->status;
    if ($jadwal->has_kuota_limit && $pendaftarCount >= $jadwal->kapasitas) {
        $status = 'penuh';
        DB::table('jadwal_bimbingans')
            ->where('id', $id)
            ->update(['status' => $status]);
    } else if (\Carbon\Carbon::parse($jadwal->waktu_selesai)->isPast()) {
        $status = 'selesai';
        DB::table('jadwal_bimbingans')
            ->where('id', $id)
            ->update(['status' => $status]);
    } else if ($status !== 'dibatalkan') {
        $status = 'tersedia';
        DB::table('jadwal_bimbingans')
            ->where('id', $id)
            ->update(['status' => $status]);
    }

    // Status label yang lebih mudah dibaca
    $statusLabel = match ($status) {
        'tersedia' => 'Tersedia',
        'penuh' => 'Penuh',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        default => 'Unknown'
    };

    return response()->json([
        'status' => $status,
        'status_label' => $statusLabel,
        'jumlah_pendaftar' => $pendaftarCount,
        'kapasitas' => $jadwal->kapasitas
    ]);
});

Route::get('/debug-jadwal-status/{id}', function ($id) {
    $jadwal = \App\Models\JadwalBimbingan::find($id);

    if (!$jadwal) {
        return "Jadwal tidak ditemukan";
    }

    $pendaftarCount = DB::table('usulan_bimbingans')
        ->where('event_id', $jadwal->event_id)
        ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
        ->count();

    return [
        'id' => $jadwal->id,
        'event_id' => $jadwal->event_id,
        'status' => $jadwal->status,
        'status_label' => $jadwal->status_label,
        'jumlah_pendaftar_db' => $jadwal->jumlah_pendaftar,
        'jumlah_pendaftar_hitung' => $pendaftarCount,
        'kapasitas' => $jadwal->kapasitas,
        'has_kuota_limit' => $jadwal->has_kuota_limit,
        'waktu_mulai' => $jadwal->waktu_mulai,
        'waktu_selesai' => $jadwal->waktu_selesai,
        'sudah_lewat' => \Carbon\Carbon::parse($jadwal->waktu_selesai)->isPast()
    ];
});

Route::get('/debug-event-id/{id}', function ($id) {
    $jadwal = \App\Models\JadwalBimbingan::where('event_id', $id)->first();
    if ($jadwal) {
        return [
            'found_by_event_id' => true,
            'id' => $jadwal->id,
            'event_id' => $jadwal->event_id
        ];
    }

    $jadwal = \App\Models\JadwalBimbingan::find($id);
    if ($jadwal) {
        return [
            'found_by_id' => true,
            'id' => $jadwal->id,
            'event_id' => $jadwal->event_id
        ];
    }

    return ['error' => 'Not found with either method'];
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('storage/cv/{filename}', function ($filename) {
    $path = storage_path('app/public/cv/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->name('cv.show');
