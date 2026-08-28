# Sistem Manajemen Agenda Rapat Universitas Andalas

> Aplikasi manajemen agenda rapat, absensi digital berbasis QR Code, dan distribusi notulen untuk lingkungan universitas.

## 📋 Tentang Proyek

Sistem Manajemen Agenda Rapat Universitas Andalas adalah sistem berbasis web yang dirancang untuk menyederhanakan siklus hidup penyelenggaraan rapat di lingkungan universitas mulai dari penjadwalan agenda, generate QR Code absensi, pencatatan kehadiran peserta secara digital, hingga distribusi notulen hasil rapat via email.

Sistem ini melayani tiga jenis pengguna:
- **Administrasi** — mengelola siklus penuh agenda rapat, dari perencanaan hingga dokumentasi kehadiran.
- **Notulen** — mendokumentasikan dan mendistribusikan hasil rapat setelah pelaksanaan.
- **Peserta/Tamu** — menghadiri rapat dan mengisi absensi tanpa perlu memiliki akun (guest access).

## ✨ Fitur Utama

### 👤 Administrasi
- Autentikasi (login) berbasis peran
- CRUD agenda rapat (pembahasan, tanggal, waktu, lokasi, narasumber, unit penyelenggara)
- Generate & kirim info agenda otomatis ke peserta setelah agenda disimpan
- Generate QR Code absensi per agenda, dapat dicetak beserta dokumen undangan
- Cetak daftar hadir peserta beserta tanda tangan digital
- Kelola data master Unit/Fakultas dan Lokasi/Ruang Rapat
- Laporan & rekapitulasi kehadiran per unit kerja

### 📝 Notulen
- Upload dokumen notulen (PDF/DOCX) untuk agenda yang telah selesai
- Melihat daftar agenda beserta status kelengkapan notulen (belum/draft/selesai diunggah)
- Kirim notifikasi email berisi notulen ke peserta terkait, dengan log riwayat pengiriman per penerima

### 🙋 Peserta / Publik (tanpa login)
- Mengakses info agenda rapat melalui link publik atau layar display per lokasi
- Scan QR Code untuk mengisi form absensi (identitas, jabatan, instansi, tanda tangan digital)
- Sistem otomatis mencocokkan data peserta dengan undangan (member) yang sudah terdaftar

## 🗂️ Aktor Sistem

| Aktor | Akses | Deskripsi |
|---|---|---|
| Administrasi | Login wajib | Bertanggung jawab penuh atas siklus hidup agenda rapat |
| Notulen | Login wajib | Mendokumentasikan hasil rapat sebagai arsip resmi |
| Peserta/Absen | Tanpa login (guest) | Menghadiri rapat & mengisi absensi via QR Code/link publik |

## 🧱 Struktur Basis Data (ERD)

Entitas utama dalam sistem:

- `USERS` — akun internal (Administrasi/Notulen)
- `UNIT` — data master fakultas/direktorat/lembaga
- `LOKASI` — ruang rapat, terhubung ke `UNIT`
- `AGENDA` — data inti rapat (nomor surat, pembahasan, deskripsi, jadwal, QR code)
- `AGENDA_MEMBER` — relasi agenda–peserta beserta peran (peserta/narasumber/moderator)
- `MEMBER` — data peserta/narasumber
- `ABSENSI` — pencatatan kehadiran & tanda tangan digital
- `LAMPIRAN` — dokumen notulen/hasil rapat
- `EMAIL_LOG` — riwayat pengiriman email notulen per penerima
- `ACTIVITY_LOG` — audit trail aktivitas sistem

Diagram ERD lengkap tersedia di folder [`/docs`](./docs).

## 🛠️ Teknologi

- **Backend**: PHP (Yii2 Framework)
- **Database**: MySQL
- **Frontend**: Yii2 View (PHP Template) / HTML, CSS, JavaScript, Bootstrap
- **Lainnya**: Library QR Code generator, signature pad (tanda tangan digital), library kirim email (SMTP/Mailer)

## 📐 Dokumentasi Perancangan

Proyek ini disusun dengan dokumentasi SKPL lengkap:
- Use Case Diagram & Deskripsi Use Case
- Scenario Diagram (11 use case)
- Activity Diagram (11 use case)
- Sequence Diagram (11 use case)
- Entity Relationship Diagram (ERD)
- Desain UI/UX per halaman (Wireframe/Mockup)

Dokumen lengkap tersedia di folder [`/docs`](./docs).

## 🚀 Instalasi

```bash
# Clone repository
git clone https://github.com/ChantikaAurora/agenda-meeting-unand.git
cd agenda_meeting

# Install dependencies
composer install

# Konfigurasi environment
cp .env.example .env
# sesuaikan konfigurasi database di config/db.php atau .env

# Migrasi database
php yii migrate

# Jalankan server lokal
php yii serve
```

## 👤 Pengembang

**Chantika Aurora Akmal** | 2311083001
**Dwi Wulan Suci** | 2311081012
D4 Teknik Rekayasa Perangkat Lunak — Politeknik Negeri Padang
