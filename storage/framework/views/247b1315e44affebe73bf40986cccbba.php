<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title'); ?> - SIGAP DESA</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php echo $__env->yieldPushContent('styles'); ?>
    <style>
        .sidebar {
            transition: all 0.3s ease;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-content {
            padding-bottom: 240px; /* Memberi ruang untuk bottom section */
        }
        
        .sidebar-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1e3a8a;
            border-top: 1px solid #1e40af;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .sidebar-content {
                padding-bottom: 280px;
            }
        }
        
        .map-container {
            height: 500px;
            z-index: 1;
        }
        
        .chatbot-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-menunggu { background-color: #e5e7eb; color: #374151; }
        .status-diterima { background-color: #fca5a5; color: #7f1d1d; }
        .status-ditolak { background-color: #d1d5db; color: #374151; }
        .status-dikerjakan { background-color: #fcd34d; color: #92400e; }
        .status-selesai { background-color: #86efac; color: #166534; }
        
        /* Custom scrollbar untuk sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #1e3a8a;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #1e40af;
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #3b82f6;
        }
        
        /* Main content adjustment */
        main {
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-blue-900 text-white">
        <div class="sidebar-content">
            <div class="p-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-map-marked-alt text-2xl"></i>
                    <h1 class="text-xl font-bold">SIGAP DESA</h1>
                </div>
                <p class="text-blue-200 text-sm mt-2">Sistem Informasi Geografis & Aspirasi Pembangunan Desa</p>
            </div>
            
            <nav class="mt-8">
                <a href="<?php echo e(route('dashboard')); ?>" class="block py-3 px-6 hover:bg-blue-800 <?php echo e(request()->routeIs('dashboard') ? 'bg-blue-800' : ''); ?> relative">
                    <i class="fas fa-home mr-3"></i> Dashboard
                    <?php
                        $pendingCount = \App\Models\Laporan::where('status', 'menunggu')->count();
                    ?>
                    <?php if($pendingCount > 0 && auth()->user()->isAdmin()): ?>
                    <span class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
                        <?php echo e($pendingCount); ?>

                    </span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('peta')); ?>" class="block py-3 px-6 hover:bg-blue-800 <?php echo e(request()->routeIs('peta') ? 'bg-blue-800' : ''); ?>">
                    <i class="fas fa-map mr-3"></i> Peta Interaktif
                </a>
                
                <?php if(!auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('laporan.create')); ?>" class="block py-3 px-6 hover:bg-blue-800 <?php echo e(request()->routeIs('laporan.create') ? 'bg-blue-800' : ''); ?>">
                    <i class="fas fa-plus-circle mr-3"></i> Buat Laporan
                </a>
                <?php endif; ?>
                
                <a href="<?php echo e(route('laporan.saya')); ?>" class="block py-3 px-6 hover:bg-blue-800 <?php echo e(request()->routeIs('laporan.saya') ? 'bg-blue-800' : ''); ?>">
                    <i class="fas fa-history mr-3"></i> <?php echo e(auth()->user()->isAdmin() ? 'Riwayat Laporan' : 'Laporan Saya'); ?>

                </a>
                
                <?php if(auth()->user()->isAdmin()): ?>
    <a href="<?php echo e(route('laporan.index')); ?>" class="block py-3 px-6 hover:bg-blue-800 <?php echo e(request()->routeIs('laporan.index') ? 'bg-blue-800' : ''); ?>">
        <i class="fas fa-tasks mr-3"></i> Kelola Laporan
    </a>
    <a href="<?php echo e(route('faq.index')); ?>" class="block py-3 px-6 hover:bg-blue-800 <?php echo e(request()->routeIs('faq.*') ? 'bg-blue-800' : ''); ?>">
        <i class="fas fa-question-circle mr-3"></i> FAQ
    </a>
    <a href="<?php echo e(route('anggaran.index')); ?>" class="block py-3 px-6 hover:bg-blue-800 <?php echo e(request()->routeIs('anggaran.*') ? 'bg-blue-800' : ''); ?>">
        <i class="fas fa-coins mr-3"></i> Anggaran
    </a>
    <?php endif; ?>
                
                <a href="<?php echo e(route('transparansi.index')); ?>" class="block py-3 px-6 hover:bg-blue-800 <?php echo e(request()->routeIs('transparansi.*') ? 'bg-blue-800' : ''); ?>">
                    <i class="fas fa-chart-pie mr-3"></i> Transparansi
                </a>
                <a href="<?php echo e(route('chatbot.index')); ?>" class="block py-3 px-6 hover:bg-blue-800 <?php echo e(request()->routeIs('chatbot.index') ? 'bg-blue-800' : ''); ?>">
                    <i class="fas fa-robot mr-3"></i> Chatbot
                </a>
            </nav>
        </div>
        
        <!-- Bottom Section dengan fixed positioning -->
        <div class="sidebar-bottom p-4">
            <div class="flex items-center">
                <img src="<?php echo e(auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=1e40af&color=ffffff'); ?>" 
                     class="w-10 h-10 rounded-full border-2 border-blue-700" alt="Avatar">
                <div class="ml-3">
                    <p class="font-medium"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-sm text-blue-300">
                        <?php echo e(auth()->user()->role == 'admin' ? 'Administrator' : 'Warga'); ?>

                        <?php if(auth()->user()->isAdmin()): ?>
                        <span class="ml-2 px-2 py-1 bg-yellow-600 text-white text-xs rounded-full">Admin</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <form action="<?php echo e(auth()->user()->role == 'admin' ? route('admin.logout') : route('logout')); ?>" 
                  method="POST" class="mt-4">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 rounded-lg flex items-center justify-center transition duration-150">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile Header -->
    <div class="md:hidden fixed top-0 left-0 right-0 bg-blue-900 text-white p-4 z-40 shadow-lg">
        <div class="flex justify-between items-center">
            <button onclick="toggleSidebar()" class="text-2xl">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="text-lg font-bold">SIGAP DESA</h1>
            <?php if(auth()->user()->isAdmin()): ?>
            <?php
                $pendingCount = \App\Models\Laporan::where('status', 'menunggu')->count();
            ?>
            <?php if($pendingCount > 0): ?>
            <div class="relative">
                <i class="fas fa-bell"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    <?php echo e($pendingCount); ?>

                </span>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <main class="md:ml-64 pt-16 md:pt-0 min-h-screen">
        <!-- Flash Messages -->
        <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mx-4 mt-4 md:mx-6" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mx-4 mt-4 md:mx-6" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="container mx-auto px-4 py-6">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- Chatbot Widget -->
<div class="chatbot-widget">
    <button onclick="toggleChatbot()" class="bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition duration-150 relative">
        <i class="fas fa-robot text-2xl"></i>
        <div id="chat-notification" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden"></div>
    </button>
    <div id="chatbot-container" class="hidden absolute bottom-20 right-0 w-80 md:w-96 bg-white rounded-lg shadow-xl border border-gray-200" style="z-index: 1001;">
        <!-- Chatbot header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg flex justify-between items-center">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-robot text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold">SIGAP Chatbot</h3>
                    <p class="text-sm text-blue-200">
                        <?php if(auth()->guard()->check()): ?>
                            <?php echo e(auth()->user()->isAdmin() ? 'Mode Admin' : 'Mode Warga'); ?>

                        <?php else: ?>
                            Mode Tamu
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="clearChatHistory()" class="text-blue-200 hover:text-white" title="Clear chat">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button onclick="toggleChatbot()" class="text-blue-200 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Chat messages -->
        <div id="chat-messages" class="h-80 overflow-y-auto p-4 space-y-3 custom-scrollbar">
            <!-- Initial message will be loaded here -->
        </div>
        
        <!-- Quick actions -->
        <div id="quick-actions" class="px-4 pb-2">
            <div class="flex flex-wrap gap-2">
                <button onclick="quickQuestion('Jam buka kantor?')" 
                        class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full hover:bg-blue-200">
                    Jam Buka
                </button>
                <button onclick="quickQuestion('Lokasi kantor?')" 
                        class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full hover:bg-purple-200">
                    Lokasi
                </button>
                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->isAdmin()): ?>
                    <button onclick="quickQuestion('Cara verifikasi laporan?')" 
                            class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full hover:bg-green-200">
                        Verifikasi
                    </button>
                    <?php else: ?>
                    <button onclick="quickQuestion('Cara membuat laporan?')" 
                            class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full hover:bg-green-200">
                        Buat Laporan
                    </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Chat input -->
        <div class="border-t p-4">
            <div class="flex">
                <input type="text" id="chat-input" placeholder="Ketik pertanyaan..." 
                       class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       onkeypress="if(event.key === 'Enter') sendMessage()">
                <button onclick="sendMessage()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700 transition duration-150">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div id="typing-indicator" class="hidden mt-2 text-sm text-gray-500">
                <i class="fas fa-circle animate-pulse text-blue-500 mr-2"></i>Chatbot sedang mengetik...
            </div>
        </div>
    </div>
</div>
            
            <!-- Quick actions -->
            <div id="quick-actions" class="px-4 pb-2">
                <div class="flex flex-wrap gap-2">
                    <button onclick="quickQuestion('Jam buka kantor?')" 
                            class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full hover:bg-blue-200">
                        Jam Buka
                    </button>
                    <button onclick="quickQuestion('Cara membuat laporan?')" 
                            class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full hover:bg-green-200">
                        Cara Lapor
                    </button>
                    <button onclick="quickQuestion('Lokasi kantor?')" 
                            class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full hover:bg-purple-200">
                        Lokasi
                    </button>
                    <button onclick="quickQuestion('Syarat KTP?')" 
                            class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full hover:bg-yellow-200">
                        Syarat KTP
                    </button>
                </div>
            </div>
            
            <!-- Chat input -->
            <div class="border-t p-4">
                <div class="flex">
                    <input type="text" id="chat-input" placeholder="Ketik pertanyaan..." 
                           class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           onkeypress="if(event.key === 'Enter') sendMessage()">
                    <button onclick="sendMessage()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700 transition duration-150">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div id="typing-indicator" class="hidden mt-2 text-sm text-gray-500">
                    <i class="fas fa-circle animate-pulse text-blue-500 mr-2"></i>Chatbot sedang mengetik...
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Sidebar toggle for mobile
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
        
        // Chatbot functionality
        let chatSessionId = null;
        let chatMessages = [];
        let unreadMessages = 0;
        
        // Load chat history from localStorage
        function loadChatHistory() {
        const userId = <?php echo e(auth()->check() ? auth()->id() : 'null'); ?>;
        const storageKey = userId ? `sigap_chat_history_${userId}` : 'sigap_chat_history_guest';
        
        const savedHistory = localStorage.getItem(storageKey);
        if (savedHistory) {
            try {
                chatMessages = JSON.parse(savedHistory);
                displayChatHistory();
            } catch (e) {
                console.error('Error loading chat history:', e);
                chatMessages = [];
            }
        } else {
            // Show initial welcome message based on user role
            const isAdmin = <?php echo e(auth()->check() && auth()->user()->isAdmin() ? 'true' : 'false'); ?>;
            const welcomeMessage = isAdmin 
                ? 'Halo Admin! Saya Chatbot SIGAP DESA. Anda dapat menanyakan tentang: lokasi kantor, jam buka, cara verifikasi laporan, atau kelola anggaran.'
                : 'Halo! Saya Chatbot SIGAP DESA. Saya bisa membantu Anda dengan informasi layanan desa. Silakan tanyakan tentang: lokasi kantor, jam buka, cara membuat laporan, atau syarat administrasi.';
            
            const initialMessage = `
                <div class="flex justify-start">
                    <div class="max-w-[80%]">
                        <div class="bg-blue-50 text-blue-800 p-3 rounded-lg rounded-tl-none">
                            <strong>Chatbot:</strong> ${welcomeMessage}
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Baru saja</p>
                    </div>
                </div>
            `;
            document.getElementById('chat-messages').innerHTML = initialMessage;
        }
        }
        
        function displayChatHistory() {
            const container = document.getElementById('chat-messages');
            if (!container) return;
            
            container.innerHTML = '';
            
            // Always show initial message
            const initialMsg = `
                <div class="flex justify-start">
                    <div class="max-w-[80%]">
                        <div class="bg-blue-50 text-blue-800 p-3 rounded-lg rounded-tl-none">
                            <strong>Chatbot:</strong> Halo! Saya Chatbot SIGAP DESA. 
                            Saya bisa membantu Anda dengan informasi layanan desa. 
                            Silakan tanyakan apa yang Anda butuhkan!
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Baru saja</p>
                    </div>
                </div>
            `;
            container.innerHTML = initialMsg;
            
            // Display saved messages
            chatMessages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = msg.sender === 'user' ? 'flex justify-end mt-3' : 'flex justify-start mt-3';
                
                const bubbleClass = msg.sender === 'user' 
                    ? 'bg-gray-100 text-gray-800 p-3 rounded-lg rounded-br-none max-w-[80%]'
                    : 'bg-blue-50 text-blue-800 p-3 rounded-lg rounded-tl-none max-w-[80%]';
                
                messageDiv.innerHTML = `
                    <div class="max-w-[80%]">
                        <div class="${bubbleClass}">
                            <strong>${msg.sender === 'user' ? 'Anda' : 'Chatbot'}:</strong> ${msg.message}
                        </div>
                        <p class="text-xs text-gray-500 mt-1">${msg.time || 'Baru saja'}</p>
                    </div>
                `;
                
                container.appendChild(messageDiv);
            });
            
            container.scrollTop = container.scrollHeight;
        }

        function saveChatToStorage() {
            const userId = <?php echo e(auth()->check() ? auth()->id() : 'null'); ?>;
            const storageKey = userId ? `sigap_chat_history_${userId}` : 'sigap_chat_history_guest';
            localStorage.setItem(storageKey, JSON.stringify(chatMessages));
}        
        
        function toggleChatbot() {
            const container = document.getElementById('chatbot-container');
            container.classList.toggle('hidden');
            
            if (!container.classList.contains('hidden')) {
                loadChatHistory();
                // Reset notification when chatbot is opened
                unreadMessages = 0;
                document.getElementById('chat-notification').classList.add('hidden');
            }
        }
        
        function quickQuestion(question) {
            document.getElementById('chat-input').value = question;
            sendMessage();
        }
        
        function showTypingIndicator() {
            document.getElementById('typing-indicator').classList.remove('hidden');
            const container = document.getElementById('chat-messages');
            container.scrollTop = container.scrollHeight;
        }
        
        function hideTypingIndicator() {
            document.getElementById('typing-indicator').classList.add('hidden');
        }
        
        function addMessageToDisplay(sender, message) {
            const container = document.getElementById('chat-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = sender === 'user' ? 'flex justify-end mt-3' : 'flex justify-start mt-3';
            
            const bubbleClass = sender === 'user' 
                ? 'bg-gray-100 text-gray-800 p-3 rounded-lg rounded-br-none max-w-[80%]'
                : 'bg-blue-50 text-blue-800 p-3 rounded-lg rounded-tl-none max-w-[80%]';
            
            const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            messageDiv.innerHTML = `
                <div class="max-w-[80%]">
                    <div class="${bubbleClass}">
                        <strong>${sender === 'user' ? 'Anda' : 'Chatbot'}:</strong> ${message}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">${time}</p>
                </div>
            `;
            
            container.appendChild(messageDiv);
            container.scrollTop = container.scrollHeight;
            
            // Save to array
            chatMessages.push({
                sender: sender,
                message: message,
                time: time
            });
            
            // Save to localStorage
            saveChatToStorage();
        }
        
        function sendMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            
            if (!message) return;
            
            // Add user message to display
            addMessageToDisplay('user', message);
            
            // Clear input
            input.value = '';
            
            // Show typing indicator
            showTypingIndicator();
            
            // Send to server
            $.ajax({
                url: '<?php echo e(route("chatbot.send")); ?>',
                method: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    message: message,
                    session_id: chatSessionId
                },
                success: function(response) {
                    hideTypingIndicator();
                    
                    chatSessionId = response.session_id;
                    
                    // Add bot response
                    addMessageToDisplay('bot', response.response.message);
                    
                    // Check if chatbot is closed, show notification
                    const chatbotContainer = document.getElementById('chatbot-container');
                    if (chatbotContainer.classList.contains('hidden')) {
                        unreadMessages++;
                        const notificationBadge = document.getElementById('chat-notification');
                        notificationBadge.textContent = unreadMessages;
                        notificationBadge.classList.remove('hidden');
                    }
                },
                error: function(xhr, status, error) {
                    hideTypingIndicator();
                    console.error('Chatbot error:', error);
                    
                    // Add error message
                    addMessageToDisplay('bot', 'Maaf, terjadi kesalahan. Silakan coba lagi atau hubungi admin.');
                }
            });
        }
        
        function clearChatHistory() {
            if (confirm('Hapus semua riwayat percakapan?')) {
                chatMessages = [];
                localStorage.removeItem('sigap_chat_history');
                displayChatHistory();
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const sidebar = document.querySelector('.sidebar');
                const mobileMenuButton = document.querySelector('[onclick="toggleSidebar()"]');
                
                if (window.innerWidth <= 768 && 
                    sidebar.classList.contains('active') &&
                    !sidebar.contains(event.target) &&
                    !mobileMenuButton.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            });
            
            // Load chat history
            loadChatHistory();
            
            // Auto-focus chat input when chatbot is opened
            const chatInput = document.getElementById('chat-input');
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (!mutation.target.classList.contains('hidden')) {
                        setTimeout(() => chatInput.focus(), 100);
                    }
                });
            });
            
            observer.observe(document.getElementById('chatbot-container'), {
                attributes: true,
                attributeFilter: ['class']
            });
        });
        
        // Enter key to send message
        document.getElementById('chat-input')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH /home/naufal/sigap-desa/resources/views/layouts/app.blade.php ENDPATH**/ ?>