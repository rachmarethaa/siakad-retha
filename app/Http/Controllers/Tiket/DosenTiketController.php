<?php

namespace App\Http\Controllers\Tiket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TiketMahasiswa;
use Illuminate\Support\Facades\Auth;
use App\Models\TiketResponse;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DosenTiketController extends Controller
{
    public function index()
    {
        // Dosen should see tickets assigned to them
        $dosen = Auth::guard('dosen')->user();
        $tikets = TiketMahasiswa::where('assigned_dosen_id', $dosen->id)->orderBy('created_at','desc')->get();
        return view('tiket.dosen.index', compact('tikets'));
    }

    public function show($id)
    {
        $dosen = Auth::guard('dosen')->user();
        $tiket = TiketMahasiswa::where('id', $id)->where('assigned_dosen_id', $dosen->id)->firstOrFail();
        $staffs = \App\Models\Staff::orderBy('name')->get();
        // Avoid querying responses if migration/table not present yet
        if (\Illuminate\Support\Facades\Schema::hasTable('tiket_responses')) {
            $tiket->load('responses');
        } else {
            $tiket->setRelation('responses', collect());
        }

        return view('tiket.dosen.show', compact('tiket','staffs'));
    }

    public function respond(Request $request, $id)
    {
        $dosen = Auth::guard('dosen')->user();
        $tiket = TiketMahasiswa::where('id', $id)->where('assigned_dosen_id', $dosen->id)->firstOrFail();
        $request->validate([
            'message' => 'required|string',
            'staff_id' => 'nullable|exists:staff,id'
        ]);

        // store response (only if responses table exists)
        if (!Schema::hasTable('tiket_responses')) {
            Log::error('TiketResponse table missing when dosen tried to respond', ['tiket_id' => $tiket->id]);
            return back()->with('error', 'Tabel `tiket_responses` belum dibuat. Jalankan migration terlebih dahulu (php artisan migrate).');
        }

        $response = TiketResponse::create([
            'tiket_mahasiswa_id' => $tiket->id,
            'sender_role' => 'dosen',
            'sender_id' => $dosen->id,
            'message' => $request->message,
        ]);

        // Save which staff the dosen might have selected (for record), but do NOT deliver to mahasiswa yet.
        if ($request->filled('staff_id')) {
            $tiket->assigned_staff_id = $request->staff_id;
        }

        // Do not change ticket status here; staff must manually forward the dosen reply to mahasiswa.
        $tiket->save();
        Log::info('Dosen responded to tiket (awaiting staff forwarding)', ['tiket_id' => $tiket->id, 'dosen_id' => $dosen->id]);

        return back()->with('success', 'Jawaban berhasil disimpan. Tunggu staff untuk meneruskan ke mahasiswa.');
    }
}
