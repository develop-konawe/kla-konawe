import { Link } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { useState } from 'react';
import AppLayout from '../layouts/AppLayout';
import { type Page } from '../types';

interface AboutProps {
    page?: Page | null;
    stats?: {
        facilities_count: number;
        services_count: number;
        clusters_count: number;
        districts_count: number;
    };
}

interface ClusterDetail {
    id: number;
    title: string;
    shortTitle: string;
    color: string;
    accentColor: string;
    bgGradient: string;
    icon: string;
    tagline: string;
    description: string;
    indicators: string[];
    localActions: string[];
    linkHref?: string;
    linkLabel?: string;
}

const CLUSTERS: ClusterDetail[] = [
    {
        id: 1,
        title: 'Klaster 1: Hak Sipil & Kebebasan',
        shortTitle: 'Hak Sipil & Kebebasan',
        color: 'blue',
        accentColor: 'text-blue-600',
        bgGradient: 'from-blue-500 to-indigo-600',
        icon: 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2',
        tagline: 'Setiap anak berhak atas identitas resmi, akses informasi sehat, dan ruang partisipasi.',
        description: 'Memastikan seluruh anak di Kabupaten Konawe memiliki identitas kependudukan sejak lahir, mendapatkan informasi layak anak (bebas dari konten pornografi dan kekerasan), serta memiliki ruang berekspresi secara damai.',
        indicators: [
            'Cakupan penerbitan Akta Kelahiran anak mencapai 100%',
            'Kepemilikan Kartu Identitas Anak (KIA) di seluruh sekolah & desa',
            'Ketersediaan Informasi Layak Anak (ILA) di perpustakaan & internet publik',
            'Keberadaan dan keaktifan Forum Anak sebagai Pelopor dan Pelapor (2P)',
        ],
        localActions: [
            'Integrasi jemput bola pencetakan KIA di PAUD, SD, dan SMP oleh Disdukcapil Konawe',
            'Pojok Baca & Literasi Digital Ramah Anak di Dinas Perpustakaan Daerah',
            'Pelibatan Forum Anak Konawe (FAK) dalam Musrenbang Penyusunan RKPD',
        ],
        linkHref: '/layanan/penerbitan-kia-online-dan-sekolah',
        linkLabel: 'Lihat Layanan Penerbitan KIA',
    },
    {
        id: 2,
        title: 'Klaster 2: Lingkungan Keluarga & Pengasuhan Alternatif',
        shortTitle: 'Lingkungan Keluarga',
        color: 'emerald',
        accentColor: 'text-emerald-600',
        bgGradient: 'from-emerald-500 to-teal-600',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        tagline: 'Keluarga tangguh tanpa perkawinan usia anak dan pengasuhan berkualitas.',
        description: 'Mewujudkan lingkungan pengasuhan yang aman, penuh kasih sayang, dan bertanggung jawab di tingkat keluarga, serta menekan angka perkawinan usia dini.',
        indicators: [
            'Pencegahan dan penurunan angka perkawinan usia anak (dispen nikah)',
            'Ketersediaan Lembaga Konsultasi Pengasuhan Keluarga (PUSPAGA)',
            'Standardisasi Lembaga Kesejahteraan Sosial Anak (LKSA)',
            'Penyediaan fasilitas penitipan anak (daycare) ramah anak di perkantoran',
        ],
        localActions: [
            'PUSPAGA Kencana Konawe: Layanan konseling parenting gratis bagi orang tua',
            'Sosialisasi berkala pencegahan perkawinan anak di 28 kecamatan bersama KUA & Pengadilan Agama',
            'Pembinaan pola asuh anak dan remaja (PAAR) bersama Tim Penggerak PKK Konawe',
        ],
        linkHref: '/layanan/puspaga-kencana-konawe',
        linkLabel: 'Lihat Layanan PUSPAGA',
    },
    {
        id: 3,
        title: 'Klaster 3: Kesehatan Dasar & Kesejahteraan',
        shortTitle: 'Kesehatan Dasar',
        color: 'rose',
        accentColor: 'text-rose-600',
        bgGradient: 'from-rose-500 to-red-600',
        icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
        tagline: 'Gizi seimbang, bebas stunting, dan fasilitas kesehatan ramah anak.',
        description: 'Menjamin akses pelayanan kesehatan dasar bermutu, gizi optimal untuk pencegahan gagal tumbuh (stunting), air bersih, serta lingkungan bebas asap rokok demi tumbuh kembang sempurna anak.',
        indicators: [
            'Penurunan prevalensi stunting balita secara terukur dan berkelanjutan',
            'Pelayanan Ramah Anak di Puskesmas (PRAP) di seluruh Puskesmas Konawe',
            'Pemberian ASI Eksklusif dan ketersediaan ruang laktasi di fasilitas publik',
            'Penerapan Kawasan Tanpa Rokok (KTR) 100% di faskes dan sekolah',
        ],
        localActions: [
            'Puskesmas Ramah Anak (PRAP) Unaaha & Wawotobi dengan ruang bermain dan poli anak khusus',
            'Pemberian Makanan Tambahan (PMT) berbahan pangan lokal untuk balita berisiko stunting',
            'Program Pelayanan Kesehatan Peduli Remaja (PKPR) pencegahan anemia siswi SMP & SMA',
        ],
        linkHref: '/fasilitas/puskesmas-ramah-anak-unaaha',
        linkLabel: 'Lihat Faskes Ramah Anak',
    },
    {
        id: 4,
        title: 'Klaster 4: Pendidikan, Waktu Luang & Seni Budaya',
        shortTitle: 'Pendidikan & Budaya',
        color: 'purple',
        accentColor: 'text-purple-600',
        bgGradient: 'from-purple-500 to-indigo-600',
        icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        tagline: 'Sekolah aman bebas perundungan, sarana bermain ramah anak & pelestarian budaya lokal.',
        description: 'Mewujudkan ekosistem sekolah tanpa kekerasan fisik maupun psikis, menyediakan wahana rekreasi ramah anak, serta menumbuhkan kebanggaan seni budaya daerah sejak dini.',
        indicators: [
            'Penerapan standarisasi Sekolah Ramah Anak (SRA) pada PAUD, SD, dan SMP',
            'Ketersediaan Ruang Bermain Ramah Anak (RBRA) yang tersertifikasi',
            'Akses Rute Aman Selamat ke dan dari Sekolah (RASS)',
            'Pendidikan inklusif bagi anak disabilitas di sekolah formal',
        ],
        localActions: [
            'Deklarasi 50+ Sekolah Ramah Anak (SRA) bebas bullying & kekerasan verbal',
            'Ruang Bermain Ramah Anak (RBRA) Kompleks Kantor Bupati Konawe bersertifikasi KemenPPPA',
            'Festival seni budaya anak dan lomba permainan tradisional daerah se-Konawe',
        ],
        linkHref: '/fasilitas/rbra-kantor-bupati-konawe',
        linkLabel: 'Lihat RBRA Konawe',
    },
    {
        id: 5,
        title: 'Klaster 5: Perlindungan Khusus',
        shortTitle: 'Perlindungan Khusus',
        color: 'amber',
        accentColor: 'text-amber-600',
        bgGradient: 'from-amber-500 to-orange-600',
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        tagline: 'Respons cepat penanganan anak korban kekerasan, eksploitasi, dan kelompok rentan.',
        description: 'Memberikan perlindungan terpadu, pendampingan hukum, medis, serta pemulihan psikososial bagi anak-anak dalam situasi darurat, anak korban kekerasan, berhadapan dengan hukum, dan anak penyandang disabilitas.',
        indicators: [
            'UPTD PPA aktif melayani penanganan kasus kekerasan anak 24 Jam',
            'Penerapan keadilan restoratif (restorative justice) bagi anak yang berhadapan dengan hukum',
            'Pencegahan pekerja anak dan eksploitasi ekonomi/seksual anak',
            'Perlindungan anak penyandang disabilitas dan anak korban bencana',
        ],
        localActions: [
            'UPTD PPA Konawe siaga hotline 24 jam dengan tim psikolog & paralegal gratis',
            'SOP bersama Polres Konawe, Bapas, dan Pengadilan Negeri Unaaha untuk diversii anak',
            'Rumah Aman (Safe House) sementara bagi anak dan perempuan korban kekerasan',
        ],
        linkHref: '/layanan/layanan-pengaduan-pendampingan-uptd-ppa',
        linkLabel: 'Hubungi UPTD PPA',
    },
];

