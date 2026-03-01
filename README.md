# VJ Gallery

Sistem VJ Gallery adalah aplikasi berbasis web untuk mengelola dan menampilkan karya visual (visual jockey), Project ini dibuat menggunakan framework Laravel dan dirancang agar mudah digunakan.  

Aplikasi ini memiliki dua role utama:
- **Admin:** Mengelola pengguna, manajemen konten, manajemen template panggung, mengakses simulasi panggung.
- **VJ:** Mengelola karya, mengakses simulasi panggung, melihat galeri karya.

---

## Kebutuhan Sistem

### 1. Perangkat Keras
- Laptop / PC Windows 10 atau 11
- Minimal RAM 4 GB (disarankan 8 GB)

### 2. Software yang harus diinstall (jika belum ada)
- XAMPP	versi 8.2.x	https://www.apachefriends.org
- Composer versi Terbaru https://getcomposer.org
- Git versi Terbaru	https://git-scm.com
- Visual Studio Code versi Terbaru https://code.visualstudio.com
- Browser : Chrome / Edge terbaru


## Instalasi Project

### 1. Persiapan Awal
1. Install semua software di atas (klik Next → Next → Finish saja).
2. Jalankan XAMPP Control Panel → klik Start pada:
    - Apache
    - MySQL

### 2. Clone Repository (GANTI DENGAN EXTRACT FILE ZIP)
```bash
git clone https://github.com/ekanurseva/vj-gallery.git
cd vj-gallery
```

### 3. Install Dependency PHP
```bash
composer install
```

### 4. (Opsional) Update Package Composer
```bash
composer update
```

### 5. Install Dependency Frontend
```bash
npm install
```

### 6. Setting Database
1. Buka http://localhost/phpmyadmin
2. Klik New → buat database bernama: vj_gallery

### 7. Duplikat File .env.example
```bash
copy .env.example .env
```

### 8. Atur Koneksi Database di .env
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vj_gallery
DB_USERNAME=root
DB_PASSWORD=
```

### 9. Generate Application Key
```bash
php artisan key:generate
```

### 10. Buat Link antara storage dan public
```bash
php artisan storage:link
```

### 11. Pastikan Setting File Storage di .env
```bash
FILESYSTEM_DISK=public
```

### 12. Jalankan Migrasi Database
```bash
php artisan migrate
```

### 13. (Opsional) Jalankan Seeder untuk Data Awal
```bash
php artisan db:seed

# Seeder tersedia:
AdminSeeder
```

### 13. Jalankan Vite (Frontend)
```bash
npm run dev
```

### 13. Jalankan Server Laravel
```bash
php artisan serve
```

# Akses di browser:
http://localhost:8000

## Akun Default (Jika Menggunakan Seeder)
Role Admin, username = admin@vj.com, password = admin123

## Troubleshooting
Port 8000 tidak bisa -> Ganti dengan php artisan serve --port=8080
Composer error -> Pastikan internet aktif
Database error -> Pastikan MySQL XAMPP menyala
500 error -> Jalankan php artisan key:generate ulang


Thank You!
Happy Coding & Keep It Simple