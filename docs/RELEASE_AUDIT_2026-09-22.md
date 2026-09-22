# Audit rilis CBT GAEKS — 22 September 2026

Status: **BELUM SIAP RILIS** menurut `PRD_CBT_GAEKS.md` §33–37. Audit ini berdasarkan commit `d214ce4`, situs publik, dan pembacaan konfigurasi Hostinger; bukan pengganti smoke test setelah deployment.

## Kondisi produksi

- `https://cbt.gaeks.com/` mengembalikan HTTP 200 tetapi kontennya adalah **Halaman default**, bukan landing GAEKS CBT.
- `/sak` dan `/health` mengembalikan HTTP 404. `/health` memang terdaftar di `routes/web.php`, sehingga Laravel dari repo ini belum melayani domain tersebut.
- Hostinger mengenali `cbt.gaeks.com` sebagai subdomain aktif pada akun `u922552590`, root `/home/u922552590/domains/gaeks.com/public_html/cbt`, PHP 8.3.33. Tidak ada database yang tercatat untuk filter domain tersebut pada saat pemeriksaan. Ini tidak membuktikan tidak adanya database di akun lain atau database yang belum diasosiasikan.
- `/.env` mengembalikan 403 pada saat pemeriksaan; status itu saja tidak membuktikan konfigurasi web root sudah aman.

## Kesesuaian PRD

| Area | Status | Bukti / tindakan tersisa |
| --- | --- | --- |
| Fondasi Laravel, skema, generator 10.000 ID, 12 exam/token | Implementasi lokal ada | Migrations dan seeders ada; tes lokal lulus. Belum terbukti dijalankan di database produksi. |
| Access gate, guide, demo (§11, §21, §33) | Parsial lokal | Form, normalisasi, error generik, rate limit, session scope per exam, guide, dan demo non-scoring sudah diuji. Tombol start masih nonaktif karena engine attempt belum tersedia. Belum dideploy. |
| Player, timer, autosave, submit, scoring, history, retry (§13–14, §24–25) | Belum ada | Tabel model telah disiapkan, tetapi endpoint, layanan, UI, dan tes perilakunya belum ada. |
| Bank soal kanonis (§16, §33) | Tersedia lokal, belum disetujui editorial/deploy | PDF pemilik produk telah diekstrak menjadi 142 item dalam JSON private; dry-run, impor SQLite, dan validator manifest lulus. Lihat `SAK_CONTENT_IMPORT.md`; verifikasi editorial dan hukum belum selesai. |
| Tryout dua sesi dan review manual (§9, §33) | Belum ada | Belum ada engine sesi 75 menit, aturan 50/50 dan minimum 40, maupun workflow review. |
| Admin dan audit mutasi (§17, §33) | Belum ada | Model/tabel tersedia, tetapi login, role, CRUD, import, review, dan audit operasional belum ada. |
| Keamanan aplikasi (§23, §33) | Belum bisa lulus | Belum ada endpoint kandidat untuk menguji IDOR, rate limit, kebocoran answer key, atau cookie/session produksi. |
| Deployment (§31, §33) | Gagal | Document root menurut PRD harus mengarah ke Laravel `public`; domain masih menayangkan placeholder. Composer, `.env`, DB, migrate/seed, dan aset produksi perlu disiapkan sebelum smoke test. |

## Verifikasi lokal dan CI

- Semua 5 run GitHub Actions yang tersedia saat audit gagal di langkah `Install Composer Dependencies`; lock sebelumnya memuat Symfony 8 yang mensyaratkan PHP >=8.4.1.
- Patch audit ini mematok platform Composer ke PHP 8.3.33 dan memperbarui lima paket Symfony ke lini 7.4; `composer install` pada PHP 8.3.35 berhasil.
- `PHPUnit`: **53 tests, 360 assertions, PASS** setelah tes importer dan access gate ditambahkan. Cakupannya belum memenuhi daftar 22 tes minimum PRD §34.
- `npm ci` dan `npm run build`: **PASS** setelah karakter `\\n` literal di langkah build CI diperbaiki.
- `npm audit` melaporkan 1 moderate (`esbuild`) dan 1 high (`vite`) pada toolchain development. Perlu pembaruan teruji, bukan `npm audit fix --force` tanpa pemeriksaan kompatibilitas.

## Gerbang sebelum menyentuh produksi

1. Selesaikan M2–M6 beserta tes penerimaan §33–34 dan verifikasi editorial/hukum 142 item bersama pemilik materi.
2. Jalankan CI yang hijau pada commit kandidat rilis; tinjau advisory npm dan risiko migrasi.
3. Backup keadaan hosting/data yang ada, lalu verifikasi secara langsung konfigurasi file, `.env`, database, dan document root. Jangan jalankan `migrate:fresh` di produksi.
4. Deploy commit yang sudah disetujui, install Composer tanpa dev, bangun/deploy aset, `migrate --force`, seed idempotent, cache Laravel, lalu smoke test kandidat dan admin melalui HTTPS.
5. Ulangi audit akses objek, kebocoran kunci jawaban, timer/reconnect, scoring historis, dan seluruh acceptance criteria pada domain produksi. Jangan menyatakan rilis selesai hanya karena `/` memberi 200.
