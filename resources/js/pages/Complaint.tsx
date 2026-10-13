import axios from 'axios';
import { AnimatePresence, motion } from 'framer-motion';
import { useState } from 'react';
import AppLayout from '../layouts/AppLayout';

// ── Types ──────────────────────────────────────────────────────────────────────
interface FormData {
    title: string;
    chronology: string;
    is_anonymous: boolean;
    reporter_name: string;
    reporter_email: string;
    reporter_phone: string;
    attachment: File | null;
    consent: boolean;
}

interface StepIndicatorProps {
    step: number;
    currentStep: number;
    label: string;
}

// ── Step Indicator ─────────────────────────────────────────────────────────────
function StepIndicator({ step, currentStep, label }: StepIndicatorProps) {
    const done = currentStep > step;
    const active = currentStep === step;
    return (
        <div className="flex items-center gap-2">
            <div
                className={`w-9 h-9 rounded-full flex items-center justify-center font-black text-sm transition-all duration-300 ${
                    done
                        ? 'bg-green-500 text-white'
                        : active
                        ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/40'
                        : 'bg-gray-100 text-gray-400'
                }`}
            >
                {done ? (
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                    </svg>
                ) : (
                    step
                )}
            </div>
            <span className={`font-bold text-sm hidden sm:block ${active ? 'text-primary-700' : done ? 'text-green-600' : 'text-gray-400'}`}>
                {label}
            </span>
        </div>
    );
}

const slideVariants = {
    enter: (dir: number) => ({ x: dir > 0 ? 60 : -60, opacity: 0 }),
    center: { x: 0, opacity: 1 },
    exit: (dir: number) => ({ x: dir > 0 ? -60 : 60, opacity: 0 }),
};

