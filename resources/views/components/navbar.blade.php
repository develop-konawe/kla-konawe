<div x-data="{ open: false }" class="fixed w-full top-0 z-50 px-4 sm:px-6 lg:px-8 pt-4 pb-2 transition-all duration-300">
    <nav class="max-w-7xl mx-auto glass rounded-2xl lg:rounded-full shadow-lg border border-white/40">
        <div class="px-5 lg:px-6 h-16 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex-shrink-0 flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-md group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="font-extrabold text-xl text-primary-800 hidden sm:block tracking-tight">KLA Konawe</span>
                </a>
            </div>
            
            <!-- Desktop Nav -->
            <div class="hidden lg:flex items-center space-x-1">
                <a href="/" class="text-gray-700 hover:text-primary-600 hover:bg-white/60 px-4 py-2 rounded-full font-bold transition text-sm">Beranda</a>
                <a href="/tentang-kla" class="text-gray-700 hover:text-primary-600 hover:bg-white/60 px-4 py-2 rounded-full font-bold transition text-sm">Tentang KLA</a>
                <a href="/layanan" class="text-gray-700 hover:text-primary-600 hover:bg-white/60 px-4 py-2 rounded-full font-bold transition text-sm">Layanan</a>
                <a href="/fasilitas" class="text-gray-700 hover:text-primary-600 hover:bg-white/60 px-4 py-2 rounded-full font-bold transition text-sm">Fasilitas</a>
                <a href="/berita" class="text-gray-700 hover:text-primary-600 hover:bg-white/60 px-4 py-2 rounded-full font-bold transition text-sm">Berita</a>
                <a href="/publikasi" class="text-gray-700 hover:text-primary-600 hover:bg-white/60 px-4 py-2 rounded-full font-bold transition text-sm">Publikasi</a>
            </div>
            
            <!-- Desktop CTA -->
            <div class="hidden lg:flex items-center space-x-3">
                <a href="/pengaduan" class="bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 px-5 py-2.5 rounded-full font-bold shadow-md hover-lift flex items-center space-x-1 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Lapor</span>
                </a>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="open = !open" class="lg:hidden inline-flex items-center justify-center p-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/60 focus:outline-none transition" aria-label="Buka menu navigasi">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="lg:hidden border-t border-white/30 pb-4 px-4">
            <div class="pt-3 pb-2 space-y-1">
                <a href="/" class="block text-gray-700 hover:text-primary-600 hover:bg-white/50 px-4 py-3 rounded-xl font-bold transition">Beranda</a>
                <a href="/tentang-kla" class="block text-gray-700 hover:text-primary-600 hover:bg-white/50 px-4 py-3 rounded-xl font-bold transition">Tentang KLA</a>
                <a href="/layanan" class="block text-gray-700 hover:text-primary-600 hover:bg-white/50 px-4 py-3 rounded-xl font-bold transition">Layanan</a>
                <a href="/fasilitas" class="block text-gray-700 hover:text-primary-600 hover:bg-white/50 px-4 py-3 rounded-xl font-bold transition">Fasilitas</a>
                <a href="/berita" class="block text-gray-700 hover:text-primary-600 hover:bg-white/50 px-4 py-3 rounded-xl font-bold transition">Berita</a>
                <a href="/publikasi" class="block text-gray-700 hover:text-primary-600 hover:bg-white/50 px-4 py-3 rounded-xl font-bold transition">Publikasi</a>
            </div>
            <div class="pt-3 border-t border-white/20 space-y-2">
                <a href="/pengaduan" class="block text-center bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-3 rounded-xl font-bold shadow-md">Lapor & Pengaduan</a>
            </div>
        </div>
    </nav>
</div>
