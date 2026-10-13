# Panduan Deploy Docker & Podman - Portal KLA Konawe

Panduan ini menjelaskan cara melakukan containerization dan deployment aplikasi **Portal KLA (Kabupaten Layak Anak) Konawe** menggunakan **Docker** atau **Podman**.

---

## 🏗️ Arsitektur Kontainer

Proyek ini menggunakan arsitektur siap produksi (*production-ready*):

1. **`app` (PHP 8.3-FPM + Nginx + Supervisor)**:
   - **Multi-stage build**:
     - *Stage 1*: Node.js 20 Alpine untuk build asset Vite + React + Tailwind ke `public/build`.
     - *Stage 2*: Composer 2 untuk mengunduh dependency PHP (`--no-dev --optimize-autoloader`).
     - *Stage 3*: PHP 8.3-FPM Alpine dengan ekstensi `pdo_mysql`, `gd`, `intl`, `zip`, `opcache`, `bcmath`, `exif`, dll.
   - **Nginx**: Menangani static asset, kompresi gzip, batas upload 64MB (Filament), dan fastcgi pass.
   - **Supervisor**: Menjalankan dan memonitor PHP-FPM, Nginx, dan Laravel Queue Worker secara simultan.
   - **Healthcheck**: Endpoint otomatis `/up` (bawaan Laravel 11/12).

2. **`db` (MySQL 8.0)**:
   - Volume persisten `db_data` untuk penyimpanan data database.
   - Dilengkapi *healthcheck* otomatis agar kontainer `app` menunggu database siap sebelum inisialisasi.

3. **`redis` (Redis 7 Alpine)**:
   - Opsional: Untuk cache, session, dan queue berkecepatan tinggi.

---

## 🚀 Persiapan Sebelum Deploy

### 1. Siapkan File Environment

Salin template environment khusus docker:
```bash
cp .env.docker.example .env
```

Sesuaikan nilai penting pada file `.env`:
- `APP_KEY`: Pastikan telah terisi (atau generate baru).
- `APP_URL`: URL domain atau IP server (misal: `http://kla.konawekab.go.id` atau `http://192.168.1.100:8080`).
- `APP_PORT`: Port host yang ingin digunakan (default: `8080`).
- `DB_PASSWORD` & `DB_ROOT_PASSWORD`: Ubah dengan password yang kuat dan aman.
- `BERITA_BASE_URL` & `BERITA_API_KEY`: API token portal berita Konawe.

---

## 🐳 Opsi A: Deploy Menggunakan Podman (Disarankan di RHEL/CentOS/Rocky/Fedora)

Sistem Anda telah terinstal **Podman** dan **Podman Compose**.

### 1. Build dan Jalankan Kontainer

```bash
# Build image aplikasi
podman-compose build

# Jalankan kontainer di latar belakang (background)
podman-compose up -d
```

### 2. Cek Status Kontainer

```bash
podman-compose ps
```

### 3. Cek Log Kontainer

```bash
podman-compose logs -f app
```

### 4. Menjalankan Database Seeder (Inisialisasi Data Awal)

Jika tabel belum memiliki data default / peran:
```bash
podman-compose exec app php artisan db:seed --force
```

### 5. Membuat User Admin Filament Baru

Untuk membuat akun administrator panel Filament:
```bash
podman-compose exec app php artisan make:filament-user
```

### 6. Menghentikan Kontainer

```bash
podman-compose down
```

---

## 🐳 Opsi B: Deploy Menggunakan Docker

Jika server produksi menggunakan Docker standar:

```bash
# 1. Build dan jalankan
docker compose up -d --build

# 2. Cek log
docker compose logs -f app

# 3. Jalankan seeder
docker compose exec app php artisan db:seed --force

# 4. Hentikan
docker compose down
```

---

## 🛠️ Perintah Praktis Menggunakan Makefile

Tersedia `Makefile` untuk mempermudah operasional harian (otomatis mendeteksi Podman/Docker):

| Perintah | Deskripsi |
| :--- | :--- |
| `make build` | Build ulang image kontainer |
| `make up` | Jalankan kontainer di latar belakang |
| `make down` | Hentikan dan bersihkan kontainer |
| `make restart` | Restart seluruh service |
| `make logs` | Pantau log secara real-time |
| `make ps` | Lihat status kontainer yang berjalan |
| `make bash` | Masuk ke terminal shell kontainer `app` |
| `make migrate` | Jalankan migrasi database |
| `make seed` | Jalankan database seeder |
| `make optimize` | Refresh cache konfigurasi, route, dan view |
| `make clean` | Hentikan kontainer beserta seluruh volume datanya |

---

## 🔒 Konfigurasi Nginx Reverse Proxy (SSL / Domain Publik)

Jika kontainer diekspos melalui Nginx utama server (misalnya untuk HTTPS / SSL Let's Encrypt), tambahkan konfigurasi reverse proxy berikut pada Nginx host:

```nginx
server {
    listen 80;
    server_name kla.konawekab.go.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name kla.konawekab.go.id;

    ssl_certificate /etc/letsencrypt/live/kla.konawekab.go.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/kla.konawekab.go.id/privkey.pem;

    client_max_body_size 64M;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 300s;
        proxy_connect_timeout 75s;
    }
}
```

---

## 💾 Backup & Restore Database

### Backup Database:
```bash
# Menggunakan Podman
podman exec -t portal_kla_db mysqldump -u root -p[ROOT_PASSWORD] portal_kla > backup_kla_$(date +%Y%m%d).sql

# Menggunakan Docker
docker exec -t portal_kla_db mysqldump -u root -p[ROOT_PASSWORD] portal_kla > backup_kla_$(date +%Y%m%d).sql
```

### Restore Database:
```bash
podman exec -i portal_kla_db mysql -u root -p[ROOT_PASSWORD] portal_kla < backup_kla_20261013.sql
```

---

## ❓ FAQ & Troubleshooting

1. **Bagaimana jika permission folder storage bermasalah pada rootless Podman?**
   Skrip `entrypoint.sh` secara otomatis menjalankan `chown` dan `chmod` ke user `www-data`. Jika menggunakan volume bind-mount lokal di SELinux, tambahkan flag `:Z` pada volume mount.

2. **Bagaimana cara mengaktifkan antrian (Queue Worker)?**
   Ubah variabel `ENABLE_QUEUE_WORKER=true` pada `.env` lalu restart kontainer. Supervisor akan otomatis menjalankan `php artisan queue:work`.

3. **Bagaimana cara membersihkan cache Laravel di dalam kontainer?**
   ```bash
   podman-compose exec app php artisan optimize:clear
   ```

