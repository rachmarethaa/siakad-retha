<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MultiRoleLoginController extends Controller
{
    /**
     * Handle multi-role authentication
     */
    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|in:staff,dosen,mahasiswa',
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'role.required' => 'Pilih role terlebih dahulu',
            'role.in' => 'Role tidak valid',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        $guard = $validated['role'];

        // First try the normal attempt (covers most cases)
        $attemptResult = Auth::guard($guard)->attempt($credentials);
        Log::info("MultiRoleLogin: attempt for guard={$guard} email={$validated['email']} result=" . ($attemptResult ? 'success' : 'failed'));

        if ($attemptResult) {
            $request->session()->regenerate();
            Log::info("MultiRoleLogin: login successful via attempt for {$validated['email']} on guard {$guard}");

            return match ($guard) {
                'staff' => redirect()->intended(route('staff.dashboard')),
                'dosen' => redirect()->intended(route('dosen.dashboard')),
                'mahasiswa' => redirect()->intended(route('mahasiswa.dashboard')),
                default => redirect()->intended(route('dashboard')),
            };
        }

        // If attempt failed, try to provide clearer diagnostics:
        // Resolve provider's model for this guard and check password manually.
        try {
            $provider = config("auth.guards.{$guard}.provider");
            $modelClass = config("auth.providers.{$provider}.model");

            if ($modelClass) {
                $user = $modelClass::where('email', $validated['email'])->first();
                Log::info("MultiRoleLogin: provider={$provider} model={$modelClass} user_found=" . ($user ? 'yes' : 'no'));

                if ($user) {
                    // Check hashed password
                    $check = Hash::check($validated['password'], $user->password);
                    Log::info("MultiRoleLogin: Hash::check for {$validated['email']} => " . ($check ? 'ok' : 'mismatch'));

                    if ($check) {
                        // Log the user in via the guard
                        Auth::guard($guard)->login($user);
                        $request->session()->regenerate();
                        Log::info("MultiRoleLogin: login successful via manual login for {$validated['email']} on guard {$guard}");

                        return match ($guard) {
                            'staff' => redirect()->intended(route('staff.dashboard')),
                            'dosen' => redirect()->intended(route('dosen.dashboard')),
                            'mahasiswa' => redirect()->intended(route('mahasiswa.dashboard')),
                            default => redirect()->intended(route('dashboard')),
                        };
                    }

                    // Password doesn't match
                    return back()
                        ->withInput($request->only('email', 'role'))
                        ->withErrors([
                            'email' => 'Password salah untuk akun ini',
                        ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('MultiRoleLoginController authenticate error: ' . $e->getMessage());
        }

        // Generic login failed fallback
        return back()
            ->withInput($request->only('email', 'role'))
            ->withErrors([
                'email' => 'Email atau password tidak sesuai untuk role yang dipilih',
            ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // Try to logout from all guards
        Auth::guard('web')->logout();
        Auth::guard('staff')->logout();
        Auth::guard('dosen')->logout();
        Auth::guard('mahasiswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
