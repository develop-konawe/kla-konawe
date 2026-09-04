<x-layouts.app title="Publikasi & Laporan">
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 pt-32 pb-20 text-white text-center relative overflow-hidden">
        <div class="h-24"></div>
        <div class="absolute top-8 left-16 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-8 right-16 w-40 h-40 bg-secondary-400/20 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Publikasi & Laporan</h1>
            <p class="text-xl text-primary-100 max-w-2xl mx-auto font-medium">Kumpulan dokumen, panduan, dan laporan capaian Kabupaten Layak Anak.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($publications as $pub)
            <div class="bg-white rounded-3xl shadow-sm hover:shadow-xl hover-lift transition p-8 border border-gray-100 flex flex-col h-full">
                <div class="flex-grow">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm font-bold">{{ $pub->year }}</span>
                        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-bold">{{ $pub->category?->name ?? 'Dokumen' }}</span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3 leading-tight">{{ $pub->title }}</h3>
                    <p class="text-gray-600 font-medium line-clamp-3">{{ $pub->description }}</p>
                </div>
                @if($pub->file_path)
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <a href="{{ Storage::url($pub->file_path) }}" target="_blank" class="flex items-center justify-center bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition group">
                        <svg class="w-5 h-5 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh Dokumen
                    </a>
                </div>
                @endif
            </div>
            @empty
            <div class="col-span-full text-center py-16 bg-white rounded-3xl border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Publikasi</h3>
                <p class="text-gray-500 font-medium">Dokumen publik sedang disiapkan oleh tim kami.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $publications->links() }}
        </div>
    </div>
</x-layouts.app>
