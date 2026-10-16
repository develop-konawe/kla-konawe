<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span class="p-2 rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                    <x-filament::icon icon="heroicon-o-globe-alt" class="w-5 h-5" />
                </span>
                <div>
                    <h2 class="text-base font-bold text-gray-950 dark:text-white">Peta Integrasi Data Admin & Website Publik KLA</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Panduan hubungan antara input data di panel admin ini dengan halaman yang dilihat oleh pengunjung dan anak-anak.</p>
                </div>
            </div>
        </x-slot>

        <!-- Quick Links to Public Website -->
        <div class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-sky-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900 border border-emerald-100 dark:border-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-bold text-emerald-950 dark:text-emerald-300">Akses Langsung Halaman Publik Pengunjung</div>
                    <div class="text-xs text-emerald-800/80 dark:text-emerald-400/80">Klik tombol di bawah ini untuk melihat langsung tampilan website yang diakses masyarakat:</div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition shadow-xs">
                        <span>Beranda Utama</span>
                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="w-3.5 h-3.5 text-gray-400" />
                    </a>
                    <a href="{{ url('/tentang-kla') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition shadow-xs">
                        <span>Tentang KLA</span>
                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="w-3.5 h-3.5 text-gray-400" />
                    </a>
                    <a href="{{ url('/layanan') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition shadow-xs">
                        <span>Layanan Anak</span>
                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="w-3.5 h-3.5 text-gray-400" />
                    </a>
                    <a href="{{ url('/fasilitas') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition shadow-xs">
                        <span>Fasilitas Ramah Anak</span>
                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="w-3.5 h-3.5 text-gray-400" />
                    </a>
                    <a href="{{ url('/berita') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition shadow-xs">
                        <span>Berita & Edukasi</span>
                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="w-3.5 h-3.5 text-gray-400" />
                    </a>
                    <a href="{{ url('/publikasi') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition shadow-xs">
                        <span>Regulasi & Laporan</span>
                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="w-3.5 h-3.5 text-gray-400" />
                    </a>
                </div>
            </div>
        </div>

        <!-- Purpose & Mapping Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 text-gray-700 dark:text-gray-300 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3 px-4">Menu Panel Admin</th>
                        <th class="py-3 px-4">Tujuan Utama KLA</th>
                        <th class="py-3 px-4">Tampilan di Website Pengunjung</th>
                        <th class="py-3 px-4">Aksi Pengelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/60 transition">
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Layanan Ramah Anak
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                            Memberikan informasi langsung kepada keluarga & anak tentang layanan pemenuhan hak anak (adminduk KIA, faskes anak, konseling, bantuan hukum UPTD PPA).
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 font-mono text-[11px]">/layanan</span>
                            <div class="text-[11px] text-gray-500 mt-0.5">Direktori pencarian, tombol kontak WA langsung, dan info OPD pengampu.</div>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('filament.admin.resources.services.index') }}" class="font-bold text-primary-600 hover:underline">Kelola Layanan &rarr;</a>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/60 transition">
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                            Fasilitas Ramah Anak
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                            Memetakan ruang publik, sekolah, puskesmas, dan taman bermain di Konawe yang aman dan terstandarisasi ramah anak.
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-400 font-mono text-[11px]">/fasilitas</span>
                            <div class="text-[11px] text-gray-500 mt-0.5">Daftar kartu fasilitas, badge "Ramah Anak", dan tautan navigasi Google Maps.</div>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('filament.admin.resources.facilities.index') }}" class="font-bold text-primary-600 hover:underline">Kelola Fasilitas &rarr;</a>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/60 transition">
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Berita & Artikel KLA
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                            Publikasi liputan kegiatan KLA Konawe, sosialisasi hak anak, dan kegiatan pelopor Forum Anak.
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-400 font-mono text-[11px]">/berita</span>
                            <div class="text-[11px] text-gray-500 mt-0.5">Artikel edukasi, estimasi waktu baca, tombol bagikan WA, dan berita terkait.</div>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('filament.admin.resources.posts.index') }}" class="font-bold text-primary-600 hover:underline">Kelola Berita &rarr;</a>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/60 transition">
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            Publikasi & Regulasi
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                            Transparansi dasar hukum KLA (Perda, SK Bupati), Rencana Aksi Daerah (RAD), panduan, dan laporan tahunan.
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 dark:bg-orange-950 dark:text-orange-400 font-mono text-[11px]">/publikasi</span>
                            <div class="text-[11px] text-gray-500 mt-0.5">Daftar dokumen resmi dengan tombol unduh berkas PDF bagi publik.</div>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('filament.admin.resources.publications.index') }}" class="font-bold text-primary-600 hover:underline">Kelola Dokumen &rarr;</a>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/60 transition">
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            Data &amp; Indikator KLA
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                            Mempublikasikan capaian realisasi 24 indikator evaluasi KLA per klaster (Cakupan Akta Lahir, KIA, Puskesmas Ramah Anak, Sekolah Ramah Anak, Penanganan Kasus) yang diampu oleh OPD terkait.
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-cyan-50 text-cyan-700 dark:bg-cyan-950 dark:text-cyan-400 font-mono text-[11px]">Beranda (#indikator-kla)</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 dark:bg-orange-950 dark:text-orange-400 font-mono text-[11px]">/publikasi (Tab Data)</span>
                            </div>
                            <div class="text-[11px] text-gray-500 mt-1">Kartu capaian interaktif, angka persentase/unit, tahun evaluasi, dan OPD pengampu.</div>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('filament.admin.resources.statistics.index') }}" class="font-bold text-primary-600 hover:underline">Kelola Indikator &rarr;</a>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/60 transition">
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            Halaman & Profil KLA
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                            Menyajikan profil kelembagaan, visi-misi, 5 klaster KLA, 24 indikator evaluasi, dan struktur Gugus Tugas KLA.
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-400 font-mono text-[11px]">/tentang-kla</span>
                            <div class="text-[11px] text-gray-500 mt-0.5">Slug <code>tentang-kla</code> mengisi profil; slug lain tampil di <code>/halaman/{slug}</code>.</div>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('filament.admin.resources.pages.index') }}" class="font-bold text-primary-600 hover:underline">Kelola Halaman &rarr;</a>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/60 transition">
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Aspirasi Suara Anak
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                            Wadah partisipasi murni anak & remaja untuk menyampaikan aspirasi, saran, dan ide bagi pembangunan Konawe.
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-400 font-mono text-[11px]">Partisipasi Anak</span>
                            <div class="text-[11px] text-gray-500 mt-0.5">Ditinjau oleh petugas sebelum dipublikasikan atau diteruskan ke Gugus Tugas.</div>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('filament.admin.resources.aspirations.index') }}" class="font-bold text-primary-600 hover:underline">Moderasi Aspirasi &rarr;</a>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/60 transition">
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                            Gugus Tugas / OPD KLA
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                            Instansi pemerintah penanggung jawab klaster & penyedia layanan anak (DP3A, Dinkes, Disdik, Dukcapil, dll).
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-mono text-[11px]">Badge Instansi</span>
                            <div class="text-[11px] text-gray-500 mt-0.5">Ditampilkan pada kartu layanan, fasilitas, dan kontak penanggung jawab.</div>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('filament.admin.resources.organizations.index') }}" class="font-bold text-primary-600 hover:underline">Kelola OPD &rarr;</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

