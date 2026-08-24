# LinkHub — Personal Online Link Sharing & Storefront

**LinkHub** adalah aplikasi web sederhana, modern, dan responsif untuk **personal online link sharing** dan **mini storefront** (link-in-bio). Dibangun menggunakan Laravel 12, Tailwind CSS, Blade, Alpine.js, dan Vite.

---

## 🚀 Fitur Utama

### 1. Admin Dashboard & Analytics
- **Authentication**: Login & Logout aman dengan Laravel Authentication.
- **Ringkasan Stats Card**: Monitoring *Total Links*, *Active Links*, *Inactive Links*, dan *Total Clicks*.
- **Click Counter**: Menghitung secara otomatis setiap kali halaman publik (`/{slug}`) dikunjungi.

### 2. Manajemen Link (`/admin/links`)
- CRUD Link dasar (Title & Slug).
- Toggle Status (Active / Inactive).
- Filter berdasarkan status dan pencarian judul/slug.
- Aksi cepat: *Copy Link*, *Open Link*, *Atur Halaman*, *Edit*, *Delete*.

### 3. Page Editor / Atur Halaman (`/admin/links/{id}/edit-page`)
- **Tampilan & Profil Toko**: Atur nama toko, bio, foto profil, tipe cover (warna/gambar upload), alignment header (*center/left*), dan layout katalog (*Grid 2 Kolom* / *List 1 Kolom*).
- **Traffic Source Parameter (`?src=`)**: Generator tag sumber trafik (misal: `instagram`, `tiktok`, `ads`) dilengkapi tombol instan *Copy Tracked Link*.
- **Kontak & Social Media**: Tombol WhatsApp direct chat (normalisasi otomatis ke format internasional `628...`) dan tautan media sosial (Instagram, TikTok, Facebook, X, Threads).
- **Kategori Produk**: Tambah, ubah, dan hapus kategori.
- **Katalog Produk**: Tambah, ubah, hapus, urutkan, dan toggle status produk.
- **Bulk Upload Excel / CSV**: Import massal produk dari spreadsheet (`.xlsx`, `.xls`, `.csv`). Kategori baru akan otomatis dibuatkan jika belum ada. Tersedia tombol *Download Template CSV*.
- **Live Preview Interaktif**: Tampilan mockup smartphone di samping editor yang diperbarui secara real-time.

### 4. Public Link Page (`/{slug}`)
- Akses publik tanpa perlu login melalui URL bersih `https://domain.com/{slug}`.
- **Pill Filter Kategori Interaktif**: Pengunjung dapat memfilter produk berdasarkan kategori secara instant tanpa reload halaman.
- **Status Link Unavailable**: Tampilan khusus jika link dalam status non-aktif (HTTP 403) atau tidak ditemukan (HTTP 404).

---

## 🛠️ Persyaratan Sistem

- **PHP**: 8.2 / 8.3+
- **Composer**: 2.x
- **Node.js**: 18+ & NPM
- **Database**: MySQL / MariaDB (Rekomendasi) atau SQLite

---

## 📦 Cara Instalasi & Jalankan Project

1. **Clone repository**:
   ```bash
   git clone <repository-url>
   cd fastlink
   ```

2. **Install Depedensi PHP & Node.js**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database** di `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=linkhub
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Migrasi Database & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

6. **Jalankan Development Server**:
   ```bash
   # Terminal 1 (Laravel Server)
   php artisan serve

   # Terminal 2 (Vite Development Asset Compilation)
   npm run dev
   ```
   Aplikasi dapat diakses melalui `http://localhost:8000`.

---

## 🔑 Akun Default Admin

Seeder bawaan menyediakan akun administrator default:

- **Email**: `admin@admin.com`
- **Password**: `admin123`

---

## 🧪 Menjalankan Automated Tests

Jalankan pengujian otomatis (PHPUnit / Laravel Feature Tests):

```bash
php artisan test
```

---

## 📄 Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).
