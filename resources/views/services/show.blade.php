<x-layouts.app :title="$service->name">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-12">
        <a href="/layanan" class="inline-flex items-center text-primary-600 hover:text-primary-800 font-bold mb-8 group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Direktori Layanan
        </a>
        
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            @if($service->image)
            <div class="w-full bg-gray-50 flex items-center justify-center p-8 border-b border-gray-100">
                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="max-w-full max-h-96 object-contain rounded-2xl shadow-sm">
            </div>
            @endif
            <div class="bg-gradient-to-r from-primary-50 to-primary-100 p-8 md:p-10 border-b border-primary-100">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-2 tracking-tight">{{ $service->name }}</h1>
                @if($service->organization)
                <p class="text-primary-700 font-bold">Penyelenggara: {{ $service->organization->name }}</p>
                @endif
            </div>
            
            <div class="p-8 md:p-10">
                <div class="prose prose-lg prose-primary max-w-none text-gray-700 mb-10
                            prose-headings:font-black prose-headings:text-gray-900">
                    {!! $service->description !!}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-100 pt-8">
                    <div class="bg-gray-50 p-6 rounded-2xl">
                        <h3 class="text-lg font-black text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Informasi Kontak
                        </h3>
                        <ul class="space-y-3 text-gray-600 font-medium">
                            @if($service->contact_info)
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $service->contact_info }}
                            </li>
                            @endif
                            @if($service->type)
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                Jenis: {{ $service->type }}
                            </li>
                            @endif
                        </ul>
                    </div>
                    
                    @if($service->working_hours)
                    <div class="bg-gray-50 p-6 rounded-2xl">
                        <h3 class="text-lg font-black text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Jam Operasional
                        </h3>
                        <div class="text-gray-600 font-medium leading-relaxed">
                            {!! nl2br(e($service->working_hours)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
