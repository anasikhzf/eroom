# Panduan Hosting Eroom (CodeIgniter 4 PWA)

Panduan lengkap untuk melakukan deployment/hosting aplikasi **Eroom** ke shared hosting (**cPanel**) atau VPS dengan panel (**Webmin/Virtualmin**) tanpa perlu akses SSH terminal.

---

## 📋 Ringkasan Fitur Produksi Baru
Aplikasi kini dilengkapi dengan:
- **PWA (Progressive Web App)**: File `manifest.json`, `sw.js`, dan ikon melingkar di `public/icons/`.
- **Claymorphism UI**: Menggunakan Tailwind CDN, Plus Jakarta Sans, dan Lucide Icons (semua aset frontend terintegrasi).
- **SEO & Sitemap Otomatis**: Endpoint dinamis `/sitemap.xml` dan `robots.txt`.
- **Workspace Protection & Brute Force Guard**: Rate limiting pada sesi dan password bcrypt.

---

## 📋 Langkah 1: Persiapan Sebelum Upload

### 1. Sesuaikan Konfigurasi Production (`app/Config/App.php` atau `.env`)
Buka file `app/Config/App.php`:
- **`$baseURL`**: Ganti dengan domain asli Anda (wajib berakhiran slash `/`):
  ```php
  public string $baseURL = 'https://nama-domain-anda.com/';
  ```
- **`$indexPage`**: Pastikan dikosongkan agar URL bersih tanpa `index.php`:
  ```php
  public string $indexPage = '';
  ```

Atau cukup atur di file **`.env`** di hosting nanti:
```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://nama-domain-anda.com/'
```

### 2. Atur Robots & Sitemap (`public/robots.txt`)
Buka `public/robots.txt` dan sesuaikan domain sitemap:
```txt
User-agent: *
Allow: /
Disallow: /uploads/

Sitemap: https://nama-domain-anda.com/sitemap.xml
```

### 3. Kompres File Project ke Format ZIP
Pilih seluruh berkas dan folder di dalam project lokal, lalu kompres ke **`project.zip`**:
- Pastikan folder `public`, `app`, `system`, `vendor`, dan `writable` ikut terkompres.
- Pastikan folder `public/icons/` dan `public/uploads/` ada di dalam arsip.

---

## 🛠️ Langkah-langkah di cPanel

### 1. Siapkan Database
1. Masuk ke **cPanel** -> buka menu **MySQL® Database Wizard**.
2. Buat database baru (misal: `usercpanel_eroom`).
3. Buat pengguna (user) database & password aman.
4. Berikan hak akses **ALL PRIVILEGES** ke user tersebut.
5. Catat nama database, user, dan password.

### 2. Import Database
1. Masuk ke **phpMyAdmin** di cPanel.
2. Klik nama database yang baru dibuat di bilah sisi kiri.
3. Klik tab **Import** -> **Choose File** -> pilih file backup SQL Anda.
4. Klik tombol **Import** (atau **Kirim** / **Go**).

### 3. Upload & Struktur Folder Aman (Rekomendasi Utama)
Demi keamanan maksimal, simpan file inti di luar `public_html`:

1. Buka **File Manager** cPanel.
2. Masuk ke direktori utama user Anda (satu tingkat di atas `public_html`, yaitu `/home/username/`).
3. Buat folder baru bernama **`eroom`**.
4. Masuk ke folder `eroom`, klik **Upload**, lalu unggah **`project.zip`**.
5. Klik kanan pada `project.zip` -> pilih **Extract**.
6. Pindahkan isi folder **`eroom/public/`** ke **`public_html`**:
   - Buka `/home/username/eroom/public/`.
   - Pilih semua file (centang *Show Hidden Files* agar `.htaccess` dan `favicon.ico` terbawa).
   - Klik tombol **Move**, isi tujuannya menjadi `/public_html/`.
7. Hubungkan `index.php`:
   - Buka folder `public_html`, klik kanan file **`index.php`** -> pilih **Edit**.
   - Cari baris:
     ```php
     $pathsPath = realpath(FCPATH . '../app/Config/Paths.php') ?: FCPATH . '../app/Config/Paths.php';
     ```
   - Ganti menjadi:
     ```php
     $pathsPath = realpath(FCPATH . '../eroom/app/Config/Paths.php') ?: FCPATH . '../eroom/app/Config/Paths.php';
     ```
   - Klik **Save Changes**.

### 4. Hubungkan Database di Hosting
Buka file `/home/username/eroom/.env` (atau `eroom/app/Config/Database.php`):
```ini
database.default.hostname = localhost
database.default.database = usercpanel_eroom
database.default.username = usercpanel_dbuser
database.default.password = PasswordAndaDiCpanel
database.default.DBDriver = MySQLi
```

### 5. Atur Izin Akses (Permissions)
1. Folder `/home/username/eroom/writable` -> atur ke **755** (atau **777** jika server membutuhkannya).
2. Folder `/public_html/uploads` -> atur ke **755** (atau **777**) agar unggahan berkas lampiran kartu kanban berfungsi.

---

## 🛠️ Langkah-langkah di Webmin / Virtualmin

1. **Buat Database**:
   - Di Virtualmin, pilih domain target -> klik **Edit Databases** -> **Create a new database**.
   - Pilih jenis MySQL dan beri nama (misal: `eroom`).
2. **Import Data**:
   - Klik nama database -> tombol **Manage...** -> **Execute SQL** -> pilih **Run SQL from file** -> upload file `.sql` Anda.
3. **Upload Berkas**:
   - Buka **File Manager** di Virtualmin.
   - Buat folder `/home/username/eroom` dan upload serta ekstrak `project.zip`.
   - Pindahkan seluruh file dari `eroom/public` ke `/home/username/public_html`.
   - Edit `/home/username/public_html/index.php` sesuaikan `$pathsPath` menunjuk ke `../eroom/app/Config/Paths.php`.
4. **Aktifkan SSL Gratis**:
   - Masuk ke menu **Server Configuration** -> **SSL Certificate** -> tab **Let's Encrypt**.
   - Klik **Request Certificate** (PWA membutuhkan koneksi HTTPS agar Service Worker aktif di browser pengguna).

---

## 🚀 Pengujian Fitur Setelah Online

Buka website Anda melalui `https://nama-domain-anda.com/`:
1. **Pengujian PWA**: Buka di Chrome / Edge / HP Android, periksa ikon instalasi aplikasi di bilah browser (Eroom siap diinstall sebagai aplikasi mandiri).
2. **Pengujian Kunci Ruang**:
   - Buat ruang terkunci dengan sandi.
   - Masuk ke ruang tersebut, lalu klik tombol **Keluar Ruang**.
   - Pastikan Anda diarahkan kembali ke halaman beranda dan status ruang langsung terkunci tanpa kilasan data sebelumnya.
3. **Pengujian Pengaturan Ruang**: Buka menu **Pengaturan** di dalam ruang untuk mengubah status publik/kunci atau warna tema.
4. **Pengujian SEO**: Kunjungi `https://nama-domain-anda.com/sitemap.xml` dan pastikan daftar ruang kerja publik Anda muncul dalam format XML standar.
