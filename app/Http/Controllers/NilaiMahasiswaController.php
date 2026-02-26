<?php

namespace App\Http\Controllers;

use App\Models\NilaiMahasiswa;
use Illuminate\Support\Facades\Auth;

class NilaiMahasiswaController extends Controller
{
    /**
     * Display all grades for the logged-in mahasiswa
     */
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $nilais = NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->with('jadwalMengajar', 'jadwalMengajar.dosen')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('Mahasiswa.nilai.index', compact('nilais', 'mahasiswa'));
    }

    /**
     * Display transkip (academic transcript)
     */
    public function transkip()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $nilais = NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
            ->with('jadwalMengajar', 'jadwalMengajar.dosen')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate GPA
        $totalCredits = 0;
        $totalPoints = 0;
        $gradeWeights = [
            'A' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'D' => 1.0,
            'E' => 0.0,
        ];

        foreach ($nilais as $nilai) {
            $weight = $gradeWeights[$nilai->grade] ?? 0;
            $totalPoints += $weight;
            $totalCredits += 1;
        }

        $gpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.00;

        return view('Mahasiswa.nilai.transkip', compact('nilais', 'mahasiswa', 'gpa'));
    }
}
