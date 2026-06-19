<?php

namespace App\Services;

use App\Models\Faq;
use Illuminate\Support\Str;

class ChatbotService
{
    public function processMessage($message, $userId = null)
    {
        $message = strtolower(trim($message));
        $sessionId = Str::uuid();
        
        // 1. Cek FAQ terlebih dahulu
        $faqResponse = $this->checkFAQ($message);
        if ($faqResponse) {
            return [
                'response' => $faqResponse['jawaban'],
                'type' => 'faq',
                'faq_id' => $faqResponse['id'],
                'session_id' => $sessionId
            ];
        }
        
        // 2. Cek kata kunci khusus
        $keywordResponse = $this->checkKeywords($message);
        if ($keywordResponse) {
            return [
                'response' => $keywordResponse,
                'type' => 'keyword',
                'session_id' => $sessionId
            ];
        }
        
        // 3. Default response
        return [
            'response' => $this->getDefaultResponse($message),
            'type' => 'default',
            'session_id' => $sessionId
        ];
    }
    
    private function checkFAQ($message)
    {
        $faqs = Faq::where('is_active', true)->get();
        
        foreach ($faqs as $faq) {
            // Cek berdasarkan keyword
            if ($faq->keywords) {
                $keywords = explode(',', $faq->keywords);
                foreach ($keywords as $keyword) {
                    if (strpos($message, trim($keyword)) !== false) {
                        $faq->increment('hit_count');
                        return $faq;
                    }
                }
            }
            
            // Cek similarity dengan pertanyaan
            similar_text(strtolower($faq->pertanyaan), $message, $percent);
            if ($percent > 60) {
                $faq->increment('hit_count');
                return $faq;
            }
        }
        
        return null;
    }
    
    private function checkKeywords($message)
    {
        $keywords = [
            'halo' => 'Halo! Saya Chatbot Sigap Desa. Ada yang bisa saya bantu?',
            'hai' => 'Hai! Selamat datang di Sigap Desa. Silakan tanyakan apa yang Anda butuhkan.',
            'pagi' => 'Pagi! Semoga hari Anda menyenangkan. Ada yang bisa saya bantu?',
            'siang' => 'Siang! Ada yang bisa saya bantu hari ini?',
            'malam' => 'Malam! Saya Chatbot Sigap Desa, siap membantu Anda 24 jam.',
            'terima kasih' => 'Sama-sama! Senang bisa membantu. Jika ada pertanyaan lain, silakan tanyakan.',
            'makasih' => 'Sama-sama! 😊',
            'bye' => 'Sampai jumpa! Jangan ragu untuk kembali jika butuh bantuan.',
            'selamat tinggal' => 'Sampai jumpa! Terima kasih telah menggunakan Sigap Desa.',
            
            // Fitur spesifik
            'lapor' => 'Untuk membuat laporan, silakan klik menu "Buat Laporan" atau kunjungi: ' . route('laporan.create'),
            'jalan rusak' => 'Untuk melaporkan jalan rusak: 1. Klik "Buat Laporan", 2. Pilih kategori "Jalan Rusak", 3. Isi formulir. ' . route('laporan.create'),
            'sampah' => 'Untuk laporan sampah: Pilih kategori "Sampah Liar" saat membuat laporan. ' . route('laporan.create'),
            'status laporan' => 'Untuk cek status laporan: Login -> Dashboard -> Daftar Laporan. Atau ' . route('dashboard'),
            'peta' => 'Anda bisa melihat peta laporan di: ' . route('peta'),
            'admin' => 'Untuk masalah khusus, hubungi admin di: admin@sigapdesa.test atau WhatsApp: 081234567890',
            'bantuan' => 'Saya bisa membantu dengan: 1. Info layanan desa, 2. Cara lapor, 3. Cek FAQ, 4. Arahkan ke menu. Apa yang Anda butuhkan?',
            
            // Layanan desa
            'ktp' => 'Info KTP: Syarat: Surat pengantar RT/RW, FC KK, FC akta, foto 3x4. Biaya gratis.',
            'kk' => 'Info KK: Syarat: Surat pengantar, FC KTP, FC akta/surat nikah. Proses 3-5 hari.',
            'surat' => 'Untuk pengurusan surat: 1. Datang ke kantor desa, 2. Bawa persyaratan, 3. Isi formulir.',
            'izin' => 'Izin usaha: Hubungi kantor desa dengan membawa: 1. KTP, 2. KK, 3. Proposal usaha.',
        ];
        
        foreach ($keywords as $keyword => $response) {
            if (strpos($message, $keyword) !== false) {
                return $response;
            }
        }
        
        return null;
    }
    
    private function getDefaultResponse($message)
    {
        $defaultResponses = [
            "Maaf, saya belum memahami pertanyaan Anda. Coba tanyakan tentang: laporan jalan rusak, syarat KTP/KK, jam buka kantor, atau status laporan.",
            "Saya Chatbot Sigap Desa. Saya bisa membantu dengan informasi layanan desa. Coba tanyakan hal yang lebih spesifik.",
            "Untuk pertanyaan kompleks, silakan hubungi admin kami di WhatsApp: 081234567890",
            "Coba gunakan kata kunci seperti: 'lapor', 'jalan rusak', 'KTP', 'KK', 'jam buka', atau 'status' untuk informasi lebih jelas.",
        ];
        
        return $defaultResponses[array_rand($defaultResponses)];
    }
    
    public function getSuggestions()
    {
        return [
            ['text' => '📋 Cara Buat Laporan', 'query' => 'cara lapor'],
            ['text' => '🕒 Jam Buka Kantor', 'query' => 'jam buka'],
            ['text' => '📍 Lokasi Kantor', 'query' => 'lokasi kantor'],
            ['text' => '📄 Syarat KTP', 'query' => 'syarat ktp'],
            ['text' => '🗺️ Lihat Peta', 'action' => 'open_map'],
        ];
    }
}