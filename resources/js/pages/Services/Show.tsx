import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useState } from 'react';
import AppLayout from '../../layouts/AppLayout';
import { type Service } from '../../types';

interface ServiceShowProps {
    service: Service;
    relatedServices?: Service[];
}

function getImageUrl(path?: string | null): string {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    return path.startsWith('/') ? path : `/storage/${path}`;
}

function extractPhoneNumber(text?: string | null): string | null {
    if (!text) return null;
    const match = text.match(/(?:(?:\+?62)|0)[0-9\- ()]{8,16}/);
    if (!match) return null;
    return match[0].replace(/[^0-9+]/g, '');
}

function formatWhatsAppNumber(phone?: string | null): string | null {
    if (!phone) return null;
    let cleaned = phone.replace(/[^0-9]/g, '');
    if (cleaned.startsWith('0')) {
        cleaned = '62' + cleaned.slice(1);
    }
    return cleaned;
}

export default function ServiceShow({ service, relatedServices = [] }: ServiceShowProps) {
    const [copied, setCopied] = useState(false);
    const [contactCopied, setContactCopied] = useState(false);

    const imageUrl = getImageUrl(service.image);
    const rawPhone = extractPhoneNumber(service.contact_info);
    const isMobileOrWA = rawPhone && (rawPhone.startsWith('08') || rawPhone.startsWith('+628') || rawPhone.startsWith('628') || service.contact_info?.toLowerCase().includes('wa'));
    const waNumber = isMobileOrWA ? formatWhatsAppNumber(rawPhone) : null;

    const handleShare = async () => {
        const shareData = {
            title: `${service.name} - Layanan KLA Konawe`,
            text: `Informasi layanan publik ramah anak: ${service.name} di Kabupaten Konawe.`,
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
            // ignore
        }
    };

    const handleCopyContact = async () => {
        if (!service.contact_info) return;
        try {
            await navigator.clipboard.writeText(service.contact_info);
            setContactCopied(true);
            setTimeout(() => setContactCopied(false), 2500);
        } catch {
            // ignore
        }
    };

    // Determine type label and badge style
    const renderTypeBadge = () => {
        const type = service.type?.toLowerCase();
        if (type === 'terpadu') {
            return (
                <span className="inline-flex items-center gap-1.5 bg-amber-400/25 border border-amber-300/40 text-amber-100 text-xs sm:text-sm font-semibold px-3.5 py-1.5 rounded-full backdrop-blur-sm">
                    ⭐ Layanan Terpadu & Terintegrasi
                </span>
            );
        }
        if (type === 'online') {
            return (
                <span className="inline-flex items-center gap-1.5 bg-sky-400/25 border border-sky-300/40 text-sky-100 text-xs sm:text-sm font-semibold px-3.5 py-1.5 rounded-full backdrop-blur-sm">
                    🌐 Layanan Online / Daring
                </span>
            );
        }
        if (type === 'offline') {
            return (
                <span className="inline-flex items-center gap-1.5 bg-emerald-400/25 border border-emerald-300/40 text-emerald-100 text-xs sm:text-sm font-semibold px-3.5 py-1.5 rounded-full backdrop-blur-sm">
                    🏢 Layanan Tatap Muka Langsung
                </span>
            );
        }
        return (
            <span className="inline-flex items-center gap-1.5 bg-primary-400/25 border border-primary-300/40 text-primary-100 text-xs sm:text-sm font-semibold px-3.5 py-1.5 rounded-full backdrop-blur-sm">
                Pelayanan Ramah Anak
            </span>
        );
    };

    return (
        <AppLayout title={`${service.name} - Layanan Publik KLA`}>
            {/* HERO SECTION */}
            <div className="relative bg-gradient-to-br from-primary-600 via-primary-700 to-indigo-900 pt-36 pb-20 text-white overflow-hidden">
                {/* Decorative background shapes */}
                <div className="absolute inset-0 opacity-20 pointer-events-none">
                    <div className="absolute -top-24 -left-24 w-96 h-96 bg-primary-400 rounded-full blur-3xl" />
                    <div className="absolute top-1/2 right-0 w-80 h-80 bg-indigo-300 rounded-full blur-3xl" />
                    <div className="absolute -bottom-20 left-1/3 w-72 h-72 bg-sky-400 rounded-full blur-2xl" />
                </div>

                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <motion.div
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.4 }}
                    >
                        {/* Breadcrumbs */}
                        <nav className="flex items-center gap-2 text-sm text-primary-200/80 mb-6 flex-wrap">
                            <Link href="/" className="hover:text-white transition">
                                Beranda
                            </Link>
                            <span>/</span>
                            <Link href="/layanan" className="hover:text-white transition">
                                Direktori Layanan Publik
                            </Link>
                            <span>/</span>
                            <span className="text-white font-medium truncate max-w-xs sm:max-w-md">
                                {service.name}
                            </span>
                        </nav>

                        {/* Badges Bar */}
                        <div className="flex flex-wrap items-center gap-2.5 mb-4">
                            {renderTypeBadge()}
                            <span className="inline-flex items-center text-xs font-bold bg-white/15 border border-white/20 text-white px-3 py-1.5 rounded-full backdrop-blur-sm">
                                Bebas Biaya (Gratis 100%)
                            </span>
                            {service.organization && (
                                <span className="inline-flex items-center text-xs font-medium bg-black/25 text-primary-100 border border-primary-400/30 px-3 py-1.5 rounded-full">
                                    <svg className="w-3.5 h-3.5 mr-1.5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {service.organization.name}
                                </span>
                            )}
                        </div>

                        {/* Title */}
                        <h1 className="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight mb-4">
                            {service.name}
                        </h1>

                        {/* Meta summary & quick contact */}
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-t border-primary-400/20">
                            {service.contact_info ? (
                                <div className="flex items-center gap-2 text-primary-100 text-sm sm:text-base font-medium">
                                    <svg className="w-5 h-5 text-primary-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>Kontak Layanan: <strong className="text-white">{service.contact_info}</strong></span>
                                </div>
                            ) : (
                                <div className="text-primary-200 text-sm">Pemerintah Kabupaten Konawe</div>
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
                                {waNumber ? (
                                    <a
                                        href={`https://wa.me/${waNumber}?text=${encodeURIComponent(`Halo, saya ingin menanyakan informasi mengenai layanan: ${service.name}`)}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white text-sm font-bold shadow-md shadow-emerald-950/30 transition active:scale-95"
                                    >
                                        <svg className="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                        </svg>
                                        WhatsApp
                                    </a>
                                ) : (
                                    <Link
                                        href="/pengaduan"
                                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-500 hover:bg-primary-400 text-white text-sm font-bold shadow-md shadow-primary-950/30 transition active:scale-95"
                                    >
                                        Kirim Pertanyaan
                                    </Link>
                                )}
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
                        {/* FEATURED IMAGE OR BANNER */}
                        <div className="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
                            {imageUrl ? (
                                <div className="relative w-full h-72 sm:h-96 md:h-[400px] bg-slate-900 overflow-hidden group">
                                    <img
                                        src={imageUrl}
                                        alt={service.name}
                                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    />
                                    <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none" />
                                    <div className="absolute bottom-4 left-4 right-4 flex items-center justify-between text-white text-xs sm:text-sm">
                                        <span className="bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/20 font-medium">
                                            Dokumentasi Layanan KLA
                                        </span>
                                    </div>
                                </div>
                            ) : (
                                <div className="h-64 sm:h-72 bg-gradient-to-br from-primary-50 via-indigo-50 to-slate-100 flex flex-col items-center justify-center text-primary-600 p-8 text-center border-b border-primary-100">
                                    <div className="w-20 h-20 rounded-3xl bg-white shadow-md flex items-center justify-center text-primary-600 mb-4">
                                        <svg className="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <h4 className="text-lg font-bold text-slate-800">{service.name}</h4>
                                    <p className="text-sm text-slate-500 max-w-sm mt-1">
                                        Layanan publik ramah anak Pemerintah Kabupaten Konawe
                                    </p>
                                </div>
                            )}

                            {/* QUICK HIGHLIGHTS METRICS */}
                            <div className="p-6 sm:p-8 bg-slate-50/60 border-t border-slate-100">
                                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div className="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                        <div className="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                            Metode
                                        </div>
                                        <div className="text-sm font-bold text-primary-600 capitalize">
                                            {service.type || 'Terpadu'}
                                        </div>
                                    </div>

                                    <div className="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                        <div className="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                            Biaya
                                        </div>
                                        <div className="text-sm font-bold text-emerald-600">
                                            Gratis (Rp 0,-)
                                        </div>
                                    </div>

                                    <div className="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                        <div className="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                            Jadwal
                                        </div>
                                        <div className="text-sm font-bold text-slate-800 truncate" title={service.working_hours || 'Hari & Jam Kerja'}>
                                            {service.working_hours || 'Hari Kerja'}
                                        </div>
                                    </div>

                                    <div className="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                        <div className="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">
                                            Sasaran
                                        </div>
                                        <div className="text-sm font-bold text-slate-800">
                                            Anak & Keluarga
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* DESCRIPTION & SERVICE DETAILS CARD */}
                        <div className="bg-white rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-200/60 border border-slate-100">
                            <h2 className="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <span className="w-8 h-8 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-bold">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                Deskripsi Layanan
                            </h2>

                            <div
                                className="prose prose-slate prose-lg max-w-none text-slate-700 leading-relaxed font-normal"
                                dangerouslySetInnerHTML={{
                                    __html: service.description || '<p>Informasi detail mengenai layanan ramah anak ini sedang disiapkan.</p>',
                                }}
                            />

                            {/* ALUR & TATA CARA MENGAKSES LAYANAN */}
                            <div className="mt-10 pt-8 border-t border-slate-100">
                                <h3 className="text-lg font-bold text-slate-900 mb-5">
                                    Tahapan Cara Mengakses Layanan
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div className="p-4 rounded-2xl bg-slate-50 border border-slate-100 relative">
                                        <div className="w-8 h-8 rounded-full bg-primary-500 text-white font-black text-sm flex items-center justify-center mb-3 shadow-md shadow-primary-500/20">
                                            1
                                        </div>
                                        <h4 className="text-sm font-bold text-slate-900 mb-1">Persiapkan Informasi</h4>
                                        <p className="text-xs text-slate-600 leading-relaxed">
                                            Siapkan identitas dasar anak/wali (NIK/KK atau bukti pendukung) serta kronologi/keperluan yang dibutuhkan.
                                        </p>
                                    </div>

                                    <div className="p-4 rounded-2xl bg-slate-50 border border-slate-100 relative">
                                        <div className="w-8 h-8 rounded-full bg-primary-500 text-white font-black text-sm flex items-center justify-center mb-3 shadow-md shadow-primary-500/20">
                                            2
                                        </div>
                                        <h4 className="text-sm font-bold text-slate-900 mb-1">Hubungi / Kunjungi Unit</h4>
                                        <p className="text-xs text-slate-600 leading-relaxed">
                                            Gunakan kontak hotline, WhatsApp, atau langsung mendatangi kantor instansi pengelola di hari kerja.
                                        </p>
                                    </div>

                                    <div className="p-4 rounded-2xl bg-slate-50 border border-slate-100 relative">
                                        <div className="w-8 h-8 rounded-full bg-primary-500 text-white font-black text-sm flex items-center justify-center mb-3 shadow-md shadow-primary-500/20">
                                            3
                                        </div>
                                        <h4 className="text-sm font-bold text-slate-900 mb-1">Pendampingan & Solusi</h4>
                                        <p className="text-xs text-slate-600 leading-relaxed">
                                            Petugas terlatih KLA memproses permohonan, memberi konsultasi, atau melakukan pendampingan hingga tuntas.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* JAMINAN KERAHASIAAN DAN PERLINDUNGAN ANAK */}
                            <div className="mt-8 p-4 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-start gap-3">
                                <div className="w-6 h-6 rounded-lg bg-blue-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <div className="text-xs text-blue-900 leading-relaxed">
                                    <strong>Prinsip Kerahasiaan & Keamanan Data:</strong> Seluruh informasi anak dan pelapor dilindungi secara ketat sesuai Undang-Undang Perlindungan Anak Nomor 35 Tahun 2014. Identitas tidak akan disebarluaskan ke pihak lain tanpa persetujuan sah.
                                </div>
                            </div>
                        </div>

                        {/* CALLOUT HOTLINE / PENGADUAN CEPAT */}
                        <div className="bg-gradient-to-r from-primary-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white flex flex-col sm:flex-row items-center justify-between gap-6 shadow-lg shadow-primary-600/20">
                            <div>
                                <span className="bg-white/20 text-white text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider mb-2 inline-block">
                                    Siaga Respons Cepat
                                </span>
                                <h3 className="text-xl font-bold mb-2">Kasus Kekerasan atau Butuh Pendampingan Darurat?</h3>
                                <p className="text-primary-100 text-sm max-w-xl">
                                    Tim UPTD PPA Kabupaten Konawe siap memberikan bantuan darurat hukum, medis, dan psikologis bagi anak dan keluarga korban kekerasan.
                                </p>
                            </div>
                            <div className="flex flex-col sm:flex-row items-center gap-3 flex-shrink-0">
                                <Link
                                    href="/pengaduan"
                                    className="px-6 py-3 rounded-2xl bg-white text-primary-700 font-bold text-sm shadow-md hover:bg-primary-50 transition active:scale-95 w-full sm:w-auto text-center"
                                >
                                    Buat Laporan Kasus
                                </Link>
                            </div>
                        </div>
                    </motion.div>

                    {/* RIGHT COLUMN: 4 COLS (STICKY SIDEBAR) */}
                    <motion.div
                        initial={{ opacity: 0, y: 24 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5, delay: 0.2 }}
                        className="lg:col-span-4 space-y-6 lg:sticky lg:top-28"
                    >
                        {/* PRIMARY CONTACT & CTA CARD */}
                        <div className="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/60 border border-slate-100 space-y-4">
                            <span className="text-[11px] font-bold uppercase tracking-wider text-primary-600">
                                Saluran Komunikasi
                            </span>
                            <h4 className="text-lg font-bold text-slate-900 leading-tight">
                                Akses Layanan Sekarang
                            </h4>

                            {service.contact_info && (
                                <div className="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                    <div className="text-xs text-slate-400 mb-1">Informasi Kontak</div>
                                    <div className="text-sm font-bold text-slate-800 break-words">
                                        {service.contact_info}
                                    </div>
                                </div>
                            )}

                            {/* WhatsApp Button if mobile/wa available */}
                            {waNumber && (
                                <a
                                    href={`https://wa.me/${waNumber}?text=${encodeURIComponent(`Halo, saya ingin berkonsultasi mengenai: ${service.name}`)}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition active:scale-98"
                                >
                                    <svg className="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                    </svg>
                                    Hubungi via WhatsApp
                                </a>
                            )}

                            {/* Phone Call Button */}
                            {rawPhone && (
                                <a
                                    href={`tel:${rawPhone}`}
                                    className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-primary-50 hover:bg-primary-100 text-primary-700 font-bold text-sm border border-primary-200/60 transition"
                                >
                                    <svg className="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Panggil ({rawPhone})
                                </a>
                            )}

                            {service.contact_info && (
                                <button
                                    type="button"
                                    onClick={handleCopyContact}
                                    className="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                    {contactCopied ? 'Kontak Berhasil Disalin!' : 'Salin Info Kontak'}
                                </button>
                            )}
                        </div>

                        {/* OPERATIONAL HOURS CARD */}
                        <div className="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/60 border border-slate-100">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 className="text-sm font-bold text-slate-900">Jadwal Pelayanan</h4>
                                    <div className="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                                        <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                                        Pelayanan Tersedia
                                    </div>
                                </div>
                            </div>

                            <div className="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-medium text-slate-800">
                                {service.working_hours || 'Senin - Jumat: 08.00 - 16.00 WITA (Hari Kerja)'}
                            </div>
                        </div>

                        {/* MANAGING OPD CARD */}
                        <div className="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/60 border border-slate-100">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-500 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-primary-500/20 flex-shrink-0">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <span className="text-[11px] font-bold uppercase tracking-wider text-primary-600">
                                        Penyelenggara
                                    </span>
                                    <h4 className="text-base font-bold text-slate-900 leading-snug">
                                        {service.organization?.name || 'Pemerintah Kabupaten Konawe'}
                                    </h4>
                                </div>
                            </div>

                            <p className="text-xs text-slate-500 mb-5 leading-relaxed">
                                {service.organization?.type ? `Unit Organisasi: ${service.organization.type}` : 'Instansi pemerintah penyelenggara layanan publik ramah anak resmi di Kabupaten Konawe.'}
                            </p>

                            <div className="space-y-3 pt-4 border-t border-slate-100 text-sm">
                                {service.organization?.phone && (
                                    <div className="flex items-center gap-3 text-slate-600">
                                        <div className="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center flex-shrink-0">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <div className="min-w-0">
                                            <div className="text-xs text-slate-400">Telepon OPD</div>
                                            <a href={`tel:${service.organization.phone}`} className="font-semibold text-primary-600 hover:underline">
                                                {service.organization.phone}
                                            </a>
                                        </div>
                                    </div>
                                )}

                                {service.organization?.email && (
                                    <div className="flex items-center gap-3 text-slate-600">
                                        <div className="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center flex-shrink-0">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div className="min-w-0">
                                            <div className="text-xs text-slate-400">Email Resmi</div>
                                            <a href={`mailto:${service.organization.email}`} className="font-semibold text-slate-700 hover:text-primary-600 truncate block">
                                                {service.organization.email}
                                            </a>
                                        </div>
                                    </div>
                                )}

                                {service.organization?.address && (
                                    <div className="flex items-start gap-3 text-slate-600">
                                        <div className="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div className="min-w-0">
                                            <div className="text-xs text-slate-400">Alamat Kantor</div>
                                            <div className="text-xs text-slate-700 font-medium leading-relaxed">
                                                {service.organization.address}
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* SHARE BUTTON BOX */}
                        <div className="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/60 border border-slate-100">
                            <button
                                type="button"
                                onClick={handleShare}
                                className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                {copied ? 'Tautan Berhasil Disalin!' : 'Bagikan Layanan Ini'}
                            </button>
                        </div>
                    </motion.div>
                </div>

                {/* RELATED SERVICES SECTION */}
                {relatedServices.length > 0 && (
                    <div className="mt-20 pt-12 border-t border-slate-200">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                            <div>
                                <span className="text-xs font-bold uppercase tracking-wider text-primary-600">
                                    Eksplorasi Layanan
                                </span>
                                <h3 className="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                                    Layanan Ramah Anak Terkait Lainnya
                                </h3>
                            </div>
                            <Link
                                href="/layanan"
                                className="inline-flex items-center text-sm font-bold text-primary-600 hover:text-primary-700 transition"
                            >
                                Lihat Semua Layanan
                                <svg className="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {relatedServices.map((item) => {
                                const itemImg = getImageUrl(item.image);
                                return (
                                    <Link
                                        key={item.id}
                                        href={`/layanan/${item.slug}`}
                                        className="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 flex flex-col"
                                    >
                                        <div className="h-44 bg-slate-100 overflow-hidden relative flex items-center justify-center">
                                            {itemImg ? (
                                                <img
                                                    src={itemImg}
                                                    alt={item.name}
                                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                />
                                            ) : (
                                                <div className="w-full h-full bg-gradient-to-br from-primary-50 to-indigo-50 flex items-center justify-center text-primary-300">
                                                    <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                            )}
                                            {item.type && (
                                                <span className="absolute top-3 left-3 bg-primary-600/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">
                                                    {item.type}
                                                </span>
                                            )}
                                        </div>
                                        <div className="p-6 flex-grow flex flex-col justify-between">
                                            <div>
                                                <h4 className="text-lg font-bold text-slate-900 group-hover:text-primary-600 transition mb-2 line-clamp-1">
                                                    {item.name}
                                                </h4>
                                                <p className="text-xs text-slate-500 line-clamp-2 mb-3">
                                                    {item.description?.replace(/<[^>]*>/g, '') || ''}
                                                </p>
                                            </div>
                                            <div className="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-primary-600">
                                                <span>Akses Layanan</span>
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