const GUGUS_TUGAS = [
    {
        name: 'Badan Perencanaan Pembangunan Daerah (Bappeda)',
        role: 'Perencanaan & Penganggaran',
        desc: 'Memastikan seluruh indikator KLA terintegrasi dalam RPJMD, Renstra, dan RKPD Kabupaten Konawe.',
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    },
    {
        name: 'Dinas P3A Kab. Konawe',
        role: 'Sekretariat & Koordinator Utama',
        desc: 'Penyelenggara koordinasi teknis, monitoring evaluasi, pendampingan UPTD PPA, dan penguatan Forum Anak.',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    },
    {
        name: 'Dinas Pendidikan & Kebudayaan',
        role: 'Pendidikan & Sekolah Ramah Anak',
        desc: 'Standardisasi Sekolah Ramah Anak (SRA), pencegahan putus sekolah, dan rute aman selamat sekolah.',
        icon: 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
    },
    {
        name: 'Dinas Kesehatan & RSUD Konawe',
        role: 'Kesehatan Dasar & Gizi Balita',
        desc: 'Pelayanan Ramah Anak di Puskesmas (PRAP), penanganan stunting, imunisasi lengkap, dan PKPR.',
        icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
    },
    {
        name: 'Dinas Kependudukan & Pencatatan Sipil',
        role: 'Hak Sipil & Identitas Anak',
        desc: 'Percepatan kepemilikan Akta Kelahiran dan Kartu Identitas Anak (KIA) secara daring dan jemput bola.',
        icon: 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0',
    },
    {
        name: 'Dinas Komunikasi & Informatika',
        role: 'Informasi Layak Anak & Portal KLA',
        desc: 'Pengembangan portal terpadu KLA, perlindungan anak di ranah siber, dan penyebaran konten edukatif.',
        icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
    },
    {
        name: 'Kepolisian Resor (Polres) & Kejaksaan',
        role: 'Penegakan Hukum & Restoratif',
        desc: 'Penerapan diversi bagi anak berhadapan hukum dan penindakan tegas bagi pelaku kekerasan anak.',
        icon: 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
    },
    {
        name: 'Forum Anak Konawe (FAK)',
        role: 'Pelopor & Pelapor (2P)',
        desc: 'Wadah partisipasi murni anak untuk menyampaikan suara, aspirasi, serta edukasi sebaya di 28 kecamatan.',
        icon: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
    },
];

const FAQS = [
    {
        q: 'Apa itu Kabupaten Layak Anak (KLA)?',
        a: 'Kabupaten Layak Anak (KLA) adalah kabupaten yang mempunyai sistem pembangunan berbasis hak anak melalui pengintegrasian komitmen dan sumber daya pemerintah, masyarakat, media, dan dunia usaha, yang terencana secara menyeluruh dan berkelanjutan dalam kebijakan, program, dan kegiatan untuk menjamin terpenuhinya hak dan perlindungan anak.',
    },
    {
        q: 'Apakah KLA hanya tentang membangun taman bermain dan fasilitas fisik?',
        a: 'Tidak. Taman bermain ramah anak hanyalah satu dari 24 indikator KLA. KLA mencakup pemenuhan dokumen akta lahir & KIA (Klaster 1), pencegahan pernikahan dini & pengasuhan anak (Klaster 2), gizi dan puskesmas ramah anak (Klaster 3), sekolah bebas perundungan (Klaster 4), serta perlindungan hukum korban kekerasan (Klaster 5).',
    },
    {
        q: 'Bagaimana peran anak dalam program KLA Konawe?',
        a: 'Anak dilibatkan secara aktif melalui Forum Anak Konawe (FAK) dengan peran 2P (Pelopor dan Pelapor). Anak berhak mengusulkan program saat Musrenbang Anak, menjadi edukator sebaya (anti-bullying, bahaya rokok/narkoba), dan berani melaporkan pelanggaran hak anak di sekitarnya.',
    },
    {
        q: 'Siapa yang dapat melapor jika terjadi kekerasan terhadap anak?',
        a: 'Siapa saja: anak korban, teman sebaya, orang tua, guru, maupun masyarakat sekitar yang melihat atau mendengar adanya indikasi kekerasan, pelecehan, atau penelantaran anak. Laporan dapat dikirimkan melalui menu Pengaduan di portal ini atau via Hotline UPTD PPA Konawe 24 Jam.',
    },
    {
        q: 'Apakah seluruh layanan bagi anak di portal ini dipungut biaya?',
        a: 'Seluruh layanan publik yang diselenggarakan oleh Pemerintah Daerah Kabupaten Konawe dalam kerangka KLA (pengaduan UPTD PPA, pembuatan KIA, konsultasi PUSPAGA, dan fasilitas publik) adalah 100% GRATIS tanpa dipungut biaya apapun.',
    },
];

