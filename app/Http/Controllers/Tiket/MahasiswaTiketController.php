<?php

namespace App\Http\Controllers\Tiket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TiketMahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class MahasiswaTiketController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $tikets = TiketMahasiswa::where('mahasiswa_id', $mahasiswa->id)->orderBy('created_at','desc')->get();
        if (Schema::hasTable('tiket_responses')) {
            $tikets->load('responses');
        }
        return view('tiket.mahasiswa.index', compact('tikets'));
    }

    public function create()
    {
        return view('tiket.mahasiswa.create');
    }

    public function show($id)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $tiket = TiketMahasiswa::where('id', $id)->where('mahasiswa_id', $mahasiswa->id)->firstOrFail();

        if (\Illuminate\Support\Facades\Schema::hasTable('tiket_responses')) {
            $tiket->load('responses');
        } else {
            $tiket->setRelation('responses', collect());
        }

        return view('tiket.mahasiswa.show', compact('tiket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:191',
            'keluhan' => 'required|string',
        ]);

        // Log current guard and user for troubleshooting
        $guardUser = Auth::guard('mahasiswa')->user();
        Log::info('MahasiswaTiketController@store called', [
            'guard' => 'mahasiswa',
            'user_id' => $guardUser ? $guardUser->id : null,
            'user_name' => $guardUser ? $guardUser->name : null,
            'request_judul' => $request->judul,
        ]);

        if (! $guardUser) {
            Log::warning('Attempt to create tiket without authenticated mahasiswa');
            return redirect()->route('index')->with('error', 'Anda harus login sebagai mahasiswa untuk mengirim tiket');
        }

        try {
            $tiket = TiketMahasiswa::create([
                'mahasiswa_id' => $guardUser->id,
                'judul' => $request->judul,
                'keluhan' => $request->keluhan,
                'status' => 'dibuka',
                'sla_deadline' => Carbon::now()->addDays(7),
            ]);

            Log::info('Tiket created', ['tiket_id' => $tiket->id]);
            return redirect()->route('tiket.mahasiswa.index')->with('success','Tiket berhasil dibuat');
        } catch (\Throwable $e) {
            Log::error('Failed to create tiket', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat tiket. Silakan coba lagi.');
        }
    }
}
