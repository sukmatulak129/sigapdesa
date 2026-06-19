<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        try {
            Log::info('Google OAuth Redirect Attempt');
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Google Redirect Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'message' => 'Gagal mengarahkan ke Google. Periksa konfigurasi OAuth.'
            ]);
        }
    }

    public function callback()
    {
        try {
            Log::info('Google OAuth Callback Started');
            
            $googleUser = Socialite::driver('google')->user();
            Log::info('Google User Data: ', [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName()
            ]);
            
            // Cari user berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                // User baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(uniqid()),
                    'role' => 'warga'
                ]);
                Log::info('New user created: ' . $googleUser->getEmail());
            } else {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar()
                ]);
                Log::info('Existing user updated: ' . $user->email);
            }

            Auth::login($user, true);
            Log::info('User logged in successfully: ' . $user->email);

            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
            
        } catch (\Exception $e) {
            Log::error('Google OAuth Callback Error: ' . $e->getMessage());
            Log::error('Error Trace: ' . $e->getTraceAsString());
            
            return redirect()->route('login')->withErrors([
                'message' => 'Login dengan Google gagal. Error: ' . $e->getMessage()
            ]);
        }
    }
}