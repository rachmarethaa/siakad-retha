<?php

namespace App\Http\Controllers;

use App\Models\JadwalMengajar;
use App\Models\NilaiMahasiswa;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index()
    {
        $dosen = auth()->guard('dosen')->user();
        $jadwals = JadwalMengajar::with('dosen')
            ->where('dosen_id', $dosen->id)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        // Hitung statistik
        $totalJadwal = JadwalMengajar::where('dosen_id', $dosen->id)->count();
        
        $nilaiCount = NilaiMahasiswa::whereIn('jadwal_mengajar_id', 
            JadwalMengajar::where('dosen_id', $dosen->id)->pluck('id')
        )->count();

        $totalMahasiswa = NilaiMahasiswa::whereIn('jadwal_mengajar_id',
            JadwalMengajar::where('dosen_id', $dosen->id)->pluck('id')
        )->distinct('mahasiswa_id')->count();

        return view('Dosen.dashboard', compact('jadwals', 'dosen', 'totalJadwal', 'nilaiCount', 'totalMahasiswa'));
    }
}
