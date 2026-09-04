<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
                    ->where('status', 'published')
                    ->first();

        // If no published page found, show a default informational page
        if (!$page) {
            $page = new Page([
                'title' => $this->getDefaultTitle($slug),
                'excerpt' => $this->getDefaultExcerpt($slug),
                'content' => $this->getDefaultContent($slug),
            ]);
        }

        return view('pages.show', compact('page'));
    }

    private function getDefaultTitle(string $slug): string
    {
        return match($slug) {
            'tentang-kla' => 'Tentang Kabupaten Layak Anak (KLA)',
            default => ucwords(str_replace('-', ' ', $slug)),
        };
    }

    private function getDefaultExcerpt(string $slug): string
    {
        return match($slug) {
            'tentang-kla' => 'Mengenal lebih dekat program Kabupaten Layak Anak (KLA) di Kabupaten Konawe, Sulawesi Tenggara.',
            default => '',
        };
    }

    private function getDefaultContent(string $slug): string
    {
        return match($slug) {
            'tentang-kla' => '
<div class="not-prose space-y-12 font-sans">
    <!-- Intro -->
    <div class="bg-primary-50 rounded-3xl p-8 md:p-10 border border-primary-100 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
        <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-6 flex items-center relative z-10">
            <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            Apa itu Kabupaten Layak Anak?
        </h2>
        <p class="text-gray-700 leading-relaxed text-lg relative z-10 font-medium">
            Kabupaten/Kota Layak Anak (KLA) adalah kabupaten/kota yang mempunyai sistem pembangunan berbasis hak anak melalui pengintegrasian komitmen dan sumber daya pemerintah, masyarakat, dan dunia usaha, yang terencana secara menyeluruh dan berkelanjutan dalam kebijakan, program, dan kegiatan untuk menjamin terpenuhinya hak dan perlindungan anak.
        </p>
    </div>

    <!-- Visi & Misi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Visi -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-gray-200/40 border border-gray-100 relative overflow-hidden group hover:border-primary-300 transition-all duration-300 hover:-translate-y-1 flex flex-col">
            <h2 class="text-2xl font-black text-gray-900 mb-5 flex items-center">
                <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
                Visi
            </h2>
            <div class="bg-primary-50/50 rounded-2xl p-6 flex-grow border border-primary-100/50">
                <p class="text-primary-900 leading-relaxed text-xl font-bold italic">
                    "Mewujudkan Kabupaten Konawe sebagai daerah yang ramah anak, di mana setiap anak tumbuh dengan sehat, cerdas, ceria, dan terlindungi."
                </p>
            </div>
        </div>

        <!-- Misi -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-gray-200/40 border border-gray-100 relative overflow-hidden group hover:border-secondary-300 transition-all duration-300 hover:-translate-y-1 flex flex-col">
            <h2 class="text-2xl font-black text-gray-900 mb-5 flex items-center">
                <div class="w-12 h-12 rounded-2xl bg-secondary-50 text-secondary-600 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                Misi
            </h2>
            <ul class="space-y-4 text-gray-700 font-medium bg-secondary-50/30 rounded-2xl p-6 border border-secondary-100/30 flex-grow">
                <li class="flex items-start">
                    <div class="w-6 h-6 rounded-full bg-secondary-100 text-secondary-600 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span>Menjamin pemenuhan hak sipil dan kebebasan anak</span>
                </li>
                <li class="flex items-start">
                    <div class="w-6 h-6 rounded-full bg-secondary-100 text-secondary-600 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span>Mewujudkan lingkungan keluarga dan pengasuhan alternatif yang aman</span>
                </li>
                <li class="flex items-start">
                    <div class="w-6 h-6 rounded-full bg-secondary-100 text-secondary-600 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span>Menjamin akses kesehatan dasar dan kesejahteraan anak</span>
                </li>
                <li class="flex items-start">
                    <div class="w-6 h-6 rounded-full bg-secondary-100 text-secondary-600 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span>Menjamin akses pendidikan, pemanfaatan waktu luang, dan kegiatan budaya</span>
                </li>
                <li class="flex items-start">
                    <div class="w-6 h-6 rounded-full bg-secondary-100 text-secondary-600 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span>Memberikan perlindungan khusus kepada anak</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Klaster Hak Anak -->
    <div class="mt-12 pt-8 border-t border-gray-100">
        <h2 class="text-3xl font-black text-gray-900 mb-3 text-center">5 Klaster Hak Anak</h2>
        <p class="text-gray-500 mb-10 text-lg text-center max-w-2xl mx-auto font-medium">Program KLA dibangun berdasarkan 5 pilar utama perlindungan hak anak yang saling berkesinambungan.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Klaster 1 -->
            <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-14 h-14 text-white rounded-2xl flex items-center justify-center font-black text-2xl mb-5 group-hover:scale-110 transition-transform" style="background: linear-gradient(135deg, #60a5fa, #2563eb); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);">1</div>
                <h3 class="text-lg font-black text-gray-900 mb-2">Hak Sipil dan Kebebasan</h3>
                <p class="text-gray-600 text-sm leading-relaxed font-medium">Setiap anak berhak memiliki identitas (akte kelahiran), kebebasan berpendapat, dan berekspresi.</p>
            </div>
            
            <!-- Klaster 2 -->
            <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-14 h-14 text-white rounded-2xl flex items-center justify-center font-black text-2xl mb-5 group-hover:scale-110 transition-transform" style="background: linear-gradient(135deg, #4ade80, #16a34a); box-shadow: 0 10px 15px -3px rgba(34, 197, 94, 0.3);">2</div>
                <h3 class="text-lg font-black text-gray-900 mb-2">Lingkungan Keluarga & Pengasuhan</h3>
                <p class="text-gray-600 text-sm leading-relaxed font-medium">Anak berhak mendapatkan pengasuhan yang layak, aman, dan penuh kasih sayang dalam lingkungan yang kondusif.</p>
            </div>
            
            <!-- Klaster 3 -->
            <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-14 h-14 text-white rounded-2xl flex items-center justify-center font-black text-2xl mb-5 group-hover:scale-110 transition-transform" style="background: linear-gradient(135deg, #f87171, #dc2626); box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);">3</div>
                <h3 class="text-lg font-black text-gray-900 mb-2">Kesehatan Dasar & Kesejahteraan</h3>
                <p class="text-gray-600 text-sm leading-relaxed font-medium">Anak berhak mendapatkan akses layanan kesehatan memadai, gizi yang baik, dan lingkungan yang sehat.</p>
            </div>
            
            <!-- Klaster 4 -->
            <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group md:col-span-1 lg:col-span-1">
                <div class="w-14 h-14 text-white rounded-2xl flex items-center justify-center font-black text-2xl mb-5 group-hover:scale-110 transition-transform" style="background: linear-gradient(135deg, #c084fc, #9333ea); box-shadow: 0 10px 15px -3px rgba(168, 85, 247, 0.3);">4</div>
                <h3 class="text-lg font-black text-gray-900 mb-2">Pendidikan & Waktu Luang</h3>
                <p class="text-gray-600 text-sm leading-relaxed font-medium">Anak berhak mendapatkan pendidikan yang berkualitas, waktu bermain, rekreasi, dan kegiatan seni budaya.</p>
            </div>
            
            <!-- Klaster 5 -->
            <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group md:col-span-2 lg:col-span-2">
                <div class="w-14 h-14 text-white rounded-2xl flex items-center justify-center font-black text-2xl mb-5 group-hover:scale-110 transition-transform" style="background: linear-gradient(135deg, #fb923c, #ea580c); box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.3);">5</div>
                <h3 class="text-lg font-black text-gray-900 mb-2">Perlindungan Khusus</h3>
                <p class="text-gray-600 text-sm leading-relaxed font-medium">Anak berhak mendapat perlindungan dari segala bentuk kekerasan, eksploitasi, perlakuan salah, dan diskriminasi di berbagai situasi dan kondisi.</p>
            </div>
        </div>
    </div>
</div>',
            default => '<p>Konten untuk halaman ini sedang disiapkan. Silakan kunjungi kembali nanti.</p>',
        };
    }
}

