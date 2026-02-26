<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\JadwalMengajar;
use App\Models\KRS;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffPresensiController extends Controller
{
    /**
     * List all jadwal for staff to choose
     */
    public function index()
    {
        $jadwals = JadwalMengajar::with('dosen')->orderBy('hari')->orderBy('jam_mulai')->get();

        return view('Staff.presensi.index', compact('jadwals'));
    }

    /**
     * Show form to input presensi for a specific jadwal
     */
    public function create($jadwalId)
    {
        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        $krs = KRS::where('jadwal_mengajar_id', $jadwal->id)
            ->where('status', 'aktif')
            ->with('mahasiswa')
            ->get();

        $tanggal = Carbon::now()->toDateString();

        $existingPresensi = Presensi::where('jadwal_mengajar_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('mahasiswa_id');

        return view('Staff.presensi.create', compact('jadwal', 'krs', 'tanggal', 'existingPresensi'));
    }

    /**
     * Store presensi submitted by staff
     */
    public function store(Request $request, $jadwalId)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.mahasiswa_id' => 'required|exists:mahasiswa,id',
            'presensi.*.status' => 'required|in:hadir,absen,izin,sakit',
            'presensi.*.jam_masuk' => 'nullable|date_format:H:i',
            'presensi.*.jam_keluar' => 'nullable|date_format:H:i',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        $jadwal = JadwalMengajar::findOrFail($jadwalId);

        // Delete existing presensi for this date and class
        Presensi::where('jadwal_mengajar_id', $jadwal->id)
            ->where('tanggal', $validated['tanggal'])
            ->delete();

        foreach ($validated['presensi'] as $p) {
            $krs = KRS::where('mahasiswa_id', $p['mahasiswa_id'])
                ->where('jadwal_mengajar_id', $jadwal->id)
                ->first();

            if ($krs) {
                Presensi::create([
                    'krs_id' => $krs->id,
                    'mahasiswa_id' => $p['mahasiswa_id'],
                    'jadwal_mengajar_id' => $jadwal->id,
                    'dosen_id' => $jadwal->dosen_id ?? null,
                    'tanggal' => $validated['tanggal'],
                    'status' => $p['status'],
                    'jam_masuk' => $p['jam_masuk'] ?? null,
                    'jam_keluar' => $p['jam_keluar'] ?? null,
                    'keterangan' => $p['keterangan'] ?? null,
                    'sudah_dikonfirmasi' => true,
                ]);
            }
        }

        return redirect()->route('staff.presensi.index')
            ->with('success', 'Presensi berhasil disimpan oleh staff');
    }
}
