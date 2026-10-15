import { Deferred, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useState } from 'react';
import AppLayout from '../../layouts/AppLayout';
import { type PaginatedData, type Post } from '../../types';

interface PostIndexProps {
    posts?: PaginatedData<Post>;
    filters?: {
        search?: string;
    };
}

function PostsSkeleton() {
    return (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[1, 2, 3, 4, 5, 6].map((n) => (
                <div
                    key={n}
                    className="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full animate-pulse"
                >
                    <div className="h-52 bg-slate-200 relative">
                        <div className="absolute top-4 left-4 h-6 w-20 bg-slate-300 rounded-full" />
                        <div className="absolute top-4 right-4 h-6 w-24 bg-slate-300 rounded-full" />
                    </div>
                    <div className="p-6 flex-grow flex flex-col justify-between space-y-4">
                        <div className="space-y-3">
                            <div className="h-5 bg-slate-200 rounded-lg w-5/6" />
                            <div className="h-5 bg-slate-200 rounded-lg w-3/5" />
                            <div className="pt-2 space-y-2">
                                <div className="h-3.5 bg-slate-100 rounded-md w-full" />
                                <div className="h-3.5 bg-slate-100 rounded-md w-full" />
                                <div className="h-3.5 bg-slate-100 rounded-md w-4/5" />
                            </div>
                        </div>
                        <div className="pt-4 border-t border-slate-50 flex items-center justify-between">
                            <div className="h-4 bg-slate-200 rounded-md w-28" />
                            <div className="h-4 w-4 bg-slate-200 rounded-full" />
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}

function PostCard({ post, index }: { post: Post; index: number }) {
    const date = post.published_at ? new Date(post.published_at) : null;
    const formatted = date?.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });

    const imageUrl = post.featured_image
        ? (post.featured_image.startsWith('http') ? post.featured_image : `/storage/${post.featured_image}`)
        : null;

    const href = post.url || `/berita/${post.slug}`;
    const isExternal = Boolean(post.url);

    const cardContent = (
        <>
            <div className="relative overflow-hidden">
                {imageUrl ? (
                    <img
                        src={imageUrl}
                        alt={post.title}
                        className="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                ) : (
                    <div className="w-full h-52 bg-primary-50 flex items-center justify-center text-primary-200">
                        <svg className="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                )}
                {post.category?.name && (
                    <div className="absolute top-4 left-4 bg-primary-600/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm">
                        {post.category.name}
                    </div>
                )}
                {formatted && (
                    <div className="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-primary-700 shadow-sm">
                        {formatted}
                    </div>
                )}
            </div>
            <div className="p-6 flex-grow flex flex-col justify-between">
                <div>
                    <h3 className="text-lg font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition line-clamp-2">
                        {post.title}
                    </h3>
                    <p className="text-gray-600 line-clamp-3 font-medium text-sm">{post.excerpt}</p>
                </div>
                <div className="mt-5 flex items-center text-primary-600 font-bold text-sm">
                    Baca selengkapnya
                    {isExternal ? (
                        <svg className="w-4 h-4 ml-1 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    ) : (
                        <svg className="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                        </svg>
                    )}
                </div>
            </div>
        </>
    );

    const linkClassName = "group bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 flex flex-col h-full overflow-hidden block";

    return (
        <motion.div
            initial={{ opacity: 0, y: 24 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.45, delay: (index % 9) * 0.06 }}
        >
            {isExternal ? (
                <a href={href} target="_blank" rel="noopener noreferrer" className={linkClassName}>
                    {cardContent}
                </a>
            ) : (
                <Link href={href} className={linkClassName}>
                    {cardContent}
                </Link>
            )}
        </motion.div>
    );
}

export default function PostsIndex({ posts, filters }: PostIndexProps) {
    const [search, setSearch] = useState(filters?.search || '');
    const [isSearching, setIsSearching] = useState(false);

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSearching(true);
        router.get('/berita', { search: search || undefined }, {
            preserveState: true,
            onFinish: () => setIsSearching(false),
        });
    };

    const handleClear = () => {
        setSearch('');
        setIsSearching(true);
        router.get('/berita', {}, {
            preserveState: true,
            onFinish: () => setIsSearching(false),
        });
    };

    return (
        <AppLayout title="Berita & Artikel">
            {/* Header Hero - Solid & Instantly Rendered */}
            <div className="bg-gradient-to-r from-primary-600 to-indigo-700 pt-32 pb-16 text-white text-center relative overflow-hidden">
                <div className="h-24" />
                <div className="relative z-10">
                    <h1 className="text-4xl md:text-5xl font-black mb-4 tracking-tight">Berita &amp; Artikel</h1>
                    <p className="text-xl text-indigo-100 max-w-2xl mx-auto font-medium">
                        Kabar terkini seputar program dan kegiatan KLA di Kabupaten Konawe.
                    </p>
                </div>
            </div>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {/* Search Bar */}
                <div className="mb-10 max-w-xl mx-auto">
                    <form onSubmit={handleSearch} className="flex gap-2">
                        <div className="relative flex-grow">
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari berita seputar Konawe..."
                                className="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-2xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                            />
                            <svg className="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <button
                            type="submit"
                            className="bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-3 rounded-2xl text-sm transition shadow-sm flex items-center gap-2"
                        >
                            {isSearching && (
                                <svg className="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            )}
                            <span>Cari</span>
                        </button>
                        {filters?.search && (
                            <button
                                type="button"
                                onClick={handleClear}
                                className="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-4 py-3 rounded-2xl text-sm transition"
                                title="Reset Pencarian"
                            >
                                Reset
                            </button>
                        )}
                    </form>
                </div>

                {/* Content Area with Deferred Skeleton Loading */}
                <Deferred data="posts" fallback={<PostsSkeleton />}>
                    {isSearching ? (
                        <PostsSkeleton />
                    ) : posts && posts.data && posts.data.length > 0 ? (
                        <>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                {posts.data.map((post, i) => (
                                    <PostCard key={post.id} post={post} index={i} />
                                ))}
                            </div>

                            {posts.last_page > 1 && (
                                <div className="mt-12 flex justify-center gap-2 flex-wrap">
                                    {posts.links.map((link, i) => (
                                        <button
                                            key={i}
                                            onClick={() => {
                                                if (link.url) {
                                                    setIsSearching(true);
                                                    router.get(link.url, {}, {
                                                        preserveState: true,
                                                        preserveScroll: true,
                                                        onFinish: () => setIsSearching(false),
                                                    });
                                                }
                                            }}
                                            disabled={!link.url}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                            className={`px-4 py-2 rounded-xl font-bold text-sm transition ${
                                                link.active
                                                    ? 'bg-primary-600 text-white shadow-md'
                                                    : link.url
                                                    ? 'bg-white text-gray-700 hover:bg-primary-50 hover:text-primary-600 border border-gray-200'
                                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                            }`}
                                        />
                                    ))}
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                            <div className="w-16 h-16 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <h3 className="text-lg font-bold text-gray-900 mb-1">Berita Tidak Ditemukan</h3>
                            <p className="text-gray-500 font-medium text-sm max-w-md mx-auto">
                                {filters?.search ? `Tidak ada berita yang cocok dengan kata kunci "${filters.search}".` : 'Belum ada berita yang dipublikasikan.'}
                            </p>
                            {filters?.search && (
                                <button
                                    type="button"
                                    onClick={handleClear}
                                    className="mt-4 px-4 py-2 bg-primary-50 text-primary-600 hover:bg-primary-100 rounded-xl text-sm font-bold transition"
                                >
                                    Lihat Semua Berita
                                </button>
                            )}
                        </div>
                    )}
                </Deferred>
            </div>
        </AppLayout>
    );
}
