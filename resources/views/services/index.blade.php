<x-layouts.app title="Direktori Layanan Publik">
    <div class="bg-gradient-to-r from-primary-500 to-primary-700 pt-32 pb-20 text-white text-center relative overflow-hidden">
        <div class="h-24"></div>
        <div class="absolute top-6 left-10 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-6 right-10 w-32 h-32 bg-secondary-400/20 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Direktori Layanan</h1>
            <p class="text-xl text-primary-100 max-w-2xl mx-auto font-medium mb-8">Temukan berbagai layanan publik yang ramah anak di Kabupaten Konawe.</p>
            
            <form method="GET" action="/layanan" class="max-w-xl mx-auto flex bg-white shadow-2xl rounded-full overflow-hidden">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari layanan (misal: kesehatan, pendidikan)" class="flex-grow border-0 px-6 py-4 text-gray-900 focus:ring-0 font-medium text-lg">
                <button type="submit" class="bg-secondary-500 hover:bg-secondary-600 text-white font-bold py-4 px-8 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Cari
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($services as $service)
            <a href="/layanan/{{ $service->slug }}" class="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover-lift transition border border-gray-100 flex flex-col h-full overflow-hidden">
                <div class="p-8 flex-grow">
                    <div class="w-12 h-12 rounded-2xl bg-primary-100 text-primary-600 flex items-center justify-center mb-5 group-hover:bg-primary-500 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition leading-tight">{{ $service->name }}</h3>
                    <p class="text-gray-600 line-clamp-3 font-medium">{!! strip_tags($service->description) !!}</p>
                    
                    @if($service->contact_info)
                    <div class="mt-4 flex items-center text-sm text-gray-500 font-medium">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        {{ $service->contact_info }}
                    </div>
                    @endif
                </div>
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-primary-600 font-bold text-sm">Lihat Detail</span>
                    <svg class="w-5 h-5 text-primary-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-16 bg-white rounded-3xl border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Layanan Tidak Ditemukan</h3>
                <p class="text-gray-500 font-medium">Coba gunakan kata kunci pencarian yang berbeda.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $services->links() }}
        </div>
    </div>
</x-layouts.app>
