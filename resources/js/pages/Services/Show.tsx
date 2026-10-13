import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AppLayout from '../../layouts/AppLayout';
import { type Service } from '../../types';

interface ServiceShowProps {
    service: Service;
}

export default function ServiceShow({ service }: ServiceShowProps) {
    return (
        <AppLayout title={service.name}>
            <div className="bg-gradient-to-r from-primary-500 to-primary-700 pt-40 pb-16 text-white relative overflow-hidden">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }}>
                        <Link
                            href="/layanan"
                            className="inline-flex items-center text-primary-200 hover:text-white font-bold mb-6 transition"
                        >
                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Direktori Layanan
                        </Link>
                        <h1 className="text-4xl md:text-5xl font-black tracking-tight">{service.name}</h1>
                        {service.contact_info && (
                            <div className="mt-4 flex items-center text-primary-100 font-medium">
                                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {service.contact_info}
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
                    {service.image && (
                        <div className="h-72 bg-gray-50 flex items-center justify-center overflow-hidden">
                            <img
                                src={`/storage/${service.image}`}
                                alt={service.name}
                                className="w-full h-full object-contain"
                            />
                        </div>
                    )}
                    <div className="p-8 md:p-12">
                        <div
                            className="prose prose-lg max-w-none text-gray-700 font-medium leading-relaxed"
                            dangerouslySetInnerHTML={{ __html: service.description ?? '<p>Keterangan layanan sedang disiapkan.</p>' }}
                        />
                    </div>
                </motion.div>
            </div>
        </AppLayout>
    );
}
