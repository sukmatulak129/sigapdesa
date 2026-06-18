#!/bin/bash

echo "🔧 Memperbaiki Google OAuth Error..."

echo "📦 Install Socialite..."
composer require laravel/socialite

echo "🗑️  Clear cache..."
php artisan config:clear
php artisan cache:clear

echo "📁 Buat view setup..."
mkdir -p resources/views/auth

echo "🔗 Tambahkan route setup..."
if ! grep -q "setup-google-oauth" routes/web.php; then
    sed -i "/use Illuminate\\Support\\Facades\\Route;/a\\\n// Setup Google OAuth Page\nRoute::get('/setup-google-oauth', function () {\n    return view('auth.google-setup');\n})->name('google.setup');" routes/web.php
fi

echo "🌐 Periksa .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

echo "🔍 Cek Google OAuth config..."
if ! grep -q "GOOGLE_CLIENT_ID" .env; then
    echo "" >> .env
    echo "# Google OAuth" >> .env
    echo "GOOGLE_CLIENT_ID=" >> .env
    echo "GOOGLE_CLIENT_SECRET=" >> .env
    echo "GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback" >> .env
fi

echo "✅ Setup selesai!"
echo ""
echo "📋 Langkah selanjutnya:"
echo "1. Buka: http://localhost:8000/setup-google-oauth"
echo "2. Ikuti panduan setup di browser"
echo "3. Dapatkan Client ID & Secret dari Google Cloud Console"
echo "4. Tambahkan ke file .env"
echo "5. Test: http://localhost:8000"