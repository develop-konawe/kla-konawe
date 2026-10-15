import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useState } from 'react';
import AppLayout from '../../layouts/AppLayout';
import { type Post } from '../../types';

interface PostShowProps {
    post: Post;
    relatedPosts?: Post[];
}

function getImageUrl(path?: string | null): string {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    return path.startsWith('/') ? path : `/storage/${path}`;
}

export default function PostShow({ post, relatedPosts = [] }: PostShowProps) {
    const [copied, setCopied] = useState(false);

    const date = post.published_at ? new Date(post.published_at) : null;
    const formattedDate = date?.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });

    const imageUrl = getImageUrl(post.featured_image);

    // Estimate reading time
    const textOnly = (post.content || '').replace(/<[^>]*>/g, ' ');
    const wordCount = textOnly.trim().split(/\s+/).filter(Boolean).length;
    const readingTime = Math.max(1, Math.ceil(wordCount / 180));

    // Resolve author name and organization
    const authorName = typeof post.author === 'object' && post.author !== null
        ? post.author.name
        : (typeof post.author === 'string' ? post.author : 'Tim Redaksi KLA Konawe');

    const opdName = typeof post.author === 'object' && post.author?.organization?.name
        ? post.author.organization.name
        : (post.opd?.name || 'Pemerintah Kabupaten Konawe');

    const handleShare = async () => {
        const shareData = {
            title: post.title,
            text: post.excerpt || post.title,
            url: window.location.href,
        };

        if (navigator.share) {
            try {
                await navigator.share(shareData);
                return;
            } catch {
                // user cancelled or share failed, fallback
            }
        }

        try {
            await navigator.clipboard.writeText(window.location.href);
            setCopied(true);
            setTimeout(() => setCopied(false), 2500);
        } catch {
            // ignore
        }
    };

    const currentUrl = typeof window !== 'undefined' ? window.location.href : '';

    return (
        <AppLayout title={post.title}>
            {/* HERO HEADER */}
            <div className="relative bg-gradient-to-br from-primary-700 via-primary-800 to-indigo-950 pt-36 pb-20 text-white overflow-hidden">
                {/* Decorative floating blur circles */}
                <div className="absolute inset-0 pointer-events-none opacity-20">
                    <div className="absolute -top-24 -left-24 w-96 h-96 bg-primary-400 rounded-full blur-3xl" />
                    <div className="absolute top-1/2 right-4 w-80 h-80 bg-indigo-300 rounded-full blur-3xl" />
                    <div className="absolute -bottom-24 left-1/3 w-80 h-80 bg-sky-300 rounded-full blur-2xl" />
                </div>

                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div>
                        {/* Breadcrumbs */}
                        <nav className="flex items-center gap-2 text-xs sm:text-sm text-primary-200/80 mb-6 flex-wrap">
                            <Link href="/" prefetch="hover" className="hover:text-white transition">
                                Beranda
                            </Link>
                            <span>/</span>
                            <Link href="/berita" prefetch="hover" className="hover:text-white transition">
                                Berita &amp; Artikel
                            </Link>
                            {post.category && (
                                <>
                                    <span>/</span>
                                    <span className="text-primary-200">{post.category.name}</span>
                                </>
                            )}
                        </nav>

                        {/* Category & Read Time Pills */}
                        <div className="flex flex-wrap items-center gap-2.5 mb-4">
                            {post.category ? (
                                <span className="inline-flex items-center gap-1.5 bg-primary-500/30 border border-primary-400/40 text-primary-100 text-xs sm:text-sm font-semibold px-3.5 py-1 rounded-full backdrop-blur-sm">
                                    {post.category.name}
                                </span>
                            ) : (
                                <span className="inline-flex items-center gap-1.5 bg-primary-500/30 border border-primary-400/40 text-primary-100 text-xs sm:text-sm font-semibold px-3.5 py-1 rounded-full backdrop-blur-sm">
                                    KLA Konawe
                                </span>
                            )}
                            <span className="inline-flex items-center text-xs font-medium bg-black/25 text-primary-200 border border-white/10 px-3 py-1 rounded-full backdrop-blur-sm">
                                ⏱️ {readingTime} menit baca
                            </span>
                        </div>

                        {/* Headline Title */}
                        <h1 className="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight mb-6">
                            {post.title}
                        </h1>

                        {/* Author & Meta Bar */}
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-primary-400/20">
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-full bg-white/20 text-white font-bold flex items-center justify-center text-sm border border-white/30 flex-shrink-0">
                                    {authorName.charAt(0).toUpperCase()}
                                </div>
                                <div className="text-left">
                                    <div className="text-sm font-bold text-white">{authorName}</div>
                                    <div className="text-xs text-primary-200">{opdName}</div>
                                </div>
                            </div>

                            <div className="flex items-center gap-3">
                                {formattedDate && (
                                    <div className="flex items-center gap-1.5 text-xs sm:text-sm text-primary-200">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{formattedDate}</span>
                                    </div>
                                )}

                                <button
                                    type="button"
                                    onClick={handleShare}
                                    className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-semibold transition active:scale-95"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                    {copied ? 'Tersalin!' : 'Bagikan'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* MAIN ARTICLE BODY & SIDEBAR */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-20 relative z-20">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    {/* ARTICLE CONTENT (8 COLS) */}
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.4, delay: 0.1 }}
                        className="lg:col-span-8 space-y-8"
                    >
                        {/* FEATURED IMAGE */}
                        <div className="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
                            {imageUrl ? (
                                <div className="relative w-full max-h-[460px] bg-slate-900 overflow-hidden group">
                                    <img
                                        src={imageUrl}
                                        alt={post.title}
                                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    />
                                    <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none" />
                                    <div className="absolute bottom-4 left-4 right-4 flex items-center justify-between text-white text-xs sm:text-sm">
                                        <span className="bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/20 font-medium">
                                            Dokumentasi Liputan KLA Konawe
                                        </span>
                                    </div>
                                </div>
                            ) : (
                                <div className="h-60 bg-gradient-to-br from-primary-50 via-indigo-50 to-slate-100 flex flex-col items-center justify-center text-primary-600 p-8 text-center border-b border-primary-100">
                                    <div className="w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center text-primary-600 mb-3">
                                        <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>
                                    </div>
                                    <h4 className="text-base font-bold text-slate-800">{post.title}</h4>
                                </div>
                            )}

                            {/* ARTICLE TEXT */}
                            <div className="p-6 sm:p-10 md:p-12">
                                {/* Lead Excerpt if present */}
                                {post.excerpt && (
                                    <div className="p-4 sm:p-5 rounded-2xl bg-primary-50/60 border-l-4 border-primary-500 mb-8 text-primary-950 font-medium text-base sm:text-lg leading-relaxed">
                                        {post.excerpt}
                                    </div>
                                )}

                                <article
                                    className="prose prose-slate prose-lg max-w-none text-slate-800 leading-relaxed font-normal
                                        prose-headings:font-black prose-headings:text-slate-900 prose-headings:tracking-tight
                                        prose-h2:text-2xl prose-h2:mt-8 prose-h2:mb-4
                                        prose-h3:text-xl prose-h3:mt-6 prose-h3:mb-3
                                        prose-p:mb-5 prose-p:leading-relaxed
                                        prose-a:text-primary-600 prose-a:font-bold hover:prose-a:underline
                                        prose-img:rounded-2xl prose-img:shadow-md
                                        prose-blockquote:border-l-4 prose-blockquote:border-primary-500 prose-blockquote:bg-slate-50 prose-blockquote:p-4 prose-blockquote:rounded-r-2xl prose-blockquote:italic"
                                    dangerouslySetInnerHTML={{
                                        __html: post.content || '<p>Konten artikel sedang disiapkan.</p>',
                                    }}
                                />

                                {/* SOCIAL SHARE ACTION BAR */}
                                <div className="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div className="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Bagikan Artikel Ini:
                                    </div>
                                    <div className="flex items-center gap-2 flex-wrap">
                                        <a
                                            href={`https://api.whatsapp.com/send?text=${encodeURIComponent(`${post.title} - Baca selengkapnya di: ${currentUrl}`)}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition inline-flex items-center gap-1.5"
                                        >
                                            <svg className="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                            </svg>
                                            WhatsApp
                                        </a>

                                        <a
                                            href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(post.title)}&url=${encodeURIComponent(currentUrl)}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition inline-flex items-center gap-1.5"
                                        >
                                            <svg className="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                            </svg>
                                            X / Twitter
                                        </a>

                                        <button
                                            type="button"
                                            onClick={handleShare}
                                            className="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
                                        >
                                            {copied ? 'Tautan Tersalin!' : 'Salin Tautan'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* EDITORIAL DISCLAIMER CARD */}
                        <div className="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200/80 text-xs text-slate-600 flex items-start gap-3">
                            <svg className="w-5 h-5 text-primary-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p className="leading-relaxed">
                                Berita dan artikel ini dipublikasikan oleh <strong>Tim Redaksi KLA Pemerintah Kabupaten Konawe</strong> sebagai media penyebarluasan Informasi Layak Anak (Klaster 1 KLA) guna mendukung keterbukaan informasi publik dan edukasi hak anak bagi seluruh masyarakat.
                            </p>
                        </div>
                    </motion.div>

                    {/* STICKY SIDEBAR (4 COLS) */}
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.4, delay: 0.2 }}
                        className="lg:col-span-4 space-y-6 lg:sticky lg:top-28"
                    >
                        {/* AUTHOR & AGENCY CARD */}
                        <div className="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/60 border border-slate-100">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-600 to-indigo-700 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-primary-500/20 flex-shrink-0">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                                <div>
                                    <span className="text-[11px] font-bold uppercase tracking-wider text-primary-600">
                                        Penerbit Resmi
                                    </span>
                                    <h4 className="text-base font-bold text-slate-900 leading-snug">
                                        {opdName}
                                    </h4>
                                </div>
                            </div>
                            <p className="text-xs text-slate-500 leading-relaxed mb-4">
                                Menyampaikan kabar terkini seputar pembangunan ramah anak, kebijakan daerah, dan kegiatan perlindungan anak di Kabupaten Konawe.
                            </p>
                            <Link
                                href="/berita"
                                prefetch="hover"
                                className="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
                            >
                                Arsip Semua Berita
                            </Link>
                        </div>

                        {/* RELATED POSTS IN SIDEBAR */}
                        {relatedPosts.length > 0 && (
                            <div className="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/60 border border-slate-100">
                                <h4 className="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <span className="w-2 h-2 rounded-full bg-primary-600" />
                                    Berita Terkait Lainnya
                                </h4>
                                <div className="space-y-4">
                                    {relatedPosts.map((item) => {
                                        const itemImg = getImageUrl(item.featured_image);
                                        const itemDate = item.published_at ? new Date(item.published_at) : null;
                                        return (
                                            <Link
                                                key={item.id}
                                                href={`/berita/${item.slug}`}
                                                prefetch="hover"
                                                className="group flex gap-3 items-start pb-3 border-b border-slate-100 last:border-0 last:pb-0"
                                            >
                                                {itemImg ? (
                                                    <div className="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                                                        <img
                                                            src={itemImg}
                                                            alt={item.title}
                                                            className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                                        />
                                                    </div>
                                                ) : (
                                                    <div className="w-16 h-16 rounded-xl bg-primary-50 text-primary-400 flex items-center justify-center flex-shrink-0">
                                                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1" />
                                                        </svg>
                                                    </div>
                                                )}
                                                <div className="min-w-0 flex-grow">
                                                    <h5 className="text-xs font-bold text-slate-900 group-hover:text-primary-600 transition line-clamp-2 leading-snug">
                                                        {item.title}
                                                    </h5>
                                                    {itemDate && (
                                                        <span className="text-[11px] text-slate-400 mt-1 block">
                                                            {itemDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                        </span>
                                                    )}
                                                </div>
                                            </Link>
                                        );
                                    })}
                                </div>
                            </div>
                        )}

                        {/* EMERGENCY REPORT CALLOUT */}
                        <div className="bg-gradient-to-br from-red-500 to-rose-600 rounded-3xl p-6 text-white shadow-xl shadow-red-500/20">
                            <span className="bg-white/20 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider mb-2 inline-block">
                                Layanan Pengaduan 24 Jam
                            </span>
                            <h4 className="text-base font-bold mb-2">Lihat Kasus Kekerasan pada Anak?</h4>
                            <p className="text-xs text-red-100 leading-relaxed mb-4">
                                Jangan ragu melapor. UPTD PPA Konawe menjamin perlindungan identitas pelapor dan korban secara rahasia.
                            </p>
                            <Link
                                href="/pengaduan"
                                prefetch="hover"
                                className="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-white text-red-600 font-bold text-xs shadow-md hover:bg-red-50 transition active:scale-95"
                            >
                                Kirim Laporan Sekarang
                            </Link>
                        </div>
                    </motion.div>
                </div>

                {/* BOTTOM RECOMMENDED NEWS GRID */}
                {relatedPosts.length > 0 && (
                    <div className="mt-20 pt-12 border-t border-slate-200">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                            <div>
                                <span className="text-xs font-bold uppercase tracking-wider text-primary-600">
                                    Informasi Terkait
                                </span>
                                <h3 className="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                                    Berita Ramah Anak Lainnya di Konawe
                                </h3>
                            </div>
                            <Link
                                href="/berita"
                                prefetch="hover"
                                className="inline-flex items-center text-sm font-bold text-primary-600 hover:text-primary-700 transition"
                            >
                                Semua Berita
                                <svg className="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {relatedPosts.map((item) => {
                                const itemImg = getImageUrl(item.featured_image);
                                const itemDate = item.published_at ? new Date(item.published_at) : null;
                                return (
                                    <Link
                                        key={item.id}
                                        href={`/berita/${item.slug}`}
                                        prefetch="hover"
                                        className="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 flex flex-col"
                                    >
                                        <div className="h-44 bg-slate-100 overflow-hidden relative">
                                            {itemImg ? (
                                                <img
                                                    src={itemImg}
                                                    alt={item.title}
                                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                />
                                            ) : (
                                                <div className="w-full h-full bg-gradient-to-br from-primary-50 to-indigo-50 flex items-center justify-center text-primary-300">
                                                    <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1" />
                                                    </svg>
                                                </div>
                                            )}
                                            {item.category && (
                                                <span className="absolute top-3 left-3 bg-primary-600/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                                    {item.category.name}
                                                </span>
                                            )}
                                        </div>
                                        <div className="p-6 flex-grow flex flex-col justify-between">
                                            <div>
                                                <h4 className="text-base font-bold text-slate-900 group-hover:text-primary-600 transition mb-2 line-clamp-2">
                                                    {item.title}
                                                </h4>
                                                <p className="text-xs text-slate-500 line-clamp-2 mb-3">
                                                    {item.excerpt || item.content?.replace(/<[^>]*>/g, '') || ''}
                                                </p>
                                            </div>
                                            <div className="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                                                <span>
                                                    {itemDate?.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                </span>
                                                <span className="font-bold text-primary-600 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                                                    Baca
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
