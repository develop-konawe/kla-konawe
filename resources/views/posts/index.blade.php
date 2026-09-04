<x-layouts.app title="Berita & Artikel">
    <div class="bg-gradient-to-r from-primary-500 to-primary-700 pt-32 pb-20 text-white text-center relative overflow-hidden">
        <div class="h-24"></div>
        <div class="absolute top-10 right-10 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-5 left-20 w-32 h-32 bg-secondary-400/20 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Berita & Artikel</h1>
            <p class="text-xl text-primary-100 max-w-2xl mx-auto font-medium">Kumpulan informasi, berita terkini, dan artikel edukasi seputar perlindungan anak.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
            <a href="/berita/{{ $post->slug }}" class="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover-lift transition overflow-hidden border border-gray-100 flex flex-col h-full">
                <div class="relative overflow-hidden">
                    @if($post->featured_image)
                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-56 bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center text-primary-300 group-hover:scale-105 transition-transform duration-500">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    @endif
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-primary-700 shadow-sm">
                        {{ $post->category?->name ?? 'Umum' }}
                    </div>
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-gray-600 shadow-sm">
                        {{ $post->published_at?->format('d M Y') }}
                    </div>
                </div>
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition leading-tight">{{ $post->title }}</h3>
                        <p class="text-gray-600 line-clamp-3 font-medium">{{ $post->excerpt }}</p>
                    </div>
                    <div class="mt-6 flex items-center text-primary-600 font-bold text-sm">
                        Baca selengkapnya
                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-16 bg-white rounded-3xl border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Berita</h3>
                <p class="text-gray-500 font-medium">Nantikan informasi terbaru dari kami.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
</x-layouts.app>
