import { Link, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { useEffect, useState } from 'react';

const navLinks = [
    { href: '/', label: 'Beranda' },
    { href: '/tentang-kla', label: 'Tentang KLA' },
    { href: '/layanan', label: 'Layanan' },
    { href: '/fasilitas', label: 'Fasilitas' },
    { href: '/berita', label: 'Berita' },
    { href: '/publikasi', label: 'Publikasi' },
];

export default function Navbar() {
    const { url } = usePage();
    const [open, setOpen] = useState(false);
    const [scrolled, setScrolled] = useState(() => typeof window !== 'undefined' && window.scrollY > 15);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 15);
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    // Close mobile menu on route change
    useEffect(() => {
        setOpen(false);
    }, [url]);

    return (
        <div className="fixed w-full top-0 z-50 px-4 sm:px-6 lg:px-8 pt-3 pb-1.5 transition-all duration-200">
            <nav
                className={`max-w-7xl mx-auto rounded-2xl lg:rounded-full shadow-lg border border-white/40 transition-all duration-200 ${
                    scrolled
                        ? 'bg-white/95 backdrop-blur-xl shadow-gray-200/60'
                        : 'bg-white/85 backdrop-blur-md'
                }`}
            >
                <div className="px-5 lg:px-6 h-16 flex items-center justify-between">
                    {/* Logo */}
                    <Link href="/" className="flex-shrink-0 flex items-center space-x-3 group">
                        <img
                            src="/images/logo_konawe.png"
                            alt="Logo Konawe"
                            className="w-10 h-10 object-contain group-hover:scale-110 transition-transform drop-shadow-md"
                        />
                        <span className="font-extrabold text-xl text-primary-800 hidden sm:block tracking-tight">
                            KLA Konawe
                        </span>
                    </Link>

                    {/* Desktop Nav */}
                    <div className="hidden lg:flex items-center space-x-1">
                        {navLinks.map((link) => {
                            const isActive = url === link.href || (link.href !== '/' && url.startsWith(link.href));
                            return (
                                <Link
                                    key={link.href}
                                    href={link.href}
                                    prefetch="hover"
                                    className={`px-4 py-2 rounded-full font-bold transition text-sm ${
                                        isActive
                                            ? 'bg-primary-100 text-primary-700'
                                            : 'text-gray-700 hover:text-primary-600 hover:bg-white/60'
                                    }`}
                                >
                                    {link.label}
                                </Link>
                            );
                        })}
                    </div>

                    {/* Desktop CTA */}
                    <div className="hidden lg:flex items-center space-x-3">
                        <Link
                            href="/pengaduan"
                            className="bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 px-5 py-2.5 rounded-full font-bold shadow-md flex items-center space-x-1.5 text-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Lapor</span>
                        </Link>
                    </div>

                    {/* Mobile Hamburger */}
                    <button
                        onClick={() => setOpen(!open)}
                        className="lg:hidden inline-flex items-center justify-center p-2 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-white/60 focus:outline-none transition"
                        aria-label="Buka menu navigasi"
                    >
                        <motion.div animate={open ? 'open' : 'closed'} className="relative w-6 h-6">
                            <motion.span
                                className="absolute left-0 top-1 w-full h-0.5 bg-current rounded-full origin-center"
                                variants={{ open: { rotate: 45, y: 7 }, closed: { rotate: 0, y: 0 } }}
                                transition={{ duration: 0.2 }}
                            />
                            <motion.span
                                className="absolute left-0 top-3 w-full h-0.5 bg-current rounded-full"
                                variants={{ open: { opacity: 0 }, closed: { opacity: 1 } }}
                                transition={{ duration: 0.2 }}
                            />
                            <motion.span
                                className="absolute left-0 top-5 w-full h-0.5 bg-current rounded-full origin-center"
                                variants={{ open: { rotate: -45, y: -7 }, closed: { rotate: 0, y: 0 } }}
                                transition={{ duration: 0.2 }}
                            />
                        </motion.div>
                    </button>
                </div>

                {/* Mobile Menu */}
                <AnimatePresence>
                    {open && (
                        <motion.div
                            initial={{ height: 0, opacity: 0 }}
                            animate={{ height: 'auto', opacity: 1 }}
                            exit={{ height: 0, opacity: 0 }}
                            transition={{ duration: 0.25, ease: 'easeInOut' }}
                            className="lg:hidden overflow-hidden border-t border-white/30"
                        >
                            <div className="pt-3 pb-4 px-4 space-y-1">
                                {navLinks.map((link) => {
                                    const isActive = url === link.href || (link.href !== '/' && url.startsWith(link.href));
                                    return (
                                        <Link
                                            key={link.href}
                                            href={link.href}
                                            prefetch="hover"
                                            className={`block px-4 py-3 rounded-xl font-bold transition ${
                                                isActive
                                                    ? 'bg-primary-100 text-primary-700'
                                                    : 'text-gray-700 hover:text-primary-600 hover:bg-white/50'
                                            }`}
                                        >
                                            {link.label}
                                        </Link>
                                    );
                                })}
                                <div className="pt-3 border-t border-white/20">
                                    <Link
                                        href="/pengaduan"
                                        className="block text-center bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-3 rounded-xl font-bold shadow-md"
                                    >
                                        Lapor &amp; Pengaduan
                                    </Link>
                                </div>
                            </div>
                        </motion.div>
                    )}
                </AnimatePresence>
            </nav>
        </div>
    );
}
