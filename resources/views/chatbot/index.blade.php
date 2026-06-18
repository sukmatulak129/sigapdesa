@extends('layouts.app')

@section('title', 'Chatbot SIGAP DESA')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Chatbot SIGAP DESA</h1>
        <p class="text-gray-600">Tanya apa saja tentang layanan desa 24/7</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chat Container -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg">
                <!-- Chat Header -->
                <div class="bg-blue-600 text-white p-6 rounded-t-lg">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-robot text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">SIGAP Chatbot</h2>
                            <p class="text-blue-200">Siap membantu Anda 24 jam</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div id="chat-container" class="h-[500px] overflow-y-auto p-6 space-y-4">
                    <!-- Initial message -->
                    <div id="initial-message" class="space-y-4">
                        <div class="flex justify-start">
                            <div class="max-w-[80%]">
                                <div class="bg-blue-100 text-blue-800 p-4 rounded-lg rounded-tl-none">
                                    <strong>Chatbot:</strong> Halo! Saya Chatbot SIGAP DESA. 
                                    Saya bisa membantu Anda dengan informasi layanan desa, 
                                    panduan membuat laporan, dan lainnya. Silakan tanyakan apa yang Anda butuhkan!
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Sekarang</p>
                            </div>
                        </div>

                        <!-- Quick Questions -->
                        <div class="mt-6">
                            <p class="text-sm text-gray-600 mb-3">Pertanyaan cepat:</p>
                            <div class="flex flex-wrap gap-2">
                                <button onclick="quickQuestion('Jam buka kantor desa?')" 
                                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm hover:bg-blue-200">
                                    <i class="far fa-clock mr-2"></i>Jam buka kantor
                                </button>
                                <button onclick="quickQuestion('Bagaimana cara membuat laporan?')" 
                                        class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm hover:bg-green-200">
                                    <i class="fas fa-file-alt mr-2"></i>Cara lapor
                                </button>
                                <button onclick="quickQuestion('Syarat pengurusan KTP?')" 
                                        class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full text-sm hover:bg-yellow-200">
                                    <i class="fas fa-id-card mr-2"></i>Syarat KTP
                                </button>
                                <button onclick="quickQuestion('Lokasi kantor desa?')" 
                                        class="px-4 py-2 bg-purple-100 text-purple-700 rounded-full text-sm hover:bg-purple-200">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Lokasi kantor
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages will be appended here -->
                </div>

                <!-- Chat Input -->
                <div class="border-t p-6">
                    <div class="flex">
                        <input type="text" 
                               id="chat-input" 
                               placeholder="Ketik pertanyaan Anda..."
                               class="flex-1 border border-gray-300 rounded-l-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               onkeypress="if(event.key === 'Enter') sendMessage()">
                        <button onclick="sendMessage()" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-r-lg hover:bg-blue-700">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    
                    <!-- Typing indicator -->
                    <div id="typing-indicator" class="hidden mt-2">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-robot text-gray-500"></i>
                            </div>
                            <div class="text-gray-500 italic">Chatbot sedang mengetik...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- FAQ Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-lg mb-4">FAQ Populer</h3>
                <div class="space-y-3">
                    <button onclick="quickQuestion('Jam buka kantor desa?')" 
                            class="w-full text-left p-3 hover:bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="far fa-clock text-blue-500 mr-3"></i>
                            <div>
                                <p class="font-medium">Jam buka kantor</p>
                                <p class="text-sm text-gray-500">Info jam pelayanan</p>
                            </div>
                        </div>
                    </button>
                    
                    <button onclick="quickQuestion('Syarat pengurusan KTP?')" 
                            class="w-full text-left p-3 hover:bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-id-card text-green-500 mr-3"></i>
                            <div>
                                <p class="font-medium">Syarat KTP</p>
                                <p class="text-sm text-gray-500">Dokumen yang dibutuhkan</p>
                            </div>
                        </div>
                    </button>
                    
                    <button onclick="quickQuestion('Bagaimana cara membuat laporan?')" 
                            class="w-full text-left p-3 hover:bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-yellow-500 mr-3"></i>
                            <div>
                                <p class="font-medium">Cara membuat laporan</p>
                                <p class="text-sm text-gray-500">Panduan lengkap</p>
                            </div>
                        </div>
                    </button>
                    
                    <button onclick="quickQuestion('Lokasi kantor desa?')" 
                            class="w-full text-left p-3 hover:bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-purple-500 mr-3"></i>
                            <div>
                                <p class="font-medium">Lokasi kantor</p>
                                <p class="text-sm text-gray-500">Alamat dan peta</p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4 text-blue-800">Butuh Bantuan?</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-blue-700 mb-2">Chatbot tidak bisa menjawab?</p>
                        <a href="https://wa.me/6281234567890" 
                           target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fab fa-whatsapp mr-2"></i> WhatsApp Admin
                        </a>
                    </div>
                    
                    <div class="pt-4 border-t border-blue-200">
                        <p class="text-sm text-blue-700 mb-2">Langsung buat laporan?</p>
                        <a href="{{ route('laporan.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i> Buat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let chatSessionId = null;
    let chatHistory = [];

    // Initialize chat
    document.addEventListener('DOMContentLoaded', function() {
        loadChatHistory();
    });

    function loadChatHistory() {
        // Load from localStorage if exists
        const savedHistory = localStorage.getItem('chatHistory');
        if (savedHistory) {
            chatHistory = JSON.parse(savedHistory);
            displayChatHistory();
        }
    }

    function displayChatHistory() {
        const container = document.getElementById('chat-container');
        container.innerHTML = '';
        
        chatHistory.forEach(msg => {
            const messageDiv = document.createElement('div');
            messageDiv.className = msg.sender === 'user' ? 'flex justify-end' : 'flex justify-start';
            
            const bubbleClass = msg.sender === 'user' 
                ? 'bg-gray-100 text-gray-800 p-4 rounded-lg rounded-br-none max-w-[80%]'
                : 'bg-blue-100 text-blue-800 p-4 rounded-lg rounded-tl-none max-w-[80%]';
            
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

    function quickQuestion(question) {
        document.getElementById('chat-input').value = question;
        sendMessage();
    }

    function showTyping() {
        document.getElementById('typing-indicator').classList.remove('hidden');
        document.getElementById('chat-container').scrollTop = document.getElementById('chat-container').scrollHeight;
    }

    function hideTyping() {
        document.getElementById('typing-indicator').classList.add('hidden');
    }

    function sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        
        if (!message) return;
        
        // Add user message to history
        const userMsg = {
            sender: 'user',
            message: message,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        };
        
        chatHistory.push(userMsg);
        displayChatHistory();
        
        // Clear input
        input.value = '';
        
        // Show typing indicator
        showTyping();
        
        // Send to server
        fetch('{{ route("chatbot.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: message,
                session_id: chatSessionId
            })
        })
        .then(response => response.json())
        .then(data => {
            hideTyping();
            
            chatSessionId = data.session_id;
            
            // Add bot response to history
            const botMsg = {
                sender: 'bot',
                message: data.response.message,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            };
            
            chatHistory.push(botMsg);
            displayChatHistory();
            
            // Save to localStorage
            localStorage.setItem('chatHistory', JSON.stringify(chatHistory));
            
            // Check if response contains link to auto-navigate
            if (data.response.metadata && data.response.metadata.type === 'link') {
                setTimeout(() => {
                    if (confirm('Chatbot memberikan link. Mau buka sekarang?')) {
                        window.location.href = data.response.metadata.url;
                    }
                }, 500);
            }
        })
        .catch(error => {
            hideTyping();
            console.error('Error:', error);
            
            // Fallback response
            const fallbackMsg = {
                sender: 'bot',
                message: 'Maaf, terjadi kesalahan. Silakan coba lagi atau hubungi admin.',
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            };
            
            chatHistory.push(fallbackMsg);
            displayChatHistory();
        });
    }

    // Clear chat history
    function clearChat() {
        if (confirm('Hapus riwayat percakapan?')) {
            chatHistory = [];
            localStorage.removeItem('chatHistory');
            displayChatHistory();
            
            // Show initial message again
            document.getElementById('initial-message').classList.remove('hidden');
        }
    }
</script>
@endpush
@endsection