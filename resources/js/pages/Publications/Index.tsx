import { router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AppLayout from '../../layouts/AppLayout';
import { type PaginatedData, type Publication } from '../../types';

interface PublicationIndexProps {
    publications: PaginatedData<Publication>;
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

export default function PublicationsIndex({ publications }: PublicationIndexProps) {
    return (
        <AppLayout title="Publikasi & Laporan">
            <div className="bg-gradient-to-r from-orange-500 to-red-600 pt-32 pb-16 text-white text-center relative overflow-hidden">
                <div className="h-24" />
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6 }}
                    className="relative z-10"
                >
                    <h1 className="text-4xl md:text-5xl font-black mb-4 tracking-tight">Publikasi &amp; Laporan</h1>
                    <p className="text-xl text-orange-100 max-w-2xl mx-auto font-medium">
                        Dokumen resmi, laporan tahunan, dan publikasi terkait program KLA Konawe.
                    </p>
                </motion.div>
            </div>

            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
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
        </AppLayout>
    );
}
