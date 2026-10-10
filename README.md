# Eroom - Ruang Kerja Kolaboratif & Kanban Board

**Eroom** adalah aplikasi web berbasis CodeIgniter 4 untuk pengelolaan proyek, tugas tim, dan ruang kerja kolaboratif dengan gaya visual **Claymorphism** serta dukungan **PWA (Progressive Web App)**.

Aplikasi ini dirancang untuk memudahkan individu maupun tim dalam merencanakan tugas melalui papan kanban interaktif, berkomunikasi melalui obrolan grup langsung di dalam ruang kerja, serta menjaga privasi proyek dengan proteksi kata sandi.

---

## 🚀 Fitur Utama

- **Papan Kanban Interaktif**: Susun daftar kolom dan pindahkan kartu tugas secara mudah (*drag and drop*).
- **Detail & Lampiran Kartu**: Tambahkan deskripsi, tenggat waktu (*due date*), penanda warna label, serta berkas/gambar lampiran.
- **Ruang Kerja Publik & Terkunci**: Buat ruang kerja terbuka (publik) atau lindungi dengan kata sandi. Pengaturan privasi ini dapat diubah kapan saja.
- **Obrolan Grup Workspace**: Fitur chat langsung di tiap workspace dengan prompt nama tampilan saat pertama kali bergabung.
- **Dukungan PWA (Progressive Web App)**: Dilengkapi Service Worker, Web Manifest, ikon melingkar, dan tombol instalasi langsung di antarmuka untuk pengalaman seperti aplikasi native.
- **Desain Claymorphism & Responsif**: Tampilan 3D lembut dengan bayangan ganda (*inner & outer shadows*), tipografi Plus Jakarta Sans, ikon berbasis SVG (Lucide Icons), serta dukungan Dark Mode & Light Mode.
- **Keamanan Terstandarisasi**: Hashing kata sandi aman (`PASSWORD_BCRYPT`), perlindungan brute-force (*rate limiting* percobaan sandi), sanitasi input, dan tombol keluar ruang dengan pencegahan *browser cache*.
- **Optimasi SEO**: Dilengkapi meta tag lengkap, Open Graph preview, file `robots.txt`, dan dynamic XML sitemap di `/sitemap.xml`.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8.1+ & [CodeIgniter 4](https://codeigniter.com)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, Tailwind CSS, Vanilla JavaScript, Lucide Icons, Plus Jakarta Sans
- **PWA**: Service Worker & Web App Manifest

---

## 💻 Panduan Instalasi Lokal

### 1. Kloning Repositori
```bash
git clone https://github.com/anasikhzf/eroom.git
cd eroom
```

### 2. Pasang Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file `env` menjadi `.env`:
```bash
cp env .env
```
Sesuaikan pengaturan URL dan database pada `.env`:
```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = nama_database_anda
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

### 4. Jalankan Migrasi Database
```bash
php spark migrate
```

### 5. Jalankan Server Lokal
```bash
php spark serve
```
Akses aplikasi melalui browser di `http://localhost:8080`.

---

## 🌐 Panduan Deployment / Hosting

Untuk panduan upload ke cPanel atau VPS Virtualmin, silakan baca dokumentasi lengkap di [hosting.md](hosting.md).

---

## 📄 Lisensi & Pembuat

- Dibuat oleh [anasikhzf](https://anasikhzf.web.app)
- Berlisensi di bawah [MIT License](LICENSE)
