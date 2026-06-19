<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    private $keywords = [
        'lokasi' => ['lokasi', 'alamat', 'dimana', 'kantor', 'tempat', 'posisi', 'letak'],
        'jam' => ['jam', 'buka', 'tutup', 'waktu', 'operasional', 'pelayanan', 'pukul'],
        'lapor' => ['lapor', 'laporkan', 'masalah', 'kerusakan', 'buat laporan', 'laporan', 'report'],
        'status' => ['status', 'cek', 'tiket', 'progres', 'perkembangan', 'tracking'],
        'kk' => ['kk', 'kartu keluarga', 'keluarga', 'kartu_keluarga'],
        'ktp' => ['ktp', 'kartu penduduk', 'identitas', 'e-ktp', 'kartu_tanda_penduduk'],
        'surat' => ['surat', 'pengantar', 'keterangan', 'domisili', 'skck', 'surat_miskin'],
        'jalan' => ['jalan', 'rusak', 'lubang', 'aspal', 'berlubang', 'jalanan'],
        'sampah' => ['sampah', 'kotor', 'bersih', 'kebersihan', 'sampah_liar', 'tumpukan'],
        'drainase' => ['drainase', 'parit', 'air', 'banjir', 'genangan', 'saluran'],
        'anggaran' => ['anggaran', 'biaya', 'dana', 'uang', 'transparansi', 'keuangan'],
        'admin' => ['admin', 'petugas', 'staff', 'pegawai', 'kontak'],
        'telepon' => ['telepon', 'telpon', 'hp', 'whatsapp', 'wa', 'kontak'],
        'syarat' => ['syarat', 'persyaratan', 'dokumen', 'berkas', 'kelengkapan'],
        'prosedur' => ['prosedur', 'cara', 'tahapan', 'langkah', 'proses'],
        'biaya' => ['biaya', 'gratis', 'bayar', 'pungutan', 'tarif'],
    ];

    public function index()
    {
        // Get user-specific chat history
        $chatHistory = [];
        if (Auth::check()) {
            $sessionId = 'user_' . Auth::id() . '_' . date('Ymd');
            $chatHistory = ChatLog::where('session_id', $sessionId)
                ->orderBy('created_at')
                ->limit(20)
                ->get();
        }
        
        return view('chatbot.index', compact('chatHistory'));
    }

    public function handleMessage(Request $request)
    {
        $message = strtolower(trim($request->input('message')));
        
        // Create session ID based on user and date
        if (Auth::check()) {
            $sessionId = 'user_' . Auth::id() . '_' . date('Ymd');
        } else {
            $sessionId = 'guest_' . Str::uuid();
        }
        
        // Simpan pesan user
        ChatLog::create([
            'session_id' => $sessionId,
            'message' => $message,
            'sender' => 'user',
            'user_id' => Auth::check() ? Auth::id() : null
        ]);

        // Cari jawaban dengan konteks user role
        $response = $this->generateResponse($message, Auth::user());

        // Simpan response bot
        ChatLog::create([
            'session_id' => $sessionId,
            'message' => $response['message'],
            'sender' => 'bot',
            'metadata' => $response['metadata']
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'response' => $response,
            'user_role' => Auth::check() ? Auth::user()->role : 'guest'
        ]);
    }

    private function generateResponse($message, $user = null)
    {
        // Cek FAQ database
        $faq = Faq::where('aktif', true)
            ->where(function($query) use ($message) {
                $query->where('pertanyaan', 'like', "%{$message}%")
                      ->orWhere('jawaban', 'like', "%{$message}%");
            })
            ->orderBy('prioritas', 'desc')
            ->first();

        if ($faq) {
            return [
                'message' => $this->formatResponse($faq->jawaban),
                'metadata' => ['type' => 'faq', 'faq_id' => $faq->id]
            ];
        }

        // Advanced keyword matching
        $matchedKeywords = $this->findKeywords($message);
        
        if (!empty($matchedKeywords)) {
            return $this->getKeywordResponse($matchedKeywords, $message, $user);
        }

        // Default response based on user role
        $defaultResponse = $user && $user->isAdmin() 
            ? "Halo Admin! Saya Chatbot SIGAP DESA. Anda dapat menanyakan tentang: lokasi kantor, jam buka, cara verifikasi laporan, atau kelola anggaran."
            : "Halo! Saya Chatbot SIGAP DESA. Saya bisa membantu Anda dengan informasi layanan desa. Silakan tanyakan tentang: lokasi kantor, jam buka, cara membuat laporan, atau syarat administrasi.";

        return [
            'message' => $this->formatResponse($defaultResponse),
            'metadata' => ['type' => 'welcome']
        ];
    }

    private function findKeywords($message)
    {
        $foundKeywords = [];
        
        foreach ($this->keywords as $key => $patterns) {
            foreach ($patterns as $pattern) {
                // Check for whole word match
                if (preg_match('/\b' . preg_quote($pattern, '/') . '\b/i', $message)) {
                    $foundKeywords[$key] = $pattern;
                    break;
                }
            }
        }
        
        return $foundKeywords;
    }

    private function getKeywordResponse($keywords, $originalMessage, $user = null)
    {
        // Prioritize certain keywords
        $priorityKeywords = ['lokasi', 'jam', 'lapor', 'status', 'telepon'];
        
        foreach ($priorityKeywords as $priority) {
            if (isset($keywords[$priority])) {
                return $this->getSpecificResponse($priority, $originalMessage, $user);
            }
        }
        
        // Return response for first found keyword
        $firstKey = array_key_first($keywords);
        return $this->getSpecificResponse($firstKey, $originalMessage, $user);
    }

    private function getSpecificResponse($keyword, $message, $user = null)
    {
        $isAdmin = $user && $user->isAdmin();
        
        $responses = [
            'lokasi' => [
                'title' => '📍 Lokasi Kantor Desa',
                'content' => "Kantor Desa SIGAP berada di:\n\n**Alamat:**\nJl. Desa Maju No. 123\nRT 01/RW 01\nKecamatan Sejahtera\n\n**Koordinat:**\n-6.208763, 106.845599\n\n**Google Maps:**\n[Klik di sini untuk buka Google Maps](https://maps.google.com/?q=-6.208763,106.845599)\n\n**Jam Kunjung:**\nSenin-Jumat: 08:00-15:00",
                'link' => 'https://maps.google.com/?q=-6.208763,106.845599'
            ],
            'jam' => [
                'title' => '🕐 Jam Operasional Kantor Desa',
                'content' => "**Jam Pelayanan Publik:**\n\n📅 Senin - Kamis:\n08:00 - 15:00 WIB\n\n📅 Jumat:\n08:00 - 11:30 WIB\n\n📅 Sabtu:\n08:00 - 12:00 WIB\n\n⏸️ **Istirahat:**\n12:00 - 13:00 WIB\n\n💡 **Catatan:**\n- Pelayanan administrasi hanya sampai 30 menit sebelum tutup\n- Hari Minggu & libur nasional tutup",
                'link' => null
            ],
            'lapor' => [
                'title' => $isAdmin ? '📝 Kelola Laporan' : '📝 Cara Membuat Laporan',
                'content' => $isAdmin 
                    ? "**Sebagai Admin, Anda dapat:**\n\n1. **Verifikasi** laporan masuk\n2. **Update status** laporan\n3. **Input biaya** saat selesai\n4. **Pantau progres** di peta\n\n**Menu Admin:**\n- [Kelola Laporan]({{ route('laporan.index') }})\n- [Lihat Peta]({{ route('peta') }})\n- [Dashboard]({{ route('dashboard') }})"
                    : "**Langkah-langkah membuat laporan:**\n\n1. **Login** ke akun SIGAP DESA Anda\n2. **Buka menu** 'Buat Laporan'\n3. **Klik lokasi** di peta tempat kejadian\n4. **Isi formulir** dengan data lengkap\n5. **Kirim laporan**\n\n**Hasil:**\n✅ Mendapat nomor tiket\n✅ Dapat dipantau statusnya\n\n[Klik di sini untuk buat laporan sekarang]({{ route('laporan.create') }})",
                'link' => $isAdmin ? route('laporan.index') : route('laporan.create')
            ],
            'status' => [
                'title' => '📊 Cek Status Laporan',
                'content' => "**Cara mengecek status laporan:**\n\n1. **Login** ke akun Anda\n2. **Buka menu** 'Laporan Saya'\n3. **Lihat daftar** laporan yang Anda buat\n\n**Status yang tersedia:**\n\n🔴 **Menunggu** - Belum diverifikasi\n🟡 **Dikerjakan** - Sedang dalam proses\n🟢 **Selesai** - Telah diselesaikan\n\n[Klik di sini untuk cek status laporan]({{ route('laporan.saya') }})",
                'link' => route('laporan.saya')
            ],
            'anggaran' => [
                'title' => $isAdmin ? '💰 Kelola Anggaran' : '💰 Transparansi Anggaran',
                'content' => $isAdmin
                    ? "**Sebagai Admin, Anda dapat:**\n\n1. **Buat program** anggaran baru\n2. **Kelola realisasi** anggaran\n3. **Pantau penggunaan** dana\n4. **Export laporan** keuangan\n\n**Menu Anggaran:**\n- [Kelola Anggaran]({{ route('anggaran.index') }})\n- [Buat Program Baru]({{ route('anggaran.create') }})\n- [Dashboard Anggaran]({{ route('anggaran.dashboard') }})"
                    : "**Informasi Anggaran Desa:**\n\n📊 **Total Anggaran Tahun Ini:** Rp 500.000.000\n🏗️ **Untuk Perbaikan Infrastruktur:** 60%\n👥 **Program Sosial:** 25%\n🏛️ **Administrasi:** 15%\n\n[Klik di sini untuk detail lengkap]({{ route('transparansi.index') }})",
                'link' => $isAdmin ? route('anggaran.index') : route('transparansi.index')
            ],
            'telepon' => [
                'title' => '📞 Kontak & Hubungan',
                'content' => "**Kontak Resmi Desa SIGAP:**\n\n📱 **WhatsApp Admin:** 0812-3456-7890\n📞 **Telepon Kantor:** (021) 1234567\n📧 **Email:** admin@sigapdesa.id\n\n**Jam Respons:**\nSenin-Jumat: 08:00-15:00\n\n**Untuk urgent:**\nSilakan hubungi WhatsApp untuk respon cepat",
                'link' => 'https://wa.me/6281234567890'
            ],
        ];

        $response = $responses[$keyword] ?? [
            'title' => 'ℹ️ Informasi',
            'content' => 'Silakan hubungi admin untuk informasi lebih lanjut.',
            'link' => null
        ];

        $formattedMessage = "**{$response['title']}**\n\n{$response['content']}";
        
        if ($response['link']) {
            $formattedMessage .= "\n\n🔗 [Buka Halaman](" . $response['link'] . ")";
        }

        return [
            'message' => $this->formatResponse($formattedMessage),
            'metadata' => [
                'type' => 'keyword',
                'keyword' => $keyword,
                'link' => $response['link']
            ]
        ];
    }

    private function formatResponse($text)
    {
        // Format newlines and basic markdown
        $text = str_replace('\n', "\n", $text);
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" class="text-blue-600 hover:underline" target="_blank">$1</a>', $text);
        
        return nl2br($text);
    }

    public function getHistory($sessionId)
    {
        $history = ChatLog::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->get();

        return response()->json($history);
    }
}