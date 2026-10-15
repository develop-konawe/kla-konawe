import { Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useEffect, useState } from 'react';
import AppLayout from '../../layouts/AppLayout';
import { type Facility, type PaginatedData } from '../../types';

interface FacilityIndexProps {
    facilities: PaginatedData<Facility>;
    search?: string;
}

function FacilityCard({ facility, index }: { facility: Facility; index: number }) {
    const imageUrl = facility.image?.startsWith('http://') || facility.image?.startsWith('https://')
        ? facility.image
        : facility.image ? `/storage/${facility.image}` : null;

    return (
        <motion.div
            initial={{ opacity: 0, y: 24 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.45, delay: (index % 6) * 0.07 }}
        >
            <Link
                href={`/fasilitas/${facility.slug}`}
                className="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 flex flex-col h-full overflow-hidden block"
            >
                {imageUrl ? (
                    <div className="h-48 overflow-hidden">
                        <img
                            src={imageUrl}
                            alt={facility.name}
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                    </div>
                ) : (
                    <div className="h-48 bg-gradient-to-br from-green-50 to-primary-50 flex items-center justify-center text-primary-200">
                        <svg className="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                )}
                <div className="p-6 flex-grow">
                    {facility.is_child_friendly && (
                        <span className="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs font-bold px-3 py-1 rounded-full mb-3">
                            <svg className="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                            </svg>
                            Ramah Anak
                        </span>
                    )}
                    <h3 className="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition">
                        {facility.name}
                    </h3>
                    {facility.address && (
                        <div className="flex items-start text-sm text-gray-500 font-medium mb-3">
                            <svg className="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span className="line-clamp-2">{facility.address}</span>
                        </div>
                    )}
                    <p className="text-gray-600 line-clamp-2 font-medium text-sm">
                        {facility.description?.replace(/<[^>]*>/g, '') ?? ''}
                    </p>
                </div>
                <div className="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <span className="text-primary-600 font-bold text-sm">Lihat Detail</span>
                    <svg className="w-5 h-5 text-primary-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </Link>
        </motion.div>
    );
}

export default function FacilitiesIndex({ facilities, search = '' }: FacilityIndexProps) {
    const [query, setQuery] = useState(search);
    const [isSearching, setIsSearching] = useState(false);

    useEffect(() => {
        const timer = setTimeout(() => {
            if (query !== search) {
                setIsSearching(true);
                router.get('/fasilitas', query ? { search: query } : {}, {
                    preserveState: true,
                    replace: true,
                    onFinish: () => setIsSearching(false),
                });
            }
        }, 400);
        return () => clearTimeout(timer);
    }, [query]);

    return (
        <AppLayout title="Fasilitas Ramah Anak">
            <div className="bg-gradient-to-r from-green-500 to-primary-600 pt-32 pb-20 text-white text-center relative overflow-hidden">
                <div className="h-24" />
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6 }}
                    className="relative z-10"
                >
                    <h1 className="text-4xl md:text-5xl font-black mb-4 tracking-tight">Fasilitas Ramah Anak</h1>
                    <p className="text-xl text-green-100 max-w-2xl mx-auto font-medium mb-8">
                        Temukan fasilitas yang aman dan nyaman untuk anak-anak di Kabupaten Konawe.
                    </p>
                    <div className="max-w-xl mx-auto flex bg-white shadow-2xl rounded-full overflow-hidden">
                        <input
                            type="text"
                            value={query}
                            onChange={(e) => setQuery(e.target.value)}
                            placeholder="Cari fasilitas (nama, alamat)..."
                            className="flex-grow border-0 px-6 py-4 text-gray-900 focus:ring-0 font-medium text-lg outline-none"
                            id="facility-search"
                        />
                        <div className="bg-primary-500 text-white font-bold py-4 px-8 flex items-center gap-2">
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
                        </div>
                    </div>
                </motion.div>
            </div>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {facilities.data.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {facilities.data.map((facility, i) => (
                            <FacilityCard key={facility.id} facility={facility} index={i} />
                        ))}
                    </div>
                ) : (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        className="text-center py-16 bg-white rounded-3xl border border-gray-100"
                    >
                        <p className="text-gray-500 font-medium text-lg">Fasilitas tidak ditemukan.</p>
                    </motion.div>
                )}

                {facilities.last_page > 1 && (
                    <div className="mt-12 flex justify-center gap-2 flex-wrap">
                        {facilities.links.map((link, i) => (
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
