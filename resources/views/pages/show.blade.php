<x-layouts.app :title="$page->title">
    <div class="bg-gradient-to-r from-primary-500 to-primary-700 pt-32 pb-20 text-white text-center relative overflow-hidden">
        <div class="h-24"></div>
        <div class="absolute top-10 left-10 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-5 right-20 w-32 h-32 bg-secondary-400/20 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">{{ $page->title }}</h1>
            @if($page->excerpt)
            <p class="text-xl text-primary-100 max-w-3xl mx-auto font-medium">{{ $page->excerpt }}</p>
            @endif
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-8 relative z-20">
        <article class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12">
            <div class="prose prose-lg prose-primary max-w-none text-gray-700
                        prose-headings:font-black prose-headings:text-gray-900
                        prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline
                        prose-li:marker:text-primary-400
                        prose-strong:text-gray-900">
                {!! $page->content !!}
            </div>
        </article>
    </div>
</x-layouts.app>
