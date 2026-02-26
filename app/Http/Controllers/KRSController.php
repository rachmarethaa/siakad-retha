<?php

namespace App\Http\Controllers;

use App\Models\KRS;
use App\Models\JadwalMengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KRSController extends Controller
{
    /**
     * Display mahasiswa's KRS (taken courses)
     */
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'aktif')
            ->with('jadwalMengajar', 'jadwalMengajar.dosen')
            ->orderBy('diambil_at', 'desc')
            ->paginate(15);

        $totalSks = 0; // placeholder untuk SKS jika ada di masa depan

        return view('Mahasiswa.krs.index', compact('krs', 'mahasiswa', 'totalSks'));
    }

    /**
     * Show form to add course to KRS
     */
    public function create()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Ambil semua jadwal mengajar sesuai prodi mahasiswa
        if (!empty($mahasiswa->prodi)) {
            $maCode = strtolower(trim($mahasiswa->prodi));
            $maLabel = config('prodi.' . $maCode) ? strtolower(trim(config('prodi.' . $maCode))) : null;

            $jadwals = JadwalMengajar::with('dosen')
                ->where(function($q) use ($maCode, $maLabel) {
                    $q->whereRaw('LOWER(TRIM(prodi)) = ?', [$maCode]);
                    if ($maLabel) {
                        $q->orWhereRaw('LOWER(TRIM(prodi)) = ?', [$maLabel]);
                    }
                    $q->orWhereNull('prodi');
                })
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();
        } else {
            $jadwals = JadwalMengajar::with('dosen')
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();
        }

        // Filter out jadwal yang sudah diambil (any status)
        $krses = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->pluck('jadwal_mengajar_id');

        $availableJadwals = $jadwals->whereNotIn('id', $krses);

        // Log for debugging why some jadwals may not appear
        Log::info('KRS create: available jadwals', [
            'mahasiswa_id' => $mahasiswa->id,
            'mahasiswa_prodi' => $mahasiswa->prodi,
            'total_jadwals_matching_prodi' => $jadwals->count(),
            'total_available' => $availableJadwals->count(),
            'sample_jadwals' => $availableJadwals->take(5)->map(function($j){ return ['id'=>$j->id,'mata_kuliah'=>$j->mata_kuliah,'prodi'=>$j->prodi]; }),
        ]);

        return view('Mahasiswa.krs.create', compact('availableJadwals', 'mahasiswa'));
    }

    /**
     * Store (add) course to KRS
     */
    public function store(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        $validated = $request->validate([
            'jadwal_mengajar_id' => 'required|exists:jadwal_mengajar,id',
        ]);

        // Check if jadwal matches mahasiswa's prodi (normalize to avoid casing/spaces mismatch)
        $jadwal = JadwalMengajar::findOrFail($validated['jadwal_mengajar_id']);

        if (!empty($mahasiswa->prodi)) {
            $maCode = strtolower(trim($mahasiswa->prodi));
            $maLabel = config('prodi.' . $maCode) ? strtolower(trim(config('prodi.' . $maCode))) : null;
            $jadwalProdi = strtolower(trim((string) $jadwal->prodi));

            Log::info('KRS store: prodi check', ['mahasiswa_id'=>$mahasiswa->id,'ma_prodi'=>$maCode,'jadwal_id'=>$jadwal->id,'jadwal_prodi'=>$jadwalProdi]);

            // allow if jadwal->prodi is null (general course)
            if (!empty($jadwal->prodi)) {
                $matchesCode = ($jadwalProdi === $maCode);
                $matchesLabel = ($maLabel ? $jadwalProdi === $maLabel : false);
                if (! $matchesCode && ! $matchesLabel) {
                    return back()->with('error', 'Mata kuliah tidak sesuai dengan program studi Anda');
                }
            }
        }

        // Check if already taken (any status)
        $existingKRS = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_mengajar_id', $validated['jadwal_mengajar_id'])
            ->first();

        if ($existingKRS && $existingKRS->status === 'aktif') {
            return back()->with('error', 'Anda sudah mengambil mata kuliah ini');
        }

        // Create or update KRS
        KRS::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'jadwal_mengajar_id' => $validated['jadwal_mengajar_id'],
            ],
            [
                'status' => 'aktif',
                'diambil_at' => now(),
            ]
        );

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'Mata kuliah berhasil ditambahkan ke KRS');
    }

    /**
     * Remove course from KRS (cancel)
     */
    public function destroy($id)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $krs = KRS::findOrFail($id);

        if ($krs->mahasiswa_id !== $mahasiswa->id) {
            abort(403);
        }

        $krs->update(['status' => 'batal']);

        return back()->with('success', 'Mata kuliah berhasil dibatalkan dari KRS');
    }

    /**
     * Show detail KRS
     */
    public function show($id)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)->findOrFail($id);
        $krs->load('jadwalMengajar', 'jadwalMengajar.dosen');

        return view('Mahasiswa.krs.show', compact('krs', 'mahasiswa'));
    }
}
