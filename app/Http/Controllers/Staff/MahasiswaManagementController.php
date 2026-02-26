<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaManagementController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::orderBy('id', 'asc')->paginate(20);
        return view('Staff.mahasiswa.index', compact('mahasiswa'));
    }

    public function create()
    {
        return view('Staff.mahasiswa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mahasiswa,email',
            'nim' => 'nullable|string|unique:mahasiswa,nim',
            'prodi' => 'nullable|string|max:150',
            'password' => 'nullable|string|min:6',
        ]);

        $password = $validated['password'] ?? 'password';

        Mahasiswa::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $validated['nim'] ?? null,
            'prodi' => $validated['prodi'] ?? null,
            'password' => Hash::make($password),
        ]);

        return redirect()->route('staff.mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $m = Mahasiswa::findOrFail($id);
        return view('Staff.mahasiswa.edit', compact('m'));
    }

    public function update(Request $request, $id)
    {
        $m = Mahasiswa::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mahasiswa,email,'.$m->id,
            'nim' => 'nullable|string|unique:mahasiswa,nim,'.$m->id,
            'prodi' => 'nullable|string|max:150',
            'password' => 'nullable|string|min:6',
        ]);

        $m->name = $validated['name'];
        $m->email = $validated['email'];
        $m->nim = $validated['nim'] ?? null;
        $m->prodi = $validated['prodi'] ?? null;
        if (!empty($validated['password'])) {
            $m->password = Hash::make($validated['password']);
        }
        $m->save();

        return redirect()->route('staff.mahasiswa.index')->with('success', 'Mahasiswa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $m = Mahasiswa::findOrFail($id);
        $m->delete();
        return redirect()->route('staff.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus');
    }

    public function resetPassword($id)
    {
        $m = Mahasiswa::findOrFail($id);
        $default = 'password';
        $m->password = Hash::make($default);
        $m->save();
        return redirect()->route('staff.mahasiswa.index')->with('success', "Password mahasiswa direset ke: $default");
    }
}
