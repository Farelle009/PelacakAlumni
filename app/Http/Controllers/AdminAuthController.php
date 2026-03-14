<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    // -------------------------------------------------------------------------
    // Sign In
    // -------------------------------------------------------------------------

    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    // -------------------------------------------------------------------------
    // Sign Up
    // -------------------------------------------------------------------------

    public function showRegister(): View
    {
        return view('admin.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:admin,username'],
            'email'    => ['required', 'email', 'max:255', 'unique:admin,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $admin = Admin::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('admin')->login($admin);

        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat. Selamat datang, ' . $admin->username . '!');
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // -------------------------------------------------------------------------
    // Edit Profile
    // -------------------------------------------------------------------------

    public function showProfile(): View
    {
        return view('admin.profile', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:admin,username,' . $admin->id],
            'email'    => ['required', 'email', 'max:255', 'unique:admin,email,' . $admin->id],
        ]);

        $admin->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (! Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}