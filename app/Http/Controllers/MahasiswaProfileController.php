<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;

class MahasiswaProfileController extends Controller
{
    /**
     * Show edit profile form
     */
    public function edit()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        return view('Mahasiswa.profile.edit', compact('mahasiswa'));
    }

    /**
     * Update profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        /** @var Mahasiswa $mahasiswa */
        $mahasiswa = Auth::guard('mahasiswa')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama harus diisi',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        $mahasiswa->name = $validated['name'];

        if (!empty($validated['password'])) {
            $mahasiswa->password = Hash::make($validated['password']);
        }

        $mahasiswa->save();

        return redirect()->route('mahasiswa.profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}
