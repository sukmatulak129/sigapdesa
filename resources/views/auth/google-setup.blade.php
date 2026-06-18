<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Google OAuth - SIGAP DESA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl w-full">
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-blue-100 rounded-full mb-4">
                <i class="fab fa-google text-blue-600 text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Setup Google OAuth</h1>
            <p class="text-gray-600 mt-2">Ikuti panduan ini untuk mengaktifkan login dengan Google</p>
        </div>

        <div class="space-y-6">
            <!-- Step 1 -->
            <div class="border border-gray-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        1
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Buka Google Cloud Console</h3>
                        <p class="text-gray-600 mt-2">
                            Buka <a href="https://console.cloud.google.com/" target="_blank" class="text-blue-600 hover:underline">console.cloud.google.com</a> 
                            dan login dengan akun Google Anda.
                        </p>
                        <div class="mt-4">
                            <a href="https://console.cloud.google.com/" target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fab fa-google mr-2"></i> Buka Google Cloud Console
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="border border-gray-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        2
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Buat Project Baru</h3>
                        <ul class="list-disc pl-5 text-gray-600 mt-2 space-y-2">
                            <li>Klik dropdown project di atas navbar</li>
                            <li>Pilih "NEW PROJECT"</li>
                            <li>Beri nama: <code class="bg-gray-100 px-2 py-1 rounded">SIGAP DESA</code></li>
                            <li>Klik "CREATE"</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="border border-gray-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        3
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Aktifkan Google+ API</h3>
                        <ul class="list-disc pl-5 text-gray-600 mt-2 space-y-2">
                            <li>Di sidebar, pilih "APIs & Services" > "Library"</li>
                            <li>Cari "Google+ API"</li>
                            <li>Klik dan pilih "ENABLE"</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Step 4 (PENTING) -->
            <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-yellow-600 text-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        4
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-yellow-800">Konfigurasi OAuth Consent Screen</h3>
                        <p class="text-yellow-700 mt-2 font-medium">Langkah ini sangat penting untuk menghindari error!</p>
                        <ul class="list-disc pl-5 text-yellow-700 mt-2 space-y-2">
                            <li>APIs & Services > "OAuth consent screen"</li>
                            <li>User Type: <strong>External</strong> (untuk testing)</li>
                            <li>Klik "CREATE"</li>
                        </ul>
                        
                        <div class="mt-4 p-4 bg-white rounded border border-yellow-300">
                            <h4 class="font-bold mb-2">Isi Form:</h4>
                            <ul class="space-y-2 text-sm">
                                <li><strong>App name:</strong> SIGAP DESA</li>
                                <li><strong>User support email:</strong> Email Anda</li>
                                <li><strong>Developer contact email:</strong> Email Anda</li>
                                <li><strong>App domain:</strong> localhost</li>
                                <li><strong>Homepage:</strong> http://localhost:8000</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="border border-gray-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        5
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Buat OAuth Credentials</h3>
                        <ul class="list-disc pl-5 text-gray-600 mt-2 space-y-2">
                            <li>APIs & Services > "Credentials"</li>
                            <li>Klik "CREATE CREDENTIALS" > "OAuth client ID"</li>
                            <li>Application type: <strong>Web application</strong></li>
                            <li>Name: <code class="bg-gray-100 px-2 py-1 rounded">SIGAP DESA Web Client</code></li>
                        </ul>

                        <div class="mt-4 p-4 bg-gray-50 rounded">
                            <h4 class="font-bold mb-2">Authorized JavaScript origins:</h4>
                            <code class="block bg-gray-100 p-2 rounded text-sm mb-2">http://localhost:8000</code>
                            <code class="block bg-gray-100 p-2 rounded text-sm">http://127.0.0.1:8000</code>
                            
                            <h4 class="font-bold mb-2 mt-4">Authorized redirect URIs:</h4>
                            <code class="block bg-gray-100 p-2 rounded text-sm mb-2">http://localhost:8000/auth/google/callback</code>
                            <code class="block bg-gray-100 p-2 rounded text-sm">http://127.0.0.1:8000/auth/google/callback</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="border border-green-200 bg-green-50 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        6
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-green-800">Copy Credentials ke .env</h3>
                        <p class="text-green-700 mt-2">Setelah membuat OAuth client, copy:</p>
                        
                        <div class="mt-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-green-700 mb-1">Client ID</label>
                                <input type="text" id="client-id" placeholder="123...apps.googleusercontent.com" 
                                       class="w-full border border-green-300 rounded-lg px-4 py-2 bg-white">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-green-700 mb-1">Client Secret</label>
                                <input type="text" id="client-secret" placeholder="GOCSPX-..." 
                                       class="w-full border border-green-300 rounded-lg px-4 py-2 bg-white">
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-white rounded border border-green-300">
                            <h4 class="font-bold mb-2">Tambahkan di file <code>.env</code>:</h4>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded text-sm overflow-x-auto">
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback</pre>
                            
                            <button onclick="copyEnvConfig()" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-copy mr-2"></i> Copy Konfigurasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 7 -->
            <div class="border border-gray-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        7
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Test Aplikasi</h3>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-terminal text-blue-500 mr-3"></i>
                                <div>
                                    <p class="font-medium">Clear cache Laravel:</p>
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">php artisan config:clear</code>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <i class="fas fa-play text-green-500 mr-3"></i>
                                <div>
                                    <p class="font-medium">Jalankan aplikasi:</p>
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">php artisan serve</code>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <i class="fas fa-browser text-purple-500 mr-3"></i>
                                <div>
                                    <p class="font-medium">Buka di browser:</p>
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">http://localhost:8000</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
            <a href="{{ url('/') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Login
            </a>
            
            <button onclick="testOAuth()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-vial mr-2"></i> Test Google OAuth
            </button>
        </div>

        <!-- Debug Info -->
        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-bold text-gray-700 mb-2">Debug Info:</h4>
            <div class="space-y-2 text-sm">
                <div class="flex items-center">
                    <span class="w-32 text-gray-600">Current URL:</span>
                    <code id="current-url" class="bg-gray-100 px-2 py-1 rounded"></code>
                </div>
                <div class="flex items-center">
                    <span class="w-32 text-gray-600">Google Auth URL:</span>
                    <code id="auth-url" class="bg-gray-100 px-2 py-1 rounded"></code>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Display current URLs
        document.getElementById('current-url').textContent = window.location.href;
        document.getElementById('auth-url').textContent = window.location.origin + '/auth/google';
        
        function copyEnvConfig() {
            const clientId = document.getElementById('client-id').value || 'YOUR_CLIENT_ID_HERE';
            const clientSecret = document.getElementById('client-secret').value || 'YOUR_CLIENT_SECRET_HERE';
            
            const config = `GOOGLE_CLIENT_ID=${clientId}\nGOOGLE_CLIENT_SECRET=${clientSecret}\nGOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback`;
            
            navigator.clipboard.writeText(config).then(() => {
                alert('Konfigurasi telah disalin ke clipboard!');
            });
        }
        
        function testOAuth() {
            window.open('/auth/google', '_blank');
        }
    </script>
</body>
</html>