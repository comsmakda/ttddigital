# TTD Digital — COM SMKN 2 Pinrang

Sistem tanda tangan digital berbasis QR Code untuk surat & sertifikat resmi
Community Programmer (COM) SMKN 2 Pinrang. Setiap dokumen mendapat kode unik
+ QR Code (dengan logo COM di tengah) yang bisa ditempel ke surat/sertifikat
dan di-scan siapa saja untuk memverifikasi keasliannya tanpa perlu login.

## Fitur

- Login admin (session-based, sementara akun lokal — lihat catatan integrasi di bawah)
- Dashboard: daftar semua TTD, pencarian, filter jenis & status
- Buat TTD baru untuk 2 jenis dokumen:
  - **Surat** — Nomor Surat, Perihal, Penandatangan, Jabatan, Tanggal
  - **Sertifikat** — Nama Sertifikat, Penandatangan, Jabatan, Tanggal
- Generate QR Code otomatis (PNG, logo COM di tengah, error-correction level tinggi supaya tetap scannable)
- Halaman verifikasi publik `/verify/{kode}` — tanpa login, menampilkan status sah / dibatalkan / tidak ditemukan
- Admin bisa membatalkan (revoke) TTD yang salah input — histori tetap tersimpan, saat di-scan tampil "Sudah Dibatalkan"
- Desain responsif mengikuti design system COM (Plus Jakarta Sans, warna teal, Tabler Icons)

## Kredensial Admin Default

```
Username: admin
Password: admin123
```

Akun ini otomatis dibuat (di-seed) saat aplikasi pertama kali jalan dan tabel
`admins` masih kosong. **Segera ganti password ini secara langsung di database**
setelah deploy, atau melalui environment variable `DEFAULT_ADMIN_USERNAME` /
`DEFAULT_ADMIN_PASSWORD` sebelum first run.

## Catatan Integrasi ke Web Utama COM (masa depan)

Login saat ini memeriksa tabel `admins` lokal (lihat `app/Core/Auth.php`,
method `attempt()`). Saat nanti sistem ini perlu terhubung ke web utama
COM SMKN 2 Pinrang, cukup ganti isi method tersebut agar memvalidasi ke
session/API web utama — tidak perlu mengubah controller, view, atau tabel
`signatures`, karena aplikasi ini sengaja dibuat berdiri sendiri (independen)
supaya bisa dipakai juga oleh sistem lain untuk verifikasi surat/sertifikat.

## Struktur Proyek

```
app/
  Controllers/   Auth, Dashboard, Signature, Verify
  Core/          Router, Controller, Database, Auth, View
  Models/        Admin, Signature
  Views/         layouts/ + halaman per fitur
  Helpers/       functions.php (env, url, asset, csrf, dst)
bootstrap/app.php   autoload + .env + session + auto-seed admin
routes/web.php      definisi seluruh route
public/              document root (index.php, assets/)
database/migrations.sql
storage/qrcodes/     file PNG QR (di luar webroot, di-stream lewat controller)
```

## Deploy ke Coolify

1. Push folder ini ke repository Git (GitHub/GitLab/dll), atau upload langsung.
2. Di Coolify, buat **New Resource → Docker Compose** (pakai `docker-compose.yml`
   yang sudah disediakan — otomatis menyiapkan app + MySQL), **atau** buat
   **New Resource → Dockerfile** kalau database MySQL sudah kamu sediakan
   terpisah (mis. layanan MySQL Coolify yang sudah ada).
3. Set environment variables berikut di Coolify (kalau pakai Dockerfile saja,
   tanpa docker-compose):

   | Variable | Contoh |
   |---|---|
   | `APP_URL` | `https://ttd.comsmkn2pinrang.sch.id` |
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `DB_HOST` | host MySQL kamu |
   | `DB_PORT` | `3306` |
   | `DB_NAME` | `ttd_digital` |
   | `DB_USER` | ... |
   | `DB_PASS` | ... |
   | `SESSION_NAME` | `ttd_digital_session` |
   | `DEFAULT_ADMIN_USERNAME` | `admin` |
   | `DEFAULT_ADMIN_PASSWORD` | ganti dari `admin123` |

4. **Penting:** `APP_URL` harus persis URL final (termasuk `https://`, tanpa
   trailing slash) karena dipakai untuk membangun isi QR Code (URL verifikasi).
5. Import `database/migrations.sql` ke database MySQL kamu (kalau tidak pakai
   `docker-compose.yml` yang sudah otomatis menjalankannya saat container
   MySQL pertama kali dibuat).
6. Mount volume persisten untuk folder `storage/` (khususnya
   `storage/qrcodes`) supaya file QR yang sudah dibuat tidak hilang saat
   redeploy.
7. Deploy. Login pertama kali dengan kredensial default di atas, lalu segera
   ganti password.

## Menjalankan secara lokal (opsional, tanpa Coolify)

```bash
cp .env.example .env
docker compose up -d --build
```

Akses di `http://localhost:8080`. Database MySQL otomatis dibuat & migration
otomatis dijalankan lewat `docker-entrypoint-initdb.d`.

## Library QR Code

Menggunakan [`endroid/qr-code`](https://github.com/endroid/qr-code) (via
Composer, ter-install otomatis saat `docker build`) dengan error-correction
level **High** dan logo COM di tengah, supaya QR tetap bisa di-scan meskipun
sebagian tertutup logo.
