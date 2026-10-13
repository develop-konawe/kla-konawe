import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AppLayout from '../../layouts/AppLayout';
import { type Facility } from '../../types';

interface FacilityShowProps {
    facility: Facility;
}

export default function FacilityShow({ facility }: FacilityShowProps) {
    return (
        <AppLayout title={facility.name}>
            <div className="bg-gradient-to-r from-green-500 to-primary-600 pt-40 pb-16 text-white relative overflow-hidden">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }}>
                        <Link
                            href="/fasilitas"
                            className="inline-flex items-center text-green-200 hover:text-white font-bold mb-6 transition"
                        >
                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Fasilitas
                        </Link>
                        {facility.is_child_friendly && (
                            <span className="inline-flex items-center gap-1 bg-green-400/30 text-green-100 text-xs font-bold px-3 py-1 rounded-full mb-4">
                                ✓ Fasilitas Ramah Anak
                            </span>
                        )}
                        <h1 className="text-4xl md:text-5xl font-black tracking-tight">{facility.name}</h1>
                        {facility.address && (
                            <div className="mt-4 flex items-center text-green-100 font-medium gap-2">
                                <svg className="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {facility.address}
                            </div>
                        )}
                    </motion.div>
                </div>
            </div>

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                    className="bg-white rounded-3xl shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden"
                >
                    {facility.image && (
                        <div className="h-72 overflow-hidden">
                            <img
                                src={`/storage/${facility.image}`}
                                alt={facility.name}
                                className="w-full h-full object-cover"
                            />
                        </div>
                    )}
                    <div className="p-8 md:p-12">
                        {facility.phone && (
                            <div className="flex items-center gap-2 mb-6 text-gray-600 font-medium">
                                <svg className="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{facility.phone}</span>
                            </div>
                        )}
                        <div
                            className="prose prose-lg max-w-none text-gray-700 font-medium leading-relaxed"
                            dangerouslySetInnerHTML={{ __html: facility.description ?? '<p>Informasi fasilitas sedang disiapkan.</p>' }}
                        />
                    </div>
                </motion.div>
            </div>
        </AppLayout>
    );
}
