# Sistem Pelacakan Alumni

Website sederhana untuk membantu kampus melakukan pelacakan alumni dari berbagai sumber publik seperti LinkedIn, Google Scholar, ResearchGate, ORCID, dan GitHub.

## Fitur Utama

- Dashboard ringkasan data alumni
- CRUD data alumni
- Menjalankan tracking alumni
- Menyimpan hasil tracking
- Menampilkan confidence score hasil pencarian
- Menampilkan link sumber/profil alumni

## Teknologi

- Laravel
- Blade
- Tailwind CSS
- MySQL

## Struktur Sederhana Proyek

```bash
app/
├── Http/Controllers/
│   ├── AlumniController.php
│   ├── TrackingController.php
│   └── DashboardController.php
├── Models/
│   ├── Alumni.php
│   ├── TrackingResult.php
│   └── TrackingSource.php
└── Services/
    ├── QueryBuilderService.php
    ├── IdentityScoringService.php
    └── PublicProfileSearchService.php

resources/views/
├── layouts/app.blade.php
├── dashboard/index.blade.php
├── alumni/index.blade.php
├── alumni/create.blade.php
├── alumni/edit.blade.php
├── alumni/show.blade.php
├── tracking/index.blade.php
└── tracking/result.blade.php
````

## Instalasi

1. Clone project

```bash
git clone https://github.com/Farelle009/PelacakAlumni.git
cd nama-project
```

2. Install dependency

```bash
composer install
npm install
```

3. Copy file environment

```bash
cp .env.example .env
```

4. Generate app key

```bash
php artisan key:generate
```

5. Atur database di file `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pelacak_alumni
DB_USERNAME=root
DB_PASSWORD=
```

6. Jalankan migration

```bash
php artisan migrate
```

7. Jalankan seeder jika ada

```bash
php artisan db:seed
```

8. Jalankan project

```bash
php artisan serve
npm run dev
```

## Tabel Utama

### `alumni`

Menyimpan data alumni seperti:

* nim
* nama_lengkap
* program_studi
* tahun_lulus
* email
* kota
* status_pelacakan

### `tracking_sources`

Menyimpan daftar sumber tracking seperti:

* LinkedIn
* Google Scholar
* ResearchGate
* ORCID
* GitHub

### `tracking_results`

Menyimpan hasil pencarian alumni seperti:

* query
* judul
* snippet
* url
* nama_terdeteksi
* afiliasi
* jabatan
* lokasi
* confidence_score
* status_verifikasi

## Alur Sistem

1. Admin menambahkan data alumni
2. Sistem membuat query pencarian berdasarkan nama alumni
3. Sistem mencari data dari sumber publik
4. Sistem menyimpan hasil tracking
5. Sistem menghitung confidence score
6. Admin melihat hasil tracking dan profil yang ditemukan

## Catatan

* Sistem ini dibuat untuk penggunaan akademik dan pembelajaran
* Hasil tracking bergantung pada data publik yang tersedia
* Tidak semua sumber selalu dapat diakses secara otomatis
* Beberapa hasil tetap perlu verifikasi manual

## Lisensi

Project ini digunakan untuk kebutuhan tugas dan pengembangan pembelajaran.