const QUIZ_QUESTIONS = [
    {
        id: 1,
        question: 'Berapakah jumlah klaster hak anak yang menjadi pilar utama penilaian Kabupaten Layak Anak?',
        options: ['3 Klaster', '5 Klaster', '7 Klaster'],
        correctIndex: 1,
        explanation: 'KLA didasarkan pada 5 Klaster Hak Anak sesuai Konvensi Hak Anak PBB dan Permen PPPA.',
    },
    {
        id: 2,
        question: 'Apakah pembuatan Kartu Identitas Anak (KIA) dan Akta Kelahiran di Konawe dipungut biaya?',
        options: ['Gratis (Rp 0,-)', 'Ada biaya administrasi desa', 'Berbayar untuk usia tertentu'],
        correctIndex: 0,
        explanation: 'Pembuatan dokumen kependudukan anak di Kabupaten Konawe 100% GRATIS dan bebas pungutan.',
    },
    {
        id: 3,
        question: 'Apa singkatan dari peran 2P yang diemban oleh Forum Anak Konawe?',
        options: ['Pemimpin dan Pengawas', 'Pelopor dan Pelapor', 'Pendidik dan Pembina'],
        correctIndex: 1,
        explanation: '2P adalah Pelopor (agen perubahan positif) dan Pelapor (berani menyuarakan hak dan melaporkan kekerasan).',
    },
];

