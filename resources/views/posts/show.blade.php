<x-layouts.app :title="$post->title">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-12">
        <a href="/berita" class="inline-flex items-center text-primary-600 hover:text-primary-800 font-bold mb-8 group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Berita
        </a>
        
        <article class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            @php
                $showImg = data_get($post, 'featured_image');
                $showImgSrc = $showImg ? (str_starts_with($showImg, 'http') ? $showImg : Storage::url($showImg)) : null;
            @endphp
            @if($showImgSrc)
            <img src="{{ $showImgSrc }}" alt="{{ data_get($post, 'title') }}" class="w-full h-64 md:h-96 object-cover">
            @endif
            
            <div class="p-8 md:p-12">
                <div class="flex flex-wrap items-center text-sm font-bold mb-6 gap-3">
                    <span class="bg-primary-100 text-primary-700 px-4 py-1.5 rounded-full">{{ $post->category?->name ?? 'Umum' }}</span>
                    <span class="text-gray-400">&bull;</span>
                    <span class="text-gray-500">{{ $post->published_at?->format('d M Y') }}</span>
                    @if($post->author)
                    <span class="text-gray-400">&bull;</span>
                    <span class="text-gray-500">Oleh {{ $post->author->name }}</span>
                    @endif
                </div>

                <h1 class="text-3xl md:text-5xl font-black text-gray-900 mb-6 leading-tight tracking-tight">{{ $post->title }}</h1>
                
                @if($post->excerpt)
                <div class="text-xl text-gray-600 mb-8 font-medium border-l-4 border-primary-400 pl-6 bg-primary-50/50 py-4 rounded-r-xl">
                    {{ $post->excerpt }}
                </div>
                @endif

                <div class="prose prose-lg prose-primary max-w-none text-gray-700 mt-8
                            prose-headings:font-black prose-headings:text-gray-900
                            prose-a:text-primary-600
                            prose-img:rounded-2xl prose-img:shadow-lg">
                    {!! $post->content !!}
                </div>
            </div>
        </article>
    </div>
</x-layouts.app>
