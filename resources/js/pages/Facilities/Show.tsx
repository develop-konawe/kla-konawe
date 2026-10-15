import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useState } from 'react';
import AppLayout from '../../layouts/AppLayout';
import { type Facility } from '../../types';

interface FacilityShowProps {
    facility: Facility;
    relatedFacilities?: Facility[];
}

function getImageUrl(path?: string | null): string {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    return path.startsWith('/') ? path : `/storage/${path}`;
}

export default function FacilityShow({ facility, relatedFacilities = [] }: FacilityShowProps) {
    const [copied, setCopied] = useState(false);
    const [addressCopied, setAddressCopied] = useState(false);

    const imageUrl = getImageUrl(facility.image);

    // Coordinate fallback or parsing
    const hasCoordinates = Boolean(facility.coordinates && facility.coordinates.includes(','));
    const mapQuery = hasCoordinates
        ? facility.coordinates
        : encodeURIComponent(`${facility.name}, ${facility.address || 'Konawe'}`);

    const googleMapsEmbedUrl = `https://maps.google.com/maps?q=${mapQuery}&hl=id&z=16&output=embed`;
    const googleMapsDirectionUrl = hasCoordinates
        ? `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(facility.coordinates!)}`
        : `https://www.google.com/maps/search/?api=1&query=${mapQuery}`;

    const handleShare = async () => {
        const shareData = {
            title: `${facility.name} - Fasilitas Ramah Anak Konawe`,
            text: `Kunjungi ${facility.name}, salah satu fasilitas ramah anak terverifikasi di Kabupaten Konawe.`,
            url: window.location.href,
        };

        if (navigator.share) {
            try {
                await navigator.share(shareData);
                return;
            } catch {
                // user cancelled or share failed, fallback to copy
            }
        }

        try {
            await navigator.clipboard.writeText(window.location.href);
            setCopied(true);
            setTimeout(() => setCopied(false), 2500);
        } catch {
            // fallback if clipboard fails
        }
    };

    const handleCopyAddress = async () => {
        if (!facility.address) return;
        try {
            await navigator.clipboard.writeText(facility.address);
            setAddressCopied(true);
            setTimeout(() => setAddressCopied(false), 2500);
        } catch {
            // ignore
        }
    };

    return (
        <AppLayout title={`${facility.name} - Fasilitas Ramah Anak`}>
            {/* HERO SECTION */}
            <div className="relative bg-gradient-to-br from-emerald-600 via-teal-700 to-slate-900 pt-36 pb-20 text-white overflow-hidden">
                {/* Decorative background shapes */}
                <div className="absolute inset-0 opacity-15 pointer-events-none">
                    <div className="absolute -top-24 -left-24 w-96 h-96 bg-emerald-400 rounded-full blur-3xl" />
                    <div className="absolute top-1/2 right-0 w-80 h-80 bg-teal-300 rounded-full blur-3xl" />
                    <div className="absolute -bottom-20 left-1/3 w-72 h-72 bg-emerald-300 rounded-full blur-2xl" />
                </div>

                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <motion.div
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.4 }}
                    >
                        {/* Breadcrumbs */}
                        <nav className="flex items-center gap-2 text-sm text-emerald-200/80 mb-6 flex-wrap">
                            <Link href="/" className="hover:text-white transition">
                                Beranda
                            </Link>
                            <span>/</span>
                            <Link href="/fasilitas" className="hover:text-white transition">
                                Fasilitas Ramah Anak
                            </Link>
                            <span>/</span>
                            <span className="text-white font-medium truncate max-w-xs sm:max-w-md">
                                {facility.name}
                            </span>
                        </nav>

                        {/* Badges Bar */}
                        <div className="flex flex-wrap items-center gap-2.5 mb-4">
                            {facility.is_child_friendly && (
                                <span className="inline-flex items-center gap-1.5 bg-emerald-500/25 border border-emerald-400/40 text-emerald-100 text-xs sm:text-sm font-semibold px-3.5 py-1.5 rounded-full backdrop-blur-sm shadow-sm">
                                    <svg className="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                    </svg>
                                    Fasilitas Ramah Anak Terverifikasi
                                </span>
                            )}
                            {facility.category && (
                                <span className="inline-flex items-center uppercase tracking-wider text-[11px] font-bold bg-white/15 border border-white/20 text-white px-3 py-1.5 rounded-full backdrop-blur-sm">
                                    {facility.category}
                                </span>
                            )}
                            {facility.organization && (
                                <span className="inline-flex items-center text-xs font-medium bg-black/25 text-teal-100 border border-teal-500/30 px-3 py-1.5 rounded-full">
                                    <svg className="w-3.5 h-3.5 mr-1.5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {facility.organization.name}
                                </span>
                            )}
                        </div>

                        {/* Title */}
                        <h1 className="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight mb-4">
                            {facility.name}
                        </h1>

                        {/* Address & Actions preview */}
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-t border-emerald-500/20">
                            {facility.address ? (
                                <div className="flex items-start gap-2 text-emerald-100 text-sm sm:text-base font-medium max-w-2xl">
                                    <svg className="w-5 h-5 text-emerald-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{facility.address}</span>
                                </div>
                            ) : (
                                <div className="text-emerald-200 text-sm">Kabupaten Konawe, Sulawesi Tenggara</div>
                            )}

                            {/* Share button */}
                            <div className="flex items-center gap-2">
                                <button
                                    onClick={handleShare}
                                    type="button"
                                    className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold transition active:scale-95"
                                >
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                    {copied ? 'Tautan Tersalin!' : 'Bagikan'}
                                </button>
                                <a
                                    href={googleMapsDirectionUrl}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white text-sm font-bold shadow-md shadow-emerald-900/30 transition active:scale-95"
                                >
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    Petunjuk Arah
                                </a>
                            </div>
                        </div>
                    </motion.div>
                </div>
            </div>

            {/* MAIN CONTENT BODY */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-20 relative z-20">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    {/* LEFT COLUMN: 8 COLS */}
                    <motion.div
                        initial={{ opacity: 0, y: 24 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5, delay: 0.1 }}
                        className="lg:col-span-8 space-y-8"
                    >
                        {/* FEATURED IMAGE CARD */}
                        <div className="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
                            {imageUrl ? (
                                <div className="relative w-full h-72 sm:h-96 md:h-[420px] bg-slate-900 overflow-hidden group">
                                    <img
                                        src={imageUrl}
                                        alt={facility.name}
                                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    />
                                    <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none" />
                                    <div className="absolute bottom-4 left-4 right-4 flex items-center justify-between text-white text-xs sm:text-sm">
                                        <span className="bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/20 font-medium">
                                            Dokumentasi Fasilitas Ramah Anak
                                        </span>
                                    </div>
                                </div>
                            ) : (
                                <div className="h-72 sm:h-80 bg-gradient-to-br from-emerald-50 via-teal-50 to-slate-100 flex flex-col items-center justify-center text-teal-600 p-8 text-center border-b border-teal-100">
                                    <div className="w-20 h-20 rounded-3xl bg-white shadow-md flex items-center justify-center text-emerald-600 mb-4">
                                        <svg className="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <h4 className="text-lg font-bold text-slate-800">{facility.name}</h4>
                                    <p className="text-sm text-slate-500 max-w-sm mt-1">
                                        Fasilitas publik ramah anak Kabupaten Konawe
                                    </p>
                                </div>
                            )}

                            {/* QUICK HIGHLIGHTS METRICS */}
                            <div className="p-6 sm:p-8 bg-slate-50/60 border-t border-slate-100">
                                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div className="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                        <div className="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                            Status KLA
                                        </div>
                                        <div className="text-sm font-bold text-emerald-600 flex items-center gap-1">
                                            <svg className="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                            </svg>
                                            Ramah Anak
                                        </div>
                                    </div>

                                    <div className="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                        <div className="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                            Kategori
                                        </div>
                                        <div className="text-sm font-bold text-slate-800 capitalize truncate">
                                            {facility.category || 'Fasilitas Umum'}
                                        </div>
                                    </div>

                                    <div className="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                        <div className="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                            Pengelola
                                        </div>
                                        <div className="text-sm font-bold text-slate-800 truncate" title={facility.organization?.name || 'Pemda Konawe'}>
                                            {facility.organization?.name ? facility.organization.name.replace(/^(Dinas|Badan|Bagian|Puskesmas)\s+/i, '') : 'Pemda Konawe'}
                                        </div>
                                    </div>

                                    <div className="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                        <div className="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                            Akses
                                        </div>
                                        <div className="text-sm font-bold text-teal-600">
                                            Terbuka Publik
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* DESCRIPTION & STANDARDS CARD */}
                        <div className="bg-white rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-200/60 border border-slate-100">
                            <h2 className="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <span className="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                Tentang Fasilitas
                            </h2>

                            <div
                                className="prose prose-slate prose-lg max-w-none text-slate-700 leading-relaxed font-normal"
                                dangerouslySetInnerHTML={{
                                    __html: facility.description || '<p>Informasi detail mengenai fasilitas ramah anak ini sedang disiapkan oleh dinas pengampu terkait.</p>',
                                }}
                            />

                            {/* STANDAR INDIKATOR RAMAH ANAK */}
                            <div className="mt-10 pt-8 border-t border-slate-100">
                                <h3 className="text-lg font-bold text-slate-900 mb-4">
                                    Indikator Standar Fasilitas Ramah Anak (KLA)
                                </h3>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div className="flex items-start gap-3 p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100/80">
                                        <div className="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div className="text-sm font-bold text-slate-900">Kawasan Tanpa Asap Rokok</div>
                                            <div className="text-xs text-slate-600 mt-0.5">Lingkungan bersih, bebas puntung dan polusi rokok demi paru-paru anak.</div>
                                        </div>
                                    </div>

                                    <div className="flex items-start gap-3 p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100/80">
                                        <div className="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div className="text-sm font-bold text-slate-900">Keamanan & Keselamatan Fisik</div>
                                            <div className="text-xs text-slate-600 mt-0.5">Sudut tumpul, material anti racun, dan permukaan ramah aktivitas balita.</div>
                                        </div>
                                    </div>

                                    <div className="flex items-start gap-3 p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100/80">
                                        <div className="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div className="text-sm font-bold text-slate-900">Ruang Laktasi & Sanitasi Higienis</div>
                                            <div className="text-xs text-slate-600 mt-0.5">Ketersediaan ruang privasi ibu menyusui dan wastafel setinggi anak.</div>
                                        </div>
                                    </div>

                                    <div className="flex items-start gap-3 p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100/80">
                                        <div className="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div className="text-sm font-bold text-slate-900">Inklusif & Akses Disabilitas</div>
                                            <div className="text-xs text-slate-600 mt-0.5">Aksesibel bagi anak-anak berkebutuhan khusus dan pengguna kursi roda.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* INTERACTIVE MAP CARD */}
                        <div className="bg-white rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-200/60 border border-slate-100">
                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                                <div>
                                    <h2 className="text-2xl font-black text-slate-900 flex items-center gap-3">
                                        <span className="w-8 h-8 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center text-sm font-bold">
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </span>
                                        Lokasi & Peta Navigasi
                                    </h2>
                                    <p className="text-sm text-slate-500 mt-1">
                                        {facility.address || 'Kabupaten Konawe, Sulawesi Tenggara'}
                                    </p>
                                </div>

                                <div className="flex items-center gap-2">
                                    {facility.address && (
                                        <button
                                            type="button"
                                            onClick={handleCopyAddress}
                                            className="px-3.5 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                        >
                                            {addressCopied ? 'Alamat Tersalin!' : 'Salin Alamat'}
                                        </button>
                                    )}
                                    <a
                                        href={googleMapsDirectionUrl}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="px-4 py-2 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition inline-flex items-center gap-1.5"
                                    >
                                        Buka Google Maps
                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            {/* Embed Map Iframe */}
                            <div className="rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-inner h-80 sm:h-96 relative">
                                <iframe
                                    title={`Peta Lokasi ${facility.name}`}
                                    src={googleMapsEmbedUrl}
                                    width="100%"
                                    height="100%"
                                    style={{ border: 0 }}
                                    allowFullScreen={false}
                                    loading="lazy"
                                    referrerPolicy="no-referrer-when-downgrade"
                                />
                            </div>

                            {facility.coordinates && (
                                <div className="mt-4 flex items-center justify-between text-xs text-slate-500 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-100">
                                    <span>Koordinat GPS: <code className="font-mono text-slate-700 font-semibold">{facility.coordinates}</code></span>
                                    <span className="text-emerald-600 font-medium">Titik Lokasi Terverifikasi</span>
                                </div>
                            )}
                        </div>

                        {/* CALLOUT PENGADUAN / FEEDBACK */}
                        <div className="bg-gradient-to-r from-teal-500 to-emerald-600 rounded-3xl p-6 sm:p-8 text-white flex flex-col sm:flex-row items-center justify-between gap-6 shadow-lg shadow-teal-500/20">
                            <div>
                                <h3 className="text-xl font-bold mb-2">Temukan Masalah atau Fasilitas Rusak?</h3>
                                <p className="text-teal-100 text-sm max-w-xl">
                                    Bantu Pemerintah Kabupaten Konawe menjaga kualitas fasilitas ramah anak dengan mengirimkan aspirasi atau pengaduan secara langsung.
                                </p>
                            </div>
                            <Link
                                href="/pengaduan"
                                className="px-6 py-3 rounded-2xl bg-white text-teal-700 font-bold text-sm shadow-md hover:bg-teal-50 transition active:scale-95 flex-shrink-0"
                            >
                                Kirim Pengaduan
                            </Link>
                        </div>
                    </motion.div>

                    {/* RIGHT COLUMN: 4 COLS (STICKY SIDEBAR) */}
                    <motion.div
                        initial={{ opacity: 0, y: 24 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5, delay: 0.2 }}
                        className="lg:col-span-4 space-y-6 lg:sticky lg:top-28"
                    >
                        {/* MANAGING OPD CARD */}
                        <div className="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/60 border border-slate-100">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/20 flex-shrink-0">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <span className="text-[11px] font-bold uppercase tracking-wider text-emerald-600">
                                        Instansi Pengelola
                                    </span>
                                    <h4 className="text-base font-bold text-slate-900 leading-snug">
                                        {facility.organization?.name || 'Pemerintah Kabupaten Konawe'}
                                    </h4>
                                </div>
                            </div>

                            <p className="text-xs text-slate-500 mb-5 leading-relaxed">
                                {facility.organization?.type ? `Unit Perangkat Daerah: ${facility.organization.type}` : 'Organisasi Perangkat Daerah penanggung jawab pemeliharaan sarana dan prasarana ramah anak di Kabupaten Konawe.'}
                            </p>

                            <div className="space-y-3 pt-4 border-t border-slate-100 text-sm">
                                {facility.phone && (
                                    <div className="flex items-center gap-3 text-slate-600">
                                        <div className="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center flex-shrink-0">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <div className="min-w-0">
                                            <div className="text-xs text-slate-400">Kontak / Telepon</div>
                                            <a href={`tel:${facility.phone.replace(/[^0-9+]/g, '')}`} className="font-semibold text-emerald-600 hover:underline">
                                                {facility.phone}
                                            </a>
                                        </div>
                                    </div>
                                )}

                                {facility.organization?.email && (
                                    <div className="flex items-center gap-3 text-slate-600">
                                        <div className="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center flex-shrink-0">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div className="min-w-0">
                                            <div className="text-xs text-slate-400">Email Resmi</div>
                                            <a href={`mailto:${facility.organization.email}`} className="font-semibold text-slate-700 hover:text-emerald-600 truncate block">
                                                {facility.organization.email}
                                            </a>
                                        </div>
                                    </div>
                                )}

                                {facility.address && (
                                    <div className="flex items-start gap-3 text-slate-600">
                                        <div className="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div className="min-w-0">
                                            <div className="text-xs text-slate-400">Lokasi / Alamat</div>
                                            <div className="text-xs text-slate-700 font-medium leading-relaxed">
                                                {facility.address}
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* ACTIONS & NAVIGATION BOX */}
                        <div className="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/60 border border-slate-100 space-y-3">
                            <h4 className="text-sm font-bold text-slate-900 mb-2">Aksi Cepat</h4>
                            
                            <a
                                href={googleMapsDirectionUrl}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition active:scale-98"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Petunjuk Arah ke Lokasi
                            </a>

                            {facility.phone && (
                                <a
                                    href={`tel:${facility.phone.replace(/[^0-9+]/g, '')}`}
                                    className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-teal-50 hover:bg-teal-100 text-teal-800 font-bold text-sm border border-teal-200/60 transition"
                                >
                                    <svg className="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Hubungi Pengelola ({facility.phone})
                                </a>
                            )}

                            <button
                                type="button"
                                onClick={handleShare}
                                className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                {copied ? 'Tautan Berhasil Disalin!' : 'Bagikan Halaman Ini'}
                            </button>
                        </div>

                        {/* VISIT NOTICE / TATA TERTIB */}
                        <div className="bg-emerald-50/70 rounded-3xl p-6 border border-emerald-100">
                            <h4 className="text-sm font-bold text-emerald-950 flex items-center gap-2 mb-3">
                                <svg className="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                                </svg>
                                Panduan Kunjungan Ramah Anak
                            </h4>
                            <ul className="text-xs text-emerald-800 space-y-2 list-disc list-inside">
                                <li>Selalu awasi dan dampingi anak saat beraktivitas.</li>
                                <li>Dilarang keras merokok atau membuang sampah sembarangan.</li>
                                <li>Jaga kebersihan mainan dan fasilitas bersama.</li>
                                <li>Laporkan segera ke petugas jika ada sarana yang rusak.</li>
                            </ul>
                        </div>
                    </motion.div>
                </div>

                {/* RELATED FACILITIES SECTION */}
                {relatedFacilities.length > 0 && (
                    <div className="mt-20 pt-12 border-t border-slate-200">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                            <div>
                                <span className="text-xs font-bold uppercase tracking-wider text-emerald-600">
                                    Eksplorasi Fasilitas
                                </span>
                                <h3 className="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                                    Fasilitas Ramah Anak Lainnya di Konawe
                                </h3>
                            </div>
                            <Link
                                href="/fasilitas"
                                className="inline-flex items-center text-sm font-bold text-emerald-600 hover:text-emerald-700 transition"
                            >
                                Lihat Semua Fasilitas
                                <svg className="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {relatedFacilities.map((item) => {
                                const itemImg = getImageUrl(item.image);
                                return (
                                    <Link
                                        key={item.id}
                                        href={`/fasilitas/${item.slug}`}
                                        className="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 flex flex-col"
                                    >
                                        <div className="h-44 bg-slate-100 overflow-hidden relative">
                                            {itemImg ? (
                                                <img
                                                    src={itemImg}
                                                    alt={item.name}
                                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                />
                                            ) : (
                                                <div className="w-full h-full bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center text-emerald-300">
                                                    <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                            )}
                                            {item.is_child_friendly && (
                                                <span className="absolute top-3 left-3 bg-emerald-600/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                                    ✓ Ramah Anak
                                                </span>
                                            )}
                                        </div>
                                        <div className="p-6 flex-grow flex flex-col justify-between">
                                            <div>
                                                <h4 className="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition mb-2 line-clamp-1">
                                                    {item.name}
                                                </h4>
                                                {item.address && (
                                                    <p className="text-xs text-slate-500 line-clamp-2 mb-3">
                                                        {item.address}
                                                    </p>
                                                )}
                                            </div>
                                            <div className="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-600">
                                                <span>Lihat Detail</span>
                                                <svg className="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
