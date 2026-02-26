<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DosenManagementController extends Controller
{
    public function index()
    {
        $dosen = Dosen::orderBy('id', 'asc')->paginate(20);
        return view('Staff.dosen.index', compact('dosen'));
    }

    public function create()
    {
        return view('Staff.dosen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dosen,email',
            'nip' => 'nullable|string|unique:dosen,nip',
            'password' => 'nullable|string|min:6',
        ]);

        $password = $validated['password'] ?? 'password';

        Dosen::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => $validated['nip'] ?? null,
            'password' => Hash::make($password),
        ]);

        return redirect()->route('staff.dosen.index')->with('success', 'Dosen berhasil ditambahkan');
    }

    public function edit($id)
    {
        $d = Dosen::findOrFail($id);
        return view('Staff.dosen.edit', compact('d'));
    }

    public function update(Request $request, $id)
    {
        $d = Dosen::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dosen,email,'.$d->id,
            'nip' => 'nullable|string|unique:dosen,nip,'.$d->id,
            'password' => 'nullable|string|min:6',
        ]);

        $d->name = $validated['name'];
        $d->email = $validated['email'];
        $d->nip = $validated['nip'] ?? null;
        if (!empty($validated['password'])) {
            $d->password = Hash::make($validated['password']);
        }
        $d->save();

        return redirect()->route('staff.dosen.index')->with('success', 'Dosen berhasil diperbarui');
    }

    public function destroy($id)
    {
        $d = Dosen::findOrFail($id);
        $d->delete();
        return redirect()->route('staff.dosen.index')->with('success', 'Dosen berhasil dihapus');
    }

    public function resetPassword($id)
    {
        $d = Dosen::findOrFail($id);
        $default = 'password';
        $d->password = Hash::make($default);
        $d->save();
        return redirect()->route('staff.dosen.index')->with('success', "Password dosen direset ke: $default");
    }
}
