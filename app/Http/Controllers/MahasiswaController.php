<?php

namespace App\Http\Controllers;

use App\Models\JadwalMengajar;
use App\Models\KRS;
use App\Models\NilaiMahasiswa;
use App\Models\TiketMahasiswa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MahasiswaController extends Controller
{
    /**
     * Display dashboard dengan:
     * - Jadwal kuliah yang diambil via KRS
     * - Semester aktif dari KRS courses
     * - Total KRS diambil
     * - GPA/IP dari nilai
     * - Tiket terbaru
     */
    public function index()
    {
        $mahasiswa = auth()->guard('mahasiswa')->user();
        
        // Get KRS yang aktif dengan jadwal dan dosen
        $krsAktif = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'aktif')
            ->with('jadwalMengajar.dosen')
            ->get();

        // Get hanya jadwal yang ada di KRS
        $jadwalIds = $krsAktif->pluck('jadwal_mengajar_id');
        $jadwals = JadwalMengajar::with('dosen')
            ->whereIn('id', $jadwalIds)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        // Ambil semester aktif dari jadwal KRS (semester pertama yang ditemukan)
        $semesterAktif = JadwalMengajar::whereIn('id', $jadwalIds)
            ->whereNotNull('semester')
            ->orderBy('semester')
            ->value('semester') ?? '-';

        // Get KRS count
        $krsCount = $krsAktif->count();

        // Get nilai/GPA
        $nilais = NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->get();
        
        $gpa = 0;
        $gradeWeights = [
            'A' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'D' => 1.0,
            'E' => 0.0,
        ];

        if ($nilais->count() > 0) {
            $totalPoints = 0;
            foreach ($nilais as $nilai) {
                $weight = $gradeWeights[$nilai->grade] ?? 0;
                $totalPoints += $weight;
            }
            $gpa = round($totalPoints / $nilais->count(), 2);
        }

        // Get recent tikets
        $recentTikets = TiketMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->latest()
            ->take(5)
            ->get();

        return view('Mahasiswa.dashboard', compact('jadwals', 'mahasiswa', 'krsCount', 'gpa', 'nilais', 'recentTikets', 'semesterAktif'));
    }
}
