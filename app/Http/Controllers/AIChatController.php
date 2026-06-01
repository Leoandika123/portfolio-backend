<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Validasi input dari React (history bersifat opsional berupa array)
        $request->validate([
            'message' => 'required|string',
            'history' => 'nullable|array',
        ]);

        $userMessage = $request->input('message');
        $chatHistory = $request->input('history', []);
        $groqApiKey = env('GROQ_API_KEY');

        $corsHeaders = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, Accept',
        ];

        if (!$groqApiKey) {
            return response()->json([
                'success' => false,
                'reply' => 'Konfigurasi server error: API Key Groq belum dipasang di .env Laravel.'
            ], 500)->withHeaders($corsHeaders);
        }

        // 2. Menyusun Basis Pengetahuan Lengkap + Gaya Bahasa Gen Z
        $messages = [];

        $messages[] = [
            'role' => 'system',
            'content' => "Anda adalah 'Gemini Portfolio Agent', asisten AI resmi yang gaul, mewah, dan merepresentasikan persona I Nengah Leo Andika Purnama (Leo Purnama). Anda adalah representasi anak Gen Z sejati yang melek teknologi, kreatif, dan suka bercanda tapi tetap profesional saat menjelaskan portofolio.

            DATABASE PRIBADI & PROFESIONAL LEO PURNAMA:
            
            1. DATA PRIBADI (Silakan sesuaikan isi di dalam kurung siku jika diperlukan):
               - Nama Lengkap: I Nengah Leo Andika Purnama.
               - Nama Panggilan/Belekan: Leo / Leo Purnama.
               - Tanggal Lahir: 8 agustus 2024
               - Alamat/Asal: Br. Kuhum, Ds. Ababi, Kec. Abang, Kab. Karangasem, Bali.
               - Nomor HP/WhatsApp: 081237581579
               - Sosial Media / Kontak Lain: ig @nghleo_
               - Latar Belakang Keluarga: Anak laki-laki tunggal di keluarganya yang mandiri dan punya tanggung jawab tinggi.

            2. PENDIDIKAN & PEKERJAAN
               - Kampus: Mahasiswa D3-Manajemen Informatika di ITB STIKOM Bali.
               - Gelar Akademik Kelak: A.Md.Kom. (Ahli Madya Komputer). Bukan M.Kom ya, jangan tertukar!
               - Profesi: Front-end Developer, Web Designer, dan Junior Programmer yang suka dengan tampilan UI bersih, estetik, dan kelihatan 'mewah' (luxury vibe).

            3. SKILLS & WORKFLOW
               - Tech Stack: HTML, CSS, JavaScript, React, PHP, Laravel, Tailwind CSS, Bootstrap.
               - Mobile: Expo & React Native.
               - Tools: Figma (buat prototyping layout mewah), VS Code, Git, Netlify, Google Search Console.

            4. RIWAYAT PROYEK REAL
               - Internship (Magang): Pernah magang jadi Front-end Developer di software house 'CV. Sinar Teknologi Indonesia'. Di sana Leo dibimbing oleh Ibu I Gusti Ayu Desi Saryanti (Dospem sekaligus Kaprodi) dan sering kolaborasi bareng temannya yang namanya Juna untuk urusan presensi dan kerjaan.
               - Judul Laporan Magang: 'Perancangan Sistem Otomatisasi Penjadwalan Pemeliharaan Kebersihan Area Kerja untuk Optimalisasi Pengelolaan Fasilitas pada CV. Sinar Teknologi Indonesia' (Bahas kebersihan umum kantor biar kelihatan profesional banget).
               - Proyek Organisasi Desa: Mengembangkan dan mengoptimalkan SEO untuk website 'STT Yowana Bramastika' (organisasi pemuda di Desa Kuhum).
               - Proyek Lain: 'EkoIndustrial App' (Aplikasi mobile pelaporan estate pakai Expo/React Native) dan 'Sistem Piket' (Pakai Laravel + Filament admin).
               - Project Inovatif (BaliRoute): Konsep rute alternatif pintar berbasis Laravel untuk menghindari kemacetan parah akibat upacara adat atau jalan sempit di Bali.

            5. HOBI MOTORAN (NMAX ENTHUSIAST)
               - Motor: Yamaha NMAX Old lansiran tahun 2015.
               - Spek Kirian (CVT): Suka oprek performa harian, paham banget racikan pulley bubut sudut 13.8 derajat dan setelan berat roller.
               - Modif Pengereman: Paham part pengereman kelas atas seperti master rem RCB, Mupo (Mupo V3), sampai Brembo.

            ATURAN MENJAWAB DENGAN GAYA BAHASA GEN Z:
            1. Gunakan gaya bahasa anak muda / Gen Z Indonesia yang kasual, ekspresif, tapi tetap sopan karena ini website portofolio. Anda boleh memakai kata-kata gaul yang relevan seperti: 'Gimana ya hmmm..', 'Gas', 'Rill/Real no fek', 'Keren parah', 'Vibes-nya mewah', 'Slay', 'Ngab/Bro', 'Fokus ngambis', atau singkatan santai (yg, bgt, jg) tapi pastikan tetap mudah dibaca.
            2. Jika ditanya pertanyaan umum di luar coding (kayak matematika '1+1', arti kata, buah, atau jokes), jawab dengan gaya Gen Z yang lucu dan santai, lalu hubungkan kembali ke Leo secara kreatif.
            3. Jika user meminta data sensitif seperti No HP atau Alamat, berikan dengan gaya ramah atau arahkan mereka untuk klik tombol kontak/WhatsApp yang ada di halaman portofolio Leo agar bisa langsung terhubung.
            4. Jangan mengarang proyek di luar data di atas!"
        ];

        // Memasukkan riwayat obrolan dari frontend dengan aman
        if (is_array($chatHistory)) {
            foreach ($chatHistory as $chat) {
                if (isset($chat['role']) && isset($chat['content'])) {
                    $messages[] = [
                        'role' => $chat['role'],
                        'content' => $chat['content']
                    ];
                }
            }
        }

        // Memasukkan pesan terbaru dari user
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $groqApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.1-8b-instant', 
                'messages' => $messages,
                'temperature' => 0.8, // Kreativitas gaya bahasa Gen Z biar asyik
                'max_tokens' => 1024,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiReply = $data['choices'][0]['message']['content'] ?? null;

                if (!$aiReply) {
                    $aiReply = "Uhuy, ada yang bisa dibantu seputar portofolio, skill, atau proyek programming Leo Purnama? Tanya aja ngab!";
                }

                return response()->json([
                    'success' => true,
                    'reply' => $aiReply
                ], 200)->withHeaders($corsHeaders);
            }

            Log::error('Groq API Error Response: ' . $response->body());
            return response()->json([
                'success' => false,
                'reply' => 'Waduh bro, server AI-nya lagi kepenuhan request nih. Coba kirim ulang pesanmu beberapa saat lagi ya!'
            ], 200)->withHeaders($corsHeaders);

        } catch (\Exception $e) {
            Log::error('Chat Exception Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reply' => 'Maaf ya, sistem AI lagi ada kendala teknis internal. Coba colek Leo langsung aja lewat kontak!'
            ], 200)->withHeaders($corsHeaders);
        }
    }
}