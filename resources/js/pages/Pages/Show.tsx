import { motion } from 'framer-motion';
import AppLayout from '../../layouts/AppLayout';
import { type Page } from '../../types';

interface PageShowProps {
    page: Page;
}

export default function PageShow({ page }: PageShowProps) {
    return (
        <AppLayout title={page.title}>
            {/* Header */}
            <div className="bg-mesh-gradient pt-40 pb-16 relative overflow-hidden">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <motion.div
                        initial={{ opacity: 0, y: 24 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.6 }}
                        className="text-center"
                    >
                        {page.excerpt && (
                            <p className="text-primary-600 font-bold text-sm uppercase tracking-wider mb-4">{page.excerpt}</p>
                        )}
                        <h1 className="text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight">
                            {page.title}
                        </h1>
                    </motion.div>
                </div>
            </div>

            {/* Content */}
            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                >
                    <div
                        className="font-sans"
                        dangerouslySetInnerHTML={{ __html: page.content ?? '<p>Konten halaman ini sedang disiapkan.</p>' }}
                    />
                </motion.div>
            </div>
        </AppLayout>
    );
}
