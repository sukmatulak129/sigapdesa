<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIGAP DESA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-block p-4 bg-blue-100 rounded-full mb-4">
                    <i class="fas fa-map-marked-alt text-blue-600 text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">SIGAP DESA</h1>
                <p class="text-gray-600 mt-2">Sistem Informasi Geografis & Aspirasi Pembangunan Desa</p>
            </div>

            <!-- Login Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-blue-800 text-sm">
                            Untuk akses aplikasi, login dengan akun Google Anda. 
                            Sistem akan secara otomatis mendaftarkan Anda sebagai warga.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Login Button -->
            <div class="space-y-4">
                <a href="{{ route('google.login') }}" 
                class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition duration-150 ease-in-out">
                    <i class="fab fa-google mr-3"></i>
                    Login dengan Google
                </a>

                <a href="{{ route('admin.login') }}" 
                class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out">
                    <i class="fas fa-lock mr-3"></i>
                    Login sebagai Admin
                </a>
            </div>   

            <!-- Demo Credentials -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Credentials untuk Demo:</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-user-shield text-gray-400 mr-2"></i>
                        <span class="text-gray-600">Admin:</span>
                        <span class="font-mono ml-2">admin@desa.local / password123</span>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-8">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Fitur Utama:</h3>
                <ul class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                    <li class="flex items-center">
                        <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                        Peta Interaktif
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-ticket-alt text-blue-500 mr-2"></i>
                        Sistem Tiket
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-chart-pie text-purple-500 mr-2"></i>
                        Transparansi Anggaran
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-robot text-yellow-500 mr-2"></i>
                        Chatbot AI
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-white text-sm opacity-90">
                © 2024 SIGAP DESA. Platform Layanan Desa Terpadu.
            </p>
        </div>
    </div>
</body>
</html>