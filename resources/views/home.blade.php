<x-layouts.app title="Beranda">
    <!-- Hero Section -->
    <section class="relative bg-mesh-gradient pt-32 pb-20 overflow-hidden">
        <div class="h-24"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center space-x-2 bg-white/70 backdrop-blur-md px-4 py-2 rounded-full mb-8 shadow-sm border border-white/50 text-primary-700 font-bold text-sm">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span>Portal Resmi Kabupaten Konawe</span>
            </div>
            
            <h1 class="text-5xl md:text-6xl font-black mb-6 tracking-tight text-gray-900 leading-tight">
                Bersama Wujudkan <br class="hidden md:block"/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-secondary-500">Kabupaten Layak Anak</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-700 mb-10 max-w-3xl mx-auto font-medium">Melindungi hak, mendengarkan suara, dan mewujudkan masa depan cerah bagi seluruh anak di Konawe.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/layanan" class="bg-primary-600 text-white hover:bg-primary-700 font-black py-4 px-8 rounded-full shadow-lg shadow-primary-500/30 hover-lift text-lg">
                    Cari Layanan Publik
                </a>
                <a href="/pengaduan" class="bg-white text-gray-900 hover:text-secondary-600 font-black py-4 px-8 rounded-full shadow-lg border border-gray-100 hover-lift text-lg flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Lapor & Pengaduan</span>
                </a>
            </div>
        </div>
        
        <!-- Bottom Wave -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none translate-y-[1px]">
            <svg class="relative block w-full h-12 md:h-24" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,117.8,200.5,107.5C241.6,101.4,282.7,85.2,321.39,56.44Z" class="fill-slate-50"></path>
            </svg>
        </div>
    </section>

    <!-- Quick Stats Section -->
    <section class="py-12 relative z-20 -mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat 1 -->
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-gray-200/50 hover-lift border border-gray-100 flex items-center space-x-6">
                    <div class="w-16 h-16 rounded-2xl bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-gray-900 mb-1">{{ $stats['services'] }}</div>
                        <div class="text-gray-500 font-bold uppercase tracking-wider text-sm">Layanan KLA</div>
                    </div>
                </div>
                <!-- Stat 2 -->
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-gray-200/50 hover-lift border border-gray-100 flex items-center space-x-6">
                    <div class="w-16 h-16 rounded-2xl bg-secondary-100 text-secondary-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-gray-900 mb-1">{{ $stats['facilities'] }}</div>
                        <div class="text-gray-500 font-bold uppercase tracking-wider text-sm">Fasilitas Ramah</div>
                    </div>
                </div>
                <!-- Stat 3 -->
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-gray-200/50 hover-lift border border-gray-100 flex items-center space-x-6">
                    <div class="w-16 h-16 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-gray-900 mb-1">Aman</div>
                        <div class="text-gray-500 font-bold uppercase tracking-wider text-sm">Sistem Laporan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions / Interaksi -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-4xl font-black text-gray-900 mb-4">Mari Berpartisipasi!</h2>
                <p class="text-lg text-gray-600">Kami mendengar suaramu. Pilih layanan di bawah ini untuk ikut serta menjadikan Konawe lebih baik.</p>
            </div>
            
            <div class="grid grid-cols-1 gap-8 max-w-2xl mx-auto">
                <!-- Pengaduan Card -->
                <a href="/pengaduan" class="group block relative rounded-3xl overflow-hidden hover-lift">
                    <div class="absolute inset-0 bg-gradient-to-br from-secondary-500 to-red-500 opacity-90"></div>
                    <div class="absolute -left-10 -top-10 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative p-10 flex items-center">
                        <div class="flex-grow">
                            <h3 class="text-3xl font-black text-white mb-2">Pengaduan</h3>
                            <p class="text-red-100 font-medium text-lg">Lapor secara rahasia jika menemukan pelanggaran hak anak.</p>
                        </div>
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-white backdrop-blur-md flex-shrink-0 ml-6 group-hover:translate-x-2 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Agenda / Upcoming Events -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-black text-gray-900">Agenda Kegiatan</h2>
                    <p class="text-gray-600 font-medium mt-2">Jadwal kegiatan terkini di Konawe</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($upcomingEvents as $agenda)
                <div class="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 p-6 flex items-start space-x-5 h-full relative overflow-hidden">
                    <!-- Highlight strip -->
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary-500 rounded-l-3xl group-hover:bg-secondary-500 transition-colors"></div>
                    
                    <!-- Date Block -->
                    <div class="flex-shrink-0 bg-primary-50 rounded-2xl p-3 text-center border border-primary-100 w-16 group-hover:bg-primary-100 transition-colors">
                        <div class="text-primary-700 text-xs font-bold uppercase tracking-wider mb-1">{{ \Carbon\Carbon::parse($agenda['start_at'] ?? now())->translatedFormat('M') }}</div>
                        <div class="text-primary-600 font-black text-2xl leading-none">{{ \Carbon\Carbon::parse($agenda['start_at'] ?? now())->format('d') }}</div>
                    </div>
                    
                    <!-- Content Block -->
                    <div class="flex flex-col flex-grow min-w-0 h-full justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition line-clamp-2 break-words" title="{{ strip_tags($agenda['title'] ?? '') }}">{{ strip_tags($agenda['title'] ?? 'Agenda Kegiatan') }}</h3>
                            <p class="text-gray-600 text-sm font-medium line-clamp-2 mb-4 break-words" title="{{ strip_tags($agenda['description'] ?? '') }}">{{ strip_tags($agenda['description'] ?? '') }}</p>
                        </div>
                        
                        <div class="space-y-2 mt-auto pt-2 border-t border-gray-50">
                            @if(isset($agenda['start_at']))
                            <div class="flex items-center text-xs text-gray-500 font-bold">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ \Carbon\Carbon::parse($agenda['start_at'])->format('H:i') }} WITA
                            </div>
                            @endif
                            @if(isset($agenda['location']))
                            <div class="flex items-center text-xs text-gray-500 font-bold">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="truncate" title="{{ $agenda['location'] }}">{{ $agenda['location'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-3xl border border-gray-100">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada agenda kegiatan terdekat.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Latest News -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-black text-gray-900">Kabar Terbaru</h2>
                    <p class="text-gray-600 font-medium mt-2">Berita terkini seputar kegiatan KLA</p>
                </div>
                <a href="/berita" class="mt-4 sm:mt-0 text-primary-600 font-bold hover:text-primary-800 bg-primary-50 px-6 py-2 rounded-full hover:bg-primary-100 transition">Lihat Semua Berita</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($latestNews as $news)
                <a href="/berita/{{ $news->slug }}" class="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover-lift transition overflow-hidden border border-gray-100 flex flex-col h-full">
                    <div class="relative overflow-hidden">
                        @if($news->featured_image)
                        <img src="{{ Storage::url($news->featured_image) }}" alt="{{ $news->title }}" class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                        <div class="w-full h-56 bg-primary-100 flex items-center justify-center text-primary-300 group-hover:scale-105 transition-transform duration-500">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        @endif
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-primary-700 shadow-sm">
                            {{ $news->published_at?->format('d M Y') }}
                        </div>
                    </div>
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition">{{ $news->title }}</h3>
                            <p class="text-gray-600 line-clamp-3 font-medium">{{ $news->excerpt }}</p>
                        </div>
                        <div class="mt-6 flex items-center text-primary-600 font-bold text-sm">
                            Baca selengkapnya 
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-3xl border border-gray-100">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada kabar terbaru.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.app>
