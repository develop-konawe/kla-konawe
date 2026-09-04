<x-layouts.app title="Fasilitas Ramah Anak">
    <div class="bg-gradient-to-r from-secondary-500 to-secondary-700 pt-32 pb-20 text-white text-center relative overflow-hidden">
        <div class="h-24"></div>
        <div class="absolute top-6 left-10 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-6 right-10 w-32 h-32 bg-primary-400/20 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Fasilitas Ramah Anak</h1>
            <p class="text-xl text-secondary-50 max-w-2xl mx-auto font-medium mb-8">Database fasilitas publik di Kabupaten Konawe yang telah diverifikasi keramahannya terhadap anak.</p>
            
            <form method="GET" action="/fasilitas" class="max-w-xl mx-auto flex bg-white shadow-2xl rounded-full overflow-hidden">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari fasilitas (misal: taman bermain, perpustakaan)" class="flex-grow border-0 px-6 py-4 text-gray-900 focus:ring-0 font-medium text-lg">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-4 px-8 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Cari
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($facilities as $facility)
            <a href="/fasilitas/{{ $facility->slug }}" class="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover-lift transition border border-gray-100 flex flex-col h-full overflow-hidden">
                <div class="p-8 flex-grow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-secondary-100 text-secondary-600 flex items-center justify-center group-hover:bg-secondary-500 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        @if($facility->is_child_friendly)
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Ramah Anak
                        </span>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-secondary-600 transition leading-tight">{{ $facility->name }}</h3>
                    <p class="text-gray-600 line-clamp-3 font-medium mb-4">{!! strip_tags($facility->description) !!}</p>
                    
                    @if($facility->address)
                    <div class="flex items-start text-sm text-gray-500 font-medium">
                        <svg class="w-4 h-4 mr-2 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="line-clamp-2">{{ $facility->address }}</span>
                    </div>
                    @endif
                </div>
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-secondary-600 font-bold text-sm">Lihat Detail</span>
                    <svg class="w-5 h-5 text-secondary-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-16 bg-white rounded-3xl border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Fasilitas Tidak Ditemukan</h3>
                <p class="text-gray-500 font-medium">Coba gunakan kata kunci pencarian yang berbeda.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $facilities->links() }}
        </div>
    </div>
</x-layouts.app>
