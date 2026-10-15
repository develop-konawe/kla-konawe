import { Head, router } from '@inertiajs/react';
import { type ReactNode, useEffect, useState } from 'react';
import Footer from '../components/Footer';
import Navbar from '../components/Navbar';

interface AppLayoutProps {
    title: string;
    children: ReactNode;
}

export default function AppLayout({ title, children }: AppLayoutProps) {
    const [isNavigating, setIsNavigating] = useState(false);

    useEffect(() => {
        const removeStart = router.on('start', () => setIsNavigating(true));
        const removeFinish = router.on('finish', () => setIsNavigating(false));
        const removeError = router.on('error', () => setIsNavigating(false));

        return () => {
            removeStart();
            removeFinish();
            removeError();
        };
    }, []);

    return (
        <>
            <Head title={title} />
            <div className="min-h-screen bg-slate-50 text-gray-900 flex flex-col relative selection:bg-primary-500 selection:text-white">
                {/* Instant Top Navigation Progress Bar */}
                {isNavigating && (
                    <div className="fixed top-0 left-0 right-0 z-[100] h-1 overflow-hidden bg-primary-100/60 backdrop-blur-sm">
                        <div className="h-full bg-gradient-to-r from-primary-600 via-emerald-400 to-indigo-600 animate-pulse w-full shadow-sm" />
                    </div>
                )}

                <Navbar />

                {/* Main Content Area - Solid 100% opacity without blinking/white flashes */}
                <main className="flex-grow">
                    {children}
                </main>

                <Footer />
            </div>
        </>
    );
}
