<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Dosen;

class DosenProfileController extends Controller
{
    /**
     * Show edit profile form
     */
    public function edit()
    {
        $dosen = Auth::guard('dosen')->user();
        return view('Dosen.profile.edit', compact('dosen'));
    }

    /**
     * Update profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        /** @var Dosen $dosen */
        $dosen = Auth::guard('dosen')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama harus diisi',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        $dosen->name = $validated['name'];

        if (!empty($validated['password'])) {
            $dosen->password = Hash::make($validated['password']);
        }

        $dosen->save();

        return redirect()->route('dosen.profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}
