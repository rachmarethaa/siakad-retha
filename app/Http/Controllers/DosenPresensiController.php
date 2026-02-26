<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\JadwalMengajar;
use App\Models\KRS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DosenPresensiController extends Controller
{
    /**
     * Display list of classes/jadwal where dosen can input presensi
     */
    public function index()
    {
        $dosen = auth()->guard('dosen')->user();

        // Get semua jadwal mengajar milik dosen
        $jadwals = JadwalMengajar::where('dosen_id', $dosen->id)
            ->with('nilai')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Tambahkan statistik presensi untuk setiap jadwal
        foreach ($jadwals as $jadwal) {
            $totalStudents = KRS::where('jadwal_mengajar_id', $jadwal->id)
                ->where('status', 'aktif')
                ->count();

            $presensiDates = Presensi::where('jadwal_mengajar_id', $jadwal->id)
                ->distinct('tanggal')
                ->count('tanggal');

            $jadwal->total_students = $totalStudents;
            $jadwal->presensi_dates = $presensiDates;
        }

        return view('Dosen.presensi.index', compact('jadwals', 'dosen'));
    }

    /**
     * Show form to input presensi for a specific class
     */
    public function create($jadwalId)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        // Check if dosen owns this jadwal
        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        // Get mahasiswa who took this class (via KRS)
        $krs = KRS::where('jadwal_mengajar_id', $jadwal->id)
            ->where('status', 'aktif')
            ->with('mahasiswa')
            ->get();

        // Get today's date
        $tanggal = Carbon::now()->toDateString();

        // Check if presensi already input for today
        $existingPresensi = Presensi::where('jadwal_mengajar_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('mahasiswa_id');

        return view('Dosen.presensi.create', compact('jadwal', 'dosen', 'krs', 'tanggal', 'existingPresensi'));
    }

    /**
     * Store presensi data
     */
    public function store(Request $request, $jadwalId)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        // Check authorization
        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        // Normalize empty time strings to null so 'nullable|date_format' passes
        $input = $request->all();
        if (isset($input['presensi']) && is_array($input['presensi'])) {
            foreach ($input['presensi'] as $i => $p) {
                // Normalize jam_masuk
                $jm = isset($p['jam_masuk']) ? trim((string) $p['jam_masuk']) : null;
                if ($jm === '' || $jm === null || !preg_match('/^\d{1,2}:\d{2}$/', $jm)) {
                    $input['presensi'][$i]['jam_masuk'] = null;
                } else {
                    $input['presensi'][$i]['jam_masuk'] = $jm;
                }

                // Normalize jam_keluar
                $jk = isset($p['jam_keluar']) ? trim((string) $p['jam_keluar']) : null;
                if ($jk === '' || $jk === null || !preg_match('/^\d{1,2}:\d{2}$/', $jk)) {
                    $input['presensi'][$i]['jam_keluar'] = null;
                } else {
                    $input['presensi'][$i]['jam_keluar'] = $jk;
                }
            }
            $request->replace($input);
            // Log payload for debugging save issues (temporary)
            Log::debug('dosen.presensi.store.payload', [
                'jadwal_id' => $jadwal->id,
                'dosen_id' => $dosen->id,
                'payload' => $request->all()
            ]);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.mahasiswa_id' => 'required|exists:mahasiswa,id',
            'presensi.*.status' => 'required|in:hadir,absen,izin,sakit',
            'presensi.*.jam_masuk' => 'nullable|date_format:H:i',
            'presensi.*.jam_keluar' => 'nullable|date_format:H:i',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        $tanggal = $validated['tanggal'];

        // Instead of bulk-deleting (which only soft-deletes and can trigger unique constraint
        // violations), upsert per KRS: restore/update trashed rows or create new ones.
        foreach ($validated['presensi'] as $p) {
            $krs = KRS::where('mahasiswa_id', $p['mahasiswa_id'])
                ->where('jadwal_mengajar_id', $jadwal->id)
                ->first();

            if (! $krs) {
                continue;
            }

            $data = [
                'krs_id' => $krs->id,
                'mahasiswa_id' => $p['mahasiswa_id'],
                'jadwal_mengajar_id' => $jadwal->id,
                'dosen_id' => $dosen->id,
                'tanggal' => $tanggal,
                'status' => $p['status'],
                'jam_masuk' => $p['jam_masuk'] ?? null,
                'jam_keluar' => $p['jam_keluar'] ?? null,
                'keterangan' => $p['keterangan'] ?? null,
                'sudah_dikonfirmasi' => true,
            ];

            // Find existing (including trashed)
            $existing = Presensi::withTrashed()
                ->where('krs_id', $krs->id)
                ->where('tanggal', $tanggal)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                // Preserve existing time/keterangan if incoming payload has nulls
                $updateData = $data;
                if (! array_key_exists('jam_masuk', $p) || is_null($p['jam_masuk'])) {
                    $updateData['jam_masuk'] = $existing->jam_masuk;
                }
                if (! array_key_exists('jam_keluar', $p) || is_null($p['jam_keluar'])) {
                    $updateData['jam_keluar'] = $existing->jam_keluar;
                }
                if (! array_key_exists('keterangan', $p) || is_null($p['keterangan'])) {
                    $updateData['keterangan'] = $existing->keterangan;
                }
                $existing->update($updateData);
            } else {
                Presensi::create($data);
            }
        }

        return redirect()->route('dosen.presensi.index')
            ->with('success', 'Presensi berhasil disimpan');
    }

    /**
     * Show presensi history for a class
     */
    public function show($jadwalId)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        // Check authorization
        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        // Get all mahasiswa in this class
        $krs = KRS::where('jadwal_mengajar_id', $jadwal->id)
            ->where('status', 'aktif')
            ->with('mahasiswa')
            ->get();

        // Get presensi history grouped by date
        $presensiByDate = Presensi::where('jadwal_mengajar_id', $jadwal->id)
            ->with('mahasiswa')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->groupBy('tanggal');

        // Calculate statistics
        $stats = $this->calculateStatistics($jadwal);

        return view('Dosen.presensi.show', compact('jadwal', 'dosen', 'krs', 'presensiByDate', 'stats'));
    }

    /**
     * Edit presensi for a specific date
     */
    public function edit($jadwalId, $tanggal)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        // Check authorization
        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        // Get mahasiswa in this class
        $krs = KRS::where('jadwal_mengajar_id', $jadwal->id)
            ->where('status', 'aktif')
            ->with('mahasiswa')
            ->get();

        // Get presensi data for this date
        $presensiData = Presensi::where('jadwal_mengajar_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('mahasiswa_id');

        return view('Dosen.presensi.edit', compact('jadwal', 'dosen', 'krs', 'tanggal', 'presensiData'));
    }

    /**
     * Update presensi for a specific date
     */
    public function update(Request $request, $jadwalId, $tanggal)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        // Check authorization
        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        // Normalize empty time strings to null so 'nullable|date_format' passes
        $input = $request->all();
        if (isset($input['presensi']) && is_array($input['presensi'])) {
            foreach ($input['presensi'] as $i => $p) {
                if (array_key_exists('jam_masuk', $p) && $p['jam_masuk'] === '') {
                    $input['presensi'][$i]['jam_masuk'] = null;
                }
                if (array_key_exists('jam_keluar', $p) && $p['jam_keluar'] === '') {
                    $input['presensi'][$i]['jam_keluar'] = null;
                }
            }
            $request->replace($input);
        }

        $validated = $request->validate([
            'presensi' => 'required|array',
            'presensi.*.mahasiswa_id' => 'required|exists:mahasiswa,id',
            'presensi.*.status' => 'required|in:hadir,absen,izin,sakit',
            'presensi.*.jam_masuk' => 'nullable|date_format:H:i',
            'presensi.*.jam_keluar' => 'nullable|date_format:H:i',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        // Instead of bulk soft-deleting, update or restore per KRS so unique constraints
        // aren't violated. This mirrors the store() behavior.
        foreach ($validated['presensi'] as $p) {
            $krs = KRS::where('mahasiswa_id', $p['mahasiswa_id'])
                ->where('jadwal_mengajar_id', $jadwal->id)
                ->first();

            if (! $krs) {
                continue;
            }

            $data = [
                'krs_id' => $krs->id,
                'mahasiswa_id' => $p['mahasiswa_id'],
                'jadwal_mengajar_id' => $jadwal->id,
                'dosen_id' => $dosen->id,
                'tanggal' => $tanggal,
                'status' => $p['status'],
                'jam_masuk' => $p['jam_masuk'] ?? null,
                'jam_keluar' => $p['jam_keluar'] ?? null,
                'keterangan' => $p['keterangan'] ?? null,
                'sudah_dikonfirmasi' => true,
            ];

            $existing = Presensi::withTrashed()
                ->where('krs_id', $krs->id)
                ->where('tanggal', $tanggal)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                // Preserve existing time/keterangan if incoming payload has nulls
                $updateData = $data;
                if (! array_key_exists('jam_masuk', $p) || is_null($p['jam_masuk'])) {
                    $updateData['jam_masuk'] = $existing->jam_masuk;
                }
                if (! array_key_exists('jam_keluar', $p) || is_null($p['jam_keluar'])) {
                    $updateData['jam_keluar'] = $existing->jam_keluar;
                }
                if (! array_key_exists('keterangan', $p) || is_null($p['keterangan'])) {
                    $updateData['keterangan'] = $existing->keterangan;
                }
                $existing->update($updateData);
            } else {
                Presensi::create($data);
            }
        }

        return redirect()->route('dosen.presensi.show', $jadwalId)
            ->with('success', 'Presensi berhasil diperbarui');
    }

    /**
     * Delete presensi entry
     */
    public function destroy($jadwalId, $presensiId)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);
        $presensi = Presensi::findOrFail($presensiId);

        // Check authorization
        if ($jadwal->dosen_id !== $dosen->id || $presensi->jadwal_mengajar_id !== $jadwal->id) {
            abort(403, 'Unauthorized');
        }

        $presensi->delete();

        return redirect()->back()->with('success', 'Presensi berhasil dihapus');
    }

    /**
     * Export presensi report
     */
    public function exportReport($jadwalId)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        // Check authorization
        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        // Get all presensi data
        $presensiData = Presensi::where('jadwal_mengajar_id', $jadwal->id)
            ->with('mahasiswa')
            ->orderBy('tanggal', 'desc')
            ->get();

        // Create CSV
        $filename = 'Presensi_' . str_replace(' ', '_', $jadwal->mata_kuliah) . '_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($jadwal, $presensiData, $dosen) {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, ['Laporan Presensi Kelas', $jadwal->mata_kuliah]);
            fputcsv($file, ['Kode MK', $jadwal->kode_mk]);
            fputcsv($file, ['Program Studi', $jadwal->prodi]);
            fputcsv($file, ['Dosen', $dosen->nama_lengkap ?? $dosen->user->name]);
            fputcsv($file, []);

            // Write column headers
            fputcsv($file, ['NIM', 'Nama Mahasiswa', 'Tanggal', 'Status', 'Jam Masuk', 'Jam Keluar', 'Keterangan']);

            // Write data
            foreach ($presensiData as $p) {
                fputcsv($file, [
                    $p->mahasiswa->nim ?? '-',
                    $p->mahasiswa->nama ?? '-',
                    $p->tanggal->format('d-m-Y'),
                    $p->getStatusLabel(),
                    $p->jam_masuk ?? '-',
                    $p->jam_keluar ?? '-',
                    $p->keterangan ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get attendance statistics for a class
     */
    private function calculateStatistics($jadwal)
    {
        $presensi = Presensi::where('jadwal_mengajar_id', $jadwal->id)->get();

        $stats = [
            'total_records' => $presensi->count(),
            'hadir' => $presensi->where('status', 'hadir')->count(),
            'absen' => $presensi->where('status', 'absen')->count(),
            'izin' => $presensi->where('status', 'izin')->count(),
            'sakit' => $presensi->where('status', 'sakit')->count(),
            'total_dates' => Presensi::where('jadwal_mengajar_id', $jadwal->id)
                ->distinct('tanggal')
                ->count('tanggal'),
        ];

        if ($stats['total_records'] > 0) {
            $stats['persentase_hadir'] = round(($stats['hadir'] / $stats['total_records']) * 100, 2);
            $stats['persentase_absen'] = round(($stats['absen'] / $stats['total_records']) * 100, 2);
        }

        return $stats;
    }

    /**
     * Get attendance summary by mahasiswa
     */
    public function getSummaryByMahasiswa($jadwalId)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        // Get all mahasiswa in this class
        $krs = KRS::where('jadwal_mengajar_id', $jadwal->id)
            ->where('status', 'aktif')
            ->with('mahasiswa')
            ->get();

        $summary = [];
        foreach ($krs as $k) {
            $presensi = Presensi::where('mahasiswa_id', $k->mahasiswa_id)
                ->where('jadwal_mengajar_id', $jadwal->id)
                ->get();

            $total = $presensi->count();
            $hadir = $presensi->where('status', 'hadir')->count();
            $persentase = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;

            $summary[] = [
                'mahasiswa' => $k->mahasiswa,
                'total' => $total,
                'hadir' => $hadir,
                'absen' => $presensi->where('status', 'absen')->count(),
                'izin' => $presensi->where('status', 'izin')->count(),
                'sakit' => $presensi->where('status', 'sakit')->count(),
                'persentase' => $persentase,
            ];
        }

        return view('Dosen.presensi.summary', compact('jadwal', 'dosen', 'summary'));
    }
}
