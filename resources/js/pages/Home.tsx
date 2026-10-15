import { Link } from '@inertiajs/react';
import { motion, useInView } from 'framer-motion';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '../layouts/AppLayout';
import { type AgendaEvent, type HomeProps, type Post } from '../types';

// ─── Animated Counter ────────────────────────────────────────────────────────
function AnimatedCounter({ target, suffix = '' }: { target: number; suffix?: string }) {
    const [count, setCount] = useState(0);
    const ref = useRef<HTMLDivElement>(null);
    const inView = useInView(ref, { once: true });

    useEffect(() => {
        if (!inView) return;
        const duration = 1500;
        const steps = 60;
        const increment = target / steps;
        let current = 0;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                setCount(target);
                clearInterval(timer);
            } else {
                setCount(Math.floor(current));
            }
        }, duration / steps);
        return () => clearInterval(timer);
    }, [inView, target]);

    return (
        <div ref={ref} className="text-4xl font-black text-gray-900 mb-1">
            {count}{suffix}
        </div>
    );
}

// ─── News Card ────────────────────────────────────────────────────────────────
function NewsCard({ news, index }: { news: Post; index: number }) {
    const date = news.published_at ? new Date(news.published_at) : null;
    const formatted = date
        ? date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
        : '';

    const imageUrl = news.featured_image
        ? (news.featured_image.startsWith('http') ? news.featured_image : `/storage/${news.featured_image}`)
        : null;

    const href = news.url || `/berita/${news.slug}`;
    const isExternal = Boolean(news.url);

    const cardContent = (
        <>
            <div className="relative overflow-hidden">
                {imageUrl ? (
                    <img
                        src={imageUrl}
                        alt={news.title}
                        className="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                ) : (
                    <div className="w-full h-56 bg-primary-100 flex items-center justify-center text-primary-300 group-hover:scale-105 transition-transform duration-500">
                        <svg className="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                )}
                {news.category?.name && (
                    <div className="absolute top-4 left-4 bg-primary-600/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm">
                        {news.category.name}
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
                    <h3 className="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition line-clamp-2">
                        {news.title}
                    </h3>
                    <p className="text-gray-600 line-clamp-3 font-medium text-sm">{news.excerpt}</p>
                </div>
                <div className="mt-6 flex items-center text-primary-600 font-bold text-sm">
                    Baca selengkapnya
                    {isExternal ? (
                        <svg className="w-4 h-4 ml-1 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    ) : (
                        <svg
                            className="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                        </svg>
                    )}
                </div>
            </div>
        </>
    );

    const linkClassName = "group bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col h-full hover:-translate-y-1 block";

    return (
        <motion.div
            initial={{ opacity: 0, y: 32 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5, delay: index * 0.1 }}
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

// ─── Agenda Card ──────────────────────────────────────────────────────────────
function AgendaCard({ agenda, index }: { agenda: AgendaEvent; index: number }) {
    const date = agenda.start_at ? new Date(agenda.start_at) : new Date();
    const monthStr = date.toLocaleDateString('id-ID', { month: 'short' }).toUpperCase();
    const dayStr = String(date.getDate()).padStart(2, '0');
    const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

    return (
        <motion.div
            initial={{ opacity: 0, y: 24 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.45, delay: index * 0.1 }}
            className="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 p-6 flex items-start space-x-5 h-full relative overflow-hidden"
        >
            <div className="absolute left-0 top-0 bottom-0 w-1.5 bg-primary-500 rounded-l-3xl group-hover:bg-secondary-500 transition-colors" />
            <div className="flex-shrink-0 bg-primary-50 rounded-2xl p-3 text-center border border-primary-100 w-16 group-hover:bg-primary-100 transition-colors">
                <div className="text-primary-700 text-xs font-bold uppercase tracking-wider mb-1">{monthStr}</div>
                <div className="text-primary-600 font-black text-2xl leading-none">{dayStr}</div>
            </div>
            <div className="flex flex-col flex-grow min-w-0 h-full justify-between">
                <div>
                    <h3
                        className="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition line-clamp-2 break-words"
                        title={agenda.title}
                    >
                        {agenda.title ?? 'Agenda Kegiatan'}
                    </h3>
                    {agenda.description && (
                        <p className="text-gray-600 text-sm font-medium line-clamp-2 mb-4 break-words">
                            {agenda.description}
                        </p>
                    )}
                </div>
                <div className="space-y-2 mt-auto pt-2 border-t border-gray-50">
                    {agenda.start_at && (
                        <div className="flex items-center text-xs text-gray-500 font-bold">
                            <svg className="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {timeStr} WITA
                        </div>
                    )}
                    {agenda.location && (
                        <div className="flex items-center text-xs text-gray-500 font-bold">
                            <svg className="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span className="truncate" title={agenda.location}>{agenda.location}</span>
                        </div>
                    )}
                </div>
            </div>
        </motion.div>
    );
}

// ─── 5 KLA Clusters Data ──────────────────────────────────────────────────────
const klaKlusters = [
    {
        number: '01',
        name: 'Hak Sipil & Kebebasan',
        description:
            'Menjamin hak anak atas identitas, kebebasan berekspresi, berkumpul, dan mendapatkan informasi yang layak serta aman.',
        color: 'from-blue-500 to-cyan-500',
        bgLight: 'bg-blue-50',
        borderColor: 'border-blue-200',
        textColor: 'text-blue-700',
        iconColor: 'text-blue-500',
        icon: (
            <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        ),
        highlights: ['Akta Kelahiran', 'Kebebasan Berekspresi', 'Perlindungan Data'],
    },
    {
        number: '02',
        name: 'Lingkungan Keluarga & Pengasuhan Alternatif',
        description:
            'Mendukung keluarga harmonis, pola asuh positif, dan memberikan perlindungan bagi anak yang membutuhkan pengasuhan alternatif.',
        color: 'from-emerald-500 to-teal-500',
        bgLight: 'bg-emerald-50',
        borderColor: 'border-emerald-200',
        textColor: 'text-emerald-700',
        iconColor: 'text-emerald-500',
        icon: (
            <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        ),
        highlights: ['Keluarga Harmonis', 'Pola Asuh Positif', 'Pengasuhan Alternatif'],
    },
    {
        number: '03',
        name: 'Kesehatan Dasar & Kesejahteraan',
        description:
            'Memastikan akses anak pada layanan kesehatan, gizi, air bersih, sanitasi, serta lingkungan bebas dari narkoba dan kekerasan.',
        color: 'from-rose-500 to-pink-500',
        bgLight: 'bg-rose-50',
        borderColor: 'border-rose-200',
        textColor: 'text-rose-700',
        iconColor: 'text-rose-500',
        icon: (
            <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        ),
        highlights: ['Gizi & Kesehatan', 'Air Bersih & Sanitasi', 'Bebas Narkoba'],
    },
    {
        number: '04',
        name: 'Pendidikan, Waktu Luang & Budaya',
        description:
            'Mewujudkan akses pendidikan inklusif, ruang bermain yang aman, dan pelestarian budaya lokal sebagai identitas anak.',
        color: 'from-amber-500 to-orange-500',
        bgLight: 'bg-amber-50',
        borderColor: 'border-amber-200',
        textColor: 'text-amber-700',
        iconColor: 'text-amber-500',
        icon: (
            <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        ),
        highlights: ['Pendidikan Inklusif', 'Ruang Bermain', 'Budaya Lokal'],
    },
    {
        number: '05',
        name: 'Perlindungan Khusus',
        description:
            'Memberikan perlindungan bagi anak dalam situasi rentan: korban kekerasan, eksploitasi, diskriminasi, dan kondisi darurat.',
        color: 'from-violet-500 to-purple-500',
        bgLight: 'bg-violet-50',
        borderColor: 'border-violet-200',
        textColor: 'text-violet-700',
        iconColor: 'text-violet-500',
        icon: (
            <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        ),
        highlights: ['Bebas Kekerasan', 'Anti Eksploitasi', 'Layanan Darurat'],
    },
];

// ─── Klaster Card ─────────────────────────────────────────────────────────────
function KlasterCard({ klaster, index }: { klaster: typeof klaKlusters[0]; index: number }) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: '-50px' }}
            transition={{ duration: 0.55, delay: index * 0.08, ease: [0.25, 0.46, 0.45, 0.94] }}
            className="group relative bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden flex flex-col hover:-translate-y-2"
        >
            {/* Top gradient bar */}
            <div className={`h-1.5 w-full bg-gradient-to-r ${klaster.color}`} />

            <div className="p-7 flex flex-col flex-grow">
                {/* Header row */}
                <div className="flex items-start justify-between mb-5">
                    <div className={`w-14 h-14 rounded-2xl ${klaster.bgLight} ${klaster.iconColor} flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300`}>
                        {klaster.icon}
                    </div>
                    <span className={`text-5xl font-black ${klaster.textColor} opacity-15 group-hover:opacity-25 transition-opacity leading-none select-none`}>
                        {klaster.number}
                    </span>
                </div>

                {/* Title */}
                <h3 className={`text-lg font-black text-gray-900 mb-3 group-hover:${klaster.textColor} transition-colors leading-snug`}>
                    {klaster.name}
                </h3>

                {/* Description */}
                <p className="text-gray-500 text-sm leading-relaxed font-medium flex-grow mb-5">
                    {klaster.description}
                </p>

                {/* Tags */}
                <div className="flex flex-wrap gap-2">
                    {klaster.highlights.map((tag) => (
                        <span
                            key={tag}
                            className={`inline-block ${klaster.bgLight} ${klaster.textColor} ${klaster.borderColor} border text-xs font-bold px-3 py-1 rounded-full`}
                        >
                            {tag}
                        </span>
                    ))}
                </div>
            </div>
        </motion.div>
    );
}

// ─── Home Page ────────────────────────────────────────────────────────────────
export default function Home({ latestNews, upcomingEvents, stats }: HomeProps) {
    const containerVariants = {
        hidden: {},
        visible: { transition: { staggerChildren: 0.05 } },
    };
    const itemVariants = {
        hidden: { opacity: 1, y: 0 },
        visible: { opacity: 1, y: 0 },
    };

    return (
        <AppLayout title="Beranda">
            {/* ── Hero Section ── */}
            <section className="relative bg-mesh-gradient pt-32 pb-24 overflow-hidden">
                <div className="h-24" />

                {/* Decorative blobs */}
                <motion.div
                    className="absolute -top-32 -right-32 w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-40"
                    animate={{ scale: [1, 1.15, 1], rotate: [0, 20, 0] }}
                    transition={{ duration: 12, repeat: Infinity, ease: 'easeInOut' }}
                />
                <motion.div
                    className="absolute top-24 -left-24 w-72 h-72 bg-secondary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"
                    animate={{ scale: [1, 1.2, 1], rotate: [0, -15, 0] }}
                    transition={{ duration: 10, repeat: Infinity, ease: 'easeInOut', delay: 2 }}
                />
                <motion.div
                    className="absolute bottom-32 left-1/3 w-64 h-64 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20"
                    animate={{ scale: [1, 1.1, 1], y: [0, -20, 0] }}
                    transition={{ duration: 8, repeat: Infinity, ease: 'easeInOut', delay: 4 }}
                />

                <motion.div
                    className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10"
                    variants={containerVariants}
                    initial="hidden"
                    animate="visible"
                >
                    <motion.div
                        variants={itemVariants}
                        className="inline-flex items-center space-x-2 bg-white/70 backdrop-blur-md px-5 py-2.5 rounded-full mb-8 shadow-sm border border-white/50 text-primary-700 font-bold text-sm"
                    >
                        <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse" />
                        <span>Portal Resmi Kabupaten Layak Anak Konawe</span>
                    </motion.div>

                    <motion.h1
                        variants={itemVariants}
                        className="text-5xl md:text-7xl font-black mb-6 tracking-tight text-gray-900 leading-[1.08]"
                    >
                        Bersama Wujudkan{' '}
                        <br className="hidden md:block" />
                        <span className="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 via-primary-500 to-secondary-500">
                            Kabupaten Layak Anak
                        </span>
                    </motion.h1>

                    <motion.p
                        variants={itemVariants}
                        className="text-xl md:text-2xl text-gray-600 mb-12 max-w-3xl mx-auto font-medium leading-relaxed"
                    >
                        Melindungi hak, mendengarkan suara, dan mewujudkan masa depan cerah{' '}
                        bagi seluruh anak di Konawe.
                    </motion.p>

                    <motion.div variants={itemVariants} className="flex flex-col sm:flex-row justify-center gap-4">
                        <Link
                            href="/layanan"
                            className="bg-primary-600 text-white hover:bg-primary-700 font-black py-4 px-9 rounded-full shadow-lg shadow-primary-500/30 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl text-lg"
                        >
                            Cari Layanan Publik
                        </Link>
                        <Link
                            href="/tentang-kla"
                            className="bg-white text-gray-900 hover:text-primary-600 font-black py-4 px-9 rounded-full shadow-lg border border-gray-100 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl text-lg"
                        >
                            Tentang KLA
                        </Link>
                        <Link
                            href="/pengaduan"
                            className="bg-white text-gray-900 hover:text-secondary-600 font-black py-4 px-8 rounded-full shadow-lg border border-gray-100 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl text-lg flex items-center justify-center space-x-2"
                        >
                            <svg className="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Lapor &amp; Pengaduan</span>
                        </Link>
                    </motion.div>

                    {/* Hero stats bar */}
                    <motion.div
                        variants={itemVariants}
                        className="mt-16 inline-flex items-center divide-x divide-white/30 bg-white/60 backdrop-blur-md rounded-2xl border border-white/50 shadow-lg overflow-hidden"
                    >
                        {[
                            { value: '5', label: 'Klaster KLA' },
                            { value: stats.services + '+', label: 'Layanan' },
                            { value: stats.facilities + '+', label: 'Fasilitas' },
                            { value: '2024', label: 'Evaluasi' },
                        ].map((item, i) => (
                            <div key={i} className="px-6 py-4 text-center">
                                <div className="text-2xl font-black text-primary-700">{item.value}</div>
                                <div className="text-xs font-bold text-gray-500 uppercase tracking-wider">{item.label}</div>
                            </div>
                        ))}
                    </motion.div>
                </motion.div>

                {/* Bottom Wave */}
                <div className="absolute bottom-0 left-0 w-full overflow-hidden leading-none translate-y-[1px]">
                    <svg className="relative block w-full h-12 md:h-24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,117.8,200.5,107.5C241.6,101.4,282.7,85.2,321.39,56.44Z" className="fill-slate-50" />
                    </svg>
                </div>
            </section>

            {/* ── Stats Cards ── */}
            <section className="py-12 relative z-20 -mt-6">
                <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {[
                            {
                                icon: (
                                    <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                ),
                                iconBg: 'bg-primary-100 text-primary-600',
                                value: stats.services,
                                label: 'Layanan KLA',
                                desc: 'Tersedia untuk masyarakat',
                            },
                            {
                                icon: (
                                    <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                ),
                                iconBg: 'bg-secondary-100 text-secondary-600',
                                value: stats.facilities,
                                label: 'Fasilitas Ramah Anak',
                                desc: 'Terverifikasi di Konawe',
                            },
                            {
                                icon: (
                                    <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                ),
                                iconBg: 'bg-green-100 text-green-600',
                                value: null,
                                label: 'Sistem Laporan',
                                desc: 'Aman & Terpercaya',
                                staticText: 'Aman',
                            },
                        ].map((stat, i) => (
                            <motion.div
                                key={i}
                                initial={{ opacity: 0, y: 20 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.5, delay: i * 0.1 }}
                                className="bg-white rounded-3xl p-8 shadow-xl shadow-gray-200/50 hover:-translate-y-1 transition-all duration-300 border border-gray-100 flex items-center space-x-6"
                            >
                                <div className={`w-16 h-16 rounded-2xl ${stat.iconBg} flex items-center justify-center flex-shrink-0`}>
                                    {stat.icon}
                                </div>
                                <div>
                                    {stat.staticText ? (
                                        <div className="text-4xl font-black text-gray-900 mb-0.5">{stat.staticText}</div>
                                    ) : (
                                        <AnimatedCounter target={stat.value ?? 0} />
                                    )}
                                    <div className="text-gray-800 font-bold text-sm">{stat.label}</div>
                                    <div className="text-gray-400 font-medium text-xs mt-0.5">{stat.desc}</div>
                                </div>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── 5 Klaster KLA Section ── */}
            <section className="py-24 bg-gradient-to-b from-slate-50 to-white relative overflow-hidden">
                {/* Background decoration */}
                <div className="absolute inset-0 pointer-events-none">
                    <div className="absolute top-0 left-1/4 w-96 h-96 bg-primary-100 rounded-full filter blur-3xl opacity-30" />
                    <div className="absolute bottom-0 right-1/4 w-80 h-80 bg-secondary-100 rounded-full filter blur-3xl opacity-20" />
                </div>

                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    {/* Section Header */}
                    <motion.div
                        initial={{ opacity: 0, y: 24 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.6 }}
                        className="text-center max-w-3xl mx-auto mb-16"
                    >
                        <div className="inline-flex items-center space-x-2 bg-primary-100 text-primary-700 px-5 py-2 rounded-full text-sm font-bold mb-6 border border-primary-200">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Kerangka KLA Nasional</span>
                        </div>
                        <h2 className="text-4xl md:text-5xl font-black text-gray-900 mb-5 leading-tight">
                            5 Klaster{' '}
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-secondary-500">
                                Kabupaten Layak Anak
                            </span>
                        </h2>
                        <p className="text-lg text-gray-600 font-medium leading-relaxed">
                            KLA dibangun atas lima klaster pemenuhan hak dan perlindungan anak yang saling bersinergi, 
                            menciptakan ekosistem yang aman, sehat, dan tumbuh bagi setiap anak.
                        </p>
                    </motion.div>

                    {/* Klaster Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        {klaKlusters.slice(0, 3).map((klaster, i) => (
                            <KlasterCard key={klaster.number} klaster={klaster} index={i} />
                        ))}
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                        {klaKlusters.slice(3).map((klaster, i) => (
                            <KlasterCard key={klaster.number} klaster={klaster} index={i + 3} />
                        ))}
                    </div>

                    {/* CTA below clusters */}
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5, delay: 0.4 }}
                        className="text-center mt-14"
                    >
                        <Link
                            href="/tentang-kla"
                            className="inline-flex items-center space-x-2 bg-primary-600 text-white hover:bg-primary-700 font-bold py-3.5 px-8 rounded-full shadow-lg shadow-primary-500/25 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl"
                        >
                            <span>Pelajari Lebih Lanjut tentang KLA</span>
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </motion.div>
                </div>
            </section>

            {/* ── Participasi CTA ── */}
            <section className="py-16">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5 }}
                        className="text-center max-w-2xl mx-auto mb-12"
                    >
                        <h2 className="text-4xl font-black text-gray-900 mb-4">Mari Berpartisipasi!</h2>
                        <p className="text-lg text-gray-600">
                            Kami mendengar suaramu. Sampaikan aspirasimu atau laporkan pelanggaran hak anak dengan aman.
                        </p>
                    </motion.div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                        {/* Aspirasi Card */}
                        <motion.div
                            initial={{ opacity: 0, scale: 0.96 }}
                            whileInView={{ opacity: 1, scale: 1 }}
                            viewport={{ once: true }}
                            transition={{ duration: 0.5 }}
                            whileHover={{ scale: 1.02 }}
                        >
                            <Link href="/aspirasi" className="group block relative rounded-3xl overflow-hidden">
                                <div className="absolute inset-0 bg-gradient-to-br from-primary-500 to-cyan-500 opacity-90" />
                                <div className="absolute -right-10 -bottom-10 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500" />
                                <div className="relative p-9 flex items-center">
                                    <div className="flex-grow">
                                        <h3 className="text-2xl font-black text-white mb-2">Sampaikan Aspirasi</h3>
                                        <p className="text-primary-100 font-medium">
                                            Suaramu penting untuk kemajuan KLA Konawe.
                                        </p>
                                    </div>
                                    <div className="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-white backdrop-blur-md flex-shrink-0 ml-6 group-hover:translate-x-2 transition-transform">
                                        <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </Link>
                        </motion.div>

                        {/* Pengaduan Card */}
                        <motion.div
                            initial={{ opacity: 0, scale: 0.96 }}
                            whileInView={{ opacity: 1, scale: 1 }}
                            viewport={{ once: true }}
                            transition={{ duration: 0.5, delay: 0.1 }}
                            whileHover={{ scale: 1.02 }}
                        >
                            <Link href="/pengaduan" className="group block relative rounded-3xl overflow-hidden">
                                <div className="absolute inset-0 bg-gradient-to-br from-secondary-500 to-red-500 opacity-90" />
                                <div className="absolute -left-10 -top-10 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500" />
                                <div className="relative p-9 flex items-center">
                                    <div className="flex-grow">
                                        <h3 className="text-2xl font-black text-white mb-2">Pengaduan</h3>
                                        <p className="text-red-100 font-medium">
                                            Lapor secara rahasia jika menemukan pelanggaran hak anak.
                                        </p>
                                    </div>
                                    <div className="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-white backdrop-blur-md flex-shrink-0 ml-6 group-hover:translate-x-2 transition-transform">
                                        <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </div>
                                </div>
                            </Link>
                        </motion.div>
                    </div>
                </div>
            </section>

            {/* ── Agenda ── */}
            <section className="py-16 bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-col sm:flex-row justify-between items-end mb-10">
                        <motion.div
                            initial={{ opacity: 0, x: -20 }}
                            whileInView={{ opacity: 1, x: 0 }}
                            viewport={{ once: true }}
                            transition={{ duration: 0.5 }}
                        >
                            <h2 className="text-3xl font-black text-gray-900">Agenda Kegiatan</h2>
                            <p className="text-gray-600 font-medium mt-2">Jadwal kegiatan terkini di Konawe</p>
                        </motion.div>
                    </div>

                    {upcomingEvents.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            {upcomingEvents.map((agenda, i) => (
                                <AgendaCard key={i} agenda={agenda} index={i} />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-12 bg-white rounded-3xl border border-gray-100">
                            <div className="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p className="text-gray-500 font-medium">Belum ada agenda kegiatan terdekat.</p>
                        </div>
                    )}
                </div>
            </section>

            {/* ── Latest News ── */}
            <section className="py-16">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-col sm:flex-row justify-between items-end mb-10">
                        <motion.div
                            initial={{ opacity: 0, x: -20 }}
                            whileInView={{ opacity: 1, x: 0 }}
                            viewport={{ once: true }}
                            transition={{ duration: 0.5 }}
                        >
                            <h2 className="text-3xl font-black text-gray-900">Kabar Terbaru</h2>
                            <p className="text-gray-600 font-medium mt-2">Berita terkini seputar kegiatan KLA</p>
                        </motion.div>
                        <Link
                            href="/berita"
                            className="mt-4 sm:mt-0 text-primary-600 font-bold hover:text-primary-800 bg-primary-50 px-6 py-2 rounded-full hover:bg-primary-100 transition"
                        >
                            Lihat Semua Berita
                        </Link>
                    </div>

                    {latestNews.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            {latestNews.map((news, i) => (
                                <NewsCard key={news.id} news={news} index={i} />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-12 bg-white rounded-3xl border border-gray-100">
                            <p className="text-gray-500 font-medium">Belum ada kabar terbaru.</p>
                        </div>
                    )}
                </div>
            </section>
        </AppLayout>
    );
}
