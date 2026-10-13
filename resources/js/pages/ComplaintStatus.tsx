import axios from 'axios';
import { motion } from 'framer-motion';
import { useState } from 'react';
import AppLayout from '../layouts/AppLayout';

interface ComplaintResult {
    ticket_number: string;
    title: string;
    status: string;
    created_at: string;
}

export default function ComplaintStatus() {
    const [ticketNumber, setTicketNumber] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState<ComplaintResult | null>(null);
    const [error, setError] = useState('');

    const handleCheck = async () => {
        if (!ticketNumber.trim()) return;
        setIsLoading(true);
        setError('');
        setResult(null);

        try {
            const res = await axios.get(`/api/pengaduan/${ticketNumber.trim()}`);
            if (res.data?.success && res.data?.data) {
                setResult(res.data.data);
            } else {
                setError('Nomor tiket tidak ditemukan. Pastikan nomor tiket yang Anda masukkan benar.');
            }
        } catch {
            setError('Tidak dapat terhubung ke server. Silakan coba lagi nanti.');
        } finally {
            setIsLoading(false);
        }
    };

    const getStatusColor = (status: string) => {
        switch (status?.toLowerCase()) {
            case 'resolved': return 'bg-green-100 text-green-700';
            case 'in_progress': case 'process': return 'bg-blue-100 text-blue-700';
            case 'rejected': return 'bg-red-100 text-red-700';
            default: return 'bg-yellow-100 text-yellow-700';
        }
    };

    const getStatusLabel = (status: string) => {
        switch (status?.toLowerCase()) {
            case 'resolved': return 'Selesai';
            case 'in_progress': case 'process': return 'Sedang Diproses';
            case 'rejected': return 'Ditolak';
            default: return 'Menunggu';
        }
    };

    return (
        <AppLayout title="Cek Status Pengaduan">
            <div className="bg-gradient-to-r from-primary-600 to-secondary-600 pt-32 pb-16 text-white text-center">
                <div className="h-24" />
                <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }}>
                    <h1 className="text-4xl md:text-5xl font-black mb-4 tracking-tight">Cek Status Pengaduan</h1>
                    <p className="text-xl text-primary-100 max-w-2xl mx-auto font-medium">
                        Masukkan nomor tiket pengaduan Anda untuk memantau perkembangannya.
                    </p>
                </motion.div>
            </div>

            <div className="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <motion.div
                    initial={{ opacity: 0, y: 24 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                    className="bg-white rounded-3xl shadow-xl shadow-gray-200/40 border border-gray-100 p-8"
                >
                    <div className="mb-6">
                        <label className="block text-sm font-bold text-gray-700 mb-2" htmlFor="ticket-input">
                            Nomor Tiket Pengaduan
                        </label>
                        <div className="flex gap-3">
                            <input
                                id="ticket-input"
                                type="text"
                                value={ticketNumber}
                                onChange={(e) => setTicketNumber(e.target.value)}
                                onKeyDown={(e) => e.key === 'Enter' && handleCheck()}
                                placeholder="Contoh: TKT-2024-001"
                                className="flex-grow border border-gray-200 rounded-xl px-4 py-3 font-medium text-gray-900 focus:ring-2 focus:ring-primary-500 outline-none transition"
                            />
                            <button
                                onClick={handleCheck}
                                disabled={isLoading || !ticketNumber.trim()}
                                className="bg-primary-600 hover:bg-primary-700 disabled:bg-primary-300 text-white font-bold px-6 py-3 rounded-xl transition flex items-center gap-2"
                            >
                                {isLoading ? (
                                    <svg className="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                ) : (
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                )}
                                Cek
                            </button>
                        </div>
                    </div>

                    {error && (
                        <motion.div
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            className="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 font-medium text-sm"
                        >
                            {error}
                        </motion.div>
                    )}

                    {result && (
                        <motion.div
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            className="border border-gray-100 rounded-2xl p-5 space-y-3"
                        >
                            <div className="flex items-center justify-between">
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor Tiket</p>
                                <p className="font-black text-primary-700 text-lg">{result.ticket_number}</p>
                            </div>
                            <div>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Judul</p>
                                <p className="font-bold text-gray-900">{result.title}</p>
                            </div>
                            <div className="flex items-center justify-between">
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider">Status</p>
                                <span className={`px-3 py-1 rounded-full text-sm font-bold ${getStatusColor(result.status)}`}>
                                    {getStatusLabel(result.status)}
                                </span>
                            </div>
                            <div className="flex items-center justify-between">
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider">Dilaporkan</p>
                                <p className="text-sm font-medium text-gray-700">
                                    {new Date(result.created_at).toLocaleDateString('id-ID', {
                                        day: '2-digit', month: 'long', year: 'numeric'
                                    })}
                                </p>
                            </div>
                        </motion.div>
                    )}
                </motion.div>
            </div>
        </AppLayout>
    );
}
