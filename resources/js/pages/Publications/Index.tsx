import { router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useMemo, useState } from 'react';
import AppLayout from '../../layouts/AppLayout';
import { type PaginatedData, type Publication, type Statistic } from '../../types';

interface PublicationIndexProps {
    publications: PaginatedData<Publication>;
    statistics?: Statistic[];
}

function PublicationCard({ pub, index }: { pub: Publication; index: number }) {
    const date = pub.published_at ? new Date(pub.published_at) : null;
    const formatted = date?.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

    return (
        <motion.div
            initial={{ opacity: 0, y: 24 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.45, delay: (index % 6) * 0.07 }}
            className="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 p-6 flex items-start gap-5"
        >
            {/* Icon */}
            <div className="w-14 h-14 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center flex-shrink-0 group-hover:bg-red-500 group-hover:text-white transition">
                <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>

            <div className="flex-grow min-w-0">
                <h3 className="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition line-clamp-2">
                    {pub.title}
                </h3>
                {pub.description && (
                    <p className="text-gray-600 text-sm font-medium line-clamp-2 mb-3">{pub.description}</p>
                )}
                <div className="flex items-center justify-between">
                    {formatted && <span className="text-xs text-gray-400 font-bold">{formatted}</span>}
                    {pub.file_url && (
                        <a
                            href={pub.file_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1.5 bg-primary-50 text-primary-700 hover:bg-primary-600 hover:text-white px-4 py-1.5 rounded-full text-sm font-bold transition"
                            onClick={(e) => e.stopPropagation()}
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Unduh
                        </a>
                    )}
                </div>
            </div>
        </motion.div>
    );
}

function StatisticCard({ stat, index }: { stat: Statistic; index: number }) {
    const getKlasterColor = (cat?: string | null) => {
        if (!cat) return { badge: 'bg-emerald-50 text-emerald-700 border-emerald-200', text: 'text-emerald-600' };
        if (cat.includes('1')) return { badge: 'bg-emerald-50 text-emerald-700 border-emerald-200', text: 'text-emerald-600' };
        if (cat.includes('2')) return { badge: 'bg-amber-50 text-amber-700 border-amber-200', text: 'text-amber-600' };
        if (cat.includes('3')) return { badge: 'bg-rose-50 text-rose-700 border-rose-200', text: 'text-rose-600' };
        if (cat.includes('4')) return { badge: 'bg-blue-50 text-blue-700 border-blue-200', text: 'text-blue-600' };
        if (cat.includes('5')) return { badge: 'bg-purple-50 text-purple-700 border-purple-200', text: 'text-purple-600' };
        return { badge: 'bg-teal-50 text-teal-700 border-teal-200', text: 'text-teal-600' };
    };

    const color = getKlasterColor(stat.category_id);

    return (
        <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.45, delay: (index % 6) * 0.06 }}
            className="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 flex flex-col justify-between relative overflow-hidden"
        >
            <div className="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-slate-100/60 to-transparent rounded-bl-full pointer-events-none group-hover:scale-125 transition-transform duration-500" />

            <div>
                {/* Badges */}
                <div className="flex items-center justify-between gap-2 mb-3">
                    <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border ${color.badge}`}>
                        {stat.category_id || 'Indikator KLA'}
                    </span>
                    {stat.year && (
                        <span className="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                            Tahun {stat.year}
                        </span>
                    )}
                </div>

                {/* Big Metric Value */}
                <div className="flex items-baseline gap-2 mb-3">
                    <span className={`text-4xl font-black tracking-tight ${color.text}`}>
                        {stat.value}
                    </span>
                    {stat.unit && (
                        <span className="text-sm font-bold text-slate-500">{stat.unit}</span>
                    )}
                </div>

                {/* Title */}
                <h3 className="text-base font-bold text-slate-900 group-hover:text-primary-600 transition-colors leading-snug line-clamp-2 mb-2">
                    {stat.title}
                </h3>
            </div>

            {/* Source OPD & Region */}
            <div className="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
                <div className="flex items-center gap-1.5 truncate">
                    <svg className="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span className="truncate">{stat.source || 'Gugus Tugas KLA Konawe'}</span>
                </div>
                {stat.region_id && (
                    <span className="text-[11px] font-bold text-slate-400 flex-shrink-0">{stat.region_id}</span>
                )}
            </div>
        </motion.div>
    );
}

export default function PublicationsIndex({ publications, statistics = [] }: PublicationIndexProps) {
    const isStatistikDefault = typeof window !== 'undefined' && new URLSearchParams(window.location.search).get('tab') === 'statistik';
    const [activeTab, setActiveTab] = useState<'dokumen' | 'statistik'>(isStatistikDefault ? 'statistik' : 'dokumen');
    const [selectedKlaster, setSelectedKlaster] = useState<string>('all');
    const [searchQuery, setSearchQuery] = useState<string>('');

    const filteredStats = useMemo(() => {
        return statistics.filter((stat) => {
            const matchesKlaster = selectedKlaster === 'all' || (stat.category_id || '').toLowerCase().includes(selectedKlaster.toLowerCase());
            const matchesSearch = !searchQuery || 
                stat.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
                (stat.source || '').toLowerCase().includes(searchQuery.toLowerCase());
            return matchesKlaster && matchesSearch;
        });
    }, [statistics, selectedKlaster, searchQuery]);

    return (
        <AppLayout title="Publikasi & Data KLA">
            <div className="bg-gradient-to-r from-orange-500 to-red-600 pt-32 pb-16 text-white text-center relative overflow-hidden">
                <div className="h-24" />
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6 }}
                    className="relative z-10"
                >
                    <h1 className="text-4xl md:text-5xl font-black mb-4 tracking-tight">Publikasi &amp; Data KLA</h1>
                    <p className="text-xl text-orange-100 max-w-2xl mx-auto font-medium">
                        Dokumen resmi, regulasi, laporan tahunan, dan data capaian indikator KLA Kabupaten Konawe.
                    </p>

                    {/* Tab Navigation */}
                    <div className="mt-8 inline-flex p-1.5 rounded-2xl bg-black/20 backdrop-blur-md border border-white/20">
                        <button
                            onClick={() => setActiveTab('dokumen')}
                            className={`px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 flex items-center gap-2 ${
                                activeTab === 'dokumen'
                                    ? 'bg-white text-gray-900 shadow-md'
                                    : 'text-white/80 hover:text-white hover:bg-white/10'
                            }`}
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Dokumen &amp; Regulasi</span>
                            <span className="px-2 py-0.5 rounded-full text-xs font-black bg-red-100 text-red-700">
                                {publications.total || publications.data.length}
                            </span>
                        </button>
                        <button
                            onClick={() => setActiveTab('statistik')}
                            className={`px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 flex items-center gap-2 ${
                                activeTab === 'statistik'
                                    ? 'bg-white text-gray-900 shadow-md'
                                    : 'text-white/80 hover:text-white hover:bg-white/10'
                            }`}
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Data &amp; Indikator KLA</span>
                            <span className="px-2 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-700">
                                {statistics.length}
                            </span>
                        </button>
                    </div>
                </motion.div>
            </div>

            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                {activeTab === 'dokumen' ? (
                    <div>
                        {publications.data.length > 0 ? (
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {publications.data.map((pub, i) => (
                                    <PublicationCard key={pub.id} pub={pub} index={i} />
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-20 bg-white rounded-3xl border border-gray-100">
                                <p className="text-gray-500 font-medium text-lg">Belum ada publikasi yang tersedia.</p>
                            </div>
                        )}

                        {publications.last_page > 1 && (
                            <div className="mt-12 flex justify-center gap-2 flex-wrap">
                                {publications.links.map((link, i) => (
                                    <button
                                        key={i}
                                        onClick={() => link.url && router.get(link.url)}
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
                    </div>
                ) : (
                    <div>
                        {/* Filter & Search Bar for Statistics */}
                        <div className="mb-10 flex flex-col md:flex-row items-center justify-between gap-4">
                            <div className="flex flex-wrap items-center gap-2">
                                {[
                                    { id: 'all', label: 'Semua' },
                                    { id: '1', label: 'Klaster 1' },
                                    { id: '2', label: 'Klaster 2' },
                                    { id: '3', label: 'Klaster 3' },
                                    { id: '4', label: 'Klaster 4' },
                                    { id: '5', label: 'Klaster 5' },
                                ].map((tab) => (
                                    <button
                                        key={tab.id}
                                        onClick={() => setSelectedKlaster(tab.id)}
                                        className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition ${
                                            selectedKlaster === tab.id
                                                ? 'bg-emerald-600 text-white shadow-sm'
                                                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
                                        }`}
                                    >
                                        {tab.label}
                                    </button>
                                ))}
                            </div>

                            <div className="w-full md:w-72">
                                <input
                                    type="text"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    placeholder="Cari indikator atau OPD..."
                                    className="w-full px-4 py-2 rounded-2xl text-xs sm:text-sm bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"
                                />
                            </div>
                        </div>

                        {filteredStats.length > 0 ? (
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {filteredStats.map((stat, i) => (
                                    <StatisticCard key={stat.id || i} stat={stat} index={i} />
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-20 bg-white rounded-3xl border border-gray-100">
                                <div className="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <p className="text-gray-700 font-bold text-lg mb-1">Tidak Ada Data Indikator</p>
                                <p className="text-gray-400 text-sm">Tidak ditemukan data indikator yang sesuai dengan filter pencarian.</p>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
