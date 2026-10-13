import { Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useEffect, useState } from 'react';
import AppLayout from '../../layouts/AppLayout';
import { type PaginatedData, type Service } from '../../types';

interface ServiceIndexProps {
    services: PaginatedData<Service>;
    search?: string;
}

function ServiceCard({ service, index }: { service: Service; index: number }) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 24 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.45, delay: (index % 6) * 0.07 }}
        >
            <Link
                href={`/layanan/${service.slug}`}
                className="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 flex flex-col h-full overflow-hidden block"
            >
                <div className="p-8 flex-grow">
                    {service.image ? (
                        <div className="h-48 -mx-8 -mt-8 mb-6 overflow-hidden bg-gray-50 flex items-center justify-center">
                            <img
                                src={`/storage/${service.image}`}
                                alt={service.name}
                                className="w-full h-full object-contain group-hover:scale-105 transition duration-500"
                            />
                        </div>
                    ) : (
                        <div className="w-12 h-12 rounded-2xl bg-primary-100 text-primary-600 flex items-center justify-center mb-5 group-hover:bg-primary-500 group-hover:text-white transition">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    )}
                    <h3 className="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition leading-tight">
                        {service.name}
                    </h3>
                    <p className="text-gray-600 line-clamp-3 font-medium">
                        {service.description?.replace(/<[^>]*>/g, '') ?? ''}
                    </p>
                    {service.contact_info && (
                        <div className="mt-4 flex items-center text-sm text-gray-500 font-medium">
                            <svg className="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {service.contact_info}
                        </div>
                    )}
                </div>
                <div className="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <span className="text-primary-600 font-bold text-sm">Lihat Detail</span>
                    <svg className="w-5 h-5 text-primary-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </Link>
        </motion.div>
    );
}

export default function ServicesIndex({ services, search = '' }: ServiceIndexProps) {
    const [query, setQuery] = useState(search);
    const [isSearching, setIsSearching] = useState(false);

    useEffect(() => {
        const timer = setTimeout(() => {
            if (query !== search) {
                setIsSearching(true);
                router.get('/layanan', query ? { search: query } : {}, {
                    preserveState: true,
                    replace: true,
                    onFinish: () => setIsSearching(false),
                });
            }
        }, 400);
        return () => clearTimeout(timer);
    }, [query]);

    return (
        <AppLayout title="Direktori Layanan Publik">
            {/* Header */}
            <div className="bg-gradient-to-r from-primary-500 to-primary-700 pt-32 pb-20 text-white text-center relative overflow-hidden">
                <div className="h-24" />
                <motion.div
                    className="absolute top-6 left-10 w-24 h-24 bg-white/10 rounded-full blur-2xl"
                    animate={{ scale: [1, 1.3, 1] }}
                    transition={{ duration: 8, repeat: Infinity }}
                />
                <motion.div
                    className="absolute bottom-6 right-10 w-32 h-32 bg-secondary-400/20 rounded-full blur-xl"
                    animate={{ scale: [1, 1.2, 1] }}
                    transition={{ duration: 6, repeat: Infinity, delay: 1 }}
                />
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6 }}
                    className="relative z-10"
                >
                    <h1 className="text-4xl md:text-5xl font-black mb-4 tracking-tight">Direktori Layanan</h1>
                    <p className="text-xl text-primary-100 max-w-2xl mx-auto font-medium mb-8">
                        Temukan berbagai layanan publik yang ramah anak di Kabupaten Konawe.
                    </p>

                    {/* Live search */}
                    <div className="max-w-xl mx-auto flex bg-white shadow-2xl rounded-full overflow-hidden">
                        <input
                            type="text"
                            value={query}
                            onChange={(e) => setQuery(e.target.value)}
                            placeholder="Cari layanan (misal: kesehatan, pendidikan)..."
                            className="flex-grow border-0 px-6 py-4 text-gray-900 focus:ring-0 font-medium text-lg outline-none"
                            id="service-search"
                        />
                        <div className="bg-secondary-500 text-white font-bold py-4 px-8 flex items-center gap-2">
                            {isSearching ? (
                                <svg className="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            ) : (
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            )}
                            <span>Cari</span>
                        </div>
                    </div>
                </motion.div>
            </div>

            {/* Grid */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {services.data.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {services.data.map((service, i) => (
                            <ServiceCard key={service.id} service={service} index={i} />
                        ))}
                    </div>
                ) : (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        className="text-center py-16 bg-white rounded-3xl border border-gray-100"
                    >
                        <div className="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                            <svg className="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 className="text-xl font-bold text-gray-900 mb-2">Layanan Tidak Ditemukan</h3>
                        <p className="text-gray-500 font-medium">Coba gunakan kata kunci pencarian yang berbeda.</p>
                    </motion.div>
                )}

                {/* Pagination */}
                {services.last_page > 1 && (
                    <div className="mt-12 flex justify-center gap-2 flex-wrap">
                        {services.links.map((link, i) => (
                            <button
                                key={i}
                                onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
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
