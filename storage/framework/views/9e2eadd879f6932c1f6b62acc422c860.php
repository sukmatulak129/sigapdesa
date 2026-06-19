<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SIGAP DESA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            min-height: 100vh;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <div class="login-card rounded-2xl shadow-2xl p-8 glow">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-block p-4 bg-blue-100 rounded-full mb-4">
                    <i class="fas fa-user-shield text-blue-600 text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Admin SIGAP DESA</h1>
                <p class="text-gray-600 mt-2">Login untuk akses panel administrator</p>
            </div>

            <!-- Login Form -->
            <form action="<?php echo e(route('admin.login.submit')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <?php if(session('error')): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <p class="text-red-700"><?php echo e(session('error')); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-red-700">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center">
                            <i class="fas fa-times-circle text-red-500 mr-2 text-xs"></i>
                            <?php echo e($error); ?>

                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Username/Email -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Username atau Email
                    </label>
                    <input type="text" 
                           name="email" 
                           value="<?php echo e(old('email')); ?>"
                           placeholder="admin@desa.local atau admin"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required autofocus>
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="password"
                               placeholder="Masukkan password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        <button type="button" 
                                onclick="togglePassword()"
                                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="mb-6 flex items-center">
                    <input type="checkbox" 
                           name="remember" 
                           id="remember"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Ingat saya
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login sebagai Admin
                </button>
            </form>

            <!-- Demo Credentials -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-key mr-2"></i>Credentials Demo:
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-user-shield text-gray-400 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Admin</p>
                            <div class="mt-1 space-y-1">
                                <p class="text-gray-600">Email: <code class="bg-gray-100 px-2 py-1 rounded">admin@desa.local</code></p>
                                <p class="text-gray-600">Password: <code class="bg-gray-100 px-2 py-1 rounded">password123</code></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links -->
            <div class="mt-6 space-y-3">
                <a href="<?php echo e(route('login')); ?>" 
                   class="block text-center text-blue-600 hover:text-blue-800 hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Login Umum
                </a>
                
                <a href="<?php echo e(route('google.setup')); ?>" 
                   class="block text-center text-gray-600 hover:text-gray-800 hover:underline text-sm">
                    <i class="fas fa-cog mr-2"></i>Setup Google OAuth
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Area terbatas untuk administrator desa
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = passwordInput.nextElementSibling.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Auto-focus on first input
        document.querySelector('input[name="email"]').focus();
    </script>
</body>
</html><?php /**PATH /home/naufal/sigap-desa/resources/views/auth/admin-login.blade.php ENDPATH**/ ?>