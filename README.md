# Buku Tamu Digital

Sistem Buku Tamu Digital berbasis web untuk tracking kunjungan masuk dan keluar pengunjung.

## 🚀 Fitur Utama

### Untuk Pengunjung

- ✅ **Absen Masuk**: Form lengkap dengan tanda tangan digital
- 👋 **Absen Keluar**: Input identitas untuk checkout
- 📱 **Mobile Friendly**: Responsive design untuk akses via HP
- 🔒 **No Login Needed**: Pengunjung tidak perlu login

### Untuk Admin

- 📊 **Dashboard**: Statistik pengunjung hari ini
- 📁 **Data Management**: Kelola dan filter data kunjungan
- 🔍 **Multiple Filters**: Hari ini, minggu, bulan, tahun, custom range
- ⏰ **Auto-Flag**: Sistem otomatis flag pengunjung yang belum checkout
- ✅ **Manual Checkout**: Admin bisa checkout manual untuk yang lupa
- 📥 **Export Excel**: Export data ke CSV/Excel
- 🚩 **Monitoring**: Highlight pengunjung yang belum checkout

## 📋 Teknologi

- **Backend**: PHP Native (tanpa framework)
- **Database**: MySQL
- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Features**: Canvas Signature, AJAX, Responsive Design

## 📦 Instalasi

### 1. Persiapan Database

```sql
-- Import file database.sql
mysql -u root -p < database.sql
```

Atau import manual via phpMyAdmin.

### 2. Konfigurasi

Edit file `config/config.php`:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Password MySQL Anda
define('DB_NAME', 'buku_tamu');

// Base URL
define('BASE_URL', 'http://localhost/BukuTamu');
```

### 3. Struktur Folder

Pastikan struktur folder seperti ini:

```
BukuTamu/
├── config/
│   ├── config.php
│   └── database.php
├── includes/
│   └── functions.php
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── data.php
│   ├── manual_checkout.php
│   ├── export_excel.php
│   └── logout.php
├── api/
│   └── find_entry.php
├── assets/
│   ├── css/
│   │   └── custom.css
│   └── js/
│       └── signature.js
├── cron/
│   └── auto_flag.php
├── logs/
├── index.php
├── exit.php
├── process_entry.php
├── process_exit.php
└── database.sql
```

### 4. Permissions

Buat folder `logs` dan berikan permission:

```bash
mkdir logs
chmod 755 logs
```

## 🔐 Login Admin Default

- **Username**: `admin`
- **Password**: `admin123`

⚠️ **PENTING**: Ganti password default setelah login pertama!

## 🎯 Cara Penggunaan

### Untuk Pengunjung

1. **Absen Masuk**:
   - Buka `http://localhost/BukuTamu/`
   - Isi form lengkap
   - Tanda tangan di canvas
   - Submit

2. **Absen Keluar**:
   - Buka `http://localhost/BukuTamu/exit.php`
   - Masukkan nomor identitas yang sama dengan saat masuk
   - Tanda tangan keluar
   - Submit

### Untuk Admin

1. **Login**:
   - Buka `http://localhost/BukuTamu/admin/login.php`
   - Login dengan kredensial admin

2. **Dashboard**:
   - Lihat statistik hari ini
   - Monitor pengunjung terbaru
   - Trigger auto-flag manual

3. **Data Management**:
   - Filter data berdasarkan periode
   - Checkout manual untuk yang lupa
   - Export ke Excel

## ⏰ Auto-Flag Cron Job

Untuk menjalankan auto-flag otomatis setiap jam 23:59:

### Windows (Task Scheduler)

```batch
php "D:\laragon\www\BukuTamu\cron\auto_flag.php"
```

### Linux (Crontab)

```bash
# Edit crontab
crontab -e

# Tambahkan line ini
59 23 * * * /usr/bin/php /path/to/BukuTamu/cron/auto_flag.php
```

Atau bisa trigger manual dari dashboard admin.

## 📊 Database Schema

### Tabel `visits`

- id, nomor_identitas, visit_date, nama, asal, fungsi
- jenis_identitas, keperluan, jam_masuk, jam_keluar
- tanda_tangan_masuk, tanda_tangan_keluar
- keterangan, status (MASUK/SELESAI)
- is_flagged, flag_note

### Tabel `admin_users`

- id, username, password, nama

## 🔧 Troubleshooting

### Error: Database connection failed

- Pastikan MySQL sudah running
- Cek konfigurasi database di `config/config.php`
- Pastikan database `buku_tamu` sudah dibuat

### Canvas signature tidak berfungsi

- Pastikan JavaScript diaktifkan
- Coba clear browser cache
- Test di browser lain

### Export Excel tidak bisa dibuka

- File CSV, buka dengan Excel
- Pilih encoding UTF-8 saat import

## 📝 Catatan Penting

1. **Exit hanya untuk akhir kunjungan**: Jika pengunjung keluar sebentar (makan siang) dan kembali lagi, tidak perlu checkout.

2. **Satu kunjungan per hari**: Satu pengunjung hanya boleh 1 record kunjungan per hari. Jika sudah masuk, tidak bisa masuk lagi di hari yang sama.

3. **Identitas disimpan normal**: Nomor identitas disimpan plain text untuk kemudahan admin mencari data.

4. **Auto-flag**: Sistem akan flag otomatis pengunjung yang belum checkout di jam 23:59.

## 🛡️ Security

- Password di-hash menggunakan bcrypt
- PDO prepared statements untuk SQL injection protection
- Session security dengan httponly cookies
- Input validation dan sanitization
- CSRF protection ready

## 📄 License

Project ini dibuat untuk keperluan internal. Silakan modifikasi sesuai kebutuhan.

## 👨‍💻 Support

Untuk pertanyaan atau issue, silakan hubungi administrator sistem.

---

**Version**: 1.0.0  
**Last Updated**: 2026-01-26
