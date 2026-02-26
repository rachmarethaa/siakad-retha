<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\JadwalMengajar;
use App\Models\TiketMahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class StaffController extends Controller
{
    public function showLoginForm()
    {
        return view('staff.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('staff.dashboard'));
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function index()
    {
        $totalStaff = Staff::count();
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = Dosen::count();
        $totalJadwal = JadwalMengajar::count();
        $totalTiket = TiketMahasiswa::count();
        $totalTiketDibuka = TiketMahasiswa::where('status', 'dibuka')->count();
        
        $recentJadwal = JadwalMengajar::latest()->take(5)->get();
        $recentTiket = TiketMahasiswa::latest()->take(5)->get();
        
        return view('Staff.dashboard', compact('totalStaff', 'totalMahasiswa', 'totalDosen', 'totalJadwal', 'totalTiket', 'totalTiketDibuka', 'recentJadwal', 'recentTiket'));
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('staff.login');
    }
}
