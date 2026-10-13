import { Head } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { type ReactNode } from 'react';
import Footer from '../components/Footer';
import Navbar from '../components/Navbar';

interface AppLayoutProps {
    title: string;
    children: ReactNode;
}

const pageVariants = {
    initial: { opacity: 0, y: 16 },
    animate: { opacity: 1, y: 0 },
    exit: { opacity: 0, y: -8 },
};

export default function AppLayout({ title, children }: AppLayoutProps) {
    return (
        <>
            <Head title={title} />
            <div className="min-h-screen bg-slate-50 text-gray-900">
                <Navbar />
                <AnimatePresence mode="wait">
                    <motion.main
                        key={title}
                        initial="initial"
                        animate="animate"
                        exit="exit"
                        variants={pageVariants}
                        transition={{ duration: 0.3, ease: [0.25, 0.46, 0.45, 0.94] }}
                    >
                        {children}
                    </motion.main>
                </AnimatePresence>
                <Footer />
            </div>
        </>
    );
}