export default function About({ page, stats }: AboutProps) {
    const [activeTab, setActiveTab] = useState<'overview' | 'clusters' | 'governance' | 'roadmap' | 'regulations'>('overview');
    const [selectedClusterId, setSelectedClusterId] = useState<number>(1);
    const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(0);

    // Quiz states
    const [quizAnswers, setQuizAnswers] = useState<{ [key: number]: number }>({});
    const [quizSubmitted, setQuizSubmitted] = useState<boolean>(false);

    const activeCluster = CLUSTERS.find((c) => c.id === selectedClusterId) || CLUSTERS[0];

    const handleQuizOptionClick = (questionId: number, optionIndex: number) => {
        if (quizSubmitted) return;
        setQuizAnswers((prev) => ({ ...prev, [questionId]: optionIndex }));
    };

    const calculateQuizScore = () => {
        let score = 0;
        QUIZ_QUESTIONS.forEach((q) => {
            if (quizAnswers[q.id] === q.correctIndex) {
                score += 1;
            }
        });
        return score;
    };

    return (
        <AppLayout title="Tentang Kabupaten Layak Anak (KLA) - Konawe">
            {/* HERO SECTION */}
            <div className="relative bg-gradient-to-br from-primary-700 via-primary-800 to-indigo-950 pt-36 pb-24 text-white overflow-hidden">
                {/* Decorative floating blur circles */}
                <div className="absolute inset-0 pointer-events-none opacity-20">
                    <div className="absolute -top-24 -left-24 w-96 h-96 bg-primary-400 rounded-full blur-3xl" />
                    <div className="absolute top-1/2 right-4 w-96 h-96 bg-secondary-400 rounded-full blur-3xl" />
                    <div className="absolute -bottom-24 left-1/3 w-80 h-80 bg-sky-300 rounded-full blur-2xl" />
                </div>

                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                    <div className="max-w-4xl mx-auto">
                        {/* Badges Ribbon */}
                        <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs sm:text-sm font-semibold text-primary-100 mb-6 shadow-sm">
                            <span className="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse" />
                            Gugus Tugas KLA • Pemerintah Daerah Kabupaten Konawe
                        </div>

                        {/* Title */}
                        <h1 className="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black tracking-tight leading-tight mb-6">
                            Mewujudkan Konawe Sebagai Daerah Ramah, Aman &amp; Layak Bagi Setiap Anak
                        </h1>

                        <p className="text-base sm:text-lg md:text-xl text-primary-100 font-medium leading-relaxed max-w-3xl mx-auto mb-10">
                            Kabupaten Layak Anak (KLA) mengintegrasikan komitmen lintas sektor pemerintah, masyarakat, media, dan dunia usaha untuk menjamin pemenuhan hak dan perlindungan anak secara menyeluruh.
                        </p>

                        {/* Fast Actions */}
                        <div className="flex flex-wrap items-center justify-center gap-3.5">
                            <button
                                type="button"
                                onClick={() => setActiveTab('clusters')}
                                className="px-6 py-3.5 rounded-full bg-white text-primary-800 hover:bg-primary-50 font-black text-sm shadow-xl shadow-primary-950/20 transition hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-2"
                            >
                                <svg className="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Jelajahi 5 Klaster KLA
                            </button>
                            <button
                                type="button"
                                onClick={() => setActiveTab('governance')}
                                className="px-6 py-3.5 rounded-full bg-primary-600/60 hover:bg-primary-600 border border-white/20 text-white font-bold text-sm backdrop-blur-sm transition hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-2"
                            >
                                <svg className="w-5 h-5 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Sinergi OPD &amp; Gugus Tugas
                            </button>
                        </div>
                    </div>

                    {/* METRICS COUNTER BAR */}
                    <div className="mt-14 max-w-5xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-4 text-left">
                        <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 sm:p-5 shadow-lg">
                            <div className="text-2xl sm:text-3xl font-black text-white">5 Klaster</div>
                            <div className="text-xs sm:text-sm text-primary-200 mt-1 font-medium">Pilar Utama Hak Anak KemenPPPA</div>
                        </div>

                        <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 sm:p-5 shadow-lg">
                            <div className="text-2xl sm:text-3xl font-black text-white">24 Indikator</div>
                            <div className="text-xs sm:text-sm text-primary-200 mt-1 font-medium">Standarisasi Evaluasi Terukur</div>
                        </div>

                        <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 sm:p-5 shadow-lg">
                            <div className="text-2xl sm:text-3xl font-black text-white">28 Kecamatan</div>
                            <div className="text-xs sm:text-sm text-primary-200 mt-1 font-medium">Komitmen Bersama se-Konawe</div>
                        </div>

                        <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 sm:p-5 shadow-lg">
                            <div className="text-2xl sm:text-3xl font-black text-emerald-300">100% Gratis</div>
                            <div className="text-xs sm:text-sm text-primary-200 mt-1 font-medium">Layanan Publik Ramah Anak</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* STICKY INTERACTIVE SUBNAV TABS */}
            <div className="sticky top-16 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center space-x-1 sm:space-x-2 overflow-x-auto py-3 no-scrollbar">
                        <button
                            type="button"
                            onClick={() => setActiveTab('overview')}
                            className={`px-4 py-2.5 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-2 ${
                                activeTab === 'overview'
                                    ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                            }`}
                        >
                            <span>🌟</span>
                            <span>Visi &amp; Prinsip Dasar</span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('clusters')}
                            className={`px-4 py-2.5 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-2 ${
                                activeTab === 'clusters'
                                    ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                            }`}
                        >
                            <span>🛡️</span>
                            <span>5 Klaster Hak Anak</span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('governance')}
                            className={`px-4 py-2.5 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-2 ${
                                activeTab === 'governance'
                                    ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                            }`}
                        >
                            <span>🏛️</span>
                            <span>Gugus Tugas &amp; OPD</span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('roadmap')}
                            className={`px-4 py-2.5 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-2 ${
                                activeTab === 'roadmap'
                                    ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                            }`}
                        >
                            <span>📈</span>
                            <span>Tahapan Evaluasi</span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('regulations')}
                            className={`px-4 py-2.5 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-2 ${
                                activeTab === 'regulations'
                                    ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                            }`}
                        >
                            <span>⚖️</span>
                            <span>Dasar Hukum</span>
                        </button>
                    </div>
                </div>
            </div>

            {/* MAIN TAB CONTENT AREA */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <AnimatePresence mode="wait">
                    {/* TAB 1: VISI, MISI & PRINSIP */}
                    {activeTab === 'overview' && (
                        <motion.div
                            key="overview"
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -16 }}
                            transition={{ duration: 0.3 }}
                            className="space-y-12"
                        >
                            {/* Definition Callout */}
                            <div className="bg-gradient-to-br from-primary-50 via-indigo-50 to-white rounded-3xl p-8 sm:p-12 border border-primary-100 shadow-xl shadow-primary-100/30 relative overflow-hidden">
                                <div className="max-w-3xl">
                                    <span className="text-xs font-bold uppercase tracking-wider text-primary-600 bg-primary-100/80 px-3 py-1 rounded-full">
                                        Landasan Pemikiran
                                    </span>
                                    <h2 className="text-2xl sm:text-3xl font-black text-slate-900 mt-4 mb-4">
                                        Apa itu Kabupaten Layak Anak (KLA)?
                                    </h2>
                                    <p className="text-slate-700 text-base sm:text-lg leading-relaxed font-normal">
                                        Kabupaten Layak Anak (KLA) adalah sistem pembangunan daerah yang menempatkan hak anak sebagai pertimbangan utama dalam setiap perencanaan, penganggaran, kebijakan, dan implementasi program. Tujuannya adalah memastikan setiap anak di Kabupaten Konawe tumbuh sehat, berpendidikan, terlindungi dari kekerasan, serta memiliki masa depan cerah.
                                    </p>
                                </div>
                            </div>

                            {/* Visi & Misi Grid */}
                            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                                {/* Visi: 5 Cols */}
                                <div className="lg:col-span-5 bg-gradient-to-br from-primary-600 to-indigo-800 rounded-3xl p-8 sm:p-10 text-white flex flex-col justify-between shadow-xl shadow-primary-600/20">
                                    <div>
                                        <div className="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center font-black mb-6">
                                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                        <span className="text-xs font-bold uppercase tracking-wider text-primary-200">
                                            Visi KLA Kabupaten Konawe
                                        </span>
                                        <h3 className="text-2xl sm:text-3xl font-black mt-2 mb-6 leading-tight">
                                            &ldquo;Terwujudnya Kabupaten Konawe yang Ramah, Aman, dan Sejahtera bagi Tumbuh Kembang Setiap Anak.&rdquo;
                                        </h3>
                                        <p className="text-primary-100 text-sm leading-relaxed">
                                            Di mana setiap anak Konawe memiliki hak hidup, hak tumbuh kembang optimal, hak perlindungan seutuhnya, serta hak berpartisipasi aktif dalam kemajuan daerah.
                                        </p>
                                    </div>
                                    <div className="mt-8 pt-6 border-t border-white/20 text-xs text-primary-200">
                                        Komitmen Terintegrasi Pemerintah Daerah &amp; Seluruh OPD Konawe
                                    </div>
                                </div>

                                {/* Misi: 7 Cols */}
                                <div className="lg:col-span-7 bg-white rounded-3xl p-8 sm:p-10 border border-slate-100 shadow-xl shadow-slate-200/60">
                                    <div className="flex items-center gap-3 mb-6">
                                        <div className="w-12 h-12 rounded-2xl bg-secondary-100 text-secondary-600 flex items-center justify-center font-black">
                                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span className="text-xs font-bold uppercase tracking-wider text-secondary-600">
                                                Langkah Strategis
                                            </span>
                                            <h3 className="text-2xl font-black text-slate-900">
                                                5 Misi Utama KLA Konawe
                                            </h3>
                                        </div>
                                    </div>

                                    <div className="space-y-4">
                                        {[
                                            'Menjamin pemenuhan hak sipil, identitas hukum (akta & KIA), serta kebebasan berekspresi positif bagi seluruh anak.',
                                            'Mengembangkan ketahanan keluarga dan pengasuhan alternatif yang aman guna mencegah perkawinan usia dini.',
                                            'Meningkatkan derajat kesehatan dasar, gizi seimbang, sanitasi ramah anak, dan penurunan stunting secara berkelanjutan.',
                                            'Mewujudkan Sekolah Ramah Anak (SRA) bebas kekerasan dan menyediakan sarana ruang bermain ramah anak terstandardisasi.',
                                            'Memperkuat sistem respons cepat perlindungan khusus dan pendampingan bagi anak yang mengalami kekerasan atau berhadapan hukum.',
                                        ].map((misi, index) => (
                                            <div key={index} className="flex items-start gap-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-slate-100/70 transition">
                                                <div className="w-7 h-7 rounded-full bg-primary-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    {index + 1}
                                                </div>
                                                <p className="text-sm text-slate-700 font-medium leading-relaxed">
                                                    {misi}
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>

                            {/* 4 Prinsip Konvensi Hak Anak */}
                            <div>
                                <div className="text-center max-w-2xl mx-auto mb-8">
                                    <span className="text-xs font-bold uppercase tracking-wider text-primary-600">
                                        Prinsip Universal
                                    </span>
                                    <h3 className="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                                        4 Prinsip Hak Anak Internasional (CRC PBB)
                                    </h3>
                                    <p className="text-sm text-slate-500 mt-2">
                                        Seluruh kebijakan KLA Kabupaten Konawe berpedoman teguh pada 4 prinsip utama perlindungan anak dunia.
                                    </p>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div className="bg-white p-6 rounded-3xl border border-slate-100 shadow-md hover:shadow-xl transition">
                                        <div className="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold mb-4">
                                            1
                                        </div>
                                        <h4 className="text-base font-bold text-slate-900 mb-2">Non-Diskriminasi</h4>
                                        <p className="text-xs text-slate-600 leading-relaxed">
                                            Semua anak berhak atas perlindungan tanpa memandang suku, agama, jenis kelamin, latar belakang sosial, atau kondisi disabilitas.
                                        </p>
                                    </div>

                                    <div className="bg-white p-6 rounded-3xl border border-slate-100 shadow-md hover:shadow-xl transition">
                                        <div className="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mb-4">
                                            2
                                        </div>
                                        <h4 className="text-base font-bold text-slate-900 mb-2">Kepentingan Terbaik Anak</h4>
                                        <p className="text-xs text-slate-600 leading-relaxed">
                                            Setiap keputusan hukum, kebijakan publik, dan tindakan administratif wajib mendahulukan apa yang terbaik bagi masa depan anak.
                                        </p>
                                    </div>

                                    <div className="bg-white p-6 rounded-3xl border border-slate-100 shadow-md hover:shadow-xl transition">
                                        <div className="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold mb-4">
                                            3
                                        </div>
                                        <h4 className="text-base font-bold text-slate-900 mb-2">Hak Hidup &amp; Berkembang</h4>
                                        <p className="text-xs text-slate-600 leading-relaxed">
                                            Jaminan kelangsungan hidup dan tumbuh kembang jasmani, rohani, intelektual, serta emosional anak secara maksimal.
                                        </p>
                                    </div>

                                    <div className="bg-white p-6 rounded-3xl border border-slate-100 shadow-md hover:shadow-xl transition">
                                        <div className="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold mb-4">
                                            4
                                        </div>
                                        <h4 className="text-base font-bold text-slate-900 mb-2">Menghargai Suara Anak</h4>
                                        <p className="text-xs text-slate-600 leading-relaxed">
                                            Anak memiliki hak untuk mengutarakan pendapat secara bebas dan pandangannya wajib dipertimbangkan dalam setiap urusan yang mempengaruhinya.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </motion.div>
                    )}

                    {/* TAB 2: 5 KLASTER HAK ANAK (INTERACTIVE EXPLORER) */}
                    {activeTab === 'clusters' && (
                        <motion.div
                            key="clusters"
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -16 }}
                            transition={{ duration: 0.3 }}
                            className="space-y-10"
                        >
                            <div className="text-center max-w-3xl mx-auto">
                                <span className="text-xs font-bold uppercase tracking-wider text-primary-600">
                                    Pilar Evaluasi KemenPPPA
                                </span>
                                <h2 className="text-3xl sm:text-4xl font-black text-slate-900 mt-1">
                                    5 Klaster Hak Anak Kabupaten Konawe
                                </h2>
                                <p className="text-slate-600 mt-3 text-base">
                                    Pilih klaster di bawah ini untuk melihat indikator penilaian, target capaian, serta aksi nyata yang dilaksanakan di Kabupaten Konawe.
                                </p>
                            </div>

                            {/* CLUSTER SELECTION PILLS */}
                            <div className="grid grid-cols-2 sm:grid-cols-5 gap-3">
                                {CLUSTERS.map((cl) => {
                                    const isSelected = selectedClusterId === cl.id;
                                    return (
                                        <button
                                            key={cl.id}
                                            type="button"
                                            onClick={() => setSelectedClusterId(cl.id)}
                                            className={`p-4 rounded-2xl text-left transition-all duration-300 flex flex-col justify-between border ${
                                                isSelected
                                                    ? 'bg-slate-900 text-white shadow-xl shadow-slate-900/20 border-slate-900 scale-[1.02]'
                                                    : 'bg-white text-slate-700 hover:bg-slate-50 border-slate-200'
                                            }`}
                                        >
                                            <div className="flex items-center justify-between mb-3">
                                                <span className={`w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm ${
                                                    isSelected ? 'bg-white text-slate-900' : 'bg-slate-100 text-slate-700'
                                                }`}>
                                                    {cl.id}
                                                </span>
                                                {isSelected && (
                                                    <span className="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping" />
                                                )}
                                            </div>
                                            <div className="text-xs sm:text-sm font-bold line-clamp-2">
                                                {cl.shortTitle}
                                            </div>
                                        </button>
                                    );
                                })}
                            </div>

                            {/* ACTIVE CLUSTER DEEP-DIVE CARD */}
                            <div className="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
                                <div className={`p-8 sm:p-10 bg-gradient-to-r ${activeCluster.bgGradient} text-white`}>
                                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div>
                                            <span className="px-3 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
                                                Klaster {activeCluster.id}
                                            </span>
                                            <h3 className="text-2xl sm:text-3xl font-black mt-2">
                                                {activeCluster.title}
                                            </h3>
                                            <p className="text-white/90 text-sm sm:text-base mt-2 max-w-2xl">
                                                {activeCluster.tagline}
                                            </p>
                                        </div>
                                        {activeCluster.linkHref && (
                                            <Link
                                                href={activeCluster.linkHref}
                                                className="px-5 py-3 rounded-2xl bg-white text-slate-900 font-bold text-xs sm:text-sm shadow-md hover:bg-slate-50 transition active:scale-95 flex-shrink-0 inline-flex items-center gap-1.5"
                                            >
                                                {activeCluster.linkLabel}
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                                </svg>
                                            </Link>
                                        )}
                                    </div>
                                </div>

                                <div className="p-8 sm:p-10 space-y-8">
                                    <div>
                                        <h4 className="text-base font-bold text-slate-900 mb-2">Penjelasan Fokus Klaster</h4>
                                        <p className="text-slate-600 text-sm leading-relaxed">
                                            {activeCluster.description}
                                        </p>
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-slate-100">
                                        {/* Indikator Standar */}
                                        <div className="space-y-4">
                                            <h4 className="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                                                <svg className="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Indikator Evaluasi Nasional (KemenPPPA)
                                            </h4>
                                            <ul className="space-y-3">
                                                {activeCluster.indicators.map((ind, i) => (
                                                    <li key={i} className="flex items-start gap-3 text-xs sm:text-sm text-slate-700 font-medium">
                                                        <span className="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                                            ✓
                                                        </span>
                                                        <span>{ind}</span>
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>

                                        {/* Aksi Nyata di Konawe */}
                                        <div className="space-y-4">
                                            <h4 className="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                                                <svg className="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                Aksi Nyata &amp; Inovasi di Kabupaten Konawe
                                            </h4>
                                            <ul className="space-y-3">
                                                {activeCluster.localActions.map((act, i) => (
                                                    <li key={i} className="flex items-start gap-3 text-xs sm:text-sm text-slate-700 font-medium bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                                        <span className="w-5 h-5 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                                            {i + 1}
                                                        </span>
                                                        <span>{act}</span>
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </motion.div>
                    )}

                    {/* TAB 3: GUGUS TUGAS & SINERGI LINTAS SEKTOR */}
                    {activeTab === 'governance' && (
                        <motion.div
                            key="governance"
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -16 }}
                            transition={{ duration: 0.3 }}
                            className="space-y-12"
                        >
                            <div className="text-center max-w-3xl mx-auto">
                                <span className="text-xs font-bold uppercase tracking-wider text-primary-600">
                                    Kolaborasi Multi-Stakeholder
                                </span>
                                <h2 className="text-3xl sm:text-4xl font-black text-slate-900 mt-1">
                                    Gugus Tugas KLA Kabupaten Konawe
                                </h2>
                                <p className="text-slate-600 mt-3 text-base">
                                    Kabupaten Layak Anak bukan kerja satu instansi saja, melainkan sinergi terpadu Organisasi Perangkat Daerah (OPD), aparat penegak hukum, dan organisasi masyarakat.
                                </p>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                {GUGUS_TUGAS.map((inst, index) => (
                                    <div
                                        key={index}
                                        className="bg-white rounded-3xl p-6 border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between"
                                    >
                                        <div>
                                            <div className="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mb-4">
                                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={inst.icon} />
                                                </svg>
                                            </div>
                                            <span className="text-[11px] font-bold uppercase tracking-wider text-primary-600 block mb-1">
                                                {inst.role}
                                            </span>
                                            <h4 className="text-base font-bold text-slate-900 mb-2 leading-snug">
                                                {inst.name}
                                            </h4>
                                            <p className="text-xs text-slate-500 leading-relaxed">
                                                {inst.desc}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {/* FORUM ANAK SPOTLIGHT */}
                            <div className="bg-gradient-to-r from-amber-500 via-orange-500 to-rose-600 rounded-3xl p-8 sm:p-12 text-white shadow-xl shadow-orange-500/20 relative overflow-hidden">
                                <div className="max-w-3xl">
                                    <span className="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block backdrop-blur-sm">
                                        Wadah Partisipasi Murni Anak
                                    </span>
                                    <h3 className="text-2xl sm:text-3xl font-black mb-4">
                                        Forum Anak Konawe (FAK): Pelopor &amp; Pelapor (2P)
                                    </h3>
                                    <p className="text-amber-50 text-base sm:text-lg leading-relaxed mb-6 font-medium">
                                        Anak-anak di Kabupaten Konawe tidak hanya menjadi objek penerima kebijakan, namun subjek utama pembangunan. Melalui Forum Anak, mereka aktif menyuarakan hak teman sebayanya ke meja pimpinan daerah.
                                    </p>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm font-semibold">
                                        <div className="bg-white/10 backdrop-blur-sm p-4 rounded-2xl border border-white/20">
                                            <div className="text-base font-bold text-amber-200 mb-1">🗣️ Sebagai Pelopor</div>
                                            <p className="text-xs text-amber-50 font-normal">
                                                Menjadi teladan kebaikan, mengkampanyekan pencegahan perundungan di sekolah, dan mempromosikan pola hidup sehat.
                                            </p>
                                        </div>
                                        <div className="bg-white/10 backdrop-blur-sm p-4 rounded-2xl border border-white/20">
                                            <div className="text-base font-bold text-amber-200 mb-1">📢 Sebagai Pelapor</div>
                                            <p className="text-xs text-amber-50 font-normal">
                                                Berani bersuara dan menyampaikan pengaduan ketika melihat diskriminasi atau tindak kekerasan terhadap anak di sekitarnya.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </motion.div>
                    )}

                    {/* TAB 4: TAHAPAN EVALUASI KLA */}
                    {activeTab === 'roadmap' && (
                        <motion.div
                            key="roadmap"
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -16 }}
                            transition={{ duration: 0.3 }}
                            className="space-y-12"
                        >
                            <div className="text-center max-w-3xl mx-auto">
                                <span className="text-xs font-bold uppercase tracking-wider text-primary-600">
                                    Standarisasi Nasional
                                </span>
                                <h2 className="text-3xl sm:text-4xl font-black text-slate-900 mt-1">
                                    5 Tingkatan Kategori Kabupaten Layak Anak
                                </h2>
                                <p className="text-slate-600 mt-3 text-base">
                                    Kementerian PPPA menetapkan evaluasi berkala dengan ambang skor nilai untuk mengukur kematangan ekosistem ramah anak di daerah.
                                </p>
                            </div>

                            {/* ROADMAP PROGRESS CARDS */}
                            <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
                                {[
                                    { level: '1. Pratama', score: 'Skor 500 - 600', desc: 'Pemenuhan komitmen dasar regulasi, gugus tugas aktif, dan inisiasi program KLA di tingkat kabupaten.', active: true },
                                    { level: '2. Madya', score: 'Skor 601 - 700', desc: 'Penguatan kelembagaan, pembentukan forum anak kecamatan, dan sertifikasi fasilitas publik.', active: true },
                                    { level: '3. Nindya', score: 'Skor 701 - 800', desc: 'Cakupan luas SRA, faskes ramah anak, dan penurunan signifikan kasus kekerasan serta pernikahan dini.', active: false },
                                    { level: '4. Utama', score: 'Skor 801 - 900', desc: 'Seluruh kecamatan dan desa memiliki kebijakan ramah anak yang mandiri dan terukur.', active: false },
                                    { level: '5. KLA Penuh', score: 'Skor 901 - 1000', desc: 'Kabupaten/Kota Layak Anak seutuhnya tanpa kompromi, menjadi rujukan perlindungan anak nasional.', active: false },
                                ].map((step, idx) => (
                                    <div
                                        key={idx}
                                        className={`p-6 rounded-3xl border flex flex-col justify-between transition-all duration-300 ${
                                            step.active
                                                ? 'bg-primary-50/80 border-primary-200 shadow-md ring-2 ring-primary-500/20'
                                                : 'bg-white border-slate-100 opacity-80'
                                        }`}
                                    >
                                        <div>
                                            <div className="flex items-center justify-between mb-3">
                                                <span className={`text-xs font-bold px-2.5 py-1 rounded-full ${
                                                    step.active ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-600'
                                                }`}>
                                                    Tingkat {idx + 1}
                                                </span>
                                                {step.active && (
                                                    <span className="text-[10px] font-bold text-primary-700 bg-primary-100 px-2 py-0.5 rounded-full">
                                                        Tahap Konawe
                                                    </span>
                                                )}
                                            </div>
                                            <h4 className="text-base font-bold text-slate-900 mb-1">{step.level}</h4>
                                            <div className="text-xs font-semibold text-primary-600 mb-3">{step.score}</div>
                                            <p className="text-xs text-slate-600 leading-relaxed">
                                                {step.desc}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {/* EVALUASI INDIKATOR BANNER */}
                            <div className="bg-slate-900 text-white rounded-3xl p-8 sm:p-10 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
                                <div>
                                    <span className="text-xs font-bold uppercase tracking-wider text-primary-400">
                                        Transparansi &amp; Akuntabilitas
                                    </span>
                                    <h3 className="text-xl sm:text-2xl font-black mt-1 mb-2">
                                        Akses Data &amp; Dokumen Evaluasi KLA Konawe
                                    </h3>
                                    <p className="text-slate-300 text-sm max-w-xl">
                                        Pemerintah Daerah Kabupaten Konawe secara terbuka mempublikasikan dokumen perencanaan dan laporan capaian tahunan Kabupaten Layak Anak.
                                    </p>
                                </div>
                                <Link
                                    href="/publikasi"
                                    className="px-6 py-3.5 rounded-2xl bg-primary-500 hover:bg-primary-400 text-white font-bold text-sm shadow-md transition active:scale-95 flex-shrink-0"
                                >
                                    Buka Direktori Publikasi
                                </Link>
                            </div>
                        </motion.div>
                    )}

                    {/* TAB 5: DASAR HUKUM & REGULASI */}
                    {activeTab === 'regulations' && (
                        <motion.div
                            key="regulations"
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -16 }}
                            transition={{ duration: 0.3 }}
                            className="space-y-8"
                        >
                            <div className="text-center max-w-3xl mx-auto">
                                <span className="text-xs font-bold uppercase tracking-wider text-primary-600">
                                    Landasan Yuridis
                                </span>
                                <h2 className="text-3xl sm:text-4xl font-black text-slate-900 mt-1">
                                    Dasar Hukum &amp; Regulasi KLA
                                </h2>
                                <p className="text-slate-600 mt-3 text-base">
                                    Penyelenggaraan Kabupaten Layak Anak di Kabupaten Konawe didasari oleh ketentuan perundang-undangan Republik Indonesia yang mengikat.
                                </p>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {[
                                    {
                                        title: 'Undang-Undang No. 35 Tahun 2014',
                                        subtitle: 'Perubahan atas UU No. 23 Tahun 2002 tentang Perlindungan Anak',
                                        desc: 'Menegaskan kewajiban negara, pemerintah daerah, dan masyarakat dalam menghormati dan menjamin hak asasi setiap anak tanpa diskriminasi.',
                                    },
                                    {
                                        title: 'Peraturan Presiden No. 25 Tahun 2021',
                                        subtitle: 'Kebijakan Kabupaten/Kota Layak Anak (KLA)',
                                        desc: 'Dasar hukum tertinggi operasional KLA di Indonesia, mengatur 24 indikator evaluasi dan keterlibatan lintas kementerian dan pemda.',
                                    },
                                    {
                                        title: 'Permen PPPA No. 12 Tahun 2022',
                                        subtitle: 'Penyelenggaraan Kabupaten/Kota Layak Anak',
                                        desc: 'Petunjuk teknis pengukuran, verifikasi lapangan, dan standarisasi klaster hak anak bagi pemerintah kabupaten/kota.',
                                    },
                                    {
                                        title: 'Peraturan Daerah & Keputusan Bupati Konawe',
                                        subtitle: 'Penyelenggaraan KLA & Pembentukan Gugus Tugas Kabupaten Konawe',
                                        desc: 'Regulasi tingkat daerah yang mengikat komitmen anggaran OPD dan pelibatan Forum Anak dalam perumusan kebijakan daerah.',
                                    },
                                ].map((reg, idx) => (
                                    <div key={idx} className="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-md hover:shadow-xl transition flex flex-col justify-between">
                                        <div>
                                            <div className="flex items-center gap-3 mb-3">
                                                <div className="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center font-bold">
                                                    §
                                                </div>
                                                <div>
                                                    <h4 className="text-base font-bold text-slate-900">{reg.title}</h4>
                                                    <div className="text-xs text-primary-600 font-semibold">{reg.subtitle}</div>
                                                </div>
                                            </div>
                                            <p className="text-xs sm:text-sm text-slate-600 leading-relaxed mt-3">
                                                {reg.desc}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </motion.div>
                    )}
                </AnimatePresence>

                {/* INTERACTIVE MINI QUIZ SECTION: "SEBERAPA TAHU ANDA TENTANG KLA KONAWE?" */}
                <div className="mt-20 bg-gradient-to-br from-indigo-50 via-primary-50 to-white rounded-3xl p-8 sm:p-12 border border-primary-100 shadow-xl">
                    <div className="text-center max-w-2xl mx-auto mb-8">
                        <span className="text-xs font-bold uppercase tracking-wider text-primary-600 bg-primary-100 px-3 py-1 rounded-full">
                            Kuis Interaktif
                        </span>
                        <h3 className="text-2xl sm:text-3xl font-black text-slate-900 mt-3">
                            Seberapa Tahu Anda tentang Hak Anak di Konawe?
                        </h3>
                        <p className="text-sm text-slate-600 mt-2">
                            Uji pemahaman Anda mengenai program Kabupaten Layak Anak melalui 3 pertanyaan singkat di bawah ini!
                        </p>
                    </div>

                    <div className="max-w-3xl mx-auto space-y-6">
                        {QUIZ_QUESTIONS.map((q, idx) => {
                            const userAnswer = quizAnswers[q.id];
                            const isAnswered = userAnswer !== undefined;
                            const isCorrect = isAnswered && userAnswer === q.correctIndex;

                            return (
                                <div key={q.id} className="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                                    <div className="flex items-start gap-3 mb-4">
                                        <span className="w-7 h-7 rounded-full bg-primary-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                            {idx + 1}
                                        </span>
                                        <h4 className="text-sm sm:text-base font-bold text-slate-900">
                                            {q.question}
                                        </h4>
                                    </div>

                                    <div className="space-y-2.5 sm:ml-10">
                                        {q.options.map((opt, optIdx) => {
                                            const isSelected = userAnswer === optIdx;
                                            let optionClass = 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700';

                                            if (quizSubmitted) {
                                                if (optIdx === q.correctIndex) {
                                                    optionClass = 'bg-emerald-50 border-emerald-400 text-emerald-800 font-bold';
                                                } else if (isSelected && optIdx !== q.correctIndex) {
                                                    optionClass = 'bg-rose-50 border-rose-300 text-rose-800 line-through';
                                                }
                                            } else if (isSelected) {
                                                optionClass = 'bg-primary-50 border-primary-500 text-primary-800 font-bold ring-2 ring-primary-500/20';
                                            }

                                            return (
                                                <button
                                                    key={optIdx}
                                                    type="button"
                                                    disabled={quizSubmitted}
                                                    onClick={() => handleQuizOptionClick(q.id, optIdx)}
                                                    className={`w-full text-left px-4 py-3 rounded-xl border text-xs sm:text-sm transition flex items-center justify-between ${optionClass}`}
                                                >
                                                    <span>{opt}</span>
                                                    {quizSubmitted && optIdx === q.correctIndex && (
                                                        <span className="text-emerald-600 font-bold">✓ Benar</span>
                                                    )}
                                                </button>
                                            );
                                        })}
                                    </div>

                                    {quizSubmitted && (
                                        <div className="mt-3 sm:ml-10 text-xs text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                            💡 <strong>Penjelasan:</strong> {q.explanation}
                                        </div>
                                    )}
                                </div>
                            );
                        })}

                        {/* Quiz Submit / Reset Button */}
                        <div className="text-center pt-4">
                            {!quizSubmitted ? (
                                <button
                                    type="button"
                                    disabled={Object.keys(quizAnswers).length < QUIZ_QUESTIONS.length}
                                    onClick={() => setQuizSubmitted(true)}
                                    className="px-8 py-3.5 rounded-full bg-primary-600 hover:bg-primary-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-black text-sm shadow-lg shadow-primary-600/30 transition active:scale-95"
                                >
                                    Cek Hasil Jawaban
                                </button>
                            ) : (
                                <div className="space-y-4">
                                    <div className="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm max-w-md mx-auto">
                                        <div className="text-2xl font-black text-primary-700">
                                            Skor Anda: {calculateQuizScore()} / {QUIZ_QUESTIONS.length}
                                        </div>
                                        <p className="text-xs text-slate-600 mt-1">
                                            {calculateQuizScore() === QUIZ_QUESTIONS.length
                                                ? 'Luar biasa! Anda memahami hak anak dan program KLA Konawe dengan sangat baik! 🎉'
                                                : 'Bagus! Anda telah mempelajari poin penting hak anak di Kabupaten Konawe.'}
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setQuizAnswers({});
                                            setQuizSubmitted(false);
                                        }}
                                        className="px-6 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
                                    >
                                        Ulangi Kuis
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* FAQ SECTION */}
                <div className="mt-20">
                    <div className="text-center max-w-2xl mx-auto mb-10">
                        <span className="text-xs font-bold uppercase tracking-wider text-primary-600">
                            Pusat Bantuan
                        </span>
                        <h3 className="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                            Pertanyaan Umum Seputar KLA (FAQ)
                        </h3>
                    </div>

                    <div className="max-w-3xl mx-auto space-y-3">
                        {FAQS.map((faq, idx) => {
                            const isOpen = openFaqIndex === idx;
                            return (
                                <div
                                    key={idx}
                                    className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition"
                                >
                                    <button
                                        type="button"
                                        onClick={() => setOpenFaqIndex(isOpen ? null : idx)}
                                        className="w-full text-left px-6 py-4 flex items-center justify-between gap-4 font-bold text-slate-900 hover:text-primary-600 transition"
                                    >
                                        <span className="text-sm sm:text-base">{faq.q}</span>
                                        <svg
                                            className={`w-5 h-5 flex-shrink-0 text-slate-400 transition-transform duration-300 ${isOpen ? 'rotate-180 text-primary-600' : ''}`}
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    {isOpen && (
                                        <div className="px-6 pb-5 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                                            {faq.a}
                                        </div>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                </div>

                {/* CUSTOM FILAMENT CONTENT FALLBACK (IF ADMIN CUSTOMIZED VIA DB) */}
                {page?.content && page.content.length > 50 && (
                    <div className="mt-16 bg-white rounded-3xl p-8 border border-slate-100 shadow-md">
                        <h4 className="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span className="w-2.5 h-2.5 rounded-full bg-primary-600" />
                            Catatan Resmi &amp; Pengumuman Pemerintah Kabupaten Konawe
                        </h4>
                        <div
                            className="prose prose-slate prose-sm max-w-none"
                            dangerouslySetInnerHTML={{ __html: page.content }}
                        />
                    </div>
                )}

                {/* FINAL CALL TO ACTION BANNER */}
                <div className="mt-20 bg-gradient-to-r from-primary-600 via-primary-700 to-indigo-800 rounded-3xl p-8 sm:p-12 text-white shadow-xl shadow-primary-700/20 text-center relative overflow-hidden">
                    <div className="max-w-2xl mx-auto relative z-10">
                        <h3 className="text-2xl sm:text-3xl font-black mb-4">
                            Mari Bersama Wujudkan Konawe Layak Anak
                        </h3>
                        <p className="text-primary-100 text-sm sm:text-base leading-relaxed mb-8">
                            Anak adalah masa depan daerah. Setiap suara, kepedulian, dan aksi nyata kita bersama membentuk lingkungan yang aman dan bermartabat bagi generasi penerus Konawe.
                        </p>

                        <div className="flex flex-wrap items-center justify-center gap-3">
                            <Link
                                href="/layanan"
                                className="px-6 py-3.5 rounded-full bg-white text-primary-800 font-bold text-sm shadow-md hover:bg-primary-50 transition active:scale-95"
                            >
                                Akses Layanan KLA
                            </Link>
                            <Link
                                href="/fasilitas"
                                className="px-6 py-3.5 rounded-full bg-primary-500 hover:bg-primary-400 text-white font-bold text-sm shadow-md transition active:scale-95"
                            >
                                Cari Fasilitas Terdekat
                            </Link>
                            <Link
                                href="/pengaduan"
                                className="px-6 py-3.5 rounded-full bg-slate-900/60 hover:bg-slate-900 border border-white/20 text-white font-bold text-sm shadow-md transition active:scale-95"
                            >
                                Kirim Pengaduan / Aspirasi
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

