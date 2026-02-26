<?php

namespace App\Http\Controllers;

use App\Models\NilaiMahasiswa;
use App\Models\JadwalMengajar;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    /**
     * List nilai untuk jadwal mengajar milik dosen
     */
    public function index()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil semua jadwal mengajar milik dosen ini
        $jadwals = JadwalMengajar::where('dosen_id', $dosen->id)
            ->with('nilai')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('dosen.nilai.index', compact('jadwals', 'dosen'));
    }

    /**
     * Show form input nilai untuk satu jadwal mengajar
     */
    public function create($jadwal_id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil jadwal
        $jadwal = JadwalMengajar::findOrFail($jadwal_id);
        
        // Verifikasi bahwa jadwal ini milik dosen yang login
        if ($jadwal->dosen_id != $dosen->id) {
            abort(403, 'Anda tidak berhak mengakses jadwal ini');
        }

        // Ambil mahasiswa yang relevan: jika jadwal punya 'prodi', filter berdasarkan prodi
        if (!empty($jadwal->prodi)) {
            $mahasiswas = Mahasiswa::where('prodi', $jadwal->prodi)->orderBy('name')->get();
        } else {
            $mahasiswas = Mahasiswa::orderBy('name')->get();
        }

        // Ambil nilai yang sudah ada untuk jadwal ini
        $nilais = NilaiMahasiswa::where('jadwal_mengajar_id', $jadwal_id)
            ->get()
            ->keyBy('mahasiswa_id');

        return view('dosen.nilai.create', compact('jadwal', 'mahasiswas', 'nilais'));
    }

    /**
     * Save nilai untuk semua mahasiswa dalam satu jadwal
     */
    public function store(Request $request, $jadwal_id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $jadwal = JadwalMengajar::findOrFail($jadwal_id);
        
        // Verifikasi ownership
        if ($jadwal->dosen_id != $dosen->id) {
            abort(403, 'Anda tidak berhak mengakses jadwal ini');
        }

        // Validasi input
        $validated = $request->validate([
            'nilai.*.mahasiswa_id' => 'required|exists:mahasiswa,id',
            'nilai.*.nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilai.*.nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai.*.nilai_uas' => 'nullable|numeric|min:0|max:100',
            'nilai.*.keterangan' => 'nullable|string|max:500',
        ]);

        // Simpan nilai untuk setiap mahasiswa
        foreach ($request->nilai as $nilai_data) {
            if (!empty($nilai_data['mahasiswa_id'])) {
                // Normalisasi: kosong atau empty string => null, else cast ke float
                $nilai_tugas = isset($nilai_data['nilai_tugas']) && $nilai_data['nilai_tugas'] !== ''
                    ? floatval($nilai_data['nilai_tugas'])
                    : null;
                $nilai_uts = isset($nilai_data['nilai_uts']) && $nilai_data['nilai_uts'] !== ''
                    ? floatval($nilai_data['nilai_uts'])
                    : null;
                $nilai_uas = isset($nilai_data['nilai_uas']) && $nilai_data['nilai_uas'] !== ''
                    ? floatval($nilai_data['nilai_uas'])
                    : null;

                // Hitung nilai akhir
                $nilai_akhir = NilaiMahasiswa::hitungNilaiAkhir($nilai_tugas, $nilai_uts, $nilai_uas);
                
                // Hitung grade
                $grade = NilaiMahasiswa::nilaiToGrade($nilai_akhir);

                // Update atau create nilai
                NilaiMahasiswa::updateOrCreate(
                    [
                        'jadwal_mengajar_id' => $jadwal_id,
                        'mahasiswa_id' => $nilai_data['mahasiswa_id'],
                    ],
                    [
                        'nilai_tugas' => $nilai_tugas,
                        'nilai_uts' => $nilai_uts,
                        'nilai_uas' => $nilai_uas,
                        'nilai_akhir' => $nilai_akhir,
                        'grade' => $grade,
                        'keterangan' => $nilai_data['keterangan'] ?? null,
                        'input_at' => now(),
                    ]
                );
            }
        }

        return redirect()->route('dosen.nilai.index')
            ->with('success', 'Nilai berhasil disimpan untuk ' . $jadwal->mata_kuliah);
    }

    /**
     * Show edit form untuk satu mahasiswa
     */
    public function edit($jadwal_id, $mahasiswa_id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $jadwal = JadwalMengajar::findOrFail($jadwal_id);
        
        if ($jadwal->dosen_id != $dosen->id) {
            abort(403);
        }

        $mahasiswa = Mahasiswa::findOrFail($mahasiswa_id);

        $nilai = NilaiMahasiswa::where('jadwal_mengajar_id', $jadwal_id)
            ->where('mahasiswa_id', $mahasiswa_id)
            ->first();

        if (!$nilai) {
            // Jika belum ada nilai, arahkan ke halaman input massal atau create khusus
            return redirect()->route('dosen.nilai.create', $jadwal_id)
                ->with('info', 'Nilai untuk mahasiswa ini belum dibuat. Silakan input nilai melalui halaman Input Nilai.');
        }

        return view('dosen.nilai.edit', compact('jadwal', 'mahasiswa', 'nilai'));
    }

    /**
     * Update nilai untuk satu mahasiswa
     */
    public function update(Request $request, $jadwal_id, $mahasiswa_id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $jadwal = JadwalMengajar::findOrFail($jadwal_id);
        
        if ($jadwal->dosen_id != $dosen->id) {
            abort(403);
        }

        $nilai = NilaiMahasiswa::where('jadwal_mengajar_id', $jadwal_id)
            ->where('mahasiswa_id', $mahasiswa_id)
            ->firstOrFail();

        $validated = $request->validate([
            'nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'nullable|numeric|min:0|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Hitung nilai akhir
        $nilai_tugas = isset($validated['nilai_tugas']) && $validated['nilai_tugas'] !== ''
            ? floatval($validated['nilai_tugas'])
            : null;
        $nilai_uts = isset($validated['nilai_uts']) && $validated['nilai_uts'] !== ''
            ? floatval($validated['nilai_uts'])
            : null;
        $nilai_uas = isset($validated['nilai_uas']) && $validated['nilai_uas'] !== ''
            ? floatval($validated['nilai_uas'])
            : null;

        $nilai_akhir = NilaiMahasiswa::hitungNilaiAkhir(
            $nilai_tugas,
            $nilai_uts,
            $nilai_uas
        );

        // Hitung grade
        $grade = NilaiMahasiswa::nilaiToGrade($nilai_akhir);

        $nilai->update([
            'nilai_tugas' => $nilai_tugas,
            'nilai_uts' => $nilai_uts,
            'nilai_uas' => $nilai_uas,
            'nilai_akhir' => $nilai_akhir,
            'grade' => $grade,
            'keterangan' => $validated['keterangan'] ?? null,
            'input_at' => now(),
        ]);

        return redirect()->route('dosen.nilai.index')
            ->with('success', 'Nilai untuk ' . $nilai->mahasiswa->name . ' berhasil diperbarui');
    }

    /**
     * View detail nilai untuk satu jadwal
     */
    public function show($jadwal_id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $jadwal = JadwalMengajar::findOrFail($jadwal_id);
        
        if ($jadwal->dosen_id != $dosen->id) {
            abort(403);
        }

        $nilais = NilaiMahasiswa::where('jadwal_mengajar_id', $jadwal_id)
            ->with('mahasiswa')
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung statistik
        if (!empty($jadwal->prodi)) {
            $total_mahasiswa = Mahasiswa::where('prodi', $jadwal->prodi)->count();
        } else {
            $total_mahasiswa = Mahasiswa::count();
        }

        $sudah_dinilai = $nilais->count();
        $belum_dinilai = max(0, $total_mahasiswa - $sudah_dinilai);

        $rata_rata_nilai = $nilais->avg('nilai_akhir');
        $tertinggi = $nilais->max('nilai_akhir');
        $terendah = $nilais->min('nilai_akhir');

        return view('dosen.nilai.show', compact('jadwal', 'nilais', 'sudah_dinilai', 'belum_dinilai', 'rata_rata_nilai', 'tertinggi', 'terendah'));
    }
}
