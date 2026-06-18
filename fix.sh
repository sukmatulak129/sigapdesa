#!/bin/bash

echo "🔧 Memperbaiki semua error SIGAP DESA..."

echo "📦 Install dependencies..."
composer require laravel/socialite
composer dump-autoload

echo "🗑️  Clear cache..."
php artisan optimize:clear

echo "📁 Buat Controller base..."
cat > app/Http/Controllers/Controller.php << 'EOF'
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
EOF

echo "🔄 Perbaiki GoogleController..."
cat > app/Http/Controllers/Auth/GoogleController.php << 'EOF'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())
                ->orWhere('google_id', $googleUser->getId())
                ->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(uniqid()),
                    'role' => 'warga'
                ]);
            } else {
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar()
                    ]);
                }
            }

            Auth::login($user, true);

            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'message' => 'Login dengan Google gagal. Silakan coba lagi atau gunakan login demo.'
            ]);
        }
    }
}
EOF

echo "🗄️  Setup database..."
rm -f database/database.sqlite
touch database/database.sqlite

echo "🚀 Migrasi database..."
php artisan migrate:fresh --force

echo "🌱 Seeding data..."
php artisan db:seed --force

echo "🔗 Storage link..."
rm -f public/storage
php artisan storage:link

echo "🔑 Generate key..."
php artisan key:generate

echo "✅ Perbaikan selesai!"
echo "🚀 Jalankan: php artisan serve"
echo "🌐 Buka: http://localhost:8000"