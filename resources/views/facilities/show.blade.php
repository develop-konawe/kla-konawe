<x-layouts.app :title="$facility->name">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-12">
        <a href="/fasilitas" class="inline-flex items-center text-secondary-600 hover:text-secondary-800 font-bold mb-8 group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Fasilitas
        </a>
        
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            @if($facility->image)
            <div class="w-full bg-gray-50 flex items-center justify-center p-8 border-b border-gray-100">
                <img src="{{ asset('storage/' . $facility->image) }}" alt="{{ $facility->name }}" class="max-w-full max-h-96 object-contain rounded-2xl shadow-sm">
            </div>
            @endif
            <div class="bg-gradient-to-r from-secondary-50 to-secondary-100 p-8 md:p-10 border-b border-secondary-100">
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    @if($facility->is_child_friendly)
                    <span class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-sm font-bold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Terverifikasi Ramah Anak
                    </span>
                    @endif
                    @if($facility->verified_at)
                    <span class="bg-blue-100 text-blue-700 px-4 py-1.5 rounded-full text-sm font-bold">
                        Diverifikasi: {{ $facility->verified_at->format('d M Y') }}
                    </span>
                    @endif
                    @if($facility->category)
                    <span class="bg-secondary-100 text-secondary-700 px-4 py-1.5 rounded-full text-sm font-bold">
                        {{ $facility->category }}
                    </span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ $facility->name }}</h1>
                @if($facility->organization)
                <p class="text-secondary-700 font-bold mt-2">Dikelola oleh: {{ $facility->organization->name }}</p>
                @endif
            </div>
            
            <div class="p-8 md:p-10">
                @if($facility->description)
                <div class="prose prose-lg prose-primary max-w-none text-gray-700 mb-10
                            prose-headings:font-black prose-headings:text-gray-900">
                    {!! $facility->description !!}
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($facility->address)
                    <div class="bg-gray-50 p-6 rounded-2xl">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Alamat</h3>
                        <div class="flex items-start text-gray-700 font-medium">
                            <svg class="w-5 h-5 mr-3 text-secondary-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ $facility->address }}</span>
                        </div>
                    </div>
                    @endif

                    @if($facility->coordinates)
                    <div class="bg-gray-50 p-6 rounded-2xl">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Koordinat</h3>
                        <div class="flex items-center text-gray-700 font-medium">
                            <svg class="w-5 h-5 mr-3 text-secondary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                            <span class="font-mono text-sm">{{ $facility->coordinates }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
