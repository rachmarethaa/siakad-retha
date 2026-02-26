<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\KRS;
use App\Models\JadwalMengajar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MahasiswaPresensiController extends Controller
{
    /**
     * Display presensi summary for all classes
     */
    public function index()
    {
        $mahasiswa = auth()->guard('mahasiswa')->user();

        // Get all KRS aktif with jadwal info
        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'aktif')
            ->with('jadwalMengajar.dosen')
            ->get();

        // Calculate attendance stats for each class
        $presensiStats = [];
        foreach ($krs as $k) {
            $presensi = Presensi::where('mahasiswa_id', $mahasiswa->id)
                ->where('jadwal_mengajar_id', $k->jadwal_mengajar_id)
                ->get();

            $total = $presensi->count();
            $hadir = $presensi->where('status', 'hadir')->count();
            $absen = $presensi->where('status', 'absen')->count();
            $izin = $presensi->where('status', 'izin')->count();
            $sakit = $presensi->where('status', 'sakit')->count();

            $persentase = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;

            // Check if attendance is below threshold (80%)
            $statusWaspada = $persentase < 80 && $total > 0;

            $presensiStats[$k->jadwal_mengajar_id] = [
                'total' => $total,
                'hadir' => $hadir,
                'absen' => $absen,
                'izin' => $izin,
                'sakit' => $sakit,
                'persentase' => $persentase,
                'status_waspada' => $statusWaspada,
            ];
        }

        return view('Mahasiswa.presensi.index', compact('krs', 'mahasiswa', 'presensiStats'));
    }

    /**
     * Display detail presensi for a specific class
     */
    public function show($jadwalId)
    {
        $mahasiswa = auth()->guard('mahasiswa')->user();

        // Check if mahasiswa took this class
        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->with('jadwalMengajar.dosen')
            ->firstOrFail();

        // Get all presensi records for this mahasiswa in this class
        $presensi = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        // Calculate stats
        $total = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->count();

        $hadir = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->where('status', 'hadir')
            ->count();

        $absen = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->where('status', 'absen')
            ->count();

        $izin = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->where('status', 'izin')
            ->count();

        $sakit = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->where('status', 'sakit')
            ->count();

        $persentase = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;

        // Count terlambat
        $terlambat = 0;
        foreach ($presensi as $p) {
            if ($p->isTerlambat()) {
                $terlambat++;
            }
        }

        return view('Mahasiswa.presensi.show', compact(
            'krs', 
            'mahasiswa', 
            'presensi', 
            'total', 
            'hadir', 
            'absen', 
            'izin', 
            'sakit', 
            'persentase',
            'terlambat'
        ));
    }

    /**
     * Get attendance summary statistics for dashboard
     */
    public function getStats()
    {
        $mahasiswa = auth()->guard('mahasiswa')->user();

        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'aktif')
            ->with('jadwalMengajar')
            ->get();

        $totalClasses = $krs->count();
        $totalPresensi = Presensi::where('mahasiswa_id', $mahasiswa->id)->count();
        $totalHadir = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'hadir')
            ->count();

        $overallPersentase = $totalPresensi > 0 ? round(($totalHadir / $totalPresensi) * 100, 2) : 0;

        // Classes dengan presensi below 80%
        $classesWarning = [];
        foreach ($krs as $k) {
            $classPresensi = Presensi::where('mahasiswa_id', $mahasiswa->id)
                ->where('jadwal_mengajar_id', $k->jadwal_mengajar_id)
                ->get();

            $persentase = $classPresensi->count() > 0 ? 
                round(($classPresensi->where('status', 'hadir')->count() / $classPresensi->count()) * 100, 2) : 0;

            if ($persentase < 80 && $classPresensi->count() > 0) {
                $classesWarning[] = [
                    'jadwal' => $k->jadwalMengajar,
                    'persentase' => $persentase
                ];
            }
        }

        return response()->json([
            'total_classes' => $totalClasses,
            'total_presensi' => $totalPresensi,
            'total_hadir' => $totalHadir,
            'overall_persentase' => $overallPersentase,
            'classes_warning' => $classesWarning
        ]);
    }

    /**
     * Export attendance report
     */
    public function exportReport()
    {
        $mahasiswa = auth()->guard('mahasiswa')->user();

        $presensiData = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->with('jadwalMengajar')
            ->orderBy('tanggal', 'desc')
            ->get();

        $filename = 'Presensi_' . str_replace(' ', '_', $mahasiswa->nama ?? 'Mahasiswa') . '_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($mahasiswa, $presensiData) {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, ['Laporan Presensi Mahasiswa']);
            fputcsv($file, ['NIM', $mahasiswa->nim ?? '-']);
            fputcsv($file, ['Nama', $mahasiswa->nama ?? '-']);
            fputcsv($file, ['Tanggal Laporan', date('d-m-Y H:i:s')]);
            fputcsv($file, []);

            // Write column headers
            fputcsv($file, ['Kode MK', 'Mata Kuliah', 'Dosen', 'Tanggal', 'Status', 'Jam Masuk', 'Jam Keluar', 'Keterangan']);

            // Write data
            foreach ($presensiData as $p) {
                fputcsv($file, [
                    $p->jadwalMengajar->kode_mk ?? '-',
                    $p->jadwalMengajar->mata_kuliah ?? '-',
                    $p->dosen->user->name ?? '-',
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
     * Get attendance history for today or specific date
     */
    public function getTodayPresensi()
    {
        $mahasiswa = auth()->guard('mahasiswa')->user();
        $today = Carbon::now()->toDateString();

        $presensi = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('tanggal', $today)
            ->with('jadwalMengajar')
            ->get();

        return response()->json($presensi);
    }

    /**
     * Check in for a class (jika menggunakan check-in system)
     */
    public function checkIn(Request $request)
    {
        $mahasiswa = auth()->guard('mahasiswa')->user();

        $validated = $request->validate([
            'jadwal_mengajar_id' => 'required|exists:jadwal_mengajar,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $today = Carbon::now()->toDateString();

        // Check if already checked in today
        $existingCheckIn = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $validated['jadwal_mengajar_id'])
            ->where('tanggal', $today)
            ->first();

        if ($existingCheckIn) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in untuk kelas ini hari ini'
            ], 400);
        }

        // Get KRS to verify enrollment
        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $validated['jadwal_mengajar_id'])
            ->first();

        if (!$krs) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar dalam kelas ini'
            ], 403);
        }

        // Create presensi with check-in time
        $presensi = Presensi::create([
            'krs_id' => $krs->id,
            'mahasiswa_id' => $mahasiswa->id,
            'jadwal_mengajar_id' => $validated['jadwal_mengajar_id'],
            'dosen_id' => JadwalMengajar::find($validated['jadwal_mengajar_id'])->dosen_id,
            'tanggal' => $today,
            'status' => 'hadir',
            'jam_masuk' => Carbon::now()->format('H:i'),
            'sudah_dikonfirmasi' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil',
            'presensi' => $presensi
        ]);
    }

    /**
     * Check out from a class
     */
    public function checkOut(Request $request, $presensiId)
    {
        $mahasiswa = auth()->guard('mahasiswa')->user();
        $presensi = Presensi::findOrFail($presensiId);

        // Verify ownership
        if ($presensi->mahasiswa_id !== $mahasiswa->id) {
            abort(403, 'Unauthorized');
        }

        // Update check-out time
        $presensi->update([
            'jam_keluar' => Carbon::now()->format('H:i')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil',
            'presensi' => $presensi
        ]);
    }
}
