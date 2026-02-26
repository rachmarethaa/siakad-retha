<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\JadwalMengajar;
use App\Models\KRS;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PresensiReportController extends Controller
{
    /**
     * Laporan Presensi untuk Dosen - Dashboard
     */
    public function dosenDashboard()
    {
        $dosen = auth()->guard('dosen')->user();

        // Get all jadwal mengajar milik dosen
        $jadwals = JadwalMengajar::where('dosen_id', $dosen->id)->get();

        // Calculate statistics
        $totalClasses = $jadwals->count();
        $totalStudents = KRS::whereIn('jadwal_mengajar_id', $jadwals->pluck('id'))
            ->where('status', 'aktif')
            ->count();

        // Get presensi statistics
        $presensiData = Presensi::whereIn('jadwal_mengajar_id', $jadwals->pluck('id'))
            ->get()
            ->groupBy('status')
            ->map->count();

        $totalHadir = $presensiData['hadir'] ?? 0;
        $totalAbsen = $presensiData['absen'] ?? 0;
        $totalIzin = $presensiData['izin'] ?? 0;
        $totalSakit = $presensiData['sakit'] ?? 0;

        // Get recent attendance records
        $recentPresensi = Presensi::whereIn('jadwal_mengajar_id', $jadwals->pluck('id'))
            ->with('mahasiswa', 'jadwalMengajar')
            ->latest('tanggal')
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Class-wise statistics
        $classStats = [];
        foreach ($jadwals as $jadwal) {
            $krsCount = KRS::where('jadwal_mengajar_id', $jadwal->id)
                ->where('status', 'aktif')
                ->count();

            $presensi = Presensi::where('jadwal_mengajar_id', $jadwal->id)->get();
            $hdir = $presensi->where('status', 'hadir')->count();
            $abs = $presensi->where('status', 'absen')->count();

            $classStats[] = [
                'jadwal' => $jadwal,
                'students' => $krsCount,
                'hadir' => $hdir,
                'absen' => $abs,
                'percentage' => $krsCount > 0 ? round(($hdir / $krsCount) * 100, 2) : 0
            ];
        }

        return view('Dosen.laporan.dashboard', compact(
            'dosen', 'totalClasses', 'totalStudents',
            'totalHadir', 'totalAbsen', 'totalIzin', 'totalSakit',
            'recentPresensi', 'classStats'
        ));
    }

    /**
     * Laporan Detail per Kelas
     */
    public function dosenClassReport($jadwalId)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        // Get semua student di kelas
        $krs = KRS::where('jadwal_mengajar_id', $jadwal->id)
            ->where('status', 'aktif')
            ->with('mahasiswa')
            ->get();

        // Get presensi data untuk kelas ini
        $presensiByStudent = Presensi::where('jadwal_mengajar_id', $jadwal->id)
            ->get()
            ->groupBy('mahasiswa_id');

        // Calculate attendance percentage per student
        $studentStats = [];
        foreach ($krs as $k) {
            $studentPresensi = $presensiByStudent[$k->mahasiswa_id] ?? collect([]);
            $totalRecords = $studentPresensi->count();

            if ($totalRecords > 0) {
                $hadir = $studentPresensi->where('status', 'hadir')->count();
                $percentage = ($hadir / $totalRecords) * 100;
            } else {
                $percentage = 0;
            }

            $studentStats[] = [
                'mahasiswa' => $k->mahasiswa,
                'nim' => $k->mahasiswa->nim,
                'prodi' => $k->mahasiswa->prodi ?? '-',
                'total' => $totalRecords,
                'hadir' => $studentPresensi->where('status', 'hadir')->count(),
                'absen' => $studentPresensi->where('status', 'absen')->count(),
                'izin' => $studentPresensi->where('status', 'izin')->count(),
                'sakit' => $studentPresensi->where('status', 'sakit')->count(),
                'percentage' => round($percentage, 2)
            ];
        }

        // Sort by attendance percentage
        usort($studentStats, function ($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        return view('Dosen.laporan.class', compact('jadwal', 'dosen', 'studentStats'));
    }

    /**
     * Export Laporan Excel - Dosen
     */
    public function dosenExportExcel($jadwalId)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        // Get data
        $krs = KRS::where('jadwal_mengajar_id', $jadwal->id)
            ->where('status', 'aktif')
            ->with('mahasiswa')
            ->get();

        $presensiByStudent = Presensi::where('jadwal_mengajar_id', $jadwal->id)
            ->get()
            ->groupBy('mahasiswa_id');

        $filename = 'Laporan_Presensi_' . $jadwal->kode_mk . '_' . now()->format('dmY') . '.xlsx';

        return \Excel::download(new PresensiExport($krs, $presensiByStudent, $jadwal), $filename);
    }

    /**
     * Export PDF - Dosen
     */
    public function dosenExportPdf($jadwalId)
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        $krs = KRS::where('jadwal_mengajar_id', $jadwal->id)
            ->where('status', 'aktif')
            ->with('mahasiswa')
            ->get();

        $presensiByStudent = Presensi::where('jadwal_mengajar_id', $jadwal->id)
            ->get()
            ->groupBy('mahasiswa_id');

        $studentStats = [];
        foreach ($krs as $k) {
            $studentPresensi = $presensiByStudent[$k->mahasiswa_id] ?? collect([]);
            $totalRecords = $studentPresensi->count();

            if ($totalRecords > 0) {
                $hadir = $studentPresensi->where('status', 'hadir')->count();
                $percentage = ($hadir / $totalRecords) * 100;
            } else {
                $percentage = 0;
            }

            $studentStats[] = [
                'mahasiswa' => $k->mahasiswa->nama,
                'nim' => $k->mahasiswa->nim,
                'prodi' => $k->mahasiswa->prodi ?? '-',
                'total' => $totalRecords,
                'hadir' => $studentPresensi->where('status', 'hadir')->count(),
                'absen' => $studentPresensi->where('status', 'absen')->count(),
                'izin' => $studentPresensi->where('status', 'izin')->count(),
                'sakit' => $studentPresensi->where('status', 'sakit')->count(),
                'percentage' => round($percentage, 2)
            ];
        }

        $pdf = PDF::loadView('Dosen.laporan.pdf', compact('jadwal', 'dosen', 'studentStats'));

        return $pdf->download('Laporan_Presensi_' . $jadwal->kode_mk . '.pdf');
    }

    /**
     * Laporan Presensi untuk Staff - Dashboard
     */
    public function staffDashboard()
    {
        // Get semua presensi data
        $totalClasses = JadwalMengajar::count();
        $totalStudents = Mahasiswa::count();

        $presensiData = Presensi::get()
            ->groupBy('status')
            ->map->count();

        $totalHadir = $presensiData['hadir'] ?? 0;
        $totalAbsen = $presensiData['absen'] ?? 0;
        $totalIzin = $presensiData['izin'] ?? 0;
        $totalSakit = $presensiData['sakit'] ?? 0;
        $totalRecords = array_sum((array) $presensiData);

        // Get top attendance performers
        $topAttendance = Presensi::where('status', 'hadir')
            ->with(['mahasiswa', 'jadwalMengajar.dosen'])
            ->latest('created_at')
            ->limit(15)
            ->get();

        // Class with highest absenteeism
        $lowestAttendance = JadwalMengajar::withCount('presensi')
            ->with('dosen')
            ->orderByDesc('presensi_count')
            ->limit(10)
            ->get();

        // Dosen performance
        $dosenStats = [];
        $dosens = \App\Models\Dosen::all();

        foreach ($dosens as $d) {
            $jadwals = JadwalMengajar::where('dosen_id', $d->id)->get();
            $totalPresensi = Presensi::whereIn('jadwal_mengajar_id', $jadwals->pluck('id'))->count();

            if ($totalPresensi > 0) {
                $hadir = Presensi::whereIn('jadwal_mengajar_id', $jadwals->pluck('id'))
                    ->where('status', 'hadir')
                    ->count();
                $percentage = ($hadir / $totalPresensi) * 100;
            } else {
                $percentage = 0;
            }

            $dosenStats[] = [
                'dosen_id' => $d->id,
                'dosen' => $d->nama_lengkap ?? $d->name,
                'classes' => $jadwals->count(),
                'percentage' => round($percentage, 2)
            ];
        }

        return view('Staff.laporan.dashboard', compact(
            'totalClasses', 'totalStudents', 'totalHadir', 'totalAbsen',
            'totalIzin', 'totalSakit', 'totalRecords', 'topAttendance',
            'lowestAttendance', 'dosenStats'
        ));
    }

    /**
     * Staff: Laporan per Dosen
     */
    public function staffDosenReport($dosenId)
    {
        $dosen = \App\Models\Dosen::findOrFail($dosenId);
        $jadwals = JadwalMengajar::where('dosen_id', $dosenId)->get();

        $classStats = [];
        foreach ($jadwals as $jadwal) {
            $krsCount = KRS::where('jadwal_mengajar_id', $jadwal->id)
                ->where('status', 'aktif')
                ->count();

            $presensi = Presensi::where('jadwal_mengajar_id', $jadwal->id)->get();
            $hadir = $presensi->where('status', 'hadir')->count();
            $absen = $presensi->where('status', 'absen')->count();
            $izin = $presensi->where('status', 'izin')->count();
            $sakit = $presensi->where('status', 'sakit')->count();
            $total = $presensi->count();

            $percentage = $total > 0 ? ($hadir / $total) * 100 : 0;

            $classStats[] = [
                'jadwal' => $jadwal,
                'students' => $krsCount,
                'total_records' => $total,
                'hadir' => $hadir,
                'absen' => $absen,
                'izin' => $izin,
                'sakit' => $sakit,
                'percentage' => round($percentage, 2)
            ];
        }

        return view('Staff.laporan.dosen', compact('dosen', 'classStats'));
    }

    /**
     * Staff: Laporan per Mahasiswa (Overall)
     */
    public function staffStudentReport()
    {
        $mahasiswas = Mahasiswa::with('presensi')->get();

        $studentStats = [];
        foreach ($mahasiswas as $m) {
            $presensi = $m->presensi;
            $total = $presensi->count();
            $hadir = $presensi->where('status', 'hadir')->count();
            $absen = $presensi->where('status', 'absen')->count();
            $izin = $presensi->where('status', 'izin')->count();
            $sakit = $presensi->where('status', 'sakit')->count();
            $percentage = $total > 0 ? ($hadir / $total) * 100 : 0;

            // Include students even if they have no presensi records yet
            $studentStats[] = [
                'mahasiswa' => $m->nama ?? ($m->name ?? '-'),
                'prodi' => $m->prodi ?? '-',
                'nim' => $m->nim ?? '-',
                'total' => $total,
                'hadir' => $hadir,
                'absen' => $absen,
                'izin' => $izin,
                'sakit' => $sakit,
                'percentage' => round($percentage, 2)
            ];
        }

        // Sort by attendance percentage
        usort($studentStats, function ($a, $b) {
            return $a['percentage'] <=> $b['percentage'];
        });

        return view('Staff.laporan.mahasiswa', compact('studentStats'));
    }

    /**
     * Staff: Export Report Excel
     */
    public function staffExportExcel()
    {
        $filename = 'Laporan_Presensi_' . now()->format('dmY') . '.xlsx';
        return \Excel::download(new PresensiStaffExport(), $filename);
    }
}
