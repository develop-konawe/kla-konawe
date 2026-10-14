<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai seeding data testing Portal KLA Konawe...');

        // Nonaktifkan foreign key checks untuk keamanan truncate/insert
        Schema::disableForeignKeyConstraints();

        // ---------------------------------------------------------------------
        // 1. Organisasi / Instansi Terkait KLA Konawe (API Orpeg / Fallback)
        // ---------------------------------------------------------------------
        $this->command->info('1/10 Seeding Organisasi Terkait KLA (API Orpeg / Fallback)...');
        
        $orpegService = app(\App\Services\OrpegService::class);
        $syncResult = $orpegService->syncUnitsToDatabase();

        if ($syncResult['success'] && $syncResult['total'] > 0) {
            $this->command->info("   -> Sukses sinkronisasi {$syncResult['total']} unit dari API Orpeg!");
        } else {
            $this->command->warn("   -> API Orpeg tidak dapat dijangkau, menggunakan fallback OPD default.");
            $defaultOrganizations = [
                ['name' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak (DP3A) Kab. Konawe', 'type' => 'dinas', 'code' => 'D0024'],
                ['name' => 'Dinas Kesehatan Kab. Konawe', 'type' => 'dinas', 'code' => 'D0004'],
                ['name' => 'Dinas Pendidikan dan Kebudayaan Kab. Konawe', 'type' => 'dinas', 'code' => 'D0005'],
                ['name' => 'Dinas Kependudukan dan Pencatatan Sipil Kab. Konawe', 'type' => 'dinas', 'code' => 'D0006'],
                ['name' => 'Dinas Sosial Kab. Konawe', 'type' => 'dinas', 'code' => 'D0025'],
                ['name' => 'Dinas Komunikasi dan Informatika Kab. Konawe', 'type' => 'dinas', 'code' => 'D0018'],
            ];
            foreach ($defaultOrganizations as $org) {
                DB::table('organizations')->updateOrInsert(
                    ['code' => $org['code']],
                    array_merge($org, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        // Pastikan wadah Forum Anak Daerah (FAD) Konawe terdaftar
        DB::table('organizations')->updateOrInsert(
            ['code' => 'ORG-FAD-KNW'],
            [
                'name' => 'Forum Anak Daerah (FAD) Kabupaten Konawe',
                'type' => 'lsm',
                'code' => 'ORG-FAD-KNW',
                'address' => 'Sekretariat Bersama Gedung Pemuda, Unaaha, Konawe',
                'phone' => '081234567890',
                'email' => 'forumanak@konawekab.go.id',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $dp3aId     = DB::table('organizations')->where('code', 'D0024')->value('id') ?? 1;
        $dinkesId   = DB::table('organizations')->where('code', 'D0004')->value('id') ?? $dp3aId;
        $dikbudId   = DB::table('organizations')->where('code', 'D0005')->value('id') ?? $dp3aId;
        $dukcapilId = DB::table('organizations')->where('code', 'D0006')->value('id') ?? $dp3aId;
        $fadId      = DB::table('organizations')->where('code', 'ORG-FAD-KNW')->value('id') ?? $dp3aId;

        // ---------------------------------------------------------------------
        // 2. User Tambahan & Operator Pengujian
        // ---------------------------------------------------------------------
        $this->command->info('2/10 Seeding Pengguna Pengujian...');
        $adminUser = User::first();
        $authorId = $adminUser ? $adminUser->id : 1;

        $testStaff = [
            [
                'name' => 'Siti Rahmawati, S.Sos',
                'username' => 'petugas_dp3a',
                'email' => 'rahma@dp3a.konawe.go.id',
                'organization_id' => $dp3aId,
                'role' => 'complaint_officer',
            ],
            [
                'name' => 'dr. Ahmad Fauzi',
                'username' => 'petugas_faskes',
                'email' => 'fauzi@dinkes.konawe.go.id',
                'organization_id' => $dinkesId,
                'role' => 'facility_manager',
            ],
            [
                'name' => 'Budi Santoso, M.Pd',
                'username' => 'editor_pendidikan',
                'email' => 'budi@dikbud.konawe.go.id',
                'organization_id' => $dikbudId,
                'role' => 'content_editor',
            ],
        ];

        $userIds = [$authorId];
        foreach ($testStaff as $staff) {
            $user = User::updateOrCreate(
                ['email' => $staff['email']],
                [
                    'name' => $staff['name'],
                    'username' => $staff['username'],
                    'password' => Hash::make('password'),
                    'organization_id' => $staff['organization_id'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            if (isset($staff['role'])) {
                $user->syncRoles([$staff['role']]);
            }
            $userIds[] = $user->id;
        }

        // ---------------------------------------------------------------------
        // 3. Kategori Klaster KLA Resmi
        // ---------------------------------------------------------------------
        $this->command->info('3/10 Seeding Kategori 5 Klaster KLA...');
        DB::table('categories')->truncate();
        
        $categories = [
            [
                'name' => 'Klaster 1: Hak Sipil & Kebebasan',
                'slug' => 'klaster-1-hak-sipil-kebebasan',
                'description' => 'Akta kelahiran, informasi ramah anak, dan partisipasi anak dalam perencanaan daerah.',
            ],
            [
                'name' => 'Klaster 2: Lingkungan Keluarga & Pengasuhan Alternatif',
                'slug' => 'klaster-2-lingkungan-keluarga-pengasuhan-alternatif',
                'description' => 'Pencegahan perkawinan anak, lembaga konsultasi keluarga (PUSPAGA), dan pengasuhan ramah anak.',
            ],
            [
                'name' => 'Klaster 3: Kesehatan Dasar & Kesejahteraan',
                'slug' => 'klaster-3-kesehatan-dasar-kesejahteraan',
                'description' => 'Puskesmas ramah anak, sanitasi air bersih, imunisasi, dan penanganan stunting balita.',
            ],
            [
                'name' => 'Klaster 4: Pendidikan, Pemanfaatan Waktu Luang & Kegiatan Budaya',
                'slug' => 'klaster-4-pendidikan-waktu-luang-budaya',
                'description' => 'Sekolah Ramah Anak (SRA), pusat kreativitas anak, perpustakaan, dan ruang bermain ramah anak.',
            ],
            [
                'name' => 'Klaster 5: Perlindungan Khusus',
                'slug' => 'klaster-5-perlindungan-khusus',
                'description' => 'Penanganan kekerasan anak, anak berhadapan dengan hukum, dan anak dalam situasi darurat.',
            ],
            [
                'name' => 'Berita & Kegiatan KLA',
                'slug' => 'berita-kegiatan-kla',
                'description' => 'Informasi umum seputar agenda, sosialisasi, dan liputan KLA Kabupaten Konawe.',
            ],
        ];

        $catIds = [];
        foreach ($categories as $cat) {
            $catIds[] = DB::table('categories')->insertGetId(array_merge($cat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ---------------------------------------------------------------------
        // 4. Halaman Statis Resmi (Profil & Kebijakan)
        // ---------------------------------------------------------------------
        $this->command->info('4/10 Seeding Halaman Statis...');
        DB::table('pages')->truncate();

        $pages = [
            [
                'title' => 'Tentang Kabupaten Layak Anak (KLA) Konawe',
                'slug' => 'tentang-kla',
                'excerpt' => 'Mengenal komitmen dan program Pemerintah Kabupaten Konawe dalam mewujudkan Kabupaten Layak Anak.',
                'content' => '<p>Kabupaten/Kota Layak Anak (KLA) di Kabupaten Konawe adalah sistem pembangunan daerah yang mengintegrasikan komitmen pemerintah, masyarakat, media, dan dunia usaha untuk pemenuhan hak-hak anak dan perlindungan khusus anak secara terencana, menyeluruh, dan berkelanjutan.</p><p>Visi utama: <strong>Mewujudkan Kabupaten Konawe yang Ramah Anak, Sehat, Cerdas, dan Terlindungi.</strong></p>',
                'status' => 'published',
                'published_at' => now(),
                'author_id' => $authorId,
                'reviewer_id' => $authorId,
            ],
            [
                'title' => 'Dasar Hukum & Regulasi KLA Konawe',
                'slug' => 'regulasi-kla',
                'excerpt' => 'Kumpulan Peraturan Daerah dan Surat Keputusan Bupati Konawe terkait Penyelenggaraan KLA.',
                'content' => '<ul><li>Peraturan Daerah Kabupaten Konawe tentang Penyelenggaraan Kabupaten Layak Anak.</li><li>Peraturan Bupati Konawe tentang Pencegahan Perkawinan Usia Anak.</li><li>SK Gugus Tugas KLA Kabupaten Konawe.</li></ul>',
                'status' => 'published',
                'published_at' => now(),
                'author_id' => $authorId,
                'reviewer_id' => $authorId,
            ],
            [
                'title' => 'Profil Forum Anak Daerah (FAD) Konawe',
                'slug' => 'forum-anak-konawe',
                'excerpt' => 'Wadah partisipasi anak sebagai Pelopor dan Pelapor (2P) di Kabupaten Konawe.',
                'content' => '<p>Forum Anak Daerah (FAD) Kabupaten Konawe dibentuk sebagai media partisipasi bagi anak-anak di Konawe untuk menyuarakan aspirasi, kebutuhan, dan pandangan mereka kepada pemangku kebijakan daerah melalui peran Pelopor dan Pelapor (2P).</p>',
                'status' => 'published',
                'published_at' => now(),
                'author_id' => $authorId,
                'reviewer_id' => $authorId,
            ],
        ];

        foreach ($pages as $page) {
            DB::table('pages')->insert(array_merge($page, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ---------------------------------------------------------------------
        // 5. Berita & Artikel KLA (Local Post Fallback)
        // ---------------------------------------------------------------------
        $this->command->info('5/10 Seeding Berita & Artikel KLA...');
        DB::table('posts')->truncate();

        $posts = [
            [
                'category_id' => $catIds[0],
                'title' => 'Disdukcapil Konawe Luncurkan Program Jemput Bola Pembuatan KIA dan Akta Anak',
                'slug' => 'disdukcapil-konawe-luncurkan-program-jemput-bola-kia-akta-anak',
                'excerpt' => 'Upaya pemenuhan Hak Sipil Anak (Klaster 1) dengan mendatangi sekolah dasar dan posyandu di wilayah pedesaan Konawe.',
                'content' => '<p>Dinas Kependudukan dan Pencatatan Sipil (Disdukcapil) Kabupaten Konawe mempercepat penerbitan Kartu Identitas Anak (KIA) dan akta kelahiran gratis melalui program jemput bola ke 28 kecamatan di seluruh wilayah Konawe.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&auto=format&fit=crop&q=80',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'author_id' => $authorId,
            ],
            [
                'category_id' => $catIds[1],
                'title' => 'DP3A Konawe Perkuat Peran PUSPAGA dalam Mencegah Perkawinan Usia Anak',
                'slug' => 'dp3a-konawe-perkuat-peran-puspaga-cegah-perkawinan-anak',
                'excerpt' => 'Konseling pra-nikah dan pengasuhan keluarga positif digencarkan untuk menekan angka dispensasi nikah dini.',
                'content' => '<p>Pusat Pembelajaran Keluarga (PUSPAGA) Konawe terus aktif memberikan edukasi dan pendampingan psikologis kepada para orang tua dan remaja guna membangun ketahanan keluarga yang ramah anak.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1491438590914-bc09fcaaf77a?w=800&auto=format&fit=crop&q=80',
                'status' => 'published',
                'published_at' => now()->subDays(4),
                'author_id' => $authorId,
            ],
            [
                'category_id' => $catIds[2],
                'title' => 'Puskesmas Ramah Anak Unaaha Raih Standarisasi Pelayanan Kesehatan Terpadu',
                'slug' => 'puskesmas-ramah-anak-unaaha-raih-standarisasi-kesehatan-terpadu',
                'excerpt' => 'Fasilitas ruang bermain, poli anak terpisah, dan tenaga kesehatan terlatih Konvensi Hak Anak.',
                'content' => '<p>Puskesmas Unaaha berhasil melengkapi seluruh indikator Pelayanan Ramah Anak di Puskesmas (PRAP), meliputi ruang tunggu khusus anak, ruang laktasi bersih, dan bebas asap rokok 100%.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?w=800&auto=format&fit=crop&q=80',
                'status' => 'published',
                'published_at' => now()->subDays(6),
                'author_id' => $authorId,
            ],
            [
                'category_id' => $catIds[3],
                'title' => 'Deklarasi 50 Sekolah Ramah Anak (SRA) Jenjang SD dan SMP se-Kabupaten Konawe',
                'slug' => 'deklarasi-50-sekolah-ramah-anak-sd-smp-kabupaten-konawe',
                'excerpt' => 'Komitmen mewujudkan lingkungan belajar yang aman, nyaman, dan bebas dari perundungan (anti-bullying).',
                'content' => '<p>Dinas Pendidikan dan Kebudayaan Konawe bersama kepala sekolah menandatangani komitmen bersama penerapan Sekolah Ramah Anak tanpa kekerasan fisik maupun verbal di lingkungan sekolah.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80',
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'author_id' => $authorId,
            ],
            [
                'category_id' => $catIds[4],
                'title' => 'UPTD PPA Konawe Siapkan Hotline Pengaduan 24 Jam untuk Korban Kekerasan Anak',
                'slug' => 'uptd-ppa-konawe-siapkan-hotline-pengaduan-24-jam-korban-kekerasan-anak',
                'excerpt' => 'Layanan pendampingan hukum, psikologis, dan medis gratis bagi anak korban tindak kekerasan.',
                'content' => '<p>Pemerintah Daerah Konawe memastikan perlindungan cepat tanggap terhadap anak melalui sistem pelaporan daring dan pendampingan lapangan terintegrasi bersama aparat penegak hukum.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&auto=format&fit=crop&q=80',
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'author_id' => $authorId,
            ],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->insert(array_merge($post, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ---------------------------------------------------------------------
        // 6. Layanan Publik Ramah Anak (Services)
        // ---------------------------------------------------------------------
        $this->command->info('6/10 Seeding Layanan Ramah Anak...');
        DB::table('services')->truncate();

        $services = [
            [
                'organization_id' => $dp3aId,
                'name' => 'Layanan Pengaduan & Pendampingan UPTD PPA',
                'slug' => 'layanan-pengaduan-pendampingan-uptd-ppa',
                'description' => 'Layanan respons cepat, penjangkauan, pendampingan hukum, dan konseling psikologis gratis bagi anak dan perempuan korban kekerasan.',
                'type' => 'terpadu',
                'contact_info' => '0811-400-8989 (Hotline 24 Jam)',
                'working_hours' => '24 Jam (Siaga Pengaduan)',
                'status' => 'published',
                'image' => 'https://images.unsplash.com/photo-1573497620053-ea5300f94f21?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'organization_id' => $dp3aId,
                'name' => 'PUSPAGA (Pusat Pembelajaran Keluarga) Kencana Konawe',
                'slug' => 'puspaga-kencana-konawe',
                'description' => 'Konsultasi pengasuhan anak (parenting), konseling pranikah, dan bimbingan psikologi anak oleh psikolog profesional.',
                'type' => 'offline',
                'contact_info' => '(0408) 2421001 / WA: 0812-9876-5432',
                'working_hours' => 'Senin - Jumat: 08.00 - 16.00 WITA',
                'status' => 'published',
                'image' => 'https://images.unsplash.com/photo-1536640712-4d4c36ff0e4e?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'organization_id' => $dukcapilId,
                'name' => 'Layanan Penerbitan KIA (Kartu Identitas Anak) Online & Sekolah',
                'slug' => 'penerbitan-kia-online-dan-sekolah',
                'description' => 'Penerbitan identitas kependudukan resmi anak usia 0-17 tahun kurang satu hari secara cepat dan terintegrasi.',
                'type' => 'online',
                'contact_info' => 'disdukcapil@konawekab.go.id',
                'working_hours' => 'Senin - Jumat: 08.00 - 15.00 WITA',
                'status' => 'published',
                'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'organization_id' => $dinkesId,
                'name' => 'Pelayanan Kesehatan Peduli Remaja (PKPR)',
                'slug' => 'pelayanan-kesehatan-peduli-remaja-pkpr',
                'description' => 'Konseling kesehatan reproduksi, gizi, pencegahan anemia remaja putri, dan kesehatan mental remaja di puskesmas se-Konawe.',
                'type' => 'offline',
                'contact_info' => 'Puskesmas Unaaha & Puskesmas Wawotobi',
                'working_hours' => 'Senin - Sabtu: 08.00 - 13.00 WITA',
                'status' => 'published',
                'image' => 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?w=800&auto=format&fit=crop&q=80',
            ],
        ];

        foreach ($services as $srv) {
            DB::table('services')->insert(array_merge($srv, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ---------------------------------------------------------------------
        // 7. Fasilitas Publik Ramah Anak (Facilities)
        // ---------------------------------------------------------------------
        $this->command->info('7/10 Seeding Fasilitas Ramah Anak...');
        DB::table('facilities')->truncate();

        $facilities = [
            [
                'organization_id' => $dp3aId,
                'name' => 'Ruang Bermain Ramah Anak (RBRA) Kantor Bupati Konawe',
                'slug' => 'rbra-kantor-bupati-konawe',
                'description' => 'Fasilitas taman bermain luar ruang bersertifikasi nasional dengan lantai peredam benturan dan mainan edukatif.',
                'category' => 'taman',
                'address' => 'Kompleks Kantor Bupati Konawe, Unaaha',
                'coordinates' => '-3.8765,122.0521',
                'status' => 'published',
                'is_child_friendly' => true,
                'verified_at' => now(),
                'verified_by' => $authorId,
                'image' => 'https://images.unsplash.com/photo-1545558014-8692077e9b5c?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'organization_id' => $dinkesId,
                'name' => 'Puskesmas Ramah Anak Unaaha',
                'slug' => 'puskesmas-ramah-anak-unaaha',
                'description' => 'Fasilitas pelayanan kesehatan dengan ruang tunggu bermain, pojok ASI/laktasi ber-AC, dan toilet anak khusus.',
                'category' => 'kesehatan',
                'address' => 'Jl. Kesehatan No. 5, Unaaha, Konawe',
                'coordinates' => '-3.8690,122.0450',
                'status' => 'published',
                'is_child_friendly' => true,
                'verified_at' => now(),
                'verified_by' => $authorId,
                'image' => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'organization_id' => $dikbudId,
                'name' => 'Pojok Baca & Perpustakaan Ramah Anak Dispusipda Konawe',
                'slug' => 'pojok-baca-ramah-anak-dispusipda',
                'description' => 'Koleksi buku dongeng, ensiklopedia anak, ruang multimedia, dan sesi mendongeng mingguan.',
                'category' => 'pendidikan',
                'address' => 'Gedung Perpustakaan Daerah Konawe, Jl. Inolobunggadue',
                'coordinates' => '-3.8720,122.0490',
                'status' => 'published',
                'is_child_friendly' => true,
                'verified_at' => now(),
                'verified_by' => $authorId,
                'image' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'organization_id' => $dp3aId,
                'name' => 'Taman Lalu Lintas Ramah Anak Unaaha',
                'slug' => 'taman-lalu-lintas-ramah-anak-unaaha',
                'description' => 'Wahana edukasi rambu lalu lintas, keselamatan berkendara anak, dan ruang hijau rekreasi keluarga.',
                'category' => 'publik',
                'address' => 'Kawasan Lapangan Inolobunggadue, Unaaha',
                'coordinates' => '-3.8745,122.0510',
                'status' => 'published',
                'is_child_friendly' => true,
                'verified_at' => now(),
                'verified_by' => $authorId,
                'image' => 'https://images.unsplash.com/photo-1500964757637-c85e8a162699?w=800&auto=format&fit=crop&q=80',
            ],
        ];

        foreach ($facilities as $fac) {
            DB::table('facilities')->insert(array_merge($fac, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ---------------------------------------------------------------------
        // 8. Aspirasi Anak Konawe (Aspirations)
        // ---------------------------------------------------------------------
        $this->command->info('8/10 Seeding Aspirasi Anak...');
        DB::table('aspirations')->truncate();

        $aspirations = [
            [
                'title' => 'Tolong fasilitasi wifi gratis dan pojok belajar di perpustakaan desa',
                'content' => 'Banyak teman-teman yang kesulitan akses internet untuk tugas sekolah dan belajar coding dasar. Kami berharap ada wifi gratis di kantor desa atau balai pertemuan.',
                'author_name' => 'Farel Aditya',
                'author_age' => 15,
                'author_region' => 'Kecamatan Wawotobi',
                'status' => 'published',
                'category' => 'education',
                'reviewed_by' => $authorId,
                'reviewed_at' => now()->subDays(1),
            ],
            [
                'title' => 'Perbanyak zona selamat sekolah (ZOSS) di depan SD poros jalan raya',
                'content' => 'Saat jam masuk dan pulang sekolah, kendaraan melaju sangat kencang di depan sekolah kami. Mohon dibuatkan marka zebra cross dan lampu kedip.',
                'author_name' => 'Nabila Putri',
                'author_age' => 13,
                'author_region' => 'Kecamatan Unaaha',
                'status' => 'published',
                'category' => 'safety',
                'reviewed_by' => $authorId,
                'reviewed_at' => now()->subDays(2),
            ],
            [
                'title' => 'Festival Seni Tradisional dan Panggung Ekspresi Anak Konawe',
                'content' => 'Kami ingin diadakan lomba tari tradisional Tolaki dan teater anak tahunan untuk melestarikan budaya lokal bagi generasi muda.',
                'author_name' => 'Andi Muhammad',
                'author_age' => 16,
                'author_region' => 'Kecamatan Sampara',
                'status' => 'published',
                'category' => 'culture',
                'reviewed_by' => $authorId,
                'reviewed_at' => now()->subDays(3),
            ],
        ];

        foreach ($aspirations as $asp) {
            DB::table('aspirations')->insert(array_merge($asp, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ---------------------------------------------------------------------
        // 9. Data Pengaduan Testing (Complaints & Cek Status)
        // ---------------------------------------------------------------------
        $this->command->info('9/10 Seeding Pengaduan Testing & Pelacakan Tiket...');
        DB::table('complaints')->truncate();
        DB::table('complaint_follow_ups')->truncate();

        $complaints = [
            [
                'ticket_number' => 'TKN-KLA-001',
                'category_id' => $catIds[4],
                'reporter_name' => 'Ibu Marlina (Orang Tua Siswa)',
                'reporter_contact' => '081234567801',
                'is_anonymous' => false,
                'victim_relation' => 'parent',
                'chronology' => 'Anak saya mengalami intimidasi dan pemalakan berulang di perjalanan pulang sekolah oleh sekelompok remaja.',
                'general_location' => 'Dekat Lapangan Unaaha, Kec. Unaaha',
                'urgency' => 'high',
                'consent_at' => now(),
                'status' => 'in_progress',
                'assigned_to' => $userIds[1],
            ],
            [
                'ticket_number' => 'TKN-KLA-002',
                'category_id' => $catIds[1],
                'reporter_name' => 'Masyarakat Peduli Anak',
                'reporter_contact' => '081234567802',
                'is_anonymous' => true,
                'victim_relation' => 'other',
                'chronology' => 'Informasi adanya rencana pernikahan anak di bawah umur (15 tahun) di salah satu desa binaan.',
                'general_location' => 'Kecamatan Abuki',
                'urgency' => 'medium',
                'consent_at' => now(),
                'status' => 'resolved',
                'assigned_to' => $userIds[1],
            ],
            [
                'ticket_number' => 'TKN-KLA-003',
                'category_id' => $catIds[3],
                'reporter_name' => 'Warga Lingkungan II',
                'reporter_contact' => '081234567803',
                'is_anonymous' => false,
                'victim_relation' => 'other',
                'chronology' => 'Peralatan ayunan di taman bermain anak rusak parah dan berkarat tajam, membahayakan balita yang bermain.',
                'general_location' => 'Taman Kelurahan Tuoy, Unaaha',
                'urgency' => 'low',
                'consent_at' => now(),
                'status' => 'new',
                'assigned_to' => null,
            ],
        ];

        foreach ($complaints as $comp) {
            $complaintId = DB::table('complaints')->insertGetId(array_merge($comp, [
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ]));

            if ($comp['status'] === 'in_progress') {
                DB::table('complaint_follow_ups')->insert([
                    'complaint_id' => $complaintId,
                    'user_id' => $userIds[1],
                    'action_type' => 'investigation',
                    'note' => 'Tim UPTD PPA telah berkoordinasi dengan pihak sekolah dan Bhabinkamtibmas untuk patroli saat jam kepulangan.',
                    'next_action' => 'Pendampingan psikologis korban dan mediasi dengan orang tua para pelaku.',
                    'created_at' => now()->subDays(1),
                    'updated_at' => now()->subDays(1),
                ]);
            } elseif ($comp['status'] === 'resolved') {
                DB::table('complaint_follow_ups')->insert([
                    'complaint_id' => $complaintId,
                    'user_id' => $userIds[1],
                    'action_type' => 'mediation',
                    'note' => 'PUSPAGA dan KUA setempat telah melakukan konseling keluarga. Orang tua sepakat anak melanjutkan sekolah hingga lulus SMA.',
                    'next_action' => 'Kasus ditutup dengan pemantauan berkala setiap bulan.',
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ]);
            }
        }

        // ---------------------------------------------------------------------
        // 10. Data Statistik Resmi KLA (Statistics)
        // ---------------------------------------------------------------------
        $this->command->info('10/10 Seeding Data Statistik KLA...');
        DB::table('statistics')->truncate();

        $statistics = [
            [
                'category_id' => (string) $catIds[0],
                'title' => 'Cakupan Kepemilikan Akta Kelahiran Anak (0-17 Tahun)',
                'year' => 2025,
                'region_id' => 'Konawe',
                'value' => 94.80,
                'unit' => '%',
                'source' => 'Disdukcapil Kabupaten Konawe',
                'status' => 'published',
                'metadata_json' => json_encode(['target_nasional' => 95, 'klaster' => 1]),
            ],
            [
                'category_id' => (string) $catIds[1],
                'title' => 'Penurunan Angka Dispensasi Kawin Anak Usia Dini',
                'year' => 2025,
                'region_id' => 'Konawe',
                'value' => 38.50,
                'unit' => '% penurunan',
                'source' => 'Pengadilan Agama Unaaha & DP3A',
                'status' => 'published',
                'metadata_json' => json_encode(['target_nasional' => 30, 'klaster' => 2]),
            ],
            [
                'category_id' => (string) $catIds[2],
                'title' => 'Jumlah Puskesmas Ramah Anak (PRAP) Terstandarisasi',
                'year' => 2025,
                'region_id' => 'Konawe',
                'value' => 24.00,
                'unit' => 'Puskesmas',
                'source' => 'Dinas Kesehatan Kabupaten Konawe',
                'status' => 'published',
                'metadata_json' => json_encode(['total_puskesmas' => 28, 'klaster' => 3]),
            ],
            [
                'category_id' => (string) $catIds[3],
                'title' => 'Jumlah Sekolah Ramah Anak (SRA) Jenjang SD & SMP',
                'year' => 2025,
                'region_id' => 'Konawe',
                'value' => 65.00,
                'unit' => 'Sekolah',
                'source' => 'Dinas Pendidikan dan Kebudayaan Kab. Konawe',
                'status' => 'published',
                'metadata_json' => json_encode(['klaster' => 4]),
            ],
            [
                'category_id' => (string) $catIds[4],
                'title' => 'Persentase Penanganan Kasus Kekerasan Anak Hingga Tuntas',
                'year' => 2025,
                'region_id' => 'Konawe',
                'value' => 100.00,
                'unit' => '%',
                'source' => 'UPTD PPA Kab. Konawe',
                'status' => 'published',
                'metadata_json' => json_encode(['total_kasus' => 16, 'selesai' => 16, 'klaster' => 5]),
            ],
        ];

        foreach ($statistics as $stat) {
            DB::table('statistics')->insert(array_merge($stat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Aktifkan kembali foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->command->info('✅ Seluruh data testing Portal KLA Konawe berhasil di-seed!');
    }
}
