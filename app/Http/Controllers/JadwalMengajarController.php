<?php

namespace App\Http\Controllers;

use App\Models\JadwalMengajar;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\KRS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JadwalMengajarController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalMengajar::with('dosen');

        if ($request->filled('dosen_id')) {
            $query->where('dosen_id', $request->dosen_id);
        }

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('mata_kuliah', 'like', "%{$s}%")
                  ->orWhere('kode_mk', 'like', "%{$s}%")
                  ->orWhere('prodi', 'like', "%{$s}%")
                  ->orWhere('ruang', 'like', "%{$s}%");
            });
        }

        $jadwals = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(15)->withQueryString();
        $dosen = Dosen::orderBy('name')->get();
        $prodis = config('prodi');

        return view('jadwal_mengajar.index', compact('jadwals', 'dosen', 'prodis'));
    }

    public function publicIndex()
    {
        $jadwals = JadwalMengajar::with('dosen')->orderBy('hari')->orderBy('jam_mulai')->get()->groupBy('hari');
        return view('jadwal_mengajar.public_index', compact('jadwals'));
    }

    /**
     * Dosen: lihat jadwal mengajarnya (authed dosen)
     */
    public function dosenIndex(Request $request)
    {
        $dosen = auth()->guard('dosen')->user();
        if (! $dosen) {
            abort(403);
        }

        $query = JadwalMengajar::with('dosen')->where('dosen_id', $dosen->id)->orderBy('hari')->orderBy('jam_mulai');

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $jadwals = $query->get()->groupBy('hari');

        return view('jadwal_mengajar.dosen_index', compact('jadwals', 'dosen'));
    }

    /**
     * Mahasiswa: lihat jadwal kuliah (hanya yang ada di KRS)
     */
    public function mahasiswaIndex(Request $request)
    {
        $mahasiswa = auth()->guard('mahasiswa')->user();
        
        // Ambil jadwal dari KRS yang aktif
        $krsJadwalIds = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'aktif')
            ->pluck('jadwal_mengajar_id');
        
        $query = JadwalMengajar::with('dosen')
            ->whereIn('id', $krsJadwalIds)
            ->orderBy('hari')
            ->orderBy('jam_mulai');

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('mata_kuliah', 'like', "%{$s}%")
                  ->orWhere('kode_mk', 'like', "%{$s}%");
            });
        }

        $jadwals = $query->get()->groupBy('hari');
        return view('jadwal_mengajar.mahasiswa_index', compact('jadwals'));
    }

    public function create()
    {
        $dosen = Dosen::orderBy('name')->get();
        $prodis = config('prodi');
        return view('jadwal_mengajar.create', compact('dosen', 'prodis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dosen_id' => 'nullable|exists:dosen,id',
            'kode_mk' => 'nullable|string|max:100',
            'mata_kuliah' => 'required|string|max:255',
            'prodi' => 'nullable|string|max:50',
            'ruang' => 'nullable|string|max:50',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'semester' => 'nullable|string|max:50',
        ]);

        // Normalisasi nilai prodi agar konsisten (hilangkan spasi di ujung)
        if (isset($data['prodi'])) {
            $data['prodi'] = trim($data['prodi']);
            if ($data['prodi'] === '') {
                $data['prodi'] = null;
            }
        }

        JadwalMengajar::create($data);

        return redirect()->route('staff.jadwal.index')->with('success', 'Jadwal berhasil dibuat.');
    }

    public function show($id)
    {
        $jadwal = JadwalMengajar::with('dosen')->findOrFail($id);
        return view('jadwal_mengajar.show', compact('jadwal'));
    }

    public function edit($id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);
        $dosen = Dosen::orderBy('name')->get();
        $prodis = config('prodi');
        return view('jadwal_mengajar.edit', compact('jadwal', 'dosen', 'prodis'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);

        // Log raw request for debugging validation issues (exclude token/method)
        Log::info('Jadwal update request received', ['id' => $id, 'input' => $request->except(['_token', '_method'])]);

        $data = $request->validate([
            'dosen_id' => 'nullable|exists:dosen,id',
            'kode_mk' => 'nullable|string|max:100',
            'mata_kuliah' => 'required|string|max:255',
            'prodi' => 'nullable|string|max:50',
            'ruang' => 'nullable|string|max:50',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'semester' => 'nullable|string|max:50',
        ]);

        // Normalisasi prodi sebelum simpan
        if (isset($data['prodi'])) {
            $data['prodi'] = trim($data['prodi']);
            if ($data['prodi'] === '') {
                $data['prodi'] = null;
            }
        }

        try {
            $jadwal->fill($data);
            $saved = $jadwal->save();
            Log::info('Jadwal update attempt', ['id' => $jadwal->id, 'data' => $data, 'saved' => $saved]);

            if (! $saved) {
                return back()->with('error', 'Gagal menyimpan jadwal.');
            }
        } catch (\Throwable $e) {
            Log::error('Jadwal update error', ['id' => $jadwal->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat menyimpan jadwal.');
        }

        return redirect()->route('staff.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);
        $jadwal->delete();
        return redirect()->route('staff.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
