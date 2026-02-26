<?php

namespace App\Http\Controllers\Tiket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TiketMahasiswa;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\TiketResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StaffTiketController extends Controller
{
    public function index()
    {
        $tikets = TiketMahasiswa::orderBy('status')->orderBy('created_at','desc')->get();
        return view('tiket.staff.index', compact('tikets'));
    }

    public function create()
    {
        $mahasiswas = \App\Models\Mahasiswa::orderBy('name')->get();
        return view('tiket.staff.create', compact('mahasiswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'judul' => 'required|string|max:191',
            'keluhan' => 'required|string',
        ]);

        $tiket = TiketMahasiswa::create([
            'mahasiswa_id' => $request->mahasiswa_id,
            'judul' => $request->judul,
            'keluhan' => $request->keluhan,
            'status' => 'dibuka',
            'sla_deadline' => Carbon::now()->addDays(7),
        ]);

        return redirect()->route('staff.tiket.index')->with('success', 'Tiket berhasil dibuat untuk mahasiswa');
    }

    public function show($id)
    {
        $tiket = TiketMahasiswa::findOrFail($id);
        $dosens = \App\Models\Dosen::orderBy('name')->get();
        // If the responses table doesn't exist yet (migration not run), avoid querying it to prevent view errors
        if (\Illuminate\Support\Facades\Schema::hasTable('tiket_responses')) {
            $tiket->load('responses');
        } else {
            $tiket->setRelation('responses', collect());
        }

        return view('tiket.staff.show', compact('tiket','dosens'));
    }

    public function assign(Request $request, $id)
    {
        $tiket = TiketMahasiswa::findOrFail($id);
        $request->validate(['staff_id' => 'nullable|exists:staff,id']);
        $tiket->assigned_staff_id = $request->staff_id;
        $tiket->status = 'diproses';
        $tiket->processed_at = Carbon::now();
        $tiket->save();
        return back()->with('success','Tiket diassign dan diproses');
    }

    public function respond(Request $request, $id)
    {
        $tiket = TiketMahasiswa::findOrFail($id);
        $request->validate(['response' => 'required|string']);
        // For simplicity we'll store response in a simple respond_at timestamp and change status
        $tiket->status = 'ditanggapi';
        $tiket->responded_at = Carbon::now();
        $tiket->save();
        // A real app would store responses in a related table; keep it simple for now.
        return back()->with('success','Tiket telah ditanggapi');
    }

    public function forwardToDosen(Request $request, $id)
    {
        $tiket = TiketMahasiswa::findOrFail($id);
        $request->validate(['dosen_id' => 'required|exists:dosen,id']);
        $tiket->assigned_dosen_id = $request->dosen_id;
        // Remember which staff forwarded this ticket so dosen can reply back to the same staff
        $staff = Auth::guard('staff')->user();
        if ($staff) {
            $tiket->assigned_staff_id = $staff->id;
        }
        // Mark as forwarded to dosen
        $tiket->status = 'diteruskan';
        $tiket->processed_at = Carbon::now();
        $tiket->save();
        Log::info('Tiket forwarded to dosen', ['tiket_id' => $tiket->id, 'dosen_id' => $request->dosen_id]);
        return back()->with('success', 'Tiket telah diteruskan ke dosen');
    }

    public function sendToMahasiswa(Request $request, $id)
    {
        $tiket = TiketMahasiswa::findOrFail($id);
        $request->validate(['message' => 'required|string']);

        $staff = Auth::guard('staff')->user();

        // create a staff response that will be delivered to mahasiswa
        if (!Schema::hasTable('tiket_responses')) {
            Log::error('TiketResponse table missing when staff tried to send to mahasiswa', ['tiket_id' => $tiket->id]);
            return back()->with('error', 'Tabel `tiket_responses` belum dibuat. Jalankan migration terlebih dahulu (php artisan migrate).');
        }

        $response = TiketResponse::create([
            'tiket_mahasiswa_id' => $tiket->id,
            'sender_role' => 'staff',
            'sender_id' => $staff->id,
            'message' => $request->message,
        ]);

        $tiket->status = 'ditanggapi';
        $tiket->responded_at = Carbon::now();
        $tiket->save();

        Log::info('Staff sent response to mahasiswa', ['tiket_id' => $tiket->id, 'staff_id' => $staff->id]);

        return back()->with('success', 'Balasan telah dikirim ke mahasiswa');
    }

    public function destroy($id)
    {
        $tiket = TiketMahasiswa::findOrFail($id);

        DB::transaction(function() use ($tiket) {
            if (Schema::hasTable('tiket_responses')) {
                TiketResponse::where('tiket_mahasiswa_id', $tiket->id)->delete();
            }
            $tiket->delete();
        });

        Log::info('Staff deleted tiket', ['tiket_id' => $id, 'staff_id' => Auth::guard('staff')->id()]);

        return redirect()->route('staff.tiket.index')->with('success', 'Tiket dan balasan terkait berhasil dihapus.');
    }
}
