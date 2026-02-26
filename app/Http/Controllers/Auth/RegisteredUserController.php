<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Staff;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:staff,dosen,mahasiswa'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nip' => ['nullable', 'string', 'max:50'],
            'nim' => ['nullable', 'string', 'max:50'],
            'prodi' => ['nullable', 'string', 'max:150'],
        ], [
            'role.required' => 'Pilih role terlebih dahulu',
            'role.in' => 'Role tidak valid',
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        $role = $validated['role'];
        $hashedPassword = Hash::make($validated['password']);

        // Register berdasarkan role
        if ($role === 'staff') {
            // Cek email sudah ada atau belum di tabel staff
            if (Staff::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'Email sudah terdaftar untuk Staff']);
            }

            $user = Staff::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $hashedPassword,
                'role' => 'staff',
            ]);

            Auth::guard('staff')->login($user);
            return redirect()->intended(route('staff.dashboard'));
        } elseif ($role === 'dosen') {
            // Cek email sudah ada atau belum di tabel dosen
            if (Dosen::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'Email sudah terdaftar untuk Dosen']);
            }

            $user = Dosen::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $hashedPassword,
                'nip' => $validated['nip'] ?? null,
            ]);

            Auth::guard('dosen')->login($user);
            return redirect()->intended(route('dosen.dashboard'));
        } elseif ($role === 'mahasiswa') {
            // Cek email sudah ada atau belum di tabel mahasiswa
            if (Mahasiswa::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'Email sudah terdaftar untuk Mahasiswa']);
            }

            $user = Mahasiswa::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $hashedPassword,
                'nim' => $validated['nim'] ?? null,
                'prodi' => $validated['prodi'] ?? null,
            ]);

            Auth::guard('mahasiswa')->login($user);
            return redirect()->intended(route('mahasiswa.dashboard'));
        }

        return redirect(route('index', absolute: false));
    }
}