// ── Main Component ─────────────────────────────────────────────────────────────
export default function Complaint() {
    const [step, setStep] = useState(1);
    const [dir, setDir] = useState(1);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [ticketNumber, setTicketNumber] = useState('');
    const [error, setError] = useState('');
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

    const [form, setForm] = useState<FormData>({
        title: '',
        chronology: '',
        is_anonymous: true,
        reporter_name: '',
        reporter_email: '',
        reporter_phone: '',
        attachment: null,
        consent: false,
    });

    const update = (field: keyof FormData, value: FormData[keyof FormData]) => {
        setForm((prev) => ({ ...prev, [field]: value }));
        setFieldErrors((prev) => ({ ...prev, [field]: '' }));
    };

    const goNext = () => {
        // Basic validation per step
        const errors: Record<string, string> = {};
        if (step === 1) {
            if (!form.title.trim()) errors.title = 'Judul pengaduan wajib diisi.';
            if (!form.chronology.trim() || form.chronology.length < 10)
                errors.chronology = 'Kronologi minimal 10 karakter.';
        }
        if (step === 2 && !form.is_anonymous) {
            if (!form.reporter_name.trim()) errors.reporter_name = 'Nama wajib diisi jika tidak anonim.';
            if (!form.reporter_phone.trim()) errors.reporter_phone = 'Nomor HP wajib diisi jika tidak anonim.';
        }
        if (Object.keys(errors).length > 0) {
            setFieldErrors(errors);
            return;
        }
        setDir(1);
        setStep((s) => s + 1);
    };

    const goPrev = () => {
        setDir(-1);
        setStep((s) => s - 1);
    };

    const handleSubmit = async () => {
        if (!form.consent) {
            setFieldErrors({ consent: 'Anda harus menyetujui pernyataan di atas.' });
            return;
        }
        setIsSubmitting(true);
        setError('');

        try {
            const data = new FormData();
            data.append('title', form.title);
            data.append('body', form.chronology);
            data.append('complaint_category_id', '1');
            data.append('is_anonymous', form.is_anonymous ? 'true' : 'false');
            data.append('is_confidential', 'true');
            data.append('reporter_name', form.is_anonymous ? 'Anonim' : form.reporter_name);
            data.append('reporter_email', form.is_anonymous ? '' : form.reporter_email);
            data.append('reporter_phone', form.is_anonymous ? '' : form.reporter_phone);
            if (form.attachment) data.append('attachments', form.attachment);

            const res = await axios.post('/api/pengaduan', data, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });

            if (res.data?.success) {
                setTicketNumber(res.data?.data?.ticket_number ?? 'TKT-SUCCESS');
                setSubmitted(true);
            } else {
                setError('Gagal mengirim pengaduan. Silakan coba lagi.');
            }
        } catch {
            setError('Terjadi kesalahan koneksi. Pastikan server API tersedia.');
        } finally {
            setIsSubmitting(false);
        }
    };

    // ── Success State ──────────────────────────────────────────────────────────
    if (submitted) {
        return (
            <AppLayout title="Pengaduan Terkirim">
                <div className="min-h-screen flex items-center justify-center px-4 pt-24">
                    <motion.div
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ type: 'spring', stiffness: 200, damping: 20 }}
                        className="bg-white rounded-3xl shadow-2xl shadow-green-200/50 border border-green-100 p-10 max-w-lg w-full text-center"
                    >
                        <motion.div
                            initial={{ scale: 0 }}
                            animate={{ scale: 1 }}
                            transition={{ type: 'spring', stiffness: 300, damping: 20, delay: 0.2 }}
                            className="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6"
                        >
                            <svg className="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </motion.div>
                        <h2 className="text-3xl font-black text-gray-900 mb-3">Pengaduan Terkirim!</h2>
                        <p className="text-gray-600 font-medium mb-6">
                            Pengaduan Anda telah diterima dan akan segera ditindaklanjuti.
                        </p>
                        <div className="bg-primary-50 rounded-2xl p-5 mb-8">
                            <p className="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Nomor Tiket Anda</p>
                            <p className="text-3xl font-black text-primary-700 tracking-wider">{ticketNumber}</p>
                            <p className="text-xs text-gray-500 mt-2 font-medium">Simpan nomor ini untuk memantau status pengaduan.</p>
                        </div>
                        <div className="flex flex-col sm:flex-row gap-3">
                            <a
                                href="/cek-pengaduan"
                                className="flex-1 bg-primary-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-primary-700 transition text-center"
                            >
                                Cek Status
                            </a>
                            <a
                                href="/"
                                className="flex-1 bg-gray-100 text-gray-700 font-bold py-3 px-6 rounded-xl hover:bg-gray-200 transition text-center"
                            >
                                Kembali ke Beranda
                            </a>
                        </div>
                    </motion.div>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout title="Formulir Pengaduan Publik">
            {/* Hero */}
            <div className="bg-gradient-to-r from-red-500 to-secondary-600 pt-32 pb-16 text-white text-center relative overflow-hidden">
                <div className="h-24" />
                <motion.div initial={{ opacity: 0, y: 24 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }} className="relative z-10">
                    <div className="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-2 rounded-full mb-5 text-sm font-bold">
                        <span className="w-2 h-2 bg-white rounded-full animate-pulse" />
                        Laporan Aman &amp; Rahasia
                    </div>
                    <h1 className="text-4xl md:text-5xl font-black mb-4 tracking-tight">Formulir Pengaduan</h1>
                    <p className="text-xl text-red-100 max-w-2xl mx-auto font-medium">
                        Laporkan pelanggaran hak anak secara aman. Identitas Anda terjamin kerahasiaannya.
                    </p>
                </motion.div>
            </div>

            <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                {/* Progress Steps */}
                <div className="flex items-center justify-between mb-10 bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                    <StepIndicator step={1} currentStep={step} label="Kronologi" />
                    <div className={`flex-1 h-1 mx-3 rounded-full transition-colors duration-300 ${step >= 2 ? 'bg-primary-400' : 'bg-gray-100'}`} />
                    <StepIndicator step={2} currentStep={step} label="Identitas" />
                    <div className={`flex-1 h-1 mx-3 rounded-full transition-colors duration-300 ${step >= 3 ? 'bg-primary-400' : 'bg-gray-100'}`} />
                    <StepIndicator step={3} currentStep={step} label="Konfirmasi" />
                </div>

                {/* Form Card */}
                <div className="bg-white rounded-3xl shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
                    <AnimatePresence mode="wait" custom={dir}>
                        {/* ── Step 1: Kronologi ── */}
                        {step === 1 && (
                            <motion.div
                                key="step1"
                                custom={dir}
                                variants={slideVariants}
                                initial="enter"
                                animate="center"
                                exit="exit"
                                transition={{ duration: 0.3, ease: 'easeInOut' }}
                                className="p-8"
                            >
                                <h2 className="text-2xl font-black text-gray-900 mb-6">Ceritakan Kronologinya</h2>

                                <div className="mb-5">
                                    <label className="block text-sm font-bold text-gray-700 mb-2" htmlFor="title">
                                        Judul Pengaduan <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="title"
                                        type="text"
                                        value={form.title}
                                        onChange={(e) => update('title', e.target.value)}
                                        placeholder="Contoh: Kekerasan terhadap anak di RT 03..."
                                        className={`w-full border rounded-xl px-4 py-3 font-medium text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition ${fieldErrors.title ? 'border-red-400 bg-red-50' : 'border-gray-200'}`}
                                    />
                                    {fieldErrors.title && <p className="mt-1.5 text-sm text-red-600 font-medium">{fieldErrors.title}</p>}
                                </div>

                                <div className="mb-6">
                                    <label className="block text-sm font-bold text-gray-700 mb-2" htmlFor="chronology">
                                        Kronologi Kejadian <span className="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="chronology"
                                        value={form.chronology}
                                        onChange={(e) => update('chronology', e.target.value)}
                                        rows={7}
                                        placeholder="Ceritakan secara detail apa yang terjadi, kapan, di mana, dan siapa yang terlibat..."
                                        className={`w-full border rounded-xl px-4 py-3 font-medium text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition resize-none ${fieldErrors.chronology ? 'border-red-400 bg-red-50' : 'border-gray-200'}`}
                                    />
                                    <div className="flex justify-between mt-1.5">
                                        {fieldErrors.chronology ? (
                                            <p className="text-sm text-red-600 font-medium">{fieldErrors.chronology}</p>
                                        ) : <span />}
                                        <span className={`text-xs font-bold ${form.chronology.length < 10 ? 'text-gray-400' : 'text-green-600'}`}>
                                            {form.chronology.length} karakter
                                        </span>
                                    </div>
                                </div>

                                <div className="mb-6">
                                    <label className="block text-sm font-bold text-gray-700 mb-2">
                                        Lampiran Bukti <span className="text-gray-400">(opsional)</span>
                                    </label>
                                    <div className={`relative border-2 border-dashed rounded-xl p-6 text-center transition ${form.attachment ? 'border-green-400 bg-green-50' : 'border-gray-200 hover:border-primary-400'}`}>
                                        <input
                                            type="file"
                                            id="attachment"
                                            className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            onChange={(e) => update('attachment', e.target.files?.[0] ?? null)}
                                            accept="image/*,video/*,.pdf,.doc,.docx"
                                        />
                                        {form.attachment ? (
                                            <div className="text-green-700 font-bold">
                                                <svg className="w-8 h-8 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {form.attachment.name}
                                            </div>
                                        ) : (
                                            <div className="text-gray-500">
                                                <svg className="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                <span className="font-medium">Klik atau seret file ke sini</span>
                                                <p className="text-xs mt-1 text-gray-400">Foto, video, PDF, Word (maks. 5MB)</p>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <button
                                    onClick={goNext}
                                    className="w-full bg-primary-600 hover:bg-primary-700 text-white font-black py-4 px-6 rounded-xl transition flex items-center justify-center gap-2 text-lg"
                                >
                                    Lanjut ke Identitas
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </motion.div>
                        )}

                        {/* ── Step 2: Identitas ── */}
                        {step === 2 && (
                            <motion.div
                                key="step2"
                                custom={dir}
                                variants={slideVariants}
                                initial="enter"
                                animate="center"
                                exit="exit"
                                transition={{ duration: 0.3, ease: 'easeInOut' }}
                                className="p-8"
                            >
                                <h2 className="text-2xl font-black text-gray-900 mb-2">Identitas Pelapor</h2>
                                <p className="text-gray-600 font-medium mb-6">Anda dapat melaporkan secara anonim untuk keamanan Anda.</p>

                                {/* Anonymous toggle */}
                                <div className="mb-6 p-5 rounded-2xl border-2 border-primary-100 bg-primary-50">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <p className="font-black text-gray-900">Laporkan Secara Anonim</p>
                                            <p className="text-sm text-gray-600 font-medium">Identitas Anda tidak akan diungkap</p>
                                        </div>
                                        <button
                                            onClick={() => update('is_anonymous', !form.is_anonymous)}
                                            className={`relative w-14 h-7 rounded-full transition-colors duration-300 ${form.is_anonymous ? 'bg-primary-600' : 'bg-gray-300'}`}
                                            role="switch"
                                            aria-checked={form.is_anonymous}
                                        >
                                            <span
                                                className={`absolute top-0.5 left-0.5 w-6 h-6 bg-white rounded-full shadow transition-transform duration-300 ${form.is_anonymous ? 'translate-x-7' : 'translate-x-0'}`}
                                            />
                                        </button>
                                    </div>
                                </div>

                                <AnimatePresence>
                                    {!form.is_anonymous && (
                                        <motion.div
                                            initial={{ height: 0, opacity: 0 }}
                                            animate={{ height: 'auto', opacity: 1 }}
                                            exit={{ height: 0, opacity: 0 }}
                                            className="overflow-hidden"
                                        >
                                            <div className="space-y-4 mb-6">
                                                <div>
                                                    <label className="block text-sm font-bold text-gray-700 mb-2">
                                                        Nama Lengkap <span className="text-red-500">*</span>
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={form.reporter_name}
                                                        onChange={(e) => update('reporter_name', e.target.value)}
                                                        className={`w-full border rounded-xl px-4 py-3 font-medium text-gray-900 focus:ring-2 focus:ring-primary-500 outline-none transition ${fieldErrors.reporter_name ? 'border-red-400 bg-red-50' : 'border-gray-200'}`}
                                                        placeholder="Nama lengkap Anda"
                                                    />
                                                    {fieldErrors.reporter_name && <p className="mt-1.5 text-sm text-red-600 font-medium">{fieldErrors.reporter_name}</p>}
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-bold text-gray-700 mb-2">
                                                        Nomor HP <span className="text-red-500">*</span>
                                                    </label>
                                                    <input
                                                        type="tel"
                                                        value={form.reporter_phone}
                                                        onChange={(e) => update('reporter_phone', e.target.value)}
                                                        className={`w-full border rounded-xl px-4 py-3 font-medium text-gray-900 focus:ring-2 focus:ring-primary-500 outline-none transition ${fieldErrors.reporter_phone ? 'border-red-400 bg-red-50' : 'border-gray-200'}`}
                                                        placeholder="08xx-xxxx-xxxx"
                                                    />
                                                    {fieldErrors.reporter_phone && <p className="mt-1.5 text-sm text-red-600 font-medium">{fieldErrors.reporter_phone}</p>}
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-bold text-gray-700 mb-2">
                                                        Email <span className="text-gray-400">(opsional)</span>
                                                    </label>
                                                    <input
                                                        type="email"
                                                        value={form.reporter_email}
                                                        onChange={(e) => update('reporter_email', e.target.value)}
                                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 font-medium text-gray-900 focus:ring-2 focus:ring-primary-500 outline-none transition"
                                                        placeholder="email@contoh.com"
                                                    />
                                                </div>
                                            </div>
                                        </motion.div>
                                    )}
                                </AnimatePresence>

                                <div className="flex gap-3 mt-6">
                                    <button
                                        onClick={goPrev}
                                        className="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-black py-4 px-6 rounded-xl transition"
                                    >
                                        Kembali
                                    </button>
                                    <button
                                        onClick={goNext}
                                        className="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-black py-4 px-6 rounded-xl transition flex items-center justify-center gap-2"
                                    >
                                        Tinjau &amp; Kirim
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </button>
                                </div>
                            </motion.div>
                        )}

                        {/* ── Step 3: Review & Submit ── */}
                        {step === 3 && (
                            <motion.div
                                key="step3"
                                custom={dir}
                                variants={slideVariants}
                                initial="enter"
                                animate="center"
                                exit="exit"
                                transition={{ duration: 0.3, ease: 'easeInOut' }}
                                className="p-8"
                            >
                                <h2 className="text-2xl font-black text-gray-900 mb-6">Tinjau Pengaduan</h2>

                                <div className="space-y-4 mb-6">
                                    <div className="bg-gray-50 rounded-2xl p-4">
                                        <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Judul</p>
                                        <p className="font-bold text-gray-900">{form.title}</p>
                                    </div>
                                    <div className="bg-gray-50 rounded-2xl p-4">
                                        <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kronologi</p>
                                        <p className="font-medium text-gray-700 whitespace-pre-wrap line-clamp-4">{form.chronology}</p>
                                    </div>
                                    <div className="bg-gray-50 rounded-2xl p-4">
                                        <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Identitas</p>
                                        <p className="font-bold text-gray-900">
                                            {form.is_anonymous ? '🔒 Anonim' : form.reporter_name}
                                        </p>
                                        {!form.is_anonymous && form.reporter_phone && (
                                            <p className="text-sm text-gray-600 font-medium mt-1">{form.reporter_phone}</p>
                                        )}
                                    </div>
                                    {form.attachment && (
                                        <div className="bg-gray-50 rounded-2xl p-4">
                                            <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Lampiran</p>
                                            <p className="font-medium text-gray-700">📎 {form.attachment.name}</p>
                                        </div>
                                    )}
                                </div>

                                {/* Consent */}
                                <label className={`flex items-start gap-3 cursor-pointer p-4 rounded-2xl border-2 transition mb-6 ${fieldErrors.consent ? 'border-red-400 bg-red-50' : form.consent ? 'border-primary-400 bg-primary-50' : 'border-gray-200'}`}>
                                    <input
                                        type="checkbox"
                                        checked={form.consent}
                                        onChange={(e) => update('consent', e.target.checked)}
                                        className="w-5 h-5 mt-0.5 rounded accent-primary-600 flex-shrink-0"
                                    />
                                    <span className="text-sm font-medium text-gray-700">
                                        Saya menyatakan bahwa informasi yang saya berikan adalah benar dan dapat dipertanggungjawabkan. Saya memahami bahwa pengaduan palsu dapat dikenai sanksi.
                                    </span>
                                </label>
                                {fieldErrors.consent && <p className="mb-4 text-sm text-red-600 font-medium">{fieldErrors.consent}</p>}

                                {error && (
                                    <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 font-medium text-sm">
                                        {error}
                                    </div>
                                )}

                                <div className="flex gap-3">
                                    <button
                                        onClick={goPrev}
                                        className="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-black py-4 px-6 rounded-xl transition"
                                    >
                                        Kembali
                                    </button>
                                    <button
                                        onClick={handleSubmit}
                                        disabled={isSubmitting}
                                        className="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-red-300 text-white font-black py-4 px-6 rounded-xl transition flex items-center justify-center gap-2"
                                    >
                                        {isSubmitting ? (
                                            <>
                                                <svg className="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                                </svg>
                                                Mengirim...
                                            </>
                                        ) : (
                                            <>
                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                Kirim Pengaduan
                                            </>
                                        )}
                                    </button>
                                </div>
                            </motion.div>
                        )}
                    </AnimatePresence>
                </div>
            </div>
        </AppLayout>
    );
}
