<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check if user exists with email or username
        $user = User::where('email', $request->email)
            ->orWhere(function($query) use ($request) {
                // If email field contains username
                $query->where('name', $request->email);
            })
            ->first();

        if (!$user) {
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Akun tidak ditemukan.'])
                ->withInput();
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->route('admin.login')
                ->withErrors(['password' => 'Password salah.'])
                ->withInput();
        }

        // Check if user has admin role
        if ($user->role !== 'admin') {
            return redirect()->route('admin.login')
                ->with('error', 'Hanya admin yang dapat login melalui halaman ini.')
                ->withInput();
        }

        // Login user
        Auth::login($user, $request->filled('remember'));

        return redirect()->route('dashboard')->with('success', 'Login admin berhasil!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Logout berhasil.');
    }
}